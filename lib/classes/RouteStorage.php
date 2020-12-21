<?php

namespace ArrayIterator;

use Exception;
use FastRoute\BadRouteException;
use FastRoute\RouteCollector;

/**
 * Class RouteStorage
 * @package ArrayIterator
 */
class RouteStorage extends GroupingRoute
{
    /**
     * RouteApi constructor.
     * @param Route $route
     * @param string $prefix
     * @param bool $groupCaseSensitive
     */
    public function __construct(
        Route $route,
        string $prefix,
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
        try {
            $containPrefix = false;
            $parser = $this->route->getRouteParser()->parse($path);
            foreach ($parser as $item) {
                foreach ($item as $i) {
                    if ($i[0] === '/' || preg_match(
                            '#^(
                            \(\[?/
                            |
                            (
                                \(\?
                                (
                                [iLmsux]+
                                | P(<[^>]+>|\'[^\']+\')
                                | (<x>|\'x\')[a-zA-Z0-9_]
                                )[)]
                                |
                            )?/
                            )
                        #x',
                            $i
                        )
                    ) {
                        $containPrefix = true;
                        break;
                    }
                }
                if ($containPrefix) {
                    break;
                }
            }
        } catch (BadRouteException $e) {
            return $e;
        }

        if (!$containPrefix) {
            $path = "/{$path}";
        }

        try {
            $this
                ->route
                ->group(
                    $this->groupPrefix,
                    function (RouteCollector $route) use ($methods, $path, $callback) {
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
