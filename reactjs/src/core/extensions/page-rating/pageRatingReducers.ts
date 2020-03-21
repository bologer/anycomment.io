import {PAGE_RATE, PAGE_RATE_SUCCESS, PAGE_RATE_FAILURE} from './PageRatingActions';
import {ReducerEnvelope} from '~/typings/ReducerEnvelope';

export interface PageRatingReducerProps {
    rate: ReducerEnvelope<{}> | undefined;
}

// eslint-disable-next-line require-jsdoc
export default function(state = {}, action) {
    switch (action.type) {
        case PAGE_RATE:
            return {...state, rate: {isFetching: true}};
        case PAGE_RATE_SUCCESS:
            return {...state, rate: {isFetching: false, payload: action.payload}};
        case PAGE_RATE_FAILURE:
            return {...state, rate: {isFetching: false, payload: action.payload}};
        default:
            return state;
    }
}
