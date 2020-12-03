<?php
/**
 * Example File Configuration
 */
// if access Direct, do the redirection
if (($_SERVER['SCRIPT_FILENAME']??null) === __FILE__) {
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
 *
 * @const THEME_PATH theme
 * -------------------------------
 */
define('ADMIN_PATH', 'admin');
define('THEME_PATH', 'theme');
define('UPLOAD_PATH', 'uploads');

// MISC
define('TIMEZONE', 'Asia/Jakarta');
define('COOKIE_MULTI_DOMAIN', true);

define('ENABLE_MULTISITE', false);
define('DEBUG', true);
