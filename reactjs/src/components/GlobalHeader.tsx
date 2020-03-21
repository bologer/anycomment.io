import React from 'react';
import PageRating from '../core/extensions/page-rating/PageRating';
import ProfileDropdown from './ProfileDropdown';

/**
 * Header which renders above main form.
 *
 * @constructor
 */
export default function GlobalHeader() {
    return (
        <div className='anycomment anycomment-global-header'>
            <PageRating />
            <ProfileDropdown />
        </div>
    );
}
