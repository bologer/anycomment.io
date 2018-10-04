import React from 'react';
import SendCommentGuest from './SendCommentGuest';
import SendCommentFormBody from './SendCommentFormBody';
import AnyCommentComponent from "./AnyCommentComponent";
import ReCAPTCHA from "react-google-recaptcha";
import {toast} from 'react-toastify';
import CommentSanitization from "./CommentSanitization";

const recapchaRef = React.createRef();

/**
 * Class SendCommentForm is used process comment before it will be sent.
 */
class SendCommentForm extends AnyCommentComponent {

    constructor(props) {
        super(props);

        const options = this.props.settings.options,
            settings = this.props.settings;

        this.state = {
            isAgreementAccepted: true,
            commentText: '',
            attachments: [],
            buttonText: settings.i18.button_send,
            replyId: 0,
            authorName: '',
            authorEmail: '',
            authorWebsite: '',
            replyName: '',
            editId: '',
            commentHTML: '',
        };

        this.reCaptchaSiteKey = options.reCaptchaSiteKey;
        this.reCaptchaTheme = options.reCaptchaTheme;
        this.reCaptchaBadge = options.reCaptchaBadge;

        this.quillRef = React.createRef();
        this.editorRef = null;
    }

    /**
     * Check whether reply to comment action is called.
     *
     * @returns {boolean}
     */
    isReply = () => {
        return this.props.action === 'reply';
    };

    /**
     * Check whether update comment action is called.
     *
     * @returns {boolean}
     */
    isUpdate = () => {
        return this.props.action === 'update';
    };

    /**
     * Generic method invoked when action changes.
     * Action can be either reply, update or default.
     *
     * Default being re initiate default comment form state.
     * @see prepareInitialForm
     */
    prepareForm = () => {
        const comment = this.props.comment;
        switch (this.props.action) {
            case 'reply':
                this.prepareReplyForm(comment);
                break;
            case 'update':
                this.prepareUpdateForm(comment);
                break;
            default:
                this.prepareInitialForm();
                break;
        }
    };

    /**
     * Reinit comment form. Initial comment form state.
     */
    prepareInitialForm = () => {
        this.setState({
            commentText: '',
            attachments: [],
            replyName: '',
            isReply: false,
            buttonText: this.props.settings.i18.button_send,
            replyId: 0,
            editId: '',
            commentHTML: ''
        });

        this.dropComment();
        this.props.handleUnsetAction();
    };

    /**
     * Prepare comment form for comment reply state.
     *
     * @param comment
     */
    prepareReplyForm = (comment) => {
        this.setState({
            replyName: comment.author_name,
            buttonText: this.props.settings.i18.button_reply,
            replyId: comment.id
        });

        this.focusCommentField();
    };

    /**
     * Prepare comment form for comment update state.
     *
     * @param comment
     */
    prepareUpdateForm = (comment) => {

        const commentHtml = comment.content;

        if (commentHtml !== '') {

            let states = {
                replyName: '',
                editId: comment.id,
                buttonText: this.props.settings.i18.button_save,
                commentHTML: commentHtml
            };

            if (comment.attachments || comment.attachments && comment.attachments.length > 0) {
                states.attachments = comment.attachments;
            }

            this.setState(states, () => {
                this.focusCommentField();
            });
        }
    };

    /**
     * Focus on comment field.
     */
    focusCommentField = () => {
        console.log(this.quillRef);
        // this.editorRef.current.focus();
    };

    attachQuillRefs = () => {
        // Ensure React-Quill reference is available:
        if (typeof this.quillRef.getEditor !== 'function') return;
        // Skip if Quill reference is defined:
        if (this.editorRef != null) return;

        const editorRef = this.quillRef.getEditor();
        if (editorRef != null) this.editorRef = editorRef;
    };

    /**
     * Handle attachment change.
     *
     * @param attachments
     */
    handleAttachmentChange = (attachments) => {
        this.setState({attachments: attachments});
    };

    /**
     * Handle author name change.
     * @param event
     */
    handleAuthorNameChange = (event) => {
        this.setState({authorName: event.target.value});
    };

    /**
     * Handle author email change.
     * @param event
     */
    handleAuthorEmailChange = (event) => {
        this.setState({authorEmail: event.target.value});
    };

    /**
     * Handle author website change.
     * @param event
     */
    handleAuthorWebsiteChange = (event) => {
        this.setState({authorWebsite: event.target.value})
    };

    /**
     * Handle agreement checkbox change.
     *
     * @param e
     */
    handleAgreement = (e) => {
        this.setState({isAgreementAccepted: e.target.checked});
    };

    /**
     * Handle comment text change.
     *
     * @param text
     */
    handleEditorChange = (text) => {
        this.setState({
            commentHTML: text,
        });

        this.storeComment(text);
    };

    /**
     * Authorized form.
     *
     * @param event
     * @returns {boolean}
     */
    handleAuthorized = (event) => {
        event.preventDefault();

        if (this.isGuest()) {
            return false;
        }

        const {settings} = this.props,
            {editId, replyId, attachments, commentHtml} = this.state,
            self = this,
            url = '/comments' + (editId ? ('/' + editId) : '');

        const cleanCommentHtml = CommentSanitization.sanitize(commentHtml);

        let params = {
            content: cleanCommentHtml,
        };

        if (!editId) {
            params.post = settings.postId;
            params.parent = replyId;
        }

        if (this.isCaptchaOn()) {
            params.captcha = recapchaRef.current.getValue();

            if (!params.captcha) {
                return false;
            }
        }

        if (attachments || attachments.length > 0) {
            params.attachments = JSON.stringify(attachments);
        }

        this.props.axios
            .request({
                method: 'post',
                url: url,
                data: params,
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.prepareInitialForm();
                self.props.loadComments();
                return true;
            })
            .catch(function (error) {
                self.showError(error);
            });

        return false;
    };


    /**
     * Handle guest form.
     * @param event
     * @returns {boolean}
     */
    handleGuest = (event) => {
        event.preventDefault();

        if (!this.isGuest()) {
            return false;
        }

        const settings = this.getSettings(),
            {editId, replyId, attachments, authorName, authorEmail, commentHtml, authorWebsite} = this.state,
            self = this;

        const url = '/comments';

        const cleanCommentHtml = CommentSanitization.sanitize(commentHtml);

        let params = {
            content: cleanCommentHtml,
        };

        if (!settings.options.isFormTypeSocials) {
            params.author_name = authorName;

            if (authorEmail !== '') {
                params.author_email = authorEmail;
            }

            if (authorWebsite !== '') {
                params.author_url = authorWebsite;
            }

            this.storeAuthorName(authorName);
            this.storeAuthorEmail(authorEmail);
            this.storeAuthorWebsite(authorWebsite);
        }

        if (!editId) {

            params.post = settings.postId;
            params.parent = replyId;
        }

        if (this.isCaptchaOn()) {
            params.captcha = recapchaRef.current.getValue();

            if (!params.captcha) {
                return false;
            }
        }

        if (attachments || attachments.length > 0) {
            params.attachments = JSON.stringify(attachments);
        }

        this.props.axios
            .request({
                method: 'post',
                url: url,
                params: params,
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.prepareInitialForm();
                self.props.loadComments();
                return true;
            })
            .catch(function (error) {
                self.showError(error);

            });

        return false;
    };

    /**
     * Handle form submit for guest and authorized clients.
     * @param event
     * @returns {boolean}
     */
    handleSubmit = (event) => {
        event.preventDefault();

        const self = this;

        if (this.isCaptchaOn()) {
            recapchaRef.current.execute();

            let checkValueInterval = setInterval(function () {
                if (recapchaRef.current.getValue() !== '') {
                    clearInterval(checkValueInterval);

                    if (!self.isGuest()) {
                        return self.handleAuthorized(event);
                    }

                    return self.handleGuest(event);
                }
            }, 100);
        } else {
            if (!this.isGuest()) {
                return self.handleAuthorized(event);
            }

            return self.handleGuest(event);
        }
    };

    /**
     * Check whether it is required to show captcha or not.
     *
     * @returns {*|boolean}
     */
    isCaptchaOn() {
        const options = this.getOptions();

        return options.reCaptchaOn && (options.reCaptchaUserAll || (this.isGuest() && options.reCaptchaUserGuest) || (!this.isGuest() && options.reCaptchaUserAuth));
    }

    /**
     * On captcha change. When token received from client.
     *
     * @param value
     */
    onCaptchaChange(value) {
    }

    static onCaptchaError(error) {
        toast.error(error);
    }

    /**
     * Prepare remembered fields.
     */
    prepareRemembered = () => {
        const email = this.getAuthorEmail(),
            name = this.getAuthorName(),
            website = this.getAuthorWebsite(),
            commentHtml = this.getComment();

        let state = {};

        if (email !== '') {
            state.authorEmail = email;
        }

        if (name !== '') {
            state.authorName = name;
        }

        if (website !== '') {
            state.authorWebsite = website;
        }

        if (commentHtml !== '') {
            state.commentHtml = commentHtml;
        }


        if (state !== {}) {
            this.setState(state);
        }
    };

    componentDidMount() {
        this.prepareRemembered();
    }

    componentDidUpdate(prevProps) {
        // Typical usage (don't forget to compare props):
        if (this.props.action !== prevProps.action || this.props.comment.id !== prevProps.comment.id) {
            this.prepareForm();
        }
    }

    render() {
        const settings = this.getSettings();
        const translations = settings.i18;

        let reCaptcha = '';

        if (this.isCaptchaOn()) {
            reCaptcha = <ReCAPTCHA
                ref={recapchaRef}
                theme={this.reCaptchaTheme}
                sitekey={this.reCaptchaSiteKey}
                badge={this.reCaptchaBadge}
                onErrored={this.onCaptchaError}
                onChange={this.onCaptchaChange}
                size="invisible"
            />;
        }

        return (
            <div className="anycomment anycomment-send-comment-body">
                <form onSubmit={this.handleSubmit}>

                    <SendCommentFormBody {...this.props}
                                         attachments={this.state.attachments}
                                         handleEditorChange={this.handleEditorChange}
                                         editorRef={this.quillRef}
                                         commentHTML={this.state.commentHTML}
                                         handleAttachmentChange={this.handleAttachmentChange}/>

                    {this.isGuest() ?
                        <SendCommentGuest {...this.state}
                                          handleAuthorNameChange={this.handleAuthorNameChange}
                                          handleAuthorEmailChange={this.handleAuthorEmailChange}
                                          handleAuthorWebsiteChange={this.handleAuthorWebsiteChange}
                                          handleAgreement={this.handleAgreement}
                                          isAgreementAccepted={this.state.isAgreementAccepted}/> :
                        <input type="submit" disabled={!this.state.isAgreementAccepted}
                               className="anycomment-btn anycomment-send-comment-body__btn"
                               value={this.state.buttonText}/>
                    }

                    <input
                        type="hidden"
                        name="parent"
                        value={this.state.replyId}
                        className="anycomment"
                        onChange={this.handleReplyIdChange}/>

                    {this.isReply() ? <div className="clearfix"></div> : ''}

                    {this.isReply() ?
                        <div
                            className="anycomment anycomment-send-comment-body-reply">{translations.reply_to} {this.state.replyName}
                            <span onClick={this.prepareInitialForm}>{translations.cancel}</span></div>
                        : ''}

                    {!this.isGuest() ? <input type="hidden" name="edit_id" value={this.state.editId}
                                              onChange={this.handleEditIdChange}/> : ''}

                    {reCaptcha}
                </form>

                <div className="clearfix"></div>
            </div>
        );
    }
}

export default SendCommentForm;