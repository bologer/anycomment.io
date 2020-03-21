import {combineReducers} from 'redux';
import comments, {CommentReducerProps} from '../core/comment/commentReducers';
import notifications, {NotificationReducersProps} from '~/core/notifications/notificationReducers';
import extensions, {ExtensionReducerProps} from '~/core/extensions';

export interface StoreProps {
    comments: CommentReducerProps;
    extensions: ExtensionReducerProps;
    notifications: NotificationReducersProps;
}

const rootReducer = combineReducers({
    comments,
    notifications,
    extensions,
});

export default rootReducer;
