import React from 'react'
import SocialIcon from './SocialIcon'
import { SocialItemOption } from './AnyCommentProvider';

export interface LoginSocialProps {
    social: SocialItemOption;
}

/**
 * Display single item of single network to login with.
 */
export default function LoginSocial({social}: LoginSocialProps) {

    if (!social.visible) {
        return null;
    }

    return (
        <li>
            <a href={social.url}
               target="_parent"
               title={social.label}
               className={"anycomment-login-with-list-" + social.slug}>
                <SocialIcon slug={social.slug} alt={social.label} />
            </a>
        </li>
    );
}
