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

		$ids = null;
		if ( ! empty( $_POST['ratings'] ) ) {
			$ids = isset( $_POST['ratings'] ) && ! array( $_POST['ratings'] )
				? intval( $_POST['ratings'] )
				: array_map( 'intval', $_POST['ratings'] );
		}

		$action = null;

		if ( AnyCommentRequest::post( 'action' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action'] );
		} elseif ( AnyCommentRequest::post( 'action2' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action2'] );
		}
		if ( $action === 'delete' && ! empty( $ids ) ) {
			if ( \AnyComment\Models\AnyCommentRating::deleted_all( 'ID', $ids ) ): ?>
                <div id="message" class="updated notice is-dismissible">
                    <p><?php esc_html_e( 'Selected ratings were deleted.', 'anycomment' ) ?></p>
                </div>
			<?php else: ?>
                <div id="message" class="error notice is-dismissible">
                    <p><?php esc_html_e( 'Failed to delete selected ratings.', 'anycomment' ) ?></p>
                </div>
			<?php endif;
		}

		$filesTable = new \AnyComment\Admin\Tables\AnyCommentRatingTable();
		$filesTable->prepare_items();
		$filesTable->display();
		?>
    </form>
</div>
