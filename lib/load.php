<?php
require_once __DIR__.'/constant.php';
require_once __DIR__.'/vendor/autoload.php';
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

if (file_exists(dirname(__DIR__).'/app.config.php')) {
    require_once dirname(__DIR__).'/app.config.php';
} elseif (file_exists(dirname(dirname(__DIR__)).'/app.config.php')) {
    require_once dirname(dirname(__DIR__)).'/app.config.php';
} else {
    if (!defined('INSTALLATION_FILE')
        || (!INSTALLATION_FILE && basename($_SERVER['SCRIPT_FILENAME']) !== 'install.php')
    ) {
        clean_buffer();

        if (defined('PING_FILE') && PING_FILE) {
            json(503, 'System Unavailable');
        }

        if (!defined('ADMIN_PATH')) {
            define('ADMIN_PATH', get_admin_path());
        }

        $adminPath = trim(ADMIN_PATH, '/');
        if (!file_exists(ROOT_PATH . '/' . $adminPath . '/install.php')) {
            $adminPath = get_admin_path();
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

defined('DEBUG') || define('DEBUG', false);

!DEBUG ? error_reporting(0) : error_reporting(~0);

defined('TIMEZONE') && date_default_timezone_set(TIMEZONE);
defined('DB_PORT') || define('DB_PORT', 3306);
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('ROUTE_API') || define('ROUTE_API', (bool) preg_match('~^/api(/.*)?$~', request_uri()));
defined('DISABLE_MULTISITE') || define('DISABLE_MULTISITE', true);
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

//var_dump(disable_multisite());
//var_dump(get_site_host_type());
//var_dump(current_site_meta());
//exit;
//ob_get_clean();
//ob_end_clean();
//no_buffer();
//print_r(ob_get_level());
//exit;
