import 'react-app-polyfill/ie9';
import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
// import registerServiceWorker from './registerServiceWorker';

const rootEl = document.getElementById('anycomment-root');

if (rootEl) {
    ReactDOM.render(<App/>, rootEl);
}
// registerServiceWorker();
