import React, {Component} from 'react';
import axios from 'axios';
import TimeAgo from 'react-timeago'
import frenchStrings from 'react-timeago/lib/language-strings/ru'
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter'

class Comments extends Component {
    constructor(props) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            items: []
        };
    }

    loadComments = () => {
        let self = this;

        return axios
            .get('http://127.0.0.1:9090/wp-json/anycomment/v1/comments')
            .then(function (response) {
                // handle success
                console.log(response);
                self.setState({
                    isLoaded: true,
                    items: response.data
                });
            })
            .catch(function (error) {
                // handle error
                console.log('err');
                console.log(error);
                self.setState({
                    error: error
                });
            })
            .then(function () {
                // always executed
            });
    };

    componentDidMount() {
        this.loadComments();
    }

    render() {
        const formatter = buildFormatter(frenchStrings);
        const {error, isLoaded, items} = this.state;
        if (error) {
            return <div>Error: {error.message}</div>;
        } else if (!isLoaded) {
            return <div>Loading...</div>;
        } else {
            return (
                <ul id="anycomment-load-container" className="anycomment-list">
                    {items.map(item => (
                        <li data-comment-id={item.id} className="comment-single">

                            <div className="comment-single-avatar" data-author-id="2">
                                <div className="comment-single-avatar__img"
                                     style={{backgroundImage: 'url(' + item.avatar_url + ')'}}>
                                </div>
                            </div>

                            <div className="comment-single-body">
                                <header className="comment-single-body-header" data-author-id="2">
                                    <div className="comment-single-body-header__author">{item.author_name}</div>
                                    <TimeAgo className="comment-single-body-header__date timeago-date-time"
                                             date={item.date} formatter={formatter}/>
                                </header>

                                <div className="comment-single-body__text">
                                    <p>{item.content}</p>
                                </div>

                            </div>

                        </li>
                    ))}
                </ul>
            );
        }
    }
}

export default Comments;
