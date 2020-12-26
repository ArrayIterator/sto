<?php
/**
 * @param bool $default
 * @param string|null $file
 * @return bool
 */
function admin_is_allowed(string $file = null, bool $default = false): bool
{
    // always allowed super admin
    if (is_super_admin()) {
        return true;
    }

    $grants = admin_page_grants();
    $base = $file ? basename($file) : get_admin_base_name_file();
    $result = $grants[$base] ?? $default;
    return is_bool($result) ? $result : $default;
}

/**
 * @return mixed
 */
function admin_page_grants()
{
    static $page;
    if (!$page) {
        $page = [
            'admin.php' => is_super_admin(),
            'about.php' => true,
            'index.php' => true,
            'profile.php' => true,
            'modules.php' => current_supervisor_can('view_module'),
            'rooms.php' => current_supervisor_can('view_rooms'),
            'settings.php' => current_supervisor_can('manage_setting'),
            'themes.php' => current_supervisor_can('view_theme'),
            'classes.php' => current_supervisor_can('view_classes'),
            'report.php' => current_supervisor_can('view_report'),
            'supervisors.php' => current_supervisor_can('view_supervisors'),
            'invigilators.php' => current_supervisor_can('view_invigilators'),
            'students.php' => current_supervisor_can('view_students'),
            'teachers.php' => current_supervisor_can('view_teachers'),
            'tasks.php' => current_supervisor_can('view_tasks'),
            'exams.php' => current_supervisor_can('view_exams'),
            'tools.php' => current_supervisor_can('view_tools'),
            'questions.php' => current_supervisor_can('view_questions'),
            'class-new.php' => current_supervisor_can('add_class') || current_supervisor_can('edit_class'),
            'exam-new.php' => current_supervisor_can('add_exam') || current_supervisor_can('edit_exam'),
            'teacher-new.php' => current_supervisor_can('add_teacher') || current_supervisor_can('edit_teacher'),
            'invigilator-new.php' => current_supervisor_can('add_invigilator') || current_supervisor_can('edit_invigilator'),
            'room-new.php' => current_supervisor_can('add_room') || current_supervisor_can('edit_room'),
            'student-new.php' => current_supervisor_can('add_student') || current_supervisor_can('edit_student'),
            'task-new.php' => current_supervisor_can('add_task') || current_supervisor_can('edit_task'),
            'question-new.php' => current_supervisor_can('add_question') || current_supervisor_can('edit_question'),
            'status.php' => current_supervisor_can('view_status'),
        ];
    }

    return hook_apply('admin_page_grants', $page);
}

/**
 * @return bool
 */
function load_admin_denied(): bool
{
    return load_admin_template('access-denied');
}
