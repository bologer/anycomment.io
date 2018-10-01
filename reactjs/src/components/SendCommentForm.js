import React from 'react';
import SendCommentGuest from './SendCommentGuest';
import SendCommentFormBody from './SendCommentFormBody';
import AnyCommentComponent from "./AnyCommentComponent";
import ReCAPTCHA from "react-google-recaptcha";
import {toast} from 'react-toastify';
import $ from "jquery";
import {EditorState} from "draft-js";
import {stateToHTML} from 'draft-js-export-html';
import {stateFromHTML} from 'draft-js-import-html';

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
            reCaptchaSiteKey: options.reCaptchaSiteKey,
            reCaptchaTheme: options.reCaptchaTheme,
            reCaptchaBadge: options.reCaptchaBadge,

            commentText: '',
            attachments: [],
            buttonText: settings.i18.button_send,
            replyId: 0,
            authorName: '',
            authorEmail: '',
            authorWebsite: '',
            replyName: '',
            editId: '',
            editorState: EditorState.createEmpty(),
        };

        this.setDomEditorRef = ref => this.domEditor = ref;
    }

    isReply = () => {
        return this.props.action === 'reply';
    };

    isUpdate = () => {
        return this.props.action === 'update';
    };

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

    prepareInitialForm = () => {
        this.setState({
            commentText: '',
            attachments: [],
            replyName: '',
            isReply: false,
            buttonText: this.props.settings.i18.button_send,
            replyId: 0,
            editId: '',
            editorState: EditorState.createEmpty()
        });

        this.props.handleUnsetAction();
    };

    prepareReplyForm = (comment) => {
        this.setState({
            replyName: comment.author_name,
            buttonText: this.props.settings.i18.button_reply,
            replyId: comment.id
        });

        this.focusCommentField();
    };

    prepareUpdateForm = (comment) => {

        const contentBlock = stateFromHTML(comment.content);

        if (contentBlock) {
            const editorState = EditorState.createWithContent(contentBlock);

            let states = {
                replyName: '',
                editId: comment.id,
                buttonText: this.props.settings.i18.button_save,
                editorState: editorState
            };


            if (comment.attachments || comment.attachments.length > 0) {
                states.attachments = comment.attachments;
            }

            this.setState(states, () => {
                this.focusCommentField();
            });

        }
    };

    /**
     * Toggle expand/shrink animation of textarea.
     * @returns {boolean}
     */
    expandCommentField = () => {
        const el = $(this.commentFieldRef.current);

        if (el.hasClass('expanded')) {
            return false;
        }

        el.addClass('expanded');
        el.animate({height: 150}, 300);
    };

    /**
     * Check whether comment text is not empty.
     *
     * @returns {boolean}
     */
    isCommentTextEmpty = () => {
        return this.state.commentText.trim() === '';
    };

    /**
     * Focus on comment field.
     */
    focusCommentField = () => {
        this.domEditor.focus();
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
     * Handle comment text change.
     * @param text {String} Text to bet set.
     * @param append {Boolean} If true, specified text will be appended to the comment text if not empty.
     */
    handleCommentTextChange = (text, append = false) => {

        // When append and comment text is already not empty, should be current text + the specified one
        if (append && !this.isCommentTextEmpty()) {
            text = this.state.commentText + text;
        }

        this.setState({commentText: text});
        this.storeComment(text);
        this.expandCommentField();
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
     * Handle comment change.
     * @param e
     */
    handleContentChange = (e) => {
        this.props.onCommentTextChange(e.target.value);
    };

    /**
     * Handle agreement check.
     *
     * @param e
     */
    handleAgreement = (e) => {
        this.setState({isAgreementAccepted: e.target.checked});
    };

    handleEditorStateChange = (editorState) => {
        this.setState({
            editorState,
        });
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
            {editId, replyId, attachments, editorState} = this.state,
            self = this,
            url = '/comments' + (editId ? ('/' + editId) : '');

        const content = stateToHTML(editorState.getCurrentContent());

        let params = {
            content: content,
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
    handleGuest(event) {
        event.preventDefault();

        if (!this.isGuest()) {
            return false;
        }

        const settings = this.getSettings();
        const self = this;

        const url = '/comments';

        let params = {
            content: this.props.commentText,
        };

        const {authorName, authorEmail, authorWebsite, attachments} = this.props;

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

        if (!this.props.editId) {

            params.post = settings.postId;
            params.parent = this.props.replyId;
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
                self.props.onSend(response.data);

                return true;
            })
            .catch(function (error) {
                self.showError(error);

            });

        return false;
    }

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
                theme={this.state.reCaptchaTheme}
                sitekey={this.state.reCaptchaSiteKey}
                badge={this.state.reCaptchaBadge}
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
                                         editorState={this.state.editorState}
                                         handleEditorStateChange={this.handleEditorStateChange}
                                         domEditorRef={this.setDomEditorRef}
                                         handleAttachmentChange={this.handleAttachmentChange}/>

                    {this.isGuest() ?
                        <SendCommentGuest {...this.props}
                                          isAgreementAccepted={this.state.isAgreementAccepted}/> :
                        <input type="submit" className="anycomment-btn anycomment-send-comment-body__btn"
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