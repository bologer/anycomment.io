import React from 'react';
import axios from 'axios';
import {toast} from 'react-toastify';
import CommonHelper from "./helpers/CommonHelper";


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
     * Move to comment and highlight it for some period.
     *
     * @param id
     * @param highlightTime
     * @param e
     * @returns {boolean}
     */
    moveToCommentAndHighlight(id, highlightTime = 2500, e) {

        if (!id) {
            return false;
        }

        if (id.indexOf('#') !== -1) {
            id = id.replace('#', '');
        }

        const element = document.getElementById(id),
            highlightClass = 'comment-single-highlight';

        console.log(id, element);

        if (!element) {
            return false;
        }

        this.moveToElement(id, function () {

            element.classList.add(highlightClass);

            setTimeout(function () {
                element.classList.remove(highlightClass);
            }, highlightTime);
        });

        return false;
    }

    /**
     * Move to specified element.
     *
     * @param id
     * @param callback
     */
    moveToElement(id, callback) {
        CommonHelper.moveToElement(id, callback);
    }

    /**
     * Check for generic comments anchor.
     * Primarily this can be used to move users directly to comments section.
     *
     * @returns {boolean}
     */
    hasCommentSectionAnchor() {
        const hash = window.location.hash;
        return hash !== '' && /#(comments|respond|to-comments|load-comments)$/.test(hash);
    }

    /**
     * Check for specific comments.
     * Can be used to load user to specific comment.
     *
     * @returns {boolean}
     */
    hasSpecificCommentAnchor() {
        const hash = window.location.hash;
        return hash !== "" && /#comment-\d{1,20}$/.test(hash);
    }

    /**
     * Show success message toast.
     * @param message
     * @param options
     */
    showSuccess(message, options = null) {
        toast.success(message, options);
    }

    /**
     * Show error message toast. It can accept axios error, message
     * will be automatically retrieved.
     *
     * @param data
     * @param options
     * @returns {boolean}
     */
    showError(data, options = null, toastId = null) {
        if (!data) {
            return false;
        }

        let message = '';

        if ('response' in data && 'data' in data.response) {
            message = data.response.data.message;
        } else {
            message = data;
        }

        if (toastId !== null) {
            if (!('render' in options)) {
                options.render = message;
            }

            if (!('type' in options)) {
                options.type = toast.TYPE.ERROR;
            }

            toast.update(toastId, options);
            return;
        }

        toast.error(message, options);
    }

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
        return this.localGet('anycomment-content')
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
        this.localDelete('anycomment-content')
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

        text = text.trim();
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