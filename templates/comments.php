<?php

use AnyComment\Cron\AnyCommentServiceSyncCron;
use AnyComment\Admin\AnyCommentIntegrationSettings;
use AnyComment\Helpers\AnyCommentLinkHelper;

/**
 * This template is used to display comments.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!comments_open() || post_password_required()) {
    return;
}

if (AnyCommentIntegrationSettings::is_sass_comments_show()):?>
    <div id="comments" class="comments-area">
        <div id="anycomment-app"></div>
    </div>
    <script>
        AnyComment = window.AnyComment || [];
        AnyComment.Comments = [];
        AnyComment.Comments.push({
            "root": "anycomment-app",
            "app_id": <?= AnyCommentServiceSyncCron::getSyncAppId() ?>,
            "language": "<?= AnyCommentLinkHelper::get_language() ?>"
        })
    </script>
    <script type="text/javascript" async src="https://cdn.anycomment.io/assets/js/louncher.js"></script>
<?php else: ?>
    <div id="comments" class="comments-area">
        <div id="anycomment-root"></div>
    </div>
<?php endif; ?>

