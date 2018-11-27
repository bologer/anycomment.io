import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"
import SocialIcon from './SocialIcon'

/**
 * Display single item of single network to login with.
 */
class LoginSocial extends AnyCommentComponent {
    render() {
        const social = this.props.social;

        if (!social.visible) {
            return (null);
        }

        return (
            <li>
                <a href={social.url}
                   target="_parent"
                   title={social.label}
                   className={"anycomment-login-with-list-" + social.slug}>
                    <SocialIcon slug={social.slug} alt={social.label}/>
                </a>
            </li>
        );
    }

    getSlug() {

    }
}

export default LoginSocial;