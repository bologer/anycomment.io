import React, {Component} from 'react';
import Lightbox from 'react-images';

class CommentAttachments extends Component {
    constructor(props) {
        super(props);

        this.state = {
            lightboxIsOpen: false,
            currentImage: 0,
            showDeleteAction: props.showDeleteAction || false
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

    handleDelete(index, event) {
        event.preventDefault();

        console.log('can try to delete', index, event);

        // this.
        // this.props.images[index]
    }

    renderGallery() {
        const {attachments} = this.props;

        if (!attachments || attachments.length <= 0)
            return (null);

        const renderedGallery = attachments.map((obj, i) => {
            const isImage = obj.isImage || false,
                isAudio = obj.isAudio || false;

            if (isImage) {
                return <li
                    key={i}
                    onClick={(e) => this.openLightbox(i, e)}
                    className="anycomment anycomment-uploads__item">
                    {this.state.showDeleteAction ?
                        <span className="anycomment anycomment-uploads__item-close"
                              onClick={(e) => this.handleDelete(i, e)}>&times;</span> : ''}
                    <img className="anycomment anycomment-uploads__item-thumbnail" src={obj.thumbnail}/>
                </li>;
            } else if (isAudio) {
                return <li
                    key={i}
                    className="anycomment anycomment-uploads__item anycomment-uploads__item-audio">
                    {this.state.showDeleteAction ?
                        <span className="anycomment anycomment-uploads__item-close"
                              onClick={(e) => this.handleDelete(i, e)}>&times;</span> : ''}
                    Audio
                </li>;
            }

            return <li
                key={i}
                className="anycomment anycomment-uploads__item anycomment-uploads__item anycomment-uploads__item-document">
                {this.state.showDeleteAction ?
                    <span className="anycomment anycomment-uploads__item-close"
                          onClick={(e) => this.handleDelete(i, e)}>&times;</span> : ''}
                Doc
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
        const {attachments} = this.props;

        if (!attachments.length) {
            return [];
        }

        return attachments.filter(item => item.isImage);
    }

    render() {
        const images = this.filterImages();

        if (!images || images.length <= 0) {
            return (null);
        }

        console.log(images);

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
