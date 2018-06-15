<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AC_AdminSettingPage')) :
    /**
     * AC_AdminSettingPage helps to process generic plugin settings.
     */
    class AC_AdminSettingPage
    {

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
                'anycomment-dashboard',
                [$this, 'page_dashboard'],
                AnyComment()->plugin_url() . '/assets/img/admin-menu-logo.svg'
            );
        }

        public function page_dashboard()
        {
            wp_enqueue_style('anycomment-admin-styles', AnyComment()->plugin_url() . '/assets/css/admin.css', [], AnyComment()->version);
            wp_enqueue_style('anycomment-admin-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:300,400,700&amp;subset=cyrillic');
            wp_enqueue_script('anycomment-admin-chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js');

            echo ec_get_template('admin/dashboard');
        }
    }
endif;

