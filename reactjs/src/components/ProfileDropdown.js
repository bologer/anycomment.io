import React from 'react';
import Select, {components} from 'react-select';
import AnyCommentComponent from "./AnyCommentComponent";


const Placeholder = (props) => {
    return (
        <components.Placeholder {...props}/>
    );
};

const customStyles = {
    control: (base, state) => ({
        ...base,
        zIndex: 3,
    }),

};

export default class ProfileDropdown extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state = {
            selectedOption: null,
        };
    }

    /**
     * Handle comment sorting.
     * @param order
     */
    handleCommentSort(order) {
        this.props.onSort(order)
    }

    /**
     * Handle dropdown change.
     * @param selectedOption
     */
    handleChange = (selectedOption) => {
        this.setState({selectedOption});

        const settings = this.getSettings();

        switch (selectedOption.value) {
            case 'logout':
                window.location.href = settings.urls.logout.replace('&amp;', '&');
                break;
            case 'order_desc':
                this.handleCommentSort('desc');
                break;

            case 'order_asc':
                this.handleCommentSort('asc');
                break;
        }
    };


    render() {
        const {selectedOption} = this.state;

        const settings = this.getSettings();


        let placeholder = settings.i18.sorting;

        let options = [
            {value: 'order_desc', label: settings.i18.sort_newest},
            {value: 'order_asc', label: settings.i18.sort_oldest}
        ];

        if (!this.isGuest()) {
            const user = settings.user;

            placeholder = user.data.display_name;
            options.push({value: 'logout', label: settings.i18.logout});
        }

        return (
            <Select
                isSearchable={false}
                style={customStyles}
                value={selectedOption}
                components={{Placeholder}}
                placeholder={placeholder}
                onChange={this.handleChange}
                options={options}
                theme={(theme) => ({
                    ...theme,
                    borderRadius: 0,
                    border: 0,
                })}
            />
        );
    }
}