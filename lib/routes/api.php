<?php
/**
 * Routes API Configurations
 */

use ArrayIterator\Controller\Api\Status;

route_api_any(Status::PING_PATH . '[/]', [Status::class, 'ping']);
