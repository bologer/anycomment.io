<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="wrap">
    <h2><?php echo __( 'Subscriptions', 'anycomment' ) ?></h2>

    <form method="post">
        <input type="hidden" name="page" value="anycomment-files">
		<?php

		$ids    = isset( $_POST['subscriptions'] ) && ! empty( $_POST['subscriptions'] ) ? $_POST['subscriptions'] : null;
		$action = isset( $_POST['action'] ) && ! empty( $_POST['action'] ) ? $_POST['action'] : null;
		if ( $action !== null && $ids !== null && $action === 'delete' ) {
			if ( \AnyComment\Models\AnyCommentSubscriptions::deleted_all( 'ID', $ids ) ) {
				$messages = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Selected subscribers were deleted.', 'anycomment' ) . '</p></div>';
			} else {
				$messages = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Failed to delete selected subscribers.', 'anycomment' ) . '</p></div>';
			}
			echo $messages;
		}

		$filesTable = new \AnyComment\Admin\Tables\AnyCommentSubscriptionsTable();
		$filesTable->prepare_items();
		$filesTable->display();
		?>
    </form>
</div>