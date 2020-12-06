<?php
/**
 * Route Files
 */

use FastRoute\RouteCollector;

set_not_allowed_handler(
    is_route_api() ? 'route_json_not_allowed_handler' : 'route_not_allowed_handler'
);
set_not_found_handler(
    is_route_api() ? 'route_json_not_found_handler' : 'route_not_found_handler'
);

// add api route
route_group(get_route_api_path(), function (RouteCollector $routeCollector) {
    is_route_api() && require __DIR__ . '/routes/api.php';
});

route_group('{path: (?!' . get_route_api_path() . '/)}', function (RouteCollector $routeCollector) {
    !is_route_api() && require __DIR__ . '/routes/common.php';
});
