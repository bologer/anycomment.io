<?php

namespace AnyComment\Rest;

use WP_Error;
use WP_REST_Server;
use WP_REST_Response;
use WP_REST_Request;

class AnyCommentRestEmbed extends AnyCommentRestController {
	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		$this->namespace = 'anycomment/v1';
		$this->rest_base = 'embed';

		$this->meta = new AnyCommentRestCommentMeta();


		add_action( 'rest_api_init', [ $this, 'register_routes' ] );

		remove_filter( 'comment_text', 'wpautop', 30 );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.7.0
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/setting', [
			[
				'methods'  => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_setting' ],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * Get comment count.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 */
	public function get_setting( $request ) {

		$js = 'window.__ANYCOMMENT_SETTINGS__ = {"generic":{"use_get_params":"0","custom_avatar":"https://cdn.anycomment.io/whitelabel/1/5f8e7a41ac2ea.jpg","terms_url":"","privacy_url":"","display_privacy_url":"1","language":"ru-RU","pagination":"25","display_terms_url":"1","ignore_subdomains":"1"},"authentication":{"allow_logout_action":"1","anonymous":"1","socials":"{\"vkontakte\":\"1\",\"twitter\":\"1\",\"facebook\":\"1\",\"google\":\"1\",\"odnoklassniki\":\"1\",\"yandex\":\"1\",\"mailru\":\"1\",\"dribbble\":\"0\",\"telegram\":\"1\",\"bitbucket\":\"0\",\"discord\":\"0\",\"twitch\":\"0\",\"steam\":\"0\"}","allow_profile_update":"1","no_anonymous_email":"0","allow_basic_auth":"1","allow_social_auth":"1"},"notifications":{"new_comment_admin":"1","comment_reply":"1","new_comment_moderator":"0","mail_logo":""},"share":{"socials":"{\"vkontakte\":\"1\",\"twitter\":\"1\",\"facebook\":\"1\",\"telegram\":\"1\",\"whatsapp\":\"1\",\"google\":\"1\",\"odnoklassniki\":\"0\"}","enable_share":"1"},"page_rating":{"availability":"all","enable_page_rating":"1"},"code_highlight":{"enable_code_highlight":"1","languages":"{\"javascript\":\"1\",\"java\":\"1\",\"php\":\"1\",\"sql\":\"1\",\"go\":\"1\",\"typescript\":\"1\"}"},"reaction":{"enable_reaction":"1","reaction_mode":"like_dislike","display_dislike_counter":"1"},"page_subscription":{"enable_page_subscription":"0"},"emoji":{"enable_emoji":"0"},"file_upload":{"enable_file_upload":"1"},"whiteLabel":true,"is_white_label":true,"is_on_paid_tariff":true,"show_ads":false,"ads_vendor":"recreativ","builder":{"global":{"primary_color":"#3699ff","secondary_color":"#b6c1c6","text_color":"#333333","accent_color":"#3281fd","muted_color":"#999999","typography_font_family":"Noto Sans","typography_font_weight":400,"background_color":""},"globalWidgets":{"button":{"background_color":"","text_color":"#ffffff","typography_font_size":{"size":14,"unit":"px"},"padding":{"unit":"px","top":10,"right":20,"bottom":10,"left":20,"isLinked":false},"border_radius":{"unit":"px","top":5,"right":5,"bottom":5,"left":5,"isLinked":true},"border_style":"","border_width":{"unit":"px","top":0,"right":0,"bottom":0,"left":0,"isLinked":true},"border_color":""}},"elements":[{"component":"comment_form","elements":[{"component":"comment_form_input","elements":[],"options":{"text_color":"#373e44","background_color":"#ffffff","typography_font_size":{"size":15,"unit":"px"},"border_radius":{"unit":"px","top":5,"right":5,"bottom":5,"left":5,"isLinked":true},"border_style":"solid","border_width":{"unit":"px","top":1,"right":1,"bottom":1,"left":1,"isLinked":false},"border_color":"#d4d6d7","padding":{"unit":"px","top":10,"right":10,"bottom":10,"left":10,"isLinked":true}}}],"options":[]},{"component":"comment","elements":[{"component":"comment_header","elements":[{"component":"comment_author_name","elements":[],"options":{"text_align":"","text_color":"","typography_font_size":{"unit":"px","size":15},"typography_font_family":"","typography_font_weight":400,"typography_line_height":{"unit":"em","size":""},"typography_letter_spacing":{"unit":"em","size":0},"margin":{"unit":"px","top":"","right":"","bottom":6,"left":"","isLinked":false},"padding":{"unit":"px","top":"","right":"","bottom":"","left":"","isLinked":true}}},{"component":"comment_date","elements":[],"options":{"text_align":"","text_color":"","typography_font_size":{"unit":"px","size":12},"typography_font_family":"","typography_font_weight":400,"typography_line_height":{"unit":"px","size":12.5},"typography_letter_spacing":{"unit":"em","size":0},"margin":{"unit":"px","top":"","right":"","bottom":"","left":"","isLinked":true},"padding":{"unit":"px","top":"","right":"","bottom":"","left":"","isLinked":true}}}],"options":[]},{"component":"comment_body","elements":[{"component":"comment_text","elements":[],"options":{"text_align":"","text_color":"","typography_font_size":{"unit":"px","size":15},"typography_font_family":"","typography_font_weight":400,"typography_line_height":{"unit":"em","size":1.5},"typography_letter_spacing":{"unit":"em","size":0},"typography_text_transform":"","typography_font_style":"","margin":{"unit":"px","top":5,"right":"","bottom":5,"left":"","isLinked":false},"padding":{"unit":"px","top":"","right":"","bottom":"","left":"","isLinked":true}}}],"options":[]}],"options":[]}],"precomputed":{"fonts":{"Noto Sans":{"fontFamily":"Noto Sans","fontWeights":[400]}}},"selectedElements":[]},"show_donate_widget":false};';

		header( 'Content-type: application/javascript' );
		echo $js;
		exit();
	}
}
