import React from 'react'
import Lightbox from 'react-images'
import AnyCommentComponent from "./AnyCommentComponent";
import SVG from 'react-inlinesvg'
import audioIcon from '../img/icons/icon-audio.svg'
import documentIcon from '../img/icons/icon-document.svg'


class CommentAttachments extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state = {
            lightboxIsOpen: false,
            currentImage: 0,
            showDeleteAction: props.showDeleteAction || false,
        };

        this.closeLightbox = this.closeLightbox.bind(this);
        this.gotoNext = this.gotoNext.bind(this);
        this.gotoPrevious = this.gotoPrevious.bind(this);
        this.gotoImage = this.gotoImage.bind(this);
        this.handleClickImage = this.handleClickImage.bind(this);
        this.openLightbox = this.openLightbox.bind(this);
        this.filterImages = this.filterImages.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
    }

    openLightbox(index, event) {
        event.preventDefault();
        this.setState({
            currentImage: index,
            lightboxIsOpen: true,
        });
    }

    closeLightbox() {
        this.setState({
            currentImage: 0,
            lightboxIsOpen: false,
        });
    }

    gotoPrevious() {
        this.setState({
            currentImage: this.state.currentImage - 1,
        });
    }

    gotoNext() {
        this.setState({
            currentImage: this.state.currentImage + 1,
        });
    }

    gotoImage(index) {
        this.setState({
            currentImage: index,
        });
    }

    handleClickImage() {
        if (this.state.currentImage === this.props.images.length - 1) return;

        this.gotoNext();
    }

    handleDelete(index, obj, event) {
        event.preventDefault();
        event.stopPropagation();

        if (this.isGuest()) {
            return false;
        }

        const settings = this.getSettings(),
            url = '/documents/delete',
            self = this;

        this.props.axios
            .request({
                method: 'post',
                url: url,
                params: {id: obj.file_id},
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                if (response.data.success) {
                    const cleanDeletedAttachment = self.props.attachments.filter((obj, i) => {
                        return i !== index;
                    });

                    self.props.handleAttachmentChange(cleanDeletedAttachment);
                }
            })
            .catch(function (error) {
                self.showError(error);
            });

        return false;
    }

    renderGallery() {
        const {attachments} = this.props;

        if (!attachments || attachments.length <= 0)
            return (null);

        const renderedGallery = attachments.map((obj, i) => {
            const type = (obj.file_type || ''),
                isImage = type === 'image',
                isAudio = type === 'audio';

            if (isImage) {
                return <li
                    key={i}
                    onClick={(e) => this.openLightbox(i, e)}
                    className="anycomment anycomment-uploads__item">
                    {this.state.showDeleteAction ?
                        <span className="anycomment anycomment-uploads__item-close"
                              onClick={(e) => this.handleDelete(i, obj, e)}>&times;</span> : ''}
                    <img className="anycomment anycomment-uploads__item-thumbnail" src={obj.file_thumbnail}/>
                </li>;
            } else if (isAudio) {
                return <li
                    key={i}
                    className="anycomment anycomment-uploads__item anycomment-uploads__item-audio">
                    {this.state.showDeleteAction ?
                        <span className="anycomment anycomment-uploads__item-close"
                              onClick={(e) => this.handleDelete(i, obj, e)}>&times;</span> : ''}
                    <a href={obj.file_url} target="_blank">
                        <SVG
                            src={audioIcon}
                            preloader={false}
                        />
                    </a>
                </li>;
            }

            return <li
                key={i}
                className="anycomment anycomment-uploads__item anycomment-uploads__item anycomment-uploads__item-document">
                {this.state.showDeleteAction ?
                    <span className="anycomment anycomment-uploads__item-close"
                          onClick={(e) => this.handleDelete(i, obj, e)}>&times;</span> : ''}
                <a href={obj.file_url} target="_blank">
                    <SVG
                        src={documentIcon}
                        preloader={false}
                    />
                </a>
            </li>;
        });

        return (renderedGallery);
    }

    /**
     * Filters files and gets list of images.
     *
     * @returns {Array}
     */
    filterImages() {
        let {attachments} = this.props;

        if (!attachments.length) {
            return [];
        }

        attachments = attachments.filter(item => (item.file_type === 'image'));

        return attachments.map((item) => {
            item['src'] = item.file_url;
            item['thumbnail'] = item.file_thumbnail;

            return item;
        });
    }

    render() {
        const images = this.filterImages(),
            {attachments} = this.props;

        if (!attachments || attachments.length <= 0) {
            return (null);
        }

        return (
            <ul className="anycomment anycomment-uploads">
                {this.renderGallery()}
                <Lightbox
                    currentImage={this.state.currentImage}
                    images={images}
                    isOpen={this.state.lightboxIsOpen}
                    onClickImage={this.handleClickImage}
                    onClickNext={this.gotoNext}
                    onClickPrev={this.gotoPrevious}
                    onClickThumbnail={this.gotoImage}
                    onClose={this.closeLightbox}
                    preventScroll={this.props.preventScroll}
                    showThumbnails={this.props.showThumbnails}
                />
            </ul>
        );
    }
}

export default CommentAttachments;
