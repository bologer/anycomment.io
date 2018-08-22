import React from 'react';
import AnyCommentComponent from './AnyCommentComponent'
import CommentAvatar from './CommentAvatar';
import CommentHeader from './CommentHeader';
import CommentFooter from './CommentFooter';
import CommentBody from './CommentBody';
import CommentAttachments from './CommentAttachments';
import {toast} from 'react-toastify';

/**
 * Comment is rendering single comment entry.
 */
class Comment extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            likesCount: props.comment.meta.likes_count,
            hasLike: props.comment.meta.has_like,
        };

        this.onReply = this.onReply.bind(this);
        this.onLike = this.onLike.bind(this);
        this.onEdit = this.onEdit.bind(this);
        this.onDelete = this.onDelete.bind(this);
    }

    /**
     * On comment reply action.
     * @param e
     * @param comment
     */
    onReply(e, comment) {
        e.preventDefault();
        this.props.changeReplyId(comment);
    }

    onLike(e) {
        e.preventDefault();

        const settings = this.getSettings();
        const self = this;
        this.props.axios
            .request({
                method: 'post',
                url: '/likes',
                params: {
                    comment: this.props.comment.id,
                    post: this.props.comment.post,
                },
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.setState({
                    likesCount: response.data.total_count,
                    hasLike: response.data.has_like,
                });
            })
            .catch(function (error) {
                if ('message' in error) {
                    toast.error(error.message);
                }
            });
    }

    /**
     * On comment edit action.
     * @param e
     * @param comment
     */
    onEdit(e, comment) {
        e.preventDefault();
        this.props.changeEditId(comment);
    }

    /**
     * On comment delete.
     *
     * @param e
     * @param comment
     */
    onDelete(e, comment) {
        e.preventDefault();
        this.props.handleDelete(comment);
    }

    render() {
        const comment = this.props.comment;
        const commentId = 'comment-' + comment.id;

        const childComments = comment.children ?
            <div className="anycomment comment-single-replies">
                <ul className="anycomment anycomment-list anycomment-list-child">
                    {comment.children.map(childrenComment => (
                        <Comment
                            changeReplyId={this.props.changeReplyId}
                            changeEditId={this.props.changeEditId}
                            handleDelete={this.props.handleDelete}
                            key={childrenComment.id}
                            comment={childrenComment}/>
                    ))}
                </ul>
            </div> : '';


        return (
            <li key={comment.id} className="anycomment comment-single" id={commentId}>

                <CommentAvatar comment={comment}/>

                <div className="comment-single-body">
                    <CommentHeader comment={comment}/>

                    <CommentBody comment={comment}/>

                    <CommentAttachments comment={comment}/>

                    <CommentFooter
                        onEdit={this.onEdit}
                        onLike={this.onLike}
                        onReply={this.onReply}
                        onDelete={this.onDelete}
                        comment={comment}
                        likesCount={this.state.likesCount}
                        hasLike={this.state.hasLike}
                    />
                </div>
                {childComments}
            </li>
        )
    }


}

export default Comment;