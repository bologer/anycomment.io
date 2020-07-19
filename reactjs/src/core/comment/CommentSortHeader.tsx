import React from 'react';
import Icon from '../../components/Icon';
import {faSortAmountDown, faSortAmountUp} from '@fortawesome/free-solid-svg-icons';
import {useConfig, useSettings} from '~/hooks/setting';
import {useDispatch, useSelector} from 'react-redux';
import {StoreProps} from '~/store/reducers';
import {CommentReducerProps} from '~/core/comment/commentReducers';
import {fetchCommentsSalient} from '~/core/comment/CommentActions';

/**
 * Renders header with elements to sort them asc./desc. order.
 *
 * @constructor
 */
export default function CommentSortHeader() {
    const settings = useSettings();
    const dispatch = useDispatch();
    const config = useConfig();
    const {listFilter, list: comments} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    /**
     * Handles sorting update.
     */
    function handleSorting() {
        const newOrder = listFilter && listFilter.order === 'asc' ? 'desc' : 'asc';
        const perPage = comments?.payload?.items?.length || settings.options.limit;
        dispatch(
            fetchCommentsSalient({
                postId: config.postId,
                offset: 0,
                perPage: perPage,
                order: newOrder,
            })
        );
    }

    const faSort = listFilter && listFilter.order === 'asc' ? faSortAmountDown : faSortAmountUp;

    const sortString = listFilter && listFilter.order === 'desc' ? settings.i18.sort_oldest : settings.i18.sort_newest;

    return (
        <div className='anycomment anycomment-summary'>
            <div
                className='anycomment anycomment-summary-count'
                dangerouslySetInnerHTML={{__html: `${settings.i18.comments_count} ${settings.commentCount}`}}
            />
            <div className='anycomment anycomment-summary-sort'>
                {settings.i18.sort_by}&nbsp;
                <span className='anycomment-link' onClick={handleSorting}>
                    {sortString}
                </span>
                &nbsp;
                <Icon icon={faSort} />
            </div>
        </div>
    );
}

CommentSortHeader.displayName = 'CommentSortHeader';
