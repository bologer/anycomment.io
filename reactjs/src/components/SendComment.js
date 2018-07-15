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
        const settings = this.props.settings;

        return (
            <div id="anycomment-send-comment"
                 className={"send-comment " + (this.props.user ? 'send-comment-authorized' : '') + ""}>
                <div className="send-comment-supheader">
                    <div className="send-comment-supheader__count"
                         id="comment-count">{this.props.commentCountText}</div>
                    <div className="send-comment-supheader__dropdown">
                        <div className="send-comment-supheader__dropdown-header"
                             onClick={(e) => this.onDropdownClick(e)}>
                            {settings.i18.sort_by}
                        </div>
                        <div className="send-comment-supheader__dropdown-list" style={{display: this.state.dropdown}}
                             id="sort-dropdown">
                            <ul>
                                <li onClick={(e) => this.handleCommentSort(e, 'desc')}>{settings.i18.sort_newest}</li>
                                <li onClick={(e) => this.handleCommentSort(e, 'asc')}>{settings.i18.sort_oldest}</li>
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