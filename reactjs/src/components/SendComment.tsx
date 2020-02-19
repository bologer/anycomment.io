import React, {useEffect, useRef, useState} from 'react';
import SendCommentGuest from './CommentFormGuest';
import SendCommentFormBody from './SendCommentFormBody';
import ReCAPTCHA from "react-google-recaptcha";
import {toast} from 'react-toastify';
import DataProcessing from './DataProcessing'
import Icon from './Icon'
import {faTimes} from '@fortawesome/free-solid-svg-icons'
import {useOptions, useSettings} from "~/hooks/setting";
import {useFormik} from "formik";
import {CommentItem} from "~/typings/models/CommentItem";
import {useDispatch, useSelector} from "react-redux";
import {fetchComments, fetchCreateComment} from "~/core/comment/CommentActions";
import {StoreProps} from "~/store/reducers";
import {CommentReducerProps} from "~/core/comment/commentReducers";
import {manageReducer} from "~/helpers/action";
import {isGuest} from "~/helpers/user";
import ReactQuill from "react-quill";

const recapchaRef = React.createRef();

export interface SendCommentProps {
    action: 'reply' | 'update' | undefined;
    comment?: CommentItem;
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
    const [button_enabled, setButtonEnabled] = useState(false);

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


            setButtonEnabled(false);

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

            let params = {
                content: comment_html,
                post: settings.postId,
            };

            if (isGuest() && !edit_id) {
                params.parent = reply_id;
            }

            if (isCaptchaOn()) {
                params.captcha = recapchaRef.current.getValue();

                if (!params.captcha) {
                    return false;
                }
            }

            if (attachments && attachments.length > 0) {
                params.attachments = JSON.stringify(attachments);
            }

            if (isGuest() && !settings.options.isFormTypeSocials) {
                params.author_name = author_name;

                if (author_email !== '') {
                    params.author_email = author_email;
                }

                if (author_website !== '') {
                    params.author_url = author_website;
                }

                // this.storeAuthorName(author_name);
                // this.storeAuthorEmail(author_email);
                // this.storeAuthorWebsite(author_website);
            }

            dispatch(fetchCreateComment(params));
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
    }, []);

    useEffect(() => {
        manageReducer({
            reducer: create,
            onSuccess: () => {
                prepareInitialForm();
                handleJustAdded();
                dispatch(fetchComments({postId: settings.postId, order: options.sort_order}));

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
     * Check whether comment field is empty or not.
     *
     * @returns {boolean}
     */
    function isCommentEmpty(text: string) {
        if (text.trim() === '') {
            return true;
        }

        const re = /^<p>(<br>|<br\/>|<br\s\/>|\s+|)<\/p>$/gm;
        return re.test(text);
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
        formik.setFieldValue('button_enabled', false);
        setAttachments([]);
        // dropComment();
        handleUnsetAction();
    }

    /**
     * Prepare comment form for comment reply state.
     *
     * @param comment
     */
    function prepareReplyForm(comment) {

        formik.setFieldValue('reply_name', comment.author_name);
        formik.setFieldValue('edit_id', '');
        formik.setFieldValue('comment_html', '');
        setButtonText(settings.i18.button_reply);
        setButtonEnabled(false);

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
            formik.setFieldValue('reply_name', '');
            formik.setFieldValue('edit_id', comment.id);
            formik.setFieldValue('comment_html', comment.content);

            setButtonText(settings.i18.button_save);
            setButtonEnabled(true);

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
        setButtonEnabled(!isCommentEmpty(text));

        formik.setFieldValue('comment_html', text);
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
        formik.setFieldValue('is_agreement_accepted', e.target.checked);
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
        <div
            className={"anycomment anycomment-form" + formClasses}>
            <form onSubmit={formik.handleSubmit}>
                {isGuest() ?
                    <SendCommentGuest formik={formik} /> :
                    <SendCommentFormBody
                        attachments={attachments}
                        handleEditorChange={handleEditorChange}
                        editorRef={editorRef}
                        commentHTML={comment_html}
                        handleAttachmentChange={handleAttachmentChange} />}


                <div className="anycomment anycomment-form__terms">
                    {isGuest() && (
                        <DataProcessing isAgreementAccepted={is_agreement_accepted}
                                        onAccept={handleAgreement} />
                    )}
                </div>

                <div className="anycomment anycomment-form__submit">
                    {isUpdate() || isReply() ? <div
                        className="anycomment anycomment-form__submit-status">{submitStatus}
                        <span className="anycomment anycomment-form__submit-status-action"
                              onClick={prepareInitialForm}><Icon icon={faTimes} /></span>
                    </div> : ''}
                    <div className="anycomment anycomment-form__submit-button">
                        <input type="submit" disabled={!button_enabled}
                               className="anycomment-btn anycomment-send-comment-body__btn"
                               value={button_text} />
                    </div>
                </div>

                <input
                    type="hidden"
                    name="parent"
                    value={reply_id}
                    className="anycomment"
                    onChange={formik.handleChange} />

                {!isGuest() && (
                    <input type="hidden" name="edit_id" value={edit_id} onChange={formik.handleChange} />
                )}

                {reCaptcha}
            </form>
        </div>
    );
}
