import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";

class SendCommentAuth extends AnyCommentComponent {
    render() {
        if (!this.props.user) {
            return null;
        }
        const url = this.props.user.avatar_url;
        const style = {
            backgroundImage: 'url(' + url + ')'
        };

        return (
            <React.Fragment>
                <div className="send-comment-body-outliner__avatar"
                     style={style}></div>
            </React.Fragment>
        );
    }
}

export default SendCommentAuth;