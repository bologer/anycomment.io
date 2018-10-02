<div class="anycomment-tab">
    <h2><?= __( 'Need Help', 'anycomment' ) ?></h2>
    <p><?= __( 'Easiest way to get help is to use one of the links below', 'anycomment' ) ?></p>
    <ul>
        <li><a href="<?= AnyCommentLinkHelper::getVkontakte() ?>"
               target="_blank"><?= __( 'Vkontakte Group', 'anycomment' ) ?></a></li>
        <li><a href="<?= AnyCommentLinkHelper::getTelegram() ?>"
               target="_blank"><?= __( 'Telegram Channel', 'anycomment' ) ?></a></li>
        <li><a href="<?= AnyCommentLinkHelper::getGitHub() ?>"
               target="_blank"><?= __( 'GitHub Repository', 'anycomment' ) ?></a></li>
    </ul>

    <h2><?= __( 'Resources', 'anycomment' ) ?></h2>
    <ul>
        <li>
            <a href="<?= AnyCommentLinkHelper::getOfficialWebsite() ?>"><?= __( 'Official Website', 'anycomment' ) ?></a>
        </li>
        <li><a href="<?= AnyCommentLinkHelper::getSocialGuidesLink() ?>"><?= __( 'Set-up Socials', 'anycomment' ) ?></a>
        </li>
        <li><a href="<?= AnyCommentLinkHelper::getGuidesLink() ?>"><?= __( 'All Guides', 'anycomment' ) ?></a></li>
    </ul>

    <h2><?= __( 'Support us', 'anycomment' ) ?></h2>

	<?php

	if ( AnyCommentLinkHelper::getLanguage() === 'ru' ) {

		$message = 'Спасибо';
	} else {
		$message = 'Thank you';
	}

	$message = urlencode( $message );
	?>
    <iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=<?= $message ?>&targets-hint=&default-sum=50&button-text=14&payment-type-choice=on&comment=on&mail=on&hint=&successURL=https%3A%2F%2Fanycomment.io%2Fspasibo%2F&quickpay=shop&account=41001780745819"
            width="100%" height="304" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
</div>
