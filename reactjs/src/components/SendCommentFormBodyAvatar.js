import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import SVG from 'react-inlinesvg'
import commentLogo from '../img/comment-logo.svg'

/**
 * Displays authorized user avatar or guest one.
 */
class SendCommentFormBodyAvatar extends AnyCommentComponent {
    render() {
        let className = "anycomment-send-comment-body-outliner__logo";
        let title = '';
        let svg = <SVG src={commentLogo} loader={false}/>;
        let style = '';

        if (!this.isGuest()) {
            const user = this.getCurrentUser();

            className = "anycomment-send-comment-body-outliner__avatar";
            title = user.data.display_name;
            svg = '';
            style = {backgroundImage: 'url(' + user.data.user_avatar + ')'}
        }

        return <div className={className} style={{style}} title={title}>{svg}</div>;
    }
}

export default SendCommentFormBodyAvatar;