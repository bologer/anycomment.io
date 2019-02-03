<?php
/**
 * This template is used to display comments.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! comments_open() || post_password_required() ) {
	return;
}

?>
<div id="comments" class="comments-area">
    <div id="anycomment-root"></div>
</div>
