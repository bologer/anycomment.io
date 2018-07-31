import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter'
import i18En from 'react-timeago/lib/language-strings/en'
import i18Ru from 'react-timeago/lib/language-strings/ru'
import TimeAgo from 'react-timeago'

/**
 * Used to render partial header of a comment.
 */
class CommentHeader extends AnyCommentComponent {
    render() {
        const {comment, settings} = this.props;

        let languageStrings = i18En;
        const locale = settings.locale.substring(0, 2);

        if (locale === 'ru') {
            languageStrings = i18Ru;
        }

        const formatter = buildFormatter(languageStrings);

        const authorName = settings.options.isShowProfileUrl && comment.owner.is_social_login && comment.owner.social_url.trim() !== '' ?
            <a target="_blank" href={comment.owner.social_url} rel="noopener noreferrer">{comment.author_name}</a> :
            comment.author_name;

        return (
            <header className="comment-single-body-header">
                <div className="comment-single-body-header__author">
                    {authorName}
                    {comment.owner.is_post_author ?
                        <span className="comment-single-body-header__author-owner">{settings.i18.author}</span>
                        : ''}
                </div>
                <TimeAgo className="comment-single-body-header__date"
                         date={comment.date} formatter={formatter}/>
            </header>
        );
    }
}

export default CommentHeader;