import React, {useEffect, useState} from 'react';
import TweetEmbed from 'react-tweet-embed';
import {CommentModel} from '~/typings/models/CommentModel';
import {useOptions, useSettings} from '~/hooks/setting';

const MAX_BODY_HEIGHT = 250;

export interface CommentBodyProps {
    comment: CommentModel;
}

/**
 * CommentBody is rendering comment text.
 */
export default function CommentBody({comment}) {
    const settings = useSettings();
    const options = useOptions();
    const [isLong, setIsLong] = useState(false);
    const [hideAsLong, setHideAsLong] = useState(false);

    useEffect(() => {
        if (options.isReadMoreOn) {
            checkCommentLength();
        }
    }, []);

    /**
     * Process comment text and search for links.
     * @returns {*}
     */
    function processContent() {
        return comment.content;
    }

    /**
     * Process third party apps, such as Tweets.
     * @param content
     * @returns Array
     */
    function processThirdParties(content) {
        let embedsToRender: React.ReactElement[] = [];

        if (options.isShowTwitterEmbeds) {
            const twitterRe = /https:\/\/twitter\.com\/.*\/([0-9]+)/gm;
            const twitterMatches = twitterRe.exec(content);

            if (twitterMatches !== null) {
                embedsToRender.push(<TweetEmbed id={twitterMatches[1]} />);
            }
        }

        if (options.isShowVideoAttachments) {
            // YouTube
            const youTubeRe = /youtube\.com.*?(\?v=|\/embed\/)(.{11})|(youtu\.be\/(.{11}))/gm;
            const youTubeMatches = content.match(youTubeRe);
            let collectedIds: {[id: number]: number} = [];

            if (youTubeMatches && youTubeMatches.length > 0) {
                let i = 0;
                for (; i < youTubeMatches.length; i++) {
                    const youTubeVideoId = youTubeMatches[i].match(/.{11}$/)[0] || '';

                    // Should skip when id not found or when ID repeats
                    if (!youTubeVideoId || collectedIds[youTubeVideoId] !== undefined) {
                        continue;
                    }

                    collectedIds[youTubeVideoId] = youTubeVideoId;

                    embedsToRender.push(
                        <iframe
                            width='260'
                            src={'https://www.youtube.com/embed/' + youTubeVideoId}
                            frameBorder='0'
                            allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture'
                            className='comment-single-body__text-embeds--youtube'
                            allowFullScreen
                        />
                    );
                }
            }
        }

        return embedsToRender;
    }

    /**
     * Toggle (show/hide) long comment.
     * @returns {*}
     */
    function toggleLongComment(e) {
        e.preventDefault();

        if (isLong) {
            setHideAsLong(!hideAsLong);
        }
    }

    /**
     * Check comment content and if too much, shorten it.
     */
    function checkCommentLength() {
        const element = document.getElementById('comment-content-' + comment.id);

        if (element && element.clientHeight > MAX_BODY_HEIGHT) {
            setIsLong(true);
            setHideAsLong(true);
        }
    }

    const bodyClasses = 'anycomment comment-single-body__text';
    const cleanContent = processContent();
    const thirdParty = processThirdParties(cleanContent);

    return (
        <div className={bodyClasses} id={`comment-content-${comment.id}`}>
            <div
                className={'comment-single-body__text-content' + (hideAsLong ? ' comment-single-body__shortened' : '')}
                style={hideAsLong ? {height: MAX_BODY_HEIGHT} : undefined}
                dangerouslySetInnerHTML={{__html: cleanContent}}
            />
            <div className='comment-single-body__text-embeds'>{thirdParty}</div>
            {isLong ? (
                <p className='comment-single-body__text-readmore' onClick={toggleLongComment}>
                    {hideAsLong ? settings.i18.read_more : settings.i18.show_less}
                </p>
            ) : (
                ''
            )}
        </div>
    );
}

CommentBody.displayName = 'CommentBody';
