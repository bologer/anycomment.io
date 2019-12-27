<?php

namespace AnyComment\Helpers;

/**
 * Class AnyCommentUrl is helper to work with URL.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Helpers
 */
class Url
{
    /**
     * Main plugin page id.
     */
    const GLOBAL_PAGE_ID = 'anycomment-dashboard';

    /**
     * Helper function to generate URL to specific controller/view-like endpoint.
     *
     * @param array $url
     *
     * @return string|void
     */
    public static function to($url)
    {
        return static::generateUrl($url);
    }

    /**
     * Generates URL from provided parameter.
     *
     * Examples:
     *
     * - ['/'] - would redirect to root plugin page
     * - ['debug/download', 'type' => 'test'] - would redirect to debug controller and download action, which would
     * pass down to download action 'type' parameter.
     *
     * @param array $url
     *
     * @return string|void
     */
    public static function generateUrl($url)
    {
        $normalized_url = null;

        if (is_array($url)) {
            if (count($url) === 1 && isset($url[0]) && $url[0] === '/') {
                $url = null;
            } elseif (isset($url[0]) && strpos($url[0], '/') !== false) {
                $url['r'] = $url[0];
                unset($url[0]);
            }

            if ($url !== null) {
                $normalized_url = http_build_query($url);
            }
        } elseif (is_string($url)) {
            $normalized_url = trim($url);
        }

        $admin_url = admin_url('admin.php?page=' . self::GLOBAL_PAGE_ID);

        if ($normalized_url === null) {
            return $admin_url;
        }

        return $admin_url . '&' . $normalized_url;
    }
}
