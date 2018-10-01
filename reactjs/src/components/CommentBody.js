import React, {Fragment} from 'react';
import AnyCommentComponent from './AnyCommentComponent'
import reactStringReplace from 'react-string-replace';
import TweetEmbed from 'react-tweet-embed';
import sanitizeHtml from 'sanitize-html';
import {renderToString} from 'react-dom/server'

const MAX_BODY_HEIGHT = 250;

/**
 * CommentBody is rendering comment text.
 */
class CommentBody extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state = {
            isLong: false,
            hideAsLong: false,
        };

        this.toggleLongComment = this.toggleLongComment.bind(this);
    }

    /**
     * Process comment text and search for links.
     * @returns {*}
     */
    processContent() {
        let content = this.props.comment.content;

        let clean = sanitizeHtml(content, {
            allowedTags: ['p', 'a', 'ul', 'ol', 'blockquote', 'li', 'b', 'i', 'u', 'strong', 'em', 'br', 'img', 'figure', 'iframe'],
            allowedAttributes: {
                a: ['href', 'target'],
                blockquote: ['class'],
                img: ['class', 'src', 'alt'],
            },
            allowedIframeHostnames: ['twitter.com']
        });

        clean = this.processThirdParties(clean);

        return clean;
    };

    /**
     * Process third party apps, such as Tweets.
     * @param content
     * @returns String
     */
    processThirdParties(content) {
        const twitterRe = /https:\/\/twitter\.com\/.*\/([0-9]{1,})/gm,
            options = this.getOptions();

        if (!options.isShowTwitterEmbeds) {
            return content;
        }

        const matches = twitterRe.exec(content);


        if (matches !== null) {
            const twitter = renderToString(<TweetEmbed id={matches[1]}/>);
            console.log(matches);
            console.log(twitter);
            content = content.replace(matches[0], twitter);
        }

        return content;
    }

    /**
     * Toggle (show/hide) long comment.
     * @returns {*}
     */
    toggleLongComment() {
        if (!this.state.isLong) {
            return false;
        }

        return this.setState({hideAsLong: !this.state.hideAsLong});
    }

    /**
     * Check comment content and if too much, shorten it.
     */
    checkCommentLength = () => {
        const element = document.getElementById("comment-content-" + this.props.comment.id);

        if (element && element.clientHeight > MAX_BODY_HEIGHT) {
            this.setState({
                isLong: true,
                hideAsLong: true
            });
        }
    };

    componentDidMount() {
        this.checkCommentLength();
    }

    render() {
        const settings = this.getSettings();
        const bodyClasses = 'anycomment comment-single-body__text';

        return <div className={bodyClasses} onClick={() => this.toggleLongComment()}
                    id={"comment-content-" + this.props.comment.id}>
            <div className={"comment-single-body__text-content" + (this.state.hideAsLong ? ' comment-single-body__shortened' : '')}
                 style={this.state.hideAsLong ? {'height': MAX_BODY_HEIGHT} : null}
                 dangerouslySetInnerHTML={{__html: this.processContent()}}></div>
            {this.state.isLong ? <p className="comment-single-body__text-readmore"
                                    onClick={() => this.toggleLongComment()}>{this.state.hideAsLong ? settings.i18.read_more : settings.i18.show_less}</p> : ''}
        </div>
    }
}

export default CommentBody;