<?php
// DATABASE
define('DB_HOST', 'localhost');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');

// HASH
define('SECURITY_KEY', sha1(__FILE__));
define('SECURITY_SALT', sha1(__DIR__ . SECURITY_KEY));

// COOKIE
define('COOKIE_SUPERVISOR_NAME', 'sto_supervisor');
define('COOKIE_STUDENT_NAME', 'sto_student');

// PATH
// define('ADMIN_PATH', 'admin');
// define('THEME_PATH', 'theme');

// MISC
define('TIMEZONE', 'Asia/Jakarta');
define('COOKIE_MULTI_DOMAIN', true);

define('DISABLE_MULTISITE', false);
define('DEBUG', true);
