<?php

/**
 * @return bool
 */
function is_admin_page() : bool
{
    $adminPath = preg_quote(get_admin_path(), '~');
    return ADMIN_AREA && preg_match("~^{$adminPath}(/|$)~", request_uri());
}

/**
 * @return false|string
 */
function get_admin_role()
{
    if (!is_supervisor() || !($spv = get_current_supervisor())) {
        return false;
    }
    $role = $spv['role']??false;
    if (!is_string($role)) {
        return false;
    }
    return strtolower($role);
}

/**
 * @return bool
 */
function is_admin_login() : bool
{
    return is_admin_page() && is_supervisor();
}

function is_super_admin() : bool
{
    return get_admin_role() === 'superadmin';
}

function is_admin() : bool
{
    return is_super_admin() || get_admin_role() === 'admin';
}
