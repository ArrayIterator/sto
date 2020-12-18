<?php
define('ADMIN_LOGOUT_PAGE', true);

require __DIR__ . '/init.php';

if (is_admin_login()) {
    delete_user_session();
}

redirect(get_admin_login_url().'?logout=success');
do_exit(0);
