<?php

namespace AnyComment\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\AnyCommentSocials;
use AnyComment\Base\BaseObject;

/**
 * Class AnyCommentNativeLoginForm is used to enhance WordPress's native
 * login form via hooks.
 */
class AnyCommentNativeLoginForm extends BaseObject {

	/**
	 * @inheritDoc
	 */
	public function init() {
		if ( AnyCommentGenericSettings::is_show_socials_in_login_page() ) {
			add_action( 'login_form', [ $this, 'social_list_login_form' ], 11 );
		}

		add_shortcode( 'anycomment_socials', [ $this, 'social_list' ] );
	}

	/**
	 * Used to display list available socials to login as alternative to
	 * regular login details.
	 */
	public function social_list_login_form() {
		$this->social_list( [ 'output' => true ] );
	}

	/**
	 * Display list of available methods to login on the website.
	 *
	 * Usage as shortcode: `[anycomment_socials]`
	 *
	 * Possible options:
	 * - only_socials (default: false) display just list of socials, without starting paragraph.
	 * - output (default: true) true would `echo` the result HTML, false would `return` it.
	 * - target_url: (default: current URI) URL where to redirect user after authorization
	 */
	public function social_list( $atts ) {
		$params = shortcode_atts( array(
			'only_socials' => false,
			'output'       => false,
			'target_url'   => home_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' ),
		), $atts );

		$socials = $this->login_with( $params['target_url'] );
		$html    = '';

		if ( ! $params['only_socials'] ) {
			$word_line = esc_html( __( 'or use any social:', 'anycomment' ) );
			$html      .= <<<HTML
<div style="margin: 0 0 10px;">
	<div class="anycomment-socials-preparagraph">$word_line</div>
</div>
HTML;
		}

		$html .= $socials;

		if ( false === $params['output'] ) {
			return $html;
		}

		echo wp_kses( $html, [
			'div'   => [
				'class' => [],
				'style' => []
			],
			'ul'    => [
				'class' => [],
			],
			'li'    => [],
			'a'     => [ 'href' => [], 'title' => [] ],
			'img'   => [ 'src' => [], 'alt' => [] ],
			'style' => [],
		] );
	}

	/**
	 * Renders login options into HTML.
	 *
	 * @param null|string $redirectUrl URL to be redirected in case of failure of success.
	 *
	 * @return string
	 */
	public function login_with( $redirectUrl = null ) {

		$socials = AnyCommentSocials::get_all( $redirectUrl );

		$socials_html = '';
		foreach ( $socials as $key => $social ):
			if ( ! $social['visible'] ) {
				continue;
			}
			$url          = esc_html( esc_attr( $social['url'] ) );
			$label        = esc_html( esc_attr( $social['label'] ) );
			$src          = AnyComment()->plugin_url() . "/assets/img/socials/$key.svg";
			$socials_html .= <<<EOT
<li>
    <a href="$url" title="$label">
       <img src="$src" alt="$label">
    </a>
</li>
EOT;
		endforeach;

		return <<<EOT
<style>
ul.anycomment-socials {
	margin: 0; 
    padding: 0;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-flex-wrap: wrap;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -webkit-justify-content: flex-start;
    -ms-flex-pack: start;
    justify-content: flex-start;
    -webkit-align-content: stretch;
    -ms-flex-line-pack: stretch;
    align-content: stretch;
}

ul.anycomment-socials,
ul.anycomment-socials li {
    list-style: none; 
}

ul.anycomment-socials li {
	margin-right: 5px;
}

ul.anycomment-socials > li > a {
	text-decoration: none;
	opacity: 1;
	box-shadow: none;
	transition: none;
	background-color: transparent;
}

ul.anycomment-socials > li > a:hover,
ul.anycomment-socials > li > a:active, 
ul.anycomment-socials > li > a:focus {
	opacity: 1;
}

ul.anycomment-socials > li > a,
ul.anycomment-socials > li > a img {
	box-shadow: none;
	-webkit-box-shadow: none;
}

ul.anycomment-socials > li > a:hover {
	opacity: 0.8;
}

ul.anycomment-socials > li > a img {
	width: 30px;
	height: 30px;
}
</style>

<ul class="anycomment-socials">
$socials_html
</ul>
EOT;
	}
}
