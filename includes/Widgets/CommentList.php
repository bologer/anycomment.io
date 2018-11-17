<?php

namespace AnyComment\Widgets;

use AnyComment\AnyCommentUser;
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
			'classname'                   => 'anycomment_widget_comment_list',
			'description'                 => __( 'Your site&#8217;s most recent comments displayed using AnyComment.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'anycomment-recent-comments', __( 'Comments', 'anycomment' ), $widget_ops );
		$this->alt_option_name = 'anycomment_widget_comment_list';
	}

	/**
	 * Outputs the default styles for the Recent Comments widget.
	 *
	 * @since 2.8.0
	 */
	public function register_styles() {
		$filename  = sprintf( '%s.min.css', md5( get_class() ) );
		$save_path = AnyComment()->plugin_path() . '/static/css/';

		$full_path = $save_path . $filename;
		$saved_url = AnyComment()->plugin_url() . '/static/css/' . $filename;


		if ( ! file_exists( $full_path ) ) {
			$compiler = new ScssCompiler();

			$is_saved = $compiler
				->set_scss( [ __DIR__ . '/assets/scss/comment-list.scss' ] )
				->compile( $full_path );

			if ( ! $is_saved ) {
				return false;
			}
		}

		wp_enqueue_style( 'anycomment-widget-comment-list', $saved_url, [] );
	}

	/**
	 * Outputs the content for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Comments widget instance.
	 */
	public function widget( $args, $instance ) {
		$this->register_styles();

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$output = '';

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		/**
		 * Filters the arguments for the Recent Comments widget.
		 *
		 * @see WP_Comment_Query::query() for information on accepted arguments.
		 *
		 * @param array $comment_args An array of arguments used to retrieve the recent comments.
		 * @param array $instance Array of settings for the current widget.
		 */
		$comments = get_comments( apply_filters( 'widget_comments_args', array(
			'number'      => $number,
			'status'      => 'approve',
			'post_status' => 'publish'
		), $instance ) );

		$output .= $args['before_widget'];
		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}


		$output .= '<ul class="anycomment-comments-widget">';
		if ( is_array( $comments ) && $comments ) {
			/**
			 * @var $comment \WP_Comment
			 */
			foreach ( (array) $comments as $comment ) {

				$comment_text       = $comment->comment_content;
				$comment_link       = esc_url( get_comment_link( $comment ) );
				$post_title         = get_the_title( $comment->comment_post_ID );
				$author_name        = $comment->comment_author;
				$author_avatar_url  = AnyCommentSocialAuth::get_user_avatar_url( $comment->comment_author_email );
				$author_social_type = AnyCommentUserMeta::get_social_type( $comment->user_id );
				$social_icon        = ! empty( $author_social_type ) ?
					AnyComment()->plugin_url() . "/assets/img/socials/$author_social_type.svg" :
					'';

				$social_logo = ! empty( $author_social_type ) ?
					'<img src="' . $social_icon . '" class="anycomment-avatar-icon" alt="' . esc_html( $author_name ) . '"/>' :
					'';

				$output .= <<<EOT
<li class="anycomment-comments-widget__item">
    <div class="anycomment-comments-widget__item__header">
        <div class="anycomment-comments-widget__item__header-avatar">
            <div class="anycomment-comments-widget__item__header-avatar-wrapper" style="background-image: url('$author_avatar_url');">
                $social_logo
            </div>
        </div>
        <div class="anycomment-comments-widget__item__header-author">$author_name</div>
    </div>
    <div class="anycomment-comments-widget__item__body"><p>$comment_text</p></div>
    <div class="anycomment-comments-widget__item__footer"><a href="$comment_link">$post_title</a></div>
</li>
EOT;
			}
		}
		$output .= '</ul>';
		$output .= $args['after_widget'];

		echo $output;
	}

	/**
	 * Handles updating settings for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance           = $old_instance;
		$instance['title']  = sanitize_text_field( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );

		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Comments widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title  = isset( $instance['title'] ) ? $instance['title'] : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>"/></p>

        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>"
                   name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1"
                   value="<?php echo $number; ?>" size="3"/></p>
		<?php
	}
}