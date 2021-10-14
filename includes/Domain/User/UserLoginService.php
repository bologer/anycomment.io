<?php


namespace AnyComment\Domain\User;


use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\AnyCommentUploadHandler;
use AnyComment\AnyCommentUser;
use AnyComment\AnyCommentUserMeta;
use AnyComment\Repository\UserRepositoryInterface;
use RuntimeException;
use WP_Error;
use WP_User;

class UserLoginService {
	/**
	 * Original username from social.
	 */
	const META_SOCIAL_ORIGINAL_USERNAME = 'anycomment_social_original_username';

	/**
	 * Determine type of social.
	 */
	const META_SOCIAL_TYPE = 'anycomment_social';

	/**
	 * Avatar hosted on website.
	 */
	const META_SOCIAL_AVATAR = 'anycomment_social_avatar';

	/**
	 * Link to social.
	 */
	const META_SOCIAL_LINK = 'anycomment_social_link';

	/**
	 * Avatar hosted on remote.
	 */
	const META_SOCIAL_AVATAR_ORIGIN = 'anycomment_social_avatar_origin';

	/**
	 * @var UserRepositoryInterface
	 */
	private $users;

	public function __construct( UserRepositoryInterface $users ) {
		$this->users = $users;
	}

	/**
	 * @param CreateUserLogin $login
	 *
	 * @return WP_User
	 */
	public function login( $login ) {
		if ( $login->getEmail() !== null ) {
			$user               = $this->users->findByEmail( $login->getEmail() );
			$foundSocialNetwork = AnyCommentUserMeta::get_social_type( $user );
			if ( $user !== null && $foundSocialNetwork !== $login->getSocialSlug() ) {
				throw new InvalidSocialNetworkUserException(
					'Invalid social network was used, used:' . $foundSocialNetwork . ', expected: ' . $login->getSocialSlug()
				);
			}
		} else {
			$user = $this->users->findBySocialSlugAndId( $login->getSocialSlug(), $login->getSocialId() );
		}

		if ( $user !== null ) {
			return $user;
		}

		$uniqueUsername = sprintf( '%s_%s', $login->getSocialSlug(), $login->getSocialId() );
		$fullName       = trim( $login->getFirstName() . ' ' . $login->getLastName() );
		$displayName    = ! empty( $fullName ) ? $fullName : $login->getDisplayName();


		$wpUserData = [
			'display_name'  => $displayName,
			'user_nicename' => $login->getFirstName(),
			'first_name'    => $login->getFirstName(),
			'last_name'     => $login->getLastName(),
			'user_login'    => AnyCommentUser::prepare_login( $displayName ),
			'role'          => AnyCommentGenericSettings::get_register_default_group(),
			'user_pass'     => wp_generate_password( 12, true )
		];

		if ( strlen( $login->getProfileUrl() ) <= 100 && false !== strpos( $login->getProfileUrl(), 'http' ) ) {
			$wpUserData['user_url'] = $login->getProfileUrl();
		}

		if ( $login->getEmail() !== null ) {
			$wpUserData['user_email'] = $login->getEmail();
		}


		$userMeta = [
			self::META_SOCIAL_ORIGINAL_USERNAME => $uniqueUsername,
			self::META_SOCIAL_TYPE              => $login->getSocialSlug(),
			self::META_SOCIAL_LINK              => $login->getProfileUrl(),
			self::META_SOCIAL_AVATAR_ORIGIN     => $login->getPhotoUrl(),
		];

		$localUrl = AnyCommentUploadHandler::upload_avatar( $login->getProfileUrl(), [
			$login->getSocialSlug(),
			$login->getPhotoUrl(),
		] );

		if ( $localUrl !== false ) {
			$userMeta[ self::META_SOCIAL_AVATAR ] = $localUrl;
		}

		$newUserId = wp_insert_user( $wpUserData );
		if ( $newUserId instanceof WP_Error ) {
			throw new UserCreateException( 'Unable to create user' );
		}

		$metaInsertCount = 0;
		foreach ( $userMeta as $metaKey => $metaValue ) {
			if ( add_user_meta( $newUserId, $metaKey, $metaValue ) ) {
				$metaInsertCount ++;
			}
		}

		$user = get_user_by( 'id', $newUserId );
		if ( $user === false ) {
			throw new RuntimeException( 'Unable to get user object' );
		}

		return $user;
	}
}
