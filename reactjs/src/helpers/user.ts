import {getSettings} from "~/hooks/setting";

/**
 * Check whether user is guest or not.
 */
export function isGuest() {
    const settings = getSettings();

    return settings && !settings.user;
}