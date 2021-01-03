<?php

/**
 * @param string $can
 * @param $args
 * @return bool
 */
function current_supervisor_can(
    string $can,
    ...$args
): bool {

    if (!is_login()) {
        return false;
    }

    $supervisor = get_current_supervisor();
    if (is_super_admin()) {
        return true;
    }

    $is_active = is_admin_active();

    $is_deleted = is_admin_deleted();
    $is_banned = is_admin_banned();
    $is_pending = is_admin_pending();

    $is_teacher = is_teacher();
    $is_invigilator = is_invigilator();

    $teacher = $is_active && $is_teacher;
    $invigilator = $is_active && $is_invigilator;

    $admin = is_admin_and_active();
    $teacher_can_see_supervisor = $teacher && teacher_can_see_supervisor();
    $teacher_can_see_supervisors = $teacher && teacher_can_see_supervisors();
    $invigilator_can_see_supervisor = $invigilator && invigilator_can_see_supervisor();
    $invigilator_can_see_supervisors = $invigilator && invigilator_can_see_supervisors();
    $teacher_admin_grant = $admin || $teacher;
    $current_can = 'current_user_can_' . $can;
    $result = false;
    switch ($can) {
        case 'view_about':
            $result = $is_active;
            break;
        case 'manage_setting':
        case 'manage_settings':
        case 'manage_tool':
        case 'manage_tools':

        case 'view_theme':
        case 'view_themes':
        case 'change_theme':

        case 'view_module':
        case 'view_modules':
        case 'deactivate_module':
        case 'deactivate_modules':
        case 'activate_module':
        case 'activate_modules':

        case 'delete_class':
        case 'delete_classes':
        case 'add_room':

        case 'edit_class':
        case 'edit_classes':
        case 'edit_room':
        case 'delete_room':

        case 'edit_supervisor':
        case 'edit_supervisors':
        case 'delete_supervisor':

        case 'edit_teacher':
        case 'edit_teachers':
        case 'edit_invigilator':
        case 'edit_invigilators':

            $result = $admin;
            break;

        case 'edit_student':
        case 'edit_students':
        case 'delete_student':

        case 'edit_question':
        case 'delete_question':

        case 'edit_task':
        case 'delete_task':

        case 'edit_exam':
        case 'delete_exam':

        case 'view_tools':

            $result = $teacher_admin_grant;

            break;

        case 'add_exam':
        case 'add_class':
        case 'add_classes':
        case 'add_supervisor':
        case 'add_teacher':
        case 'add_invigilator':
        case 'add_student':
        case 'add_question':
        case 'add_task':
            $result = $admin
                || current_supervisor_can(
                    substr_replace($can, 'edit_', 0, 4),
                    ...$args
                );

            break;

        // view
        case 'view_students':
        case 'view_student':
        case 'view_classes':
        case 'view_class':
        case 'view_questions':
        case 'view_question':
        case 'view_tasks':
        case 'view_task':
        case 'view_exams':
        case 'view_exam':
        case 'view_report':
        case 'view_rooms':
        case 'view_room':
        case 'view_status':
            $result = $teacher_admin_grant
                || $invigilator
                || current_supervisor_can(
                    substr_replace($can, 'edit_', 0, 5),
                    ...$args
                );

            break;
        case 'view_teachers':
        case 'view_teacher':

            $result = $teacher_admin_grant
                || $invigilator_can_see_supervisor
                || current_supervisor_can(
                    substr_replace($can, 'edit_', 0, 5),
                    ...$args
                );

            break;

        case 'view_supervisor':
        case 'view_invigilator':
            $result = $admin
                || $teacher_can_see_supervisor
                || $invigilator_can_see_supervisor
                || current_supervisor_can(
                    substr_replace($can, 'edit_', 0, 5),
                    ...$args
                );

            break;
        case 'view_supervisors':
        case 'view_invigilators':
            $result = $admin
                || $teacher_can_see_supervisors
                || $invigilator_can_see_supervisors
                || current_supervisor_can(
                    substr_replace($can, 'edit_', 0, 5),
                    ...$args
                );

            break;
    }

    // check if is not in stack
    if (!hook_is_in_stack($current_can)) {
        return hook_apply($current_can, $result, ...$args);
    }

    return $result;
}