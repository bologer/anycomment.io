<?php

namespace AnyComment\Rest;

use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\AnyCommentSocials;
use AnyComment\Domain\Builder\DefaultBuilder;
use AnyComment\Domain\Builder\WebsiteBuilder;
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
		$builder = AnyCommentGenericSettings::is_design_custom()
			? WebsiteBuilder::getDefault()
			: DefaultBuilder::getDefault();

		$socials     = [];
		$socialSlugs = AnyCommentSocials::get_all_slugs_only();
		foreach ( $socialSlugs as $slug ) {
			$socials[ $slug ] = 1;
		}

		$json = json_encode( [
			"generic"            => [
				"use_get_params"      => "0",
				"custom_avatar"       => "https://cdn.anycomment.io/whitelabel/1/5f8e7a41ac2ea.jpg",
				"terms_url"           => "",
				"privacy_url"         => AnyCommentGenericSettings::get_user_agreement_link(),
				"display_privacy_url" => "1",
				"language"            => "ru-RU",
				"pagination"          => "25",
				"display_terms_url"   => "1",
				"ignore_subdomains"   => "1"
			],
			'authentication'     => [
				"allow_logout_action"  => "1",
				"anonymous"            => "1",
				"socials"              => $socials,
				"allow_profile_update" => "1",
				"no_anonymous_email"   => "0",
				"allow_basic_auth"     => "1",
				"allow_social_auth"    => "1"
			],
			"notifications"      => [
				"new_comment_admin"     => "1",
				"comment_reply"         => "1",
				"new_comment_moderator" => "0",
				"mail_logo"             => ""
			],
			"share"              => [
				"socials"      => "{}",
				"enable_share" => "0"
			],
			"page_rating"        => [ "availability" => "all", "enable_page_rating" => "1" ],
			"code_highlight"     => [ "enable_code_highlight" => "0", "languages" => "{}" ],
			"reaction"           => [
				"enable_reaction"         => "1",
				"reaction_mode"           => "like_dislike",
				"display_dislike_counter" => "1"
			],
			"page_subscription"  => [ "enable_page_subscription" => "0" ],
			"emoji"              => [ "enable_emoji" => "0" ],
			"file_upload"        => [ "enable_file_upload" => "1" ],
			"whiteLabel"         => true,
			"is_on_paid_tariff"  => true,
			"show_ads"           => false,
			"ads_vendor"         => null,
			"builder"            => $builder,
			"show_donate_widget" => false
		] );

		header( 'Content-type: application/javascript' );
		echo "window.__ANYCOMMENT_SETTINGS__ = $json";
		exit();
	}
}
