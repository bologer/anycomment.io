import React from 'react'
import SendCommentFormBodyAvatar from './SendCommentFormBodyAvatar'
import AnyCommentComponent from "./AnyCommentComponent"

/**
 * Display comment field of the form.
 */
class SendCommentFormBody extends AnyCommentComponent {
    render() {
        const translations = this.props.settings.i18;

        return <div className="anycomment anycomment-send-comment-body-outliner">

            <SendCommentFormBodyAvatar user={this.props.user}/>

            <textarea name="content"
                      value={this.props.commentText}
                      required="required"
                      className="anycomment anycomment-send-comment-body-outliner__textfield"
                      placeholder={translations.add_comment}
                      onChange={this.props.handleContentChange}
                      ref={this.props.commentFieldRef}
            ></textarea>
        </div>;
    }
}

export default SendCommentFormBody;