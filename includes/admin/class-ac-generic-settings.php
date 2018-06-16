<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AC_GenericSettings')) :
    /**
     * AC_AdminSettingPage helps to process generic plugin settings.
     */
    class AC_GenericSettings extends AC_Options
    {
        /**
         * @inheritdoc
         */
        protected $option_group = 'anycomment-generic-group';
        /**
         * @inheritdoc
         */
        protected $option_name = 'anycomment-generic';
        /**
         * @inheritdoc
         */
        protected $page_slug = 'anycomment-settings';


        const OPTION_THEME_TOGGLE = 'option_theme_toggle';
        const OPTION_PLUGIN_TOGGLE = 'option_plugin_toggle';

        const THEME_DARK = 'dark';
        const THEME_LIGHT = 'light';

        /**
         * AnyCommentAdminPages constructor.
         * @param bool $init if required to init the modle.
         */
        public function __construct($init = true)
        {
            parent::__construct();
            if ($init) {
                $this->init_hooks();
            }
        }

        /**
         * Initiate hooks.
         */
        private function init_hooks()
        {
            add_action('admin_menu', [$this, 'add_menu']);
            add_action('admin_init', [$this, 'init_settings']);
        }

        /**
         * Init admin menu.
         */
        public function add_menu()
        {
            add_submenu_page(
                'anycomment-dashboard',
                __('Settings', "anycomment"),
                __('Settings', "anycomment"),
                'manage_options',
                $this->page_slug,
                [$this, 'page_html']
            );
        }

        /**
         * {@inheritdoc}
         */
        public function init_settings()
        {
            add_settings_section(
                'section_generic',
                __('Generic', "anycomment"),
                function () {
                    echo '<p>' . __('Generic settings.', "anycomment") . '</p>';
                },
                $this->page_slug
            );

            $this->render_fields(
                $this->page_slug,
                'section_generic',
                [
                    [
                        'id' => self::OPTION_PLUGIN_TOGGLE,
                        'title' => __('Enable Comments', "anycomment"),
                        'callback' => 'input_checkbox',
                        'description' => esc_html(__('Visible plugin or not. When off default comments of your website will be shown instead. Could be useful to configure comments first and then enable this option.', "anycomment"))
                    ],
                    [
                        'id' => self::OPTION_THEME_TOGGLE,
                        'title' => __('Theme', "anycomment"),
                        'callback' => 'input_select',
                        'args' => [
                            'options' => [
                                self::THEME_DARK => __('Dark', 'anycomment'),
                                self::THEME_LIGHT => __('Light', 'anycomment'),
                            ]
                        ],
                        'description' => esc_html(__('Choose theme of the comments.', "anycomment"))
                    ],
                ]
            );
        }

        /**
         * Check whether plugin is enabled or not.
         *
         * @return bool
         */
        public static function isEnabled()
        {
            return static::instance()->getOption(self::OPTION_PLUGIN_TOGGLE) !== null;
        }

        /**
         * Get currently chosen theme.
         * When value store is not matching any of the existing
         * themes -> returns `dark` as default.
         *
         * @return string|null
         */
        public static function getTheme()
        {
            $value = static::instance()->getOption(self::OPTION_THEME_TOGGLE);

            if ($value === null || $value !== self::THEME_DARK && $value !== self::THEME_LIGHT) {
                return self::THEME_DARK;
            }

            return $value;
        }
    }
endif;

