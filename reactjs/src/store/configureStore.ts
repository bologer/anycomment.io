import {createStore, applyMiddleware, compose} from 'redux';
import thunkMiddleware from 'redux-thunk';
import promiseMiddleware from 'redux-promise';
import {createLogger} from 'redux-logger';
import rootReducer from './reducers';

const loggerMiddleware = createLogger();

let middleWares = [thunkMiddleware, promiseMiddleware];
let composeEnhancers = compose;

if (process.env.NODE_ENV === 'development') {
    middleWares.push(loggerMiddleware);

    /* eslint-disable no-underscore-dangle */
    // @ts-ignore
    composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;
    /* eslint-enable */
}

// eslint-disable-next-line require-jsdoc
export function configureStore(preloadedState) {
    return createStore(rootReducer, preloadedState, composeEnhancers(applyMiddleware(...middleWares)));
}
