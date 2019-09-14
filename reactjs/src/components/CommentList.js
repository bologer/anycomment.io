import React from 'react';
import Comment from './Comment'
import SendComment from './SendComment'
import AnyCommentComponent from "./AnyCommentComponent";
import {toast} from 'react-toastify';
import CommentListSummary from "./CommentListSummary";
import Subscribe from './Subscribe'

/**
 * CommentList displays list of comments.
 */
class CommentList extends AnyCommentComponent {

    constructor(props) {
        super(props);

        const settings = this.props.settings;
        const options = settings.options;

        this.state = {
            isError: false,
            isLoaded: false,

            commentCount: parseInt(settings.commentCount, 10),
            commentCountText: '',
            comments: [],

            isLastPage: false,
            perPage: options.limit,
            offset: options.limit,
            orderBy: 'id',

            // Hold boolean whether current user just added comment or not
            // primarily used to track toast of added new comments
            isJustAdded: false,

            action: '',
            comment: '',

            order: settings.options.sort_order
        };

        /**
         * Bindings
         */
        this.loadComments = this.loadComments.bind(this);
        this.handleLoadMore = this.handleLoadMore.bind(this);
        this.handleSort = this.handleSort.bind(this);

        this.handleDelete = this.handleDelete.bind(this);
        this.checkForAnchor = this.checkForAnchor.bind(this);
    }

    /**
     * Handle comment deletion.
     *
     * @param comment
     */
    handleDelete(comment) {
        const self = this;
        const settings = this.props.settings;

        let headers = {};

        if (settings.nonce) {
            headers = {'X-WP-Nonce': settings.nonce};
        }

        this.props.axios({
            method: 'POST',
            url: '/comments/delete/' + comment.id,
            headers: headers
        }).then(function () {
            self.loadComments();
        }).catch(function (error) {
            self.showError(error);
        });
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
        const timestamp = new Date().getTime();

        const params = {
            post: settings.postId,
            parent: 0,
            per_page: this.state.perPage,
            order: this.state.order,
            order_by: this.state.orderBy,
            rnd: timestamp
        };

        let headers = {};

        if (settings.nonce) {
            headers = {'X-WP-Nonce': settings.nonce};
        }

        return this.props.axios
            .get('/comments', {
                params: params,
                headers: headers
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
                self.showError(error);
            });
    };

    /**
     * Follow new comments to load them once there is something new.
     *
     * Notice: invocation of them will be skipped in case user is out of
     * active tab. E.g. went to another tab or so as this may produce unwanted
     * requests.
     *
     * @returns {*|Promise<T>}
     */
    followNewComments() {

        // Drop request if user is not active in the tab
        if (document.hidden) {
            return false;
        }

        const self = this;
        const settings = this.props.settings;

        return this.props.axios
            .get('/comments/count', {
                params: {post: settings.postId},
            })
            .then(function (response) {

                const stateCount = parseInt(self.state.commentCount, 10);
                const currentCount = parseInt(response.data, 10);

                if (currentCount &&
                    currentCount !== 0 &&
                    stateCount !== currentCount) {

                    self.setState({
                        commentCount: currentCount
                    });

                    // Show toast only if new comment was added, not deleted or
                    // something like this
                    const shouldReload = !self.state.isJustAdded && (currentCount > stateCount);

                    if (shouldReload) {
                        self.loadComments();
                        self.showSuccess(settings.i18.new_comment_was_added, {
                            autoClose: true,
                            position: toast.POSITION.TOP_CENTER,
                            draggable: false,
                        });
                    }

                    self.setState({isJustAdded: false});
                }
            })
            .catch(function (error) {
                self.showError(error);
            });
    };

    /**
     * Handles load more comments.
     * @returns {*}
     */
    handleLoadMore() {
        if (this.state.isLastPage) {
            return false;
        }

        const self = this;
        const settings = this.props.settings;
        const limit = settings.options.limit;

        const params = {
            post: settings.postId,
            parent: 0,
            per_page: settings.options.limit,
            offset: this.state.offset,
            order: this.state.order,
            order_by: this.state.orderBy,
        };

        let headers = {};

        if (settings.nonce) {
            headers = {'X-WP-Nonce': settings.nonce};
        }

        return this.props.axios
            .get('/comments', {
                params: params,
                headers: headers
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
                self.showError(error);
            });
    }

    /**
     * Checks for anchors in the link.
     * If there are some, it could be user who came from the
     * email and trying to read his reply.
     */
    checkForAnchor() {
        const self = this;
        const hash = window.location.hash;

        if (this.hasSpecificCommentAnchor()) {
            const interval = setInterval(function () {

                const element = document.getElementById(hash.replace('#', ''));

                if (!element) {
                    self.handleLoadMore();
                } else {
                    self.moveToCommentAndHighlight(hash);
                    clearInterval(interval);
                }
            }, 1000);
        }
    };

    componentDidMount() {
        this.loadComments();

        this.checkForAnchor();

        const options = this.getOptions();

        if (options.notifyOnNewComment) {
            const self = this,
                intervalInSeconds = (options.intervalCommentsCheck * 1000) || 5000;

            setInterval(function () {
                self.followNewComments();
            }, intervalInSeconds);
        }
    }

    /**
     * Trigger whether comment was just added by user, to investigate whether comment was
     * added by current user or someone else.
     */
    handleJustAdded = () => {
        this.setState({isJustAdded: true});
    };

    /**
     * Handle action unsetting.
     * Unset any previously set action.
     */
    handleUnsetAction = () => {
        this.setState({
            action: '',
            comment: ''
        });
    };

    render() {
        const {isError, action, comment, isLoaded, comments, isLastPage} = this.state;
        const settings = this.props.settings;

        const sendComment = <SendComment
            action={action}
            comment={comment}
            handleUnsetAction={this.handleUnsetAction}
            handleJustAdded={this.handleJustAdded}
            loadComments={this.loadComments}
        />;

        const commentListSummary = <CommentListSummary
            commentCountText={this.state.commentCountText}
            order={this.state.order}
            onSort={this.handleSort}/>;

        const subscription = <Subscribe/>;

        if (isError) {
            return <React.Fragment>
                {sendComment}
                {commentListSummary}
                <div>{settings.i18.error_generic}</div>
            </React.Fragment>;
        } else if (!isLoaded) {
            return (
                <React.Fragment>
                    {sendComment}
                    {commentListSummary}
                    <div>{settings.i18.loading}</div>
                </React.Fragment>
            )
        } else if (isLoaded && !comments.length) {
            return (
                <React.Fragment>
                    {sendComment}
                    {commentListSummary}
                    {subscription}
                    <ul id="anycomment-load-container" className="anycomment anycomment-list">
                        <li className="anycomment comment-single comment-no-comments">{settings.i18.no_comments}</li>
                    </ul>
                </React.Fragment>
            )
        } else {
            return (
                <React.Fragment>
                    {sendComment}
                    {commentListSummary}
                    {subscription}
                    <ul id="anycomment-load-container" className="anycomment anycomment-list">
                        {comments.map(comment => (
                            <Comment
                                handleDelete={this.handleDelete}
                                handleUnsetAction={this.handleUnsetAction}
                                handleJustAdded={this.handleJustAdded}
                                loadComments={this.loadComments}
                                key={comment.id}
                                comment={comment}
                            />
                        ))}

                        {!isLastPage ?
                            <div className="anycomment comment-single-load-more">
                                <span onClick={(e) => this.handleLoadMore(e)}
                                      className="anycomment anycomment-btn">{settings.i18.load_more}</span>
                            </div>
                            : ''}
                    </ul>
                </React.Fragment>
            )
        }
    }
}

export default CommentList;