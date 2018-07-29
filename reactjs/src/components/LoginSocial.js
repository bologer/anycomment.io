import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"

/**
 * Display single item of single network to login with.
 */
class LoginSocial extends AnyCommentComponent {
    render() {
        const social = this.props.social;

        const src = require('../img/icons/auth/social-' + social.slug + '.svg');

        if (!social.visible) {
            return (null);
        }

        return (
            <li>
                <a href={social.url}
                   target="_parent"
                   title={social.label}
                   className={"anycomment-login-with-list-" + social.slug}>
                    <img
                        src={src}
                        alt={social.label}/></a>
            </li>
        );
    }
}

export default LoginSocial;