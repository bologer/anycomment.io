import React, {useEffect, useRef, useState} from 'react';
import SendCommentFormBody from './SendCommentFormBody';
import SendCommentFormBodyAvatar from './SendCommentFormBodyAvatar';
import ReCAPTCHA from 'react-google-recaptcha';
import DataProcessing from './DataProcessing';
import Icon from '~/components/Icon';
import {faTimes} from '@fortawesome/free-solid-svg-icons';
import {useOptions, useSettings} from '~/hooks/setting';
import {useFormik} from 'formik';
import {useDispatch, useSelector} from 'react-redux';
import {
    fetchCommentsSalient,
    fetchCreateComment,
    fetchUpdateComment,
    invalidateCommentForm,
    invalidateCreateComment,
    invalidateUpdateComment,
} from '~/core/comment/CommentActions';
import {StoreProps} from '~/store/reducers';
import {CommentReducerProps} from '~/core/comment/commentReducers';
import {manageReducer} from '~/helpers/action';
import {isGuest} from '~/helpers/user';
import ReactQuill from 'react-quill';
import {CommentModel} from '~/typings/models/CommentModel';
import styled from 'styled-components';
import LoginSocialList from './LoginSocialList';
import {GuestInputTypes, SocialsOption} from '~/components/AnyCommentProvider';
import {successSnackbar, failureSnackbar} from '~/core/notifications/NotificationActions';
import {add, get, remove} from '~/helpers/storage';

const recapchaRef = React.createRef();

export interface SendCommentProps {
    action?: 'reply' | 'update';
    comment?: CommentModel;
}

export interface FormValues {
    author_name: string;
    author_email: string;
    author_website: string;
    comment_html: string;
    is_agreement_accepted: boolean;
}

const Wrapper = styled.div`
    width: 100%;
    display: flex;
`;

const AvatarColumn = styled.div`
    padding: 0 10px 0 0;
`;

const EditorColumn = styled.div`
    margin-left: 0;
    max-width: 100%;
    flex: 1 1 0%;
`;

const GuestFieldsRoot = styled.div`
    display: flex;
    flex-wrap: wrap;
    margin-top: 15px;
`;

const GuestHeader = styled.div`
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 15px;
    color: rgb(105, 111, 113);
`;

const SocialListColumn = styled.div`
    padding-right: 20px;
`;

const GuestFieldsColumn = styled.div`
    flex: 1 1 0;
`;

let editorRef: ReactQuill | null = null;

/**
 * Class SendComment is used process comment before it will be sent.
 */
export default function SendComment({action, comment}: SendCommentProps) {
    const dispatch = useDispatch();
    const {create, update} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);
    const options = useOptions();
    const settings = useSettings();

    const [attachments, setAttachments] = useState<[]>([]);
    const [buttonText, setButtonText] = useState(settings.i18.button_send);

    const formik = useFormik<FormValues>({
        initialValues: {
            author_name: '',
            author_email: '',
            author_website: '',
            comment_html: '',
            is_agreement_accepted: false,
        },
        onSubmit: values => {
            // if (isCaptchaOn()) {
            //     recapchaRef.current.execute();
            //
            //     let checkValueInterval = setInterval(() => {
            //         if (recapchaRef.current.getValue() !== '') {
            //             clearInterval(checkValueInterval);
            //
            //             if (!self.isGuest()) {
            //                 return self.handleAuthorized(event);
            //             }
            //
            //             return handleGuest(event);
            //
            //         }
            //     }, 100);
            // }

            let data = {
                content: values.comment_html,
                post: settings.postId,
            };

            if (isReply()) {
                data.parent = comment.id;
            }

            // if (isCaptchaOn()) {
            //     data.captcha = recapchaRef.current.getValue();
            //
            //     if (!data.captcha) {
            //         return false;
            //     }
            // }

            if (attachments && attachments.length > 0) {
                data.attachments = JSON.stringify(attachments);
            }

            if (isGuest() && !settings.options.isFormTypeSocials) {
                data.author_name = values.author_name;

                if (values.author_email) {
                    data.author_email = values.author_email;
                }

                if (values.author_website) {
                    data.author_url = values.author_website;
                }
            }

            if (!action || isReply()) {
                dispatch(fetchCreateComment(settings.postId, data));
            }

            if (isUpdate()) {
                dispatch(fetchUpdateComment(comment && comment.id, data));
            }
        },
    });

    const {comment_html, is_agreement_accepted} = formik.values;

    useEffect(() => {
        prepareRemembered();
        prepareForm();
    }, [action, comment]);

    useEffect(() => {
        manageReducer({
            reducer: create,
            onSuccess: response => {
                prepareInitialForm();
                dispatch(fetchCommentsSalient({postId: settings.postId, order: options.sort_order}));

                removeRememberedComment();

                if (response.message) {
                    dispatch(successSnackbar(response.message));
                }

                dispatch(invalidateCreateComment());
            },
        });
    }, [create]);

    useEffect(() => {
        manageReducer({
            reducer: update,
            onSuccess: response => {
                prepareInitialForm();
                dispatch(fetchCommentsSalient({postId: settings.postId, order: options.sort_order}));

                if (response.message) {
                    dispatch(successSnackbar(response.message));
                }

                dispatch(invalidateUpdateComment());
            },
        });
    }, [update]);

    /**
     * Check whether reply to comment action is called.
     *
     * @returns {boolean}
     */
    function isReply() {
        return action === 'reply';
    }

    /**
     * Check whether update comment action is called.
     *
     * @returns {boolean}
     */
    function isUpdate() {
        return action === 'update';
    }

    /**
     * Generic method invoked when action changes.
     * Action can be either reply, update or default.
     *
     * Default being re initiate default comment form state.
     * @see prepareInitialForm
     */
    function prepareForm() {
        switch (action) {
            case 'reply':
                prepareReplyForm();
                break;
            case 'update':
                prepareUpdateForm(comment);
                break;
            default:
                prepareInitialForm();
                break;
        }
    }

    /**
     * Reinit comment form. Initial comment form state.
     */
    function prepareInitialForm() {
        formik.setFieldValue('button_text', settings.i18.button_send, false);
        formik.setFieldValue('comment_html', '', false);
        setAttachments([]);
        // dropComment();
        dispatch(invalidateCommentForm());
    }

    /**
     * Prepare comment form for comment reply state.
     */
    function prepareReplyForm() {
        formik.setFieldValue('comment_html', '', false);
        setButtonText(settings.i18.button_reply);

        focusCommentField();
    }

    /**
     * Prepare comment form for comment update state.
     *
     * @param comment
     */
    function prepareUpdateForm(comment) {
        const commentHtml = comment.content.trim();

        if (commentHtml !== '') {
            formik.setFieldValue('comment_html', comment.content, false);

            setButtonText(settings.i18.button_save);

            if (comment.attachments && comment.attachments.length > 0) {
                setAttachments(comment.attachments);
            }

            focusCommentField();
        }
    }

    /**
     * Removes comment text form localStorage.
     */
    function removeRememberedComment() {
        remove('form-comment_html');
    }

    /**
     * Focus on comment field.
     */
    function focusCommentField() {
        if (editorRef !== null) {
            editorRef.focus();
        }
    }

    /**
     * Handle comment text change.
     *
     * @param text
     */
    function handleEditorChange(text) {
        formik.setFieldValue('comment_html', text, false);
        add('form-comment_html', text);
    }

    /**
     * Handle field change by remembering value in localstorage & triggering formik handler.
     * @param e
     */
    function handleFieldChange(e) {
        formik.handleChange(e);

        add('form-' + e.target.name, e.taget.value);
    }

    /**
     * Handles to set editor's ref.
     * @param ref
     */
    function handleEditorRef(ref) {
        editorRef = ref;
    }

    /**
     * Handle attachment change.
     *
     * @param attachments
     */
    function handleAttachmentChange(attachments) {
        setAttachments(attachments);
    }

    /**
     * Handle agreement checkbox change.
     *
     * @param e
     */
    function handleAgreement(e) {
        formik.setFieldValue('is_agreement_accepted', e.target.checked, false);
    }

    /**
     * Check whether it is required to show captcha or not.
     *
     * @returns {*|boolean}
     */
    function isCaptchaOn() {
        return (
            options.reCaptchaOn &&
            (options.reCaptchaUserAll ||
                (isGuest() && options.reCaptchaUserGuest) ||
                (!isGuest() && options.reCaptchaUserAuth))
        );
    }

    /**
     * On captcha change. When token received from client.
     *
     * @param value
     */
    function onCaptchaChange(value) {
    }

    /**
     * Display error message from reCAPTCHA component.
     * @param error
     */
    function onCaptchaError(error) {
        dispatch(failureSnackbar(error));
    }

    /**
     * Check whether it is required to show social icons.
     *
     * @returns {*}
     */
    function showSocialIcons() {
        const isAllowedByFormType = options.isFormTypeWordpress || options.isFormTypeSocials || options.isFormTypeAll;

        return isAllowedByFormType && hasAtLeastOneSocial();
    }

    /**
     * Check whether at least one social is enabled.
     *
     * @returns {boolean}
     */
    function hasAtLeastOneSocial() {
        const socials: SocialsOption = settings.options.socials;

        if (!socials) {
            return false;
        }

        let visibleCount = 0;
        for (let key in socials) {
            if (socials[key] && socials[key].visible) {
                visibleCount++;
            }
        }

        return visibleCount > 0;
    }

    /**
     * Prepare remembered fields.
     */
    function prepareRemembered() {
        const email = get('form-email');
        const name = get('form-author_name');
        const website = get('form-author_website');
        const commentHtml = get('form-comment_html');

        let editorAvailabilityInterval = setInterval(function () {
            if (editorRef) {
                if (email !== null) {
                    formik.setFieldValue('author_email', email, false);
                }

                if (name !== null) {
                    formik.setFieldValue('author_name', name, false);
                }

                if (website !== null) {
                    formik.setFieldValue('author_website', website, false);
                }

                if (!isUpdate() && commentHtml) {
                    formik.setFieldValue('comment_html', commentHtml, false);
                }

                clearInterval(editorAvailabilityInterval);
            }
        }, 300);
    }

    /**
     * Check whether it is required to show guest fields such as name, email, website.
     *
     * @returns {*}
     */
    function showGuestFields() {
        return options.isFormTypeGuests || options.isFormTypeAll;
    }

    /**
     * Renders guest fields in order provided by options.
     */
    function renderGuestFields() {
        const inputs: GuestInputTypes = settings.options.guestInputs;

        let elementInputs: React.ReactElement[] = [];

        inputs.forEach(inputType => {
            if (inputType === 'name') {
                elementInputs.push(
                    <div className='anycomment anycomment-form__inputs-item anycomment-form__inputs-name' key='name'>
                        <label form='anycomment-author-name'>
                            {translations.name} <span className='anycomment-label-import'>*</span>
                        </label>
                        <input
                            type='text'
                            name='author_name'
                            id='anycomment-author-name'
                            value={formik.values.author_name}
                            required
                            onChange={handleFieldChange}
                        />
                    </div>
                );
            } else if (inputType === 'email') {
                elementInputs.push(
                    <div className='anycomment anycomment-form__inputs-item anycomment-form__inputs-email' key='email'>
                        <label form='anycomment-author-email'>
                            {translations.email} <span className='anycomment-label-import'>*</span>
                        </label>
                        <input
                            type='email'
                            name='author_email'
                            id='anycomment-author-email'
                            value={formik.values.author_email}
                            required
                            onChange={handleFieldChange}
                        />
                    </div>
                );
            } else if (inputType === 'website') {
                elementInputs.push(
                    <div className='anycomment-form__inputs-item anycomment-form__inputs-website' key='website'>
                        <label form='anycomment-author-website'>{translations.website}</label>
                        <input
                            type='text'
                            name='author_website'
                            id='anycomment-author-website'
                            value={formik.values.author_website}
                            onChange={handleFieldChange}
                        />
                    </div>
                );
            }
        });

        return (
            <div className={'anycomment anycomment-form__inputs anycomment-form__inputs-' + elementInputs.length}>
                {elementInputs}
            </div>
        );
    }

    const translations = settings.i18;

    let reCaptcha: null | React.ReactElement = null;

    if (isCaptchaOn() && options.reCaptchaSiteKey) {
        reCaptcha = (
            <ReCAPTCHA
                ref={recapchaRef}
                theme={options.reCaptchaTheme}
                sitekey={options.reCaptchaSiteKey}
                badge={options.reCaptchaPosition}
                onErrored={onCaptchaError}
                onChange={onCaptchaChange}
                size='invisible'
            />
        );
    }

    let formClasses = '';
    let submitStatus = '';

    if (!isGuest()) {
        formClasses = ' anycomment-form-authorized';
    }

    if (isReply()) {
        formClasses += ' anycomment-form-reply';
        submitStatus = translations.reply_to + ' ' + (comment && comment.author_name);
    } else if (isUpdate()) {
        formClasses += ' anycomment-form-update';
        submitStatus = translations.editing;
    }

    return (
        <Wrapper className={'anycomment anycomment-form' + formClasses}>
            {!isGuest() && (
                <AvatarColumn>
                    <SendCommentFormBodyAvatar />
                </AvatarColumn>
            )}
            <EditorColumn>
                <form onSubmit={formik.handleSubmit}>
                    <SendCommentFormBody
                        attachments={attachments}
                        handleEditorChange={handleEditorChange}
                        handleEditorRef={handleEditorRef}
                        commentHTML={comment_html}
                        comment={comment}
                        handleAttachmentChange={handleAttachmentChange}
                    />

                    {isGuest() && (
                        <>
                            <GuestFieldsRoot>
                                {showSocialIcons() && (
                                    <SocialListColumn>
                                        <GuestHeader>{settings.i18.login_with}</GuestHeader>
                                        <div className='anycomment anycomment-form__guest-socials'>
                                            <LoginSocialList />
                                        </div>
                                    </SocialListColumn>
                                )}
                                <GuestFieldsColumn>
                                    {showGuestFields() && <GuestHeader>{settings.i18.or_as_guest}</GuestHeader>}
                                    {showGuestFields() && renderGuestFields()}
                                </GuestFieldsColumn>
                            </GuestFieldsRoot>

                            <div className='anycomment anycomment-form__terms'>
                                <DataProcessing
                                    isAgreementAccepted={is_agreement_accepted}
                                    onAccept={handleAgreement}
                                />
                            </div>
                        </>
                    )}

                    <div className='anycomment anycomment-form__submit'>
                        {(isUpdate() || isReply()) && (
                            <div className='anycomment anycomment-form__submit-status'>
                                {submitStatus}
                                <span
                                    className='anycomment anycomment-form__submit-status-action'
                                    onClick={prepareInitialForm}
                                >
                                    <Icon icon={faTimes} />
                                </span>
                            </div>
                        )}
                        <div className='anycomment anycomment-form__submit-button'>
                            <input
                                type='submit'
                                className='anycomment-btn anycomment-send-comment-body__btn'
                                value={buttonText}
                            />
                        </div>
                    </div>

                    {reCaptcha}
                </form>
            </EditorColumn>
        </Wrapper>
    );
}

SendComment.displayName = 'SendComment';
