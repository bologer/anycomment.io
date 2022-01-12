<?php
$steps = [
	''         => [
		[
			'element' => '#anycomment-dashboard',
			'intro'   => esc_html__( 'On this page you see generic information about comments. Statistics, active users and total number of comments. Some of the other pages would have similar guide once you enter such page.' )
//                "На этой странице вы увидите общую информацию о комментариях. Статистику, активность " .
//                         "пользователей и количество комментариев. Некоторые сложные страницы будут иметь точно " .
//                         "такого же помощника как только вы зайдете на неё."
		],
		[
			'element' => '.anycomment-dashboard__splitter-possible-problems',
			'intro'   => esc_html__( 'Have a look at this block. Here, you would find possible conflicts with other plugins. However, do not pay close attention. You can ignore it if AnyComment functioning properly.' )
			/*'Обратите внимание на этот блок, тут Вы найдете потенциальные конфликты с другими плагинами. ' .
					 'Но не обращайтесь на это слишком много внимания. Если все работает, можете игнорировать его.' */
		],
		[
			'element' => '#anycomment-tab-social',
			'intro'   => esc_html__( 'In this tab you are supposed to set-up social networks. Each social network has in-depth configuration guide.' ),
//            'intro'   => 'В этой вкладке Вам нужно настроить социальные сети. У каждой социальной сети есть подробная инструкция по настройке.'
		]
	],
	'social'   => [
		[
			'element' => '.anycomment-tabs__menu-socials',
			'intro'   => esc_html__( 'Configure social networks which fit most to your website. Green indicated by the name of the social network would mean it is available to users for signup.' ),
//            'intro'   => 'Настройте социальные сети, которые подходят вашему сайту. Зеленый индикатор означает справа от названия социальной сети будет означать, что вы включили её.'
		],
		[
			'element' => '.anycomment-guide-block',
			'intro'   => esc_html__( 'For simplicity reasons, we prepared in-depth configuration guide per each social network' ),
//            'intro'   => 'Для упрощения работы у каждой социальной сети есть инструкция по настройке.'
		],
		[
			'element' => '#anycomment-tab-settings',
			'intro'   => esc_html__( 'After you configure socials networks, it is required to go to "General" settings and enable "Enable Comments" option, to replace native comment block with AnyComment\'s version.' )
//            'intro'   => 'После настройки социальных сетей не забудьте зайти в "Общие" настройки и включить опцию "Включить комментарии", чтобы заменить обычный блок комментариев на AnyComment.'
		],

	],
	'settings' => [
		[
			'element' => '#generic .anycomment-form-wrapper__field',
			'intro'   => esc_html__( 'By default AnyComment is not replacing your comment form, so you have time to configure the plugin. Enable this option, once you finished with configuration.' )
		]
	]
];

/**
 * Filters Intro.js steps, with ability to add custom steps.
 *
 * @param array $tabs An array of available tabs. This is supposed to be array in the following form:
 *
 * ```php
 * $steps['your-key'][] = ['element' => '#some-id-to-find', 'intro' => 'Some text'];
 * ```
 *
 * @since 0.0.99
 */
$steps = apply_filters( 'anycomment/admin/tour-steps', $steps );

if ( ! empty( $steps ) ) {
	$steps_in_json    = json_encode( $steps );
	$current_tab      = array_key_exists( 'tab', $_GET ) ? sanitize_text_field( $_GET['tab'] ) : sanitize_text_field( 'dashboard' );
	$disable_tour_url = menu_page_url( $_GET['page'], false );
	$disable_tour_url .= '&' . http_build_query( array_merge( $_GET, [ 'disable-tour' => $current_tab ] ) );

	echo <<<EOT
<script>
    AnyComment = window.AnyComment || {};

    AnyComment.TourSteps = {
        steps: $steps_in_json,
        onExit: function() {
            window.location.href =  '$disable_tour_url';
        },
    };
</script>
EOT;
}

