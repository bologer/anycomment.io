import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import footerLogo from '../img/mini-logo.svg'
import SVG from 'react-inlinesvg'

/**
 * Display plugin copyright in the very bottom of comments.
 */
class CommentCopyright extends AnyCommentComponent {
    render() {
        const settings = this.getSettings();

        if (!settings.options.isCopyright) {
            return (null);
        }

        return (
            <footer className="anycomment anycomment-copy-footer">
                <SVG
                    src={footerLogo}
                    preloader={false}
                /> <a href="https://anycomment.io" target="_blank">{settings.i18.footer_copyright}</a>
            </footer>
        );
    }
}

export default CommentCopyright;