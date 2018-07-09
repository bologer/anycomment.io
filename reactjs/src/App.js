import React from 'react';
import './css/theme-dark.css'
import CommentList from './components/CommentList'
import AnyCommentComponent from "./components/AnyCommentComponent";

// const SettingsContext = React.createContext('anyCommentApiSettings' in window ? window.anyCommentApiSettings : null);

class App extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state.isLoaded = false;
        this.state.user = null;
        this.state.error = null;
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
                console.log(error);
            });
    };

    componentDidMount() {
        this.getUser();
    };

    render() {
        const {error, isLoaded, user} = this.state;
        if (error) {
            return <div>Error: {error}</div>;
        } else if (!isLoaded) {
            return <div>Loading...</div>;
        } else {
            return (
                <React.Fragment>
                    <CommentList user={this.state.user}/>
                </React.Fragment>
            );
        }
    }
}

export default App;
