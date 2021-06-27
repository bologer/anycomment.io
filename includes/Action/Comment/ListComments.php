<?php

namespace AnyComment\Action\Comment;

use AnyComment\AnyCommentUserMeta;
use AnyComment\Models\AnyCommentRating;
use AnyComment\Repository\CommentRepository;
use AnyComment\Repository\UserRepository;

class ListComments {

	/**
	 * @var UserRepository
	 */
	private $users;
	/**
	 * @var CommentRepository
	 */
	private $comments;

	public function __construct( UserRepository $users, CommentRepository $comments ) {
		$this->users    = $users;
		$this->comments = $comments;
	}

	public function action( $pageUrl, $page, $perPage ) {
		if ( empty( $pageUrl ) ) {
			throw new \InvalidArgumentException( 'Missing page url' );
		}

		if ( empty( $page ) ) {
			throw new \InvalidArgumentException( 'Page must be greater then 1' );
		}

		if ( empty( $perPage ) || ( $perPage < 10 || $perPage > 500 ) ) {
			throw new \InvalidArgumentException( 'Per page must be in between 10 and 500' );
		}

		$page    = intval( $page );
		$perPage = intval( $perPage );
		$postId  = url_to_postid( $pageUrl );
		if ( $postId === 0 ) {
			throw new \DomainException( 'Something went wrong, please try again later' );
		}

		$items  = [];
		$offset = $page === 1 ? 0 : ( $page * $perPage );
		$rows   = $this->comments->findByPostAndConditions( $postId, $perPage, $offset );
		$users  = [];
		foreach ( $rows as $row ) {
			$originalUserId = intval( $row['user_id'] );
			$isGuest        = $originalUserId === 0;
			$userId         = $isGuest ? microtime( true ) : $originalUserId;

			$items[] = [
				'id'            => intval( $row['comment_ID'] ),
				'author_id'     => $userId,
				'content'       => $row['comment_content'],
				'created_date'  => $row['comment_date'],
				'updated_date'  => $row['comment_date'],
				'depth'         => 0,
				'dislike_count' => 0,
				'is_pinned'     => 0,
				'like_count'    => 0,
				'meta'          => null,
				'parent_id'     => null,
				'replies'       => [],
			];

			$usersToSearch = [];

			if ( $isGuest ) {
				$users[ $userId ] = [
					'about'        => null,
					'avatar_url'   => "https://cdn.anycomment.io/whitelabel/1/5f8e7a41ac2ea.jpg",
					'created_date' => $row['comment_date'],
					'first_name'   => $row['comment_author'],
					'id'           => $userId,
					'is_moderator' => false,
					'last_name'    => null,
					'meta'         => [ 'badge' => null ],
					'badge'        => null,
					'name'         => $row['comment_author'],
					'provider'     => null,
					'provider_id'  => null,
					'sex'          => null,
					'social_url'   => null,
					'username'     => "google_104199984889710672059",
				];
			} else {
				$usersToSearch[] = $originalUserId;
			}
		}

		$userRows = $this->users->findByIds( $usersToSearch );

		foreach ( $userRows as $user ) {
			$provider = AnyCommentUserMeta::get_social_type( $user['ID'] );
			if ( empty( $provider ) ) {
				$provider = null;
			}
			$socialUrl = AnyCommentUserMeta::get_social_profile_url( $user['ID'] );
			if ( empty( $socialUrl ) ) {
				$socialUrl = null;
			}

			$users[ $user['ID'] ] = [
				'about'        => null,
				'avatar_url'   => AnyCommentUserMeta::get_social_avatar( $user['ID'] ),
				'created_date' => $user['user_registered'],
				'first_name'   => $user['display_name'],
				'id'           => $user['ID'],
				'is_moderator' => false,
				'last_name'    => null,
				'meta'         => [ 'badge' => null ],
				'badge'        => null,
				'name'         => $user['display_name'],
				'provider'     => $provider,
				'provider_id'  => null,
				'sex'          => null,
				'social_url'   => $socialUrl,
				'username'     => null,
			];
		}

		$totalCount = $this->comments->countByPostId( $postId );
		$totalCount = intval( $totalCount );

		return [
			'items'  => $items,
			'meta'   => [],
			'page'   => $this->preparePageInformation( $postId ),
			'users'  => $users,
			'_links' => [],
			'_meta'  => [
				'currentPage' => $page,
				'pageCount'   => round( $totalCount / $perPage, 0 ),
				'perPage'     => $perPage,
				'totalCount'  => $totalCount,
			]
		];
	}

	private function preparePageInformation( $pageId ) {
		$post = get_post( $pageId );

		return [
			'comments_enabled' => comments_open( $post->ID ),
			'id'               => $post->ID,
			'page_title'       => $post->post_title,
			'rating_average'   => AnyCommentRating::get_average_by_post( $post->ID ),
			'rating_count'     => AnyCommentRating::get_count_by_post( $post->ID ),
			'url'              => get_permalink( $post ),
		];
	}
}
