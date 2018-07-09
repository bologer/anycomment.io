import React from 'react';
import axios from 'axios';

/**
 * Generic wrapper for React component.
 */
class AnyCommentComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            settings: 'anyCommentApiSettings' in window ? window.anyCommentApiSettings : null,
            // settings: {
            //     "postId": "1",
            //     "nonce": "bd753618fc",
            //     "options": {"limit": 10},
            //     "i18": {
            //         "error": "Error",
            //         "loading": "Loading11...",
            //         "button_send": "\u041e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c",
            //         "button_save": "\u0421\u043e\u0445\u0440\u0430\u043d\u0438\u0442\u044c",
            //         "button_reply": "\u041e\u0442\u0432\u0435\u0442\u0438\u0442\u044c",
            //         "sort_oldest": "\u0421\u0442\u0430\u0440\u044b\u0435",
            //         "sort_newest": "\u041d\u043e\u0432\u044b\u0435",
            //         "reply_to": "Reply to {name}",
            //         "add_comment": "\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0439..."
            //     }
            // },
            axios: axios.create({
                baseURL: 'http://127.0.0.1:9090/wp-json/anycomment/v1',
                timeout: 3000,
            }),
        };
    }
}

export default AnyCommentComponent;