<?php

namespace AnyComment\Controller;

use AnyComment\AnyCommentCore;
use AnyComment\Base\BaseObject;
use AnyComment\Web\BaseController;
use AnyComment\Helpers\AnyCommentInflector;

/**
 * Class AnyCommentControllerManager watching specific GET parameter which holds "controller/action" format and
 * trying to resolve such controller by passing does action inside.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Controller
 */
class ControllerManager extends BaseObject
{
    /**
     * Router parameter used to hold controller/action format.
     */
    const ROUTE_PARAMETER = 'r';
    /**
     * @var array List of GET parameters.
     */
    private $_parameters;

    /**
     * @var string Controller namespace format (where to search for controllers).
     */
    protected $controllerNamespace = '\\AnyComment\Controller\\%sController';

    /**
     * AnyCommentControllerManager constructor.
     *
     * @param array $parameters Get parameters.
     */
    public function __construct($parameters)
    {
        parent::__construct();

        $this->_parameters = $parameters;
    }

    /**
     * Resolve controller based on special GET parameter which holds such information.
     *
     * Notice: Nothing would happen when no parameter provided.
     */
    public function resolve()
    {
        $routeFromParams = isset($this->_parameters[self::ROUTE_PARAMETER]) ?
            $this->_parameters[self::ROUTE_PARAMETER] :
            null;

        if ( ! empty($routeFromParams)) {
            $this->resolveControllerAction($routeFromParams);
        }
    }

    /**
     * Trying to resolve controller & action.
     *
     * Runs controller with action when it was successfully resolved.
     *
     * @param string $route Router in form controller/action.
     */
    protected function resolveControllerAction($route)
    {
        if (strpos($route, '/') !== false) {
            $explodedRouter = explode('/', $route);

            $controllerNamespace = sprintf(
                $this->controllerNamespace,
                AnyCommentInflector::pascalize($explodedRouter[0])
            );

            if (class_exists($controllerNamespace)) {
                /**
                 * @var $controllerClass BaseController
                 */
                $controllerClass = new $controllerNamespace($explodedRouter[1]);
                $controllerClass->run();
            } else {
                AnyCommentCore::logger()->warning(sprintf(
                    "Failed to find class %s in %s, skipping to resolve it.",
                    $controllerNamespace,
                    __METHOD__
                ));
            }
        }
    }
}
