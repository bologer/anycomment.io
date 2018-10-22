import React, {Component} from 'react'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'

export default class Icon extends Component {

    render() {
        const classes = 'anycomment anycomment-icon';

        return <FontAwesomeIcon
            icon={this.props.icon}
            className={classes}
            size={this.props.size || ''}
            color={this.props.color || ''}
            style={this.props.style || ''}
        />
    }
}