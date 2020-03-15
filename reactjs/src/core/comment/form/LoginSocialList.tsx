import React from 'react';
import LoginSocial from './LoginSocial';
import {useSettings} from '~/hooks/setting';
import {SocialItemOption} from '~/components/AnyCommentProvider';

/**
 * Display list of available social networks to login with.
 */
export default function LoginSocialList() {
    const settings = useSettings();

    const socials: SocialItemOption[] = settings.options.socials;

    return (
        <ul>
            {Object.keys(socials).map((item, index) => (
                <LoginSocial key={index} social={socials[item]} />
            ))}
        </ul>
    );
}

LoginSocialList.displayName = 'LoginSocialList';
