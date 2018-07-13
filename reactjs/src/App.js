import React from 'react';
import './css/theme-dark.css'
import CommentList from './components/CommentList'
import CommentCopyright from './components/CommentCopyright'
import AnyCommentComponent from "./components/AnyCommentComponent";

// const SettingsContext = React.createContext('anyCommentApiSettings' in window ? window.anyCommentApiSettings : null);

class App extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state.isLoaded = false;
        this.state.user = null;
        this.state.error = null;

        this.contentRef = React.createRef();
    }

    getUser = () => {
        if (this.state.settings == null) {
            return this.setState({
                error: "No settings defined"
            });
        }

        console.log(this.state.settings);

        const nonce = this.state.settings.nonce;

        return this.state.axios
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
        const {error, isLoaded, user, settings} = this.state;
        if (error) {
            return <div>{settings.i18.error}: {error}</div>;
        } else if (!isLoaded) {
            return <div>{settings.i18.loading}</div>;
        } else {
            return (
                <React.Fragment>
                    <CommentList contentRef={this.contentRef} user={user}/>
                    <CommentCopyright/>
                </React.Fragment>
            );
        }
    }
}

export default App;
