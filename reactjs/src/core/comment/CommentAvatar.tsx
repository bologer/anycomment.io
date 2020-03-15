import React from 'react';
import SocialIcon from '../../components/SocialIcon';
import CommentRating from '../../components/CommentRating';
import {CommentModel} from '~/typings/models/CommentModel';

export interface CommentAvatarProps {
    comment: CommentModel;
}

/**
 * CommentAvatar used to display avatar partial of a single comment.
 */
export default function CommentAvatar({comment}: CommentAvatarProps) {
    const svgIcon = comment.owner.is_social_login && comment.owner.social_type && (
        <SocialIcon slug={comment.owner.social_type} size={15} classes='comment-single-avatar__img-auth-type' />
    );

    return (
        <>
            <div className='anycomment comment-single-avatar'>
                <div
                    className='anycomment comment-single-avatar__img'
                    style={{backgroundImage: 'url(' + comment.avatar_url + ')'}}
                >
                    {comment.owner.is_social_login ? svgIcon : ''}
                </div>
            </div>
            <CommentRating comment={comment} />
        </>
    );
}

CommentAvatar.displayName = 'CommentAvatar';
