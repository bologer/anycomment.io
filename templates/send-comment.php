<?php
/**
 * Template is used to render send comment box.
 */
?>

<div id="<?= AnyComment()->classPrefix() ?>send-message">
    <div class="comment-core-send-box">
        <?php $sendBoxId = uniqid(time() . '-') ?>
        <textarea class="comment-core-send-box-textfield" id="<?= $sendBoxId ?>"></textarea>
        <a href="javascript:void(0)"
           onclick="return sendComment(this, '<?= $sendBoxId ?>', null, false)"
           class="<?= AnyComment()->classPrefix() ?>btn"><?= __('Send', "anycomment") ?></a>

        <?php do_action('anycomment_logged_in_as') ?>

        <?php if (!is_user_logged_in()): ?>
            <?php do_action('anycomment_login_with', get_permalink()) ?>
        <?php endif; ?>
    </div>
</div>
