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
            files: [],
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

        const data = new FormData();
        files.map((file, i) => {
            data.append(i, file, file.name);
        });

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
                self.setState({urls: response.urls});
                self.addImageLinks();
                console.log(response);
                toast.success('Uploaded!');
            })
            .catch(function (error) {
                self.showError(error);
            });

        this.setState({
            files,
            dropzoneActive: false
        });
    }

    addImageLinks() {
        if (!this.state.urls) {
            return false;
        }

        let text = '';
        this.state.urls.map((url, i) => {
            text += url + ' ';
        });

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

            {/*<div className="anycomment anycomment-send-comment-attachments">*/}
                {/*<ul>*/}
                    {/*{*/}
                        {/*urls.map(f => <li><img src={f.preview}/> {f.name} - {f.size} bytes</li>)*/}
                    {/*}*/}
                {/*</ul>*/}
            {/*</div>*/}
        </Dropzone>
    }
}

export default SendCommentFormBody;