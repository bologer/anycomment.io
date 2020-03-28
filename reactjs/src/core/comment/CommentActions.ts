import {fetch, FetchActions} from '~/helpers/action';
import {getSettings} from '~/hooks/setting';
import {CommentModel} from '~/typings/models/CommentModel';
import {batch} from 'react-redux';

export const COMMENT_DELETE = '@comment/delete';
export const COMMENT_DELETE_SUCCESS = '@comment/delete/success';
export const COMMENT_DELETE_FAILURE = '@comment/delete/failure';
export const COMMENT_DELETE_INVALIDATE = '@comment/delete/invalidate';

export const COMMENT_UPDATE = '@comment/update';
export const COMMENT_UPDATE_SUCCESS = '@comment/update/success';
export const COMMENT_UPDATE_FAILURE = '@comment/update/failure';
export const COMMENT_UPDATE_INVALIDATE = '@comment/update/invalidate';

export const COMMENT_LIKE = '@comment/like';
export const COMMENT_LIKE_SUCCESS = '@comment/like/success';
export const COMMENT_LIKE_FAILURE = '@comment/like/failure';

export const COMMENT_ATTACHMENT_DELETE = '@comment/attachment/delete';
export const COMMENT_ATTACHMENT_DELETE_SUCCESS = '@comment/attachment/delete/success';
export const COMMENT_ATTACHMENT_DELETE_FAILURE = '@comment/attachment/delete/failure';
export const COMMENT_ATTACHMENT_DELETE_INVALIDATE = '@comment/attachment/delete/invalidate';

export const COMMENT_ATTACHMENT_UPLOAD = '@comment/attachment/upload';
export const COMMENT_ATTACHMENT_UPLOAD_SUCCESS = '@comment/attachment/upload/success';
export const COMMENT_ATTACHMENT_UPLOAD_FAILURE = '@comment/attachment/upload/failure';
export const COMMENT_ATTACHMENT_UPLOAD_INVALIDATE = '@comment/attachment/upload/failure';

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
    };
}

/**
 * Invalidates comment form.
 */
export function invalidateCommentForm() {
    return {
        type: COMMENT_FORM_INVALIDATE,
    };
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
    actions: {pre, success, failure, always},
}: FetchCommentProps & FetchCommentsBase) {
    const timestamp = new Date().getTime();

    const settings = getSettings();

    if (!perPage) {
        perPage = settings.options.limit;
    }

    if (!order) {
        order = settings.options.sort_order;
    }

    return dispatch => {
        batch(() => {
            dispatch({type: COMMENT_FETCH_FILTER, payload: {perPage, offset, order}});

            return dispatch(
                fetch({
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
                })
            );
        });
    };
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
        },
    });
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
        url: 'comments/delete/' + id,
        actions: {
            pre: COMMENT_DELETE,
            success: COMMENT_DELETE_SUCCESS,
            failure: COMMENT_DELETE_FAILURE,
            always: COMMENT_DELETE_INVALIDATE,
        },
    });
}

/**
 * Update a comment.
 * @param commentId
 * @param data
 */
export function fetchUpdateComment(commentId: number, data: {}) {
    return fetch({
        method: 'post',
        data,
        url: `comments/${commentId}`,
        actions: {
            pre: COMMENT_UPDATE,
            success: COMMENT_UPDATE_SUCCESS,
            failure: COMMENT_UPDATE_FAILURE,
        },
    });
}

/**
 * Set like on a comment.
 * @param commentId
 * @param postId
 */
export function fetchLike(commentId: number, postId: number, type: number = 1) {
    return fetch({
        method: 'post',
        params: {
            comment: commentId,
            post: postId,
            type,
        },
        url: `likes`,
        actions: {
            pre: {type: COMMENT_LIKE, commentId},
            success: response => {
                return {type: COMMENT_LIKE_SUCCESS, payload: response, commentId};
            },
            failure: {type: COMMENT_LIKE_FAILURE, commentId},
        },
    })
}

/**
 * Set like on a comment.
 * @param {number} fileId
 * @param {number} index Index of attachment to be removed on success.
 */
export function deleteAttachment(fileId: number, index: number) {
    return fetch({
        method: 'post',
        params: {id: fileId},
        url: 'documents/delete',
        actions: {
            pre: COMMENT_ATTACHMENT_DELETE,
            success: response => {
                return {
                    type: COMMENT_ATTACHMENT_DELETE_SUCCESS,
                    payload: {
                        response,
                        index,
                    },
                };
            },
            failure: COMMENT_ATTACHMENT_DELETE_FAILURE,
            always: COMMENT_ATTACHMENT_DELETE_INVALIDATE,
        },
    });
}

/**
 * Upload attachment.
 * @param attachments
 * @param entropy
 */
export function uploadAttachment(attachments, entropy) {
    return fetch({
        method: 'post',
        data: attachments,
        url: 'documents',
        actions: {
            pre: {type: COMMENT_ATTACHMENT_UPLOAD, payload: {entropy}},
            success: (response) => {
                return {
                    type: COMMENT_ATTACHMENT_UPLOAD_SUCCESS,
                    payload: {response, entropy}
                };
            },
            failure: {type: COMMENT_ATTACHMENT_UPLOAD_FAILURE, payload: {entropy}},
            always: {type: COMMENT_ATTACHMENT_UPLOAD_INVALIDATE, payload: {entropy}},
        },
    });
}

// eslint-disable-next-line require-jsdoc
export function invalidateUpdateComment() {
    return {type: COMMENT_UPDATE_INVALIDATE};
}

// eslint-disable-next-line require-jsdoc
export function invalidateDeleteComment() {
    return {type: COMMENT_DELETE_INVALIDATE};
}

// eslint-disable-next-line require-jsdoc
export function invalidateCreateComment() {
    return {type: COMMENT_CREATE_INVALIDATE};
}

// eslint-disable-next-line require-jsdoc
export function invalidateAttachmentUpload(entropy: string) {
    return {type: COMMENT_ATTACHMENT_UPLOAD_INVALIDATE, payload: {entropy}};
}
