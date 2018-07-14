import React from 'react';
import TimeAgo from 'react-timeago'
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter'
import i18En from 'react-timeago/lib/language-strings/en';
import i18Ru from 'react-timeago/lib/language-strings/ru';
import AnyCommentComponent from './AnyCommentComponent'
import CommentFooter from './CommentFooter';

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
                console.log(response);

                self.setState({
                    likesCount: response.data.total_count,
                    hasLike: response.data.has_like,
                });
            })
            .catch(function (error) {
                // handle error
                console.log('err');
                console.log(error);
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
                            key={childrenComment.id}
                            user={this.props.user}
                            comment={childrenComment}/>
                    ))}
                </ul>
            </div> : '';

        return (
            <li key={comment.id} className="comment-single">

                <div className="comment-single-avatar">
                    <div className="comment-single-avatar__img"
                         style={{backgroundImage: 'url(' + comment.avatar_url + ')'}}>
                    </div>
                </div>

                <div className="comment-single-body">
                    <header className="comment-single-body-header">
                        <div className="comment-single-body-header__author">{comment.author_name}</div>
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
                        comment={comment}
                        user={this.props.user}
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