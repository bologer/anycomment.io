import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {faReply, faHeart, faEdit} from '@fortawesome/free-solid-svg-icons'

/**
 * CommentFooter renders single comment actions such as
 * edit, reply, like, etc.
 */
class CommentFooter extends AnyCommentComponent {

    render() {
        const settings = this.props.settings;
        const comment = this.props.comment;
        const isGuest = this.isGuest();

        const replyIcon = <FontAwesomeIcon icon={faReply}/>;
        const likeIcon = <FontAwesomeIcon icon={faHeart} style={this.props.hasLike ? {color: '#EC4568'} : ''}/>;
        const editIcon = <FontAwesomeIcon icon={faEdit}/>;

        return (
            <footer className="anycomment comment-single-body__actions">
                <ul className="anycomment">
                    <li className="anycomment"><a className="anycomment" href="javascript:void(0)"
                                                  onClick={(e) => this.props.onReply(e)}>{replyIcon}{settings.i18.reply}</a>
                    </li>
                    <li className="anycomment">
                        <a href="javascript:void(0)"
                           className="anycomment comment-single-body__actions-like"
                           onClick={(e) => this.props.onLike(e)}>{likeIcon}{this.props.likesCount}</a>
                    </li>

                    {!isGuest && comment.permissions.can_edit_comment ?
                        <li className="anycomment"><a className="anycomment" href="javascript:void(0)"
                                                      onClick={(e) => this.props.onEdit(e)}>{editIcon}{settings.i18.edit}</a>
                        </li> : ''}
                    {!isGuest && comment.permissions.can_edit_comment ?
                        <li className="anycomment"><a className="anycomment" href="javascript:void(0)"
                                                      onClick={(e) => this.props.onDelete(e, comment)}>{settings.i18.delete}</a>
                        </li> : ''}
                </ul>
            </footer>
        );
    }
}


export default CommentFooter;