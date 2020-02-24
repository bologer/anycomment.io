import {combineReducers} from 'redux';
import comments, {CommentReducerProps} from '../core/comment/commentReducers';
import notifications, {NotificationReducersProps} from '~/core/notifications/notificationReducers';

export interface StoreProps {
    comments: CommentReducerProps;
    notifications: NotificationReducersProps;
}

const rootReducer = combineReducers({
    comments,
    notifications,
});

export default rootReducer;
