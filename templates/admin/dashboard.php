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
    </div>

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
            <h2><?= __( 'News', 'anycomment' ) ?></h2>
            <ul class="anycomment-dashboard__sidebar-news">
				<?php
				$posts = AnyComment()->admin_pages->get_news();

				if ( $posts !== null ):
					foreach ( $posts as $key => $post ): ?>
                        <li>
                            <div class="anycomment-dashboard__sidebar-news-date">
								<?= date( 'm.d.Y', strtotime( $post['date'] ) ) ?>

								<?php

								$postTimestamp = strtotime( $post['date'] );
								$newSeconds    = 14 * 24 * 60 * 60; // two weeks
								$difference    = time() - $postTimestamp;

								$isNew = $difference <= $newSeconds;

								if ( $isNew ): ?>
                                    <span class="anycomment-dashboard__sidebar-news-date-new"><?= __( 'New', 'anycomment' ) ?></span>
								<?php endif; ?>
                            </div>
                            <a href="<?= $post['link'] ?>"
                               target="_blank"
                               class="anycomment-dashboard__sidebar-news-title"><?= esc_html( $post['title']['rendered'] ) ?></a>
                            <div class="anycomment-dashboard__sidebar-news-content">
								<?php
								$content = isset( $post['content']['rendered'] ) ? $post['content']['rendered'] : null;

								if ( $content !== null ) {
									$content = wp_strip_all_tags( $content, true );

									if ( strlen( $content ) > 150 ) {
										$content = substr( $content, 0, 150 ) . '...';
									}

									echo $content;
								}
								?>
                            </div>
                        </li>
					<?php endforeach; ?>
				<?php else: ?>
                    <li><?= __( 'No news yet', 'anycomment' ) ?></li>
				<?php endif; ?>
            </ul>

			<?php if ( $post !== null ) : ?>
                <div class="anycomment-dashboard__sidebar-all-news">
                    <a href="https://anycomment.io/en/category/plugin-updates/"
                       target="_blank"><?= __( "All News", 'anycomment' ) ?></a>
                </div>
			<?php endif; ?>
        </aside>

        <div class="clearfix"></div>
    </div>
</div>