<div class="anycomment-dashboard__sidebar--widget">
    <h2><?php echo __( 'Support us', 'anycomment' ) ?></h2>

	<?php
	$paypal = <<<HTML
<!--<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">-->
            <!--<input type="hidden" name="cmd" value="_s-xclick">-->
            <!--<input type="hidden" name="hosted_button_id" value="UFG3CQ9GP9Y6G">-->
            <!--<input type="image" src="https://www.paypalobjects.com/en_US/RU/i/btn/btn_donateCC_LG.gif" border="0"-->
                   <!--name="submit" alt="PayPal - The safer, easier way to pay online!">-->
            <!--<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">-->
        <!--</form>-->
HTML
	?>

	<?php if ( \AnyComment\Helpers\AnyCommentLinkHelper::get_language() === 'ru' ): ?>

        <iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=<?php echo urlencode( 'Спасибо' ) ?>&targets-hint=&default-sum=50&button-text=14&payment-type-choice=on&comment=on&mail=on&hint=&successURL=https%3A%2F%2Fanycomment.io%2Fspasibo%2F&quickpay=shop&account=41001780745819"
                width="100%" height="304" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
		<?php echo $paypal ?>
	<?php else: ?>
		<?php echo $paypal; ?>
	<?php endif; ?>
</div>