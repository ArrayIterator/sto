<?php

use ArrayIterator\Route;

/**
 * Shutdown Handler
 */
function shutdown_handler()
{
    $error = error_get_last();
    if (!$error || $error['type'] !== E_ERROR) {
        return;
    }

    if (defined('ROUTE_API') && ROUTE_API) {
        if (DEBUG) {
            json(500, $error['message'], $error);
        }
        json(500, 'Internal Server Error');
        return;
    }

    if (!defined('CLEAN_BUFFER_ERROR') || CLEAN_BUFFER_ERROR !== false) {
        clean_buffer();
    }
    if (!headers_sent()) {
        set_header('Content-Type', 'text/html; charset=utf-8', 500);
    }

    include ROOT_TEMPLATES_PATH .'/error/error.php';
}

function route_not_found_handler(Route $route)
{
    set_header('Content-Type', 'text/html; charset=utf-8', 404);
    include ROOT_TEMPLATES_PATH .'/error/not-found.php';
}

function route_not_allowed_handler(Route $route, $allowedMethods = [])
{
    set_header('Content-Type', 'text/html; charset=utf-8', 404);
    include ROOT_TEMPLATES_PATH .'/error/not-allowed-method.php';
}

function route_json_not_found_handler()
{
    json(404, hook_apply('route_json_not_found_message', 'Route not found'));
}

function route_json_not_allowed_handler()
{
    json(405, hook_apply('route_json_not_allowed', 'Method not allowed'));
}

/**
 * @param int $code
 */
function do_exit(int $code = 0)
{
    exit($code);
}

/**
 * @param string $data
 */
function render(string $data)
{
    hook_run('before_render');
    echo $data;
    hook_run('after_render');
}
