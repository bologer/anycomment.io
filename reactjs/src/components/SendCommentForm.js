import React from 'react';
import SendCommentGuest from './SendCommentGuest.js';
import SendCommentAuth from './SendCommentAuth.js';
import AnyCommentComponent from "./AnyCommentComponent";

class SendCommentForm extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state.content = '';
        this.state.parent = '0';
        this.state.edit_id = '';
        this.state.shouldLogin = false;
    }

    handleContent = (event) => {
        this.setState({content: event.target.value});
    };

    handleParent = (event) => {
        event.preventDefault();
        return false;
    };

    handleEditId = (event) => {
        event.preventDefault();
        return false;
    };

    handleSubmit = (event) => {
        event.preventDefault();

        if (this.props.user === null) {
            return false;
        }

        const settings = this.state.settings;
        const self = this;
        this.state.axios
            .request({
                method: 'post',
                url: '/comments',
                params: {
                    post: settings.postId,
                    content: this.state.content,
                    parent: this.state.parent,
                },
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.setState({
                    content: '',
                    parent: '0',
                    edit_id: '',
                });
                self.props.contentRef.current.focus();
                self.props.onSend(response.data);
            })
            .catch(function (error) {
                // handle error
                console.log('err');
                console.log(error);
            })
            .then(function () {
                // always executed
            });

        return false;
    };

    shouldLogin = (e) => {
        e.preventDefault();


        if (this.props.user === null) {
            this.setState({
                shouldLogin: true
            });
            return true;
        }

        return false;
    };

    render() {
        const translations = this.state.settings.i18;

        return (
            <div className="send-comment-body">
                <form onSubmit={this.handleSubmit}>
                    <div className="send-comment-body-outliner">

                        <SendCommentGuest shouldLogin={this.state.shouldLogin} user={this.props.user}/>
                        <SendCommentAuth user={this.props.user}/>

                        <textarea name="content"
                                  ref={this.props.contentRef}
                                  value={this.state.content}
                                  required="required"
                                  className="send-comment-body-outliner__textfield"
                                  placeholder={translations.add_comment}
                                  data-original-placeholder={translations.add_comment}
                                  data-reply-name={translations.reply_to}
                                  onClick={(e) => this.shouldLogin(e)}
                                  onChange={this.handleContent}
                        ></textarea>
                    </div>

                    <input type="hidden" name="parent" value={this.state.parent} onChange={this.handleParent}/>
                    <input type="hidden" name="edit_id" value={this.state.edit_id} onChange={this.handleEditId}/>
                    <input type="submit" className="btn send-comment-body__btn"
                           value={translations.button_send}/>
                </form>

                <div className="clearfix"></div>
            </div>
        );
    }
}

export default SendCommentForm;