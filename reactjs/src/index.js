import 'react-app-polyfill/ie9';
import React from 'react';
import {render} from 'react-dom';
import App from './App';
import 'core-js/es6/number';

const commentsRootSelector = 'anycomment-root';
const commentsRoot = document.getElementById(commentsRootSelector);

if (commentsRoot) {
    render(<App />, commentsRoot);
} else {
    console.warn(`Unable to render comments as #${commentsRootSelector} selector does not exist`);
}
