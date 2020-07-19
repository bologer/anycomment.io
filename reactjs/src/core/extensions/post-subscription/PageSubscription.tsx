import React, {useEffect, useState} from 'react';
import Icon from '../../../components/Icon';
import {faEnvelope} from '@fortawesome/free-solid-svg-icons';
import {useFormik} from 'formik';
import {isGuest} from '~/helpers/user';
import {useConfig, useSettings} from '~/hooks/setting';
import {subscribeToPage} from './PageSubscriptionActions';
import {useDispatch, useSelector} from 'react-redux';
import {add, get} from '~/helpers/storage';
import {StoreProps} from '~/store/reducers';
import {PageSubscriptionReducerProps} from './pageSubscriptionReducers';
import {manageReducer} from '~/helpers/action';
import {successSnackbar} from '~/core/notifications/NotificationActions';

const LOCALE_STORE_KEY = 'anycomment-subscrine-closed';

interface SubscribeFormValues {
    email: string;
}

/**
 * Renders post subscription form.
 * @constructor
 */
export default function PageSubscription() {
    const dispatch = useDispatch();
    const settings = useSettings();
    const config = useConfig();
    const [showForm, setShowForm] = useState(true);

    const {subscription} = useSelector<StoreProps, PageSubscriptionReducerProps>(
        state => state.extensions.pageSubscription
    );

    const formik = useFormik<SubscribeFormValues>({
        initialValues: {
            email: '',
        },
        onSubmit: values => {
            const user = settings.user;
            const email = user !== null ? user.data.user_email : values.email;

            dispatch(subscribeToPage(config.postId, email));
        },
    });

    useEffect(() => {
        manageReducer({
            reducer: subscription,
            onSuccess: () => {
                dispatch(successSnackbar(settings.i18.subscribed));
            },
        });
    }, [subscription]);

    /**
     * Handle hide form action.
     */
    function handleClose() {
        add(getLocalStoargeKey(), 1);
        setShowForm(false);
    }

    /**
     * Get key used to store whether form was spreviously closed.
     */
    function getLocalStoargeKey() {
        return LOCALE_STORE_KEY + config.postId;
    }

    /**
     * Get store key + post ID as subscription can be done per post.
     *
     * @returns {string}
     */
    function wasAlreadyClosed() {
        return get(getLocalStoargeKey()) !== null;
    }

    /**
     * Check whether component should render or not.
     *
     * @returns {boolean}
     */
    function shouldRender() {
        if (!settings.options.isNotifySubscribers || !showForm || wasAlreadyClosed()) {
            return false;
        }

        return true;
    }

    if (!shouldRender()) {
        return null;
    }

    return (
        <div className='anycomment anycomment-component anycomment-subscribe'>
            <p>{settings.i18.subscribe_pre_paragraph}</p>
            <form onSubmit={formik.handleSubmit}>
                {isGuest() && (
                    <div className='anycomment anycomment-subscribe__email'>
                        <div className='anycomment anycomment-subscribe__email--icon'>
                            <Icon icon={faEnvelope} />
                        </div>
                        <input
                            onChange={formik.handleChange}
                            type='email'
                            name='email'
                            value={formik.values.email}
                            placeholder={settings.i18.email}
                            required
                        />
                    </div>
                )}
                <div className='anycomment anycomment-subscribe__submit'>
                    <input type='submit' className='anycomment-btn' value={settings.i18.subscribe} />
                </div>
                <div className='anycomment anycomment-subscribe__close' onClick={handleClose}>
                    {settings.i18.hide_this_message}
                </div>
            </form>
        </div>
    );
}

PageSubscription.displayName = 'PageSubscription';
