import React from 'react';
import SendCommentForm from "./SendCommentForm";
import AnyCommentComponent from "./AnyCommentComponent";

class SendComment extends AnyCommentComponent {
    // constructor(props) {
    //     super(props);
    // }

    processComment() {
        console.log('ready to process');
    }

    commentSort(sort) {
        console.log('sort by: ' + sort);

    }

    showSortDropdown() {
        // return jQuery('#sort-dropdown').toggle();
    }

    render() {
        return (
            <div id="anycomment-send-comment"
                 className={"send-comment " + (this.props.user ? 'send-comment-authorized' : '') + ""}>
                <div className="send-comment-supheader">
                    <div className="send-comment-supheader__count"
                         id="comment-count">{this.props.commentCount}</div>
                    <div className="send-comment-supheader__dropdown">
                        <div className="send-comment-supheader__dropdown-header"
                             onClick={this.showSortDropdown}>
                            Sort By
                        </div>
                        <div className="send-comment-supheader__dropdown-list" style={{display: 'none'}}
                             id="sort-dropdown">
                            <ul>
                                <li onClick={() => this.commentSort('new')}>Newest</li>
                                <li onClick={() => this.commentSort('old')}>Oldest</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <SendCommentForm handleChange={this.props.handleChange} user={this.props.user}/>
            </div>
        );
    }
}

export default SendComment;