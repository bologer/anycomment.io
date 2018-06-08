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
        const SOCIAL_GOOGLE = 'google';
        const SOCIAL_OK = 'odnoklasniki';

        const META_SOCIAL_TYPE = 'anycomment_social';
        const META_SOCIAL_AVATAR = 'anycomment_social_avatar';

        /**
         * @var \VK\VK
         */
        private $auth_vk;

        /**
         * @var \Facebook\Facebook
         */
        private $auth_facebook;

        /**
         * @var \Abraham\TwitterOAuth\TwitterOAuth
         */
        private $auth_twitter;

        /**
         * @var Google_Client
         */
        private $auth_google;

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


            if (AC_SocialSettingPage::isFbOn() &&
                ($appId = AC_SocialSettingPage::getFbAppId()) !== null &&
                ($appSecret = AC_SocialSettingPage::getFbAppSecret()) !== null) {
                try {
                    $this->auth_facebook = new Facebook\Facebook([
                        'app_id' => $appId,
                        'app_secret' => $appSecret,
                        'default_graph_version' => 'v2.10',
                    ]);
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {

                }
            }

            if (AC_SocialSettingPage::isGoogleOn() &&
                ($clientId = AC_SocialSettingPage::getGoogleClientId()) !== null &&
                ($clientSecretKey = AC_SocialSettingPage::getGoogleSecret()) !== null) {
                $this->auth_google = new Google_Client();
                $this->auth_google->setClientId($clientId);
                $this->auth_google->setClientSecret($clientSecretKey);
            }
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
         */
        public function process_socials(WP_REST_Request $request)
        {
            $redirect = $request->get_param('redirect');
            $social = $request->get_param('social');

//            if (empty($redirect)) {
//                wp_redirect('/');
//                wp_die();
//            }

            if (is_user_logged_in()) {
                wp_redirect($redirect);
                exit();
            }

            switch ($social):
                case self::SOCIAL_VK:
                    return $this->process_auth_vk($request, $redirect);
                    break;
                case self::SOCIAL_FACEBOOK:
                    return $this->process_auth_facebook($request, $redirect);
                    break;
                case self::SOCIAL_TWITTER:
                    return $this->process_auth_twitter($request, $redirect);
                    break;
                case self::SOCIAL_GOOGLE:
                    return $this->process_auth_google($request, $redirect);
                    break;
                default:
                    wp_redirect($redirect);
                    exit();
            endswitch;
        }

        /**
         * Get REST url + redirect url with it.
         * @param string $social_type URL type, e.g. vk
         * @param string|null $redirect Redirect URL where to send back user.
         * @return string
         */
        public static function get_callback_url($social_type, $redirect = null)
        {
            $url = static::get_rest_namespace() . "/auth/" . $social_type;

            if ($redirect !== null) {
                $url .= "?redirect=$redirect";
            }
            return rest_url($url);
        }

        /**
         * Process Google authorization.
         * @param WP_REST_Request $request
         * @param null $redirect
         */
        private function process_auth_google(WP_REST_Request $request, $redirect = null, $cookie_redirect = 'post_redirect')
        {
            $google_rest_url = static::get_callback_url(self::SOCIAL_GOOGLE);


            if ($redirect !== null) {
                setcookie($cookie_redirect, $redirect, time() + 3600);
            }

            $client = $this->auth_google;
            $client->setRedirectUri($google_rest_url);

            if ($request->get_param('code') === null) {
                wp_redirect($client->createAuthUrl([
                    Google_Service_Plus::PLUS_ME,
                    Google_Service_Plus::USERINFO_EMAIL,
                    Google_Service_Plus::USERINFO_PROFILE
                ]));
                exit();
            } else {

                $redirect = $_COOKIE[$cookie_redirect];

                $client->setRedirectUri($google_rest_url);
                $client->fetchAccessTokenWithAuthCode($request->get_param('code'));

                $googlePlus = new Google_Service_Plus($client);

                $user = $googlePlus->people->get('me');

                if (!$user instanceof Google_Service_Plus_Person) {
                    wp_redirect($redirect);
                    exit();
                }

                $email = isset($user->getEmails()[0]) && !empty($user->getEmails()[0]->getValue()) ?
                    $user->getEmails()[0]->getValue() :
                    null;

                $userdata = [
                    'user_login' => $user->getId(),
                    'display_name' => $user->getDisplayName(),
                    'user_nicename' => $user->getNickname() !== null ? $user->getNickname() : $user->getName()->getGivenName(),
                    'user_url' => $user->getUrl()
                ];


                if ($email !== null) {
                    $userdata['user_email'] = $email;
                }

                $searchBy = $email !== null ? 'email' : 'login';
                $searchValue = $email !== null ? $email : $user->getId();

                $this->auth_user(
                    $searchBy,
                    $searchValue,
                    $userdata,
                    [
                        'anycomment_social' => self::SOCIAL_GOOGLE,
                        'anycomment_social_avatar' => $user->getImage()->getUrl(),
                    ]
                );

                wp_redirect($redirect);
                exit();
            }
        }

        /**
         * Process Facebook authorization.
         * @param WP_REST_Request $request
         * @param null $redirect
         */
        private function process_auth_facebook(WP_REST_Request $request, $redirect = null, $cookie_redirect = 'post_redirect')
        {
            $helper = $this->auth_facebook->getRedirectLoginHelper();
            $facebook_rest_url = static::get_callback_url(self::SOCIAL_FACEBOOK);

            if ($redirect !== null) {
                setcookie($cookie_redirect, $redirect, time() + 3600);
            }

            if ($request->get_param('code') === null) {
                $loginUrl = $helper->getLoginUrl($facebook_rest_url, ['email']);
                wp_redirect($loginUrl);
                exit();
            } else {

                $redirect = $_COOKIE[$cookie_redirect];

                $helper->getPersistentDataHandler()->set('state', $_GET['state']);

                try {
                    $accessToken = $helper->getAccessToken();
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    wp_redirect($redirect);
                    exit;
                }

                if (!isset($accessToken) || empty($accessToken)) {
                    wp_redirect($redirect);
                    exit;
                }

                try {
                    $response = $this->auth_facebook->get('/me?fields=id,first_name,last_name,picture', $accessToken);

                    $user = $response->getGraphUser();
                } catch (\Facebook\Exceptions\FacebookSDKException $exception) {
                    wp_redirect($redirect);
                    exit();
                }

                $this->auth_user(
                    'login',
                    $user->getId(),
                    [
                        'user_login' => $user->getId(),
                        'display_name' => $user->getFirstName() . ' ' . $user->getLastName(),
                        'user_nicename' => $user->getFirstName(),
                    ],
                    [
                        'anycomment_social' => self::SOCIAL_FACEBOOK,
                        'anycomment_social_avatar' => $user->getPicture()->getUrl(),
                    ]
                );

                wp_redirect($redirect);
                exit();
            }
        }


        /**
         * Process Twitter authorization.
         * @param WP_REST_Request $request
         * @param null $redirect
         */
        private function process_auth_twitter(WP_REST_Request $request, $redirect = null)
        {
            if ($request->get_param('oauth_token') === null && $request->get_param('oauth_verifier') === null) {
                $twitter_rest_url = static::get_callback_url(self::SOCIAL_TWITTER, $redirect);

                try {
                    $request_token = $this->auth_twitter->oauth('oauth/request_token', ['oauth_callback' => $twitter_rest_url]);
                } catch (\Abraham\TwitterOAuth\TwitterOAuthException $exception) {
                    wp_redirect($redirect);
                    exit();
                }


                $url = $this->auth_twitter->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]);

                wp_redirect($url);
                exit();
            } else {
                $oauthToken = $request->get_param('oauth_token');
                $oauthVerifier = $request->get_param('oauth_verifier');

                try {
                    /**
                     * array(4) {
                     * ["oauth_token"]=>
                     * string(50) "1-3O8NhgskfKJrlre63EvdEY20rBRbpaQ76uY0pZBt"
                     * ["oauth_token_secret"]=>
                     * string(45) "v1kQlVOKTSz46xqSE5cvigKYR58PbocCq1jEM4pZ8P5WWH"
                     * ["user_id"]=>
                     * string(9) "614735741"
                     * ["screen_name"]=>
                     * string(10) "ateshabaev"
                     * }
                     */
                    $this->auth_twitter = new \Abraham\TwitterOAuth\TwitterOAuth(
                        AC_SocialSettingPage::getTwitterConsumerKey(),
                        AC_SocialSettingPage::getTwitterConsumerSecret(),
                        $oauthToken,
                        $oauthVerifier
                    );

                    $access_token = $this->auth_twitter->oauth("oauth/access_token", ["oauth_verifier" => $oauthVerifier]);
                } catch (\Abraham\TwitterOAuth\TwitterOAuthException $exception) {
                    wp_redirect($redirect);
                    exit();
                }

                $this->auth_twitter = new \Abraham\TwitterOAuth\TwitterOAuth(
                    AC_SocialSettingPage::getTwitterConsumerKey(),
                    AC_SocialSettingPage::getTwitterConsumerSecret(),
                    $access_token['oauth_token'],
                    $access_token['oauth_token_secret']
                );

                $user = $this->auth_twitter->get('account/verify_credentials', ['tweet_mode' => 'extended', 'include_entities' => 'true']);

                if (!is_object($user)) {
                    wp_redirect($redirect);
                    exit();
                }

                $fields = [];
                $fields['id'] = $user->id;

                if (isset($user->email)) {
                    $fields['user_email'] = $user->email;
                }

                $fields['display_name'] = $user->name;
                $fields['user_nicename'] = $user->name;


                $this->auth_user(
                    'login',
                    $user->id_str,
                    $fields,
                    [
                        'anycomment_social' => self::SOCIAL_TWITTER,
                        // Margin to get bigger size avatar
                        // https://developer.twitter.com/en/docs/accounts-and-users/manage-account-settings/api-reference/get-account-verify_credentials.html
                        'anycomment_social_avatar' => str_replace('_normal.jpg', '_bigger.jpg', $user->profile_image_url_https),
                        'anycomment_social_twitter_oauth_token' => $access_token['oauth_token'],
                        'anycomment_social_twitter_oauth_token_secret' => $access_token['oauth_token_secret'],
                    ]
                );

                wp_redirect($redirect);
                exit();
            }
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
                    wp_redirect($redirect);
                    exit();
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

                $this->auth_user(
                    'email',
                    $vkUser['email'],
                    [
                        'user_login' => $vkUser['user_id'],
                        'user_email' => $vkUser['email'],
                        'display_name' => trim($vkUser['first_name'] . ' ' . $vkUser['last_name']),
                        'user_nicename' => empty($vkUser['first_name']) ? $vkUser['user_id'] : $vkUser['first_name'],
                        'user_url' => 'https://vk.com/id' . $vkUser['user_id']
                    ],
                    [
                        'anycomment_social' => self::SOCIAL_VK,
                        'anycomment_social_avatar' => $vkUser['photo_50'],
                        'anycomment_social_vk_access_token' => $vkUser['access_token'],
                        'anycomment_social_vk_expires_in' => $vkUser['expires_in'],
                        'anycomment_social_vk_sex' => $vkUser['sex'],
                    ]
                );

                wp_redirect($redirect);
                exit();
            }
        }

        /**
         * Authenticate user with additional check whether such
         * user already exists or not. When user does not exist,
         * it create it. When exists, uses existing information to authorize.
         *
         * @see get_user_by() for more information about $field and $value fields.
         *
         * @param string $field Field name to be used to check existance of such user.
         * @param string $value Value of the $field to be checked for.
         * @param array $userdata User data to be created for user, when does not exist.
         * @param array $usermeta User meta data to be create for user, when does not exist.
         * @return bool
         */
        public function auth_user($field, $value, array $userdata, array $usermeta)
        {
            wp_clear_auth_cookie();

            $user = get_user_by($field, $value);

            if ($user === false) {
                $newUserId = $this->insert_user($userdata, $usermeta);

                if (!$newUserId) {
                    return false;
                }

                $userId = $newUserId;
            } else {
                $userId = $user->ID;
                $this->update_user_meta($user->ID, $usermeta);
            }

            wp_clear_auth_cookie();
            wp_set_current_user($userId);
            wp_set_auth_cookie($userId, true);

            return true;
        }

        /**
         * @param array $userdata See options below:
         * - user_login
         * - user_email
         * - display_name
         * - user_nicename
         * - user_url
         * @param array $usermeta User meta to be added when `$userdata` added successfully.
         * @return false|int false on failure, int on success.
         *
         * @see wp_generate_password() for how password is being generated.
         * @see wp_insert_user() for how `$userdata` param is being processed.
         * @see add_user_meta() for how `$usermeta` param is being processed.
         */
        public function insert_user($userdata, $usermeta)
        {
            if (!isset($userdata['userpass'])) {
                // Generate some random password for user
                $userdata['user_pass'] = wp_generate_password(12, true);
            }

            $newUserId = wp_insert_user($userdata);

            // If unable to create new user
            if ($newUserId instanceof WP_Error) {
                return false;
            }

            $metaInsertCount = 0;
            foreach ($usermeta as $key => $value) {
                if (add_user_meta($newUserId, $key, $value)) {
                    $metaInsertCount++;
                }
            }

            // If number of inserted metas is less then was requested to add
            if ($metaInsertCount < count($usermeta)) {
                return false;
            }

            return $newUserId;
        }

        /**
         * Update user meta by ID.
         *
         * @see update_user_meta() when meta does not exist, it will be added. See for more info.
         *
         * @param int $user_id User's ID.
         * @param array $usermeta List of user meta to be updated.
         * @return bool
         */
        public function update_user_meta($user_id, $usermeta)
        {
            if (!get_user_by('id', $user_id) || !is_array($usermeta) || !empty($usermeta)) {
                return false;
            }

            $metaUpdateCount = 0;
            foreach ($usermeta as $key => $value) {
                if (update_user_meta($user_id, $key, $value)) {
                    $metaUpdateCount++;
                }
            }

            // If number of inserted metas is less then was requested to add
            if ($metaUpdateCount < count($usermeta)) {
                return false;
            }

            return true;
        }

        /**
         * Get current user avatar URL.
         *
         * @return string|null NULL returned when user does not have any avatar.
         */
        public function get_active_user_avatar_url()
        {
            if (($user = wp_get_current_user()) instanceof WP_User) {

                $avatarUrl = get_user_meta($user->ID, self::META_SOCIAL_AVATAR, true);

                if (empty($avatarUrl)) {
                    return AnyComment()->plugin_url() . '/assets/img/no-avatar.svg';
                }

                return $avatarUrl;
            }

            return null;
        }

        /**
         * Get user avatar by user id.
         *
         * @param int $user_id User ID to be searched for.
         * @return mixed|null
         */
        public function get_user_avatar_url($user_id)
        {
            $avatarUrl = get_user_meta($user_id, self::META_SOCIAL_AVATAR, true);

            if (!empty($avatarUrl)) {
                return $avatarUrl;
            }

            $avatarUrl = AnyComment()->plugin_url() . '/assets/img/no-avatar.svg';

            return $avatarUrl;
        }
    }
endif;