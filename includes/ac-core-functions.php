<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get template part.
 *
 * @param string $templateName Name of the template to get.
 *
 * @return mixed
 */
function anycomment_get_template( $templateName ) {
	ob_start();
	include ANYCOMMENT_ABSPATH . "templates/$templateName.php";
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

/**
 * Display main send comment box.
 */
function anycomment_send_comment() {
	echo anycomment_get_template( 'send-comment' );
}

add_action( 'anycomment_send_comment', 'anycomment_send_comment' );

/**
 * Display log in part inside of send comment box.
 */
function anycomment_logged_in_as() {
	echo anycomment_get_template( 'send-comment-logged-in-as' );
}

add_action( 'anycomment_logged_in_as', 'anycomment_logged_in_as' );

/**
 * Display list of available login methods.
 *
 * @param bool $html If required to return rendered HTML or as array.
 * @param string $redirectUrl Redirect link after successful/failed authentication.
 *
 * @return string|array|null HTML formatted list (when $html false and array when true) of social links.
 */
function anycomment_login_with( $html = false, $redirectUrl = null ) {
	$socials = [
		AnyCommentSocialAuth::SOCIAL_VKONTAKTE     => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_VKONTAKTE,
			'url'     => AnyCommentSocialAuth::get_vk_callback( $redirectUrl ),
			'label'   => __( 'VK', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isVkOn()
		],
		AnyCommentSocialAuth::SOCIAL_TWITTER       => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_TWITTER,
			'url'     => AnyCommentSocialAuth::get_twitter_callback( $redirectUrl ),
			'label'   => __( 'Twitter', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isTwitterOn()
		],
		AnyCommentSocialAuth::SOCIAL_FACEBOOK      => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_FACEBOOK,
			'url'     => AnyCommentSocialAuth::get_facebook_callback( $redirectUrl ),
			'label'   => __( 'Facebook', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isFbOn()
		],
		AnyCommentSocialAuth::SOCIAL_GOOGLE        => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_GOOGLE,
			'url'     => AnyCommentSocialAuth::get_google_callback( $redirectUrl ),
			'label'   => __( 'Google', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isGoogleOn()
		],
		AnyCommentSocialAuth::SOCIAL_GITHUB        => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_GITHUB,
			'url'     => AnyCommentSocialAuth::get_github_callback( $redirectUrl ),
			'label'   => __( 'Github', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isGithubOn()
		],
		AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI,
			'url'     => AnyCommentSocialAuth::get_ok_callback( $redirectUrl ),
			'label'   => __( 'Odnoklassniki', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isOkOn()
		],
		AnyCommentSocialAuth::SOCIAL_INSTAGRAM     => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_INSTAGRAM,
			'url'     => AnyCommentSocialAuth::get_instagram_callback( $redirectUrl ),
			'label'   => __( 'Instagram', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isInstagramOn()
		],
		AnyCommentSocialAuth::SOCIAL_TWITCH        => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_TWITCH,
			'url'     => AnyCommentSocialAuth::get_twitch_callback( $redirectUrl ),
			'label'   => __( 'Twitch', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isTwitchOn()
		],
	];

	if ( count( $socials ) <= 0 ) {
		return null;
	}

	if ( ! $html ) {
		return $socials;
	}

	foreach ( $socials as $key => $social ):
		if ( ! $social['visible'] ) {
			continue;
		}
		?>
        <li><a href="<?= $social['url'] ?>"
               target="_parent"
               title="<?= $social['label'] ?>"
               class="<?= AnyComment()->classPrefix() ?>login-with-list-<?= $key ?>"><img
                        src="<?= AnyComment()->plugin_url() ?>/assets/img/icons/auth/social-<?= $key ?>.svg"
                        alt="<?= $social['label'] ?>"></a>
        </li>
	<?php
	endforeach;
}

add_action( 'anycomment_login_with', 'anycomment_login_with' );


/**
 * Display element to display success or fail alerts.
 */
function anycomment_notifications() {
	?>
    <ul id="notifications" class="notifications" style="display: none;"></ul>
	<?php
}

add_action( 'anycomment_notifications', 'anycomment_notifications' );

/**
 * Display main element to load comments.
 */
function anycomment_load_comments() {
	?>
    <div id="<?= AnyComment()->classPrefix() ?>loader" class="<?= AnyComment()->classPrefix() ?>loader-wrapper"
         style="display: none">
        <div class="<?= AnyComment()->classPrefix() ?>loader">
            <div class="<?= AnyComment()->classPrefix() ?>loader-rect1"></div>
            <div class="<?= AnyComment()->classPrefix() ?>loader-rect2"></div>
            <div class="<?= AnyComment()->classPrefix() ?>loader-rect3"></div>
            <div class="<?= AnyComment()->classPrefix() ?>loader-rect4"></div>
            <div class="<?= AnyComment()->classPrefix() ?>loader-rect5"></div>
        </div>
    </div>
    <ul id="<?= AnyComment()->classPrefix() ?>load-container" class="<?= AnyComment()->classPrefix() ?>list"></ul>

	<?php
}

add_action( 'anycomment_load_comments', 'anycomment_load_comments' );

/**
 * Display author's avatar as comment part.
 *
 * @param WP_Comment $comment
 */
function anycomment_avatar( $comment ) {
	?>
    <div class="comment-single-avatar" data-author-id="<?= $comment->user_id ?>">
		<?php if ( ( $avatarUrl = AnyComment()->auth->get_user_avatar_url( $comment->user_id ) ) ): ?>
            <div class="comment-single-avatar__img" style="background-image: url('<?= $avatarUrl ?>');">
				<?php if ( $social = get_user_meta( $comment->user_id, 'anycomment_social', true ) ): ?>
                    <span class="comment-single-avatar__img-auth-type <?= $social ?>"
                          style="background-image: url('<?= sprintf( AnyComment()->plugin_url() . '/assets/img/icons/avatars/social-%s.svg', $social ) ?>')"></span>
				<?php endif; ?>
            </div>
		<?php endif; ?>
    </div>
	<?php
}

add_action( 'anycomment_avatar', 'anycomment_avatar' );


/**
 * Display author of the comment part.
 *
 * @param WP_Comment $comment
 * @param WP_Comment|null $parentComment Parent comment
 */
function anycomment_author( $comment, $parentComment = null ) {
	$authorName = '' != $comment->comment_author ? $comment->comment_author : __( 'Unknown', "anycomment" );

	if ( $comment->comment_parent != 0 ) {
		$parentComment = get_comment( $comment->comment_parent );
		$parentAuthor  = $parentComment->comment_author != '' ? $parentComment->comment_author : __( 'Unknown', "anycomment" );
	}

	?>
    <header class="comment-single-body-header" data-author-id="<?= $comment->user_id ?>">
		<?php if ( ! isset( $parentComment ) ): ?>
            <div class="comment-single-body-header__author"><?= $authorName ?></div>
		<?php else: ?>
            <div class="comment-single-body-header__author">
                <span class="comment-single-body-header__author-replied"><?= sprintf( '@%s', $authorName ) ?></span>
                <span class="comment-single-body-header__author-answered"><?= __( ' answered ', "anycomemnt" ) ?></span>
                <span class="comment-single-body-header__author-parent-author"><?= $parentAuthor ?></span>
            </div>
		<?php endif; ?>
        <time class="comment-single-body-header__date timeago-date-time"
              datetime="<?= $comment->comment_date ?>"
              title="<?= date( get_option( 'date_format' ), strtotime( $comment->comment_date ) ) ?>"></time>
    </header>
	<?php
}

add_action( 'anycomment_author', 'anycomment_author' );

/**
 * Display comment text part.
 *
 * @param WP_Comment $comment
 */
function anycomment_comment_body( $comment ) {
	?>
    <div class="comment-single-body">
		<?php do_action( 'anycomment_author', $comment ) ?>

        <div class="comment-single-body__text">
            <p><?= sanitize_text_field( $comment->comment_content ) ?></p>
        </div>

		<?php do_action( 'anycomment_actions_part', $comment ) ?>
    </div>
	<?php
}

add_action( 'anycomment_comment_body', 'anycomment_comment_body' );

/**
 * Load more button.
 *
 * @param int $post_id Post ID to count comments.
 * @param int|null $limit Limit of current comments. When NULL specified, default value would be used.
 *
 * @return string|null
 */
function anycomment_load_more( $post_id, $limit = null ) {
	$commentCount = get_comments( [ 'post_id' => $post_id, 'parent' => 0, 'count' => true ] );

	if ( $commentCount <= AnyCommentRender::LIMIT || $limit !== null && $limit >= $commentCount ) {
		return null;
	}
	?>
    <div class="comment-single-load-more">
            <span onclick="return loadNext();"
                  class="btn"><?= __( "Load more", "anycomment" ) ?></span>
    </div>
	<?php
}

add_action( 'anycomment_load_more', 'anycomment_load_more', 10, 2 );


/**
 * Display actions part.
 *
 * @param WP_Comment $comment
 */
function anycomment_actions_part( $comment ) {
	if ( ! is_user_logged_in() ) {
		return;
	}
	?>
    <footer class="comment-single-body__actions">
        <ul>
            <li><a href="javascript:void(0)" data-reply-to="<?= $comment->comment_ID ?>"
                   onclick="return replyComment(this, <?= $comment->comment_ID ?>, '<?= $comment->comment_author ?>')"><?= __( 'Reply', "anycomment" ) ?></a>
            </li>
            <li>
                <span class="comment-single-body__actions-like <?= ( AnyCommentLikes::isCurrentUserHasLike( $comment->comment_ID ) ? 'active' : '' ) ?>"
                      onclick="return like(this, <?= $comment->comment_ID ?>);"><?= AnyCommentLikes::getLikesCount( $comment->comment_ID ) ?></span>
            </li>
			<?php if ( AnyComment()->render->can_edit_comment( $comment ) ): ?>
                <li><a href="javascript:void(0)" data-edit="<?= $comment->comment_ID ?>"
                       onclick="return editComment(this, <?= $comment->comment_ID ?>)"><?= __( 'Edit', "anycomment" ) ?></a>
                </li>
			<?php endif; ?>
        </ul>
    </footer>
	<?php
}

add_action( 'anycomment_actions_part', 'anycomment_actions_part' );


/**
 * Display single comment.
 *
 * @param WP_Comment $comment
 */
function anycomment_comment( $comment ) {
	?>
    <li data-comment-id="<?= $comment->comment_ID ?>" class="comment-single">

		<?php do_action( 'anycomment_avatar', $comment ) ?>

		<?php do_action( 'anycomment_comment_body', $comment ) ?>

		<?php if ( ( $childComments = AnyComment()->render->get_child_comments( $comment->comment_ID ) ) !== null ): ?>
            <div class="comment-single-replies">
                <ul class="<?= AnyComment()->classPrefix() ?>list <?= AnyComment()->classPrefix() ?>list-child">
					<?php foreach ( $childComments as $childComment ): ?>
						<?php do_action( 'anycomment_comment', $childComment ) ?>
					<?php endforeach; ?>
                </ul>
            </div>
		<?php endif; ?>
    </li>
	<?php
}

add_action( 'anycomment_comment', 'anycomment_comment' );

/**
 * Display all comments.
 *
 * @param int $postId Post id.
 * @param int|null $limit Maximum number of comments to load.
 * @param string|null $sort How to sort. Default: 'new'
 */
function anycomment_comments( $post_id, $limit = null, $sort = null ) {
	$limit = empty( $limit ) ? null : $limit;
	$sort  = empty( $sort ) ? null : $sort;

	if ( ( $comments = AnyComment()->render->get_comments( $post_id, $limit, $sort ) ) !== null ):
		foreach ( $comments as $key => $comment ):
			do_action( 'anycomment_comment', $comment );
		endforeach;
		do_action( 'anycomment_load_more', $post_id, $limit );
	else:
		?>
        <ul>
            <li class="comment-single comment-no-comments">
				<?= __( 'No comments to display', "anycomment" ) ?>
            </li>
        </ul>
	<?php
	endif;
}

add_action( 'anycomment_comments', 'anycomment_comments', 10, 3 );


/**
 * Display comment count text.
 *
 * @param int $postId Post id.
 */
function anycomment_get_comment_count_text( $post_id ) {
	echo AnyComment()->render->get_comment_count( $post_id );
}

add_action( 'anycomment_get_comment_count_text', 'anycomment_get_comment_count_text' );

/**
 * Display footer part.
 */
function anycomment_footer() {
	if ( ! AnyCommentGenericSettings::isCopyrightOn() ) {
		return null;
	}
	?>
    <footer class="main-footer">
        <img src="<?= AnyComment()->plugin_url() . '/assets/img/mini-logo.svg' ?>"
             alt="AnyComment"> <a href="https://anycomment.io"
                                  target="_blank"><?= __( 'Add Anycomment to your site', 'anycomment' ) ?></a>
    </footer>
	<?php
}

add_action( 'anycomment_footer', 'anycomment_footer' );


/**
 * Display footer part.
 */
function anycomment_iframe() {
	$iframeSrc = add_query_arg( [
		'action'   => 'iframe_comments',
		'postId'   => get_the_ID(),
		'redirect' => get_permalink(),
		'nonce'    => wp_create_nonce( 'iframe_comments' ),
	], admin_url( 'admin-ajax.php' ) );
	$style     = [
		'width'     => '1px !important',
		'min-width' => '100% !important',
		'border'    => 'medium none !important',
		'overflow'  => 'hidden !important',
		'height'    => '1px'
	];

	$styles = null;
	foreach ( $style as $name => $value ) {
		$styles .= "$name: $value;";
	}

	$randIframeId = uniqid( time() . '-' );

	$options = [
		'log'                 => false,
		'enablePublicMethods' => false,
		'enableInPageLinks'   => true,
	];

	$jsonOptions = json_encode( $options );

	$html = <<<EOT
<iframe id="$randIframeId"
        allowtransparency="true"
        scrolling="no"
        tabindex="0"
        title="AnyComment"
        src="$iframeSrc"
        frameborder="0"
        style="$styles"></iframe>
EOT;

	if ( AnyCommentGenericSettings::isLoadOnScroll() ):
		$html .= <<<EOT
<script>
    var loaded = false;

    jQuery(document).ready(function ($) {
       iframeCommentLoad();
       
       $(window).scroll(function($) {
           iframeCommentLoad();
       });
    });
    
    function iframeCommentLoad() {
         if(loaded) {
             return;
         }
         
         var iframe = jQuery('#$randIframeId'),
             iframeToTop = iframe.offset().top,
             wH = jQuery(window).height(),
             currentTop = jQuery(this).scrollTop();
         
         if(iframe.outerHeight() > 2) {
             return;
         }
           
         wH = wH * 0.9;
         
         if((currentTop + wH) > iframeToTop ) {
            loaded = true;
            jQuery('#$randIframeId').iFrameResize($jsonOptions);
         }
    }
</script>
EOT;

	else:
		$html .= <<<EOT
		<script>
		jQuery(document).ready(function ($) {
    jQuery('#$randIframeId').iFrameResize($jsonOptions);
});
		</script>
EOT;

	endif;

	if ( AnyComment()->errors->hasErrors() ) {
		$html .= <<<EOT
<script>
function scrollToIframe() {
   jQuery(window).on('load', function() {
       jQuery('html, body').animate({
            scrollTop: jQuery('#$randIframeId').offset().top
        }, 500); 
       });
   }
   
   scrollToIframe();
</script>
EOT;
	}

	echo $html;
}

add_action( 'anycomment_iframe', 'anycomment_iframe' );







