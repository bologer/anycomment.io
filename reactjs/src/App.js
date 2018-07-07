import React, {Component} from 'react';
import './css/theme-dark.css'
import SendComment from './components/SendComment.js';
import Comments from "./components/Comments";


class App extends Component {
    constructor(props) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            items: []
        };
    }

    render() {
        return [
            <SendComment/>,
            <Comments/>
        ];
    }
}

export default App;
