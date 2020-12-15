<?php
define('ADMIN_AREA', true);
define('ADMIN_DIR', __DIR__);

// DENY DIRECT ACCESS
if (($_SERVER['SCRIPT_FILENAME'] ?? null) === __FILE__) {
    !headers_sent() && header('Location: ./', true, 302);
    exit(0);
}

// LOAD LOADER
require_once dirname(__DIR__) . '/lib/load.php';
require_once __DIR__ . '/includes/menus.php';
require_once __DIR__ . '/includes/pages.php';

// SET NO ROBOTS INDEX
set_no_index_header();

// check login
if (!is_login() && !is_admin_login_page() && !is_install_page()) {
    redirect(get_admin_login_url());
    do_exit(0);
}
if (is_supervisor()) {
    if (is_admin_login_page()) {
        // check if login and is on login page
        redirect(get_admin_url());
        do_exit(0);
    }

    if (!is_admin_quarantine_page() && !is_admin() && !is_admin_active()) {
        if (!is_admin_profile_page()) {
            redirect(get_admin_url('quarantined.php'));
            do_exit(0);
        }
    }
}

hook_run('admin_init');
