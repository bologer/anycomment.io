<?php

namespace AnyComment\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AnyCommentRequest {
	/**
	 * Get user IP address.
	 *
	 * @return string
	 */
	public static function get_user_ip() {
		// Get real visitor IP behind CloudFlare network
		if ( isset( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) {
			$_SERVER['REMOTE_ADDR']    = $_SERVER["HTTP_CF_CONNECTING_IP"];
			$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
			$ip = $client;
		} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}

		return $ip;
	}

	/**
	 * Get user agent.
	 *
	 * @return null|string
	 */
	public static function get_user_agent() {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) ?
			trim( $_SERVER['HTTP_USER_AGENT'] ) :
			null;
	}

	/**
	 * Get post value for given name.
	 *
	 * @param $name
	 *
	 * @return mixed|null null when name is missing in POST, value otherwise.
	 */
	public static function post( $name ) {
		return isset( $_POST[ $name ] ) ? sanitize_text_field( $_POST[ $name ] ) : null;
	}
}
