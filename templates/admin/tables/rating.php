<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="wrap">
    <h2><?php echo __( 'Rating', 'anycomment' ) ?></h2>

    <form method="post">
        <input type="hidden" name="page" value="anycomment-files">
		<?php

		$filesToDelete = isset( $_POST['files'] ) && ! empty( $_POST['files'] ) ? $_POST['files'] : null;
		$action        = isset( $_POST['action'] ) && ! empty( $_POST['action'] ) ? $_POST['action'] : null;
		if ( $action !== null && $filesToDelete !== null && $action === 'delete' ) {
			if ( \AnyComment\Models\AnyCommentRating::delete( $filesToDelete ) ) {
				$messages = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Rating deleted.', 'anycomment' ) . '</p></div>';
			} else {
				$messages = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Failed to delete selected ratings.', 'anycomment' ) . '</p></div>';
			}
			echo $messages;
		}

		$filesTable = new \AnyComment\Admin\Tables\AnyCommentRatingTable();
		$filesTable->prepare_items();
		$filesTable->display();
		?>
    </form>
</div>