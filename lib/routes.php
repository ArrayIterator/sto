<?php
/**
 * Route Files
 */
// DENY DIRECT ACCESS
if (($_SERVER['SCRIPT_FILENAME'] ?? null) === __FILE__) {
    !headers_sent() && header('Location: ../', true, 302);
    exit(0);
}

set_not_allowed_handler(
    is_route_api() ? 'route_json_not_allowed_handler' : 'route_not_allowed_handler'
);
set_not_found_handler(
    is_route_api() ? 'route_json_not_found_handler' : 'route_not_found_handler'
);

// check if user logged
if (is_login_page() && is_student()) {
    redirect(get_site_url());
    do_exit(0);
}

// add api route
// $routes = require __DIR__ . '/routes/api.php';
//$routes = require __DIR__ . '/routes/common.php';
//unset($routes);
array_map('register_route_api_controller', (array) require __DIR__ . '/routes/api.php');
array_map('register_route_public_controller', (array) require __DIR__ . '/routes/common.php');
