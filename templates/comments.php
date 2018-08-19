<?php
/**
 * This template is used to display comments.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( post_password_required() || ! comments_open() ) {
	return;
}

?>
<div id="anycomment-root"></div>