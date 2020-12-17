<?php
// DENY DIRECT ACCESS
if (($_SERVER['SCRIPT_FILENAME'] ?? null) === __FILE__) {
    !headers_sent() && header('Location: ../', true, 302);
    exit(0);
}

// require autoload
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/constant.php';
require_once __DIR__ . '/functions/meta.php';
require_once __DIR__ . '/functions/environment.php';
require_once __DIR__ . '/functions/cache.php';
require_once __DIR__ . '/functions/path.php';
require_once __DIR__ . '/functions/url.php';
require_once __DIR__ . '/functions/software.php';
require_once __DIR__ . '/functions/filters.php';
require_once __DIR__ . '/functions/handler.php';
require_once __DIR__ . '/functions/headers.php';
require_once __DIR__ . '/functions/settings.php';
require_once __DIR__ . '/functions/attachments.php';
require_once __DIR__ . '/functions/api.php';
require_once __DIR__ . '/functions/database.php';
require_once __DIR__ . '/functions/options.php';
require_once __DIR__ . '/functions/user.php';
require_once __DIR__ . '/functions/auth.php';
require_once __DIR__ . '/functions/filters.php';
require_once __DIR__ . '/functions/translations.php';
require_once __DIR__ . '/functions/route.php';
require_once __DIR__ . '/functions/calendar.php';
require_once __DIR__ . '/functions/assets.php';
require_once __DIR__ . '/functions/templates.php';
require_once __DIR__ . '/functions/admin.environment.php';

if (ob_get_level() < 1) {
    ob_start();
}

register_shutdown_function('shutdown_handler');

$configBaseName = CONFIG_BASE_FILENAME;
$configBaseName = substr($configBaseName, -4) !== 'php'
    ? DEFAULT_CONFIG_BASE_FILENAME
    : $configBaseName;

if (file_exists(ROOT_DIR . DS . $configBaseName)) {
    /** @noinspection PhpIncludeInspection */
    require_once ROOT_DIR . DS . $configBaseName;
} elseif (file_exists(dirname(ROOT_DIR) . DS . $configBaseName)) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(ROOT_DIR) . DS . $configBaseName;
} else {
    if (!is_install_page()) {
        clean_buffer();

        defined('ADMIN_PATH') || define('ADMIN_PATH', get_scanned_admin_path());
        $adminPath = get_admin_path();
        if (!file_exists(ROOT_DIR . $adminPath . '/install.php')) {
            $adminPath = get_scanned_admin_path();
            if (!file_exists(ROOT_DIR . $adminPath . '/install.php')) {
                $adminPath = null;
            }
        }

        if (!headers_sent() && $adminPath) {
            redirect($adminPath . '/install.php');
            exit;
        }

        if (!headers_sent()) {
            set_content_type('text/html; charset=utf-8', 500);
        }

        include ROOT_TEMPLATES_DIR . '/install.php';
        return;
    }
}


// DEBUG
defined('DEBUG') || define('DEBUG', false);
!DEBUG ? error_reporting(0) : error_reporting(~0);

// TIMEZONE SET TO UTC
date_default_timezone_set('UTC');
defined('TIMEZONE') || define('TIMEZONE', 'UTC');

// DATABASE
defined('DB_PORT') || define('DB_PORT', 3306);
defined('DB_HOST') || define('DB_HOST', 'localhost');

get_scanned_admin_path();
// PATH
defined('ADMIN_PATH') || define('ADMIN_PATH', get_scanned_admin_path());
defined('ADMIN_DIR') || get_admin_directory();
defined('LOGIN_PATH') || define('LOGIN_PATH', DEFAULT_LOGIN_PATH);
defined('UPLOADS_PATH') || define('UPLOADS_PATH', DEFAULT_UPLOADS_PATH);
defined('THEMES_PATH') || define('THEMES_PATH', DEFAULT_THEMES_PATH);
defined('MODULES_PATH') || define('MODULES_PATH', DEFAULT_MODULES_PATH);
defined('CACHE_DIR') || define('CACHE_DIR', DEFAULT_CACHE_DIR);

define('UPLOADS_DIR', get_uploads_dir());
define('THEMES_DIR', get_themes_dir());
define('MODULES_DIR', get_themes_dir());

defined('ENABLE_MULTISITE') || define('ENABLE_MULTISITE', false);

defined('COOKIE_STUDENT_NAME') || define('COOKIE_STUDENT_NAME', 'sto_student');
defined('COOKIE_SUPERVISOR_NAME') || define('COOKIE_SUPERVISOR_NAME', 'sto_supervisor');
defined('COOKIE_TOKEN_NAME') || define('COOKIE_TOKEN_NAME', 'sto_token');

if (defined('COOKIE_MULTI_DOMAIN') && COOKIE_MULTI_DOMAIN) {
    session_set_cookie_params(['domain' => cookie_multi_domain(), 'path' => '/']);
    if (!defined('COOKIE_DOMAIN')) {
        define('COOKIE_DOMAIN', cookie_multi_domain());
    }
} elseif (!defined('COOKIE_DOMAIN')) {
    define('COOKIE_DOMAIN', get_host());
}

// no globals variable please!
unset($configBaseName);

cache_add_global_groups([
    'users',
    'site_options',
    'sites',
    'languages'
]);

// REQUIRE FILTERS BEFORE MODULE LOAD
require_once __DIR__ . '/filters.php';

if (!defined('DISABLE_MODULES') || !DISABLE_MODULES) {
    $loadedModules = [];
    foreach (get_site_wide_active_modules() as $moduleName => $time) {
        if (!is_string($moduleName)) {
            continue;
        }
        if (!($module = get_module($moduleName)) || !$module->isSiteWide()) {
            continue;
        }

        // mutable include
        (function (ArrayIterator\Info\Module $module, $moduleName, $loadedModules) {
            /** @noinspection PhpIncludeInspection */
            require_once $module->getPath();
        })($module, $moduleName, $loadedModules);
        $loadedModules[$moduleName] = time();
        hook_run('module_loaded', $moduleName, $time, $module);
        hook_run('site_wide_module_loaded', $moduleName, $time, $module);
    }

    // HOOK SITE WIDE LOADED
    hook_run('site_wide_modules_loaded', $loadedModules);

    foreach (get_site_active_modules() as $moduleName => $time) {
        if (!is_string($moduleName) || isset($loadedModules[$moduleName])) {
            continue;
        }

        if (!($module = get_module($moduleName)) || !$module->isSiteWide()) {
            continue;
        }

        // mutable include
        (function (ArrayIterator\Info\Module $module, $moduleName, $loadedModules) {
            /** @noinspection PhpIncludeInspection */
            require_once $module->getPath();
        })($module, $moduleName, $loadedModules);
        $loadedModules[$moduleName] = time();
        hook_run('module_loaded', $moduleName, $time, $module);
    }
}

unset($loadedModules, $moduleName, $time);

hook_run('modules_loaded');

if (!defined('ADMIN_LOGOUT_PAGE') || !ADMIN_LOGOUT_PAGE) {
    // set cookie token
    set_token_cookie();
}

// LOAD ROUTES AFTER MODULE LOADED
require_once __DIR__ . '/routes.php';

if (!is_admin_page()) {
    // LOAD THEME
    if ((!defined('DISABLE_THEME') || !DISABLE_THEME)
        && !is_route_api()
    ) {
        $theme = get_active_theme();
        $path = $theme->getPath();
        if ($path && is_file($path . 'functions.php')) {
            /** @noinspection PhpIncludeInspection */
            require_once $path . '/functions.php';
        }

        hook_run('theme_loaded');
        unset($theme, $path);
    }
    // do init
    init();
}
