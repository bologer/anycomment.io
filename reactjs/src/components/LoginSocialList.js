import React from 'react'
import LoginSocial from './LoginSocial'
import AnyCommentComponent from "./AnyCommentComponent"

/**
 * Display list of available social networks to login with.
 */
class LoginSocialList extends AnyCommentComponent {
    render() {
        const settings = this.state.settings;
        const socials = settings.options.socials;

        if (!socials) {
            return (null);
        }

        return (
            <ul>
                <li className="send-comment-body-outliner__auth-header">{settings.i18.quick_login}</li>
                {Object.keys(socials).map(item => (
                    <LoginSocial key={item} social={socials[item]} socialKey={item}/>
                ))}
            </ul>
        );
    }
}

export default LoginSocialList;