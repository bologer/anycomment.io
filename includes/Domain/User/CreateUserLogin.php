<?php

namespace AnyComment\Domain\User;

use Webmozart\Assert\Assert;

class CreateUserLogin {
	private $socialSlug;
	private $email;
	private $firstName;
	private $lastName;
	private $displayName;
	private $profileUrl;
	private $photoUrl;
	private $socialId;

	public function __construct(
		$socialSlug,
		$socialId,
		$email,
		$displayName,
		$firstName,
		$lastName,
		$profileUrl,
		$photoUrl
	) {
		Assert::stringNotEmpty( $socialSlug );
		Assert::stringNotEmpty( $socialId );
		Assert::nullOrEmail( $email );
		Assert::nullOrString( $displayName );
		Assert::stringNotEmpty( $firstName );
		Assert::nullOrString( $lastName );
		Assert::nullOrString( $profileUrl );
		Assert::nullOrString( $photoUrl );
		$this->socialSlug  = $socialSlug;
		$this->email       = $email;
		$this->firstName   = $firstName;
		$this->lastName    = $lastName;
		$this->profileUrl  = $profileUrl;
		$this->displayName = $displayName;
		$this->socialId    = $socialId;
		$this->photoUrl    = $photoUrl;
	}

	/**
	 * @return mixed
	 */
	public function getDisplayName() {
		return $this->displayName;
	}

	/**
	 * @return mixed
	 */
	public function getSocialSlug() {
		return $this->socialSlug;
	}

	/**
	 * @return mixed
	 */
	public function getSocialId() {
		return $this->socialId;
	}

	/**
	 * @return mixed
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return mixed
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @return mixed
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @return mixed
	 */
	public function getProfileUrl() {
		return $this->profileUrl;
	}

	/**
	 * @return mixed
	 */
	public function getPhotoUrl() {
		return $this->photoUrl;
	}
}
