<?php
define('QUARANTINED_FILE', true);

require __DIR__ . '/init.php';


// check if it was admin
if (is_admin() || is_admin_active()) {
    redirect(get_admin_url());
    do_exit(0);
}

set_admin_title(trans('Quarantined'));

get_admin_header_template();
get_admin_footer_template();
