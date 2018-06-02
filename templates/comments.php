<?php
/**
 * This template is used to display comments.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$postId = sanitize_text_field($_GET['postId']);
AnyComment()->setCurrentPost($postId);

if (post_password_required($postId) || !comments_open($postId)) {
    return;
}

$classPrefix = AnyComment()->classPrefix();

//$const = get_defined_constants(true);
//var_dump($const['user']);
?>

<link rel="stylesheet" href="<?= AnyComment()->plugin_url() ?>/assets/css/comments.css">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/3.6.1/iframeResizer.contentWindow.min.js"></script>

<div id="<?= $classPrefix ?>comments" class="<?= $classPrefix ?>comments-area comments-area" data-origin-limit="20"
     data-current-limit="20">
    <h2><?= __('Comments', 'anycomment') ?></h2>
    <?php do_action('anycomment_send_comment') ?>
    <?php do_action('anycomment_notifications') ?>
    <?php do_action('anycomment_load_comments') ?>
</div>

<script>
    // Load generic comments template
    function loadComments(limit) {
        showLoader();

        jQuery.post('<?= AnyComment()->ajax_url() ?>', {
            action: 'render_comments',
            _wpnonce: '<?= wp_create_nonce("load-comments-nonce") ?>',
            postId: '<?= $postId ?>',
            limit: limit
        }).done(function (data) {
            jQuery('#<?= $classPrefix ?>load-container').html(data);
        }).fail(function () {
            console.log('Unable to get most recent comments');
        }).always(function () {
            hideLoader();
        });
    }

    function loadNext() {
        let root = jQuery('#<?= $classPrefix ?>comments');
        let originLimit = root.data('origin-limit') || 20;
        let currentLimit = root.data('current-limit') || 20;

        if (originLimit === currentLimit) {
            currentLimit += 10;
            root.attr('data-current-limit', currentLimit);
        }

        loadComments(currentLimit);
    }

    // Reply to some comment
    function replyComment(el, textareaId, commentId) {
        if (!textareaId || !commentId) {
            return;
        }

        let textarea = jQuery('#' + textareaId);
        let replyBox = textarea.closest('[data-reply-box-id]');

        replyBox.slideDown(200, function () {
            textarea.focus();
        });
    }

    // Genetic send comment function
    function sendComment(el, textId, commentId, hideReplyBox = true) {
        let textarea = jQuery('#' + textId);

        if (!textarea) {
            return;
        }

        let text = textarea.val().trim() || '';

        if (!text) {
            return;
        }

        showLoader();

        jQuery.post('<?= AnyComment()->ajax_url() ?>', {
            action: 'add_comment',
            _wpnonce: '<?= wp_create_nonce("add-comment-nonce") ?>',
            postId: '<?= $postId ?>',
            commentId: commentId,
            text: text
        }, function (data) {
            if (data.success) {
                if (hideReplyBox) {
                    textarea.closest('[data-reply-box-id]').hide();
                }
                loadComments();
            } else {
                addError(data.error);
                hideLoader();
            }

            textarea.val('');
            textarea.focus();
        }, 'json');
    }

    function showLoader() {
        let loader = getLoader();
        if (!loader) {
            return;
        }

        if (!loader.length) {
            return;
        }

        let loaderHtml = loader.show();
    }

    function hideLoader() {
        let loader = getLoader();
        if (!loader) {
            return;
        }

        if (!loader.length) {
            return;
        }

        let loaderHtml = loader.hide();
    }

    function getLoader() {
        return jQuery('#<?= AnyComment()->classPrefix()?>loader');
    }

    /**
     * Add error alert.
     * @param message Message of the alert.
     */
    function addError(message) {
        addAlert('error', message);
    }

    /**
     * Add success alert.
     * @param message Message of the alert.
     */
    function addSuccess(message) {
        addAlert('success', message);
    }

    function addAlert(type, message) {
        if (!type || !message || (type !== 'success' && type !== 'error')) {
            return;
        }

        let alert = '<p class="{class}">{text}</p>'
            .replace('{class}', '<?= $classPrefix ?>-notification-' + type)
            .replace('{text}', message);
        let notifications = jQuery('#<?= $classPrefix ?>-notifications');
        notifications.html(alert);
        notifications.slideDown(300);
    }


    loadComments();
</script>