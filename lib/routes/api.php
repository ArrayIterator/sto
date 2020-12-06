<?php
/**
 * Routes API Configurations
 */
route_any('/ping[/]', function () {
    require __DIR__ . '/callback/api/ping.php';
});
