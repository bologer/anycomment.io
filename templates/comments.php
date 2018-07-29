<?php
/**
 * This template is used to display comments.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$postId = sanitize_text_field( $_GET['postId'] );
AnyComment()->setCurrentPost( $postId );

if ( post_password_required( $postId ) || ! comments_open( $postId ) ) {
	return;
}

wp_enqueue_script( 'anycomment-react', AnyComment()->plugin_url() . '/static/js/main.min.js', [], AnyComment()->version );
wp_enqueue_style( 'anycomment-styles', AnyComment()->plugin_url() . '/static/css/main.min.css', [], AnyComment()->version );

wp_localize_script( 'anycomment-react', 'anyCommentApiSettings', [
	'postId'  => $postId,
	'postUrl' => get_permalink( $postId ),
	'nonce'   => wp_create_nonce( 'wp_rest' ),
	'locale'  => get_locale(),
	'restUrl' => esc_url_raw( rest_url( 'anycomment/v1/' ) ),
	// Options from plugin
	'options' => [
		'limit'               => AnyCommentGenericSettings::getPerPage(),
		'isCopyright'         => AnyCommentGenericSettings::isCopyrightOn(),
		'socials'             => anycomment_login_with( false, get_permalink( $postId ) ),
		'theme'               => AnyCommentGenericSettings::getTheme(),
		'user_agreement_link' => AnyCommentGenericSettings::getUserAgreementLink(),
	],
	'user'    => AnyCommentUser::getSafeUser(),
	'i18'     => [
		'error_generic'         => __( "Oops, something went wrong...", "anycomment" ),
		'loading'               => __( 'Loading...', 'anycomment' ),
		'load_more'             => __( "Load more", "anycomment" ),
		'button_send'           => __( 'Send', 'anycomment' ),
		'button_save'           => __( 'Save', 'anycomment' ),
		'button_reply'          => __( 'Reply', 'anycomment' ),
		'sort_by'               => __( 'Sort By', 'anycomment' ),
		'sort_oldest'           => __( 'Oldest', 'anycomment' ),
		'sort_newest'           => __( 'Newest', 'anycomment' ),
		'reply_to'              => __( 'Reply to', 'anycomment' ),
		'add_comment'           => __( 'Your comment...', 'anycomment' ),
		'no_comments'           => __( 'No comments to display', "anycomment" ),
		'footer_copyright'      => __( 'Add Anycomment to your site', 'anycomment' ),
		'reply'                 => __( 'Reply', 'anycomment' ),
		'edit'                  => __( 'Edit', 'anycomment' ),
		'delete'                  => __( 'Delete', 'anycomment' ),
		'cancel'                => __( 'Cancel', 'anycomment' ),
		'quick_login'           => __( 'Quick Login', 'anycomment' ),
		'author'                => __( 'Author', 'anycomment' ),
		'accept_user_agreement' => sprintf(
			__( 'I accept the <a href="%s"%s>User Agreement</a>', 'anycomment' ),
			AnyCommentGenericSettings::getUserAgreementLink(),
			' target="_blank" rel="noopener noreferrer" '
		),
	]
] );
?>
<!DOCTYPE html>
<html lang="<?= get_locale() ?>">
<head>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&amp;subset=cyrillic" rel="stylesheet">
	<?php wp_enqueue_style( 'anycomment-styles' ) ?>
</head>
<body>
<div id="anycomment-root"></div>
<?php wp_footer(); ?>
</body>
</html>