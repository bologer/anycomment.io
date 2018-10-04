import React, {Component} from 'react';
import sanitizeHtml from "sanitize-html";

export default class CommentSanitization extends Component {

    /**
     * Sanitize HTML to the needs of the plugin.
     *
     * @param dirty
     */
    static sanitize(dirty) {
        return sanitizeHtml(dirty, {
            allowedTags: ['p', 'a', 'ul', 'ol', 'blockquote', 'code', 'li', 'b', 'i', 'u', 'strong', 'em', 'br', 'img', 'figure', 'iframe'],
            allowedAttributes: {
                a: ['href', 'target', 'rel'],
                blockquote: ['class'],
                img: ['class', 'src', 'alt'],
            },
            transformTags: {
                'a': sanitizeHtml.simpleTransform('a', {rel: 'noreferrer noopener nofollow'})
            },
            allowedSchemes: ['http', 'https', 'data'],
            allowedIframeHostnames: ['twitter.com'],
            // parser: {
            //     lowerCaseTags: true
            // }
        });
    }
}