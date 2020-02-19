import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import Dropdown from 'react-simple-dropdown';
import DropdownTrigger from 'react-simple-dropdown/lib/components/dropdown-trigger';
import DropdownContent from 'react-simple-dropdown/lib/components/dropdown-content';
import Icon from "./Icon";
import {faAngleDown} from '@fortawesome/free-solid-svg-icons'

export default class ProfileDropdown extends AnyCommentComponent {
    /**
     * Handle dropdown change.
     * @param e
     * @param selectedOption
     */
    handleChange = (e, selectedOption) => {
        e.preventDefault();

        const settings = this.getSettings();

        switch (selectedOption) {
            case 'logout':
                window.location.href = settings.urls.logout.replace('&amp;', '&');
                break;
            default:
        }
    };

    render() {

        const settings = this.getSettings(),
            user = settings.user;

        if (this.isGuest()) {
            return <div>{settings.i18.guest}</div>;
        }

        let items = [
            <li key={0} onClick={(e) => this.handleChange(e, 'logout')}><a href="">{settings.i18.logout}</a></li>
        ];

        return (
            <Dropdown>
                <DropdownTrigger>
                    <span className="anycomment-profile">{user.data.display_name}</span> <Icon
                    icon={faAngleDown}/></DropdownTrigger>
                <DropdownContent>
                    <ul>{items}</ul>
                </DropdownContent>
            </Dropdown>
        );
    }
}