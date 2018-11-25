import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import Icon from './Icon'
import {faCaretUp, faCaretDown} from '@fortawesome/free-solid-svg-icons'

/**
 * Renders single comment rating.
 */
class CommentRating extends AnyCommentComponent {

    constructor(props) {
        super();

        this.state = {
            rating: props.comment.meta.rating,
            hasLike: props.comment.meta.has_like,
            hasDislike: props.comment.meta.has_dislike,
        }
    }

    /**
     * Handle upvote rating.
     *
     * @param e
     */
    handleRateUp = (e) => {
        e.preventDefault();

        this.handleRequest(1, () => {
            console.log('down done');
        });
    };

    /**
     * Handle downvote rating.
     *
     * @param e
     */
    handleRateDown = (e) => {
        e.preventDefault();

        this.handleRequest(0, () => {
            console.log('down done');
        });
    };

    /**
     * Handle main rating request.
     * @param type
     * @param callback
     */
    handleRequest = (type, callback) => {
        const settings = this.getSettings();
        const self = this;
        this.props.axios
            .request({
                method: 'post',
                url: '/likes',
                params: {
                    comment: this.props.comment.id,
                    post: this.props.comment.post,
                    type: type
                },
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.setState({
                    rating: response.data.rating,
                    hasLike: response.data.has_like,
                    hasDislike: response.data.has_dislike
                });
                callback(response);
            })
            .catch(function (error) {
                self.showError(error);
                callback(error);
            });
    };

    render() {

        const {rating, hasLike, hasDislike} = this.state;

        return (
            <div className="anycomment anycomment-comment-rating">
                <div className="anycomment anycomment-comment-rating__counter"
                     itemProp="upvoteCount">{rating}</div>
                <div className="anycomment anycomment-comment-rating__actions">
                    <div className="anycomment anycomment-comment-rating__actions--up"
                         onClick={(e) => this.handleRateUp(e)}>
                        <Icon icon={faCaretUp} style={hasLike ? {color: '#53AF4A'} : ''}/></div>
                    <div className="anycomment anycomment-comment-rating__actions--down"
                         onClick={(e) => this.handleRateDown(e)}>
                        <Icon icon={faCaretDown} style={hasDislike ? {color: '#DD4B39'} : ''}/></div>
                </div>
            </div>
        );
    }
}

export default CommentRating;