<?php

use ArrayIterator\Controller\BaseController;
use ArrayIterator\RouteStorage;
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
    $is_admin_page = is_admin_page();
    $uri = $uri ?? request_uri();
    $dispatched = route()->isDispatched();
    if ($is_admin_page && !$dispatched) {
        $dispatched = route()->isDispatchedOnly();
    }

    if (!$dispatched) {
        hook_run('before_dispatch');
    }

    // use dispatch only on admin page
    // but it will allowed run dispatch on another case
    $routeInfo = $is_admin_page
        ? route()->dispatchOnly($httpMethod, $uri)
        : route()->dispatch($httpMethod, $uri, hooks());

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

/**
 * @param $controller
 * @param RouteStorage $routeApi
 * @return bool
 */
function register_route_controller($controller, RouteStorage $routeApi) : bool
{
    if (is_string($controller)) {
        // preg_match('#^(?P<className>[^:]+)([:]{1,2}(?P<method>))$#', $controller, $match);
        if (!@class_exists($controller) || ! is_subclass_of($controller, BaseController::class)) {
            return false;
        }
        $controller = new $controller;
    }

    if (!is_object($controller) || !$controller instanceof BaseController) {
        return false;
    }

    $controller($routeApi);
    return true;
}

/**
 * @param string|BaseController $controller
 * @return bool
 */
function register_route_public_controller($controller) : bool
{
    return register_route_controller($controller, route_public());
}

/**
 * @param string|BaseController $controller
 * @return bool
 */
function register_route_api_controller($controller) : bool
{
    return register_route_controller($controller, route_api());
}

/**
 * @param string $str
 * @return string
 */
function route_slash_it(string $str) : string
{
    if (preg_match('/(?:\[(.*([^\]]))([\]]+))$/', $str, $match, PREG_OFFSET_CAPTURE)) {
        if ($match[2][0] !== '/') {
            $str = substr($str, 0, $match[0][1]);
            $str .= $match[2][0] === '[' ? "[{$match[1][0]}/]]": "[{$match[1][0]}[/]]";
        }
        return $str;
    } elseif (preg_match('#^(.*)?([^\]])([\]]+)?$#', $str, $match)) {
        $slash = $match[2]??'';
        $close = $match[3]??'';
        $slash = $slash !== '/' ? "{$slash}[/]" : "[/]";
        $str = "{$match[1]}{$slash}{$close}";
        if (preg_match('#[\[]{2,}[/]\]\]#', $str)) {
            $str = "{$match[1]}/{$close}";
        }
        return $str;
    }
    $str = preg_replace('#(\[[/]+\]|[/]+)$#', '', $str);
    $str = "{$str}[/]";
    return $str;
}
