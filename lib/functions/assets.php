<?php

use ArrayIterator\Dependency\Scripts;
use ArrayIterator\Dependency\Styles;

/* -------------------------------------------------
 *                     PUBLIC
 * ------------------------------------------------*/

/**
 * @param false $handles
 * @return array
 */
function assets_print_scripts($handles = false): array
{
    hook_run(__FUNCTION__);
    // For 'wp_head'.
    if ('' === $handles) {
        $handles = false;
    }
    if (!$handles) {
        return []; // No need to instantiate if nothing is there.
    }

    return assets_scripts()->doItems($handles);
}

/**
 * @param string|string[] $handles
 * @return array
 */
function assets_print_styles($handles): array
{
    if ('' === $handles) { // For 'wp_head'.
        $handles = false;
    }

    if (!$handles) {
        hook_run(__FUNCTION__);
    }

    if (!$handles) {
        // No need to instantiate if nothing is there.
        return [];
    }

    return assets_styles()->doItems($handles);
}

function assets_enqueue_scripts()
{
    hook_run(__FUNCTION__);
}

/**
 * @return string[]
 */
function assets_print_head_scripts(): array
{
    if (!hook_has_run('assets_print_scripts')) {
        hook_run('assets_print_scripts');
    }

    return do_assets_print_head_scripts();
}

function assets_print_footer_scripts()
{
    hook_run(__FUNCTION__);
}

function assets_admin_print_footer_scripts()
{
    hook_run(__FUNCTION__);
}

function do_assets_footer_scripts()
{
    do_assets_print_late_styles();
    do_assets_print_footer_scripts();
}

/**
 * @return string[]
 * @see assets_print_head_scripts
 */
function do_assets_print_head_scripts(): array
{
    if (!hook_has_run('assets_print_scripts')) {
        hook_run('assets_print_scripts');
    }

    $wp_scripts = assets_scripts();
    $wp_scripts->doHeadItems();
    return $wp_scripts->done;
}

/**
 * @return array
 */
function do_assets_print_footer_scripts(): array
{
    $wp_scripts = assets_scripts();
    $wp_scripts->doFooterItems();
    return $wp_scripts->done;
}

/**
 * @return array
 */
function do_assets_print_late_styles(): array
{
    $styles = assets_styles();
    $styles->doFooterItems();
    return $styles->done;
}


/**
 * @param string $handle
 * @param string|null $src
 * @param array $deps
 * @param string|null $ver
 * @param null $args
 * @return bool
 */
function assets_script_add(
    string $handle,
    string $src = null,
    array $deps = [],
    string $ver = null,
    $args = null
): bool {
    return assets_scripts()->add(
        $handle,
        $src,
        $deps,
        $ver,
        $args
    );
}

/**
 * @param string $handle
 * @param string|null $src
 * @param array $deps
 * @param string|null $ver
 * @param null $args
 * @return bool
 */
function assets_style_add(
    string $handle,
    string $src = null,
    array $deps = [],
    string $ver = null,
    $args = null
): bool {
    return assets_styles()->add(
        $handle,
        $src,
        $deps,
        $ver,
        $args
    );
}

/**
 * @param string $handle
 * @param string $key
 * @param $value
 * @return bool
 */
function assets_script_add_data(string $handle, string $key, $value): bool
{
    return assets_scripts()->addData($handle, $key, $value);
}

/**
 * @param string $handle
 * @param string $key
 * @param $value
 * @return bool
 */
function assets_style_add_data(string $handle, string $key, $value): bool
{
    return assets_styles()->addData($handle, $key, $value);
}

/**
 * @param string $handle
 * @param string $src
 * @param array $deps
 * @param string|null $ver
 * @param bool $in_footer
 */
function assets_script_enqueue(
    string $handle,
    string $src = '',
    $deps = [],
    string $ver = null,
    bool $in_footer = false
) {
    $scripts = assets_scripts();
    if ($src || $in_footer) {
        $_handle = explode('?', $handle);
        if ($src) {
            $scripts->add($_handle[0], $src, $deps, $ver);
        }

        if ($in_footer) {
            $scripts->addData($_handle[0], 'group', 1);
        }
    }
    $scripts->queue($handle);
}

function assets_style_enqueue(
    string $handle,
    string $src = '',
    $deps = [],
    string $ver = null,
    string $media = 'all'
) {
    $scripts = assets_styles();
    if ($src) {
        $_handle = explode('?', $handle);
        $scripts->add($_handle[0], $src, $deps, $ver, $media);

    }
    $scripts->queue($handle);
}

/**
 * @param string $handle
 */
function assets_script_dequeue(string $handle)
{
    assets_scripts()->dequeue($handle);
}

/**
 * @param string $handle
 */
function assets_style_dequeue(string $handle)
{
    assets_styles()->dequeue($handle);
}

/**
 * @param string $handle
 * @param string $list
 * @return bool
 */
function assets_script_is(string $handle, string $list = 'enqueued'): bool
{
    return (bool)assets_scripts()->query($handle, $list);
}

/**
 * @param string $handle
 * @param string $list
 * @return bool
 */
function assets_style_is(string $handle, string $list = 'enqueued'): bool
{
    return (bool)assets_styles()->query($handle, $list);
}

/**
 * @param string $handle
 */
function assets_script_deregister(string $handle)
{
    assets_scripts()->remove($handle);
}

/**
 * @param string $handle
 */
function assets_style_deregister(string $handle)
{
    assets_styles()->remove($handle);
}

/* -------------------------------------------------
 *                     ADMIN
 * ------------------------------------------------*/

function assets_admin_enqueue_scripts()
{
    if (!is_admin_page()) {
        return;
    }

    hook_run(
        __FUNCTION__,
        basename(get_server_environment('SCRIPT_FILENAME'))
    );
}

function assets_admin_print_styles()
{
    if (!is_admin_page()) {
        return;
    }

    $file_suffix = basename(get_server_environment('SCRIPT_FILENAME'));
    hook_run("assets_admin_print_styles-{$file_suffix}");
    hook_run(__FUNCTION__);
}

/**
 * @return array
 */
function do_assets_admin_print_styles(): array
{
    if (!is_admin_page()) {
        return [];
    }

    $styles = assets_styles();
    $styles->doItems(false);
    return $styles->done;
}

/**
 * @return array
 */
function do_assets_print_styles(): array
{
    if (is_admin_page()) {
        return [];
    }

    $styles = assets_styles();
    $styles->doItems(false);
    return $styles->done;
}

/* -------------------------------------------------
 *                     JS
 * ------------------------------------------------*/

/**
 * @param string $name
 * @return false|string
 */
function get_js_core_by_name(string $name)
{
    $js_dir = INCLUDES_DIR . '/';
    if (substr($name, -4) !== '.php') {
        $name .= '.php';
    }
    if (file_exists($js_dir . $name)) {
        /** @noinspection PhpIncludeInspection */
        return require $js_dir . $name;
    }

    return false;
}

/**
 * @return string
 */
function get_js_ping() : string
{
    return get_js_core_by_name('ping')?:'';
}


/*
 * MAIN FOCUSES
 */
/**
 * @param Scripts $scripts
 */
function assets_default_scripts(Scripts $scripts)
{
    $scripts->assets_url = get_assets_url('/');
    $scripts->default_version = VERSION;
    $scripts->default_dirs = [ASSETS_JS_PATH, ASSETS_VENDOR_PATH];
    $dependencies = require INCLUDES_DIR .'/definitions/js.php';
    foreach ($dependencies as $key => $item) {
        $scripts->add($key, ...$item);
    }
}

/**
 * @param Styles $styles
 */
function assets_default_styles(Styles $styles)
{
    $styles->default_version = VERSION;
    $styles->default_dirs = [ASSETS_CSS_PATH, ASSETS_VENDOR_PATH];
    $dependencies = require INCLUDES_DIR .'/definitions/css.php';
    foreach ($dependencies as $key => $item) {
        $styles->add($key, ...$item);
    }
}

/* -------------------------------------------------
 *                     ASSETS URL
 * ------------------------------------------------*/


/**
 * @param string $uri
 * @return string
 */
function get_assets_url(string $uri = ''): string
{
    $assets = '/assets/';
    if ($uri && $uri[0] == '/') {
        $uri = substr($uri, 1);
    }

    return get_site_url($assets . $uri);
}

/**
 * @param string $uri
 * @return string
 */
function get_asset_js_url(string $uri = ''): string
{
    $assets = '/assets/js/';
    if ($uri && $uri[0] == '/') {
        $uri = substr($uri, 1);
    }

    return get_site_url($assets . $uri);
}

/**
 * @param string $uri
 * @return string
 */
function get_asset_css_url(string $uri = ''): string
{
    $assets = '/assets/css/';
    if ($uri && $uri[0] == '/') {
        $uri = substr($uri, 1);
    }

    return get_site_url($assets . $uri);
}

/**
 * @param string $uri
 * @param string|null $vendorName
 * @return string
 */
function get_assets_vendor_url(string $uri = '', string $vendorName = null): string
{
    $assets = '/assets/vendor/';
    if ($uri && $uri[0] == '/') {
        $uri = substr($uri, 1);
    }
    if ($vendorName) {
        $uri = "{$vendorName}/{$uri}";
    }

    return get_site_url($assets . $uri);
}

/**
 * @param string $name
 * @return string
 */
function js_a(string $name) : string
{
    if (substr($name, -3) !== '.js') {
        $name .= ".js";
    }
    return get_asset_js_url($name);
}

/**
 * @param string $name
 * @return string
 */
function css_a(string $name) : string
{
    if (substr($name, -4) !== '.css') {
        $name .= ".css";
    }
    return get_asset_css_url($name);
}
