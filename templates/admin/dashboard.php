<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wrap" id="anycomment-wrapper">
    <h2><?php echo __( 'Dashboard', 'anycomment' ) ?></h2>

    <div class="anycomment-notice anycomment-success">
		<?php echo sprintf( __( 'Please take a few seconds and <a href="%s" target="_blank">rate us on WordPress.org</a>. You are the one who can help to grow and make this plugin better!', 'anycomment' ), 'https://wordpress.org/support/plugin/anycomment/reviews/?rate=5#new-post' ) ?>
    </div>

    <div class="anycomment-notice anycomment-success"><?php echo sprintf( __( 'Follow us on <a href="%s" %s>VK.com</a> or join our group in <a href="%s" %s>Telegram</a>', 'anycomment' ),
			\AnyComment\Helpers\AnyCommentLinkHelper::getVkontakte(),
			'target="_blank"',
			\AnyComment\Helpers\AnyCommentLinkHelper::getTelegram(),
			'target="_blank"' ) ?></div>

    <div class="anycomment-dashboard grid-x">
        <div class="cell auto anycomment-dashboard__container">
            <header class="grid-x anycomment-dashboard__header">
                <div class="cell auto anycomment-dashboard__header-logo">
                    <img src="<?php echo AnyComment()->plugin_url() . '/assets/img/mini-logo.svg' ?>"
                         alt="<?php echo __( 'AnyComment', 'anycomment' ) ?>">
                    <h2><?php echo __( 'AnyComment', 'anycomment' ) ?>
                        &nbsp;<sup><?php echo AnyComment()->version ?></sup></h2>
                </div>

                <div class="cell auto anycomment-dashboard__header-official">
                    <a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::getOfficialWebsite() ?>"
                       target="_blank"><?php echo __( 'Official Website', 'anycomment' ) ?></a>
                </div>
            </header>

			<?php echo \AnyComment\Helpers\AnyCommentTemplate::render( 'admin/tabs' ) ?>

            <div class="anycomment-dashboard__splitter anycomment-dashboard__splitter-right-space anycomment-dashboard__splitter-possible-problems">
                <h2><?php echo __( 'Possible Problems', 'anycomment' ) ?></h2>
				<?php echo \AnyComment\Helpers\AnyCommentTemplate::render( 'admin/notifications' ) ?>
            </div>
        </div>
        <aside class="cell large-3 medium-12 anycomment-dashboard__sidebar">
			<?php echo \AnyComment\Helpers\AnyCommentTemplate::render( 'admin/help-sidebar' ) ?>
			<?php echo \AnyComment\Helpers\AnyCommentTemplate::render( 'admin/news-sidebar' ) ?>
        </aside>
    </div>
</div>