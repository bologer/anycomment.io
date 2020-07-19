import React, {useEffect, useState} from 'react';
import Icon from '../../../components/Icon';
import Rating from 'react-rating';
import {faStar} from '@fortawesome/free-solid-svg-icons';
import {faStar as faStarEmpty} from '@fortawesome/free-regular-svg-icons';
import {useConfig, useOptions, useSettings} from '~/hooks/setting';
import {useDispatch, useSelector} from 'react-redux';
import {failureSnackbar} from '~/core/notifications/NotificationActions';
import {ratePage} from './PageRatingActions';
import {StoreProps} from '~/store/reducers';
import {PageRatingReducerProps} from '~/core/extensions/page-rating/pageRatingReducers';
import {manageReducer} from '~/helpers/action';
import {fireEvent} from '~/helpers/events';

/**
 * Renders page rating.
 */
export default function PageRating() {
    const settings = useSettings();
    const options = useOptions();
    const dispatch = useDispatch();
    const config = useConfig();
    const [value, setValue] = useState<number>(settings.rating.value);
    const [count, setCount] = useState<number>(settings.rating.count);
    const [hasRated, setHasRated] = useState<boolean>(settings.rating.hasRated);

    const {rate} = useSelector<StoreProps, PageRatingReducerProps>(state => state.extensions.pageRating);

    useEffect(() => {
        manageReducer({
            reducer: rate,
            onSuccess: response => {
                fireEvent(config.events, 'onRated', {
                    pageId: config.postId,
                    value: response.value,
                    totalCount: response.count,
                });
                setValue(response.value);
                setCount(response.count);
                setHasRated(true);
            },
        });
    }, [rate]);

    /**
     * Action to set rating.
     *
     * @param rating
     * @returns {*}
     */
    function handleRate(rating) {
        if (hasRated) {
            dispatch(failureSnackbar(settings.i18.already_rated));
        } else {
            dispatch(ratePage(config.postId, rating));
        }
    }

    if (!options.isRatingOn) {
        return null;
    }

    return (
        <div itemScope itemType='http://schema.org/Article' className='anycomment anycomment-rating'>
            <div
                className={
                    'anycomment anycomment-rating__stars' + (hasRated ? ' anycomment-rating__stars-readonly' : '')
                }
            >
                <Rating
                    start={0}
                    stop={5}
                    step={1}
                    initialRating={value}
                    readonly={hasRated}
                    emptySymbol={<Icon size='lg' icon={faStarEmpty} />}
                    fullSymbol={
                        <Icon
                            className='anycomment anycomment-rating__stars-active'
                            color='#eeba64'
                            size='lg'
                            icon={faStar}
                        />
                    }
                    onClick={handleRate}
                />
            </div>
            <div
                className='anycomment anycomment-rating__count'
                itemProp='aggregateRating'
                itemScope
                itemType='http://schema.org/AggregateRating'
            >
                <span className='anycomment anycomment-rating__count-value' itemProp='ratingValue'>
                    {value}
                </span>
                &nbsp;/&nbsp;
                <span className='anycomment anycomment-rating__count-count' itemProp='reviewCount'>
                    {count}
                </span>
            </div>
        </div>
    );
}

PageRating.displayName = 'PageRating';
