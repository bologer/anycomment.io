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
            urls: [],
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

    onDrop(files) {

        const self = this,
            settings = this.getSettings(),
            fileLimit = 5;

        if (files.length > fileLimit) {
            toast.error("Please choose 5 or less files");
            files.slice(0, fileLimit);
        }

        this.setState({
            dropzoneActive: false
        });

        const data = new FormData();
        files.map((file, i) => {
            data.append(i, file, file.name);
        });

        data.append('post', settings.postId);

        self.props.axios
            .post('/documents',
                data,
                {
                    headers: {
                        'X-WP-Nonce': settings.nonce,
                        'Content-Type': `multipart/form-data; boundary=${data._boundary}`,
                    },
                    timeout: 30000,
                })
            .then(function (response) {
                self.setState({urls: response.data.urls});
                self.addImageLinks(response.data.urls);
                console.log(response);
                toast.success('Uploaded!');
            })
            .catch(function (error) {
                self.showError(error);
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

    componentDidMount() {
        this.addImageLinks();
    }

    render() {
        const translations = this.props.settings.i18;
        const {urls, dropzoneActive} = this.state;

        return <Dropzone
            disableClick
            style={{position: "relative"}}
            accept={'image/jpeg, image/png'}
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
                          placeholder={translations.add_comment}
                          onChange={this.props.handleContentChange}
                          ref={this.props.commentFieldRef}
                ></textarea>
            </div>
        </Dropzone>
    }
}

export default SendCommentFormBody;