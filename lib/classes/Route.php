<?php

namespace ArrayIterator;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher\GroupCountBased as GroupDispatcher;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

/**
 * Class Route
 * @package ArrayIterator
 */
class Route
{
    protected $dispatcher;
    protected $routeCollector;
    protected $routeInfo;
    protected $notFoundHandler;
    protected $notAllowedHandler;
    protected $dispatched = false;
    protected $routeParser;
    protected $generator;

    /**
     * Route constructor.
     */
    public function __construct()
    {
        $this->routeParser = new Std();
        $this->generator = new GroupCountBased();
        $this->routeCollector = new RouteCollector($this->routeParser, $this->generator);
    }

    /**
     * @return Std
     */
    public function getRouteParser(): Std
    {
        return $this->routeParser;
    }

    /**
     * @return GroupCountBased
     */
    public function getGenerator(): GroupCountBased
    {
        return $this->generator;
    }

    /**
     * @return bool
     */
    public function isDispatched(): bool
    {
        return $this->dispatched;
    }

    public function getRouteInfo()
    {
        return $this->routeInfo;
    }

    /**
     * @return callable|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getNotFoundHandler()
    {
        return $this->notFoundHandler;
    }

    /**
     * @param callable $notFoundHandler
     */
    public function setNotFoundHandler(callable $notFoundHandler)
    {
        $this->notFoundHandler = $notFoundHandler;
    }

    /**
     * @return callable|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getNotAllowedHandler()
    {
        return $this->notAllowedHandler;
    }

    /**
     * @param callable $notAllowedHandler
     */
    public function setNotAllowedHandler(callable $notAllowedHandler)
    {
        $this->notAllowedHandler = $notAllowedHandler;
    }

    /**
     * @return RouteCollector
     */
    public function getRouteCollector(): RouteCollector
    {
        return $this->routeCollector;
    }

    /**
     * @return GroupDispatcher
     */
    public function getDispatcher(): GroupDispatcher
    {
        if (!$this->dispatcher) {
            $this->dispatcher = new GroupDispatcher(
                $this->routeCollector->getData()
            );
        }

        return $this->dispatcher;
    }

    /**
     * @param string[]|string $method
     * @param string $pattern
     * @param callable $handler
     */
    public function add($method, string $pattern, callable $handler)
    {
        $this->routeCollector->addRoute($method, $pattern, $handler);
    }

    public function get(string $pattern, callable $handler)
    {
        $this->add('GET', $pattern, $handler);
    }

    public function patch(string $pattern, callable $handler)
    {
        $this->add('PATCH', $pattern, $handler);
    }

    public function head(string $pattern, callable $handler)
    {
        $this->add('HEAD', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler)
    {
        $this->add('DELETE', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler)
    {
        $this->add('PUT', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler)
    {
        $this->add('POST', $pattern, $handler);
    }

    public function any($pattern, callable $handler)
    {
        $this->add(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], $pattern, $handler);
    }

    /**
     * @param string $prefix
     * @param callable $callback
     */
    public function group(string $prefix, callable $callback)
    {
        $this->routeCollector->addGroup($prefix, $callback);
    }

    /**
     * @param $httpMethod
     * @param string|null $uri
     * @param Hooks|null $hook
     * @return array|mixed[]
     */
    public function dispatch(
        $httpMethod,
        string $uri = null,
        Hooks $hook = null
    ): array {
        $this->dispatched = true;
        if (!$this->routeInfo) {
            $httpMethod = strtoupper($httpMethod);
            $uri = $uri ?? $_SERVER['REQUEST_URI'];
            // Strip query string (?foo=bar) and decode URI
            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }

            $uri = rawurldecode($uri);
            $this->routeInfo = $this->getDispatcher()->dispatch($httpMethod, $uri);
            $routeInfo = $this->routeInfo[0];
            $route = $hook ? $hook->apply('route_info', $this->routeInfo, $this) : $this->routeInfo;
            if (!is_array($route)
                || !isset($route[0])
                || !in_array($route[0], [
                    Dispatcher::FOUND,
                    Dispatcher::NOT_FOUND,
                    Dispatcher::METHOD_NOT_ALLOWED,
                ], true)
                || $route[0] === Dispatcher::FOUND
                && (!isset($route[1]) || !is_callable($route[1]))
            ) {
                $route = $routeInfo;
            }

            switch ($route[0]) {
                case Dispatcher::NOT_FOUND:
                    $hook && $hook->add('is_404', function () use ($route) {
                        return true;
                    });
                    // ... 404 Not Found
                    if ($this->notFoundHandler) {
                        $handler = $this->notFoundHandler;
                        $handler($this);
                    }
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    $hook && $hook->add('is_405', function () use ($route) {
                        return true;
                    });
                    $allowedMethods = !isset($route[1]) || !is_array($route[1])
                        ? $this->routeInfo[1]
                        : $route[1];
                    if ($this->notAllowedHandler) {
                        $handler = $this->notAllowedHandler;
                        $handler($this, $allowedMethods);
                    }
                    break;
                case Dispatcher::FOUND:
                    $handler = !isset($route[1]) || !is_callable($route[1])
                        ? $this->routeInfo[1]
                        : $route[1];
                    $vars = !isset($routeInfo[2]) || !is_array($routeInfo)
                        ? $this->routeInfo[2]
                        : $routeInfo[2];
                    $handler($this, $vars);

                    // ... call $handler with $vars
                    break;
            }
        }

        return $this->routeInfo;
    }
}
