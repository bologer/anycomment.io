import {ENQUEUE_SNACKBAR, REMOVE_SNACKBAR, CLOSE_SNACKBAR} from './NotificationActions';

const DEFAULT_STATE = {notifications: []};

export interface NotificationReducersProps {
    notifications: [];
}

export default function reducer(state = DEFAULT_STATE, action) {
    switch (action.type) {
        case ENQUEUE_SNACKBAR:
            return {
                ...state,
                notifications: [
                    ...state.notifications,
                    {
                        ...action.notification,
                    },
                ],
            };

        case REMOVE_SNACKBAR:
            return {
                ...state,
                notifications: state.notifications.filter(notification => notification.key !== action.key),
            };

        case CLOSE_SNACKBAR:
            return {
                ...state,
                notifications: state.notifications.map(notification =>
                    action.dismissAll || notification.key === action.key
                        ? {...notification, dismissed: true}
                        : {...notification}
                ),
            };

        default:
            return state;
    }
}
