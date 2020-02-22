import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import Dropzone from 'react-dropzone';
import {toast} from 'react-toastify';
import CommentAttachments from './CommentAttachments';
import Icon from "./Icon";
import {faPaperclip} from '@fortawesome/free-solid-svg-icons';
import Editor from './Editor';

/**
 * Display comment field of the form.
 */
class SendCommentFormBody extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            dropzoneActive: false,
        };

        this.editorRef = React.createRef();

        this.onDragEnter = this.onDragEnter.bind(this);
        this.onDragLeave = this.onDragLeave.bind(this);
        this.onDrop = this.onDrop.bind(this);
    }

    onDragEnter() {
        this.setState({
            dropzoneActive: true
        });
    }

    onDragLeave() {
        this.setState({
            dropzoneActive: false
        });
    }

    /**
     * On files drop.
     * @param files
     */
    onDrop(files) {
        this.processFiles(files);
    }

    /**
     * Process dropped files.
     * @param files
     */
    processFiles(files) {
        const settings = this.getSettings(),
            options = settings.options;

        if (!files || files.length === 0) {
            toast.error(settings.i18.file_not_selected_or_extension);
            return false;
        }

        if (files.length > options.fileLimit) {
            toast.error(settings.i18.file_limit);
            this.setState({dropzoneActive: false});
            return false;
        }

        const filesToUpload = new FormData();

        files.forEach((file, i) => {
            filesToUpload.append(i, file, file.name);
        });

        filesToUpload.append('post', settings.postId);

        this.setState({dropzoneActive: false});
        this.uploadFiles(filesToUpload);
    }

    /**
     * Upload files to server.
     *
     * @param filesToUpload
     */
    uploadFiles(filesToUpload) {
        const self = this,
            settings = this.getSettings(),
            toastId = toast(settings.i18.file_upload_in_progress, {autoClose: false});

        let headers = {};

        headers['Content-Type'] = `multipart/form-data; boundary=${filesToUpload._boundary}`;

        if (settings.nonce) {
            headers['X-WP-Nonce'] = settings.nonce;
        }

        self.props.axios
            .post('/documents',
                filesToUpload,
                {
                    headers: headers,
                    timeout: 30000,
                })
            .then(function(response) {

                const files = response.data.files,
                    attachments = self.props.attachments;

                if (!attachments || !attachments.length) {
                    self.props.handleAttachmentChange(files);
                } else {
                    let newAttachments = attachments;
                    response.data.files.forEach((item) => {
                        newAttachments.push(item);
                    });
                    self.props.handleAttachmentChange(newAttachments);
                }

                toast.update(toastId, {
                    render: settings.i18.file_uploaded,
                    type: toast.TYPE.SUCCESS,
                    autoClose: 1500,
                    className: 'rotateY animated'
                });
            })
            .catch(function(error) {
                self.showError(error, {
                    autoClose: 1500
                }, toastId);
            });
    }

    /**
     * Check whether user can upload files.
     *
     * @returns {boolean}
     */
    canUpload = () => {
        const options = this.getOptions();

        if (options.isFileUploadAllowed && !this.isGuest()) {
            return true;
        } else if (options.isFileUploadAllowed && this.isGuest() && options.isGuestCanUpload) {
            return true;
        }

        return false;
    };

    handleEditorRef = (editorRef) => {
        this.editorRef = editorRef;
        this.editorRef.focus();
    };

    render() {
        const settings = this.getSettings();
        const options = settings.options;
        const {dropzoneActive} = this.state;
        const {attachments, comment} = this.props;

        let dropzoneRef;

        const canUpload = this.canUpload();

        const outliner = <div
            className={"anycomment anycomment-form-body-outliner" + (dropzoneActive ? ' anycomment-form-body-outliner-dropzone-active' : '')}>

            {canUpload ?
                <div className="anycomment anycomment-form-body-outliner__select-file"
                     title={settings.i18.upload_file}
                     onClick={() => {
                         dropzoneRef.open();
                     }}><Icon icon={faPaperclip} /></div> : ''}

            <Editor
                entropy={comment && comment.id}
                refHandler={this.handleEditorRef}
                placeholder={settings.i18.add_comment}
            />
        </div>;

        if (!canUpload) {
            return outliner;
        }

        return <Dropzone
            disableClick
            className="anycomment"
            ref={(node) => {
                dropzoneRef = node;
            }}
            style={{position: "relative"}}
            maxSize={options.fileMaxSize * 1000000}
            accept={options.fileMimeTypes}
            onDrop={this.onDrop.bind(this)}
            onDragEnter={this.onDragEnter.bind(this)}
            onDragLeave={this.onDragLeave.bind(this)}>
            {outliner}
            <CommentAttachments
                handleAttachmentChange={this.props.handleAttachmentChange}
                attachments={attachments}
                showDeleteAction={!this.isGuest()} />
        </Dropzone>;
    }
}

export default SendCommentFormBody;