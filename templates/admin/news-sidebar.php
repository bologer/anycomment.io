<div class="anycomment-dashboard__sidebar--widget">
    <h2><?php echo __( 'News', 'anycomment' ) ?></h2>
    <ul class="anycomment-dashboard__sidebar-news">
		<?php
		$posts = \AnyComment\Admin\AnyCommentAdminPages::get_news( 3 );

		if ( $posts !== null ):
			foreach ( $posts as $key => $post ): ?>
                <li>
                    <div class="anycomment-dashboard__sidebar-news-date">
						<?php echo date( 'd.m.Y', strtotime( $post['date'] ) ) ?>

						<?php

						$postTimestamp = strtotime( $post['date'] );
						$newSeconds    = 7 * 24 * 60 * 60; // 1 week
						$difference    = time() - $postTimestamp;

						$isNew = $difference <= $newSeconds;

						$locale       = get_locale();
						$categoryLink = sprintf( 'https://anycomment.io/%scategory/changelog/', strpos( $locale, 'ru' ) !== false ? '' : 'en/' );

						if ( $isNew ): ?>
                            <span class="anycomment-dashboard__sidebar-news-date-new"><?php echo __( 'New', 'anycomment' ) ?></span>
						<?php endif; ?>
                    </div>
                    <a href="<?php echo esc_attr( $post['link'] ) ?>"
                       target="_blank"
                       class="anycomment-dashboard__sidebar-news-title"><?php echo esc_html( $post['title']['rendered'] ) ?></a>
                    <div class="anycomment-dashboard__sidebar-news-content">
						<?php
						$content = isset( $post['content']['rendered'] ) ? $post['content']['rendered'] : null;

						if ( $content !== null ) {
							$content = wp_strip_all_tags( $content, true );
							$content = wp_trim_words( $content, 10, '...' );
							echo esc_html( $content );
						}
						?>
                    </div>
                </li>
			<?php endforeach; ?>
		<?php else: ?>
            <li><?php echo __( 'No news yet', 'anycomment' ) ?></li>
		<?php endif; ?>
    </ul>

	<?php if ( $post !== null ) : ?>
        <div class="anycomment-dashboard__sidebar-all-news">
            <a href="<?php echo esc_attr( $categoryLink ) ?>"
               target="_blank"><?php echo __( "All News", 'anycomment' ) ?></a>
        </div>
	<?php endif; ?>
</div>
