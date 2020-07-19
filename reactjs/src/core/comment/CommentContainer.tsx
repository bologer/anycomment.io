import React, {useEffect, useState} from 'react';
import CommentItem from './CommentItem';
import SendComment from '~/core/comment/form/SendComment';
import CommentSortHeader from './CommentSortHeader';
import PageSubscription from '../extensions/post-subscription/PageSubscription';
import {useConfig, useOptions, useSettings} from '~/hooks/setting';
import {hasSpecificCommentAnchor} from '~/helpers/url';
import {useDispatch, useSelector} from 'react-redux';
import {fetchComments, fetchCommentsSalient, fetchLoadMore} from '~/core/comment/CommentActions';
import {StoreProps} from '~/store/reducers';
import {CommentModel} from '~/typings/models/CommentModel';
import {moveToCommentAndHighlight} from '~/helpers/comment';
import {ListResponse} from '~/typings/ListResponse';
import {CommentReducerProps} from '~/core/comment/commentReducers';
import ReducerResolver from '~/components/ReducerResolver';

/**
 * CommentList displays list of comments.
 */
export default function CommentContainer() {
    const dispatch = useDispatch();
    const settings = useSettings();
    const options = useOptions();
    const config = useConfig();

    const {list: comments} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);
    const [offset, setOffset] = useState<number>(0);
    const [isAnchorInProgress, setAnchorInProgress] = useState<boolean>(true);

    useEffect(() => {
        dispatch(fetchComments({postId: config.postId, offset: 0, perPage: options.limit}));
    }, []);

    useEffect(() => {
        let autoUpdateInterval;
        if (options.notifyOnNewComment && !isAnchorInProgress) {
            const intervalInSeconds = options.intervalCommentsCheck * 1000 || 5000;

            const perPage = comments?.payload?.items?.length || options.limit;

            if (perPage < 50) {
                autoUpdateInterval = setInterval(() => {
                    dispatch(
                        fetchCommentsSalient({
                            postId: config.postId,
                            offset: 0,
                            perPage: perPage,
                        })
                    );
                }, intervalInSeconds);
            }
        }

        return () => autoUpdateInterval && clearInterval(autoUpdateInterval);
    }, [isAnchorInProgress, comments]);

    /**
     * Checks for anchors in the link.
     * If there are some, it could be user who came from the
     * email and trying to read his reply.
     */
    useEffect(() => {
        let interval;
        const hash = window.location.hash;

        if (hasSpecificCommentAnchor()) {
            setAnchorInProgress(true);
            interval = setInterval(() => {
                const element = document.getElementById(hash.replace('#', ''));

                if (!element) {
                    handleLoadMore();
                } else {
                    moveToCommentAndHighlight(hash);
                    clearInterval(interval);
                    setAnchorInProgress(false);
                }
            }, 1000);
        } else {
            setAnchorInProgress(false);
        }

        return () => clearInterval(interval);
    }, []);

    /**
     * Handles load more comments.
     * @returns {*}
     */
    function handleLoadMore() {
        const newOffset = offset + options.limit;
        dispatch(
            fetchLoadMore({
                postId: config.postId,
                offset: newOffset,
                perPage: options.limit,
            })
        );
        setOffset(newOffset);
    }

    /**
     * @param response
     */
    function onFetched(response: ListResponse<CommentModel>) {
        if (response.items.length === 0) {
            return (
                <ul id='anycomment-load-container' className='anycomment anycomment-list'>
                    <li className='anycomment comment-single comment-no-comments'>{settings.i18.no_comments}</li>
                </ul>
            );
        }

        if (response.items && response.items.length > 0) {
            return (
                <>
                    {response.items.map(comment => {
                        return <CommentItem key={comment.id} comment={comment} />;
                    })}
                    {response.meta.current_page !== response.meta.page_count && (
                        <div className='anycomment comment-single-load-more'>
                            <span onClick={handleLoadMore} className='anycomment anycomment-btn'>
                                {settings.i18.load_more}
                            </span>
                        </div>
                    )}
                </>
            );
        }

        return null;
    }

    return (
        <ul id='anycomment-load-container' className='anycomment anycomment-list'>
            <SendComment />
            <CommentSortHeader />
            <PageSubscription />
            <ReducerResolver reducer={comments} fetched={onFetched} />
        </ul>
    );
}

CommentContainer.displayName = 'CommentContainer';
