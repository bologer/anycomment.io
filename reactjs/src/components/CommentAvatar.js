import React from "react"
import AnyCommentComponent from "./AnyCommentComponent"
import SocialIcon from "./SocialIcon";

/**
 * CommentAvatar used to display avatar partial of a single comment.
 */
class CommentAvatar extends AnyCommentComponent {

    render() {
        const {comment} = this.props;

        const svgIcon = comment.owner.is_social_login ?
            <SocialIcon slug={comment.owner.social_type} classes="comment-single-avatar__img-auth-type"/> : '';

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