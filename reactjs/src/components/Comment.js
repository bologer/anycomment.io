import React from 'react';
import TimeAgo from 'react-timeago'
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter'
import i18En from 'react-timeago/lib/language-strings/en';
import i18Ru from 'react-timeago/lib/language-strings/ru';
import AnyCommentComponent from './AnyCommentComponent'

/**
 * Comment is rendering single comment entry.
 */
class Comment extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state.likesCount = props.comment.meta.likes_count;
        this.state.hasLike = props.comment.meta.has_like;

        this.onReply = this.onReply.bind(this);
        this.onLike = this.onLike.bind(this);
        this.onEdit = this.onEdit.bind(this);
    }

    onReply(e) {
        e.preventDefault();
        console.log('on reply');
    }

    onLike(e) {
        console.log('on like');

        const settings = this.state.settings;
        const self = this;
        this.state.axios
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

    onEdit(e) {
        console.log('on edit');
    }

    render() {
        const settings = this.state.settings;
        const comment = this.props.comment;

        const languageStrings = i18En;
        const locale = settings.locale.substring(0, 2);

        if (locale === 'ru') {
            const languageStrings = i18Ru;
        }

        const formatter = buildFormatter(languageStrings);

        return (
            <li key={comment.id} className="comment-single">

                <div className="comment-single-avatar" data-author-id="2">
                    <div className="comment-single-avatar__img"
                         style={{backgroundImage: 'url(' + comment.avatar_url + ')'}}>
                    </div>
                </div>

                <div className="comment-single-body">
                    <header className="comment-single-body-header" data-author-id="2">
                        <div className="comment-single-body-header__author">{comment.author_name}</div>
                        <TimeAgo className="comment-single-body-header__date"
                                 date={comment.date} formatter={formatter}/>
                    </header>

                    <div className="comment-single-body__text">
                        <p>{comment.content}</p>
                    </div>

                    <footer className="comment-single-body__actions">
                        <ul>
                            <li><a href="" onClick={(e) => this.onReply(e)}>{settings.i18.reply}</a>
                            </li>
                            <li>
                            <span
                                className={"comment-single-body__actions-like " + (this.state.hasLike ? 'active' : '') + ""}
                                onClick={(e) => this.onLike(e)}>{this.state.likesCount}</span>
                            </li>

                            {comment.permissions.can_edit_comment ?
                                <li><a href="" onClick={(e) => this.onEdit(e)}>{settings.i18.edit}</a>
                                </li> : ''}
                        </ul>
                    </footer>
                </div>
            </li>
        )
    }
}

export default Comment;