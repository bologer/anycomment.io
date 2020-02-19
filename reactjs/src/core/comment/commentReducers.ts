import {
    COMMENT_DELETE,
    COMMENT_DELETE_SUCCESS,
    COMMENT_DELETE_FAILURE,
    COMMENT_FETCH,
    COMMENT_FETCH_SUCCESS,
    COMMENT_FETCH_FAILURE,
    COMMENT_FETCH_FILTER,
    COMMENT_UPDATE,
    COMMENT_UPDATE_SUCCESS,
    COMMENT_UPDATE_FAILURE,
    COMMENT_LOAD_MORE,
    COMMENT_LOAD_MORE_SUCCESS,
    COMMENT_LOAD_MORE_FAILURE,
    COMMENT_CREATE,
    COMMENT_CREATE_SUCCESS,
    COMMENT_CREATE_FAILURE,
} from './CommentActions';
import {ReducerEnvelope} from "~/typings/ReducerEnvelope";
import {ListResponse} from "~/typings/ListResponse";
import {CommentItem} from "~/typings/models/CommentItem";

const DEFAULT_STATE = {
    list: undefined,
    update: undefined,
    delete: undefined,
};

export interface CommentListFilter {
    order: 'asc' | 'desc';
    perPage: number;
    orderBy: string;
}

export interface CommentReducerProps {
    list: ReducerEnvelope<ListResponse<CommentItem>> | undefined;
    listFilter: CommentListFilter | undefined;
    update: {} | undefined;
    delete: {} | undefined;
    create: {} | undefined;
}

export default function(state = DEFAULT_STATE, action) {
    switch (action.type) {
        case COMMENT_FETCH:
            return {...state, list: {isFetching: true}};
        case COMMENT_FETCH_SUCCESS:
            return {...state, list: {isFetching: false, ...action.payload}};
        case COMMENT_FETCH_FAILURE:
            return {...state, list: {isFetching: false, ...action.payload}};

        case COMMENT_LOAD_MORE:
            return {...state, loadMore: {isFetching: true}};
        case COMMENT_LOAD_MORE_SUCCESS:
            console.log(action.payload);
            return {
                ...state,
                list: {
                    isFetching: false,
                    response: {
                        items: [
                            ...state.list.response.items,
                            ...action.payload.response.items,
                        ],
                        meta: action.payload.response.meta,
                        error: null
                    },
                },
                loadMore: {
                    isFetching: false,
                    payload: action.payload,
                },
            };
        case COMMENT_LOAD_MORE_FAILURE:
            return {...state, loadMore: {isFetching: false, payload: action.payload}};

        case COMMENT_FETCH_FILTER:
            return {...state, listFilter: action.payload};

        case COMMENT_DELETE:
            return {...state, delete: {isFetching: true}};
        case COMMENT_DELETE_SUCCESS:
            return {...state, delete: {isFetching: false, ...action.payload}};
        case COMMENT_DELETE_FAILURE:
            return {...state, delete: {isFetching: false, ...action.payload}};

        case COMMENT_UPDATE:
            return {...state, update: {isFetching: true}};
        case COMMENT_UPDATE_SUCCESS:
            return {...state, update: {isFetching: false, ...action.payload}};
        case COMMENT_UPDATE_FAILURE:
            return {...state, update: {isFetching: false, ...action.payload}};

        case COMMENT_CREATE:
            return {...state, create: {isFetching: true}};
        case COMMENT_CREATE_SUCCESS:
            return {...state, create: {isFetching: false, ...action.payload}};
        case COMMENT_CREATE_FAILURE:
            return {...state, create: {isFetching: false, ...action.payload}};

        default:
            return state;
    }
}