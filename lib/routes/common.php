<?php
/**
 * Common API Configurations
 */

use ArrayIterator\Controller\Common\Auth;
use ArrayIterator\Controller\Common\Common;

route_public_any(get_login_path(), [Auth::class, 'login']);
route_public_any(get_reset_password_path(), [Auth::class, 'reset']);
route_public_any('/favicon.ico', [Common::class, 'favicon']); // favicon
