<?php

namespace AnyComment\Widgets\Native;

use AnyComment\AnyCommentCore;
use AnyComment\AnyCommentUserMeta;
use AnyComment\Base\ScssCompiler;
use AnyComment\Rest\AnyCommentSocialAuth;

/**
 * Class CommentList is a widget to display list of comments on
 * the website in style of AnyComment.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Widgets
 */
class CommentList extends \WP_Widget {
	/**
	 * Sets up a new Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'CommentList',
			'description'                 => __( 'Your site&#8217;s most recent comments displayed using AnyComment.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'anycomment-comment-list', __( 'Comments', 'anycomment' ), $widget_ops );
		$this->alt_option_name = 'anycomment-comment-list';
	}

	/**
	 * Outputs the default styles for the Recent Comments widget.
	 *
	 * @param array $instance
	 *
	 * @return string
	 */
	public function compile_styles( $instance ) {
		$mtime = filemtime( AnyComment()->plugin_path() . '/assets/widgets/comment-list/comment-list.scss' );

		$array_hash   = [];
		$array_hash[] = $mtime;
		$array_hash[] = $instance;

		$hash = md5( serialize( $array_hash ) );

		$scss_widget_cache = AnyCommentCore::cache()->getItem( 'anycomment/widgets/comment-list/' . $hash );
		$template          = '<style>%s</style>';

		if ( $scss_widget_cache->isHit() ) {
			return sprintf( $template, $scss_widget_cache->get() );
		}

		$variables = [];

		if ( ! empty( $instance ) ) {
			foreach ( $instance as $key => $value ) {
				if ( false !== strpos( $key, 'scss_' ) ) {
					$clean_key               = trim( str_replace( 'scss_', '', $key ) );
					$clean_key               = str_replace( '_', '-', $clean_key );
					$variables[ $clean_key ] = $value;
				}
			}
		}

		$compiler = new ScssCompiler();

		$compiled_css = $compiler
			->set_scss( [ AnyComment()->plugin_path() . '/assets/widgets/comment-list/comment-list.scss' ] )
			->set_variables( $variables )
			->compile();

		$scss_widget_cache->set( $compiled_css )->save();

		return sprintf( $template, $compiled_css );
	}

	/**
	 * Outputs the content for the current Recent Comments widget instance.
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 *
	 */
	public function widget( $args, $instance ) {
		$output = $this->compile_styles( $instance );

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ! empty( $instance['title'] ) ? esc_html( $instance['title'] ) : '';

		$number          = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		$read_more_limit = ( ! empty( $instance['read_more_limit'] ) ) ? absint( $instance['read_more_limit'] ) : 10;

		if ( ! $number ) {
			$number = 5;
		}

		/**
		 * Filters the arguments for the Recent Comments widget.
		 *
		 * @param array $comment_args An array of arguments used to retrieve the recent comments.
		 * @param array $instance Array of settings for the current widget.
		 *
		 * @see WP_Comment_Query::query() for information on accepted arguments.
		 *
		 */
		$comments = get_comments( apply_filters( 'widget_comments_args', array(
			'number'      => $number,
			'status'      => 'approve',
			'post_status' => 'publish',
		), $instance ) );

		$output .= $args['before_widget'];
		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}


		$output .= '<div id="anycomment-comments-widget-wrapper">';
		$output .= '<ul class="anycomment-comments-widget">';
		if ( is_array( $comments ) && $comments ) {
			/**
			 * @var $comment \WP_Comment
			 */
			foreach ( (array) $comments as $comment ) {

				$comment_text       = wp_trim_words( $comment->comment_content, $read_more_limit );
				$comment_datetime   = date( 'c', strtotime( $comment->comment_date ) );
				$comment_human_time = human_time_diff( current_time( 'timestamp' ), strtotime( $comment->comment_date ) ) . ' ' . esc_html( __( 'ago', 'anycomment' ) );
				$comment_link       = rtrim( get_permalink( $comment->comment_post_ID ), '/' ) . '#comment-' . $comment->comment_ID;
				$post_title         = get_the_title( $comment->comment_post_ID );
				$author_name        = esc_html( $comment->comment_author );
				$author_avatar_url  = AnyCommentSocialAuth::get_user_avatar_url( $comment->comment_author_email );
				$author_social_type = AnyCommentUserMeta::get_social_type( $comment->user_id );
				$social_icon        = ! empty( $author_social_type ) ?
					AnyComment()->plugin_url() . "/assets/img/socials/$author_social_type.svg" :
					'';

				$social_logo = ! empty( $author_social_type ) ?
					'<img src="' . esc_attr( esc_url( $social_icon ) ) . '" class="anycomment-avatar-icon" alt="' . esc_attr( esc_html( $author_name ) ) . '"/>' :
					'';

				$output .= <<<EOT
<li class="anycomment-comments-widget__item">
    <div class="anycomment-comments-widget__item__header">
        <div class="anycomment-comments-widget__item__header-avatar">
            <div class="anycomment-comments-widget__item__header-avatar-wrapper" style="background-image: url('$author_avatar_url');">
                $social_logo
            </div>
        </div>
        <div class="anycomment-comments-widget__item__header-meta">
            <div class="anycomment-comments-widget__item__header-meta--author">$author_name</div>
            <time class="anycomment-comments-widget__item__header-meta--date" datetime="$comment_datetime">$comment_human_time</time>
        </div>  
    </div>
    <div class="anycomment-comments-widget__item__body"><p>$comment_text</p></div>
    <div class="anycomment-comments-widget__item__footer"><a href="$comment_link">$post_title</a></div>
</li>
EOT;
			}
		}
		$output .= '</ul>';
		$output .= '</div>';
		$output .= $args['after_widget'];

		echo wp_kses(
			$output,
			[
				'div'  => [ 'class' => [], 'style' => [] ],
				'ul'   => [ 'class' => [] ],
				'li'   => [ 'class' => [] ],
				'p'    => [],
				'a'    => [ 'href' => [] ],
				'time' => [ 'class' => [], 'datetime' => [] ],
			]
		);
	}

	/**
	 * Handles updating settings for the current Recent Comments widget instance.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Updated settings to save.
	 * @since 2.8.0
	 *
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                          = $old_instance;
		$instance['title']                 = sanitize_text_field( $new_instance['title'] );
		$instance['number']                = absint( $new_instance['number'] );
		$instance['read_more_limit']       = absint( $new_instance['read_more_limit'] );
		$instance['scss_font_size']        = sanitize_text_field( $new_instance['scss_font_size'] );
		$instance['scss_background_color'] = sanitize_text_field( $new_instance['scss_background_color'] );
		$instance['scss_comment_color']    = sanitize_text_field( $new_instance['scss_comment_color'] );
		$instance['scss_author_color']     = sanitize_text_field( $new_instance['scss_author_color'] );
		$instance['scss_avatar_size']      = sanitize_text_field( $new_instance['scss_avatar_size'] );

		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Comments widget.
	 *
	 * @param array $instance Current settings.
	 *
	 * @since 2.8.0
	 *
	 */
	public function form( $instance ) {
		$title           = isset( $instance['title'] ) ? $instance['title'] : '';
		$number          = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$read_more_limit = isset( $instance['read_more_limit'] ) ? absint( $instance['read_more_limit'] ) : 10;

		$font_size        = isset( $instance['scss_font_size'] ) ? $instance['scss_font_size'] : '14px';
		$background_color = isset( $instance['scss_background_color'] ) ? $instance['scss_background_color'] : 'transparent';
		$comment_color    = isset( $instance['scss_comment_color'] ) ? $instance['scss_comment_color'] : '#2A2E2E';
		$author_color     = isset( $instance['scss_author_color'] ) ? $instance['scss_author_color'] : '#1DA1F2';
		$avatar_size      = isset( $instance['scss_avatar_size'] ) ? $instance['scss_avatar_size'] : '30px';
		?>
        <p>
            <label for="<?php echo esc_attr( esc_html( $this->get_field_id( 'title' ) ) ); ?>"><?php _e( 'Title:', 'anycomment' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( esc_html( $this->get_field_id( 'title' ) ) ); ?>"
                   name="<?php echo esc_attr( esc_html( $this->get_field_name( 'title' ) ) ); ?>" type="text"
                   value="<?php echo esc_attr( esc_html( $title ) ); ?>" /></p>

        <p>
            <label for="<?php echo esc_attr( esc_html( $this->get_field_id( 'read_more_limit' ) ) ); ?>"><?php _e( 'Number words before "...":', 'anycomment' ); ?></label>
            <input class="tiny-text"
                   id="<?php echo esc_attr( esc_html( $this->get_field_id( 'read_more_limit' ) ) ); ?>"
                   name="<?php echo esc_attr( esc_html( $this->get_field_name( 'read_more_limit' ) ) ); ?>"
                   type="number" step="1" min="1"
                   value="<?php echo esc_attr( esc_html( $read_more_limit ) ); ?>" size="3" /></p>

        <p>
            <label for="<?php echo esc_attr( esc_html( $this->get_field_id( 'number' ) ) ); ?>"><?php _e( 'Number of comments to show:', 'anycomment' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( esc_html( $this->get_field_id( 'number' ) ) ); ?>"
                   name="<?php echo esc_attr( esc_html( $this->get_field_name( 'number' ) ) ); ?>" type="number"
                   step="1" min="1"
                   value="<?php echo esc_attr( esc_html( $number ) ); ?>" size="3" /></p>

        <p>
            <label for="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_font_size' ) ) ); ?>"><?php _e( 'Font size:', 'anycomment' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_font_size' ) ) ); ?>"
                   name="<?php echo esc_attr( esc_html( $this->get_field_name( 'scss_font_size' ) ) ); ?>" type="text"
                   value="<?php echo esc_attr( esc_html( $font_size ) ); ?>" /></p>

        <p>
            <label for="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_background_color' ) ) ); ?>"><?php _e( 'Background color:', 'anycomment' ); ?></label>
            <input class="widefat"
                   id="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_background_color' ) ) ); ?>"
                   name="<?php echo esc_attr( esc_html( $this->get_field_name( 'scss_background_color' ) ) ); ?>"
                   type="text"
                   value="<?php echo esc_attr( esc_html( $background_color ) ); ?>" /></p>

        <p>
            <label for="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_comment_color' ) ) ); ?>"><?php _e( 'Comment text color:', 'anycomment' ); ?></label>
            <input class="widefat"
                   id="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_comment_color' ) ) ); ?>"
                   name="<?php echo esc_attr( esc_html( $this->get_field_name( 'scss_comment_color' ) ) ); ?>"
                   type="text"
                   value="<?php echo esc_attr( esc_html( $comment_color ) ); ?>" /></p>

        <p>
            <label for="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_author_color' ) ) ); ?>"><?php _e( 'Author name color:', 'anycomment' ); ?></label>
            <input class="widefat"
                   id="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_author_color' ) ) ); ?>"
                   name="<?php echo esc_attr( esc_html( $this->get_field_name( 'scss_author_color' ) ) ); ?>"
                   type="text"
                   value="<?php echo esc_attr( esc_html( $author_color ) ); ?>" /></p>


        <p>
            <label for="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_avatar_size' ) ) ); ?>"><?php _e( 'Author avatar size:', 'anycomment' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( esc_html( $this->get_field_id( 'scss_avatar_size' ) ) ); ?>"
                   name="<?php echo esc_attr( esc_html( $this->get_field_name( 'scss_avatar_size' ) ) ); ?>" type="text"
                   value="<?php echo esc_attr( esc_html( $avatar_size ) ); ?>" /></p>
		<?php
	}
}
