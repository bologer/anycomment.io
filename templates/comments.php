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

wp_enqueue_script( "jquery" );
wp_enqueue_script( 'anycomment-iframe-iframeResizer-contentWindow', AnyComment()->plugin_url() . '/assets/js/iframeResizer.contentWindow.min.js', [], AnyComment()->version );
wp_enqueue_script( 'anycomment-iframe-timeago', AnyComment()->plugin_url() . '/assets/js/timeago.min.js', [], AnyComment()->version );
wp_enqueue_script( 'anycomment-iframe-timeago-locales', AnyComment()->plugin_url() . '/assets/js/timeago.locales.min.js', [], AnyComment()->version );

$classPrefix = AnyComment()->classPrefix();
?>
<!DOCTYPE html>
<html lang="<?= get_locale() ?>">
<head>
    <link rel="stylesheet"
          href="<?= AnyComment()->plugin_url() ?>/assets/css/theme-<?= AnyCommentGenericSettings::getTheme() ?>.css?v=<?= AnyComment()->version ?>">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&amp;subset=cyrillic" rel="stylesheet">
</head>
<body>
<div id="<?= $classPrefix ?>comments"
     class="<?= $classPrefix ?>comments-dark"
     data-current-limit="<?= AnyCommentGenericSettings::getPerPage() ?>"
     data-sort="<?= AnyCommentRender::SORT_NEW ?>">
	<?php do_action( 'anycomment_send_comment' ) ?>
	<?php do_action( 'anycomment_notifications' ) ?>
	<?php do_action( 'anycomment_load_comments' ) ?>
	<?php do_action( 'anycomment_footer' ) ?>
</div>

<?php wp_footer(); ?>

<script>
    let settings =  <?= json_encode( [
		'url'          => esc_url_raw( rest_url( 'anycomment/v1/comments' ) ),
		'nonce'        => wp_create_nonce( 'wp_rest' ),
		'debug'        => true,
		'options'      => [
			'limit' => AnyCommentGenericSettings::getPerPage(),
			'guest' => ! is_user_logged_in()
		],
		'translations' => [
			'button_send'  => __( 'Send', 'anycomment' ),
			'button_save'  => __( 'Save', 'anycomment' ),
			'button_reply' => __( 'Reply', 'anycomment' ),
		]
	] ) ?>;

    if (settings.debug) {
        console.log('Settings are:');
        console.log(settings);
    }

    // Load generic comments template
    function loadComments(options = {}) {
        showLoader();


        let limit = null,
            sort = null;

        if (options !== {}) {
            limit = 'limit' in options ? options.limit : null;
            sort = 'sort' in options ? options.sort : null;
        }

        jQuery.post('<?= AnyComment()->ajax_url() ?>', {
            action: 'render_comments',
            _wpnonce: '<?= wp_create_nonce( "load-comments-nonce" ) ?>',
            postId: '<?= $postId ?>',
            limit: limit,
            sort: sort
        }).done(function (data) {
            jQuery('#<?= $classPrefix ?>load-container').html(data);
            loadTime();
        }).fail(function () {
            // todo: need proper way of fandling generic failures, possibly translatable message
            if (settings.debug) {
                console.log('Unable to get most recent comments');
            }
        }).always(function () {
            hideLoader();
        });
    }

    // Load next comments if available
    function loadNext() {
        let root = getRoot();
        let newLimit = parseInt(root.attr('data-current-limit')) + <?= AnyCommentGenericSettings::getPerPage() ?>;

        root.attr('data-current-limit', newLimit);

        loadComments({limit: newLimit});
    }

    // Get root object
    function getRoot() {
        return jQuery('#<?= $classPrefix ?>comments') || '';
    }

    function getForm() {
        return jQuery('#process-comment-form') || '';
    }


    function getCommentField() {
        let form = getForm();

        if (!form) {
            return;
        }

        return form.find('[name="content"]') || '';
    }

    function commentSort(type) {
        loadComments({sort: type});
    }

    function shouldLogin() {
        let guestOverlay = jQuery('#auth-required');

        if (settings.options.guest) {
            guestOverlay.hide();
            guestOverlay.fadeIn(300, function () {
                jQuery(this).hide();
                jQuery(this).show();
            });

            return true;
        }

        return false;
    }

    // Reply to some comment
    function replyComment(el, commentId) {

        if (!commentId) {
            return;
        }

        let form = getForm();


        if (!form) {
            return;
        }

        if (shouldLogin()) {
            if (settings.debug) {
                console.log('Should login, unable to reply');
            }
            return;
        }

        let commentField = form.find('[name="content"]') || '';
        let replyToField = form.find('[name="parent"]') || '';


        if (!commentField || !replyToField) {
            return;
        }

        jQuery.get(settings.url + '/' + commentId, function (resp) {
            if (settings.debug) {
                console.log('Response on reply:');
                console.log(resp);
            }
            prepareTo('reply', resp);
        });

        return false;
    }

    // Edit comment
    function editComment(el, commentId) {

        if (!commentId) {
            return;
        }

        let form = getForm();


        if (!form) {
            return;
        }

        if (shouldLogin()) {
            if (settings.debug) {
                console.log('Should login, unable to edit');
            }
            return;
        }

        let commentField = form.find('[name="content"]') || '';
        let editIdField = form.find(['[name="edit_id"]']) || '';

        if (!commentField || !editIdField) {
            return;
        }

        jQuery.get(settings.url + '/' + commentId, function (resp) {
            if (settings.debug) {
                console.log('Response on edit:');
                console.log(resp);
            }
            prepareTo('edit', resp);
        });

        return false;
    }

    function prepareTo(type, commentResponse = null) {
        if (type !== 'add' && type !== 'edit' && type !== 'reply') {
            return;
        }

        let form = getForm();

        if (!form) {
            return;
        }

        let commentField = form.find('[name="content"]') || '';
        let parentField = form.find('[name="parent"]') || '';
        let editIdField = form.find('[name="edit_id"]') || '';
        let buttonField = form.find('button') || '';

        if (settings.debug) {
            console.log(type);
        }

        switch (type) {
            case 'add':
                commentField.val('');
                parentField.val(0);
                editIdField.val('');
                commentField.attr('placeholder', commentField.data('original-placeholder'));
                buttonField.text(settings.translations.button_send);

                if (settings.debug) {
                    console.log('done ' + type);
                }
                break;
            case 'reply':
                commentField.val('');
                parentField.val(commentResponse.id);
                editIdField.val('');
                buttonField.text(settings.translations.button_reply);

                let replyToPlaceholderText = commentField.data('reply-name').replace('{name}', commentResponse.author_name);
                commentField.attr('placeholder', replyToPlaceholderText);

                if (settings.debug) {
                    console.log('done ' + type);
                }
                break;
            case 'edit':
                commentField.val(commentResponse.content);
                editIdField.val(commentResponse.id);
                parentField.val(commentResponse.parent);
                buttonField.text(settings.translations.button_save);
                if (settings.debug) {
                    console.log('done ' + type);
                }
                break;
            default:
        }

        commentField.focus();

        return true;
    }

    // Genetic send comment function
    function processComment(el, formId) {

        let form = getForm();

        if (!form) {
            return;
        }

        let commentField = form.find('[name="content"]') || '';
        let postIdField = form.find('[name="post"]') || '';
        let nonceField = form.find('[name="nonce"]') || '';
        let editIdField = form.find('[name="edit_id"]') || '';

        if (!commentField || !postIdField || !nonceField) {
            return;
        }

        let commentText = commentField.val().trim() || '';

        if (!commentText) {
            return null;
        }

        let data = form.serialize();

        let url = settings.url;

        if (editIdField) {
            url = settings.url + '/' + editIdField.val().trim();
        }

        jQuery.ajax({
            method: 'POST',
            url: url,
            dataType: 'json',
            data: data,
            headers: {'X-WP-Nonce': settings.nonce},
            beforeSend: showLoader,
            success: function (response) {
                loadComments();
                updateCount(response);
                prepareTo('add');
                cleanAlerts();
            },
            error: function (error) {
                if (settings.debug) {
                    console.log('Err:');
                    console.log(error);
                }

                let response = error.responseJSON;

                if (response.code) {
                    addError(response.message);
                }
            }
        }).always(function () {
            hideLoader();
        });
    }

    // Update comment count
    function updateCount(response) {
        if (!response) {
            return false;
        }

        if (!('meta' in response)) {
            return false;
        }

        jQuery('#comment-count').text(response.meta.count_text);
    }

    /**
     * Display loader.
     */
    function showLoader() {
        let loader = getLoader();
        if (!loader) {
            return;
        }

        if (!loader.length) {
            return;
        }

        let loaderHtml = loader.show();
    }

    /**
     * Hide loader.
     */
    function hideLoader() {
        let loader = getLoader();
        if (!loader) {
            return;
        }

        if (!loader.length) {
            return;
        }

        let loaderHtml = loader.hide();
    }

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

    loadComments();


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