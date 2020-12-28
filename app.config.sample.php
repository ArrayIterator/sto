<?php
/**
 * Example File Configuration
 */
// if access Direct, do the redirection
if (($_SERVER['SCRIPT_FILENAME'] ?? null) === __FILE__) {
    !headers_sent() && header('Location: ./', true, 302);
}

/**
 * -------------------------------
 * DATABASE
 *
 * @const DB_HOST Database Host
 * @const DB_USER Database Username
 * @const DB_PASS Database Password
 * @const DB_NAME Database Name
 * -------------------------------
 */
define('DB_HOST', 'localhost');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');

/**
 * -------------------------------
 * SECURITY
 *
 * @const SECURITY_KEY & SECURITY_SALT for
 *  reference of hashing & security key method
 * -------------------------------
 */
define('SECURITY_KEY', 'fill with random string');
define('SECURITY_SALT', 'fill with random string');

/**
 * -------------------------------
 * COOKIE
 *
 * @const COOKIE_SUPERVISOR_NAME for supervisor cookie name
 * @const COOKIE_STUDENT_NAME for students cookie name
 * -------------------------------
 */
define('COOKIE_SUPERVISOR_NAME', 'sto_supervisor');
define('COOKIE_STUDENT_NAME', 'sto_student');

/**
 * -------------------------------
 * PATH
 *
 * @const ADMIN_PATH for admin directory path
 *  please make sure declare this correctly
 *  & admin only could be placed on root directory
 *  fill without slashed cause slashed will be trimmed
 *  and add single slash on first path
 *
 * @const THEMES_PATH Themes path
 * @const LOGIN_PATH login uri suffix URL
 * @const UPLOADS_PATH Upload Path
 * @const MODULES_PATH The Modules Path
 * @const CACHE_DIR The cache directory
 * @const LANGUAGE_DIR The language directory
 * -------------------------------
 */
define('ADMIN_PATH', '/admin');
define('LOGIN_PATH', '/login');
define('THEMES_PATH', '/themes');
define('UPLOADS_PATH', '/uploads');
define('MODULES_PATH', '/modules');
define('CACHE_DIR', __DIR__ . '/cache');
define('LANGUAGE_DIR', __DIR__ . '/languages');

// MISC
define('TIMEZONE', 'Asia/Jakarta');
define('COOKIE_MULTI_DOMAIN', true);
define('DEFAULT_LANGUAGE', 'en');
define('ENABLE_MULTISITE', false);
// DEBUG MODE
define('DEBUG', false);

// DEFAULT GLOBAL SITE HOST IF SITE DATABASE EMPTY
define('DEFAULT_SITE_HOST', 'sto.dot');
define('DEFAULT_ADDITIONAL_HOST', 'sto.dot');
// FORCE MATCHES SITE HOST, SEPARATED BY COMMA
define('FORCE_SITE_HOST', 'example.com, www.example.com');