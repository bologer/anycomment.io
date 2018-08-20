import React from 'react';
import Comment from './Comment'
import SendComment from './SendComment'
import AnyCommentComponent from "./AnyCommentComponent";
import {toast} from 'react-toastify';
import $ from 'jquery';

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
            commentCountText: null,
            comments: [],

            isLastPage: false,
            perPage: options.limit,
            offset: options.limit,
            orderBy: 'id',

            // Hold boolean whether current user just added comment or not
            // primarily used to track toast of added new comments
            isJustAdded: false,

            commentText: '',
            buttonText: settings.i18.button_send,
            isReply: false,
            replyId: 0,
            replyName: '',
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
        this.toggleHeightCommentField = this.toggleHeightCommentField.bind(this);
        this.loadComments = this.loadComments.bind(this);
        this.handleLoadMore = this.handleLoadMore.bind(this);
        this.handleAddComment = this.handleAddComment.bind(this);
        this.handleSort = this.handleSort.bind(this);

        this.handleCommentTextChange = this.handleCommentTextChange.bind(this);
        this.handleReplyIdChange = this.handleReplyIdChange.bind(this);
        this.handleReplyCancel = this.handleReplyCancel.bind(this);
        this.handleEditIdChange = this.handleEditIdChange.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.checkForAnchor = this.checkForAnchor.bind(this);
    }

    /**
     * Toggle expand/shrink animation of textarea.
     * @param type
     * @returns {boolean}
     */
    toggleHeightCommentField(type) {
        const min = 96;
        const max = 200;
        const height = (type === 'min' ? min : max);

        const el = $(this.commentFieldRef.current);

        if (type === 'max' && el.outerHeight() === max || type === 'min' && el.outerHeight() === 96) {
            return false;
        }

        el.animate({height: height}, 500);
    }

    /**
     * Focus on comment field.
     */
    focusCommentField(andExpand = false) {
        this.commentFieldRef.current.focus();

        if (andExpand) {
            this.toggleHeightCommentField('max');
        }
    }

    /**
     * Handle comment text change.
     * @param text
     */
    handleCommentTextChange(text) {
        this.setState({commentText: text});
        this.toggleHeightCommentField(text.trim() !== '' ? 'max' : 'min');
    }

    /**
     * Handle reply ID change.
     * @param comment
     */
    handleReplyIdChange(comment) {
        this.setState({
            isReply: true,
            replyName: comment.author_name,
            buttonText: this.props.settings.i18.button_reply,
            replyId: comment.id
        });

        this.focusCommentField(true);
    }

    /**
     * Handel cancel of the reply.
     */
    handleReplyCancel() {
        this.setState({
            isReply: false,
            replyName: '',
            buttonText: this.props.settings.i18.button_send,
            replyId: 0
        });
        this.toggleHeightCommentField('min');
    }

    /**
     * Handle edit ID change.
     * @param comment
     */
    handleEditIdChange(comment) {
        this.setState({
            isReply: false,
            replyName: '',
            editId: comment.id,
            buttonText: this.props.settings.i18.button_save,
            commentText: comment.content
        });
        this.focusCommentField(true);
    }

    /**
     * Handle comment deletion.
     *
     * @param comment
     */
    handleDelete(comment) {
        const self = this;
        const settings = this.props.settings;

        const params = {
            id: comment.id
        };

        this.props.axios
            .delete('/comments/' + comment.id, {
                params: params,
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.loadComments();
            })
            .catch(function (error) {
                if ('message' in error) {
                    toast.error(error.message);
                }
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

        const params = {
            post: settings.postId,
            parent: 0,
            per_page: this.state.perPage,
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
                    toast.error(error.message);
                }
            });
    };

    /**
     *
     * @returns {*|Promise<T>}
     */
    followNewComments() {
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
                    if (!self.state.isJustAdded && currentCount > stateCount) {
                        toast.success(settings.i18.new_comment_was_added, {
                            onClose: () => self.loadComments(),
                            autoClose: false,
                            position: toast.POSITION.TOP_CENTER,
                            draggable: false,
                        });
                    }

                    self.setState({isJustAdded: false});
                }
            })
            .catch(function (error) {
                if ('message' in error) {
                    toast.error(error.message);
                } else {
                    toast.error(error);
                }
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
                    toast.error(error.message);
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
            replyName: '',
            isReply: false,
            buttonText: this.props.settings.i18.button_send,
            replyId: 0,
            editId: '',
            isJustAdded: true,
        });

        this.loadComments();
        this.focusCommentField();
        this.toggleHeightCommentField('min');
    };


    componentDidMount() {
        this.loadComments();

        this.checkForAnchor();

        const {options} = this.props.settings;

        if (options.notifyOnNewComment) {
            const self = this,
                intervalInSeconds = (options.intervalCommentsCheck * 1000) || 5000;

            setInterval(function () {
                self.followNewComments();
            }, intervalInSeconds);
        }
    }

    /**
     * Checks for anchors in the link.
     * If there are some, it could be user who came from the
     * email and trying to read his reply.
     */
    checkForAnchor() {
        const self = this;
        const hash = window.location.hash;

        if (hash !== "" && /#comment-\d{1,11}/.test(hash)) {
            let interval = setInterval(function () {

                let commentElement = document.getElementById(hash.replace('#', ''));

                console.log(hash.replace('#', ''));

                if (!commentElement) {
                    console.log('need to load more, as we dont see comment yet');
                    self.handleLoadMore();
                } else {
                    if ('jQuery' in window) {

                        console.log(hash, /#comment-\d{1,11}/.test(hash));

                        const jq = window.jQuery;

                        jq([document.documentElement, document.body]).animate({
                            scrollTop: jq(hash).offset().top - 50
                        }, 500);

                        clearInterval(interval);
                    }
                }
            }, 1000);
        }


    };

    render() {
        const {isError, isLoaded, comments} = this.state;
        const settings = this.props.settings;

        const sendComment = <SendComment
            commentFieldRef={this.commentFieldRef}
            commentText={this.state.commentText}
            buttonText={this.state.buttonText}
            commentCountText={this.state.commentCountText}
            replyId={this.state.replyId}
            replyName={this.state.replyName}
            isReply={this.state.isReply}
            editId={this.state.editId}
            onSort={this.handleSort}
            onCommentTextChange={this.handleCommentTextChange}
            onReplyIdChange={this.handleReplyIdChange}
            onReplyCancel={this.handleReplyCancel}
            onEditIdChange={this.handleEditIdChange}
            onSend={this.handleAddComment}/>;

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
                    <ul id="anycomment-load-container" className="anycomment anycomment-list">
                        <li className="anycomment comment-single comment-no-comments">{settings.i18.no_comments}</li>
                    </ul>
                </React.Fragment>
            )
        } else {
            return (
                <React.Fragment>
                    {sendComment}
                    <ul id="anycomment-load-container" className="anycomment anycomment-list">
                        {comments.map(comment => (
                            <Comment
                                changeReplyId={this.handleReplyIdChange}
                                changeEditId={this.handleEditIdChange}
                                handleDelete={this.handleDelete}
                                key={comment.id}
                                comment={comment}
                            />
                        ))}

                        {!this.state.isLastPage ?
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