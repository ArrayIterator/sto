<?php
require __DIR__ . '/init.php';

if (!admin_is_allowed()) {
    return load_admin_denied();
}

load_admin_template('header');
load_admin_template('footer');

