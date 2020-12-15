<?php
require __DIR__ . '/init.php';

if (
    is_teacher() && !teacher_can_see_supervisor()
    || is_invigilator() && !invigilator_can_see_supervisor()
) {
    return load_admin_template('access-denied');
}

load_admin_template('header');
load_admin_template('footer');
