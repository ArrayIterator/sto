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
require __DIR__ . '/routes/api.php';
require __DIR__ . '/routes/common.php';
