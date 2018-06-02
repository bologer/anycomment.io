<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AC_SocialAuth')) :
    /**
     * AC_SocialAuth helps to process website authentication.
     */
    class AC_SocialAuth
    {
        const SOCIAL_VK = 'vk';
        const SOCIAL_FACEBOOK = 'facebook';
        const SOCIAL_TWITTER = 'twitter';
        const SOCIAL_GOOGLE_PLUS = 'google+';

        const META_SOCIAL_TYPE = 'anycomment_social';
        const META_SOCIAL_AVATAR = 'anycomment_social_avatar';

        /**
         * @var \VK\VK
         */
        private $auth_vk;
        private $auth_facebook;
        private $auth_twitter;
        private $auth_google_plus;

        protected static $rest_prefix = 'anycomment';
        protected static $rest_version = 'v1';

        /**
         * AC_SocialAuth constructor.
         */
        public function __construct()
        {
            $this->init_socials();
            $this->init_rest_route();
        }

        /**
         * Init socials with secrets and app IDs.
         */
        public function init_socials()
        {
            if (AC_SocialSettingPage::isVkOn() &&
                ($appId = AC_SocialSettingPage::getVkAppId()) !== null &&
                ($secureKey = AC_SocialSettingPage::getVkSecureKey()) !== null) {
                try {
                    $this->auth_vk = new \VK\VK($appId, $secureKey);
                } catch (\VK\VKException $exception) {

                }
            }

            if (AC_SocialSettingPage::isTwitterOn() &&
                ($consumerKey = AC_SocialSettingPage::getTwitterConsumerKey()) !== null &&
                ($consumerSecret = AC_SocialSettingPage::getTwitterConsumerSecret()) !== null)
                $this->auth_twitter = new \Abraham\TwitterOAuth\TwitterOAuth($consumerKey, $consumerSecret);
        }

        /**
         * Get REST namespace.
         * @return string
         */
        private static function get_rest_namespace()
        {
            return sprintf('%s/%s', static::$rest_prefix, static::$rest_version);
        }

        /**
         * Used to initiate REST routes to log in client, etc.
         */
        private function init_rest_route()
        {
            add_action('rest_api_init', function () {
                $namespace = static::get_rest_namespace();
                $route = '/auth/(?P<social>\w[\w\s]*)/';
                register_rest_route($namespace, $route, [
                    'methods' => 'GET',
                    'callback' => [$this, 'process_socials'],
                ]);
            });
        }

        /**
         * Main method to process social-like request to authentication, etc.
         * @param WP_REST_Request $request
         * @return WP_Error
         */
        public function process_socials(WP_REST_Request $request)
        {
            $redirect = $request->get_param('redirect');
            $social = $request->get_param('social');

            switch ($social):
                case self::SOCIAL_VK:
                    return $this->process_auth_vk($request, $redirect);
                    break;
                case self::SOCIAL_FACEBOOK:
                    break;
                case self::SOCIAL_TWITTER:
                    return $this->process_auth_twitter($request, $redirect);
                    break;
                case self::SOCIAL_GOOGLE_PLUS:
                    break;
                default:
                    wp_redirect($redirect);
                    exit();
            endswitch;
        }

        /**
         * Get REST url + redirect url with it.
         * @param string $social_type URL type, e.g. vk
         * @param string $redirect Redirect URL where to send back user.
         * @return string
         */
        public static function get_callback_url($social_type, $redirect)
        {
            return rest_url(static::get_rest_namespace() . "/auth/" . $social_type . '?redirect=' . $redirect);
        }

        /**
         * Process Twitter authorization.
         * @param WP_REST_Request $request
         * @param null $redirect
         * @return WP_Error
         */
        private function process_auth_twitter(WP_REST_Request $request, $redirect = null)
        {
            $twitter_rest_url = static::get_callback_url(self::SOCIAL_TWITTER, $redirect);
        }

        /**
         * Process VK authorization.
         * @param WP_REST_Request $request
         * @param null $redirect
         * @return WP_Error
         */
        private function process_auth_vk(WP_REST_Request $request, $redirect = null)
        {
            $vk_rest_url = static::get_callback_url(self::SOCIAL_VK, $redirect);

            if ($request->get_param('code') === null) {
                $url = $this->auth_vk->getAuthorizeURL('email', $vk_rest_url);
                wp_redirect($url);
                exit();
            } else {
                try {
                    /**
                     * Response example:
                     * array(4) {
                     * ["access_token"]=>
                     * string(85) ""
                     * ["expires_in"]=>
                     * int(86399)
                     * ["user_id"]=>
                     * int(138463530)
                     * ["email"]=>
                     * string(20) "email@example.com"
                     * }
                     */
                    $access_token = $this->auth_vk->getAccessToken($request->get_param('code'), $vk_rest_url);
                } catch (\VK\VKException $exception) {
                    return new WP_Error('auth_vk_fail', 'Unable to authorize with VK, please try again later' . $exception->getMessage(), ['status' => 404]);
                }

                $user_info = $this->auth_vk->api('users.get', [
                    'user_id' => $access_token['user_id'],
                    'fields' => 'first_name,last_name,deactivated,sex,photo_50,photo_100',
                    'v' => 5.78
                ]);

                if (!isset($user_info['response'])) {
                    wp_redirect($redirect);
                    exit();
                }

                $vkUser = array_merge($access_token, $user_info['response'][0]);

                wp_clear_auth_cookie();

                $user = get_user_by('email', $vkUser['email']);

                if ($user === false) {
                    $newUserId = wp_insert_user([
                        'user_login' => $vkUser['user_id'],
                        'user_email' => $vkUser['email'],
                        'display_name' => trim($vkUser['first_name'] . ' ' . $vkUser['last_name']),
                        'user_nicename' => empty($vkUser['first_name']) ? $vkUser['user_id'] : $vkUser['first_name'],
                        'user_url' => 'https://vk.com/id' . $vkUser['user_id']
                    ]);

                    if ($newUserId instanceof WP_Error) {
                        wp_redirect($redirect);
                        exit();
                    }

                    add_user_meta($newUserId, 'anycomment_social', self::SOCIAL_VK);
                    add_user_meta($newUserId, 'anycomment_social_avatar', $vkUser['photo_50'], true);
                    add_user_meta($newUserId, 'anycomment_social_vk_access_token', $vkUser['access_token'], true);
                    add_user_meta($newUserId, 'anycomment_social_vk_expires_in', $vkUser['expires_in']);
                    add_user_meta($newUserId, 'anycomment_social_vk_photo50', $vkUser['photo_50'], true);
                    add_user_meta($newUserId, 'anycomment_social_vk_photo100', $vkUser['photo_100'], true);
                    add_user_meta($newUserId, 'anycomment_social_vk_sex', $vkUser['sex'], true);

                    $userId = $newUserId;
                } else {
                    $userId = $user->ID;
                }

                wp_clear_auth_cookie();
                wp_set_current_user($userId);
                wp_set_auth_cookie($userId, true);

                wp_redirect($redirect);
                exit();
            }
        }

        /**
         * Get current user avatar URL.
         *
         * @return string|null NULL returned when user does not have any avatar.
         */
        public function get_avatar_url()
        {
            if (($user = wp_get_current_user()) instanceof WP_User) {

                $avatarUrl = get_user_meta($user->ID, self::META_SOCIAL_AVATAR, true);

                if (empty($avatarUrl)) {
                    return null;
                }

                return $avatarUrl;
            }

            return null;
        }
    }
endif;