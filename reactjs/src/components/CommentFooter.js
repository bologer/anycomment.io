import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";

/**
 * CommentFooter renders single comment actions such as
 * edit, reply, like, etc.
 */
class CommentFooter extends AnyCommentComponent {

    render() {
        const settings = this.props.settings;
        const comment = this.props.comment;
        const isGuest = this.isGuest();

        return (
            <footer className="anycomment comment-single-body__actions">
                <ul className="anycomment">
                    <li className="anycomment"><a className="anycomment" href=""
                                                  onClick={(e) => this.props.onReply(e)}>{settings.i18.reply}</a>
                    </li>
                    <li className="anycomment">
                            <span
                                className={"anycomment comment-single-body__actions-like " + (this.props.hasLike ? 'active' : '') + ""}
                                onClick={(e) => this.props.onLike(e)}>{this.props.likesCount}</span>
                    </li>

                    {!isGuest && comment.permissions.can_edit_comment ?
                        <li className="anycomment"><a className="anycomment" href=""
                                                      onClick={(e) => this.props.onEdit(e)}>{settings.i18.edit}</a>
                        </li> : ''}
                    {!isGuest && comment.permissions.can_edit_comment ?
                        <li className="anycomment"><a className="anycomment" href=""
                                                      onClick={(e) => this.props.onDelete(e, comment)}>{settings.i18.delete}</a>
                        </li> : ''}
                </ul>
            </footer>
        );
    }
}


export default CommentFooter;