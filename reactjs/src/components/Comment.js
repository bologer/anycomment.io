import React from 'react';
import AnyCommentComponent from './AnyCommentComponent'
import CommentAvatar from './CommentAvatar';
import CommentHeader from './CommentHeader';
import CommentFooter from './CommentFooter';
import CommentBody from './CommentBody';
import CommentAttachments from "./CommentAttachments";
import SendComment from './SendComment'
import {fetchDeleteComment} from '../core/comment/CommentActions';


/**
 * Comment is rendering single comment entry.
 */
class Comment extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            likesCount: props.comment.meta.likes,
            hasLike: props.comment.meta.has_like,
            action: '',
        };
    }

    handleUnsetAction = () => {
        this.setState({
            action: ''
        });
    };


    /**
     * On comment reply action.
     * @param e
     */
    onReply = (e) => {
        e.preventDefault();

        this.setState({
            action: 'reply'
        });
    };

    /**
     * On comment edit action.
     * @param e
     */
    onEdit = (e) => {
        e.preventDefault();

        this.setState({
            action: 'update'
        });
    };

    onLike = (e) => {
        e.preventDefault();

        const settings = this.getSettings();
        const self = this;

        let headers = {};

        if (settings.nonce) {
            headers = {'X-WP-Nonce': settings.nonce};
        }

        this.props.axios
            .request({
                method: 'post',
                url: '/likes',
                params: {
                    comment: this.props.comment.id,
                    post: this.props.comment.post,
                    type: 1
                },
                headers: headers
            })
            .then(function (response) {
                self.setState({
                    likesCount: response.data.likes,
                    hasLike: response.data.has_like,
                });
            })
            .catch(function (error) {
                self.showError(error);
            });
    };

    /**
     * On comment delete.
     *
     * @param e
     * @param comment
     */
    onDelete = (e, comment) => {
        e.preventDefault();
        dispatch(fetchDeleteComment(comment.id));
    };

    render() {
        const comment = this.props.comment;
        const commentId = 'comment-' + comment.id;
        const settings = this.getSettings();

        const {action, likesCount, hasLike} = this.state;

        const childComments = comment.children ?
            <div className="anycomment comment-single-replies">
                <ul className="anycomment anycomment-list anycomment-list-child">
                    {comment.children.map(childrenComment => (
                        <Comment
                            loadComments={this.props.loadComments}
                            handleUnsetAction={this.handleUnsetAction}
                            handleJustAdded={this.props.handleJustAdded}
                            key={childrenComment.id}
                            comment={childrenComment}/>
                    ))}
                </ul>
            </div> : '';

        const replyForm = action && settings.post.comments_open ?
            <div className="comment-single-form-wrapper">
                <SendComment
                    action={action}
                    comment={comment}
                    handleUnsetAction={this.handleUnsetAction}
                    handleJustAdded={this.props.handleJustAdded}
                />
            </div> : '';

        return (
            <li
                key={comment.id}
                className="anycomment comment-single"
                id={commentId}
                itemType="http://schema.org/Comment"
                itemScope="itemscope">

                <CommentAvatar comment={comment}/>

                <div className="comment-single-body">
                    <CommentHeader comment={comment} onDelete={this.onDelete}/>

                    <CommentBody comment={comment}/>

                    <CommentAttachments attachments={comment.attachments}/>

                    <CommentFooter
                        onEdit={this.onEdit}
                        onLike={this.onLike}
                        onReply={this.onReply}
                        comment={comment}
                        likesCount={likesCount}
                        hasLike={hasLike}
                    />
                </div>
                {replyForm}
                {childComments}
            </li>
        )
    }
}

export default Comment;