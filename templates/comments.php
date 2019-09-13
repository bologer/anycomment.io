<?php

use AnyComment\Cron\AnyCommentServiceSyncCron;
use AnyComment\Admin\AnyCommentIntegrationSettings;
use AnyComment\Helpers\AnyCommentLinkHelper;

/**
 * This is a generic template which renders comments from local WordPress or SaaS (Cloud version).
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (AnyCommentIntegrationSettings::is_sass_comments_show()):

    $app_id = AnyCommentServiceSyncCron::getSyncAppId();


    if (empty($app_id)) {
        if (current_user_can('manage_options') || current_user_can('manage_network')) {
            $message = sprintf(
                __('In order to use AnyComment.Cloud, you need to <a href="%s" target="_blank">register</a>, add website and click "Synchronize". This notification is not shown to regular users, just administrators and moderators.', 'anycomment'),
                AnyCommentLinkHelper::get_service_website() . '/site/signup'
            );
            echo <<<HTML
<div id="comments" class="comments-area">
    <p style="color: red;">$message</p>
</div>
HTML;
            return;
        }
    }

    $post = get_post();

    $preview = null;
    $title = null;
    $author = null;

    if (!empty($post)) {
        $page_url = get_permalink($post);

        $post_thumbnail_url = get_the_post_thumbnail_url($post);

        $title = $post->post_title;
        $preview = $post_thumbnail_url !== false ? $post_thumbnail_url : null;
        $author = get_the_author_meta('user_nicename', $post->post_author);
    }
    ?>
    <div id="comments" class="comments-area">
        <div id="anycomment-app"></div>
    </div>
    <script>
        AnyComment = window.AnyComment || [];
        AnyComment.Comments = [];
        AnyComment.Comments.push({
            root: "anycomment-app",
            app_id: <?php echo $app_id ?>,
            language: "<?php echo AnyCommentLinkHelper::get_language() ?>",
            preview: "<?php echo $preview ?>",
            title: "<?php echo $title ?>",
            author: "<?php echo $author ?>",
        })
    </script>
    <script type="text/javascript" async src="https://cdn.anycomment.io/assets/js/launcher.js"></script>
<?php else: ?>
    <div id="comments" class="comments-area">
        <div id="anycomment-root"></div>
    </div>
<?php endif; ?>

