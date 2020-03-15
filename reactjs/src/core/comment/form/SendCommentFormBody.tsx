import React, {useState} from 'react';
import Dropzone from 'react-dropzone';
import CommentAttachments from '~/components/CommentAttachments';
import Icon from '~/components/Icon';
import {faPaperclip} from '@fortawesome/free-solid-svg-icons';
import Editor from '~/components/Editor';
import {useOptions, useSettings} from '~/hooks/setting';
import {isGuest} from '~/helpers/user';
import {CommentModel} from '~/typings/models/CommentModel';
import {StoreProps} from '~/store/reducers';
import {CommentReducerProps} from '~/core/comment/commentReducers';
import {useSelector} from 'react-redux';

export interface SendCommentFormBodyProps {
    attachments: [];
    comment?: CommentModel;
    commentHTML: string;
    handleAttachmentChange: (attachments: []) => void;
    handleEditorChange: (text: string) => void;
    entropy?: string;
}

/**
 * Display comment field of the form.
 */
export default function SendCommentFormBody({
    attachments,
    comment,
    handleAttachmentChange,
    handleEditorChange,
    commentHTML,
}: SendCommentFormBodyProps) {
    const settings = useSettings();
    const options = useOptions();
    const [editorRef, setEditorRef] = useState(null);
    const [dropzoneActive, setDropzoneActive] = useState<boolean>(false);
    const {form} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    function onDragEnter() {
        setDropzoneActive(true);
    }

    function onDragLeave() {
        setDropzoneActive(false);
    }

    /**
     * On files drop.
     * @param files
     */
    function onDrop(files) {
        processFiles(files);
    }

    /**
     * Process dropped files.
     * @param files
     */
    function processFiles(files) {
        if (!files || files.length === 0) {
            toast.error(settings.i18.file_not_selected_or_extension);
            return false;
        }

        if (files.length > options.fileLimit) {
            toast.error(settings.i18.file_limit);
            setDropzoneActive(false);
            return false;
        }

        const filesToUpload = new FormData();

        files.forEach((file, i) => {
            filesToUpload.append(i, file, file.name);
        });

        filesToUpload.append('post', settings.postId);

        setDropzoneActive(false);
        uploadFiles(filesToUpload);
    }

    /**
     * Upload files to server.
     *
     * @param filesToUpload
     */
    function uploadFiles(filesToUpload) {
        const toastId = toast(settings.i18.file_upload_in_progress, {autoClose: false});

        let headers = {};

        headers['Content-Type'] = `multipart/form-data; boundary=${filesToUpload._boundary}`;

        if (settings.nonce) {
            headers['X-WP-Nonce'] = settings.nonce;
        }

        self.props.axios
            .post('/documents', filesToUpload, {
                headers: headers,
                timeout: 30000,
            })
            .then(function(response) {
                const files = response.data.files,
                    attachments = self.props.attachments;

                if (!attachments || !attachments.length) {
                    handleAttachmentChange(files);
                } else {
                    let newAttachments = attachments;
                    response.data.files.forEach(item => {
                        newAttachments.push(item);
                    });
                    handleAttachmentChange(newAttachments);
                }

                toast.update(toastId, {
                    render: settings.i18.file_uploaded,
                    type: toast.TYPE.SUCCESS,
                    autoClose: 1500,
                    className: 'rotateY animated',
                });
            })
            .catch(function(error) {
                self.showError(
                    error,
                    {
                        autoClose: 1500,
                    },
                    toastId
                );
            });
    }

    /**
     * Check whether user can upload files.
     *
     * @returns {boolean}
     */
    function canUpload() {
        if (options.isFileUploadAllowed && !isGuest()) {
            return true;
        } else if (options.isFileUploadAllowed && isGuest() && options.isGuestCanUpload) {
            return true;
        }

        return false;
    }

    function handleEditorRef(ref) {
        setEditorRef(ref);
        ref.focus();
    }

    let dropzoneRef;

    const isCanUpload = canUpload();

    const outlinerClassName =
        'anycomment anycomment-form-body-outliner' +
        (dropzoneActive ? ' anycomment-form-body-outliner-dropzone-active' : '');
    const outliner = (
        <div className={outlinerClassName}>
            {isCanUpload && (
                <div
                    className='anycomment anycomment-form-body-outliner__select-file'
                    title={settings.i18.upload_file}
                    onClick={() => {
                        dropzoneRef.open();
                    }}
                >
                    <Icon icon={faPaperclip} />
                </div>
            )}

            <Editor
                onChange={handleEditorChange}
                value={commentHTML}
                entropy={comment && comment.id}
                refHandler={handleEditorRef}
                placeholder={settings.i18.add_comment}
            />
        </div>
    );

    if (!isCanUpload) {
        return outliner;
    }

    return (
        <Dropzone
            disableClick
            className='anycomment'
            ref={node => {
                dropzoneRef = node;
            }}
            style={{position: 'relative'}}
            maxSize={options.fileMaxSize * 1000000}
            accept={options.fileMimeTypes}
            onDrop={onDrop}
            onDragEnter={onDragEnter}
            onDragLeave={onDragLeave}
        >
            {outliner}
            <CommentAttachments
                handleAttachmentChange={handleAttachmentChange}
                attachments={attachments}
                showDeleteAction={!isGuest()}
            />
        </Dropzone>
    );
}

SendCommentFormBody.displayName = 'SendCommentFormBody';
