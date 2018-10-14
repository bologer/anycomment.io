import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent";
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {faStar, faStarHalf} from '@fortawesome/free-solid-svg-icons'

/**
 * Component used to display rating.
 */
class PageRating extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            value: props.settings.rating.value,
            count: props.settings.rating.count,
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

        return this.props.axios
            .request({
                method: 'post',
                url: '/rate',
                params: {
                    rating: rating,
                    post: settings.postId
                },
                headers: {'X-WP-Nonce': settings.nonce}
            })
            .then(function (response) {
                self.setState({
                    value: response.data.value,
                    count: response.data.count,
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

        let halfAfter = null;

        for (let i = 5; i >= 1; i--) {
            const isHalfStar = (halfAfter !== null && i === halfAfter);


            console.log(halfAfter);

            if (isHalfStar) {
                console.log(i, halfAfter);
            }

            const svg = <FontAwesomeIcon size={24} icon={isHalfStar ? faStarHalf : faStar}/>,
                isActive = i <= value,
                activeClass = isActive ? " anycomment-rating__stars-item-active" : '';

            const item = <span
                className={"anycomment anycomment-rating__stars-item" + activeClass}
                onClick={(e) => this.rate(e, i)}>{svg}</span>;

            if ((value / i) === 1 && (value * 2) % 0.5 !== 0) {
                halfAfter = i;
            }

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
        return (
            <div itemScope itemType="http://schema.org/Product" className="anycomment anycomment-rating">
                <div className="anycomment anycomment-rating__stars">
                    {this.renderStars()}
                </div>
                <div className="anycomment anycomment-rating__count"
                     itemProp="aggregateRating"
                     itemScope
                     itemType="http://schema.org/AggregateRating">
                    <span className="anycomment anycomment-rating__count-value"
                          itemProp="ratingValue">{this.state.value}</span>&nbsp;/&nbsp;
                    <span className="anycomment anycomment-rating__count-count"
                          itemProp="reviewCount">{this.state.count}</span>
                </div>
            </div>
        );
    }
}

export default PageRating;