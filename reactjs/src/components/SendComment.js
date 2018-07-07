import React, {Component} from 'react';
import SendCommentForm from "./SendCommentForm";

class SendComment extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLogged: true,
            commentCount: 0,
            activeUser: null,
        };
    }

    processComment() {
        console.log('ready to process');
    }

    commentSort(sort) {
        console.log('sort by: ' + sort);
    }

    showSortDropdown() {
        //return jQuery('#sort-dropdown').toggle();
    }

    render() {
        return (
            <div id="anycomment-send-comment"
                 className={"send-comment " + (this.state.isLogged ? 'send-comment-authorized' : '') + ""}>
                <div className="send-comment-supheader">
                    <div className="send-comment-supheader__count"
                         id="comment-count">{this.state.commentCount}</div>
                    <div className="send-comment-supheader__dropdown">
                        <div className="send-comment-supheader__dropdown-header"
                             onClick={this.showSortDropdown()}>
                            Sort By
                        </div>
                        <div className="send-comment-supheader__dropdown-list" style={{display: 'none'}}
                             id="sort-dropdown">
                            <ul>
                                <li onClick={this.commentSort('new')}>Newest</li>
                                <li onClick={this.commentSort('old')}>Oldest</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <SendCommentForm isLogged={this.state.isLogged}/>
            </div>
        );
    }
}

export default SendComment;