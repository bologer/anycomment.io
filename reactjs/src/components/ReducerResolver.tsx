import React from "react";
import Loader from "~/components/Loader";
import {ReducerEnvelope} from "~/typings/ReducerEnvelope";
import {ErrorEnvelope} from "~/typings/ErrorEnvelope";

export interface ReducerResolverProps {
    reducer: ReducerEnvelope<any | ErrorEnvelope> | undefined;
    fetched: (response: any) => void;
    loader?: React.ReactElement;
    showLoader?: boolean;
}

/**
 * Helps to process view logic of component when required to retrieve response object from reducer.
 *
 * For example, after reducer was mapped into the component, it is required to retrieve it as following:
 *
 * ```js
 * const {order} = this.props;
 *
 * const alreadyFetched = order && !order.isFetching;
 * const response = alreadyFetched && order.response ? order.response : null;
 * ```
 *
 * Which is difficult and boring to do everything. This is when this helper component comes handy.
 *
 * It can be used in two different ways:
 *
 * - when you have single reducer to take care of
 * - when you have collection of reducers
 *
 * Single reducer example:
 *
 * ```js
 * const {order} = this.props;
 *
 * <ViewResponse
 *      reducer: order,
 *      fetched: ({reducer: order}) => {
 *          // so, order is now actually order.response
 *      }
 * />
 * ```
 *
 * In collection of reducers, fetched callback should be carefully checked for null's as some of the reducers can be null,
 * when API requests was not finished or got corrupted.
 *
 * @param {object} reducer
 * @param {func} fetched
 * @param {boolean} showLoader
 * @param {React.ElementType} loader
 * @return {null|*}
 * @constructor
 */
export default function ReducerResolver({reducer, fetched, showLoader = true, loader}: ReducerResolverProps) {

    if (!reducer) {
        return null;
    }

    if (typeof fetched !== 'function') {
        console.warn(`success prop should be type of function, now it is ${typeof fetched}`);
        return null;
    }

    if (!loader) {
        loader = <Loader />;
    }

    if (showLoader && reducer && reducer.isFetching) {
        return loader;
    }

    if (!reducer.isFetching && reducer.payload.code && reducer.payload.message) {
        return null;
    }

    return reducer && reducer.payload ?
        fetched(reducer.payload) :
        null;
}
