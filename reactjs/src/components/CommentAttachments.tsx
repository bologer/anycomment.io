import React, {useEffect, useState} from 'react';
import Lightbox from 'react-images';
import Icon from './Icon';
import {faMusic, faFile} from '@fortawesome/free-solid-svg-icons';
import {isGuest} from '~/helpers/user';
import {useSettings} from '~/hooks/setting';
import {useDispatch, useSelector} from 'react-redux';
import {deleteAttachment} from '~/core/comment/CommentActions';
import {manageReducer} from '~/helpers/action';
import {StoreProps} from '~/store/reducers';
import {CommentReducerProps} from '~/core/comment/commentReducers';

/**
 * Renders list of attachments for a single comment.
 */
export default function CommentAttachments({
    showDeleteAction = false,
    preventScroll,
    showThumbnails,
    handleAttachmentChange,
    attachments,
}) {
    const settings = useSettings();
    const dispatch = useDispatch();
    const [lightboxIsOpen, setLightboxIsOpen] = useState<boolean>(false);
    const [currentImage, setCurrentImage] = useState<number>(0);

    const {attachmentDelete} = useSelector<StoreProps, CommentReducerProps>(state => state.comments);

    /**
     * Handles lightbox opening of selected image.
     *
     * @param index
     * @param event
     */
    function openLightbox(index, event) {
        event.preventDefault();
        setCurrentImage(index);
        setLightboxIsOpen(true);
    }

    /**
     * Closes lighbox by reseting state values to default.
     */
    function closeLightbox() {
        setCurrentImage(0);
        setLightboxIsOpen(false);
    }

    /**
     * Shows previous image.
     */
    function gotoPrevious() {
        setCurrentImage(currentImage - 1);
    }

    /**
     * Shows next image.
     */
    function gotoNext() {
        setCurrentImage(currentImage + 1);
    }

    /**
     * Shows specific image by provided index.
     * @param index
     */
    function gotoImage(index) {
        setCurrentImage(index);
    }

    function handleClickImage() {
        if (currentImage === images.length - 1) return;

        gotoNext();
    }

    useEffect(() => {
        manageReducer({
            reducer: attachmentDelete,
            onSuccess: ({index}) => {
                const cleanDeletedAttachment = attachments.filter((_obj, i) => {
                    return i !== index;
                });

                if (typeof handleAttachmentChange === 'function') {
                    handleAttachmentChange(cleanDeletedAttachment);
                }
            },
        });
    }, [attachmentDelete]);

    /**
     * Delete single attachment.
     *
     * @param index
     * @param obj
     * @param event
     */
    function handleDelete(index, obj, event) {
        event.preventDefault();
        event.stopPropagation();

        if (isGuest()) {
            return false;
        }

        dispatch(deleteAttachment(obj.file_id, index));
    }

    /**
     * Renders image gallery.
     */
    function renderGallery() {
        if (!attachments || attachments.length <= 0) return null;

        return attachments.map((obj, i) => {
            const type = obj.file_type || '';
            const isImage = type === 'image';
            const isAudio = type === 'audio';

            if (isImage) {
                return (
                    <li key={i} onClick={e => openLightbox(i, e)} className='anycomment anycomment-uploads__item'>
                        {showDeleteAction && (
                            <span
                                className='anycomment anycomment-uploads__item-close'
                                onClick={e => handleDelete(i, obj, e)}
                            >
                                &times;
                            </span>
                        )}
                        <img className='anycomment anycomment-uploads__item-thumbnail' src={obj.file_thumbnail} />
                    </li>
                );
            } else if (isAudio) {
                return (
                    <li key={i} className='anycomment anycomment-uploads__item anycomment-uploads__item-audio'>
                        {showDeleteAction && (
                            <span
                                className='anycomment anycomment-uploads__item-close'
                                onClick={e => handleDelete(i, obj, e)}
                            >
                                &times;
                            </span>
                        )}
                        <a href={obj.file_url} target='_blank' rel='noopener noreferrer'>
                            <Icon icon={faMusic} />
                        </a>
                    </li>
                );
            }

            return (
                <li
                    key={i}
                    className='anycomment anycomment-uploads__item anycomment-uploads__item anycomment-uploads__item-document'
                >
                    {showDeleteAction && (
                        <span
                            className='anycomment anycomment-uploads__item-close'
                            onClick={e => handleDelete(i, obj, e)}
                        >
                            &times;
                        </span>
                    )}
                    <a href={obj.file_url} target='_blank' rel='noopener noreferrer'>
                        <Icon icon={faFile} />
                    </a>
                </li>
            );
        });
    }

    /**
     * Filters files and gets list of images.
     *
     * @returns {Array}
     */
    function filterImages() {
        if (!attachments.length) {
            return [];
        }

        attachments = attachments.filter(item => item.file_type === 'image');

        return attachments.map(item => {
            item['src'] = item.file_url;
            item['thumbnail'] = item.file_thumbnail;

            return item;
        });
    }

    const images = filterImages();

    if (!attachments || attachments.length <= 0) {
        return null;
    }

    const theme = {
        container: {
            zIndex: 99999,
        },
    };

    return (
        <ul className='anycomment anycomment-uploads'>
            {renderGallery()}
            <Lightbox
                closeButtonTitle={settings.i18.lighbox_close}
                imageCountSeparator={settings.i18.lighbox_image_count_separator}
                leftArrowTitle={settings.i18.lighbox_left_arrow}
                rightArrowTitle={settings.i18.lighbox_right_arrow}
                currentImage={currentImage}
                images={images}
                theme={theme}
                isOpen={lightboxIsOpen}
                onClickImage={handleClickImage}
                onClickNext={gotoNext}
                onClickPrev={gotoPrevious}
                onClickThumbnail={gotoImage}
                onClose={closeLightbox}
                preventScroll={preventScroll}
                showThumbnails={showThumbnails}
            />
        </ul>
    );
}

CommentAttachments.displayName = 'CommentAttachments';
