export const ENQUEUE_SNACKBAR = 'ENQUEUE_SNACKBAR';
export const REMOVE_SNACKBAR = 'REMOVE_SNACKBAR';
export const CLOSE_SNACKBAR = 'CLOSE_SNACKBAR';

/**
 * Function returns enqueue snackbar action to put new notification in redux store
 * @param notification
 * @return {{notification: {key: number}, type: *}}
 */
export function enqueueSnackbar(notification) {
    return {
        type: ENQUEUE_SNACKBAR,
        notification: {
            key: new Date().getTime() + Math.random(),
            ...notification,
        },
    };
}

/**
 * Function returns close remove action to remove notification with given key from redux store
 * @param key
 * @return {{type: *, key: *}}
 */
export function removeSnackbar(key) {
    return {
        type: REMOVE_SNACKBAR,
        key,
    };
}

/**
 * Function returns close snackbar action to mark notification with given key as dismissed
 * If no key is provided, all snackbars will be marked as dismissed
 * @param key
 * @return {{dismissAll: boolean, type: *, key: *}}
 */
export function closeSnackbar(key) {
    return {
        type: CLOSE_SNACKBAR,
        dismissAll: !key,
        key,
    };
}

/**
 * Displays success alert.
 * @param message
 * @return {{notification: {key: number}, type: string}}
 */
export function successSnackbar(message) {
    return enqueueSnackbar({
        message,
        options: {
            variant: 'success',
        },
    });
}

/**
 * Displays failure alert.
 *
 * @param message
 * @return {{notification: {key: number}, type: string}}
 */
export function failureSnackbar(message) {
    return enqueueSnackbar({
        message,
        options: {
            variant: 'error',
        },
    });
}

/**
 * Displays warning alert.
 *
 * @param message
 * @return {{notification: {key: number}, type: string}}
 */
export function warningSnackbar(message) {
    return enqueueSnackbar({
        message,
        options: {
            variant: 'warning',
        },
    });
}
