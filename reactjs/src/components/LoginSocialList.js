import React from 'react'
import LoginSocial from './LoginSocial'
import AnyCommentComponent from "./AnyCommentComponent"

/**
 * Display list of available social networks to login with.
 */
class LoginSocialList extends AnyCommentComponent {

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

    componentDidMount() {
        if (!this.hasAtLeastOneSocial()) {
            this.props.handleGuestFields();
        }
    }

    render() {
        const settings = this.props.settings;

        if (!this.hasAtLeastOneSocial()) {
            return (null);
        }

        const socials = settings.options.socials;

        return (
            <ul>
                <li className="anycomment-form__guest-socials-text">{settings.i18.login_with}</li>
                {Object.keys(socials).map((item, index) => (
                    <LoginSocial key={index} social={socials[item]}/>
                ))}
                <li className="anycomment-form__guest-socials-text anycomment-form__guest-socials-text-as-guest anycomment-link"
                    onClick={this.props.handleGuestFields}
                    dangerouslySetInnerHTML={{__html: settings.i18.or_as_guest}}></li>
            </ul>
        );
    }
}

export default LoginSocialList;