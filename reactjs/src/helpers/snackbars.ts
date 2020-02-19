import {toast, ToastContent, ToastOptions} from 'react-toastify';

/**
 * Show success message toast.
 * @param content
 * @param options
 */
export function showSuccess(content: ToastContent, options?: ToastOptions) {
    toast.success(content, options);
}

/**
 * Show error message toast. It can accept axios error, message
 * will be automatically retrieved.
 *
 * @param data
 * @param options
 * @param toastId
 * @returns {boolean}
 */
export function showError(data, options?: ToastOptions, toastId?: string) {
    if (data) {

        let message = '';

        if ('response' in data && 'data' in data.response) {
            message = data.response.data.message;
        } else {
            message = data;
        }

        if (typeof toastId === 'number') {
            if (!('render' in options)) {
                options.render = message;
            }

            if (!('type' in options)) {
                options.type = toast.TYPE.ERROR;
            }

            toast.update(toastId, options);
        } else {
            toast.error(message, options);
        }
    }
}
