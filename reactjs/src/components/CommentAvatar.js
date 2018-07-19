import React from "react";
import AnyCommentComponent from "./AnyCommentComponent";

/**
 * CommentAvatar used to display avatar partial of a single comment.
 */
class CommentAvatar extends AnyCommentComponent {

    render() {
        const {comment} = this.props;

        const miniIconUrl = comment.owner.is_social_login ? comment.owner.social_url : '';
        const miniIconSrc = comment.owner.is_social_login ? require('../img/icons/avatars/social-' + comment.owner.social_type + '.svg') : '';

        const miniIcon = !comment.owner.social_url ?
            <span className="comment-single-avatar__img-auth-type"
                  style={{backgroundImage: 'url(' + miniIconSrc + ')'}}></span>
            : <a target="_blank" href={miniIconUrl} rel="noopener noreferrer"
                 className="comment-single-avatar__img-auth-type"
                 style={{backgroundImage: 'url(' + miniIconSrc + ')'}}></a>;

        return (
            <div className="comment-single-avatar">
                <div className="comment-single-avatar__img"
                     style={{backgroundImage: 'url(' + comment.avatar_url + ')'}}>
                    {comment.owner.is_social_login ? miniIcon : ''}
                </div>
            </div>
        );

    }
};

export default CommentAvatar;