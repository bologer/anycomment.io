<?php if ( \AnyComment\Helpers\AnyCommentLinkHelper::get_language() === 'ru' ): ?>
    <div class="anycomment-dashboard__sidebar--widget">
        <h2><?php echo __( 'Support us', 'anycomment' ) ?></h2>

        <iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=<?php echo urlencode( 'Спасибо' ) ?>&targets-hint=&default-sum=50&button-text=14&payment-type-choice=on&comment=on&mail=on&hint=&successURL=https%3A%2F%2Fanycomment.io%2Fspasibo%2F&quickpay=shop&account=41001780745819"
                width="100%" height="304" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
    </div>
<?php endif; ?>
