<?php
namespace ArrayIterator\Controller;

use ArrayIterator\RouteStorage;

/**
 * Class BaseController
 * @package ArrayIterator\Controller
 */
abstract class BaseController
{
    private $hasRegisterController = false;

    final public function __construct()
    {
        // final
    }

    protected function registerController(RouteStorage $route)
    {
        // pass
    }

    final public function doRegisterController(RouteStorage $routeApi)
    {
        if ($this->hasRegisterController) {
            return;
        }

        $this->registerController($routeApi);
    }

    public function __invoke(RouteStorage $collector)
    {
        $this->doRegisterController($collector);
    }
}