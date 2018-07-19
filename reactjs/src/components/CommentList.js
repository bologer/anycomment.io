import React from 'react';
import Comment from './Comment'
import SendComment from './SendComment'
import AnyCommentComponent from "./AnyCommentComponent";
import toast from 'react-toastify';

/**
 * CommentList displays list of comments.
 */
class CommentList extends AnyCommentComponent {

    constructor(props) {
        super(props);

        const options = this.props.settings.options;

        this.state = {
            isError: false,
            isLoaded: false,

            commentCountText: null,
            comments: [],

            isLastPage: false,
            perPage: options.limit,
            offset: options.limit,
            orderBy: 'id',


            commentText: '',
            replyId: 0,
            editId: '',
        };

        this.state.order = 'desc';


        /**
         * Form states.
         * @type {string}
         */
        this.commentFieldRef = React.createRef();

        /**
         * Bindings
         */
        this.focusCommentField = this.focusCommentField.bind(this);
        this.loadComments = this.loadComments.bind(this);
        this.handleLoadMore = this.handleLoadMore.bind(this);
        this.handleAddComment = this.handleAddComment.bind(this);
        this.handleSort = this.handleSort.bind(this);

        this.handleCommentTextChange = this.handleCommentTextChange.bind(this);
        this.handleReplyIdChange = this.handleReplyIdChange.bind(this);
        this.handleEditIdChange = this.handleEditIdChange.bind(this);
    }

    /**
     * Focus on comment field.
     */
    focusCommentField() {
        this.commentFieldRef.current.focus();
    }

    /**
     * Handle comment text change.
     * @param text
     */
    handleCommentTextChange(text) {
        this.setState({
            commentText: text
        });
        this.focusCommentField();
    }

    /**
     * Handle reply ID change.
     * @param comment
     */
    handleReplyIdChange(comment) {
        this.setState({
            replyId: comment.id
        });

        this.focusCommentField();
    }

    /**
     * Handle edit ID change.
     * @param comment
     */
    handleEditIdChange(comment) {
        this.setState({
            editId: comment.id,
            commentText: comment.content
        });
        this.focusCommentField();
    }

    /**
     * Handle sort.
     * @param order
     */
    handleSort(order) {
        const self = this;
        this.setState({
            order: order,
        }, function () {
            self.loadComments();
        });
    }

    /**
     * Load comments.
     * @returns {Promise<T>}
     */
    loadComments() {
        const self = this;
        const settings = this.props.settings;

        const params = {
            post: settings.postId,
            parent: 0,
            perPage: this.state.perPage,
            order: this.state.order,
            order_by: this.state.orderBy,
        };

        return this.props.axios
            .get('/comments', {
                params: params,
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.setState({
                    commentCountText: response.data && response.data.length > 0 ?
                        response.data[0].meta.count_text :
                        '',
                    isLoaded: true,
                    isLastPage: !response.data || response.data.length < settings.options.limit,
                    comments: response.data,
                });
            })
            .catch(function (error) {
                self.setState({
                    isLoaded: true,
                    isError: true
                });

                if ('message' in error) {
                    toast(error.message, {type: toast.TYPE.ERROR});
                }
            });
    };

    /**
     * Handles load more comments.
     * @param e
     * @returns {*}
     */
    handleLoadMore(e) {
        e.preventDefault();

        if (this.state.isLastPage) {
            return false;
        }

        const self = this;
        const settings = this.props.settings;
        const limit = settings.options.limit;

        const params = {
            post: settings.postId,
            parent: 0,
            perPage: settings.options.limit,
            offset: this.state.offset,
            order: this.state.order,
            order_by: this.state.orderBy,
        };

        return this.props.axios
            .get('/comments', {
                params: params,
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.setState({
                    comments: [...self.state.comments, ...response.data],
                    offset: self.state.offset + limit,
                    isLastPage: !response.data || response.data.length < limit
                });
            })
            .catch(function (error) {
                self.setState({
                    isLoaded: true,
                    isError: true
                });
                if ('message' in error) {
                    toast(error.message, {type: toast.TYPE.ERROR});
                }
            });
    }

    /**
     * Add new comment to the list.
     *
     * @param comment
     */
    handleAddComment(comment) {
        this.setState({
            commentText: '',
            replyId: 0,
            editId: '',
        });

        this.loadComments();
        this.focusCommentField();
    };

    componentDidMount() {
        this.loadComments();
    }

    render() {
        const {isError, isLoaded, comments} = this.state;
        const settings = this.props.settings;
        const user = this.props.user;

        const sendComment = <SendComment
            commentFieldRef={this.commentFieldRef}
            commentText={this.state.commentText}
            commentCountText={this.state.commentCountText}
            replyId={this.state.replyId}
            editId={this.state.editId}
            onSort={this.handleSort}
            onCommentTextChange={this.handleCommentTextChange}
            onReplyIdChange={this.handleReplyIdChange}
            onEditIdChange={this.handleEditIdChange}
            onSend={this.handleAddComment}
            user={user}/>;

        if (isError) {
            return <div>{settings.i18.error_generic}</div>;
        } else if (!isLoaded) {
            return (
                <React.Fragment>
                    {sendComment}
                    <div>{settings.i18.loading}</div>
                </React.Fragment>
            )
        } else if (isLoaded && !comments.length) {
            return (
                <React.Fragment>
                    {sendComment}
                    <ul id="anycomment-load-container" className="anycomment-list">
                        <li className="comment-single comment-no-comments">{settings.i18.no_comments}</li>
                    </ul>
                </React.Fragment>
            )
        } else {
            return (
                <React.Fragment>
                    {sendComment}
                    <ul id="anycomment-load-container" className="anycomment-list">
                        {comments.map(comment => (
                            <Comment
                                changeReplyId={this.handleReplyIdChange}
                                changeEditId={this.handleEditIdChange}
                                key={comment.id}
                                user={user}
                                comment={comment}
                            />
                        ))}

                        {!this.state.isLastPage ?
                            <div className="comment-single-load-more">
                            <span onClick={(e) => this.handleLoadMore(e)}
                                  className="btn">{settings.i18.load_more}</span>
                            </div>
                            : ''}
                    </ul>
                </React.Fragment>
            )
        }
    }
}

export default CommentList;