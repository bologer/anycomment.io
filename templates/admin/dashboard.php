<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wrap">
    <h2><?= __( 'Dashboard', 'anycomment' ) ?></h2>

    <div class="updated notice">
        <p>
            <strong><?= sprintf( __( 'Please take a few seconds and <a href="%s" target="_blank">rate us on WordPress.org</a>. You are the one who can help to grow and make thus plugin better!', 'anycomment' ), 'https://wordpress.org/support/plugin/anycomment/reviews/?rate=5#new-post' ) ?></strong>
        </p>
        <p>
            <strong><?= sprintf( __( 'Follow us on <a href="%s" %s>VK.com</a>', 'anycomment' ), 'https://vk.com/anycomment', 'target="_blank"' ) ?></strong>
        </p>
    </div>

	<?php if ( is_plugin_active( 'clearfy/clearfy.php' ) ): ?>
        <div class="updated error">
            <p><?= sprintf( __( 'You have <a href="%s">Clearfy</a> activated, please make sure "Remove REST API Links" is "Off" under "Performance" tab as it may cause problems to load comments.', 'anycomment' ), '/wp-admin/admin.php?page=performance-wbcr_clearfy' ) ?></p>
        </div>
	<?php endif; ?>

    <div class="anycomment-dashboard">
        <div class="anycomment-dashboard__container">
            <header class="anycomment-dashboard__header">
                <div class="anycomment-dashboard__header-logo">
                    <img src="<?= AnyComment()->plugin_url() . '/assets/img/mini-logo.svg' ?>"
                         alt="<?= __( 'AnyComment', 'anycomment' ) ?>">
                    <h2><?= __( 'AnyComment', 'anycomment' ) ?></h2>
                </div>

                <div class="anycomment-dashboard__header-official">
                    <a href="https://anycomment.io" target="_blank"><?= __( 'Official Website', 'anycomment' ) ?></a>
                </div>

                <div class="clearfix"></div>
            </header>

			<?= anycomment_get_template( 'admin/tabs' ) ?>
        </div>
        <aside class="anycomment-dashboard__sidebar">
            <?= anycomment_get_template('admin/news-sidebar') ?>
        </aside>

        <div class="clearfix"></div>
    </div>
</div>

<script>
    jQuery(document).on('ready', function () {
        setInterval(function () {
            var container = jQuery('.anycomment-dashboard__container');
            var sidebar = jQuery('.anycomment-dashboard__sidebar');
            var containerHeight = container.height();
            var sidebarHeight = jQuery('.anycomment-dashboard__sidebar').height();

            if (containerHeight > sidebarHeight) {
                sidebar.css('position', 'absolute');
            }
        }, 1000);
    });
</script>