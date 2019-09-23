import React from 'react';
import SendCommentGuest from './SendCommentGuest';
import SendCommentFormBody from './SendCommentFormBody';
import AnyCommentComponent from "./AnyCommentComponent";
import ReCAPTCHA from "react-google-recaptcha";
import {toast} from 'react-toastify';
import DataProcessing from './DataProcessing'
import Icon from './Icon'
import {faTimes} from '@fortawesome/free-solid-svg-icons'

const recapchaRef = React.createRef();

/**
 * Class SendComment is used process comment before it will be sent.
 */
class SendComment extends AnyCommentComponent {

    constructor(props) {
        super(props);

        const options = this.props.settings.options,
            settings = this.props.settings;

        this.state = {
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
            buttonEnabled: false,
            isAgreementAccepted: false,
        };

        this.reCaptchaSiteKey = options.reCaptchaSiteKey;
        this.reCaptchaTheme = options.reCaptchaTheme;
        this.reCaptchaBadge = options.reCaptchaPosition;

        this.editorRef = React.createRef();
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
     * Check whether comment field is empty or not.
     *
     * @returns {boolean}
     */
    isCommentEmpty = (text = null) => {

        text = text || this.state.commentHTML;

        if (text.trim() === '') {
            return true;
        }

        const re = /^<p>(<br>|<br\/>|<br\s\/>|\s+|)<\/p>$/gm;
        return re.test(text);
    };

    /**
     * Generic method invoked when action changes.
     * Action can be either reply, update or default.
     *
     * Default being re initiate default comment form state.
     * @see prepareInitialForm
     */
    prepareForm = () => {
        switch (this.props.action) {
            case 'reply':
                this.prepareReplyForm(this.props.comment);
                break;
            case 'update':
                this.prepareUpdateForm(this.props.comment);
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
            buttonText: this.props.settings.i18.button_send,
            replyId: 0,
            editId: '',
            commentHTML: '',
            buttonEnabled: false
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
            replyId: comment.id,
            editId: '',
            commentHTML: '',
            buttonEnabled: false
        });

        this.focusCommentField();
    };

    /**
     * Prepare comment form for comment update state.
     *
     * @param comment
     */
    prepareUpdateForm = (comment) => {

        const commentHtml = comment.content.trim();

        if (commentHtml !== '') {

            let states = {
                replyName: '',
                editId: comment.id,
                buttonText: this.props.settings.i18.button_save,
                commentHTML: comment.content,
                buttonEnabled: true
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
        const editor = this.editorRef.current;

        if (editor) {
            editor.focus();
        }
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
     * Handle comment text change.
     *
     * @param text
     */
    handleEditorChange = (text) => {

        let params = {commentHTML: text};

        params.buttonEnabled = !this.isCommentEmpty(text);

        this.setState(params);
        this.storeComment(text);
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
            {editId, replyId, attachments, commentHTML} = this.state,
            self = this,
            url = '/comments' + (editId ? ('/' + editId) : '');

        let params = {
            content: commentHTML,
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

        let headers = {};

        if (settings.nonce) {
            headers = {'X-WP-Nonce': settings.nonce};
        }

        this.props.axios
            .request({
                method: 'post',
                url: url,
                data: params,
                headers: headers
            })
            .then(function (response) {
                self.prepareInitialForm();
                self.props.handleJustAdded();
                self.props.loadComments();

                const {data} = response;

                if(data && data.response && typeof data.response === 'string') {
                    toast.success(data.response);
                }

                return true;
            })
            .catch(function (error) {
                self.setState({buttonEnabled: true});
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
            {editId, replyId, attachments, authorName, authorEmail, commentHTML, authorWebsite} = this.state,
            self = this;

        const url = '/comments';

        let params = {
            content: commentHTML,
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

        let headers = {};

        if (settings.nonce) {
            headers = {'X-WP-Nonce': settings.nonce};
        }

        this.props.axios
            .request({
                method: 'post',
                url: url,
                data: params,
                headers: headers
            })
            .then(function (response) {
                self.prepareInitialForm();
                self.props.handleJustAdded();
                self.props.loadComments();

                const {data} = response;

                if(data && data.response && typeof data.response === 'string') {
                    toast.success(data.response);
                }

                return true;
            })
            .catch(function (error) {
                self.setState({buttonEnabled: true});
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

        this.setState({buttonEnabled: false});

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
            commentHtml = this.getComment(),
            self = this;

        let state = {};

        let editorAvailabilityInterval = setInterval(function () {
            if (self.editorRef.current) {
                if (email !== '') {
                    state.authorEmail = email;
                }

                if (name !== '') {
                    state.authorName = name;
                }

                if (website !== '') {
                    state.authorWebsite = website;
                }

                // if this is not update and comment is not empty
                // should set remembered comment, otherwise remembered
                // comment would overwrite what was pasted from editing
                // comment content
                if (!self.isUpdate() && commentHtml !== '') {
                    state.commentHTML = commentHtml;
                }

                if (state !== {}) {
                    self.setState(state);
                }

                clearInterval(editorAvailabilityInterval);
            }
        }, 300);
    };

    componentDidMount() {
        this.prepareRemembered();
        this.prepareForm();
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

        const {
            buttonEnabled,
            buttonText,
            replyName,
            attachments,
            commentHTML,
            editId,
            replyId,
            isAgreementAccepted
        } = this.state;

        let reCaptcha = '';

        if (this.isCaptchaOn() && this.reCaptchaSiteKey) {
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

        const submitButton = <input type="submit" disabled={!buttonEnabled}
                                    className="anycomment-btn anycomment-send-comment-body__btn"
                                    value={buttonText}/>;

        let formClasses = '',
            submitStatus = '';

        if (!this.isGuest()) {
            formClasses = ' anycomment-form-authorized';
        }

        if (this.isReply()) {
            formClasses += ' anycomment-form-reply';
            submitStatus = translations.reply_to + " " + replyName;
        } else if (this.isUpdate()) {
            formClasses += ' anycomment-form-update';
            submitStatus = translations.editing;
        }

        return (
            <div
                className={"anycomment anycomment-form" + formClasses}>
                <form onSubmit={this.handleSubmit}>

                    {this.isGuest() ?
                        <SendCommentGuest {...this.state}
                                          handleAuthorNameChange={this.handleAuthorNameChange}
                                          handleAuthorEmailChange={this.handleAuthorEmailChange}
                                          handleAuthorWebsiteChange={this.handleAuthorWebsiteChange}/> : ''}

                    <SendCommentFormBody {...this.props}
                                         attachments={attachments}
                                         handleEditorChange={this.handleEditorChange}
                                         editorRef={this.editorRef}
                                         commentHTML={commentHTML}
                                         handleAttachmentChange={this.handleAttachmentChange}/>


                    <div className="anycomment anycomment-form__terms">
                        {this.isGuest() ? <DataProcessing isAgreementAccepted={isAgreementAccepted}
                                                          onAccept={this.handleAgreement}/> : ''}
                    </div>

                    <div className="anycomment anycomment-form__submit">
                        {this.isUpdate() || this.isReply() ? <div
                            className="anycomment anycomment-form__submit-status">{submitStatus}
                            <span className="anycomment anycomment-form__submit-status-action"
                                  onClick={this.prepareInitialForm}><Icon icon={faTimes}/></span>
                        </div> : ''}
                        <div className="anycomment anycomment-form__submit-button">
                            {submitButton}
                        </div>
                    </div>

                    <input
                        type="hidden"
                        name="parent"
                        value={replyId}
                        className="anycomment"
                        onChange={this.handleReplyIdChange}/>


                    {!this.isGuest() ? <input type="hidden" name="edit_id" value={editId}
                                              onChange={this.handleEditIdChange}/> : ''}

                    {reCaptcha}
                </form>
            </div>
        );
    }
}

export default SendComment;
