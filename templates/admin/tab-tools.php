<?php

use AnyComment\Helpers\Url;

?>

<div class="anycomment-tab">
    <h2><?php echo __('Tools', 'anycomment') ?></h2>
    <p><?php echo __('This page will have helpers and debug information related to the plugin. For example, version of plugin, WordPress or PHP. Also you may drop comments or global plugins cache, open comments for all posts, pages or WooCommerce products (when plugin activated).',
            'anycomment') ?></p>

    <hr>
    <h3><?php echo __('Cache Manager', 'anycomment') ?></h3>
    <a href="<?php echo Url::to(['cache/flush', 'type' => 'all', '_wpnonce' => wp_create_nonce()]) ?>"
       class="button button-primary"><?php echo __("Flush All", 'anycomment') ?></a>
    <a href="<?php echo Url::to(['cache/flush', 'type' => 'rest', '_wpnonce' => wp_create_nonce()]) ?>"
       class="button button-primary"><?php echo __("Flush Comments", 'anycomment') ?></a>

    <hr>
    <h3><?php echo __('Other Helpers', 'anycomment') ?></h3>
    <a href="<?php echo Url::to(['tool/open-comments', 'type' => 'all', '_wpnonce' => wp_create_nonce()]) ?>"
       class="button button-primary"><?php echo __("Open All Comments", 'anycomment') ?></a>
    <a href="<?php echo Url::to(['tool/open-comments', 'type' => 'posts', '_wpnonce' => wp_create_nonce()]) ?>"
       class="button button-primary"><?php echo __("Open Posts Comments", 'anycomment') ?></a>
    <a href="<?php echo Url::to(['tool/open-comments', 'type' => 'pages', '_wpnonce' => wp_create_nonce()]) ?>"
       class="button button-primary"><?php echo __("Open Pages Comments", 'anycomment') ?></a>
    <a href="<?php echo Url::to(['tool/open-comments', 'type' => 'wc-products', '_wpnonce' => wp_create_nonce()]) ?>"
       class="button button-primary"><?php echo __("Open WooCommerce Product Comments", 'anycomment') ?></a>

    <hr>
    <h3><?php echo __('Import HyperComments', 'anycomment') ?></h3>

    <p><?php echo __('Enter URL to XML file to start importing comments from HyperComments.', 'anycomment') ?></p>

    <form action="" method="GET">
        <input type="hidden" name="r" value="import/hypercomments">
	    <?php wp_nonce_field(); ?>
        <div class="grid-x">
            <div class="cell auto">
                <input
                        type="text"
                        name="url"
                        placeholder="http://static.hypercomments.com/data/export/hcexport_XXX.xml"
                >
            </div>
        </div>
        <input type="submit" value="<?php echo __('Import', 'anycomment') ?>" class="button button-default">
        <p><?php echo __('Clicking on "Import"  would copy all comments from provided XML document via URL to your website. It would automatically match posts, pages and create comments for them. It means after page reloaded, you may go and check for imported comments.',
                'anycomment') ?></p>
    </form>

    <form action="" method="GET">
        <input type="hidden" name="r" value="import/hypercomments">
        <input type="hidden" name="revert" value="1">
	    <?php wp_nonce_field(); ?>
        <input type="submit" value="<?php echo __('Revert', 'anycomment') ?>" class="button button-danger">
        <p><?php echo __('This action would delete all imported comments from HyperComment that were improted via this tool.',
                'anycomment') ?></p>
    </form>

    <hr>

    <h3><?php echo __('Debug Information', 'anycomment') ?></h3>
    <p><?php echo __('You may use button below to download debug report and send it AnyComment support.',
            'anycomment') ?></p>
    <div>
        <a href="<?php echo Url::to(['debug/download']) ?>"
           class="button button-primary"><?php esc_html_e('Download report') ?></a>
    </div>
</div>
