import React from 'react';
import LoginSocialList from './LoginSocialList';
import AnyCommentComponent from "./AnyCommentComponent";

class SendCommentGuest extends AnyCommentComponent {
    render() {
        if (this.props.user) {
            return null;
        }

        return (
            <React.Fragment>
                <div className="send-comment-body-outliner__logo"></div>

                <div className="send-comment-body-outliner__auth" id="auth-required"
                     style={{display: this.props.shouldLogin ? 'block' : 'none'}}>
                    <LoginSocialList/>
                </div>
            </React.Fragment>
        );
    }
}

export default SendCommentGuest;