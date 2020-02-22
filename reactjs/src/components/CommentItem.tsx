import React, {useState} from 'react';
import CommentAvatar from './CommentAvatar';
import CommentHeader from './CommentHeader';
import CommentFooter from './CommentFooter';
import CommentBody from './CommentBody';
import CommentAttachments from "./CommentAttachments";
import SendComment from './SendComment'
import {fetchDeleteComment, fetchComments} from '~/core/comment/CommentActions';
import {CommentModel} from "~/typings/models/CommentModel";
import {useSettings} from "~/hooks/setting";
import {useDispatch, useSelector} from "react-redux";
import {StoreProps} from "~/store/reducers";
import {CommentReducerProps} from "~/core/comment/commentReducers";

export interface CommentItemProps {
    comment: CommentModel;
    handleJustAdded: () => void;
    handleUnsetAction: () => void;
}

/**
 * Comment is rendering single comment entry.
 */
export default function CommentItem({
    comment,
    handleJustAdded,
    handleUnsetAction,
}: CommentItemProps) {

    const dispatch = useDispatch();
    const settings = useSettings();
    const [likesCount, setLikesCount] = useState<number>(comment.meta.likes);
    const [hasLike, setHasLike] = useState<boolean>(comment.meta.has_like);

    const {form} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    // todo: move to redux
    function onLike(e) {
        e.preventDefault();

        let headers = {};

        if (settings.nonce) {
            headers = {'X-WP-Nonce': settings.nonce};
        }

        // this.props.axios
        //     .request({
        //         method: 'post',
        //         url: '/likes',
        //         params: {
        //             comment: comment.id,
        //             post: comment.post,
        //             type: 1,
        //         },
        //         headers: headers,
        //     })
        //     .then(function(response) {
        //         self.setState({
        //             likesCount: response.data.likes,
        //             hasLike: response.data.has_like,
        //         });
        //     })
        //     .catch(function(error) {
        //         self.showError(error);
        //     });
    }

    /**
     * On comment delete.
     *
     * @param e
     * @param comment
     */
    function onDelete(e, comment) {
        e.preventDefault();
        dispatch(fetchDeleteComment(comment.id));
        dispatch(fetchComments({postId: settings.post}))
    }

    const commentId = 'comment-' + comment.id;

    const childComments = comment.children ?
        <div className="anycomment comment-single-replies">
            <ul className="anycomment anycomment-list anycomment-list-child">
                {comment.children.map(childrenComment => (
                    <CommentItem
                        handleUnsetAction={handleUnsetAction}
                        handleJustAdded={handleJustAdded}
                        key={childrenComment.id}
                        comment={childrenComment} />
                ))}
            </ul>
        </div> : '';

    const commentForm = settings.post.comments_open && form && form[comment.id] && (
        <div className="comment-single-form-wrapper">
            <SendComment
                action={form[comment.id].type}
                comment={form[comment.id].comment}
                handleUnsetAction={handleUnsetAction}
                handleJustAdded={handleJustAdded}
            />
        </div>
    );

    return (
        <li
            key={comment.id}
            className="anycomment comment-single"
            id={commentId}
            itemType="http://schema.org/Comment"
            itemScope>

            <CommentAvatar comment={comment} />

            <div className="comment-single-body">
                <CommentHeader comment={comment} onDelete={onDelete} />

                <CommentBody comment={comment} />

                <CommentAttachments attachments={comment.attachments} />

                <CommentFooter
                    onLike={onLike}
                    comment={comment}
                    likesCount={likesCount}
                    hasLike={hasLike}
                />
            </div>
            {commentForm}
            {childComments}
        </li>
    )
}

CommentItem.displayName = 'CommentItem';