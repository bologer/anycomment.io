/**
 * Generic reducer envelope returned.
 */
import {BasicEnvelope} from './BasicEnvelope';

export interface ReducerEnvelope<Response> extends BasicEnvelope<Response> {
    isFetching?: boolean;
}
