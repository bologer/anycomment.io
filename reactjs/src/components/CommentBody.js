import React from 'react';
import AnyCommentComponent from './AnyCommentComponent'
import reactStringReplace from 'react-string-replace';

/**
 * CommentBody is rendering comment text.
 */
class CommentBody extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state = {
            hideAsLong: false,
        };

        this.isLongComment = this.isLongComment.bind(this);
        this.toggleLongComment = this.toggleLongComment.bind(this);
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
     * Toggle (show/hide) long comment.
     * @returns {*}
     */
    toggleLongComment() {
        if (!this.isLongComment()) {
            return false;
        }

        return this.setState({hideAsLong: !this.state.hideAsLong});
    }

    componentDidMount() {
        if (this.isLongComment()) {
            this.setState({hideAsLong: true})
        }
    }

    /**
     * Process comment text and search for links.
     * @returns {*}
     */
    processContent() {
        let content = this.props.comment.content;

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

    render() {
        const bodyClasses = 'comment-single-body__text ' + (this.state.hideAsLong ? ' shortened' : '');

        return <div className={bodyClasses} onClick={() => this.toggleLongComment()}>
            <p>{this.processContent()}</p>
        </div>
    }
}

export default CommentBody;