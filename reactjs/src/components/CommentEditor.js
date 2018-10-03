import React, {Fragment} from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import SendCommentFormBodyAvatar from './SendCommentFormBodyAvatar'
import ReactQuill from 'react-quill';

class CommentEditor extends AnyCommentComponent {

    constructor() {
        super();

        this.modules = {
            toolbar: [
                ['bold', 'italic', 'underline', 'blockquote'],
                [{'list': 'ordered'}, {'list': 'bullet'}],
                ['link', 'image'],
                ['clean'],
            ],
        };

        this.formats = [
            'bold', 'italic', 'underline', 'blockquote',
            'list', 'bullet',
            'link', 'image', 'video'
        ]
    }

    render() {
        const {commentHTML, handleEditorChange, settings} = this.props;

        let editorRef = this.props.editorRef;

        return (
            <Fragment>
                <SendCommentFormBodyAvatar/>
                <ReactQuill value={commentHTML}
                            placeholder={settings.i18.add_comment}
                            ref={(el) => {
                                editorRef = el
                            }}
                            modules={this.modules}
                            onChange={handleEditorChange}/>
            </Fragment>
        );
    }
}

export default CommentEditor;