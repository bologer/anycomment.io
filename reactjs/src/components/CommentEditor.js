import React, {Fragment} from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import SendCommentFormBodyAvatar from './SendCommentFormBodyAvatar'
import {Editor} from 'react-draft-wysiwyg';

class CommentEditor extends AnyCommentComponent {

    editorOptions = () => {
        return {
            options: ['inline', 'blockType', 'list', 'link', 'image'],
            inline: {
                inDropdown: false,
                options: ['bold', 'italic', 'underline'],
            },
            blockType: {
                inDropdown: false,
                options: ['Blockquote'],
            },
            list: {
                inDropdown: false,
                options: ['unordered', 'ordered'],
            },
            link: {
                inDropdown: false,
                className: undefined,
                component: undefined,
                popupClassName: undefined,
                dropdownClassName: undefined,
                showOpenOptionOnHover: true,
                defaultTargetOption: '_self',
                options: ['link', 'unlink'],
            },
            image: {
                urlEnabled: true,
                uploadEnabled: false,
                alignmentEnabled: true,
                uploadCallback: undefined,
                previewImage: false,
                inputAccept: 'image/gif,image/jpeg,image/jpg,image/png,image/svg',
                alt: {present: false, mandatory: false},
                defaultSize: {
                    height: 'auto',
                    width: 'auto',
                },
            }
        };
    };

    render() {
        const {editorState} = this.props;


        return (
            <Fragment>
                <SendCommentFormBodyAvatar/>
                <Editor
                    placeholder={"123"}
                    editorState={editorState}
                    toolbar={this.editorOptions()}
                    wrapperClassName="anycomment-editor-wrapper"
                    toolbarClassName="anycomment-toolbar-wrapper"
                    editorClassName="anycomment-editor"
                    editorRef={this.props.domEditorRef}
                    localization={{
                        locale: this.getLocale()
                    }}
                    onEditorStateChange={this.props.handleEditorStateChange}
                />
            </Fragment>
        );
    }
}

export default CommentEditor;