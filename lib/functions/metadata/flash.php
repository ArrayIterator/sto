<?php

/**
 * @param string $name
 * @return string
 */
function set_flash_prefix(string $name) : string
{
    return flash()->setPrefix($name);
}

/**
 * @return string
 */
function get_flash_prefix() : string
{
    return flash()->getPrefix();
}

/**
 * @return string
 */
function get_flash_table_name() : string
{
    return flash()->getTableName();
}

/**
 * @param string $name
 * @return mixed|null
 */
function flash_get(string $name, string $prefix = null)
{
    return flash()->get($name, $prefix);
}

function flash_get_data(string $name, string $prefix = null)
{
    return flash()->getData($name, $prefix);
}

/**
 * @param string $name
 * @param $value
 * @param string|null $prefix
 */
function flash_set(string $name, $value, string $prefix = null)
{
    flash()->set($name, $value, $prefix);
}

/**
 * @param string $name
 * @param $value
 * @param string|null $prefix
 * @return bool
 */
function flash_add(string $name, $value, string $prefix = null) : bool
{
    return flash()->add($name, $value, $prefix);
}

/**
 * @param string $name
 * @param $message
 * @param null $type
 * @param mixed ...$args
 * @return array
 */
function flash_set_succeed(string $name, $message, $type = null, ...$args) : array
{
    $res = [
        PARAM_STATUS => PARAM_SUCCESS,
        PARAM_TYPE => $type,
        PARAM_MESSAGE => $message
    ];
    foreach ($args as $key => $item) {
        $res[$key] = $item;
    }

    flash_set($name, $res);
    return $res;
}

function flash_set_error(string $name, $message, $type = null, ...$args) : array
{
    $res = [
        PARAM_STATUS => PARAM_ERROR,
        PARAM_TYPE => $type,
        PARAM_MESSAGE => $message
    ];
    foreach ($args as $key => $item) {
        $res[$key] = $item;
    }
    flash_set($name, $res);

    return $res;
}

/**
 * @param string $name
 * @return array|false
 */
function flash_get_succeed(string $name)
{
    $flash = flash_get_status_response($name);
    if (!$flash || !isset($flash[PARAM_STATUS], $flash[PARAM_MESSAGE])
        || $flash[PARAM_STATUS] !== PARAM_SUCCESS
    ) {
        return false;
    }
    return $flash;
}

/**
 * @param string $name
 * @return array|false
 */
function flash_get_error(string $name)
{
    $flash = flash_get_status_response($name);
    if (!$flash || !isset($flash[PARAM_STATUS], $flash[PARAM_MESSAGE]) || $flash[PARAM_STATUS] !== PARAM_ERROR) {
        return false;
    }

    return $flash;
}

/**
 * @param string $name
 * @return array|false
 */
function flash_get_status_response(string $name)
{
    $flash = flash_get($name);
    if (!is_array($flash)
        || !isset($flash[PARAM_MESSAGE])
        || !isset($flash[PARAM_STATUS])
        || ! array_key_exists(PARAM_TYPE, $flash)
    ) {
        return false;
    }

    return $flash;
}
