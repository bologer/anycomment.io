import React from 'react';
import TimeAgo from 'react-timeago'
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter'
import i18En from 'react-timeago/lib/language-strings/en';
import i18Ru from 'react-timeago/lib/language-strings/ru';
import AnyCommentComponent from './AnyCommentComponent'
import CommentFooter from './CommentFooter';
import CommentAvatar from './CommentAvatar';
import toast from 'react-toastify';

/**
 * Comment is rendering single comment entry.
 */
class Comment extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            likesCount: props.comment.meta.likes_count,
            hasLike: props.comment.meta.has_like
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
        const settings = this.props.settings;
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
                    toast(error.message, toast.TYPE.ERROR);
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
        const settings = this.props.settings;
        const comment = this.props.comment;

        let languageStrings = i18En;
        const locale = settings.locale.substring(0, 2);

        if (locale === 'ru') {
            languageStrings = i18Ru;
        }

        const formatter = buildFormatter(languageStrings);

        const childComments = comment.children ?
            <div className="comment-single-replies">
                <ul className="anycomment-list anycomment-list-child">
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
            <li key={comment.id} className="comment-single">

                <CommentAvatar comment={comment}/>

                <div className="comment-single-body">
                    <header className="comment-single-body-header">
                        <div className="comment-single-body-header__author">
                            {comment.author_name}
                            {comment.owner.is_post_author ?
                                <span className="comment-single-body-header__author-owner">{settings.i18.author}</span>
                                : ''}
                        </div>
                        <TimeAgo className="comment-single-body-header__date"
                                 date={comment.date} formatter={formatter}/>
                    </header>

                    <div className="comment-single-body__text">
                        <p>{comment.content}</p>
                    </div>

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