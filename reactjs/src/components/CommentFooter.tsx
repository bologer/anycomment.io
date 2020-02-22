import React, {CSSProperties, useState} from 'react';
import Icon from './Icon';
import {useSettings} from "~/hooks/setting";
import {faReply, faHeart, faEdit} from '@fortawesome/free-solid-svg-icons';
import {CommentModel} from "~/typings/models/CommentModel";
import {isGuest} from '~/helpers/user';
import {useDispatch} from "react-redux";
import {activeCommentForm} from "~/core/comment/CommentActions";

export interface CommentFooterProps {
    comment: CommentModel;
    onLike: (e: React.SyntheticEvent<any>) => void;
    likesCount: number;
    hasLike: boolean;
}

/**
 * CommentFooter renders single comment actions such as
 * edit, reply, like, etc.
 */
export default function CommentFooter({
    comment,
    onLike,
    likesCount,
    hasLike,
}: CommentFooterProps) {

    const dispatch = useDispatch();
    const settings = useSettings();
    const [processLikeStates, setProcessLikeStates] = useState<boolean>(false);

    /**
     * Handle like action.
     */
    function handleLike(e) {
        e.preventDefault();

        if (!processLikeStates) {
            setProcessLikeStates(true);
        }

        onLike(e);
    }


    function onReply(e) {
        e.preventDefault();
        dispatch(activeCommentForm('reply', comment));
    }

    function onUpdate(e) {
        e.preventDefault();
        dispatch(activeCommentForm('update', comment));
    }

    const replyIcon = <Icon icon={faReply} />;
    const likeStyle: CSSProperties | undefined = hasLike ? {color: '#EC4568'} : undefined;
    const likeIcon = <Icon icon={faHeart} style={likeStyle} />;
    const updateIcon = <Icon icon={faEdit} />;

    let likeActionClass = "anycomment comment-single-body__actions-like";

    if (processLikeStates) {
        likeActionClass += " comment-single-body__actions-like-" + (hasLike ? 'active' : 'static');
    }

    return (
        <footer className="anycomment comment-single-body__actions">
            <ul className="anycomment">

                {settings.post.comments_open && (
                    <li className="anycomment">
                        <a className="anycomment" href="#"
                           onClick={onReply}>{replyIcon}{settings.i18.reply}</a>
                    </li>
                )}

                {settings.options.commentRating === 'likes' && (
                    <li className="anycomment">
                        <a href="#"
                           className={likeActionClass}
                           onClick={handleLike}>{likeIcon}
                            <span itemProp="upvoteCount">{likesCount}</span>
                        </a>
                    </li>
                )}

                {!isGuest() && comment.permissions.can_edit_comment && settings.post.comments_open && (
                    <li className="anycomment">
                        <a className="anycomment" href="#" onClick={onUpdate}>{updateIcon}{settings.i18.edit}</a>
                    </li>
                )}
            </ul>
        </footer>
    );
}

CommentFooter.displayName = 'CommentFooter';
