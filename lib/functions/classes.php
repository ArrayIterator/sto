<?php
/**
 * @return string
 */
function get_classes_table_name() : string
{
    return 'sto_classes';
}

/**
 * @param int $id
 * @return array|false
 */
function get_class_by_id(int $id)
{
    $cache = cache_get($id, 'classes', $found);
    if ($found && ($found === false || is_array($cache))) {
        return $cache;
    }
    $table = get_classes_table_name();
    $stmt = database_query("SELECT * FROM {$table} WHERE id={$id}");
    $res = false;
    if ($stmt) {
        $res = $stmt->fetchAssoc();
        $stmt->closeCursor();
        $res = $res?:false;
        if ($res) {
            $res['site_id'] = (int) ($res['site_id']??null);
            $res['id'] = (int) ($res['id']??null);
            cache_set(
                sprintf('%d(%s)', $res['site_id'], trim($res['code'])),
                $res,
                'classes_by_code'
            );
            cache_set(
                sprintf('%d(%s)', $res['site_id'], trim($res['name'])),
                $res,
                'classes_by_name'
            );
        }
    }
    cache_set($id, $res, 'classes');
    return $res;
}

/**
 * @param string $name
 * @param int|null $siteId
 * @return array|false
 */
function get_class_by_name(string $name, int $siteId = null)
{
    $siteId = $siteId??get_current_site_id();
    $cacheName = sprintf('%d(%s)', $siteId, trim($name));
    $cache = cache_get(
        $cacheName,
        'classes_by_name',
        $found
    );
    if ($found && ($found === false || is_array($cache))) {
        return $cache;
    }
    $table = get_classes_table_name();
    $stmt = database_prepare(
        "SELECT * FROM {$table} WHERE site_id={$siteId} AND name=? LIMIT 1"
    );
    $res = false;
    if ($stmt->execute([trim($name)])) {
        $res = $stmt->fetchAssoc();
        $stmt->closeCursor();
        $res = $res?:false;
        if ($res) {
            $id = (int) $res['id'];
            $res['site_id'] = (int) ($res['site_id']??null);
            $res['id'] = (int) ($res['id']??null);
            cache_set(
                sprintf('%d(%s)', $res['site_id'], trim($res['code'])),
                $res,
                'classes_by_code'
            );
            cache_set(
                $id,
                $res,
                'classes'
            );
        }
    }
    cache_set($cacheName, $res, 'classes_by_name');
    return $res;
}

/**
 * @param string $name
 * @param int|null $siteId
 * @param int $limit
 * @return array
 */
function search_class_by_name(
    string $name,
    int $siteId = null,
    int $limit = MYSQL_DEFAULT_SEARCH_LIMIT
) : array {
    $siteId = $siteId??get_current_site_id();
    $name = trim($name);
    $limit = $limit <= 1 ? 1 : (
        $limit > MYSQL_MAX_SEARCH_LIMIT
            ? MYSQL_MAX_SEARCH_LIMIT
            : $limit
    );
    $table = get_classes_table_name();
    $like = database_quote(sprintf('%%%s%%', $name));
    $likeL = database_quote(sprintf('%s%%', $name));
    $nameQ = database_quote($name);
    $stmt = database_prepare(
        "SELECT * FROM {$table} 
            WHERE site_id={$siteId} 
              AND name LIKE {$like}
            ORDER BY IF(name = {$nameQ}, 2, IF(name LIKE {$likeL},1,0)) DESC LIMIT {$limit}
    "
    );
    $data = [];
    if ($stmt->execute([trim($name)])) {
        while($res = $stmt->fetchAssoc()) {
            $id = (int)$res['id'];
            $res['site_id'] = (int)($res['site_id'] ?? null);
            $res['id'] = (int)($res['id'] ?? null);
            $res['code'] = trim((string)($res['code'] ?? null));
            cache_set(
                sprintf('%d(%s)', $res['site_id'], $res['code']),
                $res,
                'classes_by_code'
            );
            cache_set(
                sprintf('%d(%s)', $res['site_id'], $res['name']),
                $res,
                'classes_by_name'
            );
            cache_set(
                $id,
                $res,
                'classes'
            );
            $data[] = $res;
        }
        $stmt->closeCursor();
    }

    unset($stmt);

    return $data;
}

/**
 * @param string $code
 * @param int|null $siteId
 * @return array|false
 */
function get_class_by_code(string $code, int $siteId = null)
{
    $siteId = $siteId??get_current_site_id();
    $cacheName = sprintf('%d(%s)', $siteId, trim($code));
    $cache = cache_get(
        $cacheName,
        'classes_by_code',
        $found
    );
    if ($found && ($found === false || is_array($cache))) {
        return $cache;
    }
    $table = get_classes_table_name();
    $stmt = database_prepare(
        "SELECT * FROM {$table} WHERE site_id={$siteId} AND code=? LIMIT 1"
    );

    $res = false;
    if ($stmt->execute([trim($code)])) {
        $res = $stmt->fetchAssoc();
        $stmt->closeCursor();
        $res = $res?:false;
        if ($res) {
            $id = (int) $res['id'];
            $res['site_id'] = (int) ($res['site_id']??null);
            $res['id'] = (int) ($res['id']??null);
            cache_set(
                sprintf('%d(%s)', $res['site_id'], trim($res['name'])),
                $res,
                'classes_by_name'
            );
            cache_set(
                $id,
                $res,
                'classes'
            );
        }
    }
    cache_set($cacheName, $res, 'classes_by_code');
    return $res;
}

/**
 * @param string $code
 * @param int|null $siteId
 * @param int $limit
 * @return array
 */
function search_class_by_code(
    string $code,
    int $siteId = null,
    int $limit = MYSQL_DEFAULT_SEARCH_LIMIT
) : array {
    $siteId = $siteId??get_current_site_id();
    $code = trim($code);
    $limit = $limit <= 1 ? 1 : (
    $limit > MYSQL_MAX_SEARCH_LIMIT
        ? MYSQL_MAX_SEARCH_LIMIT
        : $limit
    );
    $table = get_classes_table_name();
    $like = database_quote(sprintf('%%%s%%', $code));
    $likeL = database_quote(sprintf('%s%%', $code));
    $codeQ = database_quote($code);
    $stmt = database_prepare(
        "SELECT * FROM {$table} 
            WHERE site_id={$siteId} 
              AND code LIKE {$like}
            ORDER BY IF(code = {$codeQ}, 2, IF(code LIKE {$likeL},1,0)) DESC LIMIT {$limit}
    "
    );
    $data = [];
    if ($stmt->execute([trim($code)])) {
        while($res = $stmt->fetchAssoc()) {
            $id = (int)$res['id'];
            $res['site_id'] = (int)($res['site_id'] ?? null);
            $res['id'] = (int)($res['id'] ?? null);
            $res['code'] = trim((string)($res['code'] ?? null));
            cache_set(
                sprintf('%d(%s)', $res['site_id'], $res['code']),
                $res,
                'classes_by_code'
            );
            cache_set(
                sprintf('%d(%s)', $res['site_id'], $res['name']),
                $res,
                'classes_by_name'
            );
            cache_set(
                $id,
                $res,
                'classes'
            );
            $data[] = $res;
        }
        $stmt->closeCursor();
    }

    unset($stmt);

    return $data;
}

/**
 * @param int $classId
 * @param array $classes
 * @return bool|int
 */
function update_class_data(int $classId, array $classes)
{
    $data = get_class_by_id($classId);
    if (!$data || !is_array($data)) {
        return false;
    }

    $data['site_id'] = (int) $data['site_id'];
    $siteId = $data['site_id'];
    if (!$siteId || $siteId < 1) {
        return false;
    }

    $classes = [
        'name' => $classes['name']??null,
        'code' => $classes['code']??null,
        'note' => $classes['note']??null,
    ];


    $classes['code'] = !is_string($classes['code']) ? null : trim($classes['code']);
    $classes['code'] = $classes['code'] ? trim($classes['code']) : null;
    if ($classes['code'] === null) {
        unset($classes['code']);
    } else {
        $before = get_class_by_code($classes['code']);
        if ($before && (int) $before['id'] <> $classId) {
            return -2;
        }
    }

    $classes['name'] = !is_string($classes['name']) ? null : trim($classes['name']);
    $classes['name'] = $classes['name'] ? trim($classes['name']) : null;
    if ($classes['name'] === null) {
        unset($classes['name']);
    } else {
        $before = get_class_by_name($classes['name']);
        if ($before && (int) $before['id'] <> $classId) {
            return -1;
        }
    }


    $classes['note'] = !is_string($classes['note']) ? null : trim($classes['note']);
    $classes['note'] = $classes['note'] !== null ? trim($classes['note']) : null;
    if ($classes['note'] === null) {
        unset($classes['note']);
    }

    if (empty($classes)) {
        return 1;
    }

    foreach ($classes as $item => $v) {
        if (isset($data[$item]) && $data[$item] === $v) {
            unset($classes[$item]);
        }
    }

    if (empty($classes)) {
        return 1;
    }

    $table = get_classes_table_name();
    $newClass = [];
    $args = [];
    foreach ($classes as $key => $item) {
        $keyName = ":_{$key}";
        $newClass[$key] = " {$key}={$keyName}";
        $args[$keyName] = $item;
        $data[$key] = $item;
    }

    $set = implode(', ', $newClass);
    $sql = "UPDATE {$table} SET {$set} WHERE id={$classId}";
    try {
        $stmt = database_prepare($sql);
        if (($res = $stmt->execute($args))) {
            $stmt->closeCursor();
            cache_set($classId, $data, 'classes');
            cache_set(
                sprintf('%d(%s)', $data['site_id'], trim($data['name'])),
                $res,
                'classes_by_name'
            );
            cache_set(
                sprintf('%d(%s)', $data['site_id'], trim($data['code'])),
                $res,
                'classes_by_code'
            );
        }

    } catch (Exception $e) {
        return false;
    }

    return (bool) $res;
}

/**
 * @param array $classes
 * @return array|false|int
 */
function insert_class_data(array $classes)
{
    $site_id = get_current_site_id();
    if (is_super_admin()) {
        if (isset($classes['site_id']) && is_numeric($classes['site_id'])) {
            $classes['site_id'] = abs($classes['site_id']);
            if (is_int($classes['site_id'])) {
                $exists = get_site_by_id($classes['site_id']);
                if ($exists) {
                    $site_id = $exists->getId();
                }
            }
        }
    }

    $code = $classes['code']??null;
    $code = !is_string($code) ? null : trim($code);
    $code = $code ?: null;
    if (!$code) {
        return -4;
    }
    $name = $classes['name']??null;
    $name = !is_string($name) ? null : trim($name);
    $name = $name ?: null;
    if (!$name) {
        return -3;
    }

    $classes = [
        'name' => $name,
        'code' => $code,
        'note' => $classes['note']??null,
        'site_id' => $site_id,
    ];

    $classes['note'] = !is_string($classes['note']) ? null : trim($classes['note']);
    $classes['note'] = $classes['note'] ? trim($classes['note']) : '';

    $before = get_class_by_code($classes['code'], $site_id);
    if (!empty($before)) {
        return -2;
    }

    $before = get_class_by_name($classes['name'], $site_id);
    if (!empty($before)) {
        return -1;
    }

    $table = get_classes_table_name();
    $args     = [];
    foreach ($classes as $key => $item) {
        $keyName = ":_{$key}";
        $args[$keyName] = $item;
    }

    $columns = implode(', ', array_keys($classes));
    $val = implode(', ', array_keys($args));
    $sql = "INSERT INTO {$table} ({$columns}) VALUE({$val})";
    try {
        $stmt = database_prepare($sql);
        if (($res = $stmt->execute($args))) {
            $stmt->closeCursor();
            $keyCode = sprintf('%d(%s)', $site_id, $code);
            $keyName = sprintf('%d(%s)', $site_id, $name);
            cache_delete($keyCode, 'classes_by_code');
            cache_delete($keyName, 'classes_by_name');
            return get_class_by_code($code, $site_id);
        }

    } catch (Exception $e) {
        return false;
    }

    return false;
}

/*
function delete_class_data(int $classId)
{
}*/
