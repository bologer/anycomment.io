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
                            <a href="<?= $post['link'] ?>"
                               target="_blank"
                               class="anycomment-dashboard__sidebar-news-title"><?= esc_html( $post['title']['rendered'] ) ?></a>
                        </li>
					<?php endforeach; ?>
				<?php else: ?>
                    <li><?= __( 'No news yet', 'anycomment' ) ?></li>
				<?php endif; ?>
            </ul>
        </aside>

        <div class="clearfix"></div>
    </div>
</div>