import React from 'react'
import SendCommentFormBodyAvatar from './SendCommentFormBodyAvatar'
import AnyCommentComponent from "./AnyCommentComponent"
import Dropzone from 'react-dropzone'
import {toast} from 'react-toastify'

/**
 * Display comment field of the form.
 */
class SendCommentFormBody extends AnyCommentComponent {

    constructor() {
        super();

        this.state = {
            dropzoneActive: false
        };

        this.onDragEnter = this.onDragEnter.bind(this);
        this.onDragLeave = this.onDragLeave.bind(this);
        this.onDrop = this.onDrop.bind(this);
        this.addImageLinks = this.addImageLinks.bind(this);
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
            toast.error(settings.i18.file_not_selected_or_extension)
            return false;
        }

        if (files.length > options.fileLimit) {
            toast.error(settings.i18.file_limit);
            files.slice(0, options.fileLimit);
        }

        this.setState({
            dropzoneActive: false
        });

        const filesToUpload = new FormData();
        files.map((file, i) => {
            filesToUpload.append(i, file, file.name);
        });

        filesToUpload.append('post', settings.postId);

        this.uploadFiles(filesToUpload);
    }

    /**
     * Upload files to server.
     *
     * @param filesToUpload
     */
    uploadFiles(filesToUpload) {
        const self = this,
            settings = this.getSettings();

        const toastId = toast(settings.i18.file_upload_in_progress, {autoClose: false});

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
                self.addImageLinks(response.data.urls);
                toast.update(toastId, {
                    render: settings.i18.file_uploaded,
                    type: toast.TYPE.SUCCESS,
                    autoClose: 1500,
                    className: 'rotateY animated'
                });
            })
            .catch(function (error) {
                self.showError(error, {
                    autoClose: 1500
                }, toastId);
            });
    }

    addImageLinks(links) {
        if (!links) {
            return false;
        }

        let text = '';
        links.map((url, i) => {
            text += url + ' ';
        });

        console.log(links);
        console.log(text);

        this.props.changeCommenText(text);
    }

    render() {
        const settings = this.getSettings();
        const options = settings.options;
        const {dropzoneActive} = this.state;

        console.log(options.fileMimeTypes);

        return <Dropzone
            disableClick
            style={{position: "relative"}}
            maxSize={options.fileMaxSize * 1000000}
            accept={options.fileMimeTypes}
            onDrop={this.onDrop.bind(this)}
            onDragEnter={this.onDragEnter.bind(this)}
            onDragLeave={this.onDragLeave.bind(this)}>

            <div
                className={"anycomment anycomment-send-comment-body-outliner" + (dropzoneActive ? ' anycomment-send-comment-body-outliner-dropzone-active' : '')}>

                <SendCommentFormBodyAvatar user={this.props.user}/>


                <textarea name="content"
                          value={this.props.commentText}
                          required="required"
                          className="anycomment anycomment-send-comment-body-outliner__textfield"
                          placeholder={settings.i18.add_comment}
                          onChange={this.props.handleContentChange}
                          ref={this.props.commentFieldRef}
                ></textarea>
            </div>
        </Dropzone>
    }
}

export default SendCommentFormBody;