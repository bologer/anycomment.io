import 'react-app-polyfill/ie9';
import React from 'react';
import {render} from 'react-dom';
import App from './App';
import 'core-js/es6/number';
import AnyCommentProvider, {ConfigProps} from "~/components/AnyCommentProvider";

// @ts-ignore
const settings = window.anyCommentApiSettings || undefined;
// @ts-ignore
const widgets: ConfigProps[] = window.AnyComment && window.AnyComment.WP || [];

if (widgets && settings) {

    widgets.forEach(widgetConfig => {
        const container = document.getElementById(widgetConfig.root);

        if (container) {
            const app = (
                <AnyCommentProvider config={widgetConfig} settings={settings}>
                    <App />
                </AnyCommentProvider>
            );

            render(app, container);
        }
    });
} else {
    console.warn(`Please check that you have proper selector on the page + "anyCommentApiSettings" in window object. It looks something is missing.`);
}
