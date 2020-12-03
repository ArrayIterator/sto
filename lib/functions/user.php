<?php
/**
 * @return array|false
 */
function get_current_student_data()
{
    static $student = null;
    if ($student !== null) {
        return $student;
    }

    $student = false;
    $cookies = cookie(COOKIE_STUDENT_NAME);
    if ($cookies && is_string($cookies)) {
        if (($cookies = validate_json_hash($cookies))
            && is_int($cookies['a'])
            && ($data = student()->findOneById($cookies['a'])->fetchClose())
        ) {
            student_online()->setOnline($data);
            $student = hook_apply(
                'current_student_data',
                [
                    'id' => $cookies['a'],
                    'uuid' => $cookies['u'],
                    'sid' => $cookies['s'],
                    'user' => $data,
                ],
                $data
            );
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
    $cookies = cookie(COOKIE_SUPERVISOR_NAME);
    if ($cookies && is_string($cookies)) {
        if (($cookies = validate_json_hash($cookies))
            && is_int($cookies['a'])
            && ($data = \supervisor()->findOneById($cookies['a'])->fetchClose())
        ) {
            supervisor_online()->setOnline($data);
            $supervisor = hook_apply(
                'current_supervisor_data',
                [
                    'id' => $cookies['a'],
                    'uuid' => $cookies['u'],
                    'sid' => $cookies['s'],
                    'user' => $data,
                ],
                $data
            );
        }
    }

    return $supervisor;
}

function is_supervisor()
{
    return !!get_current_supervisor_data();
}

function is_student()
{
    return !!get_current_student_data();
}

function is_allow_access_admin()
{
    $superVisor = get_current_supervisor_data();
    return (bool) hook_apply(
        'allow_access_admin',
        $superVisor ? $superVisor['disallow_admin'] : false,
        $superVisor
    );
}
