import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";

/**
 * CommentFooter renders single comment actions such as
 * edit, reply, like, etc.
 */
class CommentFooter extends AnyCommentComponent {

    render() {
        if (!this.props.user) {
            return (null);
        }

        const settings = this.state.settings;
        const comment = this.props.comment;
        const hasLike = this.props.hasLike;
        const likesCount = this.props.likesCount;

        return (
            <footer className="comment-single-body__actions">
                <ul>
                    <li><a href="" onClick={(e) => this.props.onReply(e)}>{settings.i18.reply}</a>
                    </li>
                    <li>
                            <span
                                className={"comment-single-body__actions-like " + (hasLike ? 'active' : '') + ""}
                                onClick={(e) => this.props.onLike(e)}>{likesCount}</span>
                    </li>

                    {comment.permissions.can_edit_comment ?
                        <li><a href="" onClick={(e) => this.props.onEdit(e)}>{settings.i18.edit}</a>
                        </li> : ''}
                </ul>
            </footer>
        );
    }
}


export default CommentFooter;