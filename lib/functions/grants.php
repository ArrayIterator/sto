<?php

/**
 * @param string $can
 * @param bool $default
 * @return bool
 */
function current_supervisor_can(
    string $can,
    bool $default = null
) : bool {
    $supervisor = get_current_supervisor();
    if (!$supervisor) {
        return false;
    }
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
    $is_admin = is_admin();
    $admin = $is_active && $is_admin;
    $teacher_can_see_supervisor     = $teacher && teacher_can_see_supervisor();
    $teacher_can_see_supervisors    = $teacher && teacher_can_see_supervisors();
    $invigilator_can_see_supervisor = $invigilator && invigilator_can_see_supervisor();
    $invigilator_can_see_supervisors = $invigilator && invigilator_can_see_supervisors();
    $teacher_admin_grant = $admin || $teacher;

    switch ($can) {
        case 'view_about':
            return $is_active;

        case 'manage_setting':
        case 'manage_settings':
        case 'manage_tool':
        case 'manage_tools':

        case 'view_theme':
        case 'view_themes':
        case 'change_theme':

        case 'view_module':
        case 'view_modules':
        case 'change_module':
        case 'activate_module':

        case 'add_class':
        case 'edit_class':
        case 'delete_class':

        case 'add_room':
        case 'edit_room':
        case 'delete_room':

        case 'add_supervisor':
        case 'edit_supervisor':
        case 'delete_supervisor':

        case 'add_teacher':
        case 'edit_teacher':

        case 'add_invigilator':
        case 'edit_invigilator':

            return $admin;

        case 'add_student':
        case 'edit_student':
        case 'delete_student':

        case 'add_question':
        case 'edit_question':
        case 'delete_question':

        case 'add_task':
        case 'edit_task':
        case 'delete_task':

        case 'add_exam':
        case 'edit_exam':
        case 'delete_exam':

        case 'view_tools':
            return $teacher_admin_grant;

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

            return $teacher_admin_grant || $invigilator;

        case 'view_teachers':
        case 'view_teacher':
            return $teacher_admin_grant || $invigilator_can_see_supervisor;

        case 'view_supervisor':
        case 'view_invigilator':
            return $admin || $teacher_can_see_supervisor || $invigilator_can_see_supervisor;

        case 'view_supervisors':
        case 'view_invigilators':
            return $admin || $teacher_can_see_supervisors || $invigilator_can_see_supervisors;

    }

    return $default === null ? $is_active : $default;
}