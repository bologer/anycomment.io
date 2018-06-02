<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('AC_Render')) :
    /**
     * AnyCommentRender helps to render comments on client side.
     */
    class AC_Render
    {
        public function __construct()
        {
            add_filter('comments_template', [$this, 'render_iframe']);

            add_action('wp_ajax_iframe_comments', [$this, 'iframe_comments']);
            add_action('wp_ajax_nopriv_iframe_comments', [$this, 'iframe_comments']);

            add_action('wp_ajax_render_comments', [$this, 'render_comments']);
            add_action('wp_ajax_nopriv_render_comments', [$this, 'render_comments']);

            add_action('wp_ajax_add_comment', [$this, 'add_comment']);
            add_action('wp_ajax_nopriv_add_comment', [$this, 'add_comment']);

            add_action('wp_enqueue_scripts', [$this, 'assets_load']);
        }

        /**
         * Make custom template for comments.
         * @return string
         */
        public function render_iframe()
        {
            $iframeSrc = add_query_arg([
                'action' => 'iframe_comments',
                'postId' => get_the_ID(),
                'nonce' => wp_create_nonce('iframe_comments'),
            ], admin_url('admin-ajax.php'));
            $style = [
                'width' => '100% !important',
                'min-width' => '100% !important',
                'border' => 'medium none !important',
                'overflow' => 'hidden !important',
                'height' => '700px'
            ];

            $styles = null;
            foreach ($style as $name => $value) {
                $styles .= "$name: $value;";
            }

            wp_enqueue_script(
                'anycomment-iframeResizer',
                'https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/3.6.1/iframeResizer.min.js',
                [],
                1.0
            );

            $randIframeId = uniqid(time() . '-');
            ?>

            <iframe id="<?= $randIframeId ?>"
                    allowtransparency="true"
                    scrolling="no"
                    tabindex="0"
                    title="AnyComment"
                    src="<?= $iframeSrc ?>"
                    frameborder="0"
                    style="<?= $styles ?>"></iframe>
            <script>

                jQuery(document).ready(function ($) {
                    $('#<?= $randIframeId ?>').iFrameResize({
                        log: false,
                        autoResize: true,
                        enablePublicMethods: false,
                        enableInPageLinks: true,
                    });
                });
            </script>
            <?php
            return;
        }

        public function iframe_comments()
        {
            if (!wp_verify_nonce($_GET['nonce'], 'iframe_comments')) {
                wp_die();
            }

            include ANY_COMMENT_ABSPATH . 'templates/comments.php';
            die();
        }

        /**
         * Get comments.
         * @param null|int $postId Post ID to check comments for. Avoid then get_the_ID() will be used to get id.
         * @param int $limit Limit number of comments to load.
         * @return array|null NULL when there are no comments for post.
         */
        public function get_comments($postId = null, $limit = 20)
        {
            if (empty($limit) || (int)$limit < 20) {
                $limit = 20;
            }

            $comments = get_comments([
                'post_id' => $postId === null ? get_the_ID() : $postId,
                'parent' => 0,
                'number' => $limit,
            ]);

            return count($comments) > 0 ? $comments : null;
        }

        /**
         * Get parent child comments.
         * @param int $commentId Parent comment id.
         * @param null|int $postId Post ID to check comments for. Avoid then get_the_ID() will be used to get id.
         * @return array|null NULL when there are no comments for post.
         */
        public function get_child_comments($commentId, $postId = null)
        {
            if ($commentId === null) {
                return null;
            }

            $comments = get_comments(['parent' => $commentId, 'post_id' => $postId === null ? get_the_ID() : $postId]);

            return count($comments) > 0 ? $comments : null;
        }

        /**
         * Use to get freshest list of comment list.
         */
        public function render_comments()
        {
            check_ajax_referer('load-comments-nonce');
            $postId = sanitize_text_field($_POST['postId']);
            $limit = sanitize_text_field($_POST['limit']);

            if (empty($postId)) {
                echo AnyComment()->json_error(__("No post ID specified", 'anycomment'));
                wp_die();
            }

            if (!get_post_status($postId)) {
                echo AnyComment()->json_error(sprintf(__("Unable to find post with ID #%s", 'anycomment'), $postId));
                wp_die();
            }

            do_action('anycomment_comments', $postId, $limit);
            wp_die();
        }

        /**
         * Add new comment.
         */
        public function add_comment()
        {
            check_ajax_referer('add-comment-nonce');

            $parentCommentId = trim(sanitize_text_field($_POST['commentId']));
            $text = trim(sanitize_text_field($_POST['text']));
            $postId = trim(sanitize_text_field($_POST['postId']));

            if (empty($text) || empty($postId)) {
                echo AnyComment()->json_error(__("Wrong params passed", "anycomment"));
                wp_die();
            }

            $args['comment_content'] = $text;
            $args['comment_post_ID'] = $postId;

            if (!empty($parentCommentId) && ($comment = get_comment($parentCommentId)) instanceof WP_Comment) {
                // Check that comment belongs to the current post
                if ($comment->comment_post_ID != $postId) {
                    echo AnyComment()->json_error(__('Reply comment does not belong to the post', "anycomment"));
                    wp_die();
                }

                $args['comment_parent'] = $parentCommentId;
            }

            // Process logged in user
            if (($user = wp_get_current_user()) instanceof WP_User) {
                $args['user_id'] = $user->ID;


                if (!empty($displayName = $user->display_name)) {
                    $args['comment_author'] = trim($displayName);
                }

                // Email
                if (!empty($email = $user->user_email)) {
                    $args['comment_author_email'] = $email;
                }

                $args['comment_approved'] = 1;
            } else {
                $args['comment_approved'] = 0;
            }


            if (!($newCommentId = wp_insert_comment($args))) {
                echo AnyComment()->json_error(__("Failed to add comment. Please, try again.", "anycomment"));
                wp_die();
            }

            wp_notify_postauthor($newCommentId);
            echo AnyComment()->json_success([
                'commentId' => $newCommentId,
                'parentCommentId' => $parentCommentId,
                'blag' => $args,
                'commentText' => $text,
            ]);
            wp_die();
        }
    }
endif;