import axios from '../config/api';
import qs from 'qs';
import {AxiosPromise, AxiosError, AxiosResponse, AxiosRequestConfig} from 'axios';
import {getSettings} from '~/hooks/setting';
import {failureSnackbar} from '~/core/notifications/NotificationActions';

export interface FetchActions {
    pre?: string | {};
    success: string | ((response: any) => void);
    failure: string | {};
    always?: string | {};
}

export interface FetchProps {
    method?: string;
    url: string;
    params?: {};
    data?: {};
    actions: FetchActions;
    headers?: {};
    axiosProps?: AxiosRequestConfig;
}

/**
 * Generic fetch method which shares common request preparation logic, such as nonce prefilement to header.
 * @param method
 * @param url
 * @param data
 * @param params
 * @param pre
 * @param success
 * @param failure
 * @param always
 * @param headers
 * @param axiosProps
 */
export function fetch({
    method = 'get',
    url,
    data,
    params,
    actions: {pre, success, failure, always},
    headers = {},
    ...axiosProps
}: FetchProps): AxiosPromise {
    if (!headers || (headers && !headers['X-WP-Nonce'])) {
        const settings = getSettings();
        headers['X-WP-Nonce'] = (settings && settings.nonce) || null;
    }

    if (!(data instanceof FormData)) {
        data = qs.stringify(data);
    }

    return (dispatch): Promise<any> => {
        if (typeof pre === 'string') {
            dispatch({type: pre});
        } else if (typeof pre === 'object') {
            dispatch(pre);
        }

        return axios({
            method,
            url,
            params,
            data,
            headers,
            ...axiosProps,
        })
            .then(function(response: AxiosResponse) {
                const data = response.data;

                if (typeof success === 'string') {
                    dispatch({type: success, payload: data});
                } else if (typeof success === 'function') {
                    dispatch(success(data));
                }

                if (data.code) {
                    dispatch(failureSnackbar(data.message || data.code));
                }
            })
            .catch(function(error: AxiosError) {
                const data = (error.response && error.response.data) || {};

                if (data.code) {
                    dispatch(failureSnackbar(data.message || data.code));
                }

                if (typeof failure === 'string') {
                    dispatch({type: failure, payload: data});
                } else if (typeof failure === 'object') {
                    dispatch(failure);
                }
            })
            .finally(() => {
                if (typeof always === 'string') {
                    dispatch({type: always, payload: null});
                } else if (typeof always === 'object') {
                    dispatch(always);
                }
            });
    };
}

export interface ManageResponseProps {
    reducer: any;
    dispatch?: any;
    onSuccess?: (response: any | null | undefined) => void;
    onError?: (error: string, code: string) => void;
    displaySnackbarOnError?: boolean | false;
}

/**
 * Helper which manages response.
 *
 * For example it can be used inside componentDidUpdate() to display alerts, make some internal component changes.
 *
 * It provides multiple callback functions to check what kind of response this is.
 *
 * Example usage:
 *
 * ```js
 * manageResponse({
 *     formik,
 *     reducer: login,
 *     onSuccess: ({status, error, response}) => {
 *     }
 * })
 *
 * @param {object} reducer response which is mapped mapStateToProps() by Redux.
 * @param {func} onSuccess callback called when success response was returned. Signature: function({status, error, response}).
 * @param {func} onError callback called when API returned some kind of error or envelope has error. Signature: function(error, errorCode).
 * is object list of errors prepared specifically for formik.
 */
export function manageReducer({reducer, onSuccess, onError}: ManageResponseProps) {
    if (!reducer) {
        return;
    }

    if ('isFetching' in reducer && !reducer.isFetching) {
        const payload = (reducer && reducer.payload) || undefined;

        if (payload && typeof payload.code === 'string' && typeof payload.message === 'string') {
            if (typeof onError === 'function') {
                onError(payload.message, payload.code);
            }
        } else if (payload && typeof onSuccess === 'function') {
            onSuccess(payload);
        }
    }
}
