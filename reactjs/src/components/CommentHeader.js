import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter'
import i18En from 'react-timeago/lib/language-strings/en'
import i18Ru from 'react-timeago/lib/language-strings/ru'
import TimeAgo from 'react-timeago'
import Icon from './Icon'
import Tooltip from './helpers/Tooltip'
import {faEllipsisV, faGavel, faPen} from '@fortawesome/free-solid-svg-icons'

/**
 * Used to render partial header of a comment.
 */
class CommentHeader extends AnyCommentComponent {

    /**
     * Check whether status of the comment is unapproved (being moderated).
     *
     * @returns {boolean}
     */
    isModerated = () => {
        const {comment} = this.props;

        console.log(comment);
        console.log(comment.meta)
        console.log(comment.meta.status);

        return comment.meta.status === 'unapproved';
    };

    /**
     * Check whether comment was updated.
     *
     * @returns {*}
     */
    isUpdated = () => {
        const {comment} = this.props;

        return comment.meta.is_updated || false;
    };

    render() {
        const {comment, settings} = this.props;

        let languageStrings = i18En;

        if (this.getLocale() === 'ru') {
            languageStrings = i18Ru;
        }

        const formatter = buildFormatter(languageStrings);

        const authorName = settings.options.isShowProfileUrl && comment.owner.profile_url.trim() !== '' ?
            <a className="anycomment comment-single-body-header__author-name" target="_blank"
               href={comment.owner.profile_url}
               rel="noopener noreferrer">{comment.author_name}</a> :
            <span className="anycomment comment-single-body-header__author-name">{comment.author_name}</span>;

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

                    <div className="anycomment comment-single-body-header__author--actions">
                        {this.isModerated() ?
                            <Tooltip message={settings.i18.waiting_moderation}><Icon icon={faGavel}/></Tooltip> : ''}

                        {this.isUpdated() ? <Tooltip message={settings.i18.edited}><Icon icon={faPen}/></Tooltip> : ''}

                        <Icon icon={faEllipsisV}/>
                    </div>
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