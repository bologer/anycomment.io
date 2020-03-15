import {moveToElement, scrollTop} from './scroll';
import {hasCommentSectionAnchor} from './url';

/**
 * Move to comment and highlight it for some period.
 *
 * @param id
 * @param highlightTime
 * @param e
 * @returns {boolean}
 */
export function moveToCommentAndHighlight(id, highlightTime = 2500) {
    if (!id) {
        return false;
    }

    if (id.indexOf('#') !== -1) {
        id = id.replace('#', '');
    }

    const element = document.getElementById(id),
        highlightClass = 'comment-single-highlight';

    if (!element) {
        return false;
    }

    moveToElement(id, function() {
        element.classList.add(highlightClass);

        setTimeout(function() {
            element.classList.remove(highlightClass);
        }, highlightTime);
    });

    return false;
}

/**
 * Helper to determine whether comments are visible or not.
 * @param selector
 */
export function commentsVisible(selector) {
    const root = document.getElementById(selector);

    if (!root) {
        return false;
    }

    const tillTop = root.offsetTop,
        body = document.body,
        html = document.documentElement;

    const height = Math.max(
        body.scrollHeight,
        body.offsetHeight,
        html.clientHeight,
        html.scrollHeight,
        html.offsetHeight
    );

    if (height <= window.innerHeight) {
        return true;
    }

    let wH = window.innerHeight,
        currentTop = scrollTop();

    wH = wH * 0.9;

    return currentTop + wH > tillTop;
}

/**
 * Handle scroll to comments.
 */
export function handleScrollToComments(selector: string) {
    if (hasCommentSectionAnchor()) {
        const interval = setInterval(function() {
            let el = document.getElementById(selector);
            if (el) {
                moveToElement(selector);
                clearInterval(interval);
            }
        }, 100);
    }
}
