import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";

/**
 * Displays authorized user avatar or guest one.
 */
class SendCommentFormBodyAvatar extends AnyCommentComponent {
    render() {
        let className = "anycomment-form-body-outliner__logo";
        let title = '';
        let svg = <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26">
            <path fill="none" fill-rule="evenodd" stroke="#B6C1C6"
                  d="M1 13C1 6.373 6.373 1 13 1s12 5.373 12 12-5.373 12-12 12H1V13z"/>
        </svg>;
        let style = {};

        if (!this.isGuest()) {
            const user = this.getCurrentUser();

            className = "anycomment-form-body-outliner__avatar";
            title = user.data.display_name;
            svg = '';
            style = {backgroundImage: 'url(' + user.data.user_avatar + ')'}
        }

        return <div className={className} style={style} title={title}>{svg}</div>;
    }
}

export default SendCommentFormBodyAvatar;