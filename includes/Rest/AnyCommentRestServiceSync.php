<?php

namespace AnyComment\Rest;

use AnyComment\AnyCommentCore;
use AnyComment\AnyCommentServiceApi;
use AnyComment\AnyCommentUser;
use AnyComment\Cache\AnyCommentRestCacheManager;
use AnyComment\Cron\AnyCommentServiceSyncCron;
use WP_Post;
use WP_User;
use WP_Error;
use WP_Comment;
use WP_REST_Server;
use WP_REST_Response;
use WP_REST_Request;
use WP_Comment_Query;
use WP_REST_Posts_Controller;

use AnyComment\Models\AnyCommentLikes;
use AnyComment\AnyCommentComments;
use AnyComment\AnyCommentCommentMeta;
use AnyComment\AnyCommentUserMeta;

use AnyComment\Admin\AnyCommentIntegrationSettings;
use AnyComment\Admin\AnyCommentGenericSettings;

class AnyCommentRestServiceSync extends AnyCommentRestController {

	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		$this->namespace = 'anycomment/v1';
		$this->rest_base = 'sync';

		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.7.0
	 */
	public function register_routes() {

//        register_rest_route($this->namespace, '/' . $this->rest_base . '/import', [
//            [
//                'methods' => WP_REST_Server::CREATABLE,
//                'callback' => [$this, 'import'],
//                'args' => [
//                    'post' => [
//                        'description' => __('Unique post ID', 'anycomment'),
//                        'type' => 'integer',
//                    ],
//                ],
//            ],
//            'schema' => [$this, 'get_public_item_schema'],
//        ]);

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/keys', [
			[
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'keys' ],
				'args'     => [
					'app_id'  => [
						'description' => __( 'Application ID', 'anycomment' ),
						'type'        => 'integer',
					],
					'api_key' => [
						'description' => __( 'API Key', 'anycomment' ),
						'type'        => 'string',
					],
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * @param WP_REST_Request $request
	 */
	public function import( $request ) {
		return [];
	}

	/**
	 * @param WP_REST_Request $request
	 */
	public function keys( $request ) {
		$params = $request->get_body_params();

		$param_app_id  = (int) $params['app_id'];
		$param_api_key = (string) $params['api_key'];

		if ( empty( $param_app_id ) || empty( $param_api_key ) ) {
			return $this->asFailure( 'Missing app_id or api_key' );
		}

		$resp = AnyCommentServiceApi::request()->get( 'client/app/info', [ 'token' => $params['api_key'] ] );

		if ( is_wp_error( $resp ) ) {
			AnyCommentCore::logger()->error(
				'Failed to request AnyComment',
				[
					'error'     => $resp->get_error_message(),
					'errorCode' => $resp->get_error_code(),
				]
			);

			return $this->asFailure( 'Failed to request AnyComment API, error: ' . $resp->get_error_message() . ', code: ' . $resp->get_error_code() );
		}

		if ( $resp['response']['code'] !== 200 ) {
			return $this->asFailure( 'Failed to confirm website information' );
		}

		$json = wp_remote_retrieve_body( $resp );

		$body = json_decode( $json, true );

		$url = isset( $body['response']['url'] ) ? (string) $body['response']['url'] : null;

		if ( empty( $url ) ) {
			return $this->asFailure( 'Failed to retrieve URL from response, failing to compare' );
		}

		$home_url = get_option( 'home' );

		$home_parsed   = wp_parse_url( $home_url );
		$remote_parsed = wp_parse_url( $url );


		if ( ! isset( $home_parsed['host'] ) || ! isset( $remote_parsed['host'] ) ) {
			AnyCommentCore::logger()->error( 'Failed to parse host from given URls', [
				'localUrl'  => $home_url,
				'removeUrl' => $url
			] );

			return $this->asFailure( 'Failed to parse remote or local URL' );
		}

		if ( $home_parsed['host'] !== $remote_parsed['host'] ) {
			AnyCommentCore::logger()->error( 'Local and remote URL mismatch', [
				'localUrl'  => $home_parsed['host'],
				'remoteUrl' => $remote_parsed['host'],
			] );

			return $this->asFailure( 'API key provided for application does not match host one.' );
		}

		$app_id = AnyCommentServiceApi::getSyncAppId();

		if ( empty( $app_id ) ) {
			( new AnyCommentGenericSettings( false ) )->update_specific_options(
				AnyCommentGenericSettings::GENERIC_OPTION_NAME,
				[ AnyCommentGenericSettings::OPTION_PLUGIN_TOGGLE => 'on' ]
			);

			$update_options = [
				AnyCommentIntegrationSettings::OPTION_ANYCOMMENT_SAAS_COMMENTS_SHOW => 'on',
				AnyCommentIntegrationSettings::OPTION_ANYCOMMENT_SAAS_COMMENTS_SYNC => 'on',
			];
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$update_options[ AnyCommentIntegrationSettings::WOOCOMMERCE_REPLACE_FOR_REVIEW ] = 'on';
			}
			( new AnyCommentIntegrationSettings( false ) )->update_specific_options(
				AnyCommentIntegrationSettings::INTEGRATION_OPTION_NAME,
				$update_options
			);
		}

		update_option( AnyCommentServiceApi::getSyncAppIdOptionName(), $param_app_id );
		update_option( AnyCommentServiceApi::getSyncApiKeyOptionName(), $param_api_key );

		return $this->asOk( 'App ID and API key were synced successfully' );
	}

	public function asOk( $response ) {
		return [
			'status'   => 'ok',
			'response' => $response,
			'error'    => null
		];
	}

	public function asFailure( $error ) {
		return [
			'status'   => 'fail',
			'response' => null,
			'error'    => $error
		];
	}
}
