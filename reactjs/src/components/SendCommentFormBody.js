import React from 'react'
import SendCommentFormBodyAvatar from './SendCommentFormBodyAvatar'
import AnyCommentComponent from "./AnyCommentComponent"
import Dropzone from 'react-dropzone'
import {toast} from 'react-toastify'
import SVG from 'react-inlinesvg';
import selectFileSvg from '../img/select-file.svg'
import CommentAttachments from './CommentAttachments'

/**
 * Display comment field of the form.
 */
class SendCommentFormBody extends AnyCommentComponent {

    constructor() {
        super();

        this.state = {
            dropzoneActive: false,
        };

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

        self.props.axios
            .post('/documents',
                filesToUpload,
                {
                    headers: {
                        'X-WP-Nonce': settings.nonce,
                        'Content-Type': `multipart/form-data; boundary=${filesToUpload._boundary}`,
                    },
                    timeout: 30000,
                })
            .then(function (response) {

                const files = response.data.files,
                    attachments = self.props.attachments;

                if (!attachments || !attachments.length) {
                    self.props.onAttachmentChange(files);
                } else {
                    let newAttachments = attachments;
                    response.data.files.forEach((item) => {
                        newAttachments.push(item);
                    });
                    self.props.onAttachmentChange(newAttachments);
                }

                toast.update(toastId, {
                    render: settings.i18.file_uploaded,
                    type: toast.TYPE.SUCCESS,
                    autoClose: 1500,
                    className: 'rotateY animated'
                });
            })
            .catch(function (error) {
                console.log(error);
                self.showError(error, {
                    autoClose: 1500
                }, toastId);
            });
    }

    render() {
        const settings = this.getSettings();
        const options = settings.options;
        const {dropzoneActive} = this.state;
        const {attachments} = this.props;

        let dropzoneRef;

        const canUpload = !this.isGuest() || (this.isGuest() && options.isGuestCanUpload);

        const outliner = <div
            className={"anycomment anycomment-send-comment-body-outliner" + (dropzoneActive ? ' anycomment-send-comment-body-outliner-dropzone-active' : '')}>

            <SendCommentFormBodyAvatar user={this.props.user}/>

            {canUpload ?
                <div className="anycomment anycomment-send-comment-body-outliner__select-file"
                     title={settings.i18.upload_file}
                     onClick={() => {
                         dropzoneRef.open()
                     }}><SVG src={selectFileSvg}
                             preloader={false}
                /></div> : ''}

            <textarea name="content"
                      value={this.props.commentText}
                      required="required"
                      className="anycomment anycomment-send-comment-body-outliner__textfield"
                      placeholder={settings.i18.add_comment}
                      onChange={this.props.handleContentChange}
                      ref={this.props.commentFieldRef}
            ></textarea>
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
                onAttachmentChange={this.props.onAttachmentChange}
                attachments={attachments}
                showDeleteAction={!this.isGuest()}/>
        </Dropzone>
    }
}

export default SendCommentFormBody;