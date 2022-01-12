<?php
$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dashboard';
$page       = sanitize_text_field( $_GET['page'] );
$tabs       = [
	'dashboard'   => [ 'url' => menu_page_url( $page, false ), 'text' => __( 'Dashboard', 'anycomment' ) ],
	'social'      => [
		'url'  => menu_page_url( $page, false ) . '&tab=social',
		'text' => __( 'Social', 'anycomment' )
	],
	'settings'    => [
		'url'  => menu_page_url( $page, false ) . '&tab=settings',
		'text' => __( 'Settings', 'anycomment' )
	],
	'integration' => [
		'url'  => menu_page_url( $page, false ) . '&tab=integration',
		'text' => __( 'Integration', 'anycomment' )
	],
	'addons'      => [
		'url'  => menu_page_url( $page, false ) . '&tab=addons',
		'text' => __( 'Addons', 'anycomment' )
	],
	'shortcodes'  => [
		'url'  => menu_page_url( $page, false ) . '&tab=shortcodes',
		'text' => __( 'Shortcodes', 'anycomment' )
	],
	'help'        => [
		'url'  => menu_page_url( $page, false ) . '&tab=help',
		'text' => __( 'Help', 'anycomment' )
	],
	'tools'       => [
		'url'  => menu_page_url( $page, false ) . '&tab=tools',
		'text' => __( 'Tools', 'anycomment' )
	]
];

/**
 * Filters list of available tabs.
 *
 * @param array $tabs An array of available tabs.
 *
 * @since 0.0.76
 *
 * @package string $active_tab Active tab.
 */
$tabs = apply_filters( 'anycomment/admin/tabs', $tabs, $active_tab );
?>

<?php if ( ! empty( $tabs ) && is_array( $tabs ) ): ?>
    <div class="grid-x grid-margin-x anycomment-dashboard__tabs">
        <ul class="cell">
			<?php foreach ( $tabs as $key => $tab ): ?>
                <li<?php echo esc_attr( esc_html( $active_tab === $key ? ' class="active"' : '' ) ) ?>
                        id="anycomment-tab-<?php echo esc_attr( esc_html( $key ) ) ?>">
                    <a href="<?php echo esc_attr( esc_html( $tab['url'] ) ) ?>"><?php echo esc_html( $tab['text'] ) ?></a>
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
