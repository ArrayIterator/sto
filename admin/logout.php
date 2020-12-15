<?php
define('ADMIN_LOGOUT_PAGE', true);

require __DIR__ . '/init.php';

if (is_admin_login()) {
    delete_cookie(COOKIE_SUPERVISOR_NAME);
}

redirect(get_admin_login_url());
do_exit(0);
