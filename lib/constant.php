<?php
define('APP_NAME', 'STO');
define('VERSION', '1.0.0');
define('RELEASE', '1');

define('DS', DIRECTORY_SEPARATOR);
define('LIB_PATH', __DIR__);
define('ROOT_PATH', dirname(__DIR__));
define('ROOT_TEMPLATES_PATH', __DIR__.'/templates');

define('DEFAULT_API_PATH', '/api');

// ENV
define('MICRO_TIME_FLOAT', microtime(true));
define('REQUEST_URI', $_SERVER['REQUEST_URI']);
// define('CLEAN_BUFFER_ERROR', true);

// TIME
define('SECOND_IN_MINUTES', 60);
define('SECOND_IN_HOUR', 3600);
define('SECOND_IN_DAY', SECOND_IN_HOUR*24);
define('SECOND_IN_WEEK', SECOND_IN_DAY*7);
define('SECOND_IN_MONTH', SECOND_IN_DAY*30);
define('SECOND_IN_YEAR', SECOND_IN_DAY*365);
define('CURRENT_YEAR', (int) date('Y'));
define('CURRENT_MONTH', (int) date('m'));

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
