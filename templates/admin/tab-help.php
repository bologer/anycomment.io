<div class="anycomment-tab">
    <h2><?php echo __( 'Need Help', 'anycomment' ) ?></h2>
    <p><?php echo __( 'Easiest way to get help is to use one of the links below', 'anycomment' ) ?></p>
    <ul>
        <li><a href="<?php echo AnyCommentLinkHelper::getVkontakte() ?>"
               target="_blank"><?php echo __( 'Vkontakte Group', 'anycomment' ) ?></a></li>
        <li><a href="<?php echo AnyCommentLinkHelper::getTelegram() ?>"
               target="_blank"><?php echo __( 'Telegram Channel', 'anycomment' ) ?></a></li>
        <li><a href="<?php echo AnyCommentLinkHelper::getGitHub() ?>"
               target="_blank"><?php echo __( 'GitHub Repository', 'anycomment' ) ?></a></li>
    </ul>

    <h2><?php echo __( 'Resources', 'anycomment' ) ?></h2>
    <ul>
        <li>
            <a href="<?php echo AnyCommentLinkHelper::getOfficialWebsite() ?>"><?php echo __( 'Official Website', 'anycomment' ) ?></a>
        </li>
        <li><a href="<?php echo AnyCommentLinkHelper::getSocialGuidesLink() ?>"><?php echo __( 'Set-up Socials', 'anycomment' ) ?></a>
        </li>
        <li><a href="<?php echo AnyCommentLinkHelper::getGuidesLink() ?>"><?php echo __( 'All Guides', 'anycomment' ) ?></a></li>
    </ul>

    <h2><?php echo __( 'Support us', 'anycomment' ) ?></h2>

	<?php

	if ( AnyCommentLinkHelper::getLanguage() === 'ru' ) {

		$message = 'Спасибо';
	} else {
		$message = 'Thank you';
	}

	$message = urlencode( $message );
	?>
    <iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=<?php echo $message ?>&targets-hint=&default-sum=50&button-text=14&payment-type-choice=on&comment=on&mail=on&hint=&successURL=https%3A%2F%2Fanycomment.io%2Fspasibo%2F&quickpay=shop&account=41001780745819"
            width="100%" height="304" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
</div>
