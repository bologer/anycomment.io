<?php
/**
 * Template is used to render send comment box.
 */

$post = AnyComment()->getCurrentPost();
?>

<div class="send-comment <?= (is_user_logged_in() ? 'send-comment-authorized' : '') ?>">
    <div class="send-comment-supheader">
        <span class="send-comment-supheader__count"
              id="comment-count"><?php do_action('anycomment_get_comment_count_text', $post->ID) ?></span>
        <span class="send-comment-supheader__dropdown">Sort By</span>
    </div>
    <div class="send-comment-body">
        <form method="POST" id="send-comment-form" onsubmit="sendComment(this, '#send-comment-form'); return false;">
            <div class="send-comment-body-outliner">
                <?php if (is_user_logged_in()): ?>
                    <?php if (($avatarUrl = AnyComment()->auth->get_active_user_avatar_url()) !== null): ?>
                        <div class="send-comment-body-outliner__avatar"
                             style="background-image:url('<?= $avatarUrl ?>');"></div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="send-comment-body-outliner__logo"></div>
                <?php endif; ?>
                <textarea name="comment" required="required" class="send-comment-body-outliner__textfield"
                          placeholder="<?= __("Add comment...", "anycomment") ?>"></textarea>
            </div>

            <button class="<?= AnyComment()->classPrefix() ?>btn send-comment-body__btn"><?= __('Send', "anycomment") ?></button>

            <input type="hidden" name="reply_to">
            <input type="hidden" name="post_id" value="<?= $post->ID ?>">
            <input type="hidden" name="nonce" value="<?= wp_create_nonce("add-comment-nonce") ?>">
            <input type="hidden" name="action" value="add_comment">
        </form>

        <div class="clearfix"></div>

        <?php if (!is_user_logged_in()): ?>
            <?php do_action('anycomment_login_with', get_permalink(AnyComment()->getCurrentPost()->ID)) ?>
        <?php endif; ?>
    </div>
</div>
