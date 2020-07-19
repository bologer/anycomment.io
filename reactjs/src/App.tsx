import React, {useEffect, useState} from 'react';
import CommentContainer from './core/comment/CommentContainer';
import CommentCopyright from './core/comment/CommentCopyright';
import './css/app.css';
import GlobalHeader from './components/GlobalHeader';
import {hasCommentSectionAnchor, hasSpecificCommentAnchor} from './helpers/url';
import {useConfig, useOptions, useSettings} from '~/hooks/setting';
import {useDispatch} from 'react-redux';
import {commentsVisible, handleScrollToComments} from './helpers/comment';
import NotificationList from '~/core/notifications/NotificationList';
import {failureSnackbar} from '~/core/notifications/NotificationActions';
import {fireEvent} from '~/helpers/events';

/**
 * App is main component of the application.
 */
export default function App() {
    const settings = useSettings();
    const options = useOptions();
    const config = useConfig();
    const dispatch = useDispatch();
    const [shouldLoadApp, setShouldLoadApp] = useState<boolean>(false);

    useEffect(() => {
        handleScrollToComments(config.root);
        handleLoadOnScroll();
        handleErrors();
        maybeAddIEMeta();

        fireEvent(config.events, 'init', {postId: config.postId});
    }, []);

    /**
     * Handle situation when required to load comments on scroll to them.
     */
    function handleLoadOnScroll() {
        if (!shouldLoadApp) {
            /**
             * When load on scroll is not enabled or
             * there is scroll to comments or specific comment in the url hash.
             */
            if (
                commentsVisible(config.root) ||
                (options && !options.isLoadOnScroll) ||
                hasCommentSectionAnchor() ||
                hasSpecificCommentAnchor()
            ) {
                setShouldLoadApp(true);
            }

            if (!shouldLoadApp) {
                window.addEventListener('scroll', () => {
                    if (commentsVisible(config.root)) {
                        window.removeEventListener('scroll', () => {});
                        setShouldLoadApp(true);
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

        let isMetaFound = false;
        for (let i = 0; i < metas.length; i++) {
            if (metas[i].getAttribute('http-equiv') === 'X-UA-Compatible') {
                isMetaFound = true;
                break;
            }
        }

        if (!isMetaFound) {
            const meta = document.createElement('meta');
            meta.httpEquiv = 'X-UA-Compatible';
            meta.content = 'IE=edge';
            document.getElementsByTagName('head')[0].appendChild(meta);
        }
    }

    /**
     * Handle possible backend errors.
     */
    function handleErrors() {
        const errors = (settings && settings.errors) || undefined;

        if (errors) {
            errors.map(message => dispatch(failureSnackbar(message)));
        }
    }

    if (!shouldLoadApp) {
        return null;
    }

    return (
        <div className='anycomment-app anycomment'>
            <NotificationList />
            <GlobalHeader />
            <CommentContainer />
            <CommentCopyright />
        </div>
    );
}
