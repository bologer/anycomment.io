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

        const settings = this.state.settings;

        this.state.error = null;
        this.state.isLoaded = false;
        this.state.comments = [];
        this.state.perPage = settings.options.limit;
        this.state.isLastPage = false;
        this.state.order = 'desc';
        this.state.orderBy = 'id';

        this.loadComments = this.loadComments.bind(this);
        this.handleLoadMore = this.handleLoadMore.bind(this);
        this.handleAddComment = this.handleAddComment.bind(this);
    }

    loadComments() {
        const self = this;
        const settings = this.state.settings;
        const perPage = this.state.perPage;

        const params = {
            post: settings.postId,
            per_page: this.state.perPage,
            order: this.state.order,
            order_by: this.state.orderBy,
        };

        return this.state.axios
            .get('/comments', {
                params: params,
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.setState({
                    isLoaded: true,
                    comments: response.data,
                    isLastPage: response.data.length < perPage
                });

                console.log(self.state);
            })
            .catch(function (error, l, d) {
                console.log(error);
                console.log(l);
                console.log(d);
                self.setState({
                    isLoaded: true,
                    error: error.toString()
                });
            })
            .then(function () {
            });
    };

    /**
     * Handles load more comments.
     * @param e
     * @returns {boolean}
     */
    handleLoadMore(e) {
        e.preventDefault();

        if (this.state.isLastPage) {
            return false;
        }

        const perPage = this.state.settings.options.limit;

        this.setState({
            perPage: this.state.perPage + perPage
        });

        this.loadComments();
    }

    /**
     * Add new comment to the list.
     *
     * @param comment
     */
    handleAddComment(comment) {
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
        const user = this.props.user;

        if (error) {
            return <div>{settings.i18.error}: {error}</div>;
        }

        return (
            <React.Fragment>
                <SendComment contentRef={this.props.contentRef} onSend={this.handleAddComment} user={user}/>
                {isLoaded ?
                    <ul id="anycomment-load-container" className="anycomment-list">
                        {comments.length !== [] ?
                            comments.map(comment => (
                                <Comment contentRef={this.props.contentRef} key={comment.id} user={user}
                                         comment={comment}/>
                            )) :
                            <li className="comment-single comment-no-comments">{settings.i18.no_comments}</li>}

                        <div className="comment-single-load-more">
                            <span onClick={(e) => this.handleLoadMore(e)}
                                  className="btn">{settings.i18.load_more}</span>
                        </div>
                    </ul>
                    :
                    <div>{settings.i18.loading}</div>}
            </React.Fragment>
        );
    }
}

export default CommentList;