<?php
/**
 * Routes API Configurations
 */
route_api_any('/ping[/]', function () {
    require __DIR__ . '/callback/api/ping.php';
});
