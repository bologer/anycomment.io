<?php

use AnyComment\Helpers\AnyCommentRequest;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="wrap">
    <h2><?php echo __( 'Files', 'anycomment' ) ?></h2>

    <form method="post">
        <input type="hidden" name="page" value="anycomment-files">
		<?php

		$ids    = isset( $_POST['files'] ) && ! empty( $_POST['files'] ) ? $_POST['files'] : null;
		if ( AnyCommentRequest::post( 'action' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action'] );
		} elseif ( AnyCommentRequest::post( 'action2' ) !== '-1' ) {
			$action = sanitize_text_field( $_POST['action2'] );
		}
		if ( $action !== null && $ids !== null && $action === 'delete' ) {
			if ( \AnyComment\Models\AnyCommentUploadedFiles::deleted_all( 'ID', $ids ) ) {
				$messages = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Files were deleted.', 'anycomment' ) . '</p></div>';
			} else {
				$messages = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Failed to delete selected files.', 'anycomment' ) . '</p></div>';
			}
			echo $messages;
		}

		$filesTable = new \AnyComment\Admin\Tables\AnyCommentUploadedFilesTable();
		$filesTable->prepare_items();
		$filesTable->display();
		?>
    </form>
</div>