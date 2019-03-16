<?php

namespace AnyComment\Options;


class AnyCommentOptionManager {
	/**
	 * @var string Options group.
	 */
	protected $option_group;

	/**
	 * @var string Option name.
	 */
	protected $option_name;

	/**
	 * @var string Page slug.
	 */
	protected $page_slug;

	/**
	 * @var string Key used to display option alers.
	 */
	protected $alert_key = 'anycomment-form-alert';

	/**
	 * @var array Default options. When options specified in this list do not exist in the form options, default ones will be used instead.
	 */
	protected $default_options;

	/**
	 * @var AnyCommentOptionManager Instance of current object.
	 */
	private static $_instances;

	/**
	 * @var AnyCommentOption|null
	 */
	public $fielder = null;

	/**
	 * @var null|AnyCommentOption[]
	 */
	public $options = null;

	/**
	 * @var string Associative list of properties passed to field class.
	 * @see AnyCommentSection conturctor for further information about passed options.
	 */
	protected $section_options = [];

	/**
	 * @var array Associative list of properties passed to field class.
	 * @see AnyCommentField conturctor for further information about passed options.
	 */
	protected $field_options = [];


	/**
	 * AC_Options constructor.
	 */
	public function __construct () {
		add_action( 'init', [ $this, 'init' ] );

		$transient_key = $this->alert_key . get_current_user_id();

		$transient = get_transient( $transient_key );

		if ( ! empty( $transient ) ) {

			$transient = (array) $transient;

			$type    = (string) $transient['type'];
			$message = (string) $transient['message'];

			delete_transient( $transient_key );

			add_action( 'admin_notices', function () use ( $type, $message ) {
				?>
                <div class="notice notice-<?php echo $type ?> is-dismissible">
                    <p><?php esc_html_e( $message ) ?></p>
                </div>
				<?php
			} );
		}

		add_action( 'admin_post_' . $this->option_name, function () use ( $transient_key ) {

			if ( ! ( $this->save_form() instanceof \WP_Error ) ) {
				set_transient( $transient_key, [
					'type'    => 'success',
					'message' => __( 'Saved successfully', 'anycomment' ),
				], 60 );

			} else {
				set_transient( $transient_key, [
					'type'    => 'error',
					'message' => __( 'Failed to save form. Please try again.', 'anycomment' ),
				], 60 );
			}

			wp_redirect( isset( $_POST['redirect'] ) ? $_POST['redirect'] : '/' );
			exit();
		} );
	}

	/**
	 * Init class.
	 */
	public function init () {
		register_setting( $this->option_group, $this->option_name );
	}

	/**
	 * Process form submission.
	 *
	 * @return mixed|\WP_Error
	 */
	public function save_form () {

		if ( ! wp_verify_nonce( $_POST['nonce'], $this->option_name ) ) {
			return new \WP_Error( '', __( 'Invalid nonce', 'anycomment' ) );
		}

		$option_name = isset( $_POST['action'] ) ? sanitize_text_field( trim( $_POST['action'] ) ) : null;
		$options     = $_POST;


		if ( empty( $option_name ) ) {
			return new \WP_Error( '', __( 'No page slug provided', 'anycomment' ) );
		}

		unset( $options['action'] );
		unset( $options['redirect'] );
		unset( $options['nonce'] );

		/**
		 * Fires before settings were updated.
		 *
		 * @since 0.0.81
		 *
		 * @param string $option_name Name of the option which is being updated.
		 * @param array $options List of options to update without option name.
		 */
		do_action( 'anycomment/admin/options/update', $option_name, $options );

		$this->update_db_option( $options, $option_name );

		return true;
	}

	/**
	 * Add new option to the list.
	 *
	 * @param AnyCommentOption[]
	 *
	 * @return void
	 */
	public function add_option ( $options ) {
		$this->options[] = $options;
	}

	/**
	 * @return AnyCommentOption
	 */
	public function form () {
		$this->fielder = new AnyCommentOption( [
			'page_slug'    => $this->page_slug,
			'option_name'  => $this->option_name,
			'option_group' => $this->option_group,
		] );

		$this->add_option( $this->fielder );

		return $this->fielder;
	}

	/**
	 * Start section builder.
	 *
	 * @return AnyCommentSection
	 */
	public function section_builder () {
		return new AnyCommentSection( $this->section_options );
	}

	/**
	 * Start building new field.
	 *
	 * @return AnyCommentField
	 */
	public function field_builder () {

		/**
		 * Set page slug for field when not defined on the class level.
		 */
		$options = $this->field_options;
		if ( ! isset( $options['option_name'] ) ) {
			$options['option_name'] = $this->option_name;
		}

		return new AnyCommentField( $options );
	}

	/**
	 * {@inheritdoc}
	 */
	public function run () {
		$html = '';

		$options = $this->options;

		$html .= '<form action="' . esc_url( admin_url( "admin-post.php" ) ) . '" id="' . $this->get_page_slug() . '" method="post" class="anycomment-form" novalidate>';

		$redirect_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url( $_SERVER['REQUEST_URI'] ) : '';

		$html .= '<input type="hidden" name="redirect" value="' . $redirect_url . '">';
		$html .= '<input type="hidden" name="action" value="' . $this->option_name . '">';
		$html .= '<input type="hidden" name="nonce" value="' . wp_create_nonce( $this->option_name ) . '" />';

		foreach ( $options as $option ) {
			$sections = $option->get_sections();

			if ( ! empty( $sections ) ) {
				foreach ( $sections as $section ) {
					$html .= $section;
				}
			} else {
				$fields = $option->get_fields();
				foreach ( $fields as $field ) {
					$html .= $field;
				}
			}
		}

		$html .= '<input type="submit" class="button" value="' . __( 'Save', 'anycomment' ) . '">';

		$html .= '</form>';

		return $html;
	}

	/**
	 * Get page slug.
	 *
	 * @return mixed
	 */
	public function get_page_slug () {
		return str_replace( '-', '_', $this->page_slug );
	}

	/**
	 * Display tabbed menu.
	 *
	 * @param string $page
	 */
	protected function do_tab_menu () {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		echo '<ul>';

		$i = 0;
		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			$activeClass = $i === 0 ? 'class="current"' : '';
			echo '<li ' . $activeClass . ' data-tab="' . $section['id'] . '">
				<a href="#tab-' . $section['id'] . '">' . $section['title'] . '</a>
				</li>';
			$i ++;
		}
		echo '</ul>';

		?>
        <script>
            var $ = jQuery;
            $('.anycomment-tabs__menu li').on('click', function (e) {
                e.preventDefault();
                doTab($(this));
                return false;
            });


            function doTab(el) {
                var $ = jQuery,
                    data = (el.attr('data-tab') || ''),
                    tab_id = (data.indexOf('#tab-') === -1 ? ('#tab-' + data) : data);

                if (!data) {
                    return false;
                }

                $('.anycomment-tabs__menu li').removeClass('current');
                $('.anycomment-tabs__container__tab').removeClass('current');

                el.addClass('current');
                $(tab_id).addClass('current');
            }

            $(document).ready(function () {
                var hash = window.location.hash.trim();
                if (hash !== '') {
                    var cleanedHash = hash.replace('#tab-', '');
                    console.log(cleanedHash);
                    doTab($('[data-tab="' + cleanedHash + '"]'));
                }
            });
        </script>
		<?php
	}

	/**
	 * Check whether there are any options set on model.
	 *
	 * @return bool
	 */
	public function has_options () {
		$options = $this->get_db_options();

		if ( $options === null ) {
			return false;
		}

		$nonEmptyCount = 0;
		foreach ( $options as $key => $optionValue ) {
			if ( ! empty( $optionValue ) ) {
				$nonEmptyCount ++;
			}
		}

		return $nonEmptyCount > 0;
	}

	/**
	 * Get list of available options.
	 *
	 * @return AnyCommentOption[]|null
	 */
	public function get_options () {
		return $this->options;
	}

	/**
	 * Get single option.
	 *
	 * @param string $name Options name to search for.
	 *
	 * @return mixed|null
	 */
	public function get_db_option ( $name ) {
		$options = $this->get_db_options();

		$optionValue = isset( $options[ $name ] ) ? trim( $options[ $name ] ) : null;

		return ! empty( $optionValue ) ? $optionValue : null;
	}

	/**
	 * Get list of social options.
	 *
	 * @return AnyCommentOption[]|null
	 */
	public function get_db_options () {

		$option = get_option( $this->option_name, null );

		// When options are not defined yet and there are some default ones,
		// set them for user
		if ( $option === null && ! empty( $this->default_options ) ) {
			$this->update_db_option( $this->default_options, $this->option_name );
		}

		return $option;
	}

	/**
	 * Update db option value.
	 *
	 * @param mixed $value Value of the option.
	 * @param null|string $option_name Option name. When not specified current option_name will be used.
	 */
	public function update_db_option ( $value, $option_name = null ) {

		$option = null;

		if ( $option_name === null ) {
			$option = $this->option_name;
		} else {
			$option = $option_name;
		}

		update_option( $option, $value );
	}

	/**
	 * Get instance of currently running class.
	 *
	 * @return self
	 */
	public static function instance () {
		$className = get_called_class();

		if ( ! isset( self::$_instances[ $className ] ) ) {
			self::$_instances[ $className ] = new $className( false );
		}

		return self::$_instances[ $className ];
	}
}