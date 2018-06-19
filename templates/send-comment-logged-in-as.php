<?php
/**
 * Template is used to render "logged in as" part inside of send comment box.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
return;
?>

<?php if (is_user_logged_in()): ?>
    <div class="comment-code-send-box__logged-as">
        <?php $user = wp_get_current_user(); ?>

        <p><?php printf(__('Logged in as %s', "anycomment"), $user->display_name) ?> <a
                    href="<?= wp_logout_url(get_permalink()) ?>"
                    class="<?= AnyComment()->classPrefix() ?>btn"><?= __('Logout', "anycomment") ?></a>
        </p>
    </div>
<?php endif; ?>
