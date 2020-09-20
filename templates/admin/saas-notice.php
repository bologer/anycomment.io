<div class="anycomment-notice anycomment-success">
	<?php

	$url = 'https://anycomment.io/website/' . \AnyComment\AnyCommentServiceApi::getSyncAppId();
	echo __(
		sprintf(
			'You are using AnyComment Cloud. If you would like to change any settings, use the following page - %s. Some settings may be available on this page.',
			'<a href="' . $url . '" target="_blank">' . $url . '</a>'
		)
	); ?>
</div>
