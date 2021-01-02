<?php

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
function flash_get(string $name)
{
    return flash()->get($name);
}

function flash_get_data(string $name)
{
    return flash()->getData($name);
}

/**
 * @param string $name
 * @param $value
 */
function flash_set(string $name, $value)
{
    flash()->set($name, $value);
}

/**
 * @param string $name
 * @param $value
 * @return bool
 */
function flash_add(string $name, $value) : bool
{
    return flash()->add($name, $value);
}
