import React from 'react';
import Comment from './Comment'
import SendComment from './SendComment'
import AnyCommentComponent from "./AnyCommentComponent";

/**
 * CommentList displays list of comments.
 */
class CommentList extends AnyCommentComponent {

    constructor(props) {
        super(props);
        this.state.error = null;
        this.state.isLoaded = false;
        this.state.comments = [];
    }

    loadComments = () => {
        let self = this;

        return this.state.axios
            .get('/comments')
            .then(function (response) {
                self.setState({
                    isLoaded: true,
                    comments: response.data
                });
            })
            .catch(function (error) {
                console.log(error);
                self.setState({
                    error: error
                });
            })
            .then(function () {
            });
    };

    componentDidMount() {
        this.loadComments();
    }

    render() {
        const {error, isLoaded, comments} = this.state;
        const settings = this.state.settings;

        if (error) {
            return <div>{settings.i18.error}: {error}</div>;
        } else if (!isLoaded) {
            return <div>{settings.i18.loading}</div>;
        }

        return [
            <SendComment handleChange={this.loadComments} user={this.props.user}/>,
            <ul id="anycomment-load-container" className="anycomment-list">
                {comments.map(comment => (
                    <Comment comment={comment}/>
                ))}
            </ul>
        ];
    }
}

export default CommentList;