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
function update_option(string $name, $value, int $siteId = null): bool
{
    return option()->set($name, $value, $siteId);
}

/**
 * @param int|null $siteId
 * @param mixed ...$args
 * @return ArrayGetter
 */
function get_options(int $siteId = null, ...$args): ArrayGetter
{
    return option()->values($siteId, ...$args);
}

/**
 * @param array $value
 * @param int|null $siteId
 * @return bool
 */
function update_options(array $value, int $siteId = null): bool
{
    if (empty($value)) {
        return false;
    }
    $options = option();
    $value = [];
    foreach ($value as $key => $item) {
        unset($value[$key]);
        if (!is_string($key)) {
            continue;
        }
        $value[$key] = $options->sanitizeDatabaseValue('option_value', $item);
    }
    if (empty($value)) {
        return false;
    }

    $siteId = $siteId ?? get_current_site_id() ?: $options->getSiteId();
    $optionTable = $options->getTableName();
    $sql = sprintf(
        'INSERT INTO %s (site_id, option_name, option_value) VALUES ',
        $optionTable
    );
    $args = [];
    $c = 0;
    foreach ($value as $key => $item) {
        unset($value[$key]);
        $c++;
        $h = sprintf(':h_%d', $c);
        $r = sprintf(':r_%d', $c);
        $args[$h] = $key;
        $args[$r] = $item;
        $sql .= sprintf('(%s, %s, %s), ', $siteId, $h, $r);
    }
    $sql = rtrim($sql, ', ');
    $sql .= ' ON DUPLICATE KEY UPDATE option_name=VALUE(option_name), option_value=VALUE(option_value)';

    $stmt = database_prepare(
        $sql
    );
    $bool = $stmt->execute($args);
    $stmt->closeCursor();
    unset($args);
    return $bool;
}


/**
 * @param string $name
 * @param int|null $siteId
 * @return bool
 */
function delete_option(string $name, int $siteId = null): bool
{
    $options = option();
    $siteId = func_num_args() === 1
        ? (get_current_site_id() ?: $options->getSiteId())
        : $siteId;
    $sql = sprintf(
        'DELETE FROM %s WHERE `site_id`%s AND option_name=?',
        $options->getTableName(),
        ($siteId === null ? ' IS NULL' : sprintf('=%s', $siteId))
    );
    $stmt = database_prepare($sql);
    $res = $stmt->execute([$name]);
    $stmt->closeCursor();
    return (bool)$res;
}

/**
 * @param string[] $name
 * @param int|null $siteId
 * @return bool
 */
function delete_options(array $name, int $siteId = null): bool
{
    if (empty($name)) {
        return false;
    }
    foreach ($name as $key => $item) {
        unset($name[$key]);
        if (!is_string($item)) {
            continue;
        }
        $name[$item] = '?';
    }
    if (empty($name)) {
        return false;
    }

    $options = option();
    $siteId = func_num_args() === 1
        ? (get_current_site_id() ?: $options->getSiteId())
        : $siteId;
    $sql = sprintf(
        'DELETE FROM %s WHERE `site_id`%s',
        $options->getTableName(),
        ($siteId === null ? ' IS NULL' : sprintf('=%s', $siteId))
    );
    if (count($name) === 1) {
        $sql .= ' AND option_name=?';
    } else {
        $sql .= sprintf(' AND option_name IN(%s)', implode(',', $name));
    }
    $stmt = database_prepare($sql);
    $res = $stmt->execute(array_keys($name));
    $stmt->closeCursor();
    return (bool)$res;
}
