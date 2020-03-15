import React from 'react';
import CommentAvatar from './CommentAvatar';
import CommentHeader from './CommentHeader';
import CommentFooter from './CommentFooter';
import CommentBody from './CommentBody';
import CommentAttachments from '../../components/CommentAttachments';
import SendComment from '~/core/comment/form/SendComment';
import {CommentModel} from '~/typings/models/CommentModel';
import {useSettings} from '~/hooks/setting';
import {useSelector} from 'react-redux';
import {StoreProps} from '~/store/reducers';
import {CommentReducerProps} from '~/core/comment/commentReducers';

export interface CommentItemProps {
    comment: CommentModel;
}

/**
 * Comment is rendering single comment entry.
 */
export default function CommentItem({comment}: CommentItemProps) {
    const settings = useSettings();
    const {form} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    const commentId = 'comment-' + comment.id;

    const childComments = comment.children && (
        <div className='anycomment comment-single-replies'>
            <ul className='anycomment anycomment-list anycomment-list-child'>
                {comment.children.map(childrenComment => {
                    return <CommentItem key={childrenComment.id} comment={childrenComment} />;
                })}
            </ul>
        </div>
    );

    const commentForm = settings.post.comments_open && form && form[comment.id] && (
        <div className='comment-single-form-wrapper'>
            <SendComment action={form[comment.id].type} comment={form[comment.id].comment} />
        </div>
    );

    return (
        <li
            key={comment.id}
            className='anycomment comment-single'
            id={commentId}
            itemType='http://schema.org/Comment'
            itemScope
        >
            <CommentAvatar comment={comment} />
            <div className='comment-single-body'>
                <CommentHeader comment={comment} />
                <CommentBody comment={comment} />
                <CommentAttachments attachments={comment.attachments} />
                <CommentFooter comment={comment} />
            </div>
            {commentForm}
            {childComments}
        </li>
    );
}

CommentItem.displayName = 'CommentItem';
