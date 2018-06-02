<?php
/**
 * Template is used to render send comment box.
 */
$sendBoxId = uniqid(time() . '-');
?>

<div class="send-comment">
    <div class="send-comment-supheader">
        <span class="send-comment-supheader__count">12 Comments</span>
        <span class="send-comment-supheader__dropdown">Sort By</span>
    </div>
    <div class="send-comment-body">
        <div class="send-comment-body-outliner">
            <div class="send-comment-body-outliner__logo"></div>
            <textarea class="send-comment-body-outliner__textfield" id="<?= $sendBoxId ?>" placeholder="<?= __("Add comment...", "anycomment") ?>"></textarea>
        </div>

        <a href="javascript:void(0)"
           onclick="return sendComment(this, '<?= $sendBoxId ?>', null, false)"
           class="<?= AnyComment()->classPrefix() ?>btn send-comment-body__btn"><?= __('Send', "anycomment") ?></a>

        <?php do_action('anycomment_logged_in_as') ?>

        <?php if (!is_user_logged_in()): ?>
            <?php do_action('anycomment_login_with', get_permalink(AnyComment()->getCurrentPost()->ID)) ?>
        <?php endif; ?>
    </div>
</div>
