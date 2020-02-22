import axios from '../config/api';
import qs from 'qs';
import {AxiosPromise, AxiosError, AxiosResponse, AxiosRequestConfig} from "axios";
import {getSettings} from "~/hooks/setting";

export interface FetchActions {
    pre?: string;
    success: string | ((response: any) => void);
    failure: string;
    always?: string;
}

export interface FetchProps {
    method?: string;
    url: string;
    params?: {};
    data?: {},
    actions: FetchActions;
    headers?: {},
    axiosProps?: AxiosRequestConfig;
}

export function fetch({
    method = 'get',
    url,
    data,
    params,
    actions: {
        pre,
        success,
        failure,
        always,
    },
    headers = {},
    ...axiosProps
}: FetchProps): AxiosPromise {

    if (!headers || headers && !headers['X-WP-Nonce']) {
        const settings = getSettings();
        headers['X-WP-Nonce'] = settings && settings.nonce || null;
    }

    if (!(data instanceof FormData)) {
        data = qs.stringify(data);
    }

    return (dispatch): Promise<any> => {

        if (typeof pre === 'string') {
            dispatch({type: pre});
        }

        return axios({
            method,
            url,
            params,
            data,
            headers,
            ...axiosProps,
        }).then(function(response: AxiosResponse) {
            if (typeof success === 'string') {
                dispatch({type: success, payload: response.data});
            } else if (typeof success === 'function') {
                dispatch(success(response.data));
            }
        }).catch(function(error: AxiosError) {
            dispatch({type: failure, payload: error});
        }).finally(() => {
            if (typeof always === 'string') {
                dispatch({type: always, payload: null});
            }
        })
    }
}

export interface ManageResponseProps {
    reducer: any;
    dispatch?: any;
    onSuccess?: (response: any | null | undefined) => void;
    onError?: (error: string | {}) => void,
    displaySnackbarOnError?: boolean | false,
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
export function manageReducer({
    reducer,
    onSuccess,
    onError,
}: ManageResponseProps) {

    if (!reducer) {
        return;
    }

    if (reducer && reducer.error && typeof reducer.error !== null) {

        const error = reducer && reducer.error;

        if (typeof onError === 'function') {
            onError(error);
        }
    } else if (!reducer.isFetching && reducer.status === 'ok') {
        if (typeof onSuccess === 'function') {
            onSuccess(reducer.response);
        }
    }
}
