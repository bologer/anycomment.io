import React from 'react';
import Icon from '../../components/Icon';
import {faSortAmountDown, faSortAmountUp} from '@fortawesome/free-solid-svg-icons';
import {useSettings} from '~/hooks/setting';
import {useDispatch, useSelector} from 'react-redux';
import {StoreProps} from '~/store/reducers';
import {CommentReducerProps} from '~/core/comment/commentReducers';
import ReducerResolver from '~/components/ReducerResolver';
import {ListResponse} from '~/typings/ListResponse';
import {fetchCommentsSalient} from '~/core/comment/CommentActions';
import {CommentModel} from '~/typings/models/CommentModel';

/**
 * Renders header with elements to sort them asc./desc. order.
 *
 * @constructor
 */
export default function CommentSortHeader() {
    const settings = useSettings();
    const dispatch = useDispatch();
    const {list: comments, listFilter} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    function handleSorting() {
        const newOrder = listFilter && listFilter.order === 'asc' ? 'desc' : 'asc';
        dispatch(fetchCommentsSalient({postId: settings.postId, order: newOrder}));
    }

    function onFetched(response: ListResponse<CommentModel>) {
        const faSort = listFilter && listFilter.order === 'asc' ? faSortAmountDown : faSortAmountUp;

        const sortString =
            listFilter && listFilter.order === 'desc' ? settings.i18.sort_oldest : settings.i18.sort_newest;

        return (
            <div className='anycomment anycomment-summary'>
                <div
                    className='anycomment anycomment-summary-count'
                    dangerouslySetInnerHTML={{__html: `${settings.i18.comments_count}: ${response.meta.total_count}`}}
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

    return <ReducerResolver reducer={comments} fetched={onFetched} showLoader={false} />;
}

CommentSortHeader.displayName = 'CommentSortHeader';
