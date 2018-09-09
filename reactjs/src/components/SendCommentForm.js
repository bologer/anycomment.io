import React from 'react';
import SendCommentGuest from './SendCommentGuest';
import SendCommentFormBody from './SendCommentFormBody';
import AnyCommentComponent from "./AnyCommentComponent";

/**
 * Class SendCommentForm is used process comment before it will be sent.
 */
class SendCommentForm extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            isAgreementAccepted: true
        };

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleContentChange = this.handleContentChange.bind(this);
        this.handleReplyIdChange = this.handleReplyIdChange.bind(this);
        this.handleEditIdChange = this.handleEditIdChange.bind(this);

        this.handleAgreement = this.handleAgreement.bind(this);
    }

    /**
     * Handle comment change.
     * @param e
     */
    handleContentChange(e) {
        this.props.onCommentTextChange(e.target.value);
    };

    /**
     * Handle change of parent comment (reply).
     * @param e
     */
    handleReplyIdChange(e) {
        this.props.onReplyIdChange(e.target.value);
    };

    /**
     * Handle edit ID change.
     * @param e
     */
    handleEditIdChange(e) {
        this.props.onEditIdChange(e.target.value);
    };

    /**
     * Handle agreement check.
     *
     * @param e
     */
    handleAgreement(e) {
        this.setState({isAgreementAccepted: e.target.checked});
    }

    /**
     * Authorized form.
     *
     * @param event
     * @returns {boolean}
     */
    handleAuthorized(event) {
        event.preventDefault();

        if (this.isGuest()) {
            return false;
        }

        const settings = this.props.settings;
        const self = this;

        const url = '/comments' + (this.props.editId ? ('/' + this.props.editId) : '');

        let params = {
            content: this.props.commentText,
        };

        if (!this.props.editId) {
            params.post = settings.postId;
            params.parent = this.props.replyId;
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

        const name = this.props.authorName;
        const email = this.props.authorEmail;
        const website = this.props.authorWebsite;

        if (!settings.options.isFormTypeSocials) {
            params.author_name = name;

            if (email !== '') {
                params.author_email = email;
            }

            if (website !== '') {
                params.author_url = website;
            }

            this.storeAuthorName(name);
            this.storeAuthorEmail(email);
            this.storeAuthorWebsite(website);
        }

        if (!this.props.editId) {
            params.post = settings.postId;
            params.parent = this.props.replyId;
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
    handleSubmit(event) {
        event.preventDefault();

        if (!this.isGuest()) {
            return this.handleAuthorized(event);
        }

        return this.handleGuest(event);
    };


    render() {
        const settings = this.getSettings();
        const translations = settings.i18;

        return (
            <div className="anycomment anycomment-send-comment-body">
                <form onSubmit={this.handleSubmit}>

                    <SendCommentFormBody {...this.props} handleContentChange={this.handleContentChange}
                                         changeCommenText={this.props.onCommentTextChange}/>

                    {this.isGuest() ?
                        <SendCommentGuest {...this.props} handleAgreement={this.handleAgreement}
                                          isAgreementAccepted={this.state.isAgreementAccepted}/> :
                        <div className="">
                            <a href={settings.urls.logout}>{translations.logout}</a>
                            <input type="submit" className="anycomment-btn anycomment-send-comment-body__btn"
                                   value={this.props.buttonText}/>
                            <div className="clearfix"></div>
                        </div>
                    }

                    <input
                        type="hidden"
                        name="parent"
                        value={this.props.replyId}
                        className="anycomment"
                        onChange={this.handleReplyIdChange}/>

                    {this.props.isReply ? <div className="clearfix"></div> : ''}

                    {this.props.isReply ?
                        <div
                            className="anycomment anycomment-send-comment-body-reply">{translations.reply_to} {this.props.replyName}
                            <span onClick={this.props.onReplyCancel}>{translations.cancel}</span></div>
                        : ''}

                    {!this.isGuest() ? <input type="hidden" name="edit_id" value={this.props.editId}
                                              onChange={this.handleEditIdChange}/> : ''}
                </form>

                <div className="clearfix"></div>
            </div>
        );
    }
}

export default SendCommentForm;