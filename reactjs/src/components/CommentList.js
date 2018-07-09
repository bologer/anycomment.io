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

        this.addComment = this.addComment.bind(this);
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

    /**
     * Add new comment to the list.
     *
     * @param comment
     */
    addComment(comment) {
        console.log('update state');
        console.log('comment');
        this.setState({
            comments: [comment, ...this.state.comments]
        });

        console.log('state is');
        console.log(this.state.comments);
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
            <SendComment addComment={this.addComment} user={this.props.user}/>,
            <ul id="anycomment-load-container" className="anycomment-list">
                {comments.map(comment => (
                    <Comment comment={comment}/>
                ))}
            </ul>
        ];
    }
}

export default CommentList;