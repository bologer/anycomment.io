import React from 'react';
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter';
import i18En from 'react-timeago/lib/language-strings/en';
import i18Ru from 'react-timeago/lib/language-strings/ru';
import TimeAgo from 'react-timeago';
import Icon from '../../components/Icon';
import Tooltip from '../../components/Tooltip';
import {faEllipsisV, faTrashAlt, faGavel, faPen} from '@fortawesome/free-solid-svg-icons';
import Dropdown from 'react-simple-dropdown';
import DropdownTrigger from 'react-simple-dropdown/lib/components/dropdown-trigger';
import DropdownContent from 'react-simple-dropdown/lib/components/dropdown-content';
import {CommentModel} from '~/typings/models/CommentModel';
import {useSettings} from '~/hooks/setting';
import {isGuest} from '~/helpers/user';
import {moveToCommentAndHighlight} from '~/helpers/comment';
import {fetchComments, fetchDeleteComment} from '~/core/comment/CommentActions';
import {useDispatch} from 'react-redux';

export interface CommentHeaderProps {
    comment: CommentModel;
}

/**
 * Renders single comment header.
 */
export default function CommentHeader({comment}: CommentHeaderProps) {
    const settings = useSettings();
    const dispatch = useDispatch();

    /**
     * Check whether status of the comment is unapproved (being moderated).
     *
     * @returns {boolean}
     */
    function isModerated() {
        return comment.meta.status === 'unapproved';
    }

    /**
     * Check whether comment was updated.
     *
     * @returns {*}
     */
    function isUpdated() {
        return comment.meta.is_updated || false;
    }

    /**
     * Normalizes name to render proper name without HTML entities.
     *
     * @param name
     * @return {*}
     */
    function normalizeAuthorName(name) {
        name = name.replace('&amp;', '&');
        return name;
    }

    /**
     * Handles comment deletion.
     */
    function handleDelete() {
        dispatch(fetchDeleteComment(comment.id));
        dispatch(fetchComments({postId: settings.post}));
    }

    let languageStrings = i18En;

    if (settings.locale === 'ru') {
        languageStrings = i18Ru;
    }

    const formatter = buildFormatter(languageStrings);

    const commentAuthorName = normalizeAuthorName(comment.author_name);

    const authorName =
        settings.options.isShowProfileUrl && comment.owner.profile_url.trim() !== '' ? (
            <a
                className='anycomment comment-single-body-header__author-name'
                target='_blank'
                href={comment.owner.profile_url}
                rel='noopener noreferrer'
            >
                {commentAuthorName}
            </a>
        ) : (
            <span className='anycomment comment-single-body-header__author-name'>{commentAuthorName}</span>
        );

    let additionalTags: React.ReactElement[] = [];

    if (comment.owner.is_post_author) {
        additionalTags.push(
            <span className='anycomment comment-single-body-header__author-owner' key='owner'>
                {settings.i18.author}
            </span>
        );
    }

    if (comment.parent_author_name.trim() !== '') {
        let commentHash = '#comment-' + comment.parent;
        let replyText = (
            <a onClick={() => moveToCommentAndHighlight(commentHash, 1500)} href={commentHash}>
                {settings.i18.reply_to + ' ' + comment.parent_author_name}
            </a>
        );

        additionalTags.push(
            <span className='anycomment comment-single-body-header__author-reply' key={`reply${replyText}`}>
                {replyText}
            </span>
        );
    }

    const datetime =
        settings.options.dateFormat === 'relative' ? (
            <TimeAgo
                className='anycomment comment-single-body-header__date'
                date={comment.date_gmt}
                formatter={formatter}
            />
        ) : (
            <time className='anycomment comment-single-body-header__date' dateTime={comment.date}>
                {comment.date_native}
            </time>
        );

    return (
        <header className='anycomment comment-single-body-header'>
            <div className='anycomment comment-single-body-header__author'>
                {authorName}
                {additionalTags}

                <div className='anycomment comment-single-body-header__author--actions'>
                    {isModerated() && (
                        <Tooltip message={settings.i18.waiting_moderation}>
                            <Icon icon={faGavel} />
                        </Tooltip>
                    )}

                    {settings.options.isShowUpdatedInfo && isUpdated() && (
                        <Tooltip message={settings.i18.edited}>
                            <Icon icon={faPen} />
                        </Tooltip>
                    )}

                    {!isGuest() && comment.permissions.can_edit_comment && (
                        <Dropdown>
                            <DropdownTrigger>
                                <Icon icon={faEllipsisV} />
                            </DropdownTrigger>
                            <DropdownContent>
                                <ul>
                                    <li>
                                        <a href='#' onClick={handleDelete}>
                                            <Icon icon={faTrashAlt} />
                                            &nbsp;{settings.i18.delete}
                                        </a>
                                    </li>
                                </ul>
                            </DropdownContent>
                        </Dropdown>
                    )}
                </div>
            </div>
            <a href={'#comment-' + comment.id} className='anycomment'>
                {datetime}
            </a>
        </header>
    );
}

CommentHeader.displayName = 'CommentHeader';
