<?php

use ArrayIterator\Helper\Random;
use ArrayIterator\Helper\UuidV4;
use ArrayIterator\Model\AbstractUserModel;

/* -------------------------------------------------
 *                 AUTH & VALIDATION
 * ------------------------------------------------*/

function &get_token_cookie()
{
    static $called = 0;
    static $token = null;
    if ($called === 0) {
        $called = 1;
        $token = cookie(COOKIE_TOKEN_NAME);
    }

    return $token;
}


function get_token_hash() : string
{
    $token = get_token_to_be_hashed();
    return create_form_token($token);
}

/**
 * @return string
 */
function get_token_to_be_hashed() : string
{
    $token = get_token_cookie();
    if (!is_string($token)) {
        set_token_cookie();
        $token = get_token_cookie();
    }
    return $token;
}

/**
 * @param string $data
 * @return string
 */
function create_form_token(string $data) : string
{
    return password_hash(create_security_hash($data), PASSWORD_BCRYPT);
}

/**
 * @param string $token
 * @param string|null $token_cookie
 * @return bool
 */
function validate_form_token(string $token, $token_cookie = null) : bool
{
    if ($token_cookie !== null && !is_string($token_cookie)) {
        return false;
    }

    $token_cookie = $token_cookie ?? get_token_to_be_hashed();
    $token_cookie = create_security_hash($token_cookie);
    return password_verify($token_cookie, $token);
}

/**
 * @return string
 */
function create_token_cookie() : string
{
    return create_security_hash(Random::bytes(64));
}

/**
 * @param bool $renew
 * @return string
 */
function set_token_cookie(bool $renew = false) : string
{
    $token =& get_token_cookie();
    if (!is_string($token)
        || ! preg_match('~^[a-f0-9]{40}$~', $token)
        || $renew
    ) {
        $token = create_token_cookie();
        if (!headers_sent()) {
            create_cookie(COOKIE_TOKEN_NAME, $token);
        }
    }

    return $token;
}

/**
 * @param string $data
 * @param string $additional
 * @return string
 */
function create_security_hash(string $data, string $additional = ''): string
{
    return hash_hmac('sha1', $data . SECURITY_SALT . $additional, SECURITY_KEY);
}

/**
 * @param string $hash
 * @return string
 */
function create_supervisor_hash(string $hash): string
{
    return create_type_hash($hash, SUPERVISOR);
}

/**
 * @param string $hash
 * @return string
 */
function create_student_hash(string $hash): string
{
    return create_type_hash($hash, STUDENT);
}

/**
 * @param string $hash
 * @param string $type
 * @return string
 */
function create_type_hash(string $hash, string $type): string
{
    return create_security_hash($hash, $type);
}

/**
 * @param int $userId
 * @param string $type
 * @return array|false
 */
function create_auth_hash(int $userId, string $type)
{
    $type = trim(strtolower($type));
    if (!in_array($type, [STUDENT, SUPERVISOR])) {
        return false;
    }

    $uuid = UuidV4::generate();
    $user = $type === SUPERVISOR
        ? get_supervisor_by_id($userId)
        : get_student_by_id($userId);

    if (!$user) {
        return false;
    }

    if (hook_apply('allow_create_auth_hash_different_site_id', false) !== true) {
        if ($user->getSiteId() !== get_current_site_id()) {
            return false;
        }
        if (!enable_multisite() && $user->getSiteId() !== 1) {
            return false;
        }
    }

    $siteId = $user->getSiteId() ?? get_current_site_id();
    $hash = create_type_hash($uuid, $type);
    $type = create_security_hash($userId . $hash, $siteId);
    $default = [
        'a' => $userId,
        'u' => $uuid,
        't' => $type,
        'h' => $hash,
        's' => $siteId
    ];

    return array_merge($default, hook_apply('create_auth_hash', $default));
}

/**
 * @param string $data
 * @return false|array
 */
function validate_json_hash(string $data)
{
    $dataArray = json_decode($data, true);
    if (!is_array($dataArray)
        || count($dataArray) !== 2
        || !isset($dataArray[0], $dataArray[1])
        || !is_string($dataArray[1])
        || !is_array($dataArray[0])
        || create_security_hash(serialize($dataArray[0])) === $data[1]
    ) {
        return false;
    }

    $dataArray = $dataArray[0];
    if (!isset($dataArray['a'], $dataArray['u'], $dataArray['t'], $dataArray['h'], $dataArray['s'])
        || !is_int(($userId = $dataArray['a']))
        || !is_string(($uuid = $dataArray['u']))
        || !is_string(($type = $dataArray['t']))
        || !is_string(($hash = $dataArray['h']))
        || !is_int(($siteId = $dataArray['s']))
        || !UuidV4::validate($dataArray['u'], CASE_LOWER)
    ) {
        return false;
    }

    $currentType = create_type_hash($uuid, STUDENT) === $hash
        ? STUDENT
        : (
        create_security_hash($uuid, SUPERVISOR) === $hash ? SUPERVISOR : false
        );

    if ($currentType === false) {
        return false;
    }

    if (create_security_hash($userId . $hash, $siteId) !== $type) {
        return false;
    }

    $currentData = [
        'user_id' => $userId,
        'site_id' => $siteId,
        'type' => $currentType,
        'uuid' => $uuid,
        'hash' => $hash,
        'hash_type' => $type
    ];

    return hook_apply('validate_json_hash', $currentData, $data, $dataArray);
}

/**
 * @param int $userId
 * @param string $type
 * @param bool $recreate
 * @return string
 */
function create_json_auth_user(int $userId, string $type, bool $recreate = false): string
{
    $cacheName = sprintf('%s:%d', $type, $userId);
    if (!$recreate) {
        $cache = cache_get($cacheName, 'cookie_json', $found);
        if ($found && is_string($cache) && is_array(json_decode($cache, true))) {
            return $cache;
        }
    }

    $hashArray = create_auth_hash($userId, $type);
    if (!$hashArray) {
        $data = '';
        cache_set($cacheName, '', 'cookie_json');
        return $data;
    }

    $hash = create_security_hash(serialize($hashArray));
    $hash = json_ns([$hashArray, $hash]);
    cache_set($cacheName, $hash, 'cookie_json');
    return $hash;
}

/**
 * @param int $userId
 * @param string $type
 * @param bool $recreate
 * @return string
 */
function create_json_auth_user_cookie_value(int $userId, string $type, bool $recreate = false): string
{
    return base64_encode(create_json_auth_user($userId, $type, $recreate));
}

/**
 * @param int $userId
 * @param string $type
 * @param bool $recreate
 * @return string
 * @see create_json_auth_user_cookie_value()
 */
function create_cookie_user(int $userId, string $type, bool $recreate = false) : string
{
    return create_json_auth_user_cookie_value($userId, $type, $recreate);
}

/**
 * @param int $userId
 * @param string $type
 * @param bool $remember
 * @return bool
 */
function send_user_cookie(int $userId, string $type, bool $remember = false) : bool
{
    $expire = $remember ? strtotime('+1 year') : null;
    if (!in_array($type, [SUPERVISOR, STUDENT])) {
        return false;
    }

    $cookie = create_json_auth_user_cookie_value(
        $userId,
        $type
    );
    if ($cookie == '') {
        return false;
    }
    $name = $type === SUPERVISOR
        ? COOKIE_SUPERVISOR_NAME
        : COOKIE_STUDENT_NAME;
    return create_cookie(
        $name,
        $cookie,
        $expire
    );
}

/**
 * @param int $userId
 * @param bool $remember
 * @return bool
 */
function send_supervisor_cookie(int $userId, bool $remember = false) : bool
{
    return send_user_cookie($userId, SUPERVISOR, $remember);
}

/**
 * @param int $userId student ID
 * @param bool $remember
 * @return bool
 */
function send_student_cookie(int $userId, bool $remember = false) : bool
{
    return send_user_cookie($userId, STUDENT, $remember);
}

/**
 * @param AbstractUserModel $model
 * @param bool $remember
 * @return bool
 */
function create_user_session(AbstractUserModel $model, bool $remember = false) : bool
{
    $userId = $model->getId();
    if (!$userId) {
        return false;
    }
    $data = send_user_cookie($userId, $model->getUserRoleType(), $remember);
    if ($data) {
        $data = create_json_auth_user($userId, STUDENT);
        $data = json_decode($data, true);
        $model = [
            $model,
            'login'
        ];

        if (is_array($data) && isset($data[0]) && is_array($data[0]) && isset($data[0]['u'])) {
            $model[] = hook_apply(
                'create_user_session_log_value',
                [
                    'uuid'       => $data[0]['u'],
                    'ip'         => get_real_ip_address(),
                    'user_agent' => get_user_agent(),
                    'hash' => $data[0]['h']??null,
                    'token' => get_token_cookie(),
                ]
            );
        }

        insert_user_log(...$model);
        return true;
    }

    return false;
}

/**
 * @return bool
 */
function delete_user_session() : bool
{
    $user = get_current_user_data();
    $uuid = $user ? $user->getUuid() : null;
    $hash = $user ? $user->getHash() : null;
    $user = $user ? $user->getUser() : null;
    if (!$user) {
        return false;
    }
    $sessionName = null;
    switch ($user->getUserRoleType()) {
        case SUPERVISOR:
            $sessionName = COOKIE_SUPERVISOR_NAME;
            break;
        case STUDENT:
            $sessionName = COOKIE_STUDENT_NAME;
            break;
    }

    if (!$sessionName) {
        return false;
    }

    delete_cookie($sessionName);
    insert_user_log($user, 'logout', hook_apply(
        'create_user_session_log_value',
        [
            'uuid' => $uuid,
            'ip'    => get_real_ip_address(),
            'user_agent' => get_user_agent(),
            'hash' => $hash,
            'token' => get_token_cookie(),
        ]
    ));

    return true;
}
