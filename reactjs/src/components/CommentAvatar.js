import React, {Fragment} from "react"
import AnyCommentComponent from "./AnyCommentComponent"
import SocialIcon from "./SocialIcon";
import CommentRating from './CommentRating';


/**
 * CommentAvatar used to display avatar partial of a single comment.
 */
class CommentAvatar extends AnyCommentComponent {
    render() {
        const {comment} = this.props;

        const svgIcon = comment.owner.is_social_login ?
            <SocialIcon slug={comment.owner.social_type} classes="comment-single-avatar__img-auth-type"/> : '';

        return (
            <Fragment>
                <div className="anycomment comment-single-avatar">
                    <div className="anycomment comment-single-avatar__img"
                         style={{backgroundImage: 'url(' + comment.avatar_url + ')'}}>
                        {comment.owner.is_social_login ? svgIcon : ''}
                    </div>
                </div>
                <CommentRating comment={comment}/>
            </Fragment>
        );

    }
}

export default CommentAvatar;