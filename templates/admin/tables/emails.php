<?php

use AnyComment\Helpers\AnyCommentRequest;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="wrap">
    <h2><?php echo __( 'Emails', 'anycomment' ) ?></h2>

    <form method="post">
        <input type="hidden" name="page" value="anycomment-files">
		<?php

		$ids = isset( $_POST['emails'] ) && ! empty( $_POST['emails'] ) ? $_POST['emails'] : null;
		if ( is_array( $ids ) ) {
			$ids = array_map( 'sanitize_text_field', $ids );
		} else {
			$ids = sanitize_text_field( $ids );
		}

		if ( AnyCommentRequest::post( 'action' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action'] );
		} elseif ( AnyCommentRequest::post( 'action2' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action2'] );
		}
		if ( $action !== null && $ids !== null && $action === 'delete' ) {
			if ( \AnyComment\Models\AnyCommentEmailQueue::deleted_all( 'ID', $ids ) ):?>
                <div id="message" class="updated notice is-dismissible">
                    <p><?php esc_html_e( 'Selected emails were deleted.', 'anycomment' ) ?></p>
                </div>
			<?php else: ?>
                <div id="message" class="error notice is-dismissible">
                    <p><?php esc_html_e( 'Failed to delete selected emails.', 'anycomment' ) ?></p>
                </div>
			<?php endif;
		}

		$filesTable = new \AnyComment\Admin\Tables\AnyCommentEmailQueueTable();
		$filesTable->prepare_items();
		$filesTable->display();
		?>
    </form>
</div>
