<?php
require __DIR__ . '/init.php';

if (!admin_is_allowed(__FILE__)) {
    return load_admin_denied();
}

set_admin_title('Dashboard');

get_admin_header_template();
?>
<?php
list($hours, $minutes, $seconds) = calculate_clock_delay();

get_admin_footer_template();
