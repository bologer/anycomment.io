import React, {Component} from 'react';

class SendCommentGuest extends Component {
    
    render() {
        if (this.props.user) {
            return null;
        }
        return (
            <React.Fragment>
                <div className="send-comment-body-outliner__logo"></div>

                <div className="send-comment-body-outliner__auth" id="auth-required"
                     style={{display: 'none'}}>
                    <ul>
                        <li className="send-comment-body-outliner__auth-header">Quick Login</li>
                        {/*<?php do_action( 'anycomment_login_with', get_permalink( AnyComment()->getCurrentPost()->ID ) ) ?>*/}
                    </ul>
                </div>
            </React.Fragment>
        );
    }
}

export default SendCommentGuest;