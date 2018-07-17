import React, {Component} from 'react';
import axios from 'axios';

/**
 * Generic wrapper for React component.
 */
class AnyCommentComponent extends Component {
    static defaultProps = {
        ...Component.defaultProps,
        settings: 'anyCommentApiSettings' in window ? window.anyCommentApiSettings : null,
        axios: axios.create({
            baseURL: 'anyCommentApiSettings' in window ? window.anyCommentApiSettings.restUrl : '',
            timeout: 10000,
        }),
    };
}

export default AnyCommentComponent;