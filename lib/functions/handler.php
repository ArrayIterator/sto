<?php

use ArrayIterator\Module;
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
        set_content_type('text/html; charset=utf-8', 500);
    }

    include ROOT_TEMPLATES_DIR . '/500.php';
}

/**
 * @param Route $route
 * @noinspection PhpUnusedParameterInspection
 */
function route_not_found_handler(Route $route)
{
    set_content_type('text/html; charset=utf-8', 404);
    include ROOT_TEMPLATES_DIR . '/404.php';
}

/**
 * @param Route $route
 * @param array $allowedMethods
 * @noinspection PhpUnusedParameterInspection
 */
function route_not_allowed_handler(Route $route, $allowedMethods = [])
{
    set_content_type('text/html; charset=utf-8', 404);
    include ROOT_TEMPLATES_DIR . '/405.php';
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
 * @param callable $callback
 */
function set_not_found_handler(callable $callback)
{
    \route()->setNotFoundHandler($callback);
}

/**
 * @param callable $callback
 */
function set_not_allowed_handler(callable $callback)
{
    \route()->setNotAllowedHandler($callback);
}

/**
 * @param int|string ...$args
 */
function do_exit(...$args)
{
    $code = null;
    $message = null;
    $count = count($args);
    if ($count === 0) {
        exit(0);
    }

    if (is_string($args[0])) {
        $message = $args[0];
    }

    if (is_object($args[0]) && method_exists($args[0], '__tostring')) {
        $message = (string)$args[0];
    }

    if (is_int($args[0])) {
        $code = $args[0];
    }

    if ($count > 1) {
        if ($message === null
            && (is_string($args[1])
                || is_object($args[0])
                && method_exists($args[0], '__tostring')
            )
        ) {
            $message = $args[1];
        }

        if ($code === null && is_int($args[1])) {
            $code = $args[1];
        }
    }

    $code = $code ?? 0;
    if ($message) {
        echo $message;
    }

    exit($code);
}

/**
 * @return true
 */
function return_true(): bool
{
    return true;
}

/**
 * @return false
 */
function return_false(): bool
{
    return false;
}

/**
 * @return null
 */
function return_null()
{
    return null;
}

/**
 * @param string $data
 * @param bool|null $exit
 */
function render(string $data, bool $exit = false)
{
    hook_run('before_render');
    echo $data;
    hook_run('after_render');
    if ($exit) {
        do_exit();
    }
}

/**
 * @param string $name
 * @return Module|false
 */
function module_get(string $name)
{
    return modules()->getModule($name);
}

/**
 * @param string $name
 * @return bool
 */
function module_exist(string $name): bool
{
    return (bool)module_get($name);
}

/**
 * @param string $name
 * @return Module|false only return valid modules
 */
function get_module(string $name)
{
    $mod = module_get($name);
    return $mod && $mod->isValid() ? $mod : false;
}

/**
 * Add 404 Hook
 */
function set_404()
{
    hook_add('is_404', 'return_true');
}

/**
 * @return bool
 */
function is_404() : bool
{
    return (bool) hook_apply('is_404', false);
}


/**
 * @return bool
 */
function is_not_allowed_method() : bool
{
    return (bool) hook_apply('is_not_allowed_method', false);
}
