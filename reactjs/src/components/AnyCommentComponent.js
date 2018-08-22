import React from 'react';
import axios from 'axios';

/**
 * Generic wrapper for React component.
 */
class AnyCommentComponent extends React.Component {
    static defaultProps = {
        ...React.Component.defaultProps,
        settings: 'anyCommentApiSettings' in window ? window.anyCommentApiSettings : null,
        user: 'anyCommentApiSettings' in window ? window.anyCommentApiSettings.user : null,
        axios: axios.create({
            baseURL: 'anyCommentApiSettings' in window ? window.anyCommentApiSettings.restUrl : '',
            timeout: 10000,
        }),
    };


    /**
     * Check whether user is guest or not.
     * @returns {boolean}
     */
    isGuest() {
        return !this.props.user;
    }

    /**
     * Get current user.
     * @returns {null}
     */
    getCurrentUser() {
        return this.props.user;
    }

    /**
     * Get list of all settings defined in WordPress.
     * @returns {*}
     */
    getSettings() {
        return this.props.settings;
    }

    /**
     * Get list of options defined in WordPress admin.
     * @returns {*}
     */
    getOptions() {
        return this.props.settings.options;
    }

    /**
     * Get list of available translations.
     * @returns {*}
     */
    getTranslations() {
        return this.props.settings.options.i18;
    }


    storeComment(text) {
        this.localStore('anycomment-content', text);
    }

    storeAuthorName(name) {
        this.localStore('anycomment-name', name);
    }

    storeAuthorEmail(email) {
        this.localStore('anycomment-email', email);
    }

    storeAuthorWebsite(website) {
        this.localStore('anycomment-website', website);
    }

    localStore(key, text) {
        if (!this.localStorageSupport()) {
            console.log('do not support');
            return false;
        }
        console.log(key, text, 'lol');
        localStorage.setItem(key, text);
    }

    localStorageSupport() {
        const test = 'test';
        try {
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch (e) {
            return false;
        }
    }
}

export default AnyCommentComponent;