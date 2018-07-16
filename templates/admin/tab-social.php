<form action="options.php" method="post" class="anycomment-form">
	<?php
	settings_fields( 'anycomment-social-group' );
	do_settings_sections('anycomment-settings-social');
	submit_button( __( 'Save', 'anycomment' ) );
	?>
</form>
