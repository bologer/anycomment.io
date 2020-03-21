import {combineReducers} from 'redux';
import pageRating, {PageRatingReducerProps} from './page-rating/pageRatingReducers';
import pageSubscription, {PageSubscriptionReducerProps} from './post-subscription/pageSubscriptionReducers';

export interface ExtensionReducerProps {
    pageRating: PageRatingReducerProps;
    pageSubscription: PageSubscriptionReducerProps;
}

export default combineReducers({
    pageRating,
    pageSubscription,
});
