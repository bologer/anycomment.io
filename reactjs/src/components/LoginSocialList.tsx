import React from 'react'
import LoginSocial from './LoginSocial'
import {useOptions, useSettings} from "~/hooks/setting";
import {SocialItemOption} from './AnyCommentProvider';

/**
 * Display list of available social networks to login with.
 */
export default function LoginSocialList() {

    const settings = useSettings();
    const options = useOptions();

    /**
     * Check whether at least one social is enabled.
     *
     * @returns {boolean}
     */
    function hasAtLeastOneSocial() {
        const socials: SocialItemOption[] = settings.options.socials;

        if (!socials) {
            return false;
        }

        let visibleCount = 0;
        for (let key in socials) {
            if (socials.hasOwnProperty(key) && socials[key].visible) {
                visibleCount++;
            }
        }

        return visibleCount > 0;
    }

    if (!hasAtLeastOneSocial()) {
        return null;
    }

    const socials: SocialItemOption[] = settings.options.socials;

    return (
        <ul>
            <li className="anycomment-form__guest-socials-text">{settings.i18.login_with}</li>
            {Object.keys(socials).map((item, index) => (
                <LoginSocial key={index} social={socials[item]} />
            ))}
            {options.isFormTypeAll && (
                <li className="anycomment-form__guest-socials-text anycomment-form__guest-socials-text-as-guest">{settings.i18.or_as_guest}</li>
            )}
        </ul>
    );
}
