<?php

use ArrayIterator\Database\PrepareStatement;
use ArrayIterator\Helper\StringFilter;
use ArrayIterator\Model\Student;
use ArrayIterator\Model\Supervisor;

/**
 * @return mixed|array
 * @noinspection PhpMissingReturnTypeInspection
 */
function &student_global()
{
    static $student = null;

    return $student;
}

/**
 * @return mixed|array
 * @noinspection PhpMissingReturnTypeInspection
 */
function &supervisor_global()
{
    static $supervisor = null;
    return $supervisor;
}

/**
 * @return string|false|null
 */
function get_cookie_student_data()
{
    $cookie = cookie(COOKIE_STUDENT_NAME);
    if ($cookie === null) {
        $cookie = null;
    } elseif (!is_string($cookie)) {
        $cookie = false;
    }

    return hook_apply('cookie_student_data', $cookie);
}

/**
 * @return false|string|null
 */
function get_cookie_supervisor_data()
{
    $cookie = cookie(COOKIE_SUPERVISOR_NAME);
    if ($cookie === null) {
        $cookie = null;
    } elseif (!is_string($cookie)) {
        $cookie = false;
    }

    return hook_apply('cookie_supervisor_data', $cookie);
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
        $cookies = base64_decode($cookies);
        if (StringFilter::isBinary($cookies)) {
            return false;
        }
        $cookies = $cookies ? validate_json_hash($cookies) : false;
        if (!is_array($cookies)
            || !isset(
                $cookies['user_id'],
                $cookies['site_id'],
                $cookies['type'],
                $cookies['hash'],
                $cookies['hash_type']
            )
            || $cookies['type'] !== STUDENT
            || !is_int($cookies['user_id'])
            || !is_int($cookies['site_id'])
            || !($data = student()->findOneById($cookies['user_id'])->fetchClose())
            || ($data->getSiteId() ?? $cookies['site_id']) !== $cookies['site_id']
        ) {
            return false;
        }

        if (hook_apply('set_student_online', true) === true) {
            student_online()->setOnline($data);
        }

        $student = [
            'user_id' => $cookies['user_id'],
            'site_id' => $cookies['site_id'],
            'uuid' => $cookies['uuid'],
            'type' => $cookies['type'],
            'hash' => $cookies['hash'],
            'hash_type' => $cookies['hash_type'],
            'user' => $data,
        ];
    }

    return $student;
}

/**
 * @return array|false
 */
function get_current_supervisor_data()
{

    $supervisor =& supervisor_global();
    if ($supervisor !== null) {
        return $supervisor;
    }

    $supervisor = false;
    $cookies = get_cookie_supervisor_data();
    if (is_string($cookies)) {
        $cookies = base64_decode($cookies);
        if (StringFilter::isBinary($cookies)) {
            return false;
        }
        if (!is_array(($cookies = $cookies ? validate_json_hash($cookies) : false))
            || !isset(
                $cookies['user_id'],
                $cookies['site_id'],
                $cookies['type'],
                $cookies['hash'],
                $cookies['hash_type']
            )
            || $cookies['type'] !== SUPERVISOR
            || !is_int($cookies['user_id'])
            || !is_int($cookies['site_id'])
            || !($data = \supervisor()->findOneById($cookies['user_id'])->fetchClose())
            || $data->getSiteId() !== $cookies['site_id']
        ) {
            return false;
        }
        if (hook_apply('set_supervisor_online', true) === true) {
            supervisor_online()->setOnline($data);
        }
        $supervisor = [
            'user_id' => $cookies['user_id'],
            'site_id' => $cookies['site_id'],
            'uuid' => $cookies['uuid'],
            'type' => $cookies['type'],
            'hash' => $cookies['hash'],
            'hash_type' => $cookies['hash_type'],
            'user' => $data,
        ];
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
 * @param int $id
 * @return false|Student
 */
function get_student_by_id(int $id)
{
    $res = student()->findById($id);
    if ($res) {
        $user = $res->fetch();
        $res->closeCursor();
        return $user;
    }

    return false;
}

/**
 * @param int $id
 * @return false|Supervisor
 */
function get_supervisor_by_id(int $id)
{
    $res = supervisor()->findById($id);
    if ($res) {
        $user = $res->fetch();
        $res->closeCursor();
        return $user;
    }
    return false;
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
 * Detect user logged in by dashboard area
 *
 * @return bool
 */
function is_login(): bool
{
    if (is_admin_page()) {
        return is_supervisor();
    }

    return is_student();
}

/**
 * Check user if logged in, even student or supervisor
 * @return bool
 */
function is_user_logged(): bool
{
    return is_student() || is_supervisor();
}

/**
 * @return array|false
 */
function get_current_user_data()
{
    if (!is_login()) {
        return false;
    }

    return is_admin_page()
        ? get_current_supervisor_data()
        : get_current_student_data();
}

/**
 * @return int
 */
function get_current_user_id(): int
{
    $user = get_current_user_data();
    return $user ? $user['user_id'] : 0;
}

/**
 * @return false|string
 */
function get_current_user_type()
{
    $userData = get_current_student_data();
    return $userData ? $userData['type'] : false;
}

/**
 * @return false|string
 */
function get_current_user_status()
{
    $userData = get_current_user_data();
    return $userData ? ($userData['status'] ?? false) : false;
}

/**
 * @return false|string
 */
function get_current_supervisor_role()
{
    $userData = get_current_supervisor();
    return $userData ? ($userData['role'] ?? false) : false;
}
