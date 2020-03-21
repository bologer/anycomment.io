import {PAGE_SUBSCRIPTION, PAGE_SUBSCRIPTION_SUCCESS, PAGE_SUBSCRIPTION_FAILURE} from './PageSubscriptionActions';
import {ReducerEnvelope} from '~/typings/ReducerEnvelope';

export interface PageSubscriptionReducerProps {
    subscription: ReducerEnvelope<{}> | undefined;
}

// eslint-disable-next-line require-jsdoc
export default function (state = {}, action) {
    switch (action.type) {
        case PAGE_SUBSCRIPTION:
            return {...state, subscription: {isFetching: true}};
        case PAGE_SUBSCRIPTION_SUCCESS:
            return {...state, subscription: {isFetching: false, payload: action.payload}};
        case PAGE_SUBSCRIPTION_FAILURE:
            return {...state, subscription: {isFetching: false, payload: action.payload}};
        default:
            return state;
    }
}
