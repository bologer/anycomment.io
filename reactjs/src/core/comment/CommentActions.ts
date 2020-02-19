import {fetchGeneric} from "~/helpers/action";

export const COMMENT_DELETE = '@comment/delete';
export const COMMENT_DELETE_SUCCESS = '@comment/delete/success';
export const COMMENT_DELETE_FAILURE = '@comment/delete/failure';

export const COMMENT_UPDATE = '@comment/update';
export const COMMENT_UPDATE_SUCCESS = '@comment/update/success';
export const COMMENT_UPDATE_FAILURE = '@comment/update/failure';

export const COMMENT_FETCH = '@comment/fetch';
export const COMMENT_FETCH_SUCCESS = '@comment/fetch/success';
export const COMMENT_FETCH_FAILURE = '@comment/fetch/failure';

export const COMMENT_FETCH_FILTER = '@comment/fetch-filter';

export const COMMENT_LOAD_MORE = '@comment/load-more';
export const COMMENT_LOAD_MORE_SUCCESS = '@comment/load-more/success';
export const COMMENT_LOAD_MORE_FAILURE = '@comment/load-more/failure';

export const COMMENT_CREATE = '@comment/create';
export const COMMENT_CREATE_SUCCESS = '@comment/create/success';
export const COMMENT_CREATE_FAILURE = '@comment/create/failure';

export interface FetchCommentProps {
    postId: number;
    offset?: number;
    perPage?: number;
    order?: string;
    orderBy?: string;
}

export function fetchComments({postId, offset = 0, perPage = 10, order = 'asc', orderBy}: FetchCommentProps) {

    const timestamp = new Date().getTime();

    return (dispatch) => {

        dispatch({type: COMMENT_FETCH_FILTER, payload: {perPage, offset, order, orderBy}});

        return dispatch(fetchGeneric({
            method: 'get',
            url: 'comments',
            params: {
                post: postId,
                parent: 0,
                per_page: perPage,
                order,
                offset,
                order_by: orderBy,
                rnd: timestamp,
            },
            actions: {
                pre: COMMENT_FETCH,
                success: COMMENT_FETCH_SUCCESS,
                failure: COMMENT_FETCH_FAILURE,
            },
        }))
    }
}

export function fetchCreateComment(params) {
    return fetchGeneric({
        method: 'post',
        params,
        url: 'comments',
        actions: {
            pre: COMMENT_CREATE,
            success: COMMENT_CREATE_SUCCESS,
            failure: COMMENT_CREATE_FAILURE,
        },
    })
}

export function fetchLoadMore({postId, offset = 0, perPage = 10, order, orderBy}: FetchCommentProps) {

    const timestamp = new Date().getTime();

    return (dispatch) => {
        dispatch({type: COMMENT_FETCH_FILTER, payload: {perPage, offset, order, orderBy}});

        return dispatch(fetchGeneric({
            method: 'get',
            url: 'comments',
            params: {
                post: postId,
                parent: 0,
                per_page: perPage,
                order,
                offset,
                order_by: orderBy,
                rnd: timestamp,
            },
            actions: {
                pre: COMMENT_LOAD_MORE,
                success: COMMENT_LOAD_MORE_SUCCESS,
                failure: COMMENT_LOAD_MORE_FAILURE,
            },
        }));
    }
}

export function fetchDeleteComment(id) {
    return fetchGeneric({
        method: 'post',
        url: 'comment/delete' + id,
        actions: {
            pre: COMMENT_DELETE,
            success: COMMENT_DELETE_SUCCESS,
            failure: COMMENT_DELETE_FAILURE,
        },
    })
}

// todo
export function fetchUpdateComment(data) {
    return fetchGeneric({
        method: 'post',
        data,
        url: 'comments/update',
        actions: {
            pre: COMMENT_UPDATE,
            success: COMMENT_UPDATE_SUCCESS,
            failure: COMMENT_UPDATE_FAILURE,
        },
    })
}
