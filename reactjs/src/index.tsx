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
if (!window.AnyComment) {
    // @ts-ignore
    window.AnyComment = {};
}

// @ts-ignore
const settings = window.anyCommentApiSettings || undefined;
// @ts-ignore
const widgets: ConfigProps[] = window.AnyComment.WP || [];

/**
 * Core manager which helps to bootstrap application.
 */
class Manager {
    /**
     * Helps to bootstrap application with provided config.
     * It prepares all required components and contexts and uses react DOM render() functions
     * to render it inside provided root component (from config).
     *
     * @param config
     */
    static launchWith(config: ConfigProps) {
        const container = document.getElementById(config.root);
        const store = configureStore({});

        if (container) {
            const app = (
                <AnyCommentProvider config={config} settings={settings}>
                    <Provider store={store}>
                        <SnackbarProvider>
                            <App />
                        </SnackbarProvider>
                    </Provider>
                </AnyCommentProvider>
            );

            render(app, container);
        }
    }
}

// Ability to reinit after application already loaded
// @ts-ignore
window.AnyComment.Manager = {};
// @ts-ignore
window.AnyComment.Manager.init = (config: ConfigProps) => {
    Manager.launchWith(config);
};

if (widgets && settings) {
    widgets.forEach(widgetConfig => {
        Manager.launchWith(widgetConfig);
    });
} else {
    // eslint-disable-next-line no-console
    console.warn(
        `Please check that you have proper selector on the page + "anyCommentApiSettings" in window object. It looks something is missing.`
    );
}
