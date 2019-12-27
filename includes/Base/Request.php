<?php

namespace AnyComment\Base;

/**
 * Class Request is a singletone which helps to work with request.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Base
 */
class Request extends BaseObject
{
    /**
     * Get specific get param value or the whole array.
     *
     * @param null $name Get parameter name.
     *
     * @return array|string|null Array GET param is in array format or $name is null. String when GET parameter
     * is not empty.
     */
    public function get($name = null)
    {
        if ($name === null) {
            return $_GET;
        }

        return isset($_GET[$name]) ? $_GET['name'] : null;
    }

    /**
     * Get specific get param value or the whole array.
     *
     * @param null $name POST parameter name.
     *
     * @return array|string|null Array POST param is in array format or $name is null. String when POST parameter
     * is not empty.
     */
    public function post($name = null)
    {
        if ($name === null) {
            return $_POST;
        }

        return isset($_POST[$name]) ? $_POST['name'] : null;
    }
}
