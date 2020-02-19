/**
 * Check for generic comments anchor.
 * Primarily this can be used to move users directly to comments section.
 *
 * @returns {boolean}
 */
export function hasCommentSectionAnchor(): boolean {
    const hash: string = window.location.hash;
    return hash !== '' && /#(comments|respond|to-comments|load-comments)$/.test(hash);
}

/**
 * Check for specific comments.
 * Can be used to load user to specific comment.
 *
 * @returns {boolean}
 */
export function hasSpecificCommentAnchor(): boolean {
    const hash: string = window.location.hash;
    return hash !== "" && /#comment-\d{1,20}$/.test(hash);
}