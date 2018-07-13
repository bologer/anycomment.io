import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"

/**
 * Display single item of single network to login with.
 */
class LoginSocial extends AnyCommentComponent {
    render() {
        const social = this.props.social;
        const socialKey = this.props.socialKey;

        return (
            <li>
                <a href={social.url}
                   target="_parent"
                   title={social.label}
                   className={"anycomment-login-with-list-" + {socialKey}}>
                    <img
                        src={"/assets/img/icons/auth/social-" + {socialKey} + ".svg"}
                        alt={social.label}/></a>
            </li>
        );
    }
}

export default LoginSocial;