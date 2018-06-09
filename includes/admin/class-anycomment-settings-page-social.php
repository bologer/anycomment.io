<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AC_SocialSettingPage')) :
    /**
     * AnyCommentAdminPages helps to process website authentication.
     */
    class AC_SocialSettingPage
    {
        const OPTION_GROUP = 'anycomment-social-group';
        const OPTION_NAME = 'anycomment-social';

        const PAGE_SLUG = 'anycomments-settings-social';

        const ALERT_KEY = 'social-settings-messages';

        /**
         * VK Options
         */
        const OPTION_VK_TOGGLE = 'social_vk_toggle_field';
        const OPTION_VK_APP_ID = 'social_vk_app_id_field';
        const OPTION_VK_SECRET = 'social_vk_app_secret_field';

        /**
         * Twitter options
         */
        const OPTION_TWITTER_TOGGLE = 'social_twitter_toggle_field';
        const OPTION_TWITTER_CONSUMER_KEY = 'social_twitter_consumer_key_field';
        const OPTION_TWITTER_CONSUMER_SECRET = 'social_twitter_consumer_secret_field';

        /**
         * Facebook Options
         */
        const OPTION_FACEBOOK_TOGGLE = 'social_facebook_toggle_field';
        const OPTION_FACEBOOK_APP_ID = 'social_facebook_app_id_field';
        const OPTION_FACEBOOK_APP_SECRET = 'social_facebook_app_secret_field';

        /**
         * Google Options
         */
        const OPTION_GOOGLE_TOGGLE = 'social_google_toggle_field';
        const OPTION_GOOGLE_CLIENT_ID = 'social_google_client_id_field';
        const OPTION_GOOGLE_SECRET = 'social_google_secret_field';


        /**
         * @var array List of available options.
         */
        public static $options = null;

        /**
         * AnyCommentAdminPages constructor.
         */
        public function __construct()
        {
            $this->init_hooks();
        }

        /**
         * Initiate hooks.
         */
        private function init_hooks()
        {
            add_action('admin_init', [$this, 'init_settings']);
            add_action('admin_menu', [$this, 'init_submenu']);
        }

        /**
         * Init admin menu.
         */
        public function init_submenu()
        {
            add_submenu_page(
                'anycomments-dashboard',
                __('Social Settings', "anycomment"),
                __('Social Settings', "anycomment"),
                'manage_options',
                self::PAGE_SLUG,
                [$this, 'page_html']
            );
        }

        public function init_settings()
        {
            register_setting(self::OPTION_GROUP, self::OPTION_NAME);

            /**
             * VK
             */
            add_settings_section(
                'section_vk',
                __('VK', "anycomment"),
                function () {
                    ?>
                    <p><?= __('VK authorization settings.', "anycomment") ?></p>
                    <?php
                },
                self::PAGE_SLUG
            );

            $this->render_fields(
                self::PAGE_SLUG,
                'section_vk',
                [
                    [
                        'id' => self::OPTION_VK_TOGGLE,
                        'title' => __('Enable', "anycomment"),
                        'callback' => 'input_checkbox',
                        'description' => esc_html(__('Allow VK authorization', "anycomment"))
                    ],
                    [
                        'id' => self::OPTION_VK_APP_ID,
                        'title' => __('Application ID', "anycomment"),
                        'callback' => 'input_text',
                        'description' => sprintf(__('Enter app id. Can be found in <a href="%s" target="_blank">apps</a> page', "anycomment"), 'https://vk.com/apps?act=manage')
                    ],
                    [
                        'id' => self::OPTION_VK_SECRET,
                        'title' => __('Secure key', "anycomment"),
                        'callback' => 'input_text',
                        'description' => sprintf(__('Enter secure key. Can be found in <a href="%s" target="_blank">apps</a> page', "anycomment"), 'https://vk.com/apps?act=manage')
                    ]
                ]
            );

            /**
             * Twitter
             */
            add_settings_section(
                'section_twitter',
                __('Twitter', "anycomment"),
                function () {
                    ?>
                    <p><?= __('Twitter authorization settings.', "anycomment") ?></p>
                    <?php
                },
                self::PAGE_SLUG
            );

            $this->render_fields(
                self::PAGE_SLUG,
                'section_twitter',
                [
                    [
                        'id' => self::OPTION_TWITTER_TOGGLE,
                        'title' => __('Enable', "anycomment"),
                        'callback' => 'input_checkbox',
                        'description' => __('Allow Twitter authorization', "anycomment")
                    ],
                    [
                        'id' => self::OPTION_TWITTER_CONSUMER_KEY,
                        'title' => __('Consumer Key', "anycomment"),
                        'callback' => 'input_text',
                        'description' => sprintf(__('Enter consumer key. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment"), 'https://apps.twitter.com/')
                    ],
                    [
                        'id' => self::OPTION_TWITTER_CONSUMER_SECRET,
                        'title' => __('Consumer Secret', "anycomment"),
                        'callback' => 'input_text',
                        'description' => sprintf(__('Enter consumer secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment"), 'https://apps.twitter.com/')
                    ]
                ]
            );

            /**
             * Facebook
             */
            add_settings_section(
                'section_facebook',
                __('Facebook', "anycomment"),
                function () {
                    ?>
                    <p><?= __('Facebook authorization settings.', "anycomment") ?></p>
                    <?php
                },
                self::PAGE_SLUG
            );

            $this->render_fields(
                self::PAGE_SLUG,
                'section_facebook',
                [
                    [
                        'id' => self::OPTION_FACEBOOK_TOGGLE,
                        'title' => __('Enable', "anycomment"),
                        'callback' => 'input_checkbox',
                        'description' => __('Allow Facebook authorization', "anycomment")
                    ],
                    [
                        'id' => self::OPTION_FACEBOOK_APP_ID,
                        'title' => __('App ID', "anycomment"),
                        'callback' => 'input_text',
                        'description' => sprintf(__('Enter app id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment"), 'https://developers.facebook.com/apps/')
                    ],
                    [
                        'id' => self::OPTION_FACEBOOK_APP_SECRET,
                        'title' => __('App Secret', "anycomment"),
                        'callback' => 'input_text',
                        'description' => sprintf(__('Enter app secret. Can be found in the list of <a href="%" target="_blank">apps</a>', "anycomment"), 'https://developers.facebook.com/apps/')
                    ]
                ]
            );

            /**
             * Google
             */
            add_settings_section(
                'section_google',
                __('Google', "anycomment"),
                function () {
                    ?>
                    <p><?= __('Google authorization settings.', "anycomment") ?></p>
                    <?php
                },
                self::PAGE_SLUG
            );

            $this->render_fields(
                self::PAGE_SLUG,
                'section_google',
                [
                    [
                        'id' => self::OPTION_GOOGLE_TOGGLE,
                        'title' => __('Enable', "anycomment"),
                        'callback' => 'input_checkbox',
                        'description' => __('Allow Google authorization', "anycomment")
                    ],
                    [
                        'id' => self::OPTION_GOOGLE_CLIENT_ID,
                        'title' => __('Client ID', "anycomment"),
                        'callback' => 'input_text',
                        'description' => sprintf(__('Enter client id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment"), 'https://console.developers.google.com/apis/credentials')
                    ],
                    [
                        'id' => self::OPTION_GOOGLE_SECRET,
                        'title' => __('Client Secret', "anycomment"),
                        'callback' => 'input_text',
                        'description' => sprintf(__('Enter client secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment"), 'https://console.developers.google.com/apis/credentials')
                    ]
                ]
            );
        }

        /**
         *
         * @param $page
         * @param $section_id
         * @param array $fields
         */
        public function render_fields($page, $section_id, array $fields)
        {
            foreach ($fields as $field) {

                $args = isset($field['args']) ? $field['args'] : [];

                if (!isset($args['label_for'])) {
                    $args['label_for'] = $field['id'];
                }

                if (!isset($args['description'])) {
                    $args['description'] = $field['description'];
                }

                add_settings_field(
                    $field['id'],
                    $field['title'],
                    [$this, $field['callback']],
                    $page,
                    $section_id,
                    $args
                );
            }
        }

        public function input_checkbox($args)
        {
            ?>
            <input type="checkbox" id="<?= esc_attr($args['label_for']); ?>"
                   name="<?= self::OPTION_NAME ?>[<?= esc_attr($args['label_for']); ?>]" <?= $this->getOption($args['label_for']) !== null ? 'checked="checked"' : '' ?>>
            <?php if (isset($args['description'])): ?>
            <p class="description"><?= $args['description'] ?></p>
        <?php endif; ?>
            <?php
        }

        public function input_text($args)
        {
            ?>
            <input type="text" id="<?= esc_attr($args['label_for']); ?>"
                   name="<?= self::OPTION_NAME ?>[<?= esc_attr($args['label_for']); ?>]"
                   value="<?= $this->getOption($args['label_for']) ?>">
            <?php if (isset($args['description'])): ?>
            <p class="description"><?= $args['description'] ?></p>
        <?php endif; ?>
            <?php
        }

        /**
         * top level menu:
         * callback functions
         */
        public function page_html()
        {
            // check user capabilities
            if (!current_user_can('manage_options')) {
                return;
            }

            // check if the user have submitted the settings
            // wordpress will add the "settings-updated" $_GET parameter to the url
            if (isset($_GET['settings-updated'])) {
                // add settings saved message with the class of "updated"
                add_settings_error(self::ALERT_KEY, 'wporg_message', __('Settings Saved', 'anycomment'), 'updated');
            }

            // show error/update messages
            settings_errors(self::ALERT_KEY);
            ?>
            <div class="wrap">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                <p><?= __('On this page you may configure social authorization. If social is disabled, users will be unable to login with it.', 'anycomment') ?></p>
                <form action="options.php" method="post">
                    <?php
                    settings_fields(self::OPTION_GROUP);
                    do_settings_sections(self::PAGE_SLUG);
                    submit_button(__('Save', 'anycomment'));
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Get single option.
         * @param string $name Options name to search for.
         * @return mixed|null
         */
        public static function getOption($name)
        {
            $options = static::getOptions();

            $optionValue = isset($options[$name]) ? trim($options[$name]) : null;

            return !empty($optionValue) ? $optionValue : null;
        }

        /**
         * Get list of social options.
         * @return array|null
         */
        public static function getOptions()
        {
            if (self::$options === null) {
                self::$options = get_option(self::OPTION_NAME, null);
            }


            return self::$options;
        }

        /**
         * Check whether VK social is on.
         * @return bool
         */
        public static function isVkOn()
        {
            return static::getOption(self::OPTION_VK_TOGGLE) !== null;
        }

        /**
         * Get VK App ID.
         * @return int|null
         */
        public static function getVkAppId()
        {
            return static::getOption(self::OPTION_VK_APP_ID);
        }

        /**
         * Get VK Secure key.
         * @return string|null
         */
        public static function getVkSecureKey()
        {
            return static::getOption(self::OPTION_VK_SECRET);
        }


        /**
         * Check whether Twitter is on.
         * @return bool
         */
        public static function isTwitterOn()
        {
            return static::getOption(self::OPTION_TWITTER_TOGGLE) !== null;
        }

        /**
         * Get Twitter consumer key.
         * @return string|null
         */
        public static function getTwitterConsumerKey()
        {
            return static::getOption(self::OPTION_TWITTER_CONSUMER_KEY);
        }

        /**
         * Get Twitter consumer secret.
         * @return string|null
         */
        public static function getTwitterConsumerSecret()
        {
            return static::getOption(self::OPTION_TWITTER_CONSUMER_SECRET);
        }

        /**
         * Check whether Facebook social is on.
         * @return bool
         */
        public static function isFbOn()
        {
            return static::getOption(self::OPTION_FACEBOOK_TOGGLE) !== null;
        }

        /**
         * Get Facebook App ID.
         * @return int|null
         */
        public static function getFbAppId()
        {
            return static::getOption(self::OPTION_FACEBOOK_APP_ID);
        }

        /**
         * Get Facebook Secure key.
         * @return string|null
         */
        public static function getFbAppSecret()
        {
            return static::getOption(self::OPTION_FACEBOOK_APP_SECRET);
        }

        /**
         * Check whether Google social is on.
         * @return bool
         */
        public static function isGoogleOn()
        {
            return static::getOption(self::OPTION_GOOGLE_TOGGLE) !== null;
        }

        /**
         * Get Google Client ID.
         * @return int|null
         */
        public static function getGoogleClientId()
        {
            return static::getOption(self::OPTION_GOOGLE_CLIENT_ID);
        }

        /**
         * Get Google secret key.
         * @return string|null
         */
        public static function getGoogleSecret()
        {
            return static::getOption(self::OPTION_GOOGLE_SECRET);
        }
    }
endif;

