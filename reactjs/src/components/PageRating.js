import React from 'react'
import Icon from './Icon'
import Rating from 'react-rating'
import {toast} from 'react-toastify'
import AnyCommentComponent from "./AnyCommentComponent";
import {faStar} from '@fortawesome/free-solid-svg-icons'
import {faStar as faStarEmpty} from '@fortawesome/free-regular-svg-icons'

/**
 * Component used to display rating.
 */
class PageRating extends AnyCommentComponent {

    constructor(props) {
        super();

        this.state = {
            value: props.settings.rating.value,
            count: props.settings.rating.count,
            hasRated: props.settings.rating.hasRated
        }
    }

    /**
     * Action to set rating.
     *
     * @param rating
     * @returns {*}
     */
    onRate = (rating) => {
        const settings = this.getSettings(),
            self = this;

        if (this.state.hasRated) {
            toast.error(settings.i18.already_rated);
            return false;
        }

        let headers = {};

        if (settings.nonce) {
            headers = {'X-WP-Nonce': settings.nonce};
        }

        return this.props.axios
            .request({
                method: 'post',
                url: '/rate',
                params: {
                    rating: rating,
                    post: settings.postId
                },
                headers: headers
            })
            .then(function (response) {
                self.setState({
                    value: response.data.value,
                    count: response.data.count,
                    hasRated: true
                });
            })
            .catch(function (error) {
                self.showError(error);
            });
    };

    /**
     * Render component.
     *
     * @returns {*}
     */
    render() {
        const options = this.getOptions();

        if (!options.isRatingOn) {
            return null;
        }

        const {value, count, hasRated} = this.state;

        return (
            <div itemScope itemType="http://schema.org/Article"
                 className="anycomment anycomment-rating">
                <div
                    className={"anycomment anycomment-rating__stars" + (hasRated ? " anycomment-rating__stars-readonly" : '')}>
                    <Rating
                        start={0}
                        stop={5}
                        step={1}
                        initialRating={value}
                        readonly={hasRated}
                        emptySymbol={<Icon size="22px" icon={faStarEmpty}/>}
                        fullSymbol={<Icon class="anycomment anycomment-rating__stars-active" color="#eeba64" size="22px" icon={faStar}/>}
                        onClick={this.onRate}
                    />
                </div>
                <div className="anycomment anycomment-rating__count"
                     itemProp="aggregateRating"
                     itemScope
                     itemType="http://schema.org/AggregateRating">
                    <span className="anycomment anycomment-rating__count-value"
                          itemProp="ratingValue">{value}</span>&nbsp;/&nbsp;
                    <span className="anycomment anycomment-rating__count-count"
                          itemProp="reviewCount">{count}</span>
                </div>
            </div>
        );
    }
}

export default PageRating;