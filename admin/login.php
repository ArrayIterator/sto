<?php
define('ADMIN_AREA', true);
define('ADMIN_LOGIN_PAGE', true);

require_once dirname(__DIR__) . '/lib/load.php';

set_header('X-Robots-Tag', ROBOTS_ALL_NOINDEX);
