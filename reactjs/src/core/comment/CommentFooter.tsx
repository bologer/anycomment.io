import React, {CSSProperties, useEffect, useState} from 'react';
import Icon from '../../components/Icon';
import {useSettings} from '~/hooks/setting';
import {faReply, faHeart, faEdit} from '@fortawesome/free-solid-svg-icons';
import {CommentModel} from '~/typings/models/CommentModel';
import {isGuest} from '~/helpers/user';
import {useDispatch, useSelector} from 'react-redux';
import {activeCommentForm, fetchLike} from '~/core/comment/CommentActions';
import {CommentReducerProps} from '~/core/comment/commentReducers';
import {StoreProps} from '~/store/reducers';
import {manageReducer} from '~/helpers/action';

export interface CommentFooterProps {
    comment: CommentModel;
}

/**
 * CommentFooter renders single comment actions such as
 * edit, reply, like, etc.
 */
export default function CommentFooter({comment}: CommentFooterProps) {
    const dispatch = useDispatch();
    const settings = useSettings();

    const [likesCount, setLikesCount] = useState<number>(comment.meta.likes);
    const [hasLike, setHasLike] = useState<boolean>(comment.meta.has_like);
    const [processLikeStates, setProcessLikeStates] = useState<boolean>(false);
    const {like} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    useEffect(() => {
        const singleLike = (like && like[comment.id]) || undefined;
        if (singleLike) {
            manageReducer({
                reducer: singleLike,
                onSuccess: response => {
                    setLikesCount(response.likes);
                    setHasLike(response.has_like);
                },
            });
        }
    }, [like]);

    /**
     * Handle like action.
     */
    function handleLike(e) {
        e.preventDefault();

        if (!processLikeStates) {
            setProcessLikeStates(true);
        }

        dispatch(fetchLike(comment.id, comment.post));
    }

    // eslint-disable-next-line require-jsdoc
    function onReply(e) {
        e.preventDefault();
        dispatch(activeCommentForm('reply', comment));
    }

    // eslint-disable-next-line require-jsdoc
    function onUpdate(e) {
        e.preventDefault();
        dispatch(activeCommentForm('update', comment));
    }

    const replyIcon = <Icon icon={faReply} />;
    const likeStyle: CSSProperties | undefined = hasLike ? {color: '#EC4568'} : undefined;
    const likeIcon = <Icon icon={faHeart} style={likeStyle} />;
    const updateIcon = <Icon icon={faEdit} />;

    let likeActionClass = 'anycomment comment-single-body__actions-like';

    if (processLikeStates) {
        likeActionClass += ' comment-single-body__actions-like-' + (hasLike ? 'active' : 'static');
    }

    return (
        <footer className='anycomment comment-single-body__actions'>
            <ul className='anycomment'>
                {settings.post.comments_open && (
                    <li className='anycomment'>
                        <a className='anycomment' href='#' onClick={onReply}>
                            {replyIcon}
                            {settings.i18.reply}
                        </a>
                    </li>
                )}

                {settings.options.commentRating === 'likes' && (
                    <li className='anycomment'>
                        <a href='#' className={likeActionClass} onClick={handleLike}>
                            {likeIcon}
                            <span itemProp='upvoteCount'>{likesCount}</span>
                        </a>
                    </li>
                )}

                {!isGuest() && comment.permissions.can_edit_comment && settings.post.comments_open && (
                    <li className='anycomment'>
                        <a className='anycomment' href='#' onClick={onUpdate}>
                            {updateIcon}
                            {settings.i18.edit}
                        </a>
                    </li>
                )}
            </ul>
        </footer>
    );
}

CommentFooter.displayName = 'CommentFooter';
