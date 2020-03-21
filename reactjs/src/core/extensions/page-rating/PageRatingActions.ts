import {fetch} from '~/helpers/action';

export const PAGE_RATE = '@extensions/page-rating/rate';
export const PAGE_RATE_SUCCESS = '@extensions/page-rating/rate/success';
export const PAGE_RATE_FAILURE = '@extensions/page-rating/rate/failure';

/**
 * Rate page with provided value.
 *
 * @param {number} postId
 * @param {number} ratingValue
 */
export function ratePage(postId: number, ratingValue: number) {
    return fetch({
        method: 'post',
        url: 'rate',
        params: {
            rating: ratingValue,
            post: postId,
        },
        actions: {
            pre: PAGE_RATE,
            success: PAGE_RATE_SUCCESS,
            failure: PAGE_RATE_FAILURE,
        },
    });
}
