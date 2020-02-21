import React, {useEffect, useState} from 'react'
import CommentContainer from './components/CommentContainer'
import CommentCopyright from './components/CommentCopyright'
import './css/app.css'
import {ToastContainer} from 'react-toastify'
import {toast} from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import GlobalHeader from "./components/GlobalHeader";
import {hasCommentSectionAnchor, hasSpecificCommentAnchor} from './helpers/url'
import {useConfig, useOptions, useSettings} from "~/hooks/setting";
import {Provider} from "react-redux";
import {configureStore} from './store/configureStore'
import { commentsVisible, handleScrollToComments } from './helpers/comment'

const store = configureStore({});

/**
 * App is main component of the application.
 */
export default function App() {

    const settings = useSettings();
    const options = useOptions();
    const config = useConfig();
    const [shouldLoad, setShouldLoad] = useState<boolean>(false);

    useEffect(() => {
        handleScrollToComments(config.root);
        handleLoadOnScroll();
        handleErrors();
        maybeAddIEMeta();
    }, []);

    /**
     * Handle situation when required to load comments on scroll to them.
     */
    function handleLoadOnScroll() {

        if (!shouldLoad) {
            /**
             * When load on scroll is not enabled or
             * there is scroll to comments or specific comment in the url hash.
             */
            if (commentsVisible(config.root) || options && !options.isLoadOnScroll || (hasCommentSectionAnchor() || hasSpecificCommentAnchor())) {
                setShouldLoad(true)
            }

            if (!shouldLoad) {
                window.addEventListener('scroll', function() {
                    if (commentsVisible(config.root)) {
                        window.removeEventListener('scroll', function() {
                        });
                        setShouldLoad(true);
                    }
                });
            }
        }
    }

    /**
     * Make plugin IE compatible.
     */
    function maybeAddIEMeta() {
        const metas = document.getElementsByTagName('meta');

        let found = false;
        for (let i = 0; i < metas.length; i++) {
            if (metas[i].getAttribute('http-equiv') === 'X-UA-Compatible') {
                found = true;
                break;
            }
        }

        if (!found) {
            var meta = document.createElement('meta');
            meta.httpEquiv = "X-UA-Compatible";
            meta.content = "IE=edge";
            document.getElementsByTagName('head')[0].appendChild(meta);
        }
    }

    /**
     * Handle possible backend errors.
     */
    function handleErrors() {
        const errors = settings && settings.errors || undefined;

        if (errors) {
            errors.map((message) => toast.error(message));
        }
    }

    if (!shouldLoad) {
        return null;
    }

    return (
        <Provider store={store}>
            <div id="anycomment-root-inner" className="anycomment">
                <ToastContainer />
                <GlobalHeader />
                <CommentContainer />
                <CommentCopyright />
            </div>
        </Provider>
    );
}
