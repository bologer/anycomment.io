import {fetch, FetchActions} from "~/helpers/action";
import {getSettings} from "~/hooks/setting";
import {CommentModel} from "~/typings/models/CommentModel";

export const COMMENT_DELETE = '@comment/delete';
export const COMMENT_DELETE_SUCCESS = '@comment/delete/success';
export const COMMENT_DELETE_FAILURE = '@comment/delete/failure';
export const COMMENT_DELETE_INVALIDATE = '@comment/delete/invalidate';

export const COMMENT_UPDATE = '@comment/update';
export const COMMENT_UPDATE_SUCCESS = '@comment/update/success';
export const COMMENT_UPDATE_FAILURE = '@comment/update/failure';
export const COMMENT_UPDATE_INVALIDATE = '@comment/update/invalidate';

export const COMMENT_FETCH = '@comment/fetch';
export const COMMENT_FETCH_SUCCESS = '@comment/fetch/success';
export const COMMENT_FETCH_FAILURE = '@comment/fetch/failure';

export const COMMENT_FETCH_SALIENT_SUCCESS = '@comment/fetch-salient/success';
export const COMMENT_FETCH_SALIENT_FAILURE = '@comment/fetch-salient/failure';

export const COMMENT_FETCH_FILTER = '@comment/fetch-filter';

export const COMMENT_LOAD_MORE = '@comment/load-more';
export const COMMENT_LOAD_MORE_SUCCESS = '@comment/load-more/success';
export const COMMENT_LOAD_MORE_FAILURE = '@comment/load-more/failure';

export const COMMENT_CREATE = '@comment/create';
export const COMMENT_CREATE_SUCCESS = '@comment/create/success';
export const COMMENT_CREATE_FAILURE = '@comment/create/failure';
export const COMMENT_CREATE_INVALIDATE = '@comment/create/invalidate';

export const COMMENT_FORM = '@comment/form';
export const COMMENT_FORM_INVALIDATE = '@comment/form/invalidate';

export type CommentFormType = 'reply' | 'update';

/**
 * Sets currently active form type + comment data.
 *
 * @param {string} type
 * @param {object} comment
 */
export function activeCommentForm(type: CommentFormType, comment: CommentModel) {
    return {
        type: COMMENT_FORM,
        payload: {
            type,
            comment,
        },
    }
}

/**
 * Invalidates comment form.
 */
export function invalidateCommentForm() {
    return {
        type: COMMENT_FORM_INVALIDATE,
    }
}

interface FetchCommentProps {
    postId: number;
    offset?: number;
    perPage?: number;
    order?: string;
}

interface FetchCommentsBase {
    actions: FetchActions;
}

/**
 * Common helper to fetch list of comments.
 *
 * @param postId
 * @param offset
 * @param perPage
 * @param order
 * @param pre
 * @param success
 * @param failure
 * @param always
 */
export function fetchCommentsBase({
    postId,
    offset = 0,
    perPage,
    order,
    actions: {
        pre,
        success,
        failure,
        always,
    },
}: FetchCommentProps & FetchCommentsBase) {
    const timestamp = new Date().getTime();

    const settings = getSettings();

    if (!perPage) {
        perPage = settings.options.limit;
    }

    if (!order) {
        order = settings.options.sort_order;
    }

    return (dispatch) => {

        dispatch({type: COMMENT_FETCH_FILTER, payload: {perPage, offset, order}});

        return dispatch(fetch({
            method: 'get',
            url: 'comments',
            params: {
                post: postId,
                parent: 0,
                per_page: perPage,
                order,
                offset,
                rnd: timestamp,
            },
            actions: {pre, success, failure, always},
        }))
    }
}

/**
 * This salient version of comment fetching.
 *
 * It fetches & updates data on background without loader.
 *
 * @param postId
 * @param offset
 * @param perPage
 * @param order
 */
export function fetchCommentsSalient({postId, offset = 0, perPage, order}: FetchCommentProps) {
    return fetchCommentsBase({
        postId,
        offset,
        perPage,
        order,
        actions: {
            success: COMMENT_FETCH_SALIENT_SUCCESS,
            failure: COMMENT_FETCH_SALIENT_FAILURE,
        },
    });
}

/**
 * Fetches comments with loading state.
 *
 * @param postId
 * @param offset
 * @param perPage
 * @param order
 */
export function fetchComments({postId, offset = 0, perPage, order}: FetchCommentProps) {
    return fetchCommentsBase({
        postId,
        offset,
        perPage,
        order,
        actions: {
            pre: COMMENT_FETCH,
            success: COMMENT_FETCH_SUCCESS,
            failure: COMMENT_FETCH_FAILURE,
        },
    });
}

/**
 * Create new comment.
 *
 * @param postId
 * @param params
 */
export function fetchCreateComment(postId: number, params) {
    return fetch({
        method: 'post',
        params: {post: postId},
        data: params,
        url: 'comments',
        actions: {
            pre: COMMENT_CREATE,
            success: COMMENT_CREATE_SUCCESS,
            failure: COMMENT_CREATE_FAILURE,
            always: COMMENT_CREATE_INVALIDATE,
        },
    })
}

/**
 * Handles logic of loading further comments.
 *
 * @param postId
 * @param offset
 * @param perPage
 * @param order
 */
export function fetchLoadMore({postId, offset = 0, perPage, order}: FetchCommentProps) {
    return fetchCommentsBase({
        postId,
        offset,
        perPage,
        order,
        actions: {
            pre: COMMENT_LOAD_MORE,
            success: COMMENT_LOAD_MORE_SUCCESS,
            failure: COMMENT_LOAD_MORE_FAILURE,
        },
    });
}

/**
 * Delete a comment.
 *
 * @param {number} id
 */
export function fetchDeleteComment(id: number) {
    return fetch({
        method: 'post',
        url: 'comment/delete' + id,
        actions: {
            pre: COMMENT_DELETE,
            success: COMMENT_DELETE_SUCCESS,
            failure: COMMENT_DELETE_FAILURE,
            always: COMMENT_DELETE_INVALIDATE,
        },
    })
}

/**
 * Update a comment.
 * @param data
 * todo: proper implementation
 */
export function fetchUpdateComment(data) {
    return fetch({
        method: 'post',
        data,
        url: 'comments/update',
        actions: {
            pre: COMMENT_UPDATE,
            success: COMMENT_UPDATE_SUCCESS,
            failure: COMMENT_UPDATE_FAILURE,
            always: COMMENT_UPDATE_INVALIDATE,
        },
    })
}
