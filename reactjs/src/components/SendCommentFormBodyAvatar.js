import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";

/**
 * Displays authorized user avatar or guest one.
 */
class SendCommentFormBodyAvatar extends AnyCommentComponent {
    render() {
        if (!this.props.user) {
            return <div className="anycomment-send-comment-body-outliner__logo"></div>;
        }

        const url = this.props.user.data.user_avatar;
        const name = this.props.user.data.display_name;

        const style = {
            backgroundImage: 'url(' + url + ')'
        };

        return <div className="anycomment-send-comment-body-outliner__avatar"
                    style={style} title={name}></div>;
    }
}

export default SendCommentFormBodyAvatar;