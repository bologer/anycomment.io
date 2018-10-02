<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wrap">
    <h2><?= __( 'Dashboard', 'anycomment' ) ?></h2>

    <div class="anycomment-notice anycomment-success">
		<?= sprintf( __( 'Please take a few seconds and <a href="%s" target="_blank">rate us on WordPress.org</a>. You are the one who can help to grow and make this plugin better!', 'anycomment' ), 'https://wordpress.org/support/plugin/anycomment/reviews/?rate=5#new-post' ) ?>
    </div>

    <div class="anycomment-notice anycomment-success"><?= sprintf( __( 'Follow us on <a href="%s" %s>VK.com</a> or join our group in <a href="%s" %s>Telegram</a>', 'anycomment' ),
			AnyCommentLinkHelper::getVkontakte(),
			'target="_blank"',
			AnyCommentLinkHelper::getTelegram(),
			'target="_blank"' ) ?></div>

    <div class="anycomment-dashboard">
        <div class="anycomment-dashboard__container">
            <header class="anycomment-dashboard__header">
                <div class="anycomment-dashboard__header-logo">
                    <img src="<?= AnyComment()->plugin_url() . '/assets/img/mini-logo.svg' ?>"
                         alt="<?= __( 'AnyComment', 'anycomment' ) ?>">
                    <h2><?= __( 'AnyComment', 'anycomment' ) ?></h2>
                </div>

                <div class="anycomment-dashboard__header-official">
                    <a href="<?= AnyCommentLinkHelper::getOfficialWebsite() ?>"
                       target="_blank"><?= __( 'Official Website', 'anycomment' ) ?></a>
                </div>

                <div class="clearfix"></div>
            </header>

			<?= anycomment_get_template( 'admin/tabs' ) ?>

            <div class="anycomment-dashboard__splitter anycomment-dashboard__splitter-right-space anycomment-dashboard__splitter-possible-problems">
                <h2><?= __( 'Possible Problems', 'anycomment' ) ?></h2>
				<?= anycomment_get_template( 'admin/notifications' ) ?>
            </div>
        </div>
        <aside class="anycomment-dashboard__sidebar">
			<?= anycomment_get_template( 'admin/news-sidebar' ) ?>
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