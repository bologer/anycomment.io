import React from 'react';
import SendCommentGuest from './SendCommentGuest.js';
import SendCommentAuth from './SendCommentAuth.js';
import AnyCommentComponent from "./AnyCommentComponent";
import {toast} from "react-toastify";

class SendCommentForm extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            isShouldLogin: false,
        };

        this.handleSubmit = this.handleSubmit.bind(this);
        this.shouldLogin = this.shouldLogin.bind(this);
        this.handleContentChange = this.handleContentChange.bind(this);
        this.handleReplyIdChange = this.handleReplyIdChange.bind(this);
        this.handleEditIdChange = this.handleEditIdChange.bind(this);
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

    handleSubmit(event) {
        event.preventDefault();

        if (this.props.user === null) {
            return false;
        }

        const settings = this.props.settings;
        const self = this;

        const url = '/comments' + (this.props.editId ? ('/' + this.props.editId) : '');

        let params = {
            content: this.props.commentText
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
            })
            .catch(function (error) {
                toast(error.message, {type: toast.TYPE.ERROR});
            });

        return false;
    };

    /**
     * Check whether user should login or not.
     * @returns {boolean}
     */
    shouldLogin() {
        if (this.props.user === null) {
            this.setState({
                isShouldLogin: true
            });
            return true;
        }

        return false;
    };

    render() {
        const translations = this.props.settings.i18;

        return (
            <div className="send-comment-body">
                <form onSubmit={this.handleSubmit}>
                    <div className="send-comment-body-outliner">

                        <SendCommentGuest isShouldLogin={this.state.isShouldLogin} user={this.props.user}/>
                        <SendCommentAuth user={this.props.user}/>

                        <textarea name="content"
                                  value={this.props.commentText}
                                  required="required"
                                  className="send-comment-body-outliner__textfield"
                                  placeholder={translations.add_comment}
                                  onClick={this.shouldLogin}
                                  onChange={this.handleContentChange}
                                  ref={this.props.commentFieldRef}
                        ></textarea>
                    </div>

                    {this.props.user ?
                        <input
                            type="hidden"
                            name="parent"
                            value={this.props.replyId}
                            onChange={this.handleReplyIdChange}/> : ''}

                    {this.props.isReply !== '' ?
                        <p className="send-comment-body-reply">{translations.reply_to} {this.props.replyName}
                            <span onClick={this.props.onReplyCancel}>{translations.cancel}</span></p>
                        : ''}

                    {this.props.user ? <input type="hidden" name="edit_id" value={this.props.editId}
                                              onChange={this.handleEditIdChange}/> : ''}

                    {this.props.user ? <input type="submit" className="btn send-comment-body__btn"
                                              value={translations.button_send}/> : ''}
                </form>

                <div className="clearfix"></div>
            </div>
        );
    }
}

export default SendCommentForm;