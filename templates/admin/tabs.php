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
	'help'        => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=help',
		'text' => __( 'Help', 'anycomment' )
	],
	'tools'       => [
		'url'  => menu_page_url( $_GET['page'], false ) . '&tab=tools',
		'text' => __( 'Tools', 'anycomment' )
	]
]
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

<?php echo \AnyComment\Helpers\AnyCommentTemplate::render( 'admin/tab-' . $active_tab ) ?>