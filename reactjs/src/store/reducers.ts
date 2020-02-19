import {combineReducers} from 'redux';
import comments, {CommentReducerProps} from '../core/comment/commentReducers';

export interface StoreProps {
    comments: CommentReducerProps;
}

const rootReducer = combineReducers({
    comments,
});

export default rootReducer;
