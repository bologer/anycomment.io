import React from 'react';
import './css/theme-dark.css'
import CommentList from './components/CommentList'
import CommentCopyright from './components/CommentCopyright'
import AnyCommentComponent from "./components/AnyCommentComponent";
import 'iframe-resizer/js/iframeResizer.contentWindow'

class App extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state = {
            isLoaded: false,
            user: null,
            error: null
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
                error: "No settings defined"
            });
        }

        console.log(settings);

        const nonce = settings.nonce;

        return this.props.axios
            .get('/users/me', {
                headers: {"X-WP-Nonce": nonce}
            })
            .then(response => {
                console.log('user');
                console.log(response.data);
                this.setState({isLoaded: true, user: response.data});
            })
            .catch(error => {
                this.setState({isLoaded: true});
            });
    };

    componentDidMount() {
        this.getUser();
    };

    render() {
        const settings = this.props.settings;
        const {error, isLoaded, user} = this.state;

        if (error) {
            return <div>{settings.i18.error}: {error}</div>;
        } else if (!isLoaded) {
            return <div>{settings.i18.loading}</div>;
        } else {
            return (
                <React.Fragment>
                    <CommentList user={user}/>
                    <CommentCopyright/>
                </React.Fragment>
            );
        }
    }
}

export default App;
