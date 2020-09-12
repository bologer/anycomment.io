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

		$ids    = isset( $_POST['emails'] ) && ! empty( $_POST['emails'] ) ? $_POST['emails'] : null;
		if ( AnyCommentRequest::post( 'action' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action'] );
		} elseif ( AnyCommentRequest::post( 'action2' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action2'] );
		}
		if ( $action !== null && $ids !== null && $action === 'delete' ) {
			if ( \AnyComment\Models\AnyCommentEmailQueue::deleted_all( 'ID', $ids ) ) {
				$messages = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Selected emails were deleted.', 'anycomment' ) . '</p></div>';
			} else {
				$messages = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Failed to delete selected emails.', 'anycomment' ) . '</p></div>';
			}
			echo $messages;
		}

		$filesTable = new \AnyComment\Admin\Tables\AnyCommentEmailQueueTable();
		$filesTable->prepare_items();
		$filesTable->display();
		?>
    </form>
</div>