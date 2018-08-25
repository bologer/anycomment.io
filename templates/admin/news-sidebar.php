<h2><?= __( 'News', 'anycomment' ) ?></h2>
<ul class="anycomment-dashboard__sidebar-news">
	<?php
	$posts = AnyComment()->admin_pages->get_news( 3 );

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

					$locale       = get_locale();
					$categoryLink = sprintf( 'https://anycomment.io/%scategory/changelog/', strpos( $locale, 'ru' ) !== false ? '' : 'en/' );

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
        <a href="<?= $categoryLink ?>"
           target="_blank"><?= __( "All News", 'anycomment' ) ?></a>
    </div>
<?php endif; ?>