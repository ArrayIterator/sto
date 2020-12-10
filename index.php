<?php
use ArrayIterator\Exception\NotFoundException;

define('PUBLIC_FILE', __FILE__);
require_once __DIR__ .'/lib/load.php';
require_once __DIR__ .'/lib/routes.php';

try {
    $routeInfo = route_dispatch(http_method(), REQUEST_URI);
} catch (NotFoundException $exception) {
    $handler = route()->getNotFoundHandler();
    $handler = !is_callable($handler)
        ? 'route_not_found_handler'
        : $handler;
    $handler(route());
}
