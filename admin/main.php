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
if (!is_login()
    && (!defined('ADMIN_LOGIN_PAGE') || !ADMIN_LOGIN_PAGE)
    && (!defined('INSTALLATION_FILE') || !INSTALLATION_FILE)
) {
    redirect(get_admin_login_url());
    do_exit(0);
}
