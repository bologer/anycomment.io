<?php

use AnyComment\Helpers\AnyCommentLinkHelper;

$addons = [
	[
		'title'       => __( 'AnyComment Analytics', 'anycomment' ),
		'description' => __( 'It is an advanced analytics for AnyComment & it is free!',
			'anycomment' ),
		'url'         => 'https://{language}wordpress.org/plugins/anycomment-analytics/'
	]
];

/**
 * Filters addon list.
 *
 * @param array $addons List of available addons.
 *
 * @since 0.0.99
 *
 */
$addons = apply_filters( 'anycomment/admin/addons', $addons )

?>

<div class="grid-x grid-padding-y">
	<?php foreach ( $addons as $addon ): ?>
        <div class="cell large-3 medium-2 small-12">
            <div class="callout">
                <h3><?php echo esc_html( $addon['title'] ) ?></h3>
                <p><?php echo esc_html( $addon['description'] ) ?></p>


				<?php if ( ! class_exists( '\AnyCommentAnalytics' ) ):
					$language = AnyCommentLinkHelper::get_language();

					switch ( $language ) {
						// List of translated languages
						case 'ru':
							$language .= '.';
							break;
						default:
							$language = '';
					}
					$addon['url'] = str_replace( '{language}', $language, $addon['url'] );
					?>
                    <a href="<?php echo esc_html( $addon['url'] ) ?>"
                       target="_blank"
                       class="button button-primary"
                    >
						<?php echo esc_html__( 'Download', 'anycomment' ) ?>
                    </a>
				<?php else: ?>
                    <p>
						<?php echo esc_html__( 'Already installed', 'anycomment' ) ?>
                    </p>
				<?php endif; ?>

            </div>
        </div>
	<?php endforeach; ?>
</div>
