import React from 'react';
import AnyCommentComponent from './AnyCommentComponent'
import reactStringReplace from 'react-string-replace';
import TweetEmbed from 'react-tweet-embed'

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
        const settings = this.getOptions(),
            newLineRe = /(\n)/gi;

        let content = this.props.comment.content;

        if (!settings.isLinkClickable) {
            return content;
        }

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


        // Replace links
        content = reactStringReplace(content, linksRe, (match, i) => this.processUrls(match, i));

        return content;
    };

    /**
     * Process URLs and other content, e.g. Tweets.
     * @param match
     * @param i
     * @returns String
     */
    processUrls(match, i) {


        const twitterRe = /https:\/\/twitter\.com\/.*\/([0-9]{1,})/gm,
            link = <a key={match + i} className="anycomment" href={match} target="_blank"
                      rel="noreferrer noopener">{match}</a>,
            options = this.getOptions();

        if (!options.isShowTwitterEmbeds) {
            return link;
        }

        const matches = twitterRe.exec(match);

        if (matches !== null) {
            return <TweetEmbed id={matches[1]}/>;
        }

        return link;
    }

    render() {
        const settings = this.getSettings();
        const bodyClasses = 'anycomment comment-single-body__text ' + (this.state.hideAsLong ? ' comment-single-body__shortened' : '');

        return <div className={bodyClasses} onClick={() => this.toggleLongComment()}>
            <div className="comment-single-body__text-content" dangerouslySetInnerHTML={{__html: this.processContent()}}></div>
            {this.isLongComment() ? <p className="comment-single-body__text-readmore"
                                       onClick={() => this.toggleLongComment()}>{this.state.hideAsLong ? settings.i18.read_more : settings.i18.show_less}</p> : ''}
        </div>
    }
}

export default CommentBody;