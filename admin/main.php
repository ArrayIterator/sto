<?php
define('ADMIN_AREA', true);

// DENY DIRECT ACCESS
if (($_SERVER['SCRIPT_FILENAME']??null) === __FILE__) {
    !headers_sent() && header('Location: ./', true, 302);
    exit(0);
}

require_once __DIR__ . '/../lib/load.php';
require_once __DIR__ . '/includes/functions/templates.php';

set_no_index_header();

// check login
if (!is_login() && ! is_admin_login_page() && ! is_install_page()) {
    redirect(get_admin_login_url());
    do_exit(0);
}

// check if login and is on login page
if (is_supervisor() && is_admin_login_page()) {
    redirect(get_admin_url());
    do_exit(0);
}

hook_run('admin_init');
