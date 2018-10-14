import React from 'react';
import SendCommentForm from "./SendCommentForm";
import AnyCommentComponent from "./AnyCommentComponent";
import ProfileDropdown from './ProfileDropdown';

class SendComment extends AnyCommentComponent {
    /**
     * Handle comment sorting.
     * @param e
     * @param order
     */
    handleCommentSort(e, order) {
        e.preventDefault();

        this.props.onSort(order)
    }

    render() {
        return (
            <div id="anycomment anycomment-send-comment"
                 className="anycomment-send-comment">
                <div className="anycomment anycomment-send-comment-supheader">
                    <div className="anycomment anycomment-send-comment-supheader__count"
                         id="comment-count">{this.props.commentCountText}</div>
                    <div className="anycomment anycomment-send-comment-supheader__dropdown">
                        <ProfileDropdown onSort={this.props.onSort}/>
                    </div>
                </div>

                <SendCommentForm {...this.props} />
            </div>
        );
    }
}

export default SendComment;