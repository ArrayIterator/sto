<?php

use ArrayIterator\Helper\UuidV4;

/* -------------------------------------------------
 *                 AUTH & VALIDATION
 * ------------------------------------------------*/

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
    $hash = json_encode([$hashArray, $hash], JSON_UNESCAPED_SLASHES);
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
