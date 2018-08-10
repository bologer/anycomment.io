<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/10/18
 * Time: 6:29 PM
 */

class AnyCommentMigration_0_0_45 {
	public $version = '0.0.45';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {

		$option = get_option( 'anycomment-social', true );

		if ( $option === true ) {
			return true;
		}

		return ! isset( $option['social_vk_toggle_field'] ) &&
		       ! isset( $option['social_vk_app_id_field'] ) &&
		       ! isset( $option['social_vk_app_secret_field'] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		$option = get_option( 'anycomment-social', true );

		$option['social_vkontakte_toggle_field']     = $option['social_vk_toggle_field'];
		$option['social_vkontakte_app_id_field']     = $option['social_vk_app_id_field'];
		$option['social_vkontakte_app_secret_field'] = $option['social_vk_app_secret_field'];

		unset( $option['social_vk_toggle_field'] );
		unset( $option['social_vk_app_id_field'] );
		unset( $option['social_vk_app_secret_field'] );

		return update_option( 'anycomment-social', $option );
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		$option = get_option( 'anycomment-social' );

		$option['social_vk_toggle_field']     = $option['social_vkontakte_toggle_field'];
		$option['social_vk_app_id_field']     = $option['social_vkontakte_app_id_field'];
		$option['social_vk_app_secret_field'] = $option['social_vkontakte_app_secret_field'];

		unset( $option['social_vkontakte_toggle_field'] );
		unset( $option['social_vkontakte_app_id_field'] );
		unset( $option['social_vkontakte_app_secret_field'] );

		return update_option( 'anycomment-social', $option );
	}
}