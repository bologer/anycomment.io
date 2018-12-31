<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wrap">
    <h2><?php echo __( 'Dashboard', 'anycomment' ) ?></h2>

    <div id="anycomment-wrapper">
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
								'url'  => \AnyComment\Helpers\AnyCommentLinkHelper::get_telegram()
							],
							'vkontakte' => [
								'text' => __( 'Vkontakte', 'anycomment' ),
								'url'  => \AnyComment\Helpers\AnyCommentLinkHelper::get_vkontakte()
							],
							'website'   => [
								'text' => __( 'Website', 'anycomment' ),
								'url'  => \AnyComment\Helpers\AnyCommentLinkHelper::get_official_website()
							],
							'github'    => [
								'text' => __( 'GitHub', 'anycomment' ),
								'url'  => \AnyComment\Helpers\AnyCommentLinkHelper::get_github()
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
				<?php
				$locale = get_locale();

				if ( strpos( $locale, 'ru' ) !== false ):?>
                    <style>
                        .anycomment-analytics-promo {
                            background-color: #ec4568;
                            color: #fff;
                            padding: 10px;
                            margin-bottom: 26px;
                            border-radius: 10px;
                        }

                        .anycomment-analytics-promo p {
                            font-size: 12pt;
                            margin: 0;
                            padding: 0;
                        }

                        .anycomment-analytics-promo p,
                        .anycomment-analytics-promo a {
                            color: #fff;
                        }

                        .anycomment-analytics-promo p > a {
                            text-decoration: underline;
                        }

                        .anycomment-analytics-promo p > a:hover {
                            text-decoration: none;
                        }

                        .anycomment-analytics-promo .anycomment-analytics-promo--highlight {
                            background-color: yellow;
                            color: #ec4568;
                            padding: 1px 6px;
                            font-size: 15pt;
                            font-weight: bold;
                        }

                        .anycomment-analytics-promo .anycomment-analytics-promo__more {
                            padding: 5px 0;
                            display: inline-block;
                            margin-top: 7px;
                            border-bottom: 4px solid #fff;
                            font-weight: bold;
                            font-size: 13pt;
                        }

                        .anycomment-analytics-promo .anycomment-analytics-promo__more:hover {
                            color: #ff0;
                            border-bottom: 4px solid #ff0;
                        }
                    </style>
                    <div class="anycomment-analytics-promo">
                        <p>Совсем скоро выйдет первый аддон (дополнение) «AnyComment Аналитика», прямо сейчас есть
                            возможность
                            <a href="https://anycomment.io/aa-promo/?utm_source=anycomment_sidebar" target="_blank">получить
                                скидку</a> <span class="anycomment-analytics-promo--highlight">20%</span> для всех
                            подписывшихся.</p>
                        <div>
                            <a href="https://anycomment.io/aa-promo/?utm_source=anycomment_sidebar" target="_blank"
                               class="anycomment-analytics-promo__more">Подробнее</a>
                        </div>
                    </div>
				<?php endif; ?>

				<?php echo \AnyComment\Helpers\AnyCommentTemplate::render( 'admin/help-sidebar' ) ?>
				<?php echo \AnyComment\Helpers\AnyCommentTemplate::render( 'admin/news-sidebar' ) ?>
            </aside>
        </div>
    </div>
</div>