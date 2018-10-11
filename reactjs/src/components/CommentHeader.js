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

        if (this.getLocale() === 'ru') {
            languageStrings = i18Ru;
        }

        const formatter = buildFormatter(languageStrings);

        const authorName = settings.options.isShowProfileUrl && comment.owner.profile_url.trim() !== '' ?
            <a className="anycomment" target="_blank" href={comment.owner.profile_url}
               rel="noopener noreferrer">{comment.author_name}</a> :
            comment.author_name;

        let additionalTags = [];

        if (comment.owner.is_post_author) {
            additionalTags.push(<span
                className="anycomment comment-single-body-header__author-owner">{settings.i18.author}</span>);
        }

        if (comment.parent_author_name.trim() !== '') {
            let commentHash = '#comment-' + comment.parent;
            let replyText = <a onClick={() => this.moveToCommentAndHighlight(commentHash, 1500)}
                               href={commentHash}>{settings.i18.reply_to + ' ' + comment.parent_author_name}</a>;
            additionalTags.push(<span
                className="anycomment comment-single-body-header__author-reply">{replyText}</span>);
        }

        return (
            <header className="anycomment comment-single-body-header">
                <div className="anycomment comment-single-body-header__author">
                    {authorName}
                    {additionalTags}
                </div>
                <a href={'#comment-' + comment.id} className="anycomment">
                    <TimeAgo className="anycomment comment-single-body-header__date"
                             date={comment.date_gmt} formatter={formatter}/>
                </a>
            </header>
        );
    }
}

export default CommentHeader;