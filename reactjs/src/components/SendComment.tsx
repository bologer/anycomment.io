import React, {useEffect, useRef, useState} from 'react';
import SendCommentGuest from './CommentFormGuest';
import SendCommentFormBody from './SendCommentFormBody';
import SendCommentFormBodyAvatar from './SendCommentFormBodyAvatar'
import ReCAPTCHA from "react-google-recaptcha";
import {toast} from 'react-toastify';
import DataProcessing from './DataProcessing'
import Icon from './Icon'
import {faTimes} from '@fortawesome/free-solid-svg-icons'
import {useOptions, useSettings} from "~/hooks/setting";
import {useFormik} from "formik";
import {useDispatch, useSelector} from "react-redux";
import {
    fetchCommentsSalient,
    fetchCreateComment,
    invalidateCommentForm,
} from "~/core/comment/CommentActions";
import {StoreProps} from "~/store/reducers";
import {CommentReducerProps} from "~/core/comment/commentReducers";
import {manageReducer} from "~/helpers/action";
import {isGuest} from "~/helpers/user";
import ReactQuill from "react-quill";
import {CommentModel} from "~/typings/models/CommentModel";
import styled from 'styled-components';
import LoginSocialList from "~/components/LoginSocialList";
import {SocialItemOption} from "~/components/AnyCommentProvider";

const recapchaRef = React.createRef();

export interface SendCommentProps {
    action: 'reply' | 'update' | undefined;
    comment?: CommentModel;
    handleUnsetAction: () => void; //todo: adopt
    handleJustAdded: () => void; //todo: adopt
}

export interface FormValues {
    reply_id: number;
    author_name: string;
    author_email: string;
    author_website: string;
    reply_name: string;
    edit_id: string;
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

/**
 * Class SendComment is used process comment before it will be sent.
 */
export default function SendComment({
    action,
    comment,
    handleUnsetAction,
    handleJustAdded,
}: SendCommentProps) {

    const dispatch = useDispatch();
    const {create} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);
    const options = useOptions();
    const settings = useSettings();

    const [attachments, setAttachments] = useState<[]>([]);
    const [button_text, setButtonText] = useState(settings.i18.button_send);

    const formik = useFormik<FormValues>({
        initialValues: {
            reply_id: 0,
            author_name: '',
            author_email: '',
            author_website: '',
            reply_name: '',
            edit_id: '',
            comment_html: '',
            is_agreement_accepted: false,
        },
        onSubmit: values => {

            // if (isCaptchaOn()) {
            //     recapchaRef.current.execute();
            //
            //     let checkValueInterval = setInterval(function() {
            //         if (recapchaRef.current.getValue() !== '') {
            //             clearInterval(checkValueInterval);
            //
            //             if (!self.isGuest()) {
            //                 return self.handleAuthorized(event);
            //             }
            //
            //             return handleGuest(event);

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

                // this.storeAuthorName(author_name);
                // this.storeAuthorEmail(author_email);
                // this.storeAuthorWebsite(author_website);
            }

            dispatch(fetchCreateComment(settings.postId, data));
        },
    });

    const {
        reply_id,
        author_name,
        author_email,
        author_website,
        reply_name,
        edit_id,
        comment_html,
        is_agreement_accepted,
    } = formik.values;

    const editorRef = useRef<ReactQuill>(null);

    useEffect(() => {
        prepareRemembered();
        prepareForm();
    }, [action, comment]);

    useEffect(() => {
        manageReducer({
            reducer: create,
            onSuccess: () => {
                prepareInitialForm();
                handleJustAdded();
                dispatch(fetchCommentsSalient({postId: settings.postId, order: options.sort_order}));

                // const {data} = response;
                //
                // if (data && data.response && typeof data.response === 'string') {
                //     toast.success(data.response);
                // }
            },
        });
    }, [create]);

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
                prepareReplyForm(comment);
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
        formik.setFieldValue('comment_text', '');
        formik.setFieldValue('reply_name', '');
        formik.setFieldValue('button_text', settings.i18.button_send);
        formik.setFieldValue('reply_id', 0);
        formik.setFieldValue('edit_id', '');
        formik.setFieldValue('comment_html', '');
        setAttachments([]);
        // dropComment();
        handleUnsetAction();
        dispatch(invalidateCommentForm());
    }

    /**
     * Prepare comment form for comment reply state.
     *
     * @param comment
     */
    function prepareReplyForm(comment) {

        formik.setValues({
            ...formik.values,
            reply_name: comment.author_name,
            edit_id: '',
            comment_html: '',
        }, false);

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

            formik.setValues({
                ...formik.values,
                reply_name: '',
                edit_id: comment.id,
                comment_html: comment.content,
            });

            setButtonText(settings.i18.button_save);

            if (comment.attachments && comment.attachments.length > 0) {
                setAttachments(comment.attachments);
            }

            focusCommentField();
        }
    }

    /**
     * Focus on comment field.
     */
    function focusCommentField() {
        const editor = editorRef && editorRef.current;

        if (editor) {
            editor.focus();
        }
    }

    /**
     * Handle comment text change.
     *
     * @param text
     */
    function handleEditorChange(text) {
        formik.setFieldValue('comment_html', text, false);
        // storeComment(text);
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
        return options.reCaptchaOn && (options.reCaptchaUserAll || (isGuest() && options.reCaptchaUserGuest) || (!isGuest() && options.reCaptchaUserAuth));
    }

    /**
     * On captcha change. When token received from client.
     *
     * @param value
     */
    function onCaptchaChange(value) {
    }

    function onCaptchaError(error) {
        toast.error(error);
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
        const socials: SocialItemOption[] = settings.options.socials;

        if (!socials) {
            return false;
        }

        let visibleCount = 0;
        for (let key in socials) {
            if (socials.hasOwnProperty(key) && socials[key].visible) {
                visibleCount++;
            }
        }

        return visibleCount > 0;
    }

    /**
     * Prepare remembered fields.
     */
    function prepareRemembered() {
        // const email = this.getAuthorEmail(),
        //     name = this.getAuthorName(),
        //     website = this.getAuthorWebsite(),
        //     commentHtml = this.getComment(),
        //     self = this;
        //
        // let state = {};
        //
        // let editorAvailabilityInterval = setInterval(function() {
        //     if (editorRef.current) {
        //         if (email !== '') {
        //             state.author_email = email;
        //         }
        //
        //         if (name !== '') {
        //             state.author_name = name;
        //         }
        //
        //         if (website !== '') {
        //             state.author_website = website;
        //         }
        //
        //         // if this is not update and comment is not empty
        //         // should set remembered comment, otherwise remembered
        //         // comment would overwrite what was pasted from editing
        //         // comment content
        //         if (!self.isUpdate() && commentHtml !== '') {
        //             state.comment_html = commentHtml;
        //         }
        //
        //         if (state !== {}) {
        //             self.setState(state);
        //         }
        //
        //         clearInterval(editorAvailabilityInterval);
        //     }
        // }, 300);
    }

    const translations = settings.i18;

    let reCaptcha = '';

    if (isCaptchaOn() && options.reCaptchaSiteKey) {
        // reCaptcha = <ReCAPTCHA
        //     ref={recapchaRef}
        //     theme={options.reCaptchaTheme}
        //     sitekey={options.reCaptchaSiteKey}
        //     badge={reCaptchaBadge}
        //     onErrored={onCaptchaError}
        //     onChange={onCaptchaChange}
        //     size="invisible"
        // />;
    }

    let formClasses = '',
        submitStatus = '';

    if (!isGuest()) {
        formClasses = ' anycomment-form-authorized';
    }

    if (isReply()) {
        formClasses += ' anycomment-form-reply';
        submitStatus = translations.reply_to + " " + reply_name;
    } else if (isUpdate()) {
        formClasses += ' anycomment-form-update';
        submitStatus = translations.editing;
    }

    return (

        <Wrapper className={"anycomment anycomment-form" + formClasses}>
            {!isGuest() && (
                <AvatarColumn>
                    <SendCommentFormBodyAvatar />
                </AvatarColumn>
            )}
            <EditorColumn>
                <form onSubmit={formik.handleSubmit}>
                    <SendCommentFormBody
                        comment={comment}
                        attachments={attachments}
                        handleEditorChange={handleEditorChange}
                        editorRef={editorRef}
                        commentHTML={comment_html}
                        handleAttachmentChange={handleAttachmentChange} />

                    {isGuest() && (
                        <>
                            <GuestFieldsRoot>
                                {showSocialIcons() && (
                                    <SocialListColumn>
                                        <GuestHeader>{settings.i18.login_with}</GuestHeader>
                                        <div className="anycomment anycomment-form__guest-socials">
                                            <LoginSocialList />
                                        </div>
                                    </SocialListColumn>
                                )}
                                <GuestFieldsColumn>
                                    {options.isFormTypeAll && <GuestHeader>{settings.i18.or_as_guest}</GuestHeader>}
                                    <SendCommentGuest formik={formik} />
                                </GuestFieldsColumn>
                            </GuestFieldsRoot>

                            <div className="anycomment anycomment-form__terms">
                                <DataProcessing
                                    isAgreementAccepted={is_agreement_accepted}
                                    onAccept={handleAgreement} />
                            </div>
                        </>
                    )}

                    <div className="anycomment anycomment-form__submit">
                        {isUpdate() || isReply() && (
                            <div
                                className="anycomment anycomment-form__submit-status">{submitStatus}
                                <span className="anycomment anycomment-form__submit-status-action"
                                      onClick={prepareInitialForm}><Icon icon={faTimes} /></span>
                            </div>
                        )}
                        <div className="anycomment anycomment-form__submit-button">
                            <input
                                type="submit"
                                className="anycomment-btn anycomment-send-comment-body__btn"
                                value={button_text}
                            />
                        </div>
                    </div>

                    <input
                        type="hidden"
                        name="parent"
                        value={reply_id}
                        className="anycomment"
                        onChange={formik.handleChange}
                    />
                    {reCaptcha}
                </form>
            </EditorColumn>
        </Wrapper>
    );
}
