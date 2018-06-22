<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AnyCommentAdminPages')) :
    /**
     * AnyCommentAdminPages helps to process website authentication.
     */
    class AnyCommentAdminPages
    {
        /**
         * @var AnyCommentSocialSettings
         */
        public $page_options_social;

        /**
         * @var AnyCommentGenericSettings
         */
        public $page_options_general;

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
            $this->page_options_social = new AnyCommentSocialSettings();
            $this->page_options_general = new AnyCommentGenericSettings();
        }

        /**
         * Initiate hooks.
         */
        private function init_hooks()
        {
            add_action('admin_menu', [$this, 'add_menu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_dashboard_scripts']);
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
                AnyComment()->plugin_url() . '/assets/img/admin-menu-logo.png'
            );


        }

        /**
         * Display dashboard page.
         */
        public function page_dashboard()
        {
            echo anycomment_get_template('admin/dashboard');
        }

        /**
         * Load dashboard styles & scripts.
         */
        public function enqueue_dashboard_scripts()
        {
            wp_enqueue_style('anycomment-admin-styles', AnyComment()->plugin_url() . '/assets/css/admin.css', [], AnyComment()->version);
            wp_enqueue_style('anycomment-admin-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:300,400,700&amp;subset=cyrillic');
            wp_enqueue_script('anycomment-admin-chartjs', AnyComment()->plugin_url() . '/assets/js/Chart.min.js', [], AnyComment()->version);
        }
    }
endif;

