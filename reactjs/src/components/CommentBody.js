import React from 'react';
import AnyCommentComponent from './AnyCommentComponent'
import TweetEmbed from 'react-tweet-embed';

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
        return this.props.comment.content;
    };

    /**
     * Process third party apps, such as Tweets.
     * @param content
     * @returns Array
     */
    processThirdParties(content) {

        const options = this.getOptions();

        let embedsToRender = [];

        if (options.isShowTwitterEmbeds) {
            const twitterRe = /https:\/\/twitter\.com\/.*\/([0-9]{1,})/gm,
                twitterMatches = twitterRe.exec(content);

            if (twitterMatches !== null) {
                embedsToRender.push(<TweetEmbed id={twitterMatches[1]}/>);
            }
        }

        if (options.isShowVideoAttachments) {
            // YouTube
            const youTubeRe = /youtube\.com.*?(\?v=|\/embed\/)(.{11})|(youtu\.be\/(.{11}))/gm,
                youTubeMatches = content.match(youTubeRe),
                collectedIds = [];

            if (youTubeMatches && youTubeMatches.length > 0) {
                let i = 0;
                for (; i < youTubeMatches.length; i++) {
                    const youTubeVideoId = youTubeMatches[i].match(/.{11}$/)[0] || '';

                    // Should skip when id not found or when ID repeats
                    if (!youTubeVideoId || collectedIds[youTubeVideoId] !== undefined) {
                        continue;
                    }

                    collectedIds[youTubeVideoId] = youTubeVideoId;

                    embedsToRender.push(<iframe width="260" height="150"
                                                src={"https://www.youtube.com/embed/" + youTubeVideoId}
                                                frameBorder="0"
                                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                                className="comment-single-body__text-embeds--youtube"
                                                allowFullScreen></iframe>);
                }
            }
        }


        return embedsToRender;
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

        const options = this.getOptions();

        if (options.isReadMoreOn) {
            this.checkCommentLength();
        }
    }

    render() {
        const settings = this.getSettings(),
            bodyClasses = 'anycomment comment-single-body__text',
            cleanContent = this.processContent(),
            thirdParty = this.processThirdParties(cleanContent),
            {isLong, hideAsLong} = this.state;

        return <div className={bodyClasses} id={"comment-content-" + this.props.comment.id}>
            <div
                className={"comment-single-body__text-content" + (hideAsLong ? ' comment-single-body__shortened' : '')}
                style={hideAsLong ? {'height': MAX_BODY_HEIGHT} : null}
                dangerouslySetInnerHTML={{__html: cleanContent}}></div>
            <div className="comment-single-body__text-embeds">{thirdParty}</div>
            {isLong ? <p className="comment-single-body__text-readmore"
                         onClick={() => this.toggleLongComment()}>{hideAsLong ? settings.i18.read_more : settings.i18.show_less}</p> : ''}
        </div>
    }
}

export default CommentBody;