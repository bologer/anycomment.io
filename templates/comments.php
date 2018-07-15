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
	// Options from plugin
	'options' => [
		'limit'       => AnyCommentGenericSettings::getPerPage(),
		'isCopyright' => AnyCommentGenericSettings::isCopyrightOn(),
		'socials'     => anycomment_login_with(),
		'theme'       => AnyCommentGenericSettings::getTheme(),
	],
	'i18'     => [
		'error'            => __( 'Error', 'anycomment' ),
		'loading'          => __( 'Loading...', 'anycomment' ),
		'load_more'        => __( "Load more", "anycomment" ),
		'button_send'      => __( 'Send', 'anycomment' ),
		'button_save'      => __( 'Save', 'anycomment' ),
		'button_reply'     => __( 'Reply', 'anycomment' ),
		'sort_by'          => __( 'Sort By', 'anycomment' ),
		'sort_oldest'      => __( 'Oldest', 'anycomment' ),
		'sort_newest'      => __( 'Newest', 'anycomment' ),
		'reply_to'         => __( 'Reply to {name}', 'anycomment' ),
		'add_comment'      => __( 'Add comment...', 'anycomment' ),
		'no_comments'      => __( 'No comments to display', "anycomment" ),
		'footer_copyright' => __( 'Add Anycomment to your site', 'anycomment' ),
		'reply'            => __( 'Reply', 'anycomment' ),
		'edit'             => __( 'Edit', 'anycomment' ),
		'quick_login'      => __( 'Quick Login', 'anycomment' ),
		'author'           => __( 'Author', 'anycomment' ),
	]
] );


$classPrefix = AnyComment()->classPrefix();
?>
<!DOCTYPE html>
<html lang="<?= get_locale() ?>">
<head>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&amp;subset=cyrillic" rel="stylesheet">
	<?php wp_enqueue_style( 'anycomment-styles' ) ?>
</head>
<body>
<div id="<?= $classPrefix ?>comments"
     class="<?= $classPrefix ?>comments-dark"
     data-current-limit="<?= AnyCommentGenericSettings::getPerPage() ?>"
     data-sort="<?= AnyCommentRender::SORT_NEW ?>">

	<?php do_action( 'anycomment_notifications' ) ?>
    <div id="root" data-nonce="<?= wp_create_nonce( 'wp_rest' ) ?>"></div>
</div>

<?php wp_footer(); ?>

<script>
    let settings =  <?= json_encode( [
		'url'          => esc_url_raw( rest_url( 'anycomment/v1/comments' ) ),
		'urlLikes'     => esc_url_raw( rest_url( 'anycomment/v1/likes' ) ),
		'nonce'        => wp_create_nonce( 'wp_rest' ),
		'debug'        => true,
		'postId'       => $postId,
		'postUrl'      => get_post_permalink( $postId ),
		'options'      => [
			'locale' => get_locale(),
			'limit'  => AnyCommentGenericSettings::getPerPage(),
			'guest'  => ! is_user_logged_in()
		],
		'translations' => [
			'button_send'  => __( 'Send', 'anycomment' ),
			'button_save'  => __( 'Save', 'anycomment' ),
			'button_reply' => __( 'Reply', 'anycomment' ),
		]
	] ) ?>;

    /**
     * Get loader.
     */
    function getLoader() {
        return jQuery('#<?= AnyComment()->classPrefix()?>loader');
    }

    /**
     * Add error alert.
     * @param message Message of the alert.
     */
    function addError(message) {
        addAlert('error', message);
    }

    /**
     * Add success alert.
     * @param message Message of the alert.
     */
    function addSuccess(message) {
        addAlert('success', message);
    }

    /**
     * Add new alert by specifying type and message.
     * @param type Type of the alert.
     * @param message Message of the alert.
     */
    function addAlert(type, message) {
        if (!type || !message || (type !== 'success' && type !== 'error')) {
            return;
        }

        let alert = '<li class="{class}" onclick="cleanAlerts()">{text}</li>'
            .replace('{class}', 'alert ' + type)
            .replace('{text}', message);
        let notifications = jQuery('#notifications');

        let isOpen = notifications.length;

        let htmlContent = alert;

        if (isOpen) {
            notifications.slideUp(300, function () {
                notifications.html(htmlContent);
            });
            notifications.slideDown(300);
        } else {
            notifications.slideDown(300);
            notifications.html(htmlContent);
        }
    }

    /**
     * Clean alerts.
     */
    function cleanAlerts() {
        let notifications = jQuery('#notifications');

        notifications.slideUp(300, function () {
            jQuery(this).html('');
        });
    }

    // loadComments();


    // Load time
    function loadTime(lang = '<?= get_locale() ?>') {
        let i = setInterval(function () {
            if (('timeago' in window)) {
                timeago().render(jQuery('.timeago-date-time'), lang.substring(0, 2));
                //{defaultLocale: '<?= get_locale() ?>'}
                clearInterval(i);
            }
        }, 1000);
    }
</script>


<?php if ( AnyComment()->errors->hasErrors() ): ?>
    <script>
        /**
         * Display error messages form cookies. After errors display
         * they will be automatically deleted.
         * @returns {boolean}
         */
        function displayCookieErrors() {
            let errors = JSON.parse('<?= AnyComment()->errors->getErrors() ?>');

            if (!errors) {
                return false;
            }

            errors.forEach(function (element) {
                addError(element);
            });
        }

        displayCookieErrors();
    </script>
<?php endif; ?>
</body>
</html>