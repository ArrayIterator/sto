<?php

use ArrayIterator\Info\Module;
use ArrayIterator\Info\Theme;
use ArrayIterator\Route;

/**
 * Shutdown Handler
 */
function shutdown_handler()
{
    $error = error_get_last();

    if (!$error || !in_array($error['type'], [E_ERROR, E_PARSE])) {
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
    set_title('404 Not Found');
    set_content_type('text/html; charset=utf-8', 404);
    if (!load_template('404')) {
        include ROOT_TEMPLATES_DIR . '/404.php';
    }
    do_exit();
}

/**
 * @param Route $route
 * @param array $allowedMethods
 * @noinspection PhpUnusedParameterInspection
 */
function route_not_allowed_handler(Route $route, $allowedMethods = [])
{
    set_title('404 Method Not Allowed');
    set_content_type('text/html; charset=utf-8', 404);
    if (!load_template('405')) {
        include ROOT_TEMPLATES_DIR . '/405.php';
    }
    do_exit();
}

function route_json_not_found_handler()
{
    json(404, hook_apply('route_json_not_found_message', 'Route not found'));
}

function route_not_found()
{
    is_route_api() ? route_json_not_found_handler() : route_not_found_handler(route());
    do_exit();
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
    route()->setNotFoundHandler($callback);
}

/**
 * @param callable $callback
 */
function set_not_allowed_handler(callable $callback)
{
    route()->setNotAllowedHandler($callback);
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
        render($message);
    }

    hook_run_once('do_exit');
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

function return_zero(): int
{
    return 0;
}

/**
 * @return string
 */
function return_string(): string
{
    return '';
}

/**
 * @return array
 */
function return_array(): array
{
    return [];
}

/**
 * @param string $data
 * @param bool|null $exit
 */
function render(string $data = null, bool $exit = false)
{
    hook_run('before_render', $data, $exit);
    if ($data !== null) {
        echo $data;
    }
    hook_run('after_render', $data, $exit);
    unset($data);
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
 * @param string $name
 * @return Theme|false
 */
function theme_get(string $name)
{
    return themes()->getTheme($name);
}

/**
 * @param string $name
 * @return bool
 */
function theme_exist(string $name): bool
{
    return (bool)theme_get($name);
}

/**
 * @param string $name
 * @return Module|false only return valid modules
 */
function get_theme(string $name)
{
    $thm = theme_get($name);
    return $thm ?: false;
}

/**
 * @return Theme[]
 */
function get_all_themes(): array
{
    return themes()->getThemes();
}

/**
 * Add 404 Hook
 */
function set_404()
{
    hook_add('is_404', 'return_true');
}

/**
 * Override 404
 */
function set_200()
{
    hook_add('is_404', 'return_false');
    hook_add('is_405', 'return_false');
}

/**
 * @return bool
 */
function is_404(): bool
{
    return (bool)hook_apply('is_404', false);
}


/**
 * @return bool
 */
function is_405(): bool
{
    return (bool)hook_apply('is_405', false);
}
