<?php
/**
 * @return string
 */
function get_classes_table_name() : string
{
    return 'sto_classes';
}

function get_classes_teacher_table_name() : string
{
    return 'sto_classes_teacher';
}


/**
 * @param array $data
 * @return array
 */
function get_classes_data_filters(array $data) : array
{
    $ids = [];
    foreach ($data as &$item) {
        $ids[$item['id']] = true;
        if (isset($item['site_id']) && is_numeric($item['site_id'])) {
            $item['site_id'] = (int) $item['site_id'];
        }
    }
    if (empty($data)) {
        return $data;
    }

    $implodedIds = implode(',', $ids);
    $where = count($ids) === 1
        ? " class_id={$implodedIds} "
        : "class_id IN ({$implodedIds})";

    $teacher_table = get_classes_teacher_table_name();
    $site_table = site()->getTableName();
    $supervisor_table = supervisor()->getTableName();
    $sql = "
SELECT
   sto_classes_teacher.class_id as class_id,
   ss.id as id,
   ss.full_name as name,
   sto_classes_teacher.year as teach_year,
   ss.site_id as site_id,
   site.name as site_name
FROM {$teacher_table}
    LEFT JOIN {$supervisor_table} ss on sto_classes_teacher.teacher = ss.id
    LEFT JOIN {$site_table} site on ss.site_id = site.id
WHERE {$where}
";

    $stmt = database_unbuffered_query($sql);
    while ($row = $stmt->fetchAssoc()) {
        $id = $row['class_id'];
        unset($row['class_id']);
        if (!isset($data[$id])) {
            continue;
        }

        $row['id'] = (int) $row['id'];
        if (isset($row['site_id']) && is_numeric($row['site_id'])) {
            $row['site_id'] = (int)$row['site_id'];
        }

        $data[$id]['teachers'][] = $row;
    }

    foreach ($data as $classId => $item) {
        cache_set_current(
            trim($item['code']),
            $item,
            'classes_code',
            $item['site_id']
        );
        cache_set_current(
            trim($item['name']),
            $item,
            'classes_name',
            $item['site_id']
        );
        cache_set(
            $classId,
            $item,
            'classes'
        );
    }

    $stmt->closeCursor();
    return $data;
}

/**
 * @param int|null $limit
 * @param int|null $offset
 * @param int[] $siteIds
 * @return array[]
 */
function get_classes_data(
    int $limit = null,
    int $offset = null,
    array $siteIds = null
) : array {

    $siteIds = get_generate_site_ids($siteIds);
    $table = get_classes_table_name();
    $where = 'site_id ';
    $where .= !empty($siteIds)
        ? sprintf(count($siteIds) === 1 ? ' = %d ' : 'IN (%s) ', implode(',', $siteIds))
        : 'IS NOT NULL AND site_id > 0 ';
    $sql = "SELECT *, count(id) OVER() as total FROM {$table} WHERE ";
    $limit = $limit < 1 ? MYSQL_MAX_RESULT_LIMIT : $limit;
    $offset = $offset > 0 ? $offset : 0;
    $sqlLimit = "LIMIT {$limit}";
    if ($offset > 0) {
        $sqlLimit .= " OFFSET {$offset}";
    }

    $sql .= $where;
    $sql .= $sqlLimit;
    $stmt = database_unbuffered_query($sql);
    $data = [
        'total'  => 0,
        'count' => 0,
        'meta' => [
            'page' => [
                'total' => 0,
                'current' => 0,
            ],
            'next' => [
                'offset' => $offset,
                'limit' => $limit,
                'total' => 0
            ],
            'query' => [
                'limit' => $limit,
                'offset' => $offset,
                'site_id' => $siteIds,
            ],
        ],
        'results' => []
    ];

    $result = [];
    $total = 0;
    while ($row = $stmt->fetchAssoc()) {
        $row['teachers'] = [];
        $id = (int) $row['id'];
        if ($total === 0) {
            $total = (int) $row['total'];
        }
        unset($row['total']);
        $row['id'] = (int) $row['id'];
        $result[$id] = $row;
    }

    $stmt->closeCursor();
    if ($total === 0) {
        $sql = "SELECT count(id) as total FROM {$table} WHERE ";
        $sql .= $where;
        try {
            $stmt = database_unbuffered_query_execute($sql);
            if ($stmt) {
                $total = (int) ($stmt->fetchClose(PDO::FETCH_ASSOC)['total']??$total);
            }
        } catch (Exception $e) {
            // passs
        }
    }

    if (!empty($result)) {
        $result = get_classes_data_filters($result);
        $data['results'] = array_values($result);
    }

    $data['count'] = count($result);
    if ($data['count'] > $total) {
        $total = $data['count'];
    }

    $calc = calculate_page_query(
        $limit,
        $offset,
        $total,
        $data['count']
    );

    $data['total'] = $calc['total'];
    $meta =& $data['meta'];
    $meta['page']['total'] = $calc['total_page'];
    $meta['page']['current'] = $calc['current_page'];
    $meta['next']['offset'] = $calc['next_offset'];
    $meta['next']['limit'] = $calc['next_limit'];
    $meta['next']['total'] = $calc['next_total'];
    return $data;
}

/**
 * @param int $id
 * @return array
 */
function get_classes_by_id(int $id) : array
{
    $cache = cache_get($id, 'classes', $found);
    if ($found && ($cache === false || is_array($cache))) {
        return $cache ? [$cache] : [];
    }

    $table = get_classes_table_name();
    $sql = "SELECT * FROM {$table} WHERE id={$id} LIMIT 1";
    $stmt = database_query_execute($sql);
    $data = [];
    if ($stmt) {
        while ($row = $stmt->fetchAssoc()) {
            $row['teachers'] = [];
            $id = (int) $row['id'];
            $row['id'] = (int) $row['id'];
            $data[$id] = $row;
        }
    }

    $data = get_classes_data_filters($data);
    return array_values($data);
}

/**
 * @param int $id
 * @return false|array
 */
function get_class_by_id(int $id)
{
    $result = get_classes_by_id($id);
    return $result[0]??false;
}

/**
 * @param string $name
 * @param int[] $siteId
 * @return array
 */
function get_classes_by_name(string $name, array $siteId = null) : array
{
    $name = trim($name);
    $siteIds = get_generate_site_ids($siteId);
    if (count($siteIds) === 1) {
        $siteId = reset($siteIds);
        $cache = cache_get_current(
            $name,
            'classes_name',
            $found,
            $siteId
        );
        if ($found && ($cache === false || is_array($cache))) {
            return $cache ? [$cache] : [];
        }
    }

    $table = get_classes_table_name();
    if (!empty($siteIds)) {
        $siteIdWhere = sprintf(
            count($siteIds) === 1 ? ' =%d ' : ' IN (%s) ',
            implode(',', $siteIds)
        );
    } else {
        $siteIdWhere = ' IS NOT NULL AND site_id > 0 ';
    }

    $sql = " SELECT * FROM {$table} WHERE name=? AND site_id{$siteIdWhere}";
    $stmt = database_prepare_execute($sql, [$name]);
    $data = [];
    if ($stmt) {
        while ($row = $stmt->fetchAssoc()) {
            $row['id'] = (int) $row['id'];
            $row['teachers'] = [];
            $data[$row['id']] = $row;
        }
    }

    $data = get_classes_data_filters($data);
    return array_values($data);
}

/**
 * @param string $name
 * @param int|null $siteIds
 * @return array|false|mixed
 */
function get_class_by_name(string $name, int $siteIds = null)
{
    $siteIds = $siteIds??get_current_site_id();
    $result = get_classes_by_name($name, [$siteIds]);
    return $result[0]??false;
}

/**
 * @param string $code
 * @param array|null $siteIds
 * @return array|array[]
 */
function get_classes_by_code(string $code, array $siteIds = null) : array
{
    $siteIds = get_generate_site_ids($siteIds);

    $code = trim($code);
    if (count($siteIds) === 1) {
        $siteId = reset($siteIds);
        $cache = cache_get_current(
            $code,
            'classes_code',
            $found,
            $siteId
        );

        if ($found && ($cache === false || is_array($cache))) {
            return $cache ? [$cache] : [];
        }
    }

    $table = get_classes_table_name();
    if (!empty($siteIds)) {
        $siteIdWhere = sprintf(
            count($siteIds) === 1 ? ' =%d ' : ' IN (%s) ',
            implode(',', $siteIds)
        );
    } else {
        $siteIdWhere = ' IS NOT NULL AND site_id > 0 ';
    }

    $sql = " SELECT * FROM {$table} WHERE code=? AND site_id{$siteIdWhere}";
    $stmt = database_prepare_execute($sql, [$code]);
    $data = [];
    if ($stmt) {
        while ($row = $stmt->fetchAssoc()) {
            $row['id'] = (int) $row['id'];
            $row['teachers'] = [];
            $data[$row['id']] = $row;
        }
    }

    $data = get_classes_data_filters($data);
    return array_values($data);
}

/**
 * @param string $code
 * @param int|null $siteIds
 * @return array[]|false
 */
function get_class_by_code(string $code, int $siteIds = null)
{
    $siteIds = $siteIds??get_current_site_id();
    $result = get_classes_by_code($code, [$siteIds]);
    return $result[0]?:false;
}

/**
 * @param string $type
 * @param string $name
 * @param array|null $siteIds
 * @param int $limit
 * @param int $offset
 * @param null $result
 * @return array|false
 */
function search_classes_by(
    string $type,
    string $name,
    array $siteIds = null,
    int $limit = MYSQL_DEFAULT_SEARCH_LIMIT,
    int $offset = 0,
    &$result = null
) {
    $type = trim(strtolower($type));
    if ($type !== 'name' && $type !== 'code') {
        return false;
    }

    $siteIds = get_generate_site_ids($siteIds);
    $limit  = get_generate_max_search_result_limit($limit);
    $offset = get_generate_min_offset($offset);

    $name = trim($name);
    $table = get_classes_table_name();

    $likeSearch = database_quote(sprintf('%%%s%%', $name));
    $likeLeft = database_quote(sprintf('%s%%', $name));
    $nameQuery = database_quote($name);

    if (!empty($siteIds)) {
        $siteIdWhere = sprintf(
            count($siteIds) === 1 ? ' =%d ' : ' IN (%s) ',
            implode(',', $siteIds)
        );
    } else {
        $siteIdWhere = ' IS NOT NULL AND site_id > 0 ';
    }

    $data = [
        'total'  => 0,
        'count' => 0,
        'meta' => [
            'page' => [
                'total' => 0,
                'current' => 0,
            ],
            'next' => [
                'offset' => $offset,
                'limit' => $limit,
                'total' => 0
            ],
            'query' => [
                'limit' => $limit,
                'offset' => $offset,
                'site_id' => $siteIds,
            ],
        ],
        'results' => []
    ];

    $stmt = database_prepare_execute(
        "
        SELECT count(class.id) as total FROM {$table} as class
            WHERE class.site_id {$siteIdWhere} AND class.name LIKE {$likeSearch}
                ORDER BY
                    IF(class.name = {$nameQuery}, 2, IF(class.name LIKE {$likeLeft},1,0)) 
    "
    );

    $res = $stmt ? $stmt->fetchClose(PDO::FETCH_ASSOC) : false;
    $total = $res ? abs($res['total']??0) : 0;
    unset($res);

    $stmt = database_unbuffered_query_execute(
        "
SELECT *
    FROM {$table} as class
        WHERE class.site_id {$siteIdWhere} AND class.{$type} LIKE {$likeSearch}
        ORDER BY 
             IF(class.{$type} = {$nameQuery}, 2, IF(class.{$type} LIKE {$likeLeft},1,0))
                DESC LIMIT {$limit} OFFSET {$offset}
    "
    );

    $result = [];
    while ($row = $stmt->fetchAssoc()) {
        $row['teachers'] = [];
        $id = (int) $row['id'];
        unset($row['total']);
        $row['id'] = (int) $row['id'];
        $result[$id] = $row;
    }

    $stmt->closeCursor();

    if (!empty($result)) {
        $result = get_classes_data_filters($result);
        $data['results'] = array_values($result);
    }

    $data['count'] = count($result);
    if ($data['count'] > $total) {
        $total = $data['count'];
    }

    $calc = calculate_page_query(
        $limit,
        $offset,
        $total,
        $data['count']
    );
    $data['total'] = $calc['total'];
    $meta =& $data['meta'];
    $meta['page']['total'] = $calc['total_page'];
    $meta['page']['current'] = $calc['current_page'];
    $meta['next']['offset'] = $calc['next_offset'];
    $meta['next']['limit'] = $calc['next_limit'];
    $meta['next']['total'] = $calc['next_total'];
    return $data;
}

/**
 * @param string $name
 * @param int[] $siteIds
 * @param int $limit
 * @param int $offset
 * @param $result
 * @return array
 */
function search_class_by_name(
    string $name,
    array $siteIds = null,
    int $limit = MYSQL_DEFAULT_SEARCH_LIMIT,
    int $offset = 0,
    &$result = null
) : array {
    return search_classes_by('name', $name, $siteIds, $limit, $offset, $result);
}

/**
 * @param string $code
 * @param array|null $siteIds
 * @param int $limit
 * @param int $offset
 * @param null $result
 * @return array
 */
function search_class_by_code(
    string $code,
    array $siteIds = null,
    int $limit = MYSQL_DEFAULT_SEARCH_LIMIT,
    int $offset = 0,
    &$result = null
) : array {
    return search_classes_by('code', $code, $siteIds, $limit, $offset, $result);
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
        $before = get_class_by_code($classes['code'], $siteId);
        if ($before && ((int) $before['id']) <> $classId) {
            return -2;
        }
    }

    $classes['name'] = !is_string($classes['name']) ? null : trim($classes['name']);
    $classes['name'] = $classes['name'] ? trim($classes['name']) : null;
    if ($classes['name'] === null) {
        unset($classes['name']);
    } else {
        $before = get_class_by_name($classes['name'], $siteId);
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

    $res = false;
    $set = implode(', ', $newClass);
    $sql = "UPDATE {$table} SET {$set} WHERE id={$classId}";
    try {
        $stmt = database_prepare_execute($sql, $args);
        if ($stmt) {
            cache_delete($classId, 'classes');
            get_class_by_id($classId);
            $res = true;
        }
        $stmt && $stmt->closeCursor();
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
        $stmt = database_prepare_execute($sql);
        if (($stmt)) {
            $stmt->closeCursor();
            cache_delete_current($code, 'classes_code', $site_id);
            cache_delete_current($name, 'classes_name', $site_id);
            return get_class_by_code($code, [$site_id]);
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
