<?php

use ArrayIterator\Model\Religion;

/**
 * @return string
 */
function get_religion_table_name() : string
{
    return religion()->getTableName();
}

/**
 * @param string $code
 * @param int|null $siteId
 * @return Religion|false
 */
function get_religion_by_code(string $code, int $siteId = null)
{
    $siteId = $siteId ?? get_current_site_id();
    $result = cache_get_current($code, 'religions_code', $found, $siteId);
    if ($found) {
        return $result;
    }
    $result = religion()->getByCode($code, $siteId);
    cache_set_current($code, $result, 'religions_code');
    if ($result instanceof Religion) {
        $name = $result->get('name');
        cache_set_current($name, $result, 'religions_name');
    }
    return $result;
}

function get_religion_by_name(string $name, int $siteId = null)
{
    $siteId = $siteId ?? get_current_site_id();
    $result = cache_get_current($name, 'religions_name', $found, $siteId);
    if ($found) {
        return $result;
    }
    $result = religion()->getByName($name, $siteId);
    cache_set_current($name, $result, 'religions_name');
    if ($result instanceof Religion) {
        $code = $result->get('code');
        cache_set_current($code, $result, 'religions_code');
    }
    return $result;
}
