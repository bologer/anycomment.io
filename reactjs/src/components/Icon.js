import React, {Component} from 'react'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'

export default class Icon extends Component {

    render() {
        let classes = 'anycomment anycomment-icon',
            customClass = this.props.class || '';

        if (customClass) {
            classes += ' ' + customClass;
        }

        return <FontAwesomeIcon
            icon={this.props.icon}
            className={classes}
            size={this.props.size || ''}
            color={this.props.color || ''}
            style={this.props.style || ''}
        />
    }
}