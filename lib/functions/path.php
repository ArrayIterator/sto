<?php
/**
 * @return string
 */
function get_root_dir(): string
{
    static $rootDir;
    if (!$rootDir) {
        $rootDir = normalize_directory(realpath(ROOT_DIR) ?: ROOT_DIR);
    }
    return $rootDir;
}

/**
 * @return string
 */
function get_admin_path(): string
{
    static $adminPath = null;
    if ($adminPath) {
        $path = hook_apply('admin_path', $adminPath) ?: $adminPath;
        return '/' . trim($path, '/');
    }

    $adminPath = is_string(ADMIN_PATH)
        ? '/' . trim(preg_replace('~[\\\\/]+~', '/', ADMIN_PATH), '/')
        : null;

    if (strpos($adminPath, '?')) {
        $adminPath = preg_replace('~(\?.*)$~', '', $adminPath);
    }

    $adminPath = $adminPath ?: get_scanned_admin_path();
    $path = hook_apply('admin_path', $adminPath) ?: $adminPath;
    return '/' . trim($path, '/');
}

/**
 * @return string
 */
function get_admin_directory(): string
{
    if (defined('ADMIN_DIR')) {
        return ADMIN_DIR;
    }

    define('ADMIN_DIR', normalize_directory(ROOT_DIR . '/' . ADMIN_PATH));
    return ADMIN_DIR;
}

/**
 * @return string
 */
function get_admin_includes_directory(): string
{
    static $dir;
    if (!$dir) {
        $dir = normalize_directory(get_admin_directory() . '/includes');
    }
    return $dir;
}

/**
 * @return string
 */
function get_scanned_admin_path(): string
{
    static $admin;
    if (is_string($admin)) {
        return $admin;
    }

    $filesToCheck = [
        'admin.php',
        'index.php',
        'install.php',
        'login.php',
        'main.php',
        'modules.php',
        'profile.php',
        'quarantined.php',
        'settings.php',
        'students.php',
        'supervisors.php',
        'themes.php',
    ];

    $admin = 'admin';
    $found = false;
    if (is_dir(ROOT_DIR . '/' . $admin)) {
        $found = true;
        foreach ($filesToCheck as $file) {
            if (!file_exists(ROOT_DIR . '/' . $admin . '/' . $file)) {
                $found = false;
                break;
            }
        }
    }

    if (!$found) {
        foreach (glob(ROOT_DIR . '/*', GLOB_ONLYDIR) as $dir) {
            $admin = basename($dir);
            $found = true;
            foreach ($filesToCheck as $file) {
                if (!file_exists(ROOT_DIR . '/' . $admin . '/' . $file)) {
                    $found = false;
                    break;
                }
            }
            if (!$found) {
                continue;
            }
            break;
        }
    }

    $admin = $found ? $admin : 'admin';
    $admin = '/' . trim($admin, '/');
    return $admin;
}

/**
 * @return string
 */
function get_login_path(): string
{
    static $loginPath = null;
    if ($loginPath) {
        $path = hook_apply('login_path', $loginPath) ?: $loginPath;
        return '/' . trim($path, '/');
    }

    $loginPath = is_string(LOGIN_PATH)
        ? '/' . trim(preg_replace('~[\\\\/]+~', '/', LOGIN_PATH), '/')
        : null;

    if (strpos($loginPath, '?')) {
        $loginPath = preg_replace('~(\?.*)$~', '', $loginPath);
    }

    $loginPath = $loginPath ?: DEFAULT_LOGIN_PATH;
    $path = hook_apply('login_path', $loginPath) ?: $loginPath;
    return '/' . trim($path, '/');
}

/**
 * @return string
 */
function get_themes_path(): string
{
    static $loginPath = null;
    if ($loginPath) {
        $path = hook_apply('themes_path', $loginPath);
        if (!$path) {
            $path = $loginPath;
        }
        return '/' . trim($path, '/');
    }

    $loginPath = is_string(THEMES_PATH)
        ? '/' . trim(preg_replace('~[\\\\/]+~', '/', THEMES_PATH), '/')
        : null;

    if (strpos($loginPath, '?')) {
        $loginPath = preg_replace('~(\?.*)$~', '', $loginPath);
    }

    $loginPath = $loginPath ?: DEFAULT_THEMES_PATH;
    $path = hook_apply('themes_path', $loginPath);
    if (!$path) {
        $path = $loginPath;
    }

    return '/' . trim($path, '/');
}

/**
 * @return string
 */
function get_themes_dir(): string
{
    static $path;
    if (!$path) {
        $path = get_themes_path();
        $path = normalize_directory(ROOT_DIR . $path);
    }

    return $path;
}

/**
 * @return string
 */
function get_modules_path(): string
{
    static $modulePath = null;
    // no hooks on module
    if ($modulePath) {
        return $modulePath;
    }

    $modulePath = is_string(MODULES_PATH)
        ? '/' . trim(preg_replace('~[\\\\/]+~', '/', MODULES_PATH), '/')
        : null;

    if (strpos($modulePath, '?')) {
        $modulePath = preg_replace('~(\?.*)$~', '', $modulePath);
    }

    $modulePath = $modulePath ?: DEFAULT_MODULES_PATH;
    return $modulePath = '/' . trim($modulePath, '/');
}


/**
 * @return string
 */
function get_modules_dir(): string
{
    static $path;
    if (!$path) {
        $path = get_modules_path();
        $path = normalize_directory(ROOT_DIR . $path);
    }

    return $path;
}

/**
 * @return string
 */
function get_uploads_path(): string
{
    static $uploadsPath = null;
    // no hooks on module
    if ($uploadsPath) {
        return $uploadsPath;
    }

    $uploadsPath = is_string(UPLOADS_PATH)
        ? '/' . trim(preg_replace('~[\\\\/]+~', '/', UPLOADS_PATH), '/')
        : null;

    if (strpos($uploadsPath, '?')) {
        $uploadsPath = preg_replace('~(\?.*)$~', '', $uploadsPath);
    }

    $uploadsPath = $uploadsPath ?: DEFAULT_UPLOADS_PATH;
    return $uploadsPath = '/' . trim($uploadsPath, '/');
}

/**
 * @return string
 */
function get_temp_directory(): string
{
    static $temp = '';
    if (defined('TEMP_DIR') &&
        is_dir(TEMP_DIR)
        && realpath(TEMP_DIR)
    ) {
        return slash_it(TEMP_DIR);
    }

    if ($temp) {
        return slash_it($temp);
    }

    if (function_exists('sys_get_temp_dir')) {
        $temp = sys_get_temp_dir();
        if (@is_dir($temp) && is_writable($temp)) {
            return slash_it($temp);
        }
    }

    $temp = ini_get('upload_tmp_dir');
    if (@is_dir($temp) && is_writable($temp)) {
        return slash_it($temp);
    }

    $temp = UPLOADS_DIR . '/';
    if (is_dir($temp) && is_writable($temp)) {
        return $temp;
    }

    return '/tmp/';
}

/**
 * @return string
 */
function get_uploads_dir(): string
{
    static $path;
    if (!$path) {
        $path = get_uploads_path();
        $path = normalize_directory(ROOT_DIR . $path);
    }

    return $path;
}

/**
 * @return string
 */
function get_language_dir(): string
{
    static $path;
    if (!$path) {
        $path = normalize_directory(LANGUAGE_DIR);
        $path = un_slash_it($path);
    }

    return $path;
}

/**
 * @return string
 */
function get_post_uploads_dir(): string
{
    return get_uploads_dir() . DIRECTORY_SEPARATOR . 'posts';
}

/**
 * @return string
 */
function get_question_uploads_dir(): string
{
    return get_uploads_dir() . DIRECTORY_SEPARATOR . 'questions';
}

function get_question_uploads_path(): string
{
    return slash_it(un_slash_it(get_uploads_path()) . '/questions/');
}

/**
 * @return string
 */
function get_avatar_uploads_dir(): string
{
    return get_uploads_dir() . DIRECTORY_SEPARATOR . 'avatars';
}

/**
 * @return string
 */
function get_avatar_uploads_path(): string
{
    return slash_it(un_slash_it(get_uploads_path()) . '/avatars/');
}

/**
 * @return string
 */
function get_logo_uploads_dir(): string
{
    return get_uploads_dir() . DIRECTORY_SEPARATOR . 'logos';
}
/**
 * @return string
 */
function get_logo_uploads_path(): string
{
    return slash_it(un_slash_it(get_uploads_path()) . '/logos/');
}

/**
 * @param string $name
 * @return string
 */
function get_avatar_url(string $name = ''): string
{
    $path = get_avatar_uploads_path();
    $name = ltrim($name, '/');
    $path = sprintf('%s%s', $path, $name);
    return get_site_url($path);
}

/**
 * @param string $name
 * @return string
 */
function get_logo_url(string $name = ''): string
{
    $path = get_logo_uploads_path();
    $name = ltrim($name, '/');
    $path = sprintf('%s%s', $path, $name);
    return get_site_url($path);
}

/**
 * @param string $name
 * @return string
 */
function get_question_upload_url(string $name = ''): string
{
    $path = get_question_uploads_path();
    $name = ltrim($name, '/');
    $path = sprintf('%s%s', $path, $name);
    return get_site_url($path);
}

/**
 * @return string
 */
function get_cache_dir(): string
{
    $cacheDir = CACHE_DIR;
    return hook_apply('cache_dir', normalize_directory($cacheDir));
}

/**
 * @return string
 */
function get_route_api_path(): string
{
    $route = (string)hook_apply('route_api_path', DEFAULT_API_PATH);
    if (strpos($route, '?')) {
        $route = preg_replace('~(\?.*)$~', '', $route);
    }

    $route = preg_replace('~[\\\\/]+~', '/', $route);
    // only valid path
    return '/' . trim($route, '/');
}
