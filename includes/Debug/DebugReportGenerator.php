<?php

namespace AnyComment\Debug;

use AnyComment\Interfaces\ReportGeneratorImpl;

/**
 * Class AnyCommentDebugReport is used to generate debug report for users to help developers to  understand the cause
 * of certain problems.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment
 */
class DebugReportGenerator implements ReportGeneratorImpl
{
    /**
     * Generate report.
     *
     * @param array|null $preparedData Array when prepared data should be used, otherwise data from prepare() will be used.
     *
     * @return string
     * @see AnyCommentDebugReport::prepare() for further information.
     */
    public function generate()
    {
        $preparedData = $this->prepare();

        $text = '';
        foreach ($preparedData as $key => $debug) {
            $text .= sprintf("%s: %s\n\n", $debug['name'], $debug['value']);
        }

        return $text;
    }

    /**
     * Prepare report data.
     *
     * @return array
     */
    protected function prepare()
    {
        global $wp_version;

        $debugData = [
            ['name' => __('AnyComment Version', 'anycomment'), 'value' => AnyComment()->version],
            ['name' => __('WordPress Version', 'anycomment'), 'value' => $wp_version],
            ['name' => __('PHP Version', 'anycomment'), 'value' => PHP_VERSION],
            ['name' => __('Locale', 'anycomment'), 'value' => get_locale()],
            [
                'name'  => __('Extensions Loaded', 'anycomment'),
                'value' => implode(', ', get_loaded_extensions()),
            ],
            [
                'name'  => __('Generic Settings', 'anycomment'),
                'value' => json_encode(get_option('anycomment-generic')),
            ],
            ['name' => __('Active Plugins', 'anycomment'), 'value' => $this->get_active_plugins_formatted()],
            ['name' => __('Generated at', 'anycomment'), 'value' => date('c')],
            ['name' => 'Rest URL', 'value' => get_rest_url()],
        ];

        return $debugData;
    }

    /**
     * Get list of active plugins and prepare pretty print for them.
     *
     * @return string
     */
    protected function get_active_plugins_formatted()
    {
        $active_plugins = get_option('active_plugins', null);

        if ($active_plugins === null) {
            return '';
        }

        $all_plugins  = get_plugins();
        $pretty_print = '' . PHP_EOL;

        foreach ($active_plugins as $active_plugin) {
            if (isset($all_plugins[$active_plugin])) {
                $advanced_info = $all_plugins[$active_plugin];
                $name          = isset($advanced_info['Name']) ? $advanced_info['Name'] : '';
                $version       = isset($advanced_info['Version']) ? $advanced_info['Version'] : '';

                $pretty_print .= sprintf("%s (%s)" . PHP_EOL, $name, $version);
            }
        }

        return $pretty_print;
    }
}
