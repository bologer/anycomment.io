import React from 'react';
import TimeAgo from 'react-timeago'
import frenchStrings from 'react-timeago/lib/language-strings/ru'
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter'
import AnyCommentComponent from './AnyCommentComponent'

/**
 * Comment is rendering single comment entry.
 */
class Comment extends AnyCommentComponent {
    render() {
        const comment = this.props.comment;
        const formatter = buildFormatter(frenchStrings);

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
                        <TimeAgo className="comment-single-body-header__date timeago-date-time"
                                 date={comment.date} formatter={formatter}/>
                    </header>

                    <div className="comment-single-body__text">
                        <p>{comment.content}</p>
                    </div>

                </div>

            </li>
        )
    }
}

export default Comment;