<?php
/**
 * @param bool $default
 * @param string|null $file
 * @return bool
 */
function admin_is_allowed(string $file = null, bool $default = false) : bool
{
    // always allowed super admin
    if (is_super_admin()) {
        return true;
    }
    $grants = admin_page_grants();
    $base = $file ? basename($file) : get_admin_base_name_file();
    $result = $grants[$base]??$default;
    return is_bool($result) ? $result : $default;
}

/**
 * @return mixed
 */
function admin_page_grants()
{
    $is_admin       = is_admin();
    $is_teacher     = is_teacher();
    $is_invigilator = is_invigilator();
    $teacher_can_see_supervisor = $is_teacher && teacher_can_see_supervisor();
    $invigilator_can_see_supervisor = $is_invigilator && invigilator_can_see_supervisor();
    $teacher_admin_grant = $is_admin || $is_teacher;
    $page = [
        'about.php' => true,
        'admin.php' => is_super_admin(),
        'index.php' => true,
        'modules.php' => $is_admin,
        'profile.php' => is_supervisor(),
        'settings.php' => $is_admin,
        'themes.php' => $is_admin,
        'report.php' => $is_admin || $is_teacher || $is_invigilator,
        'supervisors.php'  => $is_admin || $teacher_can_see_supervisor || $invigilator_can_see_supervisor,
        'invigilators.php' => $teacher_admin_grant || $invigilator_can_see_supervisor,
        'students.php'  => $teacher_admin_grant || $is_invigilator,
        'teachers.php'  => $teacher_admin_grant || $invigilator_can_see_supervisor,
        'tasks.php'      => $teacher_admin_grant || $is_invigilator,
        'exams.php'      => $teacher_admin_grant || $is_invigilator,
        'tools.php'      => $teacher_admin_grant,
        'questions.php'  => $teacher_admin_grant || $is_invigilator,
        'teacher-new.php'  => $is_admin,
        'invigilator-new.php'  => $is_admin,
        'student-new.php'  => $teacher_admin_grant,
        'exam-new.php'     => $teacher_admin_grant,
        'task-new.php'     => $teacher_admin_grant,
        'question-new.php' => $teacher_admin_grant,
    ];

    return hook_apply('admin_page_grants', $page);
}

/**
 * @return bool
 */
function load_admin_denied() : bool
{
    return load_admin_template('access-denied');
}
