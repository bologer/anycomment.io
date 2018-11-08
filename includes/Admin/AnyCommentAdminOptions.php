<?php

namespace AnyComment\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * AnyCommentAdminPages helps to process website authentication.
 */
class AnyCommentAdminOptions {
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
	protected $alert_key = 'anycomment-options-alert';

	/**
	 * @var array Default options. When options specified in this list do not exist in the form options, default ones will be used instead.
	 */
	protected $default_options;

	/**
	 * @var AnyCommentAdminOptions Instance of current object.
	 */
	private static $_instances;

	/**
	 * @var array List of available options.
	 */
	public $options = null;

	/**
	 * AC_Options constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Init class.
	 */
	public function init() {
		register_setting( $this->option_group, $this->option_name );
	}

	/**
	 * Render fields.
	 *
	 * @param array $section ID of the section to render fields for.
	 * @param array $fields List of fields to render.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function render_fields( $section, $fields ) {

		if ( empty( $fields ) ) {
			return false;
		}

		add_settings_section(
			$section['id'],
			$section['name'],
			isset( $section['callback'] ) ? $section['callback'] : null,
			$this->page_slug
		);

		foreach ( $fields as $field ) {

			$args = isset( $field['args'] ) ? $field['args'] : [];

			if ( ! isset( $args['label_for'] ) ) {
				$args['label_for'] = $field['id'];
			}

			if ( isset( $field['type'] ) && ! isset( $args['type'] ) ) {
				$args['type'] = $field['type'];
			}

			if ( ! isset( $args['description'] ) ) {
				$args['description'] = $field['description'];
			}

			if ( isset( $field['options'] ) ) {
				$args['options'] = $field['options'];
			}

			if ( isset( $field['before'] ) ) {
				$args['before'] = $field['before'];
			}

			if ( isset( $field['after'] ) ) {
				$args['after'] = $field['after'];
			}

			add_settings_field(
				$field['id'],
				$field['title'],
				[ $this, 'page_html' ],
				$this->page_slug,
				$section['id'],
				$args
			);
		}

		return true;
	}

	/**
	 * Helper to render select.
	 *
	 * @param array $args List of passed arguments.
	 *
	 * @return string
	 */
	public function input_select( $args ) {
		$for     = $args['for'];
		$name    = $args['name'];
		$options = $args['options'];


		$options_html = '';
		foreach ( $options as $key => $value ) {
			$option_value = $this->get_option( $for );
			$selected     = isset( $this->get_options()[ $for ] ) ? ( selected( $option_value, $key, false ) ) : ( '' );
			$options_html .= <<<EOL
                <option value="$key" $selected>$value</option>
EOL;
		}

		return <<<EOL
            <select name="$name"
                    class="anycomment-select2"
                    id="$for">$options_html</select>
EOL;
	}

	/**
	 * Helper to render checkbox.
	 *
	 * @param array $args List of passed arguments.
	 *
	 * @return string
	 */
	public function input_checkbox( $args ) {
		$for               = $args['for'];
		$name              = $args['name'];
		$value             = $args['value'];
		$title             = $args['title'];
		$description       = $args['description'];
		$checked_attribute = $value !== null ? 'checked="checked"' : '';

		return <<<EOL
			<div class="grid-x">
			    <div class="cell auto shrink align-self-middle">
                    <div class="switch tiny">
                        <input class="switch-input" id="$for" type="checkbox"
                               name="$name"
                            $checked_attribute>
                        <label class="switch-paddle" for="$for"></label>
                    </div>
                </div>
                <div class="cell auto">
                    <label for="$for">$title</label>
                    <p class="description">$description</p>
                </div>
            </div>
EOL;
	}

	/**
	 * Helper to render input color.
	 *
	 * @param array $args List of passed arguments.
	 *
	 * @return string
	 */
	public function input_color( $args ) {
		$for   = $args['for'];
		$name  = $args['name'];
		$value = $args['value'];

		return <<<EOL
            <input type="text" id="$for"
                   name="$name"
                   value="$value"
                   class="anycomment-input-color"
                   pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
            >
EOL;
	}

	/**
	 * Helper to render input text.
	 *
	 * @param array $args List of passed arguments.
	 *
	 * @return string
	 */
	public function input_text( $args ) {
		$type  = $args['type'];
		$for   = $args['for'];
		$name  = $args['name'];
		$value = $args['value'];

		return <<<EOT
            <input type="$type" id="$for" name="$name" value="$value">
EOT;
	}

	/**
	 * Helper to render textarea.
	 *
	 * @param array $args List of passed arguments.
	 *
	 * @return string
	 */
	public function input_textarea( $args ) {
		$for   = $args['for'];
		$name  = $args['name'];
		$value = $args['value'];

		return <<<EOT
            <textarea rows="5" id="$for" name="$name">$value</textarea>
EOT;
	}

	/**
	 * top level menu:
	 * callback functions
	 *
	 * @param bool $wrapper Whether to wrap for with header or not.
	 */
	public function page_html( $wrapper = true ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error( $this->alert_key, 'anycomment_message', __( 'Settings Saved', 'anycomment' ), 'updated' );
		}

		settings_errors( $this->alert_key );
		?>
		<?php if ( $wrapper ): ?>
            <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php endif; ?>
        <form action="options.php" method="post" class="anycomment-form" novalidate>
			<?php
			settings_fields( $this->option_group );
			$this->do_tab_sections( $this->page_slug );
			submit_button( __( 'Save', 'anycomment' ) );
			?>
        </form>
        <script src="<?php echo AnyComment()->plugin_url() ?>/assets/js/forms.js"></script>
		<?php if ( $wrapper ): ?>
            </div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Display tabbed menu.
	 *
	 * @param string $page
	 */
	protected function do_tab_menu( $page ) {
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
	 * Custom wrapper over original WordPress core method.
	 *
	 * Part of the Settings API. Use this in a settings page callback function
	 * to output all the sections and fields that were added to that $page with
	 * add_settings_section() and add_settings_field()
	 *
	 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
	 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
	 * @since 0.0.45
	 *
	 * @param string $page The slug name of the page whose settings sections you want to output
	 * @param bool Whether required to have header or not.
	 */
	protected function do_tab_sections( $page, $includeHeader = true ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		$i = 0;
		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			if ( $includeHeader && isset( $section['title'] ) ) {
				echo "<h2>{$section['title']}</h2>";
			}

			if ( $includeHeader && $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				continue;
			}

			echo '<div id="tab-' . $section['id'] . '" class="anycomment-tabs__container__tab ' . ( $i === 0 ? 'current' : '' ) . '">';
			echo '<div class="grid-x anycomment-form-wrapper">';
			$this->do_settings_fields( $page, $section['id'] );
			echo '</div>';

			echo '</div>';

			$i ++;
		}
	}

	/**
	 * Print out the settings fields for a particular settings section
	 *
	 * Part of the Settings API. Use this in a settings page to output
	 * a specific section. Should normally be called by do_settings_sections()
	 * rather than directly.
	 *
	 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
	 *
	 * @since 2.7.0
	 *
	 * @param string $page Slug title of the admin page who's settings fields you want to show.
	 * @param string $section Slug title of the settings section who's fields you want to show.
	 */
	public function do_settings_fields( $page, $section ) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
			echo $this->do_field( $field );
		}
	}

	public function do_field( $field ) {

		$html = '';
		$args = $field['args'];
		$type = trim( $args['type'] );

		$field_data = [
			'type'        => $type,
			'for'         => esc_attr( $args['label_for'] ),
			'title'       => $field['title'],
			'description' => isset( $args['description'] ) ? trim( $args['description'] ) : '',
			'value'       => $this->get_option( $args['label_for'] ),
			'name'        => $this->option_name . '[' . $args['label_for'] . ']',
			'options'     => isset( $args['options'] ) ? $args['options'] : null
		];

		$label       = '<label for="' . $field_data['for'] . '">' . $field_data['title'] . '</label>';
		$description = ! empty( $field_data['description'] ) ?
			'<p class="description">' . $field_data['description'] . '</p>' :
			'';


		$html .= '<div class="cell anycomment-form-wrapper__field">';

		if ( isset( $args['before'] ) ) {
			$html .= is_callable( $args['before'] ) ?
				call_user_func( $args['before'] ) :
				$args['before'];
		}

		switch ( $type ) {
			case 'text':
			case 'number':
			case 'numeric':
				$html .= $label;
				$html .= $description;
				$html .= $this->input_text( $field_data );
				break;
			case 'toggle':
			case 'checkbox':
				$html .= $this->input_checkbox( $field_data );
				break;
			case 'dropdown':
			case 'list':
			case 'select':
				if ( isset( $field_data['options'] ) ) {
					$html .= $label;
					$html .= $description;
					$html .= $this->input_select( $field_data );
				}
				break;
			case 'textarea':
				$html .= $label;
				$html .= $description;
				$html .= $this->input_textarea( $field_data );
				break;
			case 'color':
				$html .= $label;
				$html .= $description;
				$html .= $this->input_color( $field_data );
				break;
			default:
		}

		if ( isset( $args['after'] ) ) {
			$html .= is_callable( $args['after'] ) ?
				call_user_func( $args['after'] ) :
				$args['after'];
		}

		$html .= '</div>';


		return $html;
	}

	/**
	 * Check whether there are any options set on model.
	 *
	 * @return bool
	 */
	public function has_options() {
		$options = $this->get_options();

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
	 * Get single option.
	 *
	 * @param string $name Options name to search for.
	 *
	 * @return mixed|null
	 */
	public function get_option( $name ) {
		$options = $this->get_options();

		$optionValue = isset( $options[ $name ] ) ? trim( $options[ $name ] ) : null;

		return ! empty( $optionValue ) ? $optionValue : null;
	}

	/**
	 * Get list of social options.
	 * @return array|null
	 */
	public function get_options() {
		if ( $this->options === null ) {
			$this->options = get_option( $this->option_name, null );
		}

		if ( ! empty( $this->default_options ) ) {
			foreach ( $this->default_options as $key => $optionValue ) {
				$setDefault = ! isset( $this->options[ $key ] ) && ! strpos( $key, 'toggle' ) ||
				              isset( $this->options[ $key ] ) && $this->options[ $key ] != 0 && empty( $this->options[ $key ] );
				if ( $setDefault ) {
					$this->options[ $key ] = $optionValue;
				}
			}
		}

		return $this->options;
	}

	/**
	 * Get instance of currently running class.
	 * @return self
	 */
	public static function instance() {
		$className = get_called_class();

		if ( ! isset( self::$_instances[ $className ] ) ) {
			self::$_instances[ $className ] = new $className( false );
		}

		return self::$_instances[ $className ];
	}
}