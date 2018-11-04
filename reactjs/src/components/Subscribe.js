import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent";
import Icon from "./Icon";
import {faTimes} from '@fortawesome/free-solid-svg-icons'


const LOCALE_STORE_KEY = 'anycomment-subscrine-closed';

class Subscribe extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            email: '',
            showForm: true,
        };
    }

    /**
     * Handle email change.
     * @param event
     */
    handleAuthorEmailChange = (event) => {
        this.setState({email: event.target.value});
    };

    /**
     * Handle hide form action.
     *
     * @param event
     */
    handleClose = (event) => {
        event.preventDefault();

        this.localStore(LOCALE_STORE_KEY, 1);
        this.setState({showForm: false});
    };

    /**
     * Handle form submit for guest and authorized clients.
     * @param e
     * @returns {boolean}
     */
    handleSubmit = (e) => {
        e.preventDefault();

        const self = this;
        const {email} = this.state;
        const {settings} = this.props;

        this.props.axios
            .request({
                method: 'post',
                url: '/subscribe',
                params: {
                    email: email,
                    post: settings.postId,
                },
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.setState({showForm: false});
                self.showSuccess(settings.i18.subscribed);
            })
            .catch(function (error) {
                self.showError(error);
            });
    };

    render() {
        const {email, showForm} = this.state;

        if (!showForm || this.getCurrentUser() === null || this.localGet(LOCALE_STORE_KEY)) {
            return (null);
        }

        return (
            <div className="anycomment anycomment-component anycomment-subscribe">
                <div className="anycomment-subscribe__close" onClick={this.handleClose}>
                    <Icon icon={faTimes}/>
                </div>
                <p>You may subscribe to comments for this post by providing your email address:</p>
                <form onSubmit={this.handleSubmit}>
                    <div className="anycomment anycomment-subscribe__email">
                        <input onChange={this.handleAuthorEmailChange} type="email" value={email}
                               required={true}/>
                    </div>
                    <div className="anycomment anycomment-subscribe__submit">
                        <input type="submit"
                               className="anycomment-btn"
                               value="Subscribe"/>
                    </div>
                </form>
            </div>
        );
    }
}

export default Subscribe;