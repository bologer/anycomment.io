import React from 'react';
// @ts-ignore
import footerLogo from '~/img/mini-logo.svg'
import {useSettings} from '~/hooks/setting';

/**
 * Display plugin copyright in the very bottom of comments.
 */
export default function CommentCopyright() {

    const settings = useSettings();

    if (!settings || settings && !settings.options.isCopyright) {
        return null;
    }

    return (
        <footer className="anycomment anycomment-copy-footer">
            {footerLogo} <a href="https://anycomment.io"
                         rel="nofollow"
                         target="_blank">{settings.i18.footer_copyright}</a>
        </footer>
    );
}
