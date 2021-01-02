<?php
// ----------------------------------------------------------
// APPLICATION
// ----------------------------------------------------------
define('APP_NAME', 'STO');
define('APP_SHORT_NAME', 'STO');
define('VERSION', '1.0.0');
define('RELEASE', '1');

// ----------------------------------------------------------
// BASE
// ----------------------------------------------------------
define('DEFAULT_COOKIE_SUCCEED_NAME', '__saved_state_');
define('DEFAULT_CONFIG_BASE_FILENAME', 'app.config.php');
defined('CONFIG_BASE_FILENAME')
    || define('CONFIG_BASE_FILENAME', DEFAULT_CONFIG_BASE_FILENAME);

// ----------------------------------------------------------
// ASSETS VERSION
// ----------------------------------------------------------
define('VERSION_JQUERY', 'v3.5.1');
define('VERSION_BOOTSTRAP', 'v4.5.3');
define('VERSION_POPPER_JS', 'v2.6.0');
define('VERSION_CHART_JS', 'v2.9.4');
define('VERSION_MOMENT_JS', '2.29.1');
define('VERSION_ICOFONT_CSS', '1.0.1');
define('VERSION_SELECT2', '4.0.13');
define('VERSION_UNDERSCORE_JS', '1.12.0');
define('VERSION_CRYPTO_JS', '4.0.0');
define('VERSION_QUILL', 'v1.3.6');

// ----------------------------------------------------------
// SET MYSQL LIMIT TO 1000
// ----------------------------------------------------------
define('MYSQL_MAX_RESULT_LIMIT', 200);
define('MYSQL_MAX_SEARCH_LIMIT', 100);
define('MYSQL_DEFAULT_DISPLAY_LIMIT', 20);
define('MYSQL_DEFAULT_SEARCH_LIMIT', 20);

// ----------------------------------------------------------
// PATH
// ----------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);
define('LIB_DIR', __DIR__);
define('LIB_PATH', basename(__DIR__));
define('ROOT_DIR', dirname(__DIR__));
define('INCLUDES_DIR', __DIR__ .'/includes');
define('ROOT_TEMPLATES_DIR', __DIR__ . '/templates');
define('DEFAULT_CACHE_DIR', ROOT_DIR . ' /cache');
define('DEFAULT_LANGUAGE_DIR', ROOT_DIR . '/languages');

define('ASSETS_PATH', '/assets');
define('ASSETS_VENDOR_PATH', ASSETS_PATH.'/vendor');
define('ASSETS_CSS_PATH', ASSETS_PATH.'/css');
define('ASSETS_JS_PATH', ASSETS_PATH.'/js');
define('ASSETS_IMAGE_PATH', ASSETS_PATH.'/images');

define('DEFAULT_API_PATH', '/api');
define('DEFAULT_LOGIN_PATH', '/login');
define('DEFAULT_UPLOADS_PATH', '/uploads');
define('DEFAULT_THEMES_PATH', '/themes');
define('DEFAULT_MODULES_PATH', '/modules');

// ----------------------------------------------------------
// PARAM
// ----------------------------------------------------------
define('PARAM_SEARCH_QUERY', 'q');
define('PARAM_TYPE_QUERY', 'type');
define('PARAM_FILTER_QUERY', 'filter');
define('PARAM_LIMIT_QUERY', 'limit');
define('PARAM_OFFSET_QUERY', 'offset');
define('PARAM_PAGE_QUERY', 'page');
define('PARAM_ID_QUERY', 'id');
define('PARAM_SITE_ID_QUERY', 'site_id');
define('PARAM_SITE_IDS_QUERY', 'site_ids');
define('PARAM_ACTION_QUERY', 'action');
define('PARAM_STATUS_QUERY', 'status');
define('PARAM_ERROR_QUERY', 'error');
define('PARAM_SUCCESS_QUERY', 'success');
define('PARAM_LOGIN_QUERY', 'login');
define('PARAM_USER_ID_QUERY', 'user_id');
define('PARAM_RESPONSE_QUERY', 'response');

// ----------------------------------------------------------
// ENV
// ----------------------------------------------------------
define('SUPERVISOR', 'supervisor');
define('STUDENT', 'student');
define('MICRO_TIME_FLOAT', microtime(true));
define('REQUEST_URI', $_SERVER['REQUEST_URI']);

// define('CLEAN_BUFFER_ERROR', true);

// ----------------------------------------------------------
// TIME & SIZE
// ----------------------------------------------------------
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

// ----------------------------------------------------------
// BROWSER
// ----------------------------------------------------------
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

// ----------------------------------------------------------
// WEB SERVER
// ----------------------------------------------------------
define('WEBSERVER_APACHE', 'Apache');
define('WEBSERVER_APACHE_TOMCAT', 'Apache Tomcat');
define('WEBSERVER_APACHE_LIGHTTPD', 'Lighttpd');
define('WEBSERVER_APACHE_CHEROKEE', 'Lighttpd');
define('WEBSERVER_UNICORN', 'Unicorn');
define('WEBSERVER_LITESPEED', 'Litespeed');
define('WEBSERVER_NGINX', 'Nginx');
define('WEBSERVER_HIAWATHA', 'Hiawatha');
define('WEBSERVER_IIS', 'IIS');

// ----------------------------------------------------------
// ROLE
// ----------------------------------------------------------
define('ROLE_STUDENT', 'student');
define('ROLE_ADMIN', 'admin');
define('ROLE_SUPER_ADMIN', 'superadmin');
define('ROLE_TEACHER', 'teacher');
define('ROLE_AUDITOR', 'auditor');
define('ROLE_INVIGILATOR', 'invigilator');
define('ROLE_CONTRIBUTOR', 'contributor');
define('ROLE_EDITOR', 'editor');

// ----------------------------------------------------------
// ERROR STATUS
// ----------------------------------------------------------
define('RESULT_ERROR_EMPTY_CODE', -4);
define('RESULT_ERROR_EMPTY_NAME', -3);
define('RESULT_ERROR_EXIST_CODE', -2);
define('RESULT_ERROR_EXIST_NAME', -1);
define('RESULT_ERROR_EMPTY', 0);
define('RESULT_ERROR_OK', 1);
define('RESULT_ERROR_SUCCEED', true);
define('RESULT_ERROR_FAIL', false);

// ----------------------------------------------------------
// STATUS
// ----------------------------------------------------------
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_BANNED', 'banned');
define('STATUS_PENDING', 'pending');
define('STATUS_DELETED', 'deleted');
define('STATUS_HIDDEN', 'hidden');
define('STATUS_TRASH', 'trash');
define('STATUS_PUBLISHED', 'published');
define('STATUS_DRAFT', 'draft');
define('STATUS_PUBLIC', 'public');

// ----------------------------------------------------------
// POST TYPE
// ----------------------------------------------------------
define('TYPE_POST', 'post');
define('TYPE_PAGE', 'page');
define('TYPE_ANNOUNCEMENT', 'announcement');
define('TYPE_REVISION', 'revision');

// ----------------------------------------------------------
// HTTP STATUS CODE
// ----------------------------------------------------------
define('HTTP_CODE_CONTINUE', 100);
define('HTTP_CODE_SWITCHING_PROTOCOLS', 101);
define('HTTP_CODE_PROCESSING', 102);
define('HTTP_CODE_EARLY_HINTS', 103);
define('HTTP_CODE_OK', 200);
define('HTTP_CODE_CREATED', 201);
define('HTTP_CODE_ACCEPTED', 202);
define('HTTP_CODE_NON_AUTHORITATIVE_INFORMATION', 203);
define('HTTP_CODE_NO_CONTENT', 204);
define('HTTP_CODE_RESET_CONTENT', 205);
define('HTTP_CODE_PARTIAL_CONTENT', 206);
define('HTTP_CODE_MULTI_STATUS', 207);
define('HTTP_CODE_IM_USED', 226);
define('HTTP_CODE_MULTIPLE_CHOICES', 300);
define('HTTP_CODE_MOVED_PERMANENTLY', 301);
define('HTTP_CODE_FOUND', 302);
define('HTTP_CODE_SEE_OTHER', 303);
define('HTTP_CODE_NOT_MODIFIED', 304);
define('HTTP_CODE_USE_PROXY', 305);
define('HTTP_CODE_RESERVED', 306);
define('HTTP_CODE_TEMPORARY_REDIRECT', 307);
define('HTTP_CODE_PERMANENT_REDIRECT', 308);
define('HTTP_CODE_BAD_REQUEST', 400);
define('HTTP_CODE_UNAUTHORIZED', 401);
define('HTTP_CODE_PAYMENT_REQUIRED', 402);
define('HTTP_CODE_FORBIDDEN', 403);
define('HTTP_CODE_NOT_FOUND', 404);
define('HTTP_CODE_METHOD_NOT_ALLOWED', 405);
define('HTTP_CODE_NOT_ACCEPTABLE', 406);
define('HTTP_CODE_PROXY_AUTHENTICATION_REQUIRED', 407);
define('HTTP_CODE_REQUEST_TIMEOUT', 408);
define('HTTP_CODE_CONFLICT', 409);
define('HTTP_CODE_GONE', 410);
define('HTTP_CODE_LENGTH_REQUIRED', 411);
define('HTTP_CODE_PRECONDITION_FAILED', 412);
define('HTTP_CODE_REQUEST_ENTITY_TOO_LARGE', 413);
define('HTTP_CODE_REQUEST_URI_TOO_LONG', 414);
define('HTTP_CODE_UNSUPPORTED_MEDIA_TYPE', 415);
define('HTTP_CODE_REQUESTED_RANGE_NOT_SATISFIABLE', 416);
define('HTTP_CODE_EXPECTATION_FAILED', 417);
define('HTTP_CODE_IM_A_TEAPOT', 418);
define('HTTP_CODE_MISDIRECTED_REQUEST', 421);
define('HTTP_CODE_UNPROCESSABLE_ENTITY', 422);
define('HTTP_CODE_LOCKED', 423);
define('HTTP_CODE_FAILED_DEPENDENCY', 424);
define('HTTP_CODE_UPGRADE_REQUIRED', 426);
define('HTTP_CODE_PRECONDITION_REQUIRED', 428);
define('HTTP_CODE_TOO_MANY_REQUESTS', 429);
define('HTTP_CODE_REQUEST_HEADER_FIELDS_TOO_LARGE', 431);
define('HTTP_CODE_UNAVAILABLE_FOR_LEGAL_REASONS', 451);
define('HTTP_CODE_INTERNAL_SERVER_ERROR', 500);
define('HTTP_CODE_NOT_IMPLEMENTED', 501);
define('HTTP_CODE_BAD_GATEWAY', 502);
define('HTTP_CODE_SERVICE_UNAVAILABLE', 503);
define('HTTP_CODE_GATEWAY_TIMEOUT', 504);
define('HTTP_CODE_HTTP_VERSION_NOT_SUPPORTED', 505);
define('HTTP_CODE_VARIANT_ALSO_NEGOTIATES', 506);
define('HTTP_CODE_INSUFFICIENT_STORAGE', 507);
define('HTTP_CODE_NOT_EXTENDED', 510);
define('HTTP_CODE_NETWORK_AUTHENTICATION_REQUIRED', 511);
