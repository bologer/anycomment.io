<form action="options.php" method="post" class="anycomment-form">
	<?php
	settings_fields( 'anycomment-generic-group' );
	do_settings_sections('anycomment-settings');
	submit_button( __( 'Save', 'anycomment' ) );
	?>
</form>
