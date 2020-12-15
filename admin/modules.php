<?php
require __DIR__ . '/init.php';

if (!admin_is_allowed(__FILE__)) {
    return load_admin_denied();
}


switch (query_param('status')) {
    case 'active':
        set_admin_title(trans('Active Modules'));
        break;
    case 'inactive':
        set_admin_title(trans('Inactive Modules'));
        break;
    default:
        set_admin_title(trans('All Modules'));
}

get_admin_header_template();
get_admin_footer_template();
