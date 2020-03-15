import 'react-app-polyfill/ie9';
import React from 'react';
import {render} from 'react-dom';
import App from './App';
import 'core-js/es6/number';
import AnyCommentProvider, {ConfigProps} from '~/components/AnyCommentProvider';
import {SnackbarProvider} from 'notistack';
import {Provider} from 'react-redux';
import {configureStore} from '~/store/configureStore';

// @ts-ignore
const settings = window.anyCommentApiSettings || undefined;
// @ts-ignore
const widgets: ConfigProps[] = (window.AnyComment && window.AnyComment.WP) || [];

if (widgets && settings) {
    widgets.forEach(widgetConfig => {
        const container = document.getElementById(widgetConfig.root);
        const store = configureStore({});

        if (container) {
            const app = (
                <AnyCommentProvider config={widgetConfig} settings={settings}>
                    <Provider store={store}>
                        <SnackbarProvider>
                            <App />
                        </SnackbarProvider>
                    </Provider>
                </AnyCommentProvider>
            );

            render(app, container);
        }
    });
} else {
    // eslint-disable-next-line no-console
    console.warn(
        `Please check that you have proper selector on the page + "anyCommentApiSettings" in window object. It looks something is missing.`
    );
}
