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
        this.state.per_page = 20;

        this.handleAddComment = this.handleAddComment.bind(this);
    }

    loadComments() {
        const self = this;
        const settings = this.state.settings;

        return this.state.axios
            .get('/comments', {
                params: {
                    post: settings.postId,
                    per_page: this.state.per_page
                },
                headers: {'X-WP-Nonce': settings.nonce}
            })
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
    handleAddComment(comment) {
        console.log('update state');
        console.log('comment');
        this.setState({
            comments: [comment, ...this.state.comments]
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

        return (
            <React.Fragment>
                <SendComment onSend={this.handleAddComment} user={this.props.user}/>
                <ul id="anycomment-load-container" className="anycomment-list">
                    {comments.length > 0 ?
                        comments.map(comment => (
                            <Comment comment={comment}/>
                        )) :
                        <li className="comment-single comment-no-comments">{settings.i18.no_comments}</li>}
                </ul>
            </React.Fragment>
        );
    }
}

export default CommentList;