<form action="options.php" method="post" class="anycomment-form">
	<?php
	settings_fields( 'anycomment-integration-group' );
	do_settings_sections('anycomment-integration');
	submit_button( __( 'Save', 'anycomment' ) );
	?>
</form>
