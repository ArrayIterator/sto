<?php
namespace ArrayIterator;

use Exception;
use FastRoute\BadRouteException;
use FastRoute\RouteCollector;

/**
 * Class RouteApiCollection
 * @package ArrayIterator
 */
abstract class GroupingRoute
{
    /**
     * @var Route
     */
    protected $route;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $groupPrefix;

    /**
     * GroupingRoute constructor.
     * @param Route $route
     * @param string $prefix
     * @param bool $groupCaseSensitive
     */
    public function __construct(
        Route $route,
        string $prefix,
        bool $groupCaseSensitive = true
    ) {
        $this->route = $route;
        $this->prefix = '/'.trim($prefix, '/');
        $this->groupPrefix = strpos($prefix, '{') !== false
            ? $prefix
            : sprintf(
                '{group: (?:%s%s)}',
                !$groupCaseSensitive ? '(?i)' : '',
                preg_quote($this->prefix, '#')
            );
        $this->route->getRouteParser()->parse($this->groupPrefix);
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getGroupPrefix(): string
    {
        return $this->groupPrefix;
    }

    /**
     * @param string $path
     * @param callable $callback
     * @return bool|Exception|BadRouteException
     */
    public function group(string $path, callable $callback)
    {
        if ($this->route->isDispatched()) {
            return false;
        }
        $e = $this->isRoutePathAllowed($path);
        if ($e instanceof Exception) {
            return $e;
        }

        $this
            ->route
            ->group(
                $this->groupPrefix,
                function (RouteCollector $r) use ($path, $callback) {
                    $r->addGroup($path, $callback);
                });
        return true;
    }

    /**
     * @param string|array $methods
     * @param string $path
     * @param callable $callback
     * @return bool|BadRouteException|Exception
     */
    abstract public function add(
        $methods,
        string $path,
        callable $callback
    );

    /**
     * @param string $path
     * @return array|Exception|BadRouteException
     */
    public function isRoutePathAllowed(string $path)
    {
        try {
            return $this->route->getRouteParser()->parse($path);
        } catch (BadRouteException $e) {
            return $e;
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * @param string $path
     * @param callable $callable
     * @return bool|int
     */
    public function get(string $path, callable $callable)
    {
        return $this->add('GET', $path, $callable);
    }

    /**
     * @param string $path
     * @param callable $callable
     * @return bool|int
     */
    public function post(string $path, callable $callable)
    {
        return $this->add('POST', $path, $callable);
    }

    public function put(string $path, callable $callable)
    {
        return $this->add('PUT', $path, $callable);
    }
    public function patch(string $path, callable $callable)
    {
        return $this->add('PATCH', $path, $callable);
    }

    public function delete(string $path, callable $callable)
    {
        return $this->add('DELETE', $path, $callable);
    }

    public function any(string $path, callable $callable)
    {
        return $this->add(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], $path, $callable);
    }
}
