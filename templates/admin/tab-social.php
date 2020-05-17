<?php
use AnyComment\Helpers\AnyCommentLinkHelper;
use AnyComment\Admin\AnyCommentAdminPages;
?>
<div class="anycomment-tab">
    <div class="anycomment-notice anycomment-success">
		<?= sprintf(
			__( 'Try out our <a href="%s" target="_blank">AnyComment Cloud</a>. We already configured all of the socials for you, just choose what you need.', 'anycomment' ),
			'https://anycomment.io/' . AnyCommentLinkHelper::get_language() . '/docs/connect-to-saas'
		) ?>
    </div>
	<?php echo AnyCommentAdminPages::get_socials()->run() ?>
</div>
