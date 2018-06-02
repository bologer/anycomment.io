<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('EasyCommentAuthVK')) :
    /**
     * EasyCommentAuthVK helps to process website authentication.
     */
    class EasyCommentAuthVK
    {
        /**
         * EasyCommentAuthVK constructor.
         */
        public function __construct()
        {
            $this->init_hooks();
        }

        /**
         * Initiate init hooks.
         */
        private function init_hooks()
        {
            add_action('wp_ajax_nopriv_auth_vk', [$this, 'auth_vk']);
        }

        /**
         * Process VK authentication process.
         */
        public function auth_vk()
        {
            check_ajax_referer('auth-vk-nonce');
            $postId = trim(sanitize_text_field($_POST['postId']));
            $hash = trim(sanitize_text_field($_POST['hash']));
            $firstName = trim(sanitize_text_field($_POST['firstName']));
            $lastName = trim(sanitize_text_field($_POST['lastName']));

            if (empty($postId)) {
                echo AnyComment()->json_error(__("No post ID specified"));
                wp_die();
            }

            if (!get_post_status($postId)) {
                echo AnyComment()->json_error(sprintf(__("Unable to find post with ID #%s"), $postId));
                wp_die();
            }

            if (empty($hash)) {
                echo AnyComment()->json_error(__('Unable to find required hash'));
                wp_die();
            }

            $args = explode('&', $hash);

            foreach ($args as $key => $arg) {
                $tmp = explode('=', $arg);
                if (isset($tmp[0]) && isset($tmp[1])) {
                    $args[$tmp[0]] = trim($tmp[1]);
                    unset($args[$key]);
                }
            }

            wp_clear_auth_cookie();

            $user = get_user_by('email', $args['email']);

            if ($user === false) {
                $newUserId = wp_insert_user([
                    'user_login' => $args['user_id'],
                    'user_email' => $args['email'],
                    'display_name' => trim($firstName . ' ' . $lastName),
                    'user_nicename' => $firstName,
                    'user_url' => 'https://vk.com/id' . $args['user_id']
                ]);

                if ($newUserId instanceof WP_Error) {
                    echo AnyComment()->json_error(__('Unable to create new user, try again later'), $newUserId->errors);
                    wp_die();
                }

                $userId = $newUserId;
            } else {
                $userId = $user->ID;
            }

            wp_clear_auth_cookie();
            wp_set_current_user($userId);
            wp_set_auth_cookie($userId, true);

            echo AnyComment()->json_success();

            wp_die();
        }
    }
endif;
