<?php
define('ADMIN_LOGIN_PAGE', true);

require __DIR__ . '/init.php';

// set no cache
set_no_cache_header();

load_admin_template('login');
