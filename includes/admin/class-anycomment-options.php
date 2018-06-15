<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AC_SocialSettingPage')) :
    /**
     * AnyCommentAdminPages helps to process website authentication.
     */
    class AC_Options
    {
        protected $option_group;
        protected $option_name;

        protected $page_slug;
        protected $alert_key;

        /**
         * @var AC_Options Instance of current object.
         */
        private static $instance;

        /**
         * @var array List of available options.
         */
        public $options = null;

        /**
         *
         * @param $page
         * @param $section_id
         * @param array $fields
         */
        public function render_fields($page, $section_id, array $fields)
        {
            foreach ($fields as $field) {

                $args = isset($field['args']) ? $field['args'] : [];

                if (!isset($args['label_for'])) {
                    $args['label_for'] = $field['id'];
                }

                if (!isset($args['description'])) {
                    $args['description'] = $field['description'];
                }

                add_settings_field(
                    $field['id'],
                    $field['title'],
                    [$this, $field['callback']],
                    $page,
                    $section_id,
                    $args
                );
            }
        }

        /**
         * Helper to render checkbox.
         * @param array $args List of passed arguments.
         */
        public function input_checkbox($args)
        {
            ?>
            <input type="checkbox" id="<?= esc_attr($args['label_for']); ?>"
                   name="<?= $this->option_name ?>[<?= esc_attr($args['label_for']); ?>]" <?= $this->getOption($args['label_for']) !== null ? 'checked="checked"' : '' ?>>
            <?php if (isset($args['description'])): ?>
            <p class="description"><?= $args['description'] ?></p>
        <?php endif; ?>
            <?php
        }

        /**
         * Helper to render input text.
         * @param array $args List of passed arguments.
         */
        public function input_text($args)
        {
            ?>
            <input type="text" id="<?= esc_attr($args['label_for']); ?>"
                   name="<?= $this->option_name ?>[<?= esc_attr($args['label_for']); ?>]"
                   value="<?= $this->getOption($args['label_for']) ?>">
            <?php if (isset($args['description'])): ?>
            <p class="description"><?= $args['description'] ?></p>
        <?php endif; ?>
            <?php
        }

        /**
         * top level menu:
         * callback functions
         */
        public function page_html()
        {
            if (!current_user_can('manage_options')) {
                return;
            }

            if (isset($_GET['settings-updated'])) {
                add_settings_error($this->alert_key, 'anycomment_message', __('Settings Saved', 'anycomment'), 'updated');
            }

            settings_errors($this->alert_key);
            ?>
            <div class="wrap">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                <form action="options.php" method="post">
                    <?php
                    settings_fields($this->option_group);
                    do_settings_sections($this->page_slug);
                    submit_button(__('Save', 'anycomment'));
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Get single option.
         * @param string $name Options name to search for.
         * @return mixed|null
         */
        public function getOption($name)
        {
            $options = $this->getOptions();

            $optionValue = isset($options[$name]) ? trim($options[$name]) : null;

            return !empty($optionValue) ? $optionValue : null;
        }

        /**
         * Get list of social options.
         * @return array|null
         */
        public function getOptions()
        {
            if ($this->options === null) {
                $this->options = get_option($this->option_name, null);
            }

            return $this->options;
        }

        public static function instance()
        {
            if (self::$instance === null) {
                self::$instance = new AC_Options();
            }

            return self::$instance;
        }
    }
endif;

