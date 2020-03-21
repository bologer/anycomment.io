import {fetch} from '~/helpers/action';

export const PAGE_SUBSCRIPTION = '@extensions/page-subscription/subscribe';
export const PAGE_SUBSCRIPTION_SUCCESS = '@extensions/page-subscription/subscribe/success';
export const PAGE_SUBSCRIPTION_FAILURE = '@extensions/page-subscription/subscribe/failure';

/**
 * Subscribe user to provided post by email.
 *
 * @param {number} postId
 * @param {string} email
 */
export function subscribeToPage(postId: number, email: string) {
    return fetch({
        method: 'post',
        url: 'subscribe',
        params: {
            post: postId,
            email,
        },
        actions: {
            pre: PAGE_SUBSCRIPTION,
            success: PAGE_SUBSCRIPTION_SUCCESS,
            failure: PAGE_SUBSCRIPTION_FAILURE,
        },
    });
}
