<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use AnyComment\Helpers\AnyCommentRequest;

?>

<div class="wrap">
    <h2><?php echo __( 'Rating', 'anycomment' ) ?></h2>

    <form method="post">
        <input type="hidden" name="page" value="anycomment-files">
		<?php

		$ids    = isset( $_POST['ratings'] ) && ! empty( $_POST['ratings'] ) ? $_POST['ratings'] : null;
		$action = null;

		if ( AnyCommentRequest::post( 'action' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action'] );
		} elseif ( AnyCommentRequest::post( 'action2' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action2'] );
		}
		if ( $action === 'delete' && ! empty( $ids ) ) {
			if ( \AnyComment\Models\AnyCommentRating::deleted_all( 'ID', $ids ) ) {
				$messages = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Selected ratings were deleted.', 'anycomment' ) . '</p></div>';
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