<?php
define('APP_NAME', 'STO');
define('VERSION', '1.0.0');
define('RELEASE', '1');

define('DS', DIRECTORY_SEPARATOR);
define('LIB_DIR', __DIR__);
define('LIB_PATH', basename(__DIR__));
define('ROOT_DIR', dirname(__DIR__));
define('ROOT_TEMPLATES_DIR', __DIR__ . '/templates');
define('DEFAULT_CACHE_DIR', ROOT_DIR . ' /cache');

define('DEFAULT_API_PATH', '/api');
define('DEFAULT_LOGIN_PATH', '/login');
define('DEFAULT_UPLOADS_PATH', '/uploads');
define('DEFAULT_THEMES_PATH', '/themes');
define('DEFAULT_MODULES_PATH', '/modules');

// ENV
define('MICRO_TIME_FLOAT', microtime(true));
define('REQUEST_URI', $_SERVER['REQUEST_URI']);

// define('CLEAN_BUFFER_ERROR', true);

// TIME
define('SECOND_IN_MINUTES', 60);
define('SECOND_IN_HOUR', 3600);
define('SECOND_IN_DAY', SECOND_IN_HOUR * 24);
define('SECOND_IN_WEEK', SECOND_IN_DAY * 7);
define('SECOND_IN_MONTH', SECOND_IN_DAY * 30);
define('SECOND_IN_YEAR', SECOND_IN_DAY * 365);
define('CURRENT_YEAR', (int)date('Y'));
define('CURRENT_MONTH', (int)date('m'));

define('KB_IN_BYTES', 1024);
define('MB_IN_BYTES', 1024 * KB_IN_BYTES);
define('GB_IN_BYTES', 1024 * MB_IN_BYTES);
define('TB_IN_BYTES', 1024 * GB_IN_BYTES);

// BASE
define('DEFAULT_CONFIG_BASE_FILENAME', 'app.config.php');
defined('CONFIG_BASE_FILENAME') || define('CONFIG_BASE_FILENAME', DEFAULT_CONFIG_BASE_FILENAME);

// BROWSER
define('BROWSER_LYNX', 'Lynx');
define('BROWSER_EDGE', 'Edge');
define('BROWSER_CHROME', 'Chrome');
define('BROWSER_CHROME_FRAME', 'Chrome Frame');
define('BROWSER_SAFARI', 'Safari');
define('BROWSER_IE', 'IE');
define('BROWSER_FIREFOX', 'Firefox');
define('BROWSER_GECKO', 'Gecko');
define('BROWSER_OPERA', 'Opera');
define('BROWSER_OPERA_MINI', 'Opera Mini');
define('BROWSER_NETSCAPE_4', 'Netscape 4');

// WEB SERVER
define('WEBSERVER_APACHE', 'Apache');
define('WEBSERVER_APACHE_TOMCAT', 'Apache Tomcat');
define('WEBSERVER_APACHE_LIGHTTPD', 'Lighttpd');
define('WEBSERVER_APACHE_CHEROKEE', 'Lighttpd');
define('WEBSERVER_UNICORN', 'Unicorn');
define('WEBSERVER_LITESPEED', 'Litespeed');
define('WEBSERVER_NGINX', 'Nginx');
define('WEBSERVER_HIAWATHA', 'Hiawatha');
define('WEBSERVER_IIS', 'IIS');

// ROLE
define('ROLE_STUDENT', 'student');
define('ROLE_ADMIN', 'admin');
define('ROLE_SUPER_ADMIN', 'superadmin');
define('ROLE_TEACHER', 'teacher');
define('ROLE_AUDITOR', 'auditor');
define('ROLE_INVIGILATOR', 'invigilator');
define('ROLE_CONTRIBUTOR', 'contributor');
define('ROLE_EDITOR', 'editor');

// STATUS
define('STATUS_ACTIVE', 'active');
define('STATUS_BANNED', 'banned');
define('STATUS_PENDING', 'pending');
define('STATUS_DELETED', 'deleted');
define('STATUS_HIDDEN', 'hidden');
define('STATUS_TRASH', 'trash');
define('STATUS_PUBLISHED', 'published');
define('STATUS_DRAFT', 'draft');
define('STATUS_PUBLIC', 'public');

// POST TYPE
define('TYPE_POST', 'post');
define('TYPE_PAGE', 'page');
define('TYPE_ANNOUNCEMENT', 'announcement');
define('TYPE_REVISION', 'revision');
