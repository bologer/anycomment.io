import React, {useEffect, useState} from 'react';
import Comment from './Comment'
import SendComment from './SendComment'
import {toast} from 'react-toastify';
import CommentListSummary from "./CommentListSummary";
import Subscribe from './Subscribe'
import {useOptions, useSettings} from '~/hooks/setting';
import {hasSpecificCommentAnchor} from "~/helpers/url";
import {useDispatch, useSelector} from "react-redux";
import {fetchComments, fetchLoadMore} from "~/core/comment/CommentActions";
import {StoreProps} from "~/store/reducers";
import {CommentItem} from "~/typings/models/CommentItem";
import {moveToCommentAndHighlight} from "~/helpers/comment";
import {showError, showSuccess} from "~/helpers/snackbars";
import {ListResponse} from "~/typings/ListResponse";
import {CommentReducerProps} from "~/core/comment/commentReducers";
import ReducerResolver from "~/components/ReducerResolver";

/**
 * CommentList displays list of comments.
 */
export default function CommentContainer() {

    const dispatch = useDispatch();
    const settings = useSettings();
    const options = useOptions();

    const {list: comments} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    const [offset, setOffset] = useState<number>(0);
    // Hold boolean whether current user just added comment or not
    // primarily used to track toast of added new comments
    const [isJustAdded, setIsJustAdded] = useState<boolean>(false);
    const [action, setAction] = useState<'reply' | 'update'>(undefined);
    const [comment, setComment] = useState<CommentItem | undefined>(undefined);

    useEffect(() => {
        dispatch(fetchComments({postId: settings.postId, order: options.sort_order, perPage: options.limit}));
        checkForAnchor();
        //
        // if (options.notifyOnNewComment) {
        //     const intervalInSeconds = (options.intervalCommentsCheck * 1000) || 5000;
        //
        //     setInterval(function() {
        //         followNewComments();
        //     }, intervalInSeconds);
        // }
    }, []);

    /**
     * Handles load more comments.
     * @returns {*}
     */
    function handleLoadMore() {
        const newOffset = offset + options.limit;

        dispatch(fetchLoadMore({postId: settings.postId, offset: newOffset, perPage: options.limit}));
        setOffset(newOffset);
    }

    /**
     * Checks for anchors in the link.
     * If there are some, it could be user who came from the
     * email and trying to read his reply.
     */
    function checkForAnchor() {
        const hash = window.location.hash;

        if (hasSpecificCommentAnchor()) {
            const interval = setInterval(function() {

                const element = document.getElementById(hash.replace('#', ''));

                if (!element) {
                    handleLoadMore();
                } else {
                    moveToCommentAndHighlight(hash);
                    clearInterval(interval);
                }
            }, 1000);
        }
    }

    /**
     * Trigger whether comment was just added by user, to investigate whether comment was
     * added by current user or someone else.
     */
    function handleJustAdded() {
        setIsJustAdded(true);
    }

    /**
     * Handle action unsetting.
     * Unset any previously set action.
     */
    function handleUnsetAction() {
        setAction('');
        setComment(undefined);
    }

    function onFetched(response: ListResponse<CommentItem>) {

        if (response.items.length === 0) {
            return (
                <ul id="anycomment-load-container" className="anycomment anycomment-list">
                    <li className="anycomment comment-single comment-no-comments">{settings.i18.no_comments}</li>
                </ul>
            );
        }

        if (response.items && response.items.length > 0) {
            return (
                <>
                    {response.items.map(comment => (
                        <Comment
                            handleUnsetAction={handleUnsetAction}
                            handleJustAdded={handleJustAdded}
                            key={comment.id}
                            comment={comment}
                        />
                    ))}
                    {
                        response.meta.current_page !== response.meta.page_count && (
                            <div className="anycomment comment-single-load-more">
                                <span onClick={handleLoadMore}
                                      className="anycomment anycomment-btn">{settings.i18.load_more}</span>
                            </div>
                        )
                    }
                </>
            )
        }
    }

    return (
        <ul id="anycomment-load-container" className="anycomment anycomment-list">
            <SendComment
                action={action}
                comment={comment}
                handleUnsetAction={handleUnsetAction}
                handleJustAdded={handleJustAdded}
            />
            <CommentListSummary />
            <Subscribe />

            <ReducerResolver
                reducer={comments}
                fetched={onFetched}
            />
        </ul>
    );
}
