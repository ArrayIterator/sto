<?php

use ArrayIterator\ArrayGetter;

/**
 * @param string $name
 * @param null $default
 * @param int|null $siteId
 * @param bool $usePrevious
 * @return mixed
 */
function get_option(string $name, $default = null, int $siteId = null, bool $usePrevious = true)
{
    $data = option()->value($name, $default, $siteId, $usePrevious);
    return hook_apply('options_' . $name, $data, $name, $default, $siteId);
}

/**
 * @param string $name
 * @param $value
 * @param int|null $siteId
 * @return bool
 */
function update_option(string $name, $value, int $siteId = null)
{
    return option()->set($name, $value, $siteId);
}

/**
 * @param int|null $siteId
 * @param mixed ...$args
 * @return ArrayGetter
 */
function get_options(int $siteId = null, ...$args)
{
    return option()->values($siteId, ...$args);
}
