<?php

if (!defined('ROOT_DIR') || !defined('ADMIN_AREA')) {
    return;
}

/**
 * @param $result
 * @param null $args
 * @return bool
 */
function hook_grant_current_user_can_edit_class(bool $result, $args = null) : bool
{
    if (is_admin_and_active()) {
        return true;
    }
    if ($args === null) {
        return false;
    }

    $args_n = abs_r($args);
    if (!$result || ! is_int($args_n)) {
        return false;
    }

    if (!is_teacher()) {
        return false;
    }

    $class = get_class_by_id($args_n);
    if (!$class || $class['created_by'] !== get_current_user_id()) {
        return false;
    }

    return true;
}

hook_add('current_user_can_edit_class', 'hook_grant_current_user_can_edit_class', 1);
hook_add('current_user_can_edit_classes', 'hook_grant_current_user_can_edit_class', 1);
//hook_add('script_loader_ver', 'return_string');
// hook_add('style_loader_ver', 'return_string');