<?php
/**
 * @param bool $default
 * @param string|null $file
 * @return bool
 */
function admin_is_allowed(string $file = null, bool $default = true) : bool
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
    $page = [
        'index.php' => true,
        'about.php' => true,
        'profile.php' => true,
        'modules.php' => is_admin(),
        'settings.php' => is_admin(),
        'students.php' => is_admin() || is_teacher() || is_invigilator(),
        'supervisors.php' => is_admin()
            || is_teacher() && teacher_can_see_supervisor()
            || is_invigilator() && invigilator_can_see_supervisor(),
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
