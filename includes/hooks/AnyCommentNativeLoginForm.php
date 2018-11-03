<?php

/**
 * Class AnyCommentNativeLoginForm is used to enhance WordPress's native
 * login form via hooks.
 */
class AnyCommentNativeLoginForm {

	/**
	 * AnyCommentNativeLoginForm constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init model.
	 */
	public function init() {
		if ( AnyCommentGenericSettings::is_show_socials_in_login_page() ) {
			add_action( 'login_form', [ $this, 'social_list' ], 11 );
		}

		add_shortcode( 'anycomment_socials', [ $this, 'social_list' ] );
	}

	/**
	 * Display list of available methods to login on the website.
	 *
	 * Usage as shortcode: `[anycomment_socials]`
	 */
	public function social_list( $atts ) {
		$params = shortcode_atts( array(
			'only_socials' => false,
		), $atts );

		$socials = $this->login_with();
		$html    = '';

		if ( ! $params['only_socials'] ) {
			$word_line = __( 'or use any social:', 'anycomment' );
			$html      .= '<div style="margin: 0 0 10px;"><div class="anycomment-socials-preparagraph">' . $word_line . '</div>';
		}

		$html .= $socials;

		$html .= '</div>';

		echo $html;
	}

	/**
	 * Renders login options into HTML.
	 *
	 * @param null|string $redirectUrl URL to be redirected in case of failure of success.
	 *
	 * @return string
	 */
	public function login_with( $redirectUrl = null ) {

		$socials = anycomment_get_socials( $redirectUrl );

		$socials_html = '';
		foreach ( $socials as $key => $social ):
			if ( ! $social['visible'] ) {
				continue;
			}
			$url          = $social['url'];
			$label        = $social['label'];
			$src          = AnyComment()->plugin_url() . "/assets/img/icons/auth/social-$key.svg";
			$socials_html .= <<<HTML
<li>
    <a href="$url" title="$label">
       <img src="$src" alt="$label">
    </a>
</li>
HTML;
		endforeach;

		return <<<HTML
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
HTML;
	}
}