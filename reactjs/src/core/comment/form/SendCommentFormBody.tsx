import React, {useEffect, useState} from 'react';
import Dropzone from 'react-dropzone';
import CommentAttachments from '~/components/CommentAttachments';
import Icon from '~/components/Icon';
import {faPaperclip} from '@fortawesome/free-solid-svg-icons';
import Editor from '~/components/Editor';
import {useOptions, useSettings} from '~/hooks/setting';
import {isGuest} from '~/helpers/user';
import {CommentModel} from '~/typings/models/CommentModel';
import {useDispatch, useSelector} from 'react-redux';
import {failureSnackbar, successSnackbar} from '~/core/notifications/NotificationActions';
import {uploadAttachment} from '~/core/comment/CommentActions';
import {StoreProps} from '~/store/reducers';
import {CommentReducerProps} from '~/core/comment/commentReducers';

export interface SendCommentFormBodyProps {
    attachments: [];
    comment?: CommentModel;
    commentHTML: string;
    handleAttachmentChange: (attachments: []) => void;
    handleEditorChange: (text: string) => void;
    entropy?: string;
    handleEditorRef: (editorRef: any) => void;
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
    handleEditorRef,
}: SendCommentFormBodyProps) {
    const dispatch = useDispatch();
    const settings = useSettings();
    const options = useOptions();
    const [dropzoneActive, setDropzoneActive] = useState<boolean>(false);
    const {attachmentUpload} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    const attachmentEntropy = comment && comment.id || 'default';

    useEffect(() => {

        const attachmentReducer = attachmentUpload[attachmentEntropy] || undefined;

        if (attachmentReducer && !attachmentReducer.isFetching) {
            const {payload: response} = attachmentReducer;
            if (response) {
                const files = response.files || undefined;

                if (files) {
                    if (!attachments || !attachments.length) {
                        handleAttachmentChange(files);
                    } else {
                        let newAttachments: [] = attachments;
                        files.forEach(item => {
                            newAttachments.push(item);
                        });
                        handleAttachmentChange(newAttachments);
                    }
                }

                dispatch(successSnackbar(settings.i18.file_uploaded));
            }
        }
    }, [attachmentUpload[attachmentEntropy]]);

    /**
     * When user drags file over comment form.
     */
    function onDragEnter() {
        setDropzoneActive(true);
    }

    /**
     * When user drags outside comment form.
     */
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
            dispatch(failureSnackbar(settings.i18.file_not_selected_or_extension));
            return false;
        }

        if (files.length > options.fileLimit) {
            dispatch(failureSnackbar(settings.i18.file_limit));
            setDropzoneActive(false);
            return false;
        }

        const filesToUpload = new FormData();

        files.forEach((file, i) => {
            filesToUpload.append(i, file, file.name);
        });

        filesToUpload.append('post', settings.postId);
        setDropzoneActive(false);

        dispatch(successSnackbar(settings.i18.file_upload_in_progress));
        dispatch(uploadAttachment(filesToUpload, attachmentEntropy));

        return true;
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
                showToolbar={options.isEditorOn}
                formats={options.editorToolbarOptions}
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
