import React from 'react';
import Icon from './Icon'
import {faSortAmountDown, faSortAmountUp} from '@fortawesome/free-solid-svg-icons'
import {useSettings} from "~/hooks/setting";
import {useDispatch, useSelector} from "react-redux";
import {StoreProps} from "~/store/reducers";
import {CommentReducerProps} from "~/core/comment/commentReducers";
import ReducerResolver from "~/components/ReducerResolver";
import {ListResponse} from "~/typings/ListResponse";
import {CommentItem} from "~/typings/models/CommentItem";
import {fetchComments} from "~/core/comment/CommentActions";

export default function CommentListSummary() {

    const settings = useSettings();

    const dispatch = useDispatch();
    const {list: comments, listFilter} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    function handleSorting() {
        dispatch(fetchComments({postId: settings.postId, order: listFilter.order === 'asc' ? 'desc' : 'asc'}))
    }

    function onFetched(response: ListResponse<CommentItem>) {
        const faSort = listFilter.order === 'asc' ?
            faSortAmountDown :
            faSortAmountUp;

        const sortString = listFilter.order === 'asc' ?
            settings.i18.sort_oldest :
            settings.i18.sort_newest;

        return (
            <div className="anycomment anycomment-summary">
                <div className="anycomment anycomment-summary-count"
                     dangerouslySetInnerHTML={{__html: 'Комментариев: ' + response.meta.total_count}}></div>
                <div className="anycomment anycomment-summary-sort">
                    {settings.i18.sort_by}&nbsp;<span className="anycomment-link"
                                                      onClick={handleSorting}>{sortString}</span>&nbsp;
                    <Icon icon={faSort} />
                </div>
            </div>
        );
    }

    return (
        <ReducerResolver reducer={comments} fetched={onFetched} showLoader={false} />
    )
}