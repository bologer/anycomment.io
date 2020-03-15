import React from 'react';
import PageRating from './PageRating';
import ProfileDropdown from './ProfileDropdown';

export default function GlobalHeader() {
    return (
        <div className='anycomment anycomment-global-header'>
            <PageRating />
            <ProfileDropdown />
        </div>
    );
}
