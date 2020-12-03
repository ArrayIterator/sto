<?php

use ArrayIterator\Helper\RandomToken;
use ArrayIterator\Helper\Uuid;

/**
 * @param string $data
 * @param string $additional
 * @return string
 */
function create_security_hash(string $data, string $additional = '')
{
    return sha1(SECURITY_KEY . $data . SECURITY_SALT . $additional);
}

/**
 * @param int $userId
 * @param string $type
 * @return array|null
 */
function create_auth_hash(int $userId, string $type)
{
    $type = trim(strtolower($type));
    switch ($type) {
        case 'student':
            $user = student()->getById($userId);
            break;
        case 'supervisor':
            $user = supervisor()->getById($userId);
            break;
        default:
            return null;
    }

    if (!$user) {
        return null;
    }

    $uuid = Uuid::generate();
    $hash = create_security_hash($userId . '|' . $type, $uuid);
    $default = [
        'a' => $user->getId(),
        'u' => $uuid,
        't' => $type,
        'h' => RandomToken::create($hash, 0),
        's' => RandomToken::create($hash, 0)
    ];
    return array_merge($default, hook_apply('create_auth_hash', $default));
}

/**
 * @param int $userId
 * @param string $type
 * @return string
 */
function create_json_hash(int $userId, string $type)
{
    $hash = create_auth_hash($userId, $type);
    return sprintf(
        '%s',
        json_encode($hash, JSON_UNESCAPED_SLASHES)
    );
}

/**
 * @param string $data
 * @return false|array
 */
function validate_json_hash(string $data)
{
    $data = json_decode($data, true);
    if (!is_array($data)
        || !isset($data['a'], $data['u'], $data['t'], $data['h'], $data['s'])
        || !is_numeric($data['a'])
        || !is_string($data['u'])
        || !Uuid::validate($data['u'])
        || !is_string($data['t'])
        || !is_string($data['h'])
        || !is_numeric($data['s'])
    ) {
        return false;
    }

    $hash = create_security_hash($data['a'] . '|' . $data['t'], $data['u']);
    if (!RandomToken::verify($data['h'], $hash)) {
        return false;
    }

    return hook_apply('validate_json_hash', $data);
}
