<?php
/**
 * This template is used to display comments.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( post_password_required() || ! comments_open() ) {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'moderate_comments' ) ) {
		echo '<p style="color: red; padding: 20px 0; font-size: 14px;">' . __( 'This notice displayed only for users with roles: administrator or moderator. It looks like comments are disabled for this post or globally or this post is password protected.', 'anycomment' ) . '</p>';
	}

	return;
}

?>
<div id="comments">
    <div id="anycomment-root"></div>
</div>
