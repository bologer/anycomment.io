import React from 'react';
import AnyCommentComponent from './AnyCommentComponent'

/**
 * CommentAttachments is checking comment content for images, links, videos and
 * generates attachments from was was found.
 */
class CommentAttachments extends AnyCommentComponent {

    processAttachments() {

        const options = this.getOptions();

        if (!options.isShowImageAttachments && !options.isShowVideoAttachments) {
            return null;
        }

        const comment = this.props.comment;

        let attachments = [];

        if (options.isShowVideoAttachments) {
            const videosRe = /(^(https?:\/\/)?((www\.)?youtube\.com|youtu\.?be|rutube.ru)\/.+$)/gi;
            const videoMatches = [...new Set(comment.content.match(videosRe))];

            if (videoMatches.length > 0) {
                for (let i = 0; i < videoMatches.length; i++) {
                    const isYoutube = videoMatches[i].indexOf('rutube.ru') === -1;
                    const videoName = isYoutube ? 'YouTube' : 'Rutube';
                    const className = 'anycomment comment-attachment comment-attachment__link ' + (isYoutube ? 'youtube' : 'rutube');
                    const style = isYoutube ?
                        {backgroundColor: '#f00', color: '#fff'} :
                        {
                            backgroundColor: '#0968c8', color: '#fff'
                        };

                    attachments.push(<li><a className={className}
                                            href={videoMatches[i]}
                                            style={style}
                                            target="_blank"
                                            rel="noreferrer noopener">{videoName}</a></li>);
                }
            }
        }

        if (options.isShowImageAttachments) {
            const imageRe = /((http(s?):)([/|.|\w|\s|-])*\.(?:jpg|jpeg|gif|png|svg))/gi;
            const imageMatches = [...new Set(comment.content.match(imageRe))];

            if (imageMatches.length > 0) {
                for (let k = 0; k < imageMatches.length; k++) {
                    attachments.push(<li><a className="anycomment comment-attachment comment-attachment__image"
                                            href={imageMatches[k]}
                                            target="_blank"
                                            rel="noreferrer noopener"
                                            style={{backgroundImage: 'url(' + imageMatches[k] + ')'}}></a></li>);
                }
            }
        }

        if (!attachments) {
            return null;
        }

        return <ul className="comment-attachments clearfix">{attachments}</ul>;
    }

    render() {
        return this.processAttachments();
    }
}

export default CommentAttachments;