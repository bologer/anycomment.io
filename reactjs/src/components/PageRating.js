import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent";
import Icon from './Icon'
import {faStar, faStarHalfAlt} from '@fortawesome/free-solid-svg-icons'
import {toast} from 'react-toastify'

/**
 * Component used to display rating.
 */
class PageRating extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            value: props.settings.rating.value,
            count: props.settings.rating.count,
            hasRated: props.settings.rating.hasRated
        }
    }

    /**
     * Action to set rating.
     *
     * @param e
     * @param rating
     * @returns {*}
     */
    rate = (e, rating) => {
        e.preventDefault();

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
     * Render stars.
     *
     * @returns {Array}
     */
    renderStars() {
        let stars = [];

        let {value} = this.state;

        const matches = value > 0 ? value.match(/([1-5])\.([0-9])/m) : [];

        for (let i = 5; i >= 1; i--) {

            const isActive = i <= value,
                activeClass = isActive ? " anycomment-rating__stars-item-active" : '';

            let icon = faStar;

            if (matches !== [] && parseInt(matches[1]) === i && matches[2] >= 5) {
                icon = faStarHalfAlt;
            }

            const item = <span
                className={"anycomment anycomment-rating__stars-item" + activeClass}
                onClick={(e) => this.rate(e, i)}><Icon size={24} icon={icon}/></span>;

            stars.push(item);
        }

        return stars;
    }

    /**
     * Render component.
     *
     * @returns {*}
     */
    render() {
        const options = this.getOptions();

        if (!options.isRatingOn) {
            return (null);
        }

        const {value, count, hasRated} = this.state;

        return (
            <div itemScope itemType="http://schema.org/Article"
                 className="anycomment anycomment-rating">
                <div
                    className={"anycomment anycomment-rating__stars" + (hasRated ? " anycomment-rating__stars-readonly" : '')}>
                    {this.renderStars()}
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