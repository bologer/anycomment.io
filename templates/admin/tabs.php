<?php
$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dashboard';
$tabs       = [
	'dashboard'   => [ 'url' => menu_page_url( $_GET['page'], false ), 'text' => __( 'Dashboard', 'anycomment' ) ],
	'social'      => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=social',
		'text' => __( 'Social', 'anycomment' )
	],
	'settings'    => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=settings',
		'text' => __( 'Settings', 'anycomment' )
	],
	'integration' => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=integration',
		'text' => __( 'Integration', 'anycomment' )
	],
	'shortcodes'  => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=shortcodes',
		'text' => __( 'Shortcodes', 'anycomment' )
	],
	'help'  => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=help',
		'text' => __( 'Help', 'anycomment' )
	],
	'tools'  => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=tools',
		'text' => __( 'Tools', 'anycomment' )
	]
]
?>

<?php if ( ! empty( $tabs ) ): ?>
    <div class="anycomment-dashboard__tabs">
        <ul>
			<?php foreach ( $tabs as $key => $tab ): ?>
                <li<?= $active_tab === $key ? ' class="active"' : '' ?>><a
                            href="<?= $tab['url'] ?>"><?= $tab['text'] ?></a>
                </li>
			<?php endforeach; ?>
        </ul>
        <div class="clearfix"></div>
    </div>
<?php endif; ?>

<?= anycomment_get_template( 'admin/tab-' . $active_tab ) ?>

<div class="clearfix"></div>
