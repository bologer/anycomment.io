import React from 'react';
import Dropdown from 'react-simple-dropdown';
import DropdownTrigger from 'react-simple-dropdown/lib/components/dropdown-trigger';
import DropdownContent from 'react-simple-dropdown/lib/components/dropdown-content';
import Icon from './Icon';
import {faAngleDown} from '@fortawesome/free-solid-svg-icons';
import {useSettings} from '~/hooks/setting';
import {isGuest} from '~/helpers/user';

export default function ProfileDropdown() {
    const settings = useSettings();

    /**
     * Handle dropdown change.
     * @param e
     */
    function handeLogout(e) {
        e.preventDefault();

        window.location.href = settings.urls.logout.replace('&amp;', '&');
    }

    const user = settings.user;

    if (isGuest()) {
        return null;
    }

    let items = [
        <li key={0} onClick={handeLogout}>
            <a href='#'>{settings.i18.logout}</a>
        </li>,
    ];

    return (
        <Dropdown>
            <DropdownTrigger>
                <span className='anycomment-profile'>{user && user.data.display_name}</span> <Icon icon={faAngleDown} />
            </DropdownTrigger>
            <DropdownContent>
                <ul>{items}</ul>
            </DropdownContent>
        </Dropdown>
    );
}

ProfileDropdown.displayName = 'ProfileDropdown';
