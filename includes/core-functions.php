<?php

if (!function_exists('ec_get_template')):

    /**
     * Get template part.
     * @param string $templateName Name of the template to get.
     * @return mixed
     */
    function ec_get_template($templateName)
    {
        ob_start();
        include ANY_COMMENT_ABSPATH . "templates/$templateName.php";
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
endif;


if (!function_exists('anycomment_send_comment')):
    /**
     * Display main send comment box.
     */
    function anycomment_send_comment()
    {
        echo ec_get_template('send-comment');
    }

    add_action('anycomment_send_comment', 'anycomment_send_comment');
endif;

if (!function_exists('anycomment_logged_in_as')):
    /**
     * Display log in part inside of send comment box.
     */
    function anycomment_logged_in_as()
    {
        echo ec_get_template('send-comment-logged-in-as');
    }

    add_action('anycomment_logged_in_as', 'anycomment_logged_in_as');
endif;

if (!function_exists('anycomment_login_with')):
    /**
     * Display list of available login methods.
     * @param string $redirectUrl Redirect link after successful/failed authentication.
     * @return string|null HTML formatted list of social links.
     */
    function anycomment_login_with($redirectUrl = null)
    {
        $socials = [
            AC_SocialAuth::SOCIAL_VK => [
                'url' => AC_SocialAuth::get_callback_url(AC_SocialAuth::SOCIAL_VK, $redirectUrl),
                'label' => __('VK', "anycomment"),
            ],
            AC_SocialAuth::SOCIAL_TWITTER => [
                'url' => AC_SocialAuth::get_callback_url(AC_SocialAuth::SOCIAL_TWITTER, $redirectUrl),
                'label' => __('Twitter', "anycomment")
            ],
            AC_SocialAuth::SOCIAL_FACEBOOK => [
                'url' => AC_SocialAuth::get_callback_url(AC_SocialAuth::SOCIAL_FACEBOOK, $redirectUrl),
                'label' => __('Facebook', "anycomment")
            ],
            AC_SocialAuth::SOCIAL_GOOGLE => [
                'url' => AC_SocialAuth::get_callback_url(AC_SocialAuth::SOCIAL_GOOGLE, $redirectUrl),
                'label' => __('Google+', "anycomment")
            ],
        ];

        if (count($socials) <= 0) {
            return null;
        }

        foreach ($socials as $key => $social): ?>
            <li><a href="<?= $social['url'] ?>"
                   target="_parent"
                   title="<?= $social['label'] ?>"
                   class="<?= AnyComment()->classPrefix() ?>login-with-list-<?= $key ?>"><img
                            src="<?= AnyComment()->plugin_url() ?>/assets/img/social-auth-<?= $key ?>.svg"
                            alt="<?= $social['label'] ?>"></a>
            </li>
        <?php
        endforeach;
    }

    add_action('anycomment_login_with', 'anycomment_login_with');
endif;


if (!function_exists('anycomment_notifications')):
    /**
     * Display element to display success or fail alerts.
     */
    function anycomment_notifications()
    {
        ?>
        <div id="<?= AnyComment()->classPrefix() ?>notifications" style="display: none;"></div>
        <?php
    }

    add_action('anycomment_notifications', 'anycomment_notifications');
endif;

if (!function_exists('anycomment_load_comments')):
    /**
     * Display main element to load comments.
     */
    function anycomment_load_comments()
    {
        ?>
        <div id="<?= AnyComment()->classPrefix() ?>loader" class="<?= AnyComment()->classPrefix() ?>loader-wrapper"
             style="display: none">
            <div class="<?= AnyComment()->classPrefix() ?>loader">
                <div class="<?= AnyComment()->classPrefix() ?>loader-rect1"></div>
                <div class="<?= AnyComment()->classPrefix() ?>loader-rect2"></div>
                <div class="<?= AnyComment()->classPrefix() ?>loader-rect3"></div>
                <div class="<?= AnyComment()->classPrefix() ?>loader-rect4"></div>
                <div class="<?= AnyComment()->classPrefix() ?>loader-rect5"></div>
            </div>
        </div>
        <ul id="<?= AnyComment()->classPrefix() ?>load-container" class="<?= AnyComment()->classPrefix() ?>list"></ul>

        <?php
    }

    add_action('anycomment_load_comments', 'anycomment_load_comments');
endif;

if (!function_exists('anycomment_avatar')):
    /**
     * Display author's avatar as comment part.
     * @param WP_Comment $comment
     */
    function anycomment_avatar($comment)
    {
        ?>
        <div class="comment-single-avatar" data-author-id="<?= $comment->user_id ?>">
            <?php if (($avatarUrl = AnyComment()->auth->get_user_avatar_url($comment->user_id))): ?>
                <div class="comment-single-avatar__img" style="background-image: url('<?= $avatarUrl ?>');"></div>
            <?php endif; ?>
        </div>
        <?php
    }

    add_action('anycomment_avatar', 'anycomment_avatar');
endif;


if (!function_exists('anycomment_author')):
    /**
     * Display author of the comment part.
     * @param WP_Comment $comment
     * @param WP_Comment|null $parentComment Parent comment
     */
    function anycomment_author($comment, $parentComment = null)
    {
        $authorName = '' != $comment->comment_author ? $comment->comment_author : __('Unknown', "anycomment");

        if ($comment->comment_parent != 0) {
            $parentComment = get_comment($comment->comment_parent);
            $parentAuthor = $parentComment->comment_author != '' ? $comment->comment_author : __('Unknown', "anycomment");
        }

        ?>
        <header class="comment-single-body-header" data-author-id="<?= $comment->user_id ?>">
            <?php if (!isset($parentComment)): ?>
                <div class="comment-single-body-header__author"><?= $authorName ?></div>
            <?php else: ?>
                <div class="comment-single-body-header__author">
                    <span class="comment-single-body-header__author-replied"><?= sprintf('@%s', $authorName) ?></span>
                    <span class="comment-single-body-header__author-answered"><?= __(' answered ', "anycomemnt") ?></span>
                    <span class="comment-single-body-header__author-parent-author"><?= $parentAuthor ?></span>
                </div>
            <?php endif; ?>
            <time class="comment-single-body-header__date timeago-date-time"
                  datetime="<?= $comment->comment_date ?>"></time>
        </header>
        <?php
    }

    add_action('anycomment_author', 'anycomment_author');
endif;

if (!function_exists('anycomment_comment_body')):
    /**
     * Display comment text part.
     * @param WP_Comment $comment
     */
    function anycomment_comment_body($comment)
    {
        ?>
        <div class="comment-single-body">
            <?php do_action('anycomment_author', $comment) ?>

            <div class="comment-single-body__text">
                <p><?= $comment->comment_content ?></p>
            </div>

            <?php do_action('anycomment_actions_part', $comment) ?>
        </div>
        <?php
    }

    add_action('anycomment_comment_body', 'anycomment_comment_body');
endif;

if (!function_exists('anycomment_load_more')):
    /**
     * Load more button.
     */
    function anycomment_load_more()
    {
        ?>
        <div class="comment-single-load-more"
             onclick="return loadNext();"><?= __("Load more", "anycomment") ?></div>
        <?php
    }

    add_action('anycomment_load_more', 'anycomment_load_more');
endif;


if (!function_exists('anycomment_actions_part')):
    /**
     * Display actions part.
     * @param WP_Comment $comment
     */
    function anycomment_actions_part($comment)
    {
        ?>
        <?php
        $parentReplyBoxId = uniqid(sprintf('%s', $comment->comment_ID));
        ?>

        <footer class="comment-single-body__actions">
            <ul>
                <li><a href="javascript:void(0)" data-reply-to="<?= $comment->comment_ID ?>"
                       onclick="return replyComment(this, <?= $comment->comment_ID ?>)"><?= __('Reply', "anycomment") ?></a>
                </li>
            </ul>
        </footer>
        <?php
    }

    add_action('anycomment_actions_part', 'anycomment_actions_part');
endif;


if (!function_exists('anycomment_comment')):
    /**
     * Display single comment.
     * @param WP_Comment $comment
     */
    function anycomment_comment($comment)
    {
        ?>
        <li data-comment-id="<?= $comment->comment_ID ?>" class="comment-single">

            <?php do_action('anycomment_avatar', $comment) ?>

            <?php do_action('anycomment_comment_body', $comment) ?>

            <?php if (($childComments = AnyComment()->render->get_child_comments($comment->comment_ID)) !== null): ?>
                <div class="comment-single-replies">
                    <ul class="<?= AnyComment()->classPrefix() ?>list <?= AnyComment()->classPrefix() ?>list-child">
                        <?php foreach ($childComments as $childComment): ?>
                            <?php do_action('anycomment_comment', $childComment) ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </li>
        <?php
    }

    add_action('anycomment_comment', 'anycomment_comment');
endif;

if (!function_exists('anycomment_comments')):
    /**
     * Display all comments.
     * @param int $postId Post id.
     * @param int $limit Maximum number of comments to load.
     */
    function anycomment_comments($postId, $limit = null)
    {
        if (($comments = AnyComment()->render->get_comments($postId, $limit)) !== null):
            foreach ($comments as $key => $comment):
                do_action('anycomment_comment', $comment);
            endforeach;
            do_action('anycomment_load_more');
        else:
            ?>
            <ul>
                <li class="comment-single comment-no-comments">
                    <?= __('No comments to display', "anycomment") ?>
                </li>
            </ul>
        <?php
        endif;
    }

    add_action('anycomment_comments', 'anycomment_comments', 10, 2);
endif;


if (!function_exists('anycomment_get_comment_count_text')):
    /**
     * Display comment count text.
     * @param int $postId Post id.
     */
    function anycomment_get_comment_count_text($post_id)
    {
        echo AnyComment()->render->get_comment_count($post_id);
    }

    add_action('anycomment_get_comment_count_text', 'anycomment_get_comment_count_text');
endif;

if (!function_exists('anycomment_footer')):
    /**
     * Display footer part.
     */
    function anycomment_footer()
    {
        ?>
        <footer class="main-footer">
            <img src="<?= AnyComment()->plugin_url() . '/assets/img/mini-logo.svg' ?>"
                 alt="AnyComment"> <a href="https://anycomment.io"
                                      target="_blank"><?= __('Add Anycomment to your site', 'anycomment') ?></a>
        </footer>
        <?php
    }

    add_action('anycomment_footer', 'anycomment_footer');
endif;






