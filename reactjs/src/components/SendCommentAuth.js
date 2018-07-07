import React, {Component} from 'react';

class SendCommentAuth extends Component {
    render() {
        if (!this.props.isLogged) {
            return null;
        }
        return [
            <div className="send-comment-body-outliner__avatar"
                 style={{backgroundImage: 'url(' + 123 + ')'}}></div>
        ];
    }
}

export default SendCommentAuth;