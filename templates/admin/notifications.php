<?php

$items = \AnyComment\Admin\AnyCommentProblemNotifier::get_problem_list();

if ( ! empty( $items ) ): ?>
    <p><?php echo __( 'Please do not treat every notice below as a problem. They are generated based on the plugins you use, based on the WordPress version you run and environment you have. They should be treated as suggestions.', 'anycomment' ) ?></p>
    <ul>
		<?php foreach ( $items as $key => $item ): ?>
			<?php
			$class = 'anycomment-notice';
			$class .= isset( $item['level'] ) && $item['level'] === \AnyComment\Admin\AnyCommentProblemNotifier::LEVEL_CRITICAL ? ' anycomment-error' : '';
			?>
            <li>
                <p class="<?php echo esc_attr( esc_html( $class ) ) ?>"><?php echo wp_kses( $item['message'], [ 'a' => [ 'href' => [] ] ] ) ?></p>
            </li>
		<?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="anycomment-notice anycomment-success"><?php echo __( 'Good job! It looks like everything is okay.', 'anycomment' ) ?></p>
<?php endif; ?>
