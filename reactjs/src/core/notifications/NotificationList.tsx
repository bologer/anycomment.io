import React, {useEffect, useState} from 'react';
import {useDispatch, useSelector} from 'react-redux';
import {useSnackbar} from 'notistack';
import {removeSnackbar} from './NotificationActions';
import IconButton from '@material-ui/core/IconButton';
import CloseIcon from '@material-ui/icons/Close';
import {StoreProps} from "~/store/reducers";
import {NotificationReducersProps} from "~/core/notifications/notificationReducers";

/**
 * Manages displaying alerts (snackbars) using the notistack library and manages notifications inside Redux state.
 *
 * Closing button is added to each snackbar unless a custom options.action is provided
 */
export default function NotificationList() {
    const dispatch = useDispatch();
    const {notifications} = useSelector<StoreProps, NotificationReducersProps>(store => store.notifications);
    const [displayed, setDisplayed] = useState<Array<string>>([]);
    const {closeSnackbar, enqueueSnackbar} = useSnackbar();

    /**
     * Returns close button component with onClick set to close this particular snackabr
     * @param key
     */
    function getAction(key) {
        return (
            <IconButton onClick={() => closeSnackbar(key)}>
                <CloseIcon />
            </IconButton>
        );
    }

    useEffect(() => {
        notifications.forEach(({key, message, options, dismissed}) => {
            if (dismissed) {
                closeSnackbar(key);
                return;
            }

            if (displayed.includes(key)) {
                return;
            }

            enqueueSnackbar(message, {
                key,
                action: options.action || getAction(key),
                ...options,
                onExited: (_event, key) => {
                    dispatch(removeSnackbar(key));
                },
            });

            setDisplayed(displayed => [...displayed, key]);
        });
    }, [notifications]);

    return null;
}

NotificationList.displayName = 'NotificationList';
