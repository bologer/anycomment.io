<?php

namespace AnyComment\Import;

use AnyComment\AnyCommentCore;

/**
 * Class HyperComments is used to export comments from HyperComments to AnyComment or local database.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Import
 */
class HyperComments {

	const META_ID = 'hc_id'; // Comment meta id

	/**
	 * @var null|string URL where to download XML file and start parsing.
	 */
	private $_url = null;

	private $parser;
	private $tmpFile;
	private $tmpHandle;
	private $buffer;

	/**
	 * @var null|string Current active post
	 */
	private $post_url = null;

	/**
	 * @var \WP_Post|null Instance of post if it was possible to retrieve base on URL.
	 */
	private $post;

	/**
	 * @var array Current processing comment data.
	 */
	private $comment = [];

	/**
	 * @var string
	 */
	private $save_dir = null;

	/**
	 * @var string Currently running tag.
	 */
	private $tag = null;

	/**
	 * HyperComments constructor.
	 *
	 * @param string $url URL where to download XML file.
	 */
	public function __construct( $url ) {
		$this->_url = $url;

		$this->prepare_save_dir();
	}

	/**
	 * Start processing comments.
	 *
	 * @return bool
	 */
	public function process() {

		if ( empty( $this->_url ) ) {
			return false;
		}

		if ( $this->download() ) {
			$this->read_by_pieces( $this->save_dir, 4096 );
			$this->clean_up();
		}

		return true;
	}

	/**
	 * Revert comments back to original state.
	 *
	 * It would delete all imported comments from database.
	 *
	 * @return bool
	 */
	public static function revert() {
		global $wpdb;

		$sql = "DELETE c, cm FROM {$wpdb->comments} c";
		$sql .= " LEFT JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id";
		$sql .= $wpdb->prepare( " WHERE cm.meta_key = %s", self::META_ID );

		$rows = $wpdb->query( $sql );

		return false !== $rows;
	}

	/**
	 * Prepare saving directory.
	 */
	public function prepare_save_dir() {
		$upload_dirs = wp_upload_dir();

		$unique_name = wp_unique_filename( $upload_dirs['basedir'], uniqid() );

		$this->save_dir = $upload_dirs['basedir'] . DIRECTORY_SEPARATOR . $unique_name . '.xml';
	}

	/**
	 * @return bool
	 */
	public function download() {
		$response = wp_remote_get( $this->_url, [
			'stream'   => true,
			'filename' => $this->save_dir,
		] );

		if ( is_wp_error( $response ) ) {
			$this->delete_saved_file();

			return false;
		}

		return true;
	}

	/**
	 * Read comments by pieces.
	 *
	 * @param string $file Absolute path to file.
	 * @param int $chunkSize Chunk size. Default is 4096.
	 *
	 * @return bool
	 */
	public function read_by_pieces( $file, $chunkSize = 4096 ) {

		if ( file_exists( $file ) ) {

			try {
				$this->parser = xml_parser_create( 'UTF-8' );
				xml_set_object( $this->parser, $this );
				xml_set_element_handler( $this->parser, 'tag_start', 'tag_end' );
				xml_set_character_data_handler( $this->parser, 'cdata' );
				$handle = fopen( $file, 'rb' );
				while ( $chunk = fread( $handle, $chunkSize ) ) {
					xml_parse( $this->parser, $chunk, false );
				}
				xml_parse( $this->parser, '', true );
				fclose( $handle );
				xml_parser_free( $this->parser );

			} catch ( \Exception $exception ) {
				AnyCommentCore::logger()->error( sprintf(
					'Failed  read HyperComments file "%s" as exception was thrown: "%s", in %s:%s',
					$file,
					$exception->getMessage(),
					$exception->getFile(),
					$exception->getLine()
				) );

				$this->clean_up();

				return false;
			}
		}

		return true;
	}

	/**
	 * Process starting tag.
	 *
	 * @param $parser
	 * @param $tag
	 * @param $attr
	 */
	public function tag_start( $parser, $tag, $attr ) {
		$this->tag = $tag;

		if ( $tag == 'COMMENT' && ! empty( $this->post ) ) {
			$this->tmpFile   = tempnam( __DIR__, 'xml' );
			$this->tmpHandle = fopen( $this->tmpFile, 'wb' );
		}
	}

	/**
	 * Process ending tag.
	 *
	 * @param $parser
	 * @param $tag
	 */
	public function tag_end( $parser, $tag ) {
		if ( $tag == 'COMMENT' ) {
			$this->save_comment();
			$this->comment = [];
		} elseif ( $tag === 'POST' ) {
			$this->post_url = null;
			$this->post     = null;
		} elseif ( $this->tmpHandle ) {
			if ( $this->buffer ) {
				fwrite( $this->tmpHandle, base64_decode( $this->buffer ) );
				$this->buffer = '';
			}
			fclose( $this->tmpHandle );
			$this->tmpHandle = null;
		}
	}

	/**
	 * Process tag data.
	 *
	 * @param $parser
	 * @param $data
	 */
	public function cdata( $parser, $data ) {

		switch ( $this->tag ) {
			case 'ID':
				$this->comment['unused']['id'] = $data;
				break;
			case 'PARENT_ID':
				$this->comment['unused']['parent_id'] = $data;
				break;
			case 'TEXT':
				if ( isset( $this->comment['comment_content'] ) ) {
					// This allows to glue together long comments
					$this->comment['comment_content'] .= $data;
				} else {
					// This is for short or one-line comments
					$this->comment['comment_content'] = $data;
				}
				break;
			case 'NICK':
				$this->comment['comment_author'] = $data;
				break;
			case 'TIME':
				$this->comment['comment_date']     = date( 'Y-m-d H:i:s', strtotime( $data ) );
				$this->comment['comment_date_gmt'] = date( 'Y-m-d H:i:s', strtotime( $data ) );
				break;
			case 'IP':
				$this->comment['comment_author_ip'] = $data;
				break;
			case 'EMAIL':
				$this->comment['comment_author_email'] = $data;
				break;
		}

		if ( $this->tag === 'URL' ) {
			$this->post_url = trim( $data );
			$this->post     = get_post( url_to_postid( $this->post_url ) );
		} elseif ( $this->tmpHandle ) {
			$data = trim( $data );
			if ( $this->buffer ) {
				$data         = $this->buffer . $data;
				$this->buffer = '';
			}
			if ( 0 != ( $modulo = strlen( $data ) % 4 ) ) {
				$this->buffer = substr( $data, - $modulo );
				$data         = substr( $data, 0, - $modulo );
			}
			fwrite( $this->tmpHandle, base64_decode( $data ) );
		}
	}

	/**
	 * Save processed comment to database.
	 */
	public function save_comment() {

		if ( empty( $this->post ) ) {
			return false;
		}

		$commentdata = [];

		$parsed_comment = $this->comment;

		$unused = $parsed_comment['unused'];

		unset( $parsed_comment['unused'] );

		if ( isset( $unused['parent_id'] ) && ! empty( $unused['parent_id'] ) ) {
			global $wpdb;
			$sql = "SELECT * FROM {$wpdb->commentmeta} WHERE meta_key = %s AND meta_value = %s";

			$prepared_sql = $wpdb->prepare( $sql, self::META_ID, $unused['parent_id'] );
			$row          = $wpdb->get_row( $prepared_sql, ARRAY_A );

			if ( ! empty( $row ) ) {
				$commentdata['comment_parent'] = $row['comment_id'];
			}
		}

		$commentdata['comment_post_ID'] = $this->post->ID;

		$commentdata = array_merge( $commentdata, $parsed_comment );

		$commentdata['comment_content'] = wpautop( $commentdata['comment_content'] );

		$comment_id = wp_insert_comment( $commentdata );

		if ( false !== $comment_id ) {
			add_comment_meta( $comment_id, self::META_ID, $unused['id'] );
		}

		return true;
	}

	/**
	 * This make clean-up task.
	 */
	public function clean_up() {
		$this->delete_saved_file();
	}

	/**
	 * Delete saved file if exists.
	 *
	 * @return bool
	 */
	public function delete_saved_file() {
		if ( file_exists( $this->save_dir ) ) {
			if ( @unlink( $this->save_dir ) ) {
				return true;
			}
		}

		return false;
	}
}
