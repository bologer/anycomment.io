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
    private $_url = 'https://anycomment.io';

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
        $model = new self();
        if (ANYCOMMENT_DEBUG) {
            // todo: move to config
            $model->setUrl('http://anyservice.loc');
        }

        return ($model);
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
            'redirection' => 10,
            'sslverify' => false,
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
			'redirection' => 10,
			'sslverify' => false,
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

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
    }

    /**
     * Check whether app id and key available.
     *
     * @return bool
     */
    public static function is_ready()
    {
        $app_id = AnyCommentServiceApi::getSyncAppId();
        $api_key = AnyCommentServiceApi::getSyncApiKey();
        return !empty($app_id) && !empty($api_key);
    }

    /**
     * @return string
     */
    public static function getSyncAppIdOptionName()
    {
        return 'anycomment_sync_app_id';
    }

    /**
     * @return string
     */
    public static function getSyncApiKeyOptionName()
    {
        return 'anycomment_sync_api_key';
    }

    /**
     * Get sync App ID from SaaS.
     *
     * @param null $default
     * @return mixed|void
     */
    public static function getSyncAppId($default = null)
    {
        return get_option(static::getSyncAppIdOptionName(), $default);
    }

    /**
     * Get sync API key from SaaS.
     *
     * @param null $default
     * @return mixed|void
     */
    public static function getSyncApiKey($default = null)
    {
        return get_option(static::getSyncApiKeyOptionName(), $default);
    }
}
