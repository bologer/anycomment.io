import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";

class SendCommentAuth extends AnyCommentComponent {
    render() {
        if (!this.props.user) {
            return (null);
        }

        const url = this.props.user.data.user_avatar;
        const name = this.props.user.data.display_name;

        const style = {
            backgroundImage: 'url(' + url + ')'
        };

        return (
            <React.Fragment>
                <div className="send-comment-body-outliner__avatar"
                     style={style} title={name}></div>
            </React.Fragment>
        );
    }
}

export default SendCommentAuth;