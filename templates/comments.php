<?php

use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\AnyCommentSeoFriendly;
use AnyComment\AnyCommentServiceApi;
use AnyComment\Admin\AnyCommentIntegrationSettings;
use AnyComment\Helpers\AnyCommentLinkHelper;
use AnyComment\Rest\AnyCommentSocialAuth;

/**
 * This is a generic template which renders comments from local WordPress or SaaS (Cloud version).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$post = get_post();

if ( AnyCommentIntegrationSettings::is_sass_comments_show() ):

	$app_id = AnyCommentServiceApi::getSyncAppId();


	if ( empty( $app_id ) ) {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_network' ) ) {
			$message = sprintf(
				__( 'In order to use AnyComment.Cloud, you need to <a href="%s" target="_blank">register</a>, add website and click "Synchronize". This notification is not shown to regular users, just administrators and moderators.', 'anycomment' ),
				AnyCommentLinkHelper::get_service_website() . '/site/signup'
			);
			echo <<<HTML
<div id="comments" class="comments-area">
    <p style="color: red;">$message</p>
</div>
HTML;

			return;
		}
	}

	$preview = null;
	$title   = null;
	$author  = null;

	$root_id = 'anycomment-app';

	if ( ! empty( $post ) ) {
		$page_url = get_permalink( $post );

		$post_thumbnail_url = get_the_post_thumbnail_url( $post );

		$title   = esc_html( $post->post_title );
		$preview = $post_thumbnail_url !== false ? esc_url( $post_thumbnail_url ) : null;

		$first_name = get_the_author_meta( 'first_name', $post->post_author );
		$last_name  = get_the_author_meta( 'last_name', $post->post_author );

		if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
			$author = esc_html( trim( $first_name . ' ' . $last_name ) );
		} else {
			$author = esc_html( get_the_author_meta( 'nickname', $post->post_author ) );
		}

		$root_id .= '-' . $post->ID;
	}

	$config_attributes = [
		'root'     => esc_html( $root_id ),
		'app_id'   => esc_html( $app_id ),
		'language' => AnyCommentLinkHelper::get_saas_languages(),
		'preview'  => $preview,
		'title'    => $title,
		'author'   => $author,
	];

	if ( is_user_logged_in() ) {
		$user                     = wp_get_current_user();
		$user                     = [
			'id'          => $user->ID,
			'name'        => esc_html( $user->display_name ),
			'email'       => esc_html( $user->user_email ),
			'avatar_url'  => esc_url( AnyCommentSocialAuth::get_user_avatar_url( $user->ID ) ),
			'profile_url' => empty( $user->user_url ) ? null : esc_url( $user->user_url ),
		];
		$data                     = base64_encode( json_encode( $user ) );
		$timestampMillis          = round( microtime( true ) * 1000 );
		$signature                = md5( implode( '', [
			$data,
			AnyCommentServiceApi::getSyncApiKey(),
			$timestampMillis
		] ) );
		$config_attributes['sso'] = [
			'data'             => $data,
			'signature'        => $signature,
			'timestamp_millis' => $timestampMillis
		];
	}

	?>
    <div id="comments" class="comments-area">
        <div id="<?php echo esc_attr( esc_html( $root_id ) ) ?>"></div>
    </div>
    <script>
        AnyComment = window.AnyComment || [];
        AnyComment.Comments = [];
        AnyComment.Comments.push(<?php echo json_encode( $config_attributes ) ?>);
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://widget.anycomment.io/comment/embed.js";
        var sa = document.getElementsByTagName("script")[0];
        sa.parentNode.insertBefore(s, s.nextSibling);
    </script>
<?php else: ?>
    <div id="comments" class="comments-area">
		<?php
		$embed_script = <<<HTML
<div id="anycomment-root"></div>
<script type="text/javascript">
    AnyComment = window.AnyComment || [];
    AnyComment.WP = AnyComment.WP || [];
    AnyComment.WP.push({
        root: 'anycomment-root',        
    });
</script>
HTML;

		echo apply_filters( 'anycomment/client/embed-native-script', $embed_script, $post );
		?>
    </div>
<?php endif; ?>

<?php if ( AnyCommentGenericSettings::is_seo_on() ) : ?>
    <noscript>
		<?php echo ( new AnyCommentSeoFriendly( $post->ID ) )->render() ?>
    </noscript>
<?php endif; ?>

