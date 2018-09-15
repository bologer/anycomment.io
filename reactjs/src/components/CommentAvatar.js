import React from "react"
import AnyCommentComponent from "./AnyCommentComponent"
import SVG from 'react-inlinesvg'

/**
 * CommentAvatar used to display avatar partial of a single comment.
 */
class CommentAvatar extends AnyCommentComponent {

    render() {
        const {comment} = this.props;

        const miniIconSrc = comment.owner.is_social_login ? require('../img/icons/avatars/social-' + comment.owner.social_type + '.svg') : '';

        const svgIcon = miniIconSrc ?
            <SVG src={miniIconSrc} className="anycomment comment-single-avatar__img-auth-type" preload={false}/> : '';

        return (
            <div className="anycomment comment-single-avatar">
                <div className="anycomment comment-single-avatar__img"
                     style={{backgroundImage: 'url(' + comment.avatar_url + ')'}}>
                    {comment.owner.is_social_login ? svgIcon : ''}
                </div>
            </div>
        );

    }
}

export default CommentAvatar;