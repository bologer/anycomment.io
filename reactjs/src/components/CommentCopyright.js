import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import footerLogo from '../img/mini-logo.svg'

/**
 * Display plugin copyright in the very bottom of comments.
 */
class CommentCopyright extends AnyCommentComponent {
    render() {
        const settings = this.state.settings;

        if (!settings.options.isCopyright) {
            return null;
        }

        return (
            <footer className="main-footer">
                <img src={footerLogo}
                     alt="AnyComment"/> <a href="https://anycomment.io"
                                           target="_blank">{settings.i18.footer_copyright}</a>
            </footer>
        );
    }
}

export default CommentCopyright;