<?php
/**
 * @return string
 */
function get_admin_base_name_file() : string
{
    static $file;
    if ($file) {
        return $file;
    }
    $base     = normalize_path(get_server_environment('SCRIPT_FILENAME'));
    $adminDir = slash_it(normalize_path(ADMIN_DIR));
    $file = substr($base, strlen($adminDir));
    return $file;
}

/**
 * @return bool
 */
function is_admin_page(): bool
{
    static $is_admin;
    if (is_bool($is_admin)) {
        return $is_admin;
    }
    $siteUri = preg_replace('#(https?://)[^/]+/?#', '/', get_current_url());
    $adminUri = preg_replace('#(https?://)[^/]+/?#', '/', rtrim(get_admin_url(), '/'));
    $adminPath = preg_quote($adminUri, '~');
    $is_admin = defined('ADMIN_AREA')
        && ADMIN_AREA
        && preg_match("~^{$adminPath}(/$|/.*)?(?:\?.*)?$~", $siteUri);
    return $is_admin;
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
 * @return bool
 */
function is_admin_profile_page() : bool
{
    return is_admin_page() && basename(server_environment()['SCRIPT_FILENAME']) === 'profile.php';
}

/**
 * @return bool
 */
function is_admin_dashboard_page() : bool
{
    return is_admin_page() && basename(server_environment()['SCRIPT_FILENAME']) === 'index.php';
}

/**
 * Check whether is admin login page
 *
 * @return bool
 */
function is_admin_quarantine_page(): bool
{
    return defined('QUARANTINED_FILE')
        && QUARANTINED_FILE
        && basename(server_environment()['SCRIPT_FILENAME']) === 'quarantined.php'
        && is_admin_page();
}

/**
 * @return false|string
 */
function get_admin_role()
{
    if (!is_supervisor() || !($role = get_current_supervisor_role())) {
        return false;
    }
    if (!is_string($role)) {
        return false;
    }
    return trim(strtolower($role));
}

/**
 * @return false|string
 */
function get_admin_status()
{
    if (!is_supervisor() || !($status = get_current_supervisor_status())) {
        return false;
    }

    if (!is_string($status)) {
        return false;
    }
    return trim(strtolower($status));
}

/**
 * @return bool
 */
function is_admin_login(): bool
{
    return is_admin_page() && is_supervisor();
}

/**
 * @param string $role
 * @return bool
 */
function admin_role_is(string $role) : bool
{
    return get_admin_role() === strtolower(trim($role));
}

/**
 * @param string $status
 * @return bool
 */
function admin_status_is(string $status) : bool
{
    return get_admin_status() === strtolower(trim($status));
}

/**
 * @return bool
 */
function is_super_admin(): bool
{
    return admin_role_is('superadmin');
}

/**
 * @return bool
 */
function is_admin(): bool
{
    return is_super_admin() || admin_role_is('admin');
}

function is_teacher() : bool
{
    return admin_role_is('teacher');
}

function is_editor() : bool
{
    return admin_role_is('editor');
}

function is_invigilator() : bool
{
    return admin_role_is('invigilator');
}

function is_admin_active() : bool
{
    return admin_status_is('active');
}

function is_admin_banned() : bool
{
    return admin_status_is('active');
}

/**
 * @return bool
 */
function is_admin_deleted() : bool
{
    return admin_status_is('deleted') || admin_status_is('delete');
}

/**
 * @return bool
 */
function is_admin_pending() : bool
{
    return admin_status_is('pending');
}

/**
 * @return bool
 */
function teacher_can_see_supervisor() : bool
{
    $data = get_site_option('teacher_can_see_supervisors');
    $data = is_string($data) ? trim(strtolower($data)) : $data;
    return hook_apply(
            'teacher_can_see_supervisor',
            $data === 'true' || $data === 'yes' || $data === true
        ) === true;
}

/**
 * @return bool
 */
function invigilator_can_see_supervisor() : bool
{
    $data = get_site_option('invigilator_can_see_supervisor');
    $data = is_string($data) ? trim(strtolower($data)) : $data;
    return hook_apply(
            'invigilator_can_see_supervisor',
            $data === 'true' || $data === 'yes' || $data === true
        ) === true;
}
