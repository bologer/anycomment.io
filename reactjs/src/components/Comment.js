import React from 'react';
import AnyCommentComponent from './AnyCommentComponent'
import CommentAvatar from './CommentAvatar';
import CommentHeader from './CommentHeader';
import CommentFooter from './CommentFooter';
import {toast} from 'react-toastify';
import reactStringReplace from 'react-string-replace';

/**
 * Comment is rendering single comment entry.
 */
class Comment extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            likesCount: props.comment.meta.likes_count,
            hasLike: props.comment.meta.has_like
        };

        this.onReply = this.onReply.bind(this);
        this.onLike = this.onLike.bind(this);
        this.onEdit = this.onEdit.bind(this);
        this.onDelete = this.onDelete.bind(this);
    }

    /**
     * Check whether comment text is too long.
     *
     * @returns {boolean}
     */
    isLongComment() {
        const comment = this.props.comment;

        if (!comment.content) {
            return false;
        }

        return comment.content.length > 250;
    }

    /**
     * On comment reply action.
     * @param e
     * @param comment
     */
    onReply(e, comment) {
        e.preventDefault();
        this.props.changeReplyId(comment);
    }

    onLike(e) {
        e.preventDefault();
        const settings = this.props.settings;
        const self = this;
        this.props.axios
            .request({
                method: 'post',
                url: '/likes',
                params: {
                    comment: this.props.comment.id,
                    post: this.props.comment.post,
                },
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.setState({
                    likesCount: response.data.total_count,
                    hasLike: response.data.has_like,
                });
            })
            .catch(function (error) {
                if ('message' in error) {
                    toast.error(error.message);
                }
            });
    }

    /**
     * On comment edit action.
     * @param e
     * @param comment
     */
    onEdit(e, comment) {
        e.preventDefault();
        this.props.changeEditId(comment);
    }

    /**
     * On comment delete.
     *
     * @param e
     * @param comment
     */
    onDelete(e, comment) {
        e.preventDefault();
        this.props.handleDelete(comment);
    }

    processContent(comment) {
        let content = comment.content;

        const linksRe = new RegExp(
            // protocol identifier
            "((?:(?:https?|ftp)://)" +
            // user:pass authentication
            "(?:\\S+(?::\\S*)?@)?" +
            "(?:" +
            // IP address exclusion
            // private & local networks
            "(?!(?:10|127)(?:\\.\\d{1,3}){3})" +
            "(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})" +
            "(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})" +
            // IP address dotted notation octets
            // excludes loopback network 0.0.0.0
            // excludes reserved space >= 224.0.0.0
            // excludes network & broacast addresses
            // (first & last IP address of each class)
            "(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])" +
            "(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}" +
            "(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))" +
            "|" +
            // host name
            "(?:(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)" +
            // domain name
            "(?:\\.(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)*" +
            // TLD identifier
            "(?:\\.(?:[a-z\\u00a1-\\uffff]{2,}))" +
            // sorry, ignore TLD ending with dot
            // "\\.?" +
            ")" +
            // port number
            "(?::\\d{2,5})?" +
            // resource path, excluding a trailing punctuation mark
            "(?:[/?#](?:\\S*[^\\s!\"'()*,-.:;<>?\\[\\]_`{|}~]|))?)"
            , "gi"
        );

        content = reactStringReplace(content, linksRe, (match, i) => (
            <a key={match + i} href={match} target="_blank" rel="noreferrer noopener">{match}</a>
        ));

        return content;
    };

    processAttachments(comment) {
        let attachments = [];

        const youtubeRe = /(^(https?:\/\/)?((www\.)?youtube\.com|youtu\.?be)\/.+$)/gi;
        const imageRe = /((http(s?):)([/|.|\w|\s|-])*\.(?:jpg|jpeg|gif|png|svg))/gi;

        const youtubeMatches = comment.content.match(youtubeRe);
        const imageMatches = comment.content.match(imageRe);

        if (youtubeMatches) {
            for (let i = 0; i < youtubeMatches.length; i++) {
                attachments.push(<li><a className="comment-attachment comment-attachment__link" href={youtubeMatches[i]}
                                        target="_blank"
                                        rel="noreferrer noopener"></a></li>);
            }
        }

        if (imageMatches) {
            for (let k = 0; k < imageMatches.length; k++) {
                attachments.push(<li><a className="comment-attachment comment-attachment__image" href={imageMatches[k]}
                                        target="_blank"
                                        rel="noreferrer noopener"
                                        style={{backgroundImage: 'url(' + imageMatches[k] + ')'}}></a></li>);
            }
        }

        if (!attachments) {
            return null;
        }

        return <ul className="comment-attachments clearfix">{attachments}</ul>;
    }

    render() {
        const comment = this.props.comment;

        const childComments = comment.children ?
            <div className="comment-single-replies">
                <ul className="anycomment-list anycomment-list-child">
                    {comment.children.map(childrenComment => (
                        <Comment
                            changeReplyId={this.props.changeReplyId}
                            changeEditId={this.props.changeEditId}
                            handleDelete={this.props.handleDelete}
                            key={childrenComment.id}
                            comment={childrenComment}/>
                    ))}
                </ul>
            </div> : '';

        const bodyClasses = 'comment-single-body__text' + (this.isLongComment() ? ' shortened' : '');

        return (
            <li key={comment.id} className="comment-single">

                <CommentAvatar comment={comment}/>

                <div className="comment-single-body">
                    <CommentHeader comment={comment}/>

                    <div className={bodyClasses}>
                        <p>{this.processContent(comment)}</p>
                    </div>

                    {this.processAttachments(comment)}

                    <CommentFooter
                        onEdit={this.onEdit}
                        onLike={this.onLike}
                        onReply={this.onReply}
                        onDelete={this.onDelete}
                        comment={comment}
                        likesCount={this.state.likesCount}
                        hasLike={this.state.hasLike}
                    />
                </div>
                {childComments}
            </li>
        )
    }


}

export default Comment;