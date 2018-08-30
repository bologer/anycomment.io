<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnyCommentAdminOptions' ) ) :
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
		 * @param string $page Page to be rendered fields for.
		 * @param string $section_id ID of the section to render fields for.
		 * @param array $fields List of fields to render.
		 *
		 * @return bool True on success, false on failure.
		 */
		public function render_fields( $page, $section_id, $fields ) {

			if ( empty( $fields ) ) {
				return false;
			}

			foreach ( $fields as $field ) {

				$args = isset( $field['args'] ) ? $field['args'] : [];

				if ( ! isset( $args['label_for'] ) ) {
					$args['label_for'] = $field['id'];
				}

				if ( ! isset( $args['description'] ) ) {
					$args['description'] = $field['description'];
				}

				if ( isset( $field['options'] ) ) {
					$args['options'] = $field['options'];
				}

				add_settings_field(
					$field['id'],
					$field['title'],
					[ $this, $field['callback'] ],
					$page,
					$section_id,
					$args
				);
			}

			return true;
		}

		/**
		 * Helper to render select.
		 *
		 * @param array $args List of passed arguments.
		 */
		public function input_select( $args ) {
			?>
            <select name="<?= $this->option_name ?>[<?= esc_attr( $args['label_for'] ); ?>]"
                    class="anycomment-select2"
                    id="<?= esc_attr( $args['label_for'] ); ?>">
				<?php
				$options = $args['options'];

				if ( isset( $options ) ):
					foreach ( $options as $key => $value ):
						?>
                        <option value="<?= $key ?>" <?= isset( $this->getOptions()[ $args['label_for'] ] ) ? ( selected( $this->getOption( $args['label_for'] ), $key, false ) ) : ( '' ); ?>><?= $value ?></option>
					<?php
					endforeach;
				endif; ?>
            </select>
            <div class="clearfix"></div>
            <p class="description"><?= $args['description'] ?></p>
			<?php
		}

		/**
		 * Helper to render checkbox.
		 *
		 * @param array $args List of passed arguments.
		 */
		public function input_checkbox( $args ) {
			?>
            <label class="switch">
                <input type="checkbox" id="<?= esc_attr( $args['label_for'] ); ?>"
                       name="<?= $this->option_name ?>[<?= esc_attr( $args['label_for'] ); ?>]" <?= $this->getOption( $args['label_for'] ) !== null ? 'checked="checked"' : '' ?>>
                <span></span>
            </label>
			<?php if ( isset( $args['description'] ) ): ?>
                <div class="clearfix"></div>
                <p class="description"><?= $args['description'] ?></p>
			<?php endif; ?>
			<?php
		}

		/**
		 * Helper to render input text.
		 *
		 * @param array $args List of passed arguments.
		 */
		public function input_text( $args ) {
			?>
            <input type="text" id="<?= esc_attr( $args['label_for'] ); ?>"
                   name="<?= $this->option_name ?>[<?= esc_attr( $args['label_for'] ); ?>]"
                   value="<?= $this->getOption( $args['label_for'] ) ?>">
			<?php if ( isset( $args['description'] ) ): ?>
                <div class="clearfix"></div>
                <p class="description"><?= $args['description'] ?></p>
			<?php endif; ?>
			<?php
		}

		/**
		 * Helper to render textarea.
		 *
		 * @param array $args List of passed arguments.
		 */
		public function input_textarea( $args ) {
			?>
            <textarea rows="5" id="<?= esc_attr( $args['label_for'] ); ?>"
                      name="<?= $this->option_name ?>[<?= esc_attr( $args['label_for'] ); ?>]"><?= $this->getOption( $args['label_for'] ) ?></textarea>
			<?php if ( isset( $args['description'] ) ): ?>
                <div class="clearfix"></div>
                <p class="description"><?= $args['description'] ?></p>
			<?php endif; ?>
			<?php
		}

		/**
		 * Helper to render input of type number.
		 *
		 * @param array $args List of passed arguments.
		 */
		public function input_number( $args ) {
			?>
            <input type="number" id="<?= esc_attr( $args['label_for'] ); ?>"
                   name="<?= $this->option_name ?>[<?= esc_attr( $args['label_for'] ); ?>]"
                   value="<?= $this->getOption( $args['label_for'] ) ?>">
			<?php if ( isset( $args['description'] ) ): ?>
                <div class="clearfix"></div>
                <p class="description"><?= $args['description'] ?></p>
			<?php endif; ?>
			<?php
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
            <form action="options.php" method="post" class="anycomment-form">
				<?php
				settings_fields( $this->option_group );
				do_settings_sections( $this->page_slug );
				submit_button( __( 'Save', 'anycomment' ) );
				?>
            </form>
            <script src="<?= AnyComment()->plugin_url() ?>/assets/js/forms.js"></script>
            <script src="<?= AnyComment()->plugin_url() ?>/assets/js/select2.min.js"></script>
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
				echo '<li data-tab="' . $section['id'] . '">
				<a href="#tab-' . $section['id'] . '">' . $section['title'] . '</a>
				</li>';
				$i ++;
			}
			echo '</ul>';

			?>
            <script>
                jQuery('.anycomment-tabs__menu li').on('click', function (e) {
                    e.preventDefault();

                    var data = jQuery(this).attr('data-tab') || '';
                    var tab_id = '#tab-' + data;

                    if (!data) {
                        return false;
                    }

                    jQuery('.anycomment-tabs__menu li').removeClass('current');
                    jQuery('.anycomment-tabs__container__tab').removeClass('current');

                    jQuery(this).addClass('current');
                    jQuery(tab_id).addClass('current');

                    return false;
                })
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
				if ( $includeHeader && $section['title'] ) {
					echo "<h2>{$section['title']}</h2>\n";
				}

				if ( $includeHeader && $section['callback'] ) {
					call_user_func( $section['callback'], $section );
				}

				if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
					continue;
				}

				echo '<div id="tab-' . $section['id'] . '" class="anycomment-tabs__container__tab ' . ( $i === 0 ? 'current' : '' ) . '">';
				echo '<div class="form-table-wrapper">';
				echo '<table class="form-table">';
				do_settings_fields( $page, $section['id'] );
				echo '</table>';
				echo '</div>';

				echo '<div class="clearfix"></div></div>';

				$i ++;
			}
		}

		/**
		 * Check whether there are any options set on model.
		 *
		 * @return bool
		 */
		public function hasOptions() {
			$options = $this->getOptions();

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
		public function getOption( $name ) {
			$options = $this->getOptions();

			$optionValue = isset( $options[ $name ] ) ? trim( $options[ $name ] ) : null;

			return ! empty( $optionValue ) ? $optionValue : null;
		}

		/**
		 * Get list of social options.
		 * @return array|null
		 */
		public function getOptions() {
			if ( $this->options === null ) {
				$this->options = get_option( $this->option_name, null );
			}

			if ( ! empty( $this->default_options ) ) {
				foreach ( $this->default_options as $key => $optionValue ) {
					$setDefault = ! isset( $this->options[ $key ] ) && ! strpos( $key, 'toggle' ) ||
					              isset( $this->options[ $key ] ) && empty( $this->options[ $key ] );
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
endif;

