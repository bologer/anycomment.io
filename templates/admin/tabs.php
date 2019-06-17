<?php
$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dashboard';
$tabs       = [
	'dashboard'   => [ 'url' => menu_page_url( $_GET['page'], false ), 'text' => __( 'Dashboard', 'anycomment' ) ],
	'social'      => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=social',
		'text' => __( 'Authentication', 'anycomment' )
	],
	'settings'    => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=settings',
		'text' => __( 'Settings', 'anycomment' )
	],
	'integration' => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=integration',
		'text' => __( 'Integrations', 'anycomment' )
	],
	'shortcodes'  => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=shortcodes',
		'text' => __( 'Shortcodes', 'anycomment' )
	],
	'help'        => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=help',
		'text' => __( 'Help', 'anycomment' )
	],
	'tools'       => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=tools',
		'text' => __( 'Tools', 'anycomment' )
	]
];

/**
 * Filters list of available tabs.
 *
 * @since 0.0.76
 *
 * @param array $tabs An array of available tabs.
 *
 * @package string $active_tab Active tab.
 */
$tabs = apply_filters( 'anycomment/admin/tabs', $tabs, $active_tab );
?>

<?php if ( ! empty( $tabs ) ): ?>
    <div class="grid-x grid-margin-x anycomment-dashboard__tabs">
        <ul class="cell">
			<?php foreach ( $tabs as $key => $tab ): ?>
                <li<?php echo $active_tab === $key ? ' class="active"' : '' ?>><a
                            href="<?php echo $tab['url'] ?>"><?php echo $tab['text'] ?></a>
                </li>
			<?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php

$callback = isset( $tabs[ $active_tab ]['callback'] ) ? $tabs[ $active_tab ]['callback'] : null;

if ( $callback !== null ) {
    echo \AnyComment\Helpers\AnyCommentTemplate::render( $callback );
} else {
	echo \AnyComment\Helpers\AnyCommentTemplate::render( 'admin/tab-' . $active_tab );
}
