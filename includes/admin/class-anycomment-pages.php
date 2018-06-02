<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AC_AdminPages')) :
    /**
     * AnyCommentAdminPages helps to process website authentication.
     */
    class AC_AdminPages
    {
        /**
         * @var AC_SocialSettingPage
         */
        public $page_options_social;

        /**
         * AnyCommentAdminPages constructor.
         */
        public function __construct()
        {
            $this->init_hooks();
            $this->init();
        }

        /**
         * Include pages.
         */
        private function init()
        {
            $this->page_options_social = new AC_SocialSettingPage();
        }

        /**
         * Initiate hooks.
         */
        private function init_hooks()
        {
            add_action('admin_menu', [$this, 'add_menu']);
        }

        /**
         * Init admin menu.
         */
        public function add_menu()
        {
            add_menu_page(
                __('AnyComment', "anycomment"),
                __('AnyComment', "anycomment"),
                'manage_options',
                'anycomments-dashboard',
                [$this, 'page_dashboard'],
                ''
            );
        }

        public function page_dashboard()
        {
            echo '123';
        }
    }
endif;

