<?php

use ArrayIterator\Model\Supervisor;

/**
 * @return array|null
 */
function &student_global()
{
    static $student = null;

    return $student;
}

/**
 * @return string|false|null
 */
function get_cookie_student_data()
{
    $cookie = cookie(COOKIE_STUDENT_NAME);
    if ($cookie === null) {
        return null;
    }
    if (!is_string($cookie)) {
        return false;
    }

    return $cookie;
}

/**
 * @return false|string|null
 */
function get_cookie_supervisor_data()
{
    $cookie = cookie(COOKIE_SUPERVISOR_NAME);
    if ($cookie === null) {
        return null;
    }
    if (!is_string($cookie)) {
        return false;
    }

    return $cookie;
}

/**
 * @return array|false
 */
function get_current_student_data()
{
    $student =& student_global();
    if ($student !== null) {
        return $student;
    }

    $student = false;
    $cookies = get_cookie_student_data();
    if ($cookies && is_string($cookies)) {
        if (($cookies = validate_json_hash($cookies))
            && is_int($cookies['a'])
            && ($data = student()->findOneById($cookies['a'])->fetchClose())
        ) {
            student_online()->setOnline($data);
            $student = [
                'id' => $cookies['a'],
                'uuid' => $cookies['u'],
                'sid' => $cookies['s'],
                'user' => $data,
            ];
        }
    }

    return $student;
}

/**
 * @return array|false
 */
function get_current_supervisor_data()
{
    static $supervisor = null;
    if ($supervisor !== null) {
        return $supervisor;
    }
    $supervisor = false;
    $cookies = get_cookie_supervisor_data();
    if ($cookies && is_string($cookies)) {
        if (($cookies = validate_json_hash($cookies))
            && is_int($cookies['a'])
            && ($data = \supervisor()->findOneById($cookies['a'])->fetchClose())
        ) {
            supervisor_online()->setOnline($data);
            $supervisor = [
                'id' => $cookies['a'],
                'uuid' => $cookies['u'],
                'sid' => $cookies['s'],
                'user' => $data
            ];
        }
    }

    return $supervisor;
}

/**
 * @return Supervisor|false
 */
function get_current_supervisor()
{
    $supervisor = get_current_supervisor_data();
    return $supervisor ? ($supervisor['user'] ?? false) : false;
}

/**
 * @return array|false
 */
function get_current_student()
{
    $student = get_current_student_data();
    return $student ? ($student['user'] ?? false) : false;
}

/**
 * @return bool
 */
function is_supervisor(): bool
{
    return !!get_current_supervisor_data();
}

/**
 * @return bool
 */
function is_student(): bool
{
    return !!get_current_student_data();
}

/**
 * @return bool
 */
function is_allow_access_admin(): bool
{
    $superVisor = get_current_supervisor();
    return (bool)hook_apply(
        'allow_access_admin',
        $superVisor ? !$superVisor['disallow_admin'] : false,
        $superVisor
    );
}

/**
 * @return bool
 */
function is_allow_access_dashboard(): bool
{
    $superVisor = get_current_student();
    return (bool)hook_apply(
        'allow_access_dashboard',
        $superVisor ? !$superVisor['disallow_admin'] : false,
        $superVisor
    );
}

/**
 * @return bool
 */
function is_login(): bool
{
    if (is_admin_page()) {
        return is_supervisor();
    }
    return is_student();
}
