import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"
import socials from "../img/icons/auth/*.svg";

/**
 * Display single item of single network to login with.
 */
class LoginSocial extends AnyCommentComponent {
    render() {
        const social = this.props.social;


        console.log(socials);
        console.log(src);

        const src = socials['social-' + social.slug];

        return (null);



        if (!social.visible) {
            return (null);
        }

        return (
            <li key={this.key}>
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