/**
 * Get value from storag by key.
 *
 * @param key
 * @param cast
 */
export function get(key: string, cast: boolean = false): any {
    if (!key) {
        // eslint-disable-next-line no-console
        console.warn('get() fail, no key provided');
        return null;
    }

    const s = getStorage();

    if (!s) {
        return null;
    }

    const value = s.getItem(key);

    if (s && value !== null) {
        if (cast && value.indexOf('{') !== -1) {
            return JSON.parse(value);
        }

        return value;
    }

    return null;
}

/**
 * Removes provided key value from storage.
 * @param key
 */
export function remove(key) {
    if (!key) {
        return false;
    }

    const s = getStorage();

    return s && s.removeItem(key);
}

/**
 * Adds new value into local storage or updates existing one.
 *
 * @param key
 * @param value
 */
export function add(key: string | number | {}, value: string | {}) {
    const s = getStorage();

    if (s) {
        let unmutatedValue: string | null = null;

        if (typeof value === 'string' || typeof value === 'number') {
            unmutatedValue = value;
        } else if (typeof value === 'object') {
            unmutatedValue = JSON.stringify(value);
        }

        if (unmutatedValue !== null) {
            s.setItem(key, unmutatedValue);
        } else {
            // eslint-disable-next-line no-console
            console.warn('add() failed to add new localStorage, as value for key is not string and object');
        }
    }
}

/**
 * Check whether local storage available or not.
 */
export function isStorageAvailable() {
    let ls;

    try {
        ls = window.localStorage;
    } catch (e) {
        return false;
    }

    if (!ls) {
        return false;
    }

    return true;
}

/**
 * Returns localstoge when available, otherwise null.
 */
export function getStorage() {
    if (isStorageAvailable()) {
        return window.localStorage;
    }

    return null;
}
