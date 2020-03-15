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
            baseURL:
                process.env.ENV === 'dev'
                    ? process.env.API_URL
                    : 'anyCommentApiSettings' in window
                        ? window.anyCommentApiSettings.restUrl
                        : '',
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

    getComment() {
        return this.localGet('anycomment-content');
    }

    getAuthorName() {
        return this.localGet('anycomment-name');
    }

    getAuthorEmail() {
        return this.localGet('anycomment-email');
    }

    getAuthorWebsite() {
        return this.localGet('anycomment-website');
    }

    dropComment() {
        this.localDelete('anycomment-content');
    }

    dropAuthorName() {
        this.localDelete('anycomment-name');
    }

    dropAuthorEmail() {
        this.localDelete('anycomment-email');
    }

    dropAuthorWebsite() {
        this.localDelete('anycomment-website');
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

    localGet(key, returnValue = '') {
        if (!this.localStorageSupport()) {
            return returnValue;
        }

        if (!key) {
            return returnValue;
        }

        let value = localStorage.getItem(key) || '';

        if (!value) {
            return returnValue;
        }

        value = value.trim();

        return value || returnValue;
    }

    localStore(key, text) {
        if (!this.localStorageSupport()) {
            return false;
        }

        if (!key) {
            return false;
        }

        if (typeof text === 'string') {
            text = text.trim();
        }

        localStorage.setItem(key, text);
    }

    localDelete(key) {
        if (!this.localStorageSupport()) {
            return false;
        }

        if (localStorage.getItem(key)) {
            localStorage.removeItem(key);
            return true;
        }

        return false;
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

    /**
     * Get short or full version of  locale.
     *
     * Short: e.g. ru, whereas long: ru_RU
     *
     * @param full {Boolean}
     * @returns {*}
     */
    getLocale(full = false) {
        const settings = this.getSettings(),
            locale = settings.locale;

        if (full) {
            return locale;
        }

        return locale.substring(0, 2);
    }
}

export default AnyCommentComponent;
