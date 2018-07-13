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
            //     "postId": "8",
            //     "postUrl": "http://127.0.0.1:9090/index.php/anothe-rdjwqidq/",
            //     "nonce": "cf858f267a",
            //     "locale": "ru_RU",
            //     "options": {
            //         "limit": 10,
            //         "isCopyright": true,
            //         "socials": {
            //             "vkontakte": {
            //                 "url": "http://127.0.0.1:9090/index.php/wp-json/anycomment/v1/auth/vkontakte",
            //                 "label": "\u0412\u041a",
            //                 "visible": true
            //             },
            //             "twitter": {
            //                 "url": "http://127.0.0.1:9090/index.php/wp-json/anycomment/v1/auth/twitter",
            //                 "label": "\u0422\u0432\u0438\u0442\u0442\u0435\u0440",
            //                 "visible": false
            //             },
            //             "facebook": {
            //                 "url": "http://127.0.0.1:9090/index.php/wp-json/anycomment/v1/auth/facebook",
            //                 "label": "Facebook",
            //                 "visible": false
            //             },
            //             "google": {
            //                 "url": "http://127.0.0.1:9090/index.php/wp-json/anycomment/v1/auth/google",
            //                 "label": "Google",
            //                 "visible": false
            //             },
            //             "github": {
            //                 "url": "http://127.0.0.1:9090/index.php/wp-json/anycomment/v1/auth/github",
            //                 "label": "Github",
            //                 "visible": false
            //             },
            //             "odnoklassniki": {
            //                 "url": "http://127.0.0.1:9090/index.php/wp-json/anycomment/v1/auth/odnoklassniki",
            //                 "label": "\u041e\u0434\u043d\u043e\u043a\u043b\u0430\u0441\u0441\u043d\u0438\u043a\u0438",
            //                 "visible": false
            //             }
            //         }
            //     },
            //     "i18": {
            //         "error": "Error",
            //         "loading": "Loading...",
            //         "load_more": "\u0417\u0430\u0433\u0440\u0443\u0437\u0438\u0442\u044c \u0435\u0449\u0435",
            //         "button_send": "\u041e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c",
            //         "button_save": "\u0421\u043e\u0445\u0440\u0430\u043d\u0438\u0442\u044c",
            //         "button_reply": "\u041e\u0442\u0432\u0435\u0442\u0438\u0442\u044c",
            //         "sort_oldest": "\u0421\u0442\u0430\u0440\u044b\u0435",
            //         "sort_newest": "\u041d\u043e\u0432\u044b\u0435",
            //         "reply_to": "Reply to {name}",
            //         "add_comment": "\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0439...",
            //         "no_comments": "\u041f\u043e\u043a\u0430 \u0435\u0449\u0435 \u043d\u0435 \u0431\u044b\u043b\u043e \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432",
            //         "footer_copyright": "\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c AnyComment \u043d\u0430 \u0441\u0432\u043e\u0439 \u0441\u0430\u0439\u0442",
            //         "reply": "\u041e\u0442\u0432\u0435\u0442\u0438\u0442\u044c",
            //         "edit": "\u0418\u0437\u043c\u0435\u043d\u0438\u0442\u044c",
            //         "quick_login": "\u0411\u044b\u0441\u0442\u0440\u044b\u0439 \u0432\u0445\u043e\u0434"
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