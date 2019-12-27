<?php

namespace AnyComment\Base;

/**
 * Class BaseObject is base class implementation.
 *
 * @since 0.0.99
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Base
 */
class BaseObject
{
    /**
     * BaseObject constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initiation class. Called by class constructor.
     */
    public function init()
    {
    }
}
