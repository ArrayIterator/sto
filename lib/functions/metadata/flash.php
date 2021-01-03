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
