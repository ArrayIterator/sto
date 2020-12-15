<?php
require __DIR__ . '/init.php';

if (!admin_is_allowed(__FILE__)) {
    return load_admin_denied();
}

set_admin_title(trans('General Settings'));

get_admin_header_template();
get_admin_footer_template();
