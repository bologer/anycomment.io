import 'react-app-polyfill/ie9';
import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import 'core-js/es6/number';
// import registerServiceWorker from './registerServiceWorker';

const rootEl = document.getElementById('anycomment-root');

if (rootEl) {
    ReactDOM.render(<App/>, rootEl);
}
// registerServiceWorker();
