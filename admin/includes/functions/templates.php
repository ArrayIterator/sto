<?php
/**
 * @param string $file
 * @return bool
 */
function load_admin_template(string $file) : bool
{
    if (substr($file, -4) !== '.php') {
        $file .= '.php';
    }
    if (!file_exists($file)) {
        $file = dirname(__DIR__) . '/templates/'.$file;
        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            require $file;
            return true;
        }
    }

    return false;
}

/**
 * @param bool $reLoad
 */
function get_admin_header_template(bool $reLoad = false)
{
    static $loaded;
    if (!$reLoad && $loaded) {
        return;
    }

    $loaded = true;
    load_admin_template('header');
}

/**
 * @param bool $reLoad
 */
function get_admin_footer_template(bool $reLoad = false)
{
    static $loaded;
    if (!$reLoad && $loaded) {
        return;
    }
    $loaded = true;
    load_admin_template('footer');
}

/**
 * @return string
 */
function get_admin_title() : string
{
    $title = is_admin_login() ? 'Dashboard' : get_admin_login_title();
    $title = (string) hook_apply(
        'admin_title',
        $title
    );
    return htmlentities(trans($title));
}

/**
 * @return string
 */
function get_admin_login_title() : string
{
    return (string) hook_apply('admin_login_title', 'Login To Admin Area');
}

