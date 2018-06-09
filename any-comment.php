<?php

/**
 * Plugin Name: AnyComment
 * Plugin URI: https://anycomment.io
 * Description: AnyComment is an advanced commenting system for WordPress.
 * Version: 0.1
 * Author: Bologer
 * Author URI: http://bologer.ru
 * Requires at least: 4.4
 * Tested up to: 4.7
 *
 * Text Domain: anycomment
 * Domain Path: /languages
 *
 * @package AnyComment
 * @author bologer
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AnyComment')) :

    /**
     * Main EasyComment Class.
     *
     */
    class AnyComment
    {

        /**
         * EasyComment version.
         *
         * @var string
         */
        public $version = '0.1';

        /**
         * Instance of render class.
         *
         * @var null|AC_Render
         */
        public $render = null;

        /**
         * @var null|AC_SocialAuth
         */
        public $auth = null;

        /**
         * Generic class prefix for all plugin HTML elements.
         * @var string
         */
        public $classPrefix = 'anycomment-';

        /**
         * @var null|AC_AdminPages
         */
        public $admin_pages = null;

        /**
         * Instance of EasyComment.
         * @var null|AnyComment
         */
        private static $_instance = null;


        /**
         * Post ID when available.
         * @var null|WP_Post
         */
        public $currentPost = null;

        /**
         * AnyComment constructor.
         */
        public function __construct()
        {
            $this->init();

            do_action('any_comment_loaded');
        }

        /**
         * Init method to invoke starting scripts.
         */
        public function init()
        {
            $this->define_constants();
            $this->includes();
            $this->init_textdomain();
            $this->init_hooks();
        }

        public function init_textdomain()
        {
            load_plugin_textdomain("anycomment", false, basename(dirname(__FILE__)) . '/languages');
        }

        /**
         * Main EasyComment Instance.
         *
         * Ensures only one instance of EasyComment is loaded or can be loaded.
         *
         * @since 2.1
         * @static
         * @see AnyComment()
         * @return AnyComment Instance of plugin.
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Initiate hooks.
         */
        private function init_hooks()
        {
        }

        /**
         * Define EasyComment Constants.
         */
        private function define_constants()
        {
            $this->define('ANY_COMMENT_PLUGIN_FILE', __FILE__);
            $this->define('ANY_COMMENT_LANG', __FILE__);
            $this->define('ANY_COMMENT_ABSPATH', dirname(__FILE__) . '/');
            $this->define('ANY_COMMENT_PLUGIN_BASENAME', plugin_basename(__FILE__));
            $this->define('ANY_COMMENT_VERSION', $this->version);
            $this->define('ANY_COMMENT_DEBUG', false);
        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define($name, $value)
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        /**
         * Get the plugin url.
         * @return string
         */
        public function plugin_url()
        {
            return untrailingslashit(plugins_url('/', __FILE__));
        }

        /**
         * Get the plugin path.
         * @return string
         */
        public function plugin_path()
        {
            return untrailingslashit(plugin_dir_path(__FILE__));
        }

        /**
         * Get Ajax URL.
         * @return string
         */
        public function ajax_url()
        {
            return admin_url('admin-ajax.php', 'relative');
        }

        /**
         * Returns generic class prefix that should be used on all HTML elements.
         *
         * @return string
         */
        public function classPrefix()
        {
            return $this->classPrefix;
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes()
        {
            /**
             * Class autoloader.
             */
            include_once(ANY_COMMENT_ABSPATH . 'includes/class-ec-render.php');
            include_once(ANY_COMMENT_ABSPATH . 'includes/core-functions.php');
            include_once(ANY_COMMENT_ABSPATH . 'includes/api/class-anyc-social-auth.php');

            /**
             * Admin related
             */
            include_once(ANY_COMMENT_ABSPATH . 'includes/admin/class-anycomment-pages.php');
            include_once(ANY_COMMENT_ABSPATH . 'includes/admin/class-anycomment-settings-page-social.php');


            /**
             * Authentication social libraries
             */

            if (AC_SocialSettingPage::isVkOn()) {
                // VK
                include_once(ANY_COMMENT_ABSPATH . 'includes/api/vk/src/VK/VK.php');
                include_once(ANY_COMMENT_ABSPATH . 'includes/api/vk/src/VK/VKException.php');
            }


            if (AC_SocialSettingPage::isTwitterOn()) {
                include_once(ANY_COMMENT_ABSPATH . 'includes/api/twitter/autoload.php'); //// Twitter
            }

            if (AC_SocialSettingPage::isFbOn()) {
                include_once(ANY_COMMENT_ABSPATH . 'includes/api/facebook/src/Facebook/autoload.php'); // Facebook
            }

            if (AC_SocialSettingPage::isGoogleOn()) {
                include_once(ANY_COMMENT_ABSPATH . 'includes/api/google/vendor/autoload.php'); // Google
            }


            $this->render = new AC_Render();
            $this->admin_pages = new AC_AdminPages();
            $this->auth = new AC_SocialAuth();
        }

        /**
         * Fail response.
         * @param string $error Error for response.
         * @return string JSON fail response.
         */
        public function json_error($error, $response = [])
        {
            return $this->json_response(false, $response, $error);
        }

        /**
         * Success response.
         * @param array $response Specify custom response params.
         * @return string
         */
        public function json_success($response = [])
        {
            return $this->json_response(true, $response);
        }

        /**
         * @param bool $success Whether response is success or not.
         * @param array $response Specify custom response
         * @param $error
         * @return string JSON string.
         */
        public function json_response($success = true, $response = [], $error = null)
        {
            return json_encode([
                'success' => (bool)$success,
                'response' => json_encode($response),
                'error' => $error,
                'time' => time()
            ]);
        }

        /**
         * Set post object by having post.
         * @param int $postId
         */
        public function setCurrentPost($postId = null)
        {
            if ($postId === null) {
                $this->currentPost = get_post();
            } else {
                if (($post = get_post($postId)) !== null) {
                    $this->currentPost = $post;
                }
            }
        }

        /**
         * Post when available.
         * @return null|WP_Post
         */
        public function getCurrentPost()
        {
            return $this->currentPost;
        }
    }
endif;

function AnyComment()
{
    return AnyComment::instance();
}

AnyComment();