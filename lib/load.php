<?php
require_once __DIR__.'/constant.php';
require_once __DIR__.'/functions/meta.php';
require_once __DIR__.'/functions/environment.php';
require_once __DIR__.'/functions/filters.php';
require_once __DIR__.'/functions/handler.php';
require_once __DIR__.'/functions/headers.php';
require_once __DIR__.'/functions/api.php';
require_once __DIR__.'/functions/options.php';
require_once __DIR__.'/functions/settings.php';
require_once __DIR__.'/functions/user.php';
require_once __DIR__.'/functions/auth.php';
require_once __DIR__.'/functions/filters.php';
require_once __DIR__.'/functions/translations.php';
require_once __DIR__.'/functions/route.php';
require_once __DIR__.'/functions/calendar.php';

if (ob_get_level() < 1) {
    ob_start();
}

register_shutdown_function('shutdown_handler');

// require autoload
require __DIR__.'/vendor/autoload.php';

$configBaseName = CONFIG_BASE_FILENAME;
$configBaseName = substr($configBaseName, -4) !== 'php'
    ? DEFAULT_CONFIG_BASE_FILENAME
    : $configBaseName;

if (file_exists(ROOT_PATH. DS .$configBaseName)) {
    /** @noinspection PhpIncludeInspection */
    require_once ROOT_PATH . DS . $configBaseName;
} elseif (file_exists(dirname(ROOT_PATH) . DS . $configBaseName)) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(ROOT_PATH) . DS . $configBaseName;
} else {
    if (!defined('INSTALLATION_FILE')
        || (!INSTALLATION_FILE && basename($_SERVER['SCRIPT_FILENAME']) !== 'install.php')
    ) {
        clean_buffer();

        if (defined('PING_FILE') && PING_FILE) {
            json(503, 'System Unavailable');
        }

        defined('ADMIN_PATH') || define('ADMIN_PATH', get_scanned_admin_path());
        $adminPath = get_admin_path();
        if (!file_exists(ROOT_PATH . '/' . $adminPath . '/install.php')) {
            $adminPath = get_scanned_admin_path();
            if (!file_exists(ROOT_PATH . '/' . $adminPath . '/install.php')) {
                $adminPath = null;
            }
        }

        if (!headers_sent() && $adminPath) {
            header('Location: /' . $adminPath . '/install.php', true, 302);
            exit;
        }

        if (!headers_sent()) {
            header('Content-Type: text/html', true, 500);
        }

        include ROOT_TEMPLATES_PATH . '/error/install.php';
        return;
    }
}


// DEBUG
defined('DEBUG') || define('DEBUG', false);
!DEBUG ? error_reporting(0) : error_reporting(~0);

// MISC
defined('TIMEZONE') && date_default_timezone_set(TIMEZONE);

// DATABASE
defined('DB_PORT') || define('DB_PORT', 3306);
defined('DB_HOST') || define('DB_HOST', 'localhost');

// PATH
defined('ADMIN_PATH') || define('ADMIN_PATH', get_scanned_admin_path());
defined('UPLOAD_PATH') || define('UPLOAD_PATH', 'uploads');

defined('ENABLE_MULTISITE') || define('ENABLE_MULTISITE', false);
defined('THEME_PATH') || define('THEME_PATH', 'theme');

defined('COOKIE_STUDENT_NAME') || define('COOKIE_STUDENT_NAME', 'sto_student');
defined('COOKIE_SUPERVISOR_NAME') || define('COOKIE_SUPERVISOR_NAME', 'sto_supervisor');

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
