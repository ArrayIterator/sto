<?php

use ArrayIterator\Helper\StringFilter;
use ArrayIterator\Model\AbstractOnlineModel;
use ArrayIterator\Model\AbstractUserModel;
use ArrayIterator\Model\Student;
use ArrayIterator\Model\Supervisor;
use ArrayIterator\User;

/**
 * @return string
 */
function get_student_table_name() : string
{
    return student()->getTableName();
}

/**
 * @return string
 */
function get_supervisor_table_name() : string
{
    return supervisor()->getTableName();
}

/**
 * @return string
 */
function get_student_online_table_name() : string
{
    return student_online()->getTableName();
}

/**
 * @return string
 */
function get_supervisor_online_table_name() : string
{
    return supervisor_online()->getTableName();
}

/**
 * @return mixed|User
 * @noinspection PhpMissingReturnTypeInspection
 */
function &student_global()
{
    static $student = null;

    return $student;
}

/**
 * @return mixed|User
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
 * @return User|false
 */
function get_current_student_data()
{
    $student =& student_global();
    if ($student === false || $student instanceof User) {
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

        if (hook_apply('allow_get_current_student_data_different_site_id', false) !== true) {
            // check if site id is not 1
            if ($data->getSiteId() !== 1) {
                if (!enable_multisite()) {
                    return false;
                }
            }

            if ($data->getSiteId() !== get_current_site_id()) {
                return false;
            }
        }

        if (hook_apply('set_student_online', true) === true) {
            student_online()->setOnline($data);
        }

        $student = new User(
            $cookies['user_id'],
            $cookies['site_id'],
            $cookies['uuid'],
            $cookies['type'],
            $cookies['hash'],
            $cookies['hash_type'],
            $data
        );
        /*
        $student = [
            'user_id' => $cookies['user_id'],
            'site_id' => $cookies['site_id'],
            'uuid' => $cookies['uuid'],
            'type' => $cookies['type'],
            'hash' => $cookies['hash'],
            'hash_type' => $cookies['hash_type'],
            'user' => $data,
        ];*/
    }

    return $student;
}

/**
 * @return User|false
 */
function get_current_supervisor_data()
{
    $supervisor =& supervisor_global();
    if ($supervisor === false || $supervisor instanceof User) {
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
        if (hook_apply('allow_get_current_supervisor_data_different_site_id', false) !== true) {
            // check if site id is not 1
            if ($data->getSiteId() !== 1) {
                if (!enable_multisite()) {
                    return false;
                }
            }

            if ($data->getSiteId() !== get_current_site_id()) {
                return false;
            }
        }

        if (hook_apply('set_supervisor_online', true) === true) {
            supervisor_online()->setOnline($data);
        }
        $supervisor = new User(
            $cookies['user_id'],
            $cookies['site_id'],
            $cookies['uuid'],
            $cookies['type'],
            $cookies['hash'],
            $cookies['hash_type'],
            $data
        );
        /*
        $supervisor = [
            'user_id' => $cookies['user_id'],
            'site_id' => $cookies['site_id'],
            'uuid' => $cookies['uuid'],
            'type' => $cookies['type'],
            'hash' => $cookies['hash'],
            'hash_type' => $cookies['hash_type'],
            'user' => $data,
        ];*/
    }

    return $supervisor;
}

/**
 * @return Supervisor|false
 */
function get_current_supervisor()
{
    $supervisor = get_current_supervisor_data();
    return $supervisor ? $supervisor->getUser() : false;
}

/**
 * @return Student|false
 */
function get_current_student()
{
    $student = get_current_student_data();
    return $student ? $student->getUser() : false;
}

/**
 * @param int $id
 * @return false|Student
 */
function get_student_by_id(int $id)
{
    $key = $id;
    $user = cache_get($key, 'students', $found);
    if ($found && ($user === false || $found instanceof Student)) {
        return $user;
    }
    cache_set($key, false, 'students');
    $res = student()->findById($id);
    if ($res) {
        /**
         * @var Student
         */
        $user = $res->fetch();
        $res->closeCursor();
        cache_set($key, $user, 'students');
        if ($user) {
            $key = trim(strtolower($user->get('username')));
            cache_set($key, $user, 'students');
        }
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
    $key = $id;
    $user = cache_get($key, 'supervisors', $found);
    if ($found && ($user === false || $found instanceof Supervisor)) {
        return $user;
    }
    cache_set($key, false, 'supervisors');
    $res = supervisor()->findById($id);
    if ($res) {
        $user = $res->fetch();
        $res->closeCursor();
        cache_set($key, $user, 'supervisors');
        if ($user) {
            $key = trim(strtolower($user->get('username')));
            cache_set($key, $user, 'supervisors');
        }
        return $user;
    }
    return false;
}

/**
 * @param string $username
 * @return false|AbstractUserModel
 */
function get_supervisor_by_username(string $username)
{
    if (trim($username) === '') {
        return false;
    }

    $key = trim(strtolower($username));
    $user = cache_get($key, 'supervisors', $found);
    if ($found && ($user === false || $found instanceof Student)) {
        return $user;
    }
    cache_set($key, false, 'supervisors');
    $res = supervisor()->findOneByUsername($username);
    if ($res) {
        $user = $res->fetch();
        cache_set($key, $user, 'supervisors');
        if ($user) {
            cache_set($user->getId(), $user, 'supervisors');
        }
        $res->closeCursor();
        return $user;
    }

    return false;
}

/**
 * @param string $username
 * @return false|AbstractUserModel
 */
function get_student_by_username(string $username)
{
    if (trim($username) === '') {
        return false;
    }
    $key = trim(strtolower($username));
    $user = cache_get($key, 'students', $found);
    if ($found && ($user === false || $found instanceof Student)) {
        return $user;
    }
    cache_set($key, false, 'students');
    $res = supervisor()->findOneByUsername($username);
    if ($res) {
        $user = $res->fetch();
        cache_set($key, $user, 'students');
        if ($user) {
            cache_set($user->getId(), $user, 'students');
        }
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
    if (is_route_api()) {
        return is_supervisor() || is_student();
    }

    return is_admin_page() ? is_supervisor() : is_student();
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
 * @return User|false
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
    $userData = get_current_user_data();
    return $userData ? $userData->getUser()->getUserRoleType() : false;
}

/**
 * @return false|string
 */
function get_current_user_role()
{
    $userData = get_current_user_data();
    return $userData ? ($userData->getUser()->get('role')??false) : false;
}

/**
 * @return false|string
 */
function get_current_user_status()
{
    $userData = get_current_user_data();
    return $userData ? ($userData->getUser()->get('status')??false) : false;
}

/**
 * @return false|string
 */
function get_current_supervisor_role()
{
    $userData = get_current_supervisor();
    return $userData ? ($userData['role'] ?? false) : false;
}

/**
 * @return false|string
 */
function get_current_supervisor_status()
{
    $userData = get_current_supervisor();
    return $userData ? ($userData['status'] ?? false) : false;
}

/**
 * @return false|string
 */
function get_current_supervisor_full_name()
{
    $userData = get_current_supervisor();
    return $userData ? ($userData['full_name'] ?? false) : false;
}

/**
 * @param AbstractUserModel $model
 * @param string $type
 * @param null $data
 * @return bool
 */
function insert_user_log(AbstractUserModel $model, string $type, $data = null) : bool
{
    try {
        $obj = $model->getObjectUserLog();
        return $obj->insertData($model, $type, $data);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * @param int $studentId
 * @return AbstractOnlineModel|false|mixed
 */
function get_student_online_status(int $studentId)
{
    $key = sprintf('student(%d)', $studentId);
    $cache = cache_get($key, 'users_online', $found);
    if ($found) {
        return $cache;
    }

    $data = student_online()->userOnline($studentId);
    cache_set($key, $data, 'users_online');
    return $data;
}
/**
 * @param int $supervisor
 * @return AbstractOnlineModel|false|mixed
 */
function get_supervisor_online_status(int $supervisor)
{
    $key = sprintf('supervisor(%d)', $supervisor);
    $cache = cache_get($key, 'users_online', $found);
    if ($found) {
        return $cache;
    }

    $data = supervisor_online()->userOnline($supervisor);
    cache_set($key, $data, 'users_online');
    return $data;
}

/**
 * @param int $studentId
 * @return bool
 */
function is_student_online(int $studentId) : bool
{
    $status = get_student_online_status($studentId);
    if (!$status) {
        return false;
    }
    return (bool) ($status['online']??null);
}

/**
 * @param int $supervisorId
 * @return bool
 */
function is_supervisor_online(int $supervisorId) : bool
{
    $status = get_supervisor_online_status($supervisorId);
    if (!$status) {
        return false;
    }
    return (bool) ($status['online']??null);
}
