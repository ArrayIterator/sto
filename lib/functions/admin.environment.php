<?php

/**
 * @return bool
 */
function is_admin_page(): bool
{
    $siteUri = preg_replace('#(https?://)[^/]+/?#', '/', get_current_url());
    $adminUri = preg_replace('#(https?://)[^/]+/?#', '/', rtrim(get_admin_url(), '/'));
    $adminPath = preg_quote($adminUri, '~');
    return defined('ADMIN_AREA')
        && ADMIN_AREA
        && preg_match("~^{$adminPath}(/$|/.*)?(?:\?.*)?$~", $siteUri);
}

/**
 * Check whether is installation page
 *
 * @return bool
 */
function is_install_page(): bool
{
    return defined('INSTALLATION_FILE')
        && INSTALLATION_FILE
        && basename(server_environment()['SCRIPT_FILENAME']) === 'install.php'
        && is_admin_page();
}

/**
 * Check whether is admin login page
 *
 * @return bool
 */
function is_admin_login_page(): bool
{
    return defined('ADMIN_LOGIN_PAGE')
        && ADMIN_LOGIN_PAGE
        && basename(server_environment()['SCRIPT_FILENAME']) === 'login.php'
        && is_admin_page();
}

/**
 * @return false|string
 */
function get_admin_role()
{
    if (!is_supervisor() || !($spv = get_current_supervisor())) {
        return false;
    }
    $role = $spv['role'] ?? false;
    if (!is_string($role)) {
        return false;
    }
    return strtolower($role);
}

/**
 * @return bool
 */
function is_admin_login(): bool
{
    return is_admin_page() && is_supervisor();
}

/**
 * @return bool
 */
function is_super_admin(): bool
{
    return get_admin_role() === 'superadmin';
}

/**
 * @return bool
 */
function is_admin(): bool
{
    return is_super_admin() || get_admin_role() === 'admin';
}
