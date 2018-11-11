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
					<?php

					$links = [
						'telegram'  => [
							'text' => __( 'Telegram', 'anycomment' ),
							'url'  => \AnyComment\Helpers\AnyCommentLinkHelper::getTelegram()
						],
						'vkontakte' => [
							'text' => __( 'Vkontakte', 'anycomment' ),
							'url'  => \AnyComment\Helpers\AnyCommentLinkHelper::getVkontakte()
						],
						'website'   => [
							'text' => __( 'Website', 'anycomment' ),
							'url'  => \AnyComment\Helpers\AnyCommentLinkHelper::getOfficialWebsite()
						],
						'github'    => [
							'text' => __( 'GitHub', 'anycomment' ),
							'url'  => \AnyComment\Helpers\AnyCommentLinkHelper::getGitHub()
						],
					];

					$i = 0;
					foreach ( $links as $link ) {
						$separator = $i !== 0 ? ' · ' : '';
						echo $separator . '<a href="' . $link["url"] . '" target="_blank">' . $link['text'] . '</a>';
						$i ++;
					}
					?>
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