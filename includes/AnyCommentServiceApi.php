<?php

namespace AnyComment;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class AnyCommentServiceApi
{
    /**
     * @var string API url.
     */
    private $_url = 'http://anyservice.loc';

    /**
     * @var string API version.
     */
    private $_version = 'v1';

    /**
     * @var int Timeout in seconds.
     */
    private $_timeout = 15;


    /**
     * Constructs object itself.
     *
     * @return AnyCommentServiceApi
     */
    public static function request()
    {
        return (new self);
    }

    /**
     * Send POST request.
     * @param $route
     * @param $body
     * @param array $params
     * @return array|\WP_Error
     */
    public function post($route, $body, $params = [])
    {
        return wp_remote_post($this->buildUrl($route, $params), [
            'timeout' => $this->_timeout,
            'body' => $body,
            'cookies' => ANYCOMMENT_DEBUG ? [
                'XDEBUG_SESSION' => 'XDEBUG_ECLIPSE'
            ] : []
        ]);
    }

    /**
     * Send GET request.
     *
     * @param $route
     * @param array $params
     * @return array|\WP_Error
     */
    public function get($route, $params = [])
    {
        return wp_remote_get($this->buildUrl($route, $params), [
            'timeout' => $this->_timeout,
            'cookies' => ANYCOMMENT_DEBUG ? [
                'XDEBUG_SESSION' => 'XDEBUG_ECLIPSE'
            ] : []
        ]);
    }

    /**
     * Build url from provided path and params.
     * @param $path
     * @param array $params
     * @return string
     */
    protected function buildUrl($path, $params = [])
    {
        $url = sprintf('%s/%s/%s', rtrim($this->_url, '/'), $this->_version, ltrim($path, '/'));

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }
}
