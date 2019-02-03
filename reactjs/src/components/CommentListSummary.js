import React from 'react';
import Icon from './Icon'
import {faSortAmountDown, faSortAmountUp} from '@fortawesome/free-solid-svg-icons'
import AnyCommentComponent from "./AnyCommentComponent";

export default class CommentListSummary extends AnyCommentComponent {
    /**
     * Handle comment sorting.
     */
    handleSorting = () => {
        const order = this.props.order === 'asc' ? 'desc' : 'asc';

        this.props.onSort(order)
    };

    render() {
        const settings = this.getSettings();

        const faSort = this.props.order === 'asc' ?
            faSortAmountDown :
            faSortAmountUp;

        const sortString = this.props.order === 'asc' ?
            settings.i18.sort_oldest :
            settings.i18.sort_newest;

        /**
         * @link https://github.com/bologer/anycomment.io/issues/370 for further information about commentCountText being set via dangerouslySetInnerHTML
         */
        return (
            <div className="anycomment anycomment-summary">
                <div className="anycomment anycomment-summary-count"
                     dangerouslySetInnerHTML={{__html: this.props.commentCountText}}></div>
                <div className="anycomment anycomment-summary-sort">
                    {settings.i18.sort_by}&nbsp;<span className="anycomment-link"
                                                      onClick={this.handleSorting}>{sortString}</span>&nbsp;
                    <Icon icon={faSort}/>
                </div>
            </div>
        );
    }
}