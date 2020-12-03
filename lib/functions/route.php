<?php
function route_collector()
{
    return application()->getRoute()->getRouteCollector();
}

function route_group(string $prefix, callable $callback)
{
    route()->group($prefix, $callback);
}

function route_get(string $pattern, callable $callable)
{
    route()->get($pattern, $callable);
}

function route_post(string $pattern, callable $callable)
{
    route()->post($pattern, $callable);
}

function route_delete(string $pattern, callable $callable)
{
    route()->delete($pattern, $callable);
}
function route_patch(string $pattern, callable $callable)
{
    route()->patch($pattern, $callable);
}
function route_put(string $pattern, callable $callable)
{
    route()->put($pattern, $callable);
}
function route_head(string $pattern, callable $callable)
{
    route()->head($pattern, $callable);
}
function route_any(string $pattern, callable $callable)
{
    route()->any($pattern, $callable);
}

/**
 * @param string $httpMethod
 * @param $uri
 * @return array
 */
function route_dispatch(string $httpMethod, $uri)
{
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
