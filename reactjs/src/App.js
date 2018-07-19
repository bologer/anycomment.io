import React from 'react';
import CommentList from './components/CommentList'
import CommentCopyright from './components/CommentCopyright'
import AnyCommentComponent from "./components/AnyCommentComponent";
import 'iframe-resizer/js/iframeResizer.contentWindow'
import './css/comments.css'
import {ToastContainer, toast} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

class App extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state = {
            isLoaded: false,
            user: null,
            isError: false
        };

        this.getUser = this.getUser.bind(this);
    }

    /**
     * Get current user.
     * @returns {*}
     */
    getUser() {
        const settings = this.props.settings;

        if (settings == null) {
            return this.setState({
                isError: true
            });
        }

        const nonce = settings.nonce;

        return this.props.axios
            .get('/users/me', {
                headers: {"X-WP-Nonce": nonce}
            })
            .then(response => {
                this.setState({isLoaded: true, user: response.data});
            })
            .catch(error => {
                this.setState({isLoaded: true});
                if ('message' in error) {
                    toast(error.message, {type: toast.TYPE.ERROR});
                }
            });
    };

    componentDidMount() {
        this.getUser();
    };

    render() {
        const settings = this.props.settings;
        const {isError, isLoaded, user} = this.state;

        if (isError) {
            return <div>{settings.i18.error_generic}</div>;
        } else if (!isLoaded) {
            return <div>{settings.i18.loading}</div>;
        } else {
            return (
                <div id="anycomment-root" className={'anycomment anycomment-' + this.props.settings.options.theme}>
                    <ToastContainer/>
                    <CommentList user={user}/>
                    <CommentCopyright/>
                </div>
            );
        }
    }
}

export default App;
