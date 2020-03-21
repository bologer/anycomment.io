import {combineReducers} from 'redux';
import pageRating, {PageRatingReducerProps} from './page-rating/pageRatingReducers';

export interface ExtensionReducerProps {
    pageRating: PageRatingReducerProps;
}

export default combineReducers({
    pageRating: pageRating,
});
