import React from 'react';
import SendCommentForm from "./SendCommentForm";
import AnyCommentComponent from "./AnyCommentComponent";

class SendComment extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state = {
            dropdown: 'none'
        };

        this.onDropdownClick = this.onDropdownClick.bind(this);
    }

    /**
     * Handle comment sorting.
     * @param e
     * @param order
     */
    handleCommentSort(e, order) {
        e.preventDefault();

        this.props.onSort(order)
    }

    onDropdownClick(event) {
        const el = document.getElementById('sort-dropdown');

        this.setState({dropdown: el.style.display === 'block' ? 'none' : 'block'});
    }

    render() {
        const settings = this.getSettings();

        return (
            <div id="anycomment anycomment-send-comment"
                 className={"anycomment-send-comment " + (!this.isGuest() ? 'send-comment-authorized' : '') + ""}>
                <div className="anycomment anycomment-send-comment-supheader">
                    <div className="anycomment anycomment-send-comment-supheader__count"
                         id="comment-count">{this.props.commentCountText}</div>
                    <div className="anycomment anycomment-send-comment-supheader__dropdown">
                        <div className="anycomment anycomment-send-comment-supheader__dropdown-header"
                             onClick={(e) => this.onDropdownClick(e)}>
                            {settings.i18.sort_by}
                        </div>
                        <div className="anycomment anycomment-send-comment-supheader__dropdown-list"
                             style={{display: this.state.dropdown}}
                             id="sort-dropdown">
                            <ul className="anycomment">
                                <li className="anycomment"
                                    onClick={(e) => this.handleCommentSort(e, 'desc')}>{settings.i18.sort_newest}</li>
                                <li className="anycomment"
                                    onClick={(e) => this.handleCommentSort(e, 'asc')}>{settings.i18.sort_oldest}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <SendCommentForm {...this.props} />
            </div>
        );
    }
}

export default SendComment;