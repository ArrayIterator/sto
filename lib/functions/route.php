<?php

use FastRoute\RouteCollector;

/**
 * @return RouteCollector
 */
function route_collector() : RouteCollector
{
    return application()->getRoute()->getRouteCollector();
}

/**
 * @param string $prefix
 * @param callable $callback
 */
function route_group(string $prefix, callable $callback)
{
    route()->group($prefix, $callback);
}

/**
 * @param string $pattern
 * @param callable $callable
 */
function route_get(string $pattern, callable $callable)
{
    route()->get($pattern, $callable);
}

/**
 * @param string $pattern
 * @param callable $callable
 */
function route_post(string $pattern, callable $callable)
{
    route()->post($pattern, $callable);
}

/**
 * @param string $pattern
 * @param callable $callable
 */
function route_delete(string $pattern, callable $callable)
{
    route()->delete($pattern, $callable);
}

/**
 * @param string $pattern
 * @param callable $callable
 */
function route_patch(string $pattern, callable $callable)
{
    route()->patch($pattern, $callable);
}

/**
 * @param string $pattern
 * @param callable $callable
 */
function route_put(string $pattern, callable $callable)
{
    route()->put($pattern, $callable);
}

/**
 * @param string $pattern
 * @param callable $callable
 */
function route_head(string $pattern, callable $callable)
{
    route()->head($pattern, $callable);
}

/**
 * @param string $pattern
 * @param callable $callable
 */
function route_any(string $pattern, callable $callable)
{
    route()->any($pattern, $callable);
}

/**
 * @param string $httpMethod
 * @param string|null $uri
 * @return array
 */
function route_dispatch(string $httpMethod, string $uri = null)
{
    $uri = $uri??request_uri();
    $dispatched = route()->isDispatched();
    if (!$dispatched) {
        hook_run('before_dispatch');
    }
    $routeInfo = route()->dispatch($httpMethod, $uri);
    if (!$dispatched) {
        hook_run('after_dispatch');
    }
    return $routeInfo;
}
