<?php
/**
 * Common API Configurations
 */

route_public_any(get_login_path(), '\ArrayIterator\Controller\Auth::login');
route_public_any(get_reset_password_path(), '\ArrayIterator\Controller\Auth::reset');
// favicon
route_public_any('/favicon.ico', '\ArrayIterator\Controller\Common::favicon');
