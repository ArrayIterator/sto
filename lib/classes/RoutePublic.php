<?php
namespace ArrayIterator;

use Exception;
use FastRoute\BadRouteException;

/**
 * Class RouteApi
 * @package ArrayIterator
 */
class RoutePublic extends GroupingRoute
{
    /**
     * RouteApi constructor.
     * @param Route $route
     * @param string $prefix
     * @param bool $groupCaseSensitive
     */
    public function __construct(
        Route $route,
        string $prefix = '{group: (?!/api/)}',
        bool $groupCaseSensitive = true
    ) {
        parent::__construct($route, $prefix, $groupCaseSensitive);
    }

    /**
     * @param array|string $methods
     * @param string $path
     * @param callable $callback
     * @return bool|Exception|BadRouteException
     */
    public function add($methods, string $path, callable $callback)
    {
        if ($this->route->isDispatched()) {
            return false;
        }
        $e = $this->isRoutePathAllowed($path);
        if ($e instanceof Exception) {
            return $e;
        }
        try {
            $this
                ->route
                ->group(
                    $this->groupPrefix,
                    function ($route) use ($methods, $path, $callback) {
                        $route->addRoute($methods, $path, $callback);
                    });
            return true;
        } catch (BadRouteException $e) {
            return $e;
        } catch (Exception $e) {
            return $e;
        }
    }
}
