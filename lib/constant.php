<?php
define('VERSION', '1.0.0');
define('RELEASE', '1');

define('LIB_PATH', __DIR__);
define('ROOT_PATH', dirname(__DIR__));
define('ROOT_TEMPLATES_PATH', __DIR__.'/templates');

define('MICRO_TIME_FLOAT', microtime(true));
define('REQUEST_URI', $_SERVER['REQUEST_URI']);
// define('CLEAN_BUFFER_ERROR', true);

define('SECOND_IN_MINUTES', 60);
define('SECOND_IN_HOUR', 3600);
define('SECOND_IN_DAY', SECOND_IN_HOUR*24);
define('SECOND_IN_WEEK', SECOND_IN_DAY*7);
define('SECOND_IN_MONTH', SECOND_IN_DAY*30);
define('SECOND_IN_YEAR', SECOND_IN_DAY*365);
define('CURRENT_YEAR', (int) date('Y'));
define('CURRENT_MONTH', (int) date('m'));
