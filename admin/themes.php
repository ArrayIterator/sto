<?php
require __DIR__ . '/init.php';

if (!is_admin()) {
    return load_admin_template('access-denied');
}

load_admin_template('header');
load_admin_template('footer');
