<?php

use ArrayIterator\Helper\StringFilter;
use ArrayIterator\Model\AbstractOnlineModel;
use ArrayIterator\Model\AbstractUserModel;
use ArrayIterator\Model\Student;
use ArrayIterator\Model\Supervisor;
use ArrayIterator\User;

/**
 * @return string
 */
function get_student_table_name() : string
{
    return student()->getTableName();
}

/**
 * @return string
 */
function get_supervisor_table_name() : string
{
    return supervisor()->getTableName();
}

/**
 * @return string
 */
function get_student_online_table_name() : string
{
    return student_online()->getTableName();
}

/**
 * @return string
 */
function get_supervisor_online_table_name() : string
{
    return supervisor_online()->getTableName();
}

/**
 * @return mixed|User
 * @noinspection PhpMissingReturnTypeInspection
 */
function &student_global()
{
    static $student = null;

    return $student;
}

/**
 * @return mixed|User
 * @noinspection PhpMissingReturnTypeInspection
 */
function &supervisor_global()
{
    static $supervisor = null;
    return $supervisor;
}

/**
 * @return string|false|null
 */
function get_cookie_student_data()
{
    $cookie = cookie(COOKIE_STUDENT_NAME);
    if ($cookie === null) {
        $cookie = null;
    } elseif (!is_string($cookie)) {
        $cookie = false;
    }

    return hook_apply('cookie_student_data', $cookie);
}

/**
 * @return false|string|null
 */
function get_cookie_supervisor_data()
{
    $cookie = cookie(COOKIE_SUPERVISOR_NAME);
    if ($cookie === null) {
        $cookie = null;
    } elseif (!is_string($cookie)) {
        $cookie = false;
    }

    return hook_apply('cookie_supervisor_data', $cookie);
}

/**
 * @return User|false
 */
function get_current_student_data()
{
    $student =& student_global();
    if ($student === false || $student instanceof User) {
        return $student;
    }

    $student = false;
    $cookies = get_cookie_student_data();
    if ($cookies && is_string($cookies)) {
        $cookies = base64_decode($cookies);
        if (StringFilter::isBinary($cookies)) {
            return false;
        }
        $cookies = $cookies ? validate_json_hash($cookies) : false;
        if (!is_array($cookies)
            || !isset(
                $cookies['user_id'],
                $cookies['site_id'],
                $cookies['type'],
                $cookies['hash'],
                $cookies['hash_type']
            )
            || $cookies['type'] !== STUDENT
            || !is_int($cookies['user_id'])
            || !is_int($cookies['site_id'])
            || !($data = student()->findOneById($cookies['user_id'])->fetchClose())
            || ($data->getSiteId() ?? $cookies['site_id']) !== $cookies['site_id']
        ) {
            return false;
        }

        if (hook_apply('allow_get_current_student_data_different_site_id', false) !== true) {
            // check if site id is not 1
            if ($data->getSiteId() !== 1) {
                if (!enable_multisite()) {
                    return false;
                }
            }

            if ($data->getSiteId() !== get_current_site_id()) {
                return false;
            }
        }

        if (hook_apply('set_student_online', true) === true) {
            student_online()->setOnline($data);
        }

        $student = new User(
            $cookies['user_id'],
            $cookies['site_id'],
            $cookies['uuid'],
            $cookies['type'],
            $cookies['hash'],
            $cookies['hash_type'],
            $data
        );
        /*
        $student = [
            'user_id' => $cookies['user_id'],
            'site_id' => $cookies['site_id'],
            'uuid' => $cookies['uuid'],
            'type' => $cookies['type'],
            'hash' => $cookies['hash'],
            'hash_type' => $cookies['hash_type'],
            'user' => $data,
        ];*/
    }

    return $student;
}

/**
 * @return User|false
 */
function get_current_supervisor_data()
{
    $supervisor =& supervisor_global();
    if ($supervisor === false || $supervisor instanceof User) {
        return $supervisor;
    }

    $supervisor = false;
    $cookies = get_cookie_supervisor_data();
    if (is_string($cookies)) {
        $cookies = base64_decode($cookies);
        if (StringFilter::isBinary($cookies)) {
            return false;
        }
        if (!is_array(($cookies = $cookies ? validate_json_hash($cookies) : false))
            || !isset(
                $cookies['user_id'],
                $cookies['site_id'],
                $cookies['type'],
                $cookies['hash'],
                $cookies['hash_type']
            )
            || $cookies['type'] !== SUPERVISOR
            || !is_int($cookies['user_id'])
            || !is_int($cookies['site_id'])
            || !($data = \supervisor()->findOneById($cookies['user_id'])->fetchClose())
            || $data->getSiteId() !== $cookies['site_id']
        ) {
            return false;
        }
        if (hook_apply('allow_get_current_supervisor_data_different_site_id', false) !== true) {
            // check if site id is not 1
            if ($data->getSiteId() !== 1) {
                if (!enable_multisite()) {
                    return false;
                }
            }

            if ($data->getSiteId() !== get_current_site_id()) {
                return false;
            }
        }

        if (hook_apply('set_supervisor_online', true) === true) {
            supervisor_online()->setOnline($data);
        }
        $supervisor = new User(
            $cookies['user_id'],
            $cookies['site_id'],
            $cookies['uuid'],
            $cookies['type'],
            $cookies['hash'],
            $cookies['hash_type'],
            $data
        );
        /*
        $supervisor = [
            'user_id' => $cookies['user_id'],
            'site_id' => $cookies['site_id'],
            'uuid' => $cookies['uuid'],
            'type' => $cookies['type'],
            'hash' => $cookies['hash'],
            'hash_type' => $cookies['hash_type'],
            'user' => $data,
        ];*/
    }

    return $supervisor;
}

/**
 * @return Supervisor|false
 */
function get_current_supervisor()
{
    $supervisor = get_current_supervisor_data();
    return $supervisor ? $supervisor->getUser() : false;
}

/**
 * @return Student|false
 */
function get_current_student()
{
    $student = get_current_student_data();
    return $student ? $student->getUser() : false;
}

/**
 * @param int $id
 * @return false|Student
 */
function get_student_by_id(int $id)
{
    $key = $id;
    $user = cache_get($key, 'students', $found);
    if ($found && ($user === false || $found instanceof Student)) {
        return $user;
    }
    cache_set($key, false, 'students');
    $res = student()->findById($id);
    if ($res) {
        /**
         * @var Student
         */
        $user = $res->fetch();
        $res->closeCursor();
        cache_set($key, $user, 'students');
        if ($user) {
            $key = trim(strtolower($user->get('username')));
            cache_set($key, $user, 'students');
        }
        return $user;
    }

    return false;
}

/**
 * @param int $id
 * @return false|Supervisor
 */
function get_supervisor_by_id(int $id)
{
    $key = $id;
    $user = cache_get($key, 'supervisors', $found);
    if ($found && ($user === false || $found instanceof Supervisor)) {
        return $user;
    }
    cache_set($key, false, 'supervisors');
    $res = supervisor()->findById($id);
    if ($res) {
        $user = $res->fetch();
        $res->closeCursor();
        cache_set($key, $user, 'supervisors');
        if ($user) {
            $key = trim(strtolower($user->get('username')));
            cache_set($key, $user, 'supervisors');
        }
        return $user;
    }
    return false;
}

/**
 * @param string $username
 * @return false|AbstractUserModel
 */
function get_supervisor_by_username(string $username)
{
    if (trim($username) === '') {
        return false;
    }

    $key = trim(strtolower($username));
    $user = cache_get($key, 'supervisors', $found);
    if ($found && ($user === false || $found instanceof Student)) {
        return $user;
    }
    cache_set($key, false, 'supervisors');
    $res = supervisor()->findOneByUsername($username);
    if ($res) {
        $user = $res->fetch();
        cache_set($key, $user, 'supervisors');
        if ($user) {
            cache_set($user->getId(), $user, 'supervisors');
        }
        $res->closeCursor();
        return $user;
    }

    return false;
}

/**
 * @param string $username
 * @return false|AbstractUserModel
 */
function get_student_by_username(string $username)
{
    if (trim($username) === '') {
        return false;
    }
    $key = trim(strtolower($username));
    $user = cache_get($key, 'students', $found);
    if ($found && ($user === false || $found instanceof Student)) {
        return $user;
    }
    cache_set($key, false, 'students');
    $res = supervisor()->findOneByUsername($username);
    if ($res) {
        $user = $res->fetch();
        cache_set($key, $user, 'students');
        if ($user) {
            cache_set($user->getId(), $user, 'students');
        }
        $res->closeCursor();
        return $user;
    }

    return false;
}

/**
 * @return bool
 */
function is_supervisor(): bool
{
    return !!get_current_supervisor_data();
}

/**
 * @return bool
 */
function is_student(): bool
{
    return !!get_current_student_data();
}

/**
 * @return bool
 */
function is_allow_access_admin(): bool
{
    $superVisor = get_current_supervisor();
    return (bool)hook_apply(
        'allow_access_admin',
        $superVisor ? !$superVisor['disallow_admin'] : false,
        $superVisor
    );
}

/**
 * @return bool
 */
function is_allow_access_dashboard(): bool
{
    $superVisor = get_current_student();
    return (bool)hook_apply(
        'allow_access_dashboard',
        $superVisor ? !$superVisor['disallow_admin'] : false,
        $superVisor
    );
}

/**
 * Detect user logged in by dashboard area
 *
 * @return bool
 */
function is_login(): bool
{
    if (is_route_api()) {
        return is_supervisor() || is_student();
    }

    return is_admin_page() ? is_supervisor() : is_student();
}

/**
 * Check user if logged in, even student or supervisor
 * @return bool
 */
function is_user_logged(): bool
{
    return is_student() || is_supervisor();
}

/**
 * @return User|false
 */
function get_current_user_data()
{
    if (!is_login()) {
        return false;
    }

    return is_admin_page()
        ? get_current_supervisor_data()
        : get_current_student_data();
}

/**
 * @return int
 */
function get_current_user_id(): int
{
    $user = get_current_user_data();
    return $user ? $user['user_id'] : 0;
}

/**
 * @return int
 */
function get_current_user_site_id() : int
{
    $user = get_current_user_data();
    return $user ? $user['site_id'] : 0;
}

/**
 * @return false|string
 */
function get_current_user_type()
{
    $userData = get_current_user_data();
    return $userData ? $userData->getUser()->getUserRoleType() : false;
}

/**
 * @return false|string
 */
function get_current_user_role()
{
    $userData = get_current_user_data();
    return $userData ? ($userData->getUser()->get('role')??false) : false;
}

/**
 * @return false|string
 */
function get_current_user_status()
{
    $userData = get_current_user_data();
    return $userData ? ($userData->getUser()->get('status')??false) : false;
}

/**
 * @return false|string
 */
function get_current_supervisor_role()
{
    $userData = get_current_supervisor();
    return $userData ? ($userData['role'] ?? false) : false;
}

/**
 * @return false|string
 */
function get_current_supervisor_status()
{
    $userData = get_current_supervisor();
    return $userData ? ($userData['status'] ?? false) : false;
}

/**
 * @return false|string
 */
function get_current_supervisor_full_name()
{
    $userData = get_current_supervisor();
    return $userData ? ($userData['full_name'] ?? false) : false;
}

/**
 * @param AbstractUserModel $model
 * @param string $type
 * @param null $data
 * @return bool
 */
function insert_user_log(AbstractUserModel $model, string $type, $data = null) : bool
{
    try {
        $obj = $model->getObjectUserLog();
        return $obj->insertData($model, $type, $data);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * @param int $studentId
 * @return AbstractOnlineModel|false|mixed
 */
function get_student_online_status(int $studentId)
{
    $key = sprintf('student(%d)', $studentId);
    $cache = cache_get($key, 'users_online', $found);
    if ($found) {
        return $cache;
    }

    $data = student_online()->userOnline($studentId);
    cache_set($key, $data, 'users_online');
    return $data;
}
/**
 * @param int $supervisor
 * @return AbstractOnlineModel|false|mixed
 */
function get_supervisor_online_status(int $supervisor)
{
    $key = sprintf('supervisor(%d)', $supervisor);
    $cache = cache_get($key, 'users_online', $found);
    if ($found) {
        return $cache;
    }

    $data = supervisor_online()->userOnline($supervisor);
    cache_set($key, $data, 'users_online');
    return $data;
}

/**
 * @param int $studentId
 * @return bool
 */
function is_student_online(int $studentId) : bool
{
    $status = get_student_online_status($studentId);
    if (!$status) {
        return false;
    }
    return (bool) ($status['online']??null);
}

/**
 * @param int $supervisorId
 * @return bool
 */
function is_supervisor_online(int $supervisorId) : bool
{
    $status = get_supervisor_online_status($supervisorId);
    if (!$status) {
        return false;
    }
    return (bool) ($status['online']??null);
}


/**
 * @param array $data
 * @return array
 */
function get_users_data_filters(array $data) : array
{
    $ids = [];
    foreach ($data as $key => $item) {
        if (isset($item['site_id']) && is_numeric($item['site_id'])) {
            $siteId = (int) $item['site_id'];
            $data[$key]['site_id'] = $siteId;
            if (!isset($ids[$siteId])) {
                $ids[$siteId] = [];
            }
            $data[$key]['avatar'] = !is_string($item['avatar']) || trim($item['avatar']) !== ''
                ? get_avatar_url($item['avatar'])
                : null;
            $ids[$siteId][] = $item['id'];
        }
    }

    if (empty($data)) {
        return $data;
    }

    $implodedIds = implode(',', array_keys($ids));
    $where = count($ids) === 1
        ? " id={$implodedIds} "
        : "id IN ({$implodedIds})";
    $site_table = site()->getTableName();
    $sql = "SELECT * FROM {$site_table} WHERE {$where}";

    $stmt = database_unbuffered_query($sql);
    while ($row = $stmt->fetchAssoc()) {
        $row['id'] = (int) $row['id'];
        $siteId = $row['id'];
        if (!isset($ids[$siteId])) {
            continue;
        }
        foreach ($ids[$siteId] as $item) {
            if (!isset($data[$item])) {
                continue;
            }
            $data[$item]['site'] = [
                'name' => (string) $row['name'],
                'host' => $row['host'],
                'additional_host' => $row['additional_host'],
                'status' => $row['status'],
                'token' => $row['token'],
                'logo' => $row['logo'] ? get_logo_url($row['logo']) : null,
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ];

            $username = trim(strtolower($data[$item]['username']));
            $email = trim(strtolower($data[$item]['email']));
            $name = trim(($data[$item]['full_name']));
            cache_set_current(
                $username,
                $data[$item],
                'supervisors_list_username',
                $siteId
            );
            cache_set_current(
                $email,
                $data[$item],
                'supervisors_list_email',
                $siteId
            );
            cache_set_current(
                $name,
                $data[$item],
                'supervisors_list_name',
                $siteId
            );
            cache_set(
                $item,
                $data[$item],
                'supervisors_list_id'
            );
        }
    }

    $stmt->closeCursor();
    return $data;
}

/**
 * @param int|null $limit
 * @param int|null $offset
 * @param string|null $role
 * @param int[] $siteIds
 * @return array[]
 */
function get_supervisors_data(
    int $limit = null,
    int $offset = null,
    string $role = null,
    array $siteIds = null
) : array {

    $siteIds = get_generate_site_ids($siteIds);
    $limit  = get_generate_max_search_result_limit($limit);
    $table = supervisor()->getTableName();
    $where = "{$table}.site_id ";
    $where .= !empty($siteIds)
        ? sprintf(count($siteIds) === 1 ? ' = %d ' : 'IN (%s) ', implode(',', $siteIds))
        : "IS NOT NULL AND {$table}.site_id > 0 ";
    $role = $role ? strtolower(trim($role)) : null;
    $role = $role?:null;

    $is_super_admin = is_super_admin();
    $is_admin = is_admin();

    if (!$is_super_admin) {
        $where .= " AND LOWER(TRIM({$table}.role)) != '".ROLE_SUPER_ADMIN."' ";
    }
    if ($role && ($is_super_admin || $role !== ROLE_SUPER_ADMIN)) {
        $where .= " AND LOWER(TRIM({$table}.role))=".database_quote($role);
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
                'role' => $role
            ],
        ],
        'results' => []
    ];

    $canViewSupervisors = current_supervisor_can('view_supervisors');
    $canViewTeachers = current_supervisor_can('view_teachers');
    $canViewInvigilators = current_supervisor_can('view_invigilators');
    if (!$is_super_admin && $role === ROLE_SUPER_ADMIN
        || !$is_admin && ($role === ROLE_ADMIN || $role === ROLE_SUPER_ADMIN)
        || $role === ROLE_TEACHER && ! $canViewTeachers
        || $role === ROLE_INVIGILATOR && !$canViewInvigilators
        || !$role && !$canViewSupervisors
    ) {
        return $data;
    }

    $religion_table = get_religion_table_name();
    $sql = "
SELECT count({$table}.id) OVER() as total,
  {$table}.*,
	{$religion_table}.code as religion_code,
    {$religion_table}.name as religion_name
	FROM {$table} 
        LEFT JOIN {$religion_table} ON {$religion_table}.code = {$table}.religion
    WHERE ";

    $limit = $limit < 1 ? MYSQL_MAX_RESULT_LIMIT : $limit;
    $offset = $offset > 0 ? $offset : 0;
    $sqlLimit = "LIMIT {$limit}";
    if ($offset > 0) {
        $sqlLimit .= " OFFSET {$offset}";
    }

    $sql .= $where;
    $sql .= $sqlLimit;
    $stmt = database_unbuffered_query($sql);
    $result = [];
    $total = 0;
    while ($row = $stmt->fetchAssoc()) {
        $id = (int) $row['id'];
        if ($total === 0) {
            $total = (int) $row['total'];
        }
        $row['religion'] = [
            'code' => $row['religion_code']?:null,
            'name' => $row['religion_name']?:null,
        ];
        $row['disallow_admin'] = $row['disallow_admin'] == '1';
        unset($row['total'], $row['religion_code'], $row['religion_name']);
        $row['id'] = (int) $row['id'];
        $row['site_id'] = (int) $row['site_id'];
        $result[$id] = $row;
    }

    $stmt->closeCursor();
    if ($total === 0) {
        $sql = "SELECT count({$table}.id) as total FROM {$table} WHERE ";
        $sql .= $where;
        try {
            $stmt = database_unbuffered_query_execute($sql);
            if ($stmt) {
                $total = (int) ($stmt->fetchClose(PDO::FETCH_ASSOC)['total']??$total);
            }
        } catch (Exception $e) {
            // pass
        }
    }

    if (!empty($result)) {
        $result = get_users_data_filters($result);
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
function get_supervisors_list_by_id(int $id) : array
{
    $cache = cache_get(
        $id,
        'supervisors_list_id',
        $found
    );

    if ($found && ($cache === false || is_array($cache))) {
        return $cache ? [$cache] : [];
    }

    unset($cache);
    $table = supervisor()->getTableName();
    $religion_table = get_religion_table_name();
    $sql = "
SELECT
  {$table}.*,
	{$religion_table}.code as religion_code,
    {$religion_table}.name as religion_name
	FROM {$table} 
        LEFT JOIN {$religion_table} ON {$religion_table}.code = {$table}.religion
    WHERE {$table}.id={$id} LIMIT 1";

    $result = [];
    $stmt = database_query($sql);
    while ($row = $stmt->fetchAssoc()) {
        $id = (int) $row['id'];
        $row['religion'] = [
            'code' => $row['religion_code']?:null,
            'name' => $row['religion_name']?:null,
        ];
        $row['disallow_admin'] = $row['disallow_admin'] == '1';
        unset($row['total'], $row['religion_code'], $row['religion_name']);
        $row['id'] = (int) $row['id'];
        $row['site_id'] = (int) $row['site_id'];
        $result[$id] = $row;
    }

    $stmt->closeCursor();

    $result = get_users_data_filters($result);
    return array_values($result);
}

/**
 * @param string $name
 * @param array|int[]|null $siteId
 * @return array|array[]
 */
function get_supervisors_list_by_name(string $name, array $siteId = null) : array
{
    if (trim($name) === '') {
        return [];
    }

    $name = trim($name);
    $siteIds = get_generate_site_ids($siteId);
    if (count($siteIds) === 1) {
        $siteId = reset($siteIds);
        $cache = cache_get_current(
            $name,
            'supervisors_list_name',
            $found,
            $siteId
        );
        if ($found && ($cache === false || is_array($cache))) {
            return $cache ? [$cache] : [];
        }
    }

    $siteIdWhere = '';
    if (!empty($siteIds)) {
        $siteIdWhere = sprintf(
            count($siteIds) === 1 ? ' =%d ' : ' IN (%s) ',
            implode(',', $siteIds)
        );
    }

    $table = supervisor()->getTableName();
    $religion_table = get_religion_table_name();
    $name = database_quote(trim(strtolower($name)));
    if ($siteIdWhere !== '') {
        $siteIdWhere = " {$table}.site_id {$siteIdWhere} ";
    }
    $sql = "
SELECT
  {$table}.*,
	{$religion_table}.code as religion_code,
    {$religion_table}.name as religion_name
	FROM {$table} 
        LEFT JOIN {$religion_table} ON {$religion_table}.code = {$table}.religion
    WHERE LOWER({$table}.full_name)={$name} {$siteIdWhere}";

    $result = [];
    $stmt = database_query($sql);
    while ($row = $stmt->fetchAssoc()) {
        $id = (int) $row['id'];
        $row['religion'] = [
            'code' => $row['religion_code']?:null,
            'name' => $row['religion_name']?:null,
        ];
        $row['disallow_admin'] = $row['disallow_admin'] == '1';
        unset($row['total'], $row['religion_code'], $row['religion_name']);
        $row['id'] = (int) $row['id'];
        $row['site_id'] = (int) $row['site_id'];
        $result[$id] = $row;
    }

    $stmt->closeCursor();
    $result = get_users_data_filters($result);
    return array_values($result);
}

/**
 * @param string $username
 * @param array|int[]|null $siteId
 * @return array|array[]
 */
function get_supervisors_list_by_username(string $username, array $siteId = null) : array
{
    if (trim($username) === '') {
        return [];
    }

    $username = trim(strtolower($username));
    $siteIds = get_generate_site_ids($siteId);

    if (count($siteIds) === 1) {
        $siteId = reset($siteIds);
        $cache = cache_get_current(
            $username,
            'supervisors_list_username',
            $found,
            $siteId
        );
        if ($found && ($cache === false || is_array($cache))) {
            return $cache ? [$cache] : [];
        }
    }

    $siteIdWhere = '';
    if (!empty($siteIds)) {
        $siteIdWhere = sprintf(
            count($siteIds) === 1 ? ' =%d ' : ' IN (%s) ',
            implode(',', $siteIds)
        );
    }

    $table = supervisor()->getTableName();
    $religion_table = get_religion_table_name();
    $username = database_quote(trim(strtolower($username)));
    if ($siteIdWhere !== '') {
        $siteIdWhere = " {$table}.site_id {$siteIdWhere} ";
    }
    $sql = "
SELECT
  {$table}.*,
	{$religion_table}.code as religion_code,
    {$religion_table}.name as religion_name
	FROM {$table} 
        LEFT JOIN {$religion_table} ON {$religion_table}.code = {$table}.religion
    WHERE LOWER({$table}.username)={$username} {$siteIdWhere}";

    $result = [];
    $stmt = database_query($sql);
    while ($row = $stmt->fetchAssoc()) {
        $id = (int) $row['id'];
        $row['religion'] = [
            'code' => $row['religion_code']?:null,
            'name' => $row['religion_name']?:null,
        ];
        $row['disallow_admin'] = $row['disallow_admin'] == '1';
        unset($row['total'], $row['religion_code'], $row['religion_name']);
        $row['id'] = (int) $row['id'];
        $row['site_id'] = (int) $row['site_id'];
        $result[$id] = $row;
    }

    $stmt->closeCursor();
    $result = get_users_data_filters($result);
    return array_values($result);
}

/**
 * @param string $email
 * @param array|int[]|null $siteId
 * @return array|array[]
 */
function get_supervisors_list_by_email(string $email, array $siteId = null) : array
{
    if (trim($email) === '') {
        return [];
    }

    $email = trim(strtolower($email));
    $siteIds = get_generate_site_ids($siteId);
    if (count($siteIds) === 1) {
        $siteId = reset($siteIds);
        $cache = cache_get_current(
            $email,
            'supervisors_list_email',
            $found,
            $siteId
        );
        if ($found && ($cache === false || is_array($cache))) {
            return $cache ? [$cache] : [];
        }
    }

    $siteIdWhere = '';
    if (!empty($siteIds)) {
        $siteIdWhere = sprintf(
            count($siteIds) === 1 ? ' =%d ' : ' IN (%s) ',
            implode(',', $siteIds)
        );
    }

    $table = supervisor()->getTableName();
    $religion_table = get_religion_table_name();
    $email = database_quote(trim(strtolower($email)));
    if ($siteIdWhere !== '') {
        $siteIdWhere = " {$table}.site_id {$siteIdWhere} ";
    }
    $sql = "
SELECT
  {$table}.*,
	{$religion_table}.code as religion_code,
    {$religion_table}.name as religion_name
	FROM {$table} 
        LEFT JOIN {$religion_table} ON {$religion_table}.code = {$table}.religion
    WHERE LOWER(TRIM({$table}.email))={$email} {$siteIdWhere}";
    $result = [];
    $stmt = database_query($sql);
    while ($row = $stmt->fetchAssoc()) {
        $id = (int) $row['id'];
        $row['religion'] = [
            'code' => $row['religion_code']?:null,
            'name' => $row['religion_name']?:null,
        ];
        $row['disallow_admin'] = $row['disallow_admin'] == '1';
        unset($row['total'], $row['religion_code'], $row['religion_name']);
        $row['id'] = (int) $row['id'];
        $row['site_id'] = (int) $row['site_id'];
        $result[$id] = $row;
    }

    $stmt->closeCursor();

    $result = get_users_data_filters($result);
    return array_values($result);
}

/**
 * @param string $type
 * @param string $name
 * @param array|null $siteIds
 * @param string|null $role
 * @param int $limit
 * @param int $offset
 * @param null $result
 * @return array|false
 */
function search_supervisors_list_by(
    string $type,
    string $name,
    array $siteIds = null,
    string $role = null,
    int $limit = MYSQL_DEFAULT_SEARCH_LIMIT,
    int $offset = 0,
    &$result = null
) {
    $type = trim(strtolower($type));
    if ($type === 'name') {
        $type = 'full_name';
    }

    if ($type !== 'username' && $type !== 'email' && $type !== 'full_name') {
        return false;
    }

    $siteIds = get_generate_site_ids($siteIds);
    $limit  = get_generate_max_search_result_limit($limit);

    $offset = get_generate_min_offset($offset);

    $name = trim($name);
    $table = supervisor()->getTableName();
    $likeSearch = database_quote_like_all($name);
    $likeLeft = database_quote_like_left($name);
    $nameQuery = database_quote($name);

    if (!empty($siteIds)) {
        $siteIdWhere = sprintf(
            count($siteIds) === 1 ? ' =%d ' : ' IN (%s) ',
            implode(',', $siteIds)
        );
    } else {
        $siteIdWhere = " IS NOT NULL AND {$table}.site_id > 0 ";
    }

    $role = $role ? strtolower(trim($role)) : null;
    $role = $role?:null;
    $roleWhere = '';

    $is_super_admin = is_super_admin();
    $is_admin = is_admin();

    if (!$is_super_admin) {
        $roleWhere = " AND LOWER(TRIM({$table}.role)) != '".ROLE_SUPER_ADMIN."' ";
    }
    if ($role && ($is_super_admin || $role !== ROLE_SUPER_ADMIN)) {
        $roleWhere = " AND LOWER(TRIM({$table}.role))=".database_quote($role);
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

    $canViewSupervisors = current_supervisor_can('view_supervisors');
    $canViewTeachers = current_supervisor_can('view_teachers');
    $canViewInvigilators = current_supervisor_can('view_invigilators');
    if (!$is_super_admin && $role === ROLE_SUPER_ADMIN
        || !$is_admin && ($role === ROLE_ADMIN || $role === ROLE_SUPER_ADMIN)
        || $role === ROLE_TEACHER && ! $canViewTeachers
        || $role === ROLE_INVIGILATOR && !$canViewInvigilators
        || !$role && !$canViewSupervisors
    ) {
        return $data;
    }
    $sql = "
        SELECT count({$table}.id) as total FROM {$table}
            WHERE 
                  {$table}.site_id {$siteIdWhere}
                  {$roleWhere} 
                AND {$table}.{$type} LIKE {$likeSearch}
                ORDER BY
                    IF({$table}.{$type} = {$nameQuery}, 2, IF({$table}.{$type} LIKE {$likeLeft},1,0)) 
    ";
    $stmt = database_prepare_execute($sql);

    $res = $stmt ? $stmt->fetchClose(PDO::FETCH_ASSOC) : false;
    $total = $res ? abs($res['total']??0) : 0;
    $religion_table = get_religion_table_name();
    unset($res);
    $sql =         "
SELECT {$table}.*,
	{$religion_table}.code as religion_code,
    {$religion_table}.name as religion_name
    FROM sto_supervisor
        LEFT JOIN {$religion_table} ON {$religion_table}.code = {$table}.religion
        WHERE {$table}.site_id {$siteIdWhere} AND {$table}.{$type} LIKE {$likeSearch}
            {$roleWhere} 
        ORDER BY 
             IF({$table}.{$type} = {$nameQuery}, 2, IF({$table}.{$type} LIKE {$likeLeft},1,0))
                DESC LIMIT {$limit} OFFSET {$offset}
    ";

    $stmt = database_unbuffered_query_execute($sql);

    $result = [];
    while ($row = $stmt->fetchAssoc()) {
        $id = (int) $row['id'];
        $row['religion'] = [
            'code' => $row['religion_code']?:null,
            'name' => $row['religion_name']?:null,
        ];
        $row['disallow_admin'] = $row['disallow_admin'] == '1';
        unset($row['total'], $row['religion_code'], $row['religion_name']);
        $row['id'] = (int) $row['id'];
        $row['site_id'] = (int) $row['site_id'];
        $result[$id] = $row;
    }

    $stmt->closeCursor();

    if (!empty($result)) {
        $result = get_users_data_filters($result);
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
 * @return false|array
 */
function get_supervisor_list_by_id(int $id)
{
    $result = get_supervisors_list_by_id($id);
    return $result[0]??false;
}

/**
 * @param string $username
 * @param int|null $siteIds
 * @return array|false|mixed
 */
function get_supervisor_list_by_username(string $username, int $siteIds = null)
{
    $siteIds = $siteIds??get_current_site_id();
    $result = get_supervisors_list_by_username($username, [$siteIds]);
    return $result[0]??false;
}

/**
 * @param string $username
 * @param int|null $siteIds
 * @return array|false|mixed
 */
function get_supervisor_list_by_email(string $username, int $siteIds = null)
{
    $siteIds = $siteIds??get_current_site_id();
    $result = get_supervisors_list_by_email($username, [$siteIds]);
    return $result[0]??false;
}

/**
 * @param string $username
 * @param int|null $siteIds
 * @return array|false
 */
function get_supervisor_list_by_name(string $username, int $siteIds = null)
{
    $siteIds = $siteIds??get_current_site_id();
    $result = get_supervisors_list_by_name($username, [$siteIds]);
    if (!empty($result)) {
        return $result;
    }
    return false;
}

/**
 * @param string $email
 * @param array|null $siteIds
 * @param string|null $role
 * @param int $limit
 * @param int $offset
 * @param null $result
 * @return array
 */
function search_supervisors_list_by_email(
    string $email,
    array $siteIds = null,
    string $role = null,
    int $limit = MYSQL_DEFAULT_SEARCH_LIMIT,
    int $offset = 0,
    &$result = null
) : array {
    return search_supervisors_list_by(
        'email',
        $email,
        $siteIds,
        $role,
        $limit,
        $offset,
        $result
    )?:[];
}

/**
 * @param string $username
 * @param array|null $siteIds
 * @param string|null $role
 * @param int $limit
 * @param int $offset
 * @param null $result
 * @return array
 */
function search_supervisors_list_by_username(
    string $username,
    array $siteIds = null,
    string $role = null,
    int $limit = MYSQL_DEFAULT_SEARCH_LIMIT,
    int $offset = 0,
    &$result = null
) : array {
    return search_supervisors_list_by(
        'username',
        $username,
        $siteIds,
        $role,
        $limit,
        $offset,
        $result
    )?:[];
}

/**
 * @param string $name
 * @param array|null $siteIds
 * @param string|null $role
 * @param int $limit
 * @param int $offset
 * @param null $result
 * @return array
 */
function search_supervisors_list_by_name(
    string $name,
    array $siteIds = null,
    string $role = null,
    int $limit = MYSQL_DEFAULT_SEARCH_LIMIT,
    int $offset = 0,
    &$result = null
) : array {
    return search_supervisors_list_by(
        'full_name',
        $name,
        $siteIds,
        $role,
        $limit,
        $offset,
        $result
    )?:[];
}
