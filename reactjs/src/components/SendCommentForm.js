import React, {Component} from 'react';
import axios from 'axios';
import SendCommentGuest from './SendCommentGuest.js';
import SendCommentAuth from './SendCommentAuth.js';

class SendCommentForm extends Component {

    hande = (event) => {
        this.setState({content: event.target.value});
    };

    handleParent = (event) => {
        this.setState({parent: event.target.value});
    };

    handleEditId = (event) => {
        this.setState({edit_id: event.target.value});
    };

    handlePost = (event) => {
        this.setState({post: event.target.value});
    };

    handleNonce = (event) => {
        this.setState({nonce: event.target.value});
    };

    handleSubmit = (event) => {
        console.log(this.state);
        event.preventDefault();

        const data = new FormData(event.target);

        axios
            .post('http://127.0.0.1:9090/wp-json/anycomment/v1/comments')
            .then(function (response) {
                // handle success
                console.log('err');
                console.log(response);
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

    shouldLogin = (event) => {
        console.log('should login');
    };

    render() {
        return (
            <div className="send-comment-body">
                <form onSubmit={this.handleSubmit}>
                    <div className="send-comment-body-outliner">

                        <SendCommentGuest isLogged={this.props.isLogged}/>
                        <SendCommentAuth isLogged={this.props.isLogged}/>

                        <textarea name="content" required="required"
                                  className="send-comment-body-outliner__textfield"
                                  placeholder="Add comment..."
                                  data-original-placeholder="Add comment..."
                                  data-reply-name="Reply to {name}"
                                  // onClick={this.shouldLogin}
                                  onChange={this.handleComment}
                        ></textarea>
                    </div>

                    <input type="hidden" name="parent" value="0" onChange={this.handleParent}/>
                    <input type="hidden" name="edit_id" onChange={this.handleEditId}/>
                    <input type="hidden" name="post" value="123" onChange={this.handlePost}/>
                    <input type="hidden" name="nonce" value="123" onChange={this.handleNonce}/>
                    <input type="submit" className="btn send-comment-body__btn" value="Send"/>
                </form>

                <div className="clearfix"></div>
            </div>
        );
    }
}

export default SendCommentForm;