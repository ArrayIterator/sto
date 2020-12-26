<?php

if (!defined('ADMIN_AREA')) {
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

    if (!$result || !is_numeric($args) || is_int(abs($args))) {
        return false;
    }

    if (!is_teacher()) {
        return false;
    }

    $args = abs($args);
    $class = get_class_by_id($args);
    if (!$class || $class['created_by'] !== get_current_user_id()) {
        return false;
    }

    return true;
}

hook_add('current_user_can_edit_class', 'hook_grant_current_user_can_edit_class', 1);
hook_add('current_user_can_edit_classes', 'hook_grant_current_user_can_edit_class', 1);
