import React from 'react'
import LoginSocial from './LoginSocial'
import AnyCommentComponent from "./AnyCommentComponent"

/**
 * Display list of available social networks to login with.
 */
class LoginSocialList extends AnyCommentComponent {
    render() {
        const settings = this.props.settings;

        if (!this.hasAtLeastOneSocial()) {
            return (null);
        }

        const socials = settings.options.socials;

        return [
            <div className="anycomment anycomment-form-guest__header">{settings.i18.quick_login}:</div>,
            <ul>
                {Object.keys(socials).map((item, index) => (
                    <LoginSocial key={index} social={socials[item]}/>
                ))}
            </ul>
        ];
    }

    /**
     * Check whether at least one social is enabled.
     *
     * @returns {boolean}
     */
    hasAtLeastOneSocial() {
        const settings = this.getSettings();
        const socials = settings.options.socials;

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
}

export default LoginSocialList;