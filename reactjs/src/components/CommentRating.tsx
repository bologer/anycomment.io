import React, {useEffect, useState} from 'react';
import Icon from './Icon';
import {faCaretUp, faCaretDown} from '@fortawesome/free-solid-svg-icons';
import {CommentModel} from '~/typings/models/CommentModel';
import {fetchLike} from '~/core/comment/CommentActions';
import {useOptions} from '~/hooks/setting';
import {useDispatch, useSelector} from 'react-redux';
import {StoreProps} from '~/store/reducers';
import {CommentReducerProps} from '~/core/comment/commentReducers';
import {manageReducer} from '~/helpers/action';

export interface CommentRatingProps {
    comment: CommentModel;
}

/**
 * Renders single comment rating.
 */
export default function CommentRating({comment}: CommentRatingProps) {
    const options = useOptions();
    const dispatch = useDispatch();
    const [rating, setRating] = useState<number>(comment.meta.rating);
    const [hasLike, setHasLike] = useState<boolean>(comment.meta.has_like);
    const [hasDislike, setHasDislike] = useState<boolean>(comment.meta.has_dislike);
    const {like} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    useEffect(() => {
        const singleLike = (like && like[comment.id]) || undefined;
        if (singleLike) {
            manageReducer({
                reducer: singleLike,
                onSuccess: response => {
                    setRating(response.rating);
                    setHasDislike(response.has_dislike);
                    setHasLike(response.has_like);
                },
            });
        }
    }, [like]);

    /**
     * Handle upvote rating.
     *
     * @param e
     */
    function handleRateUp(e) {
        e.preventDefault();

        dispatch(fetchLike(comment.id, comment.post, 1));
    }

    /**
     * Handle downvote rating.
     *
     * @param e
     */
    function handleRateDown(e) {
        e.preventDefault();

        dispatch(fetchLike(comment.id, comment.post, 0));
    }

    if (options.commentRating !== 'likes_dislikes') {
        return null;
    }

    return (
        <div className='anycomment anycomment-comment-rating'>
            <div className='anycomment anycomment-comment-rating__counter' itemProp='upvoteCount'>
                {rating}
            </div>
            <div className='anycomment anycomment-comment-rating__actions'>
                <div className='anycomment anycomment-comment-rating__actions--up' onClick={handleRateUp}>
                    <Icon icon={faCaretUp} style={hasLike && {color: '#53AF4A'}} />
                </div>
                <div className='anycomment anycomment-comment-rating__actions--down' onClick={handleRateDown}>
                    <Icon icon={faCaretDown} style={hasDislike && {color: '#DD4B39'}} />
                </div>
            </div>
        </div>
    );
}

CommentRating.displayName = 'CommentRating';
