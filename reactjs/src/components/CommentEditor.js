import React, {Fragment} from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import ReactQuill from 'react-quill';

class CommentEditor extends AnyCommentComponent {

    /**
     * Get list of enabled modules.
     */
    getModules = () => {
        const {isEditorOn, editorToolbarOptions} = this.getOptions();

        let toolbar = isEditorOn ? [] : false;

        if (isEditorOn && editorToolbarOptions && editorToolbarOptions !== []) {
            let genericsList = ['bold', 'italic', 'underline', 'blockquote'];

            genericsList = genericsList.filter((item) => {
                return editorToolbarOptions.indexOf(item) !== -1;
            });

            if (genericsList) {
                toolbar.push(genericsList);
            }

            let list = [];
            if (editorToolbarOptions.indexOf('ordered') !== -1) {
                list.push({'list': 'ordered'});
            }

            if (editorToolbarOptions.indexOf('bullet') !== -1) {
                list.push({'list': 'bullet'});
            }

            if (list) {
                toolbar.push(list);
            }


            let other = ['link', 'clean'];

            other = other.filter((item) => {
                return editorToolbarOptions.indexOf(item) !== -1;
            });

            if (other) {
                toolbar.push(other);
            }
        }


        return {
            toolbar: toolbar
        };
    };

    render() {
        const {commentHTML, handleEditorChange, settings} = this.props;

        let editorRef = this.props.editorRef;

        return (
            <ReactQuill
                className={'anycomment-quill-editor anycomment-lang-' + this.getLocale()}
                theme="bubble"
                value={commentHTML}
                placeholder={settings.i18.add_comment}
                ref={editorRef}
                modules={this.getModules()}
                onChange={handleEditorChange} />
        );
    }
}

export default CommentEditor;