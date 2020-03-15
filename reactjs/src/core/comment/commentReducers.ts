import {
    COMMENT_DELETE,
    COMMENT_DELETE_SUCCESS,
    COMMENT_DELETE_FAILURE,
    COMMENT_DELETE_INVALIDATE,
    COMMENT_FETCH,
    COMMENT_FETCH_SUCCESS,
    COMMENT_FETCH_FAILURE,
    COMMENT_FETCH_FILTER,
    COMMENT_UPDATE,
    COMMENT_UPDATE_SUCCESS,
    COMMENT_UPDATE_FAILURE,
    COMMENT_UPDATE_INVALIDATE,
    COMMENT_LOAD_MORE,
    COMMENT_LOAD_MORE_SUCCESS,
    COMMENT_LOAD_MORE_FAILURE,
    COMMENT_CREATE,
    COMMENT_CREATE_SUCCESS,
    COMMENT_CREATE_FAILURE,
    COMMENT_CREATE_INVALIDATE,
    COMMENT_FORM,
    CommentFormType,
    COMMENT_FORM_INVALIDATE,
    COMMENT_FETCH_SALIENT_SUCCESS,
    COMMENT_FETCH_SALIENT_FAILURE,
    COMMENT_LIKE,
    COMMENT_LIKE_SUCCESS,
    COMMENT_LIKE_FAILURE,
} from './CommentActions';
import {ReducerEnvelope} from '~/typings/ReducerEnvelope';
import {ListResponse} from '~/typings/ListResponse';
import {CommentModel} from '~/typings/models/CommentModel';

export interface CommentListFilter {
    order: 'asc' | 'desc';
    perPage: number;
    orderBy: string;
}

export interface CommentForm {
    [commentId: number]: {
        type: CommentFormType;
        comment: CommentModel;
    };
}

export interface CommentReducerProps {
    list: ReducerEnvelope<ListResponse<CommentModel>> | undefined;
    listFilter: CommentListFilter | undefined;
    update: {} | undefined;
    delete: {} | undefined;
    create: {} | undefined;
    form: CommentForm | undefined;
    like: {} | undefined;
}

// eslint-disable-next-line require-jsdoc
export default function(state = {}, action) {
    switch (action.type) {
        case COMMENT_FETCH:
            return {...state, list: {isFetching: true}};
        case COMMENT_FETCH_SUCCESS:
        case COMMENT_FETCH_SALIENT_SUCCESS:
            return {...state, list: {isFetching: false, payload: action.payload}};
        case COMMENT_FETCH_FAILURE:
        case COMMENT_FETCH_SALIENT_FAILURE:
            return {...state, list: {isFetching: false, payload: action.payload}};
        case COMMENT_LOAD_MORE:
            return {...state, loadMore: {isFetching: true}};
        case COMMENT_LOAD_MORE_SUCCESS:
            return {
                ...state,
                list: {
                    isFetching: false,
                    payload: {
                        items: [...state.list.payload.items, ...action.payload.items],
                        meta: action.payload.meta,
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
            return {...state, delete: {isFetching: false, payload: action.payload}};
        case COMMENT_DELETE_FAILURE:
            return {...state, delete: {isFetching: false, payload: action.payload}};
        case COMMENT_DELETE_INVALIDATE:
            return {...state, delete: undefined};

        case COMMENT_UPDATE:
            return {...state, update: {isFetching: true}};
        case COMMENT_UPDATE_SUCCESS:
            return {...state, update: {isFetching: false, payload: action.payload}};
        case COMMENT_UPDATE_FAILURE:
            return {...state, update: {isFetching: false, payload: action.payload}};
        case COMMENT_UPDATE_INVALIDATE:
            return {...state, update: undefined};

        case COMMENT_CREATE:
            return {...state, create: {isFetching: true}};
        case COMMENT_CREATE_SUCCESS:
            return {...state, create: {isFetching: false, payload: action.payload}};
        case COMMENT_CREATE_FAILURE:
            return {...state, create: {isFetching: false, payload: action.payload}};
        case COMMENT_CREATE_INVALIDATE:
            return {...state, create: undefined};

        case COMMENT_LIKE:
            return {...state, like: {[action.commentId]: {isFetching: true}}};
        case COMMENT_LIKE_SUCCESS:
            return {...state, like: {[action.commentId]: {isFetching: false, payload: action.payload}}};
        case COMMENT_LIKE_FAILURE:
            return {...state, like: {[action.commentId]: {isFetching: false, payload: action.payload}}};

        case COMMENT_FORM:
            return {...state, form: {[action.payload.comment.id]: action.payload}};
        case COMMENT_FORM_INVALIDATE:
            return {...state, form: undefined};
        default:
            return state;
    }
}
