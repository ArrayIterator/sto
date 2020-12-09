<?php
define('ADMIN_LOGIN_PAGE', true);

require __DIR__ . '/main.php';

// set no cache
set_no_cache_header();

get_admin_header_template();
get_admin_footer_template();
