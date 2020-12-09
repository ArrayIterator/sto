<?php

use FastRoute\BadRouteException;

/* -------------------------------------------------
 *                   ROUTE
 * ------------------------------------------------*/

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
function route_dispatch(string $httpMethod, string $uri = null): array
{
    $uri = $uri ?? request_uri();
    $dispatched = route()->isDispatched();
    if (!$dispatched) {
        hook_run('before_dispatch');
    }
    $routeInfo = route()->dispatch($httpMethod, $uri, hooks());
    if (!$dispatched) {
        hook_run('after_dispatch');
    }

    return $routeInfo;
}

/* -------------------------------------------------
 *                  API ROUTE
 * ------------------------------------------------*/

/**
 * @param string|string[] $method
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_api_add($method, string $pattern, callable $callback)
{
    return route_api()->add($method, $pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_api_get(string $pattern, callable $callback)
{
    return route_api()->get($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_api_post(string $pattern, callable $callback)
{
    return route_api()->post($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_api_put(string $pattern, callable $callback)
{
    return route_api()->put($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_api_patch(string $pattern, callable $callback)
{
    return route_api()->patch($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_api_delete(string $pattern, callable $callback)
{
    return route_api()->delete($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_api_any(string $pattern, callable $callback)
{
    return route_api()->any($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_api_group(string $pattern, callable $callback)
{
    return route_api()->group($pattern, $callback);
}


/* -------------------------------------------------
 *                  PUBLIC ROUTE
 * ------------------------------------------------*/

/**
 * @param string|string[] $method
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_public_add($method, string $pattern, callable $callback)
{
    return route_public()->add($method, $pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_public_get(string $pattern, callable $callback)
{
    return route_public()->get($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_public_post(string $pattern, callable $callback)
{
    return route_public()->post($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_public_put(string $pattern, callable $callback)
{
    return route_public()->put($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_public_patch(string $pattern, callable $callback)
{
    return route_public()->patch($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_public_delete(string $pattern, callable $callback)
{
    return route_public()->delete($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_public_any(string $pattern, callable $callback)
{
    return route_public()->any($pattern, $callback);
}

/**
 * @param string $pattern
 * @param callable $callback
 * @return bool|Exception|BadRouteException
 */
function route_public_group(string $pattern, callable $callback)
{
    return route_public()->group($pattern, $callback);
}
