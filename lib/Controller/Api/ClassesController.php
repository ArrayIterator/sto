<?php
namespace ArrayIterator\Controller\Api;

use ArrayIterator\Controller\BaseController;
use ArrayIterator\Helper\NormalizerData;
use ArrayIterator\Route;
use ArrayIterator\RouteStorage;
use Exception;

/**
 * Class ClassesController
 * @package ArrayIterator\Controller\Api
 */
class ClassesController extends BaseController
{
    public function getClasses()
    {
        if (!is_user_logged() || is_supervisor() && !is_admin_active()) {
            route_not_found();
            return;
        }

        if (!current_supervisor_can('view_classes')) {
            json(401, trans('Access Denied'));
            return;
        }

        $limit  = query_param_int('limit');
        $offset = query_param_int('offset');
        $siteIds = [];
        $hasSiteIds = false;

        if (has_query_param('site_ids')) {
            $hasSiteIds = true;
            $siteIds = get_super_admin_site_ids_param();
        }

        if (has_query_param('site_id')) {
            $siteId = get_super_admin_site_id_param();
            $siteId = $siteId === 0 ? false : $siteId;
            if ($siteId !== false && !in_array($siteId, $siteIds)) {
                $siteIds[] = $siteId;
            }
        }
        if (!is_super_admin()) {
            $hasSiteIds = false;
            $siteIds = [get_current_site_id()];
        }

        $table = get_classes_table_name();
        $sql = "SELECT * FROM {$table} ";
        $where = 'WHERE site_id ';
        $where .= !empty($siteIds)
            ? sprintf('IN (%s) ', implode(',', $siteIds))
            : 'IS NOT NULL AND site_id != 0 ';
        $sql .= $where;

        $originalLimit = $limit;
        $limit = $limit < 1 ? MYSQL_DEFAULT_SEARCH_LIMIT : $limit;
        $sql .= "ORDER BY id LIMIT {$limit}";
        $offset = $offset > 0 ? $offset : 0;
        if ($offset > 0) {
            $sql .= " OFFSET {$offset}";
        }
        $countSQL = "SELECT count(id) as total FROM {$table} ";
        $countSQL .= $where;
        $totalities = null;
        try {
            $st = database_unbuffered_query($countSQL);
            $totalities = $st->fetchAssoc();
            $st->closeCursor();
            $totalities = $totalities ? ($totalities['total']??null) : null;
            $totalities = $totalities !== null ? abs($totalities) : null;
        } catch (Exception $e) {
            $totalities = null;
        }

        $data = [
            'total' => $totalities,
            'page_total' => 0,
            'current_page' => 0,
            'site_id' => $siteIds,
            'limit' => $limit,
            'offset' => $offset,
            'count' => 0,
            'next' => [
                'offset' => null,
                'limit' => null,
                'url' => null,
            ],
            'results' => []
        ];
        try {
            $count =& $data['count'];
            $st = database_unbuffered_query($sql);
            if (!$st) {
                json(200, $data);
            }

            while ($row = $st->fetchAssoc()) {
                if ($count++ === 0) {
                    // $data['total'] = (int) ($row['total']);
                    $data['page_total'] = 1;
                    $data['current_page'] = 1;
                }

                $data['results'][] = [
                    'id' => (int) $row['id'],
                    'site_id' => (int) $row['site_id'],
                    'code' => $row['code'],
                    'name' => $row['name'],
                    'note' => $row['note'],
                ];
            }
            $st->close();
            if ($data['total'] === null) {
                $data['total'] = $count;
            }

            $total = $data['total'];
            $offset = $offset+$count;
            $nextTotal = $total - $offset;
            $dataLimit = $data['limit'];
            if ($originalLimit !== $limit && $dataLimit > $total) {
                $data['limit'] = $data['total'];
            }

            if ($total > 0) {
                $page_total = $dataLimit >= $total ? 1 : ceil($total / $dataLimit);
                $data['page_total'] = $page_total;
                $current_page = $page_total - ceil($total / $offset) + 1;
                $data['current_page'] = $current_page;
            }

            if ($nextTotal > 0) {
                $data['next']['offset'] = $offset;
                $data['next']['limit'] = $nextTotal > $limit ? $limit : $nextTotal;
                $args = $data['next'];
                unset($args['url']);
                if (!empty($siteIds)) {
                    $key = $hasSiteIds ? 'site_id' : 'site_ids';
                    $args[$key] = implode(',', $siteIds);
                }

                $data['next']['url'] = sprintf(
                    '%s?%s',
                    get_current_uri()->getPath(),
                    NormalizerData::buildQuery($args)
                );
            }
        } catch (Exception $e) {
            echo $e;
            exit;
        }

        json(200, $data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param Route $r
     * @param array $params
     */
    public function getClass(Route $r, array $params = [])
    {
        if (!is_user_logged() || is_supervisor() && !is_admin_active()) {
            route_not_found();
            return;
        }

        if (!current_supervisor_can('view_class')) {
            json(401, trans('Access Denied'));
            return;
        }

        $id = $params['id']??null;
        $id = is_numeric($id) ? abs($id) : $id;
        if (!is_int($id)) {
            route_not_found();
        }

        $site_id = !is_super_admin() ? get_current_site_id() : null;
        $result = get_class_by_id($id);

        if (empty($result)
            || $site_id !== null && $site_id <> $result['site_id']
        ) {
            json(200, []);
        }

        $result = [
            'id' => (int) $result['id'],
            'site_id' => (int) $result['site_id'],
            'code' => $result['code'],
            'name' => $result['name'],
            'note' => $result['note'],
        ];


        json(200, $result, JSON_PRETTY_PRINT);
    }

    /**
     * @param Route $r
     * @param array $params
     */
    public function saveClass(Route $r, array $params = [])
    {
        if (!is_user_logged() || is_supervisor() && !is_admin_active()) {
            route_not_found();
            return;
        }

        if (!current_supervisor_can('edit_class')
            && !current_supervisor_can('add_class')
        ) {
            json(401, trans('Access Denied'));
            return;
        }

        $data = [
            'success' => false,
            'text'    => trans('No Data'),
            'result' => []
        ];

        $id = $params['id']??null;
        $action = $params['action']??null;
        $id = is_numeric($id) ? abs($id) : $id;

        $is_update = $action === 'edit';
        $original = is_int($id) && $id > 0 ? get_class_by_id($id) : null;
        if (empty($original)) {
            if ($is_update && $id !== null) {
                json(412, trans_sprintf(
                    'Class id %s has not exists',
                    is_numeric($id) ? $id : ''
                ));
                return;
            }

            $is_update = false;
        }

        if ($is_update) {
            // prevent update
            if (!current_supervisor_can('edit_class')) {
                json(401, trans('Access Denied'));
                return;
            }

            $data['success'] = false;
            $response = update_class_data($id, posts());
            // succeed
            if ($response === true || $response === 1) {
                $data['success'] = true;
                $data['text'] = trans_sprintf('Class %s successfully updated!', post('code'));
                $data['result'] = get_class_by_id($id);
                json(200, $data);
                return;
            }
        } else {
            if (!current_supervisor_can('add_class')) {
                json(401, trans('Access Denied'));
                return;
            }

            $response = insert_class_data(posts());
            $data['success'] = false;
            if (is_array($response)) {
                $data['text'] = trans_sprintf('Class %s successfully saved!', $response['code']);
                $data['success'] = true;
                $data['result'] = $response;
                unset($response);

                json(200, $data);
                return;
            }
        }

        if ($response === -4) {
            $data = trans_sprintf('Class %s could not be empty!', trans('Code'));
            json(412, $data);
            return;
        }
        if ($response === -3) {
            $data = trans_sprintf('Class %s could not be empty!', trans('Name'));
            json(412, $data);
            return;
        }

        if ($response === -2) {
            $data = trans_sprintf('Class code %s is duplicate!', post('code'));
            json(412, $data);
            return;
        }

        if ($response === -1) {
            $data = trans_sprintf('Class name %s is duplicate!', post('name'));
            json(412, $data);
            return;
        }

        json(406, trans('Error save data!'));
    }

    public function searchClasses(Route $route, array $params = [])
    {
        if (!is_user_logged() || is_supervisor() && !is_admin_active()) {
            route_not_found();
            return;
        }

        if (!current_supervisor_can('view_classes')) {
            json(401, trans('Access Denied'));
            return;
        }

        $type = query_param('type');
        $type = is_string($type) ? trim(strtolower($type)) : '';
        if (!$type || !in_array($type, ['name', 'code'])) {
            json(412, trans('Search type is invalid!'));
        }
        $search = query_param('q');
        if (!$search || !is_string($search) || trim($search) === '') {
            json(428, trans('Search query could not be empty!'));
            return;
        }
        $siteId = get_current_site_id();
        if (is_super_admin()) {
            $sId = query_param('site_id');
            $sId = is_string($sId) ? trim($sId) : $sId;
            $sId = is_numeric($sId) ? abs($sId) : $siteId;
            $sId = is_int($sId) ? ($sId) : $siteId;
            $siteId = $sId;
        }
        $filter = query_param('filter');
        $filter = !is_string($filter) ? null : trim($filter);
        $filter = $filter ? explode(',', $filter) : [];
        foreach ($filter as $key => $item) {
            if (!in_array($item, ['code', 'name', 'id', 'note'])) {
                unset($filter[$key]);
            }
        }

        $filter = array_values($filter);
        $filter = array_flip($filter);
        $limit = query_param('limit');
        $limit = !is_numeric($limit) ? MYSQL_DEFAULT_SEARCH_LIMIT : abs(intval($limit));
        $limit = $limit <= 1 ? 1 : (
            $limit > MYSQL_MAX_SEARCH_LIMIT
                ? MYSQL_MAX_SEARCH_LIMIT
                : $limit
            );

        $data = $type === 'name'
            ? search_class_by_name($search, $siteId, $limit)
            : search_class_by_code($search, $siteId, $limit);
        if (!empty($filter)) {
            foreach ($data as $key => &$item) {
                foreach ($item as $k => $v) {
                    if (!isset($filter[$k]) && $k !== 'id') {
                        unset($item[$k]);
                    }
                }
            }
        }

        json(200, [
            'count' => count($data),
            'site_id' => [$siteId],
            'type' => $type,
            'query' => $search,
            'limit' => $limit,
            'filters' => array_keys($filter),
            'result' => $data
        ], JSON_PRETTY_PRINT);
    }

    protected function registerController(RouteStorage $route)
    {
        if (!is_user_logged() || is_supervisor() && !is_admin_active()) {
            return;
        }

        $route->get(
            '/classes[/]',
            [$this, 'getClasses']
        );
        $route->post(
            '/classes[/]',
            [$this, 'saveClass']
        );

        $route->get(
            '/classes/{id: [0-9]+}[/]',
            [$this, 'getClass']
        );

        $route->get(
            '/classes/search[/]',
            [$this, 'searchClasses']
        );
    }
}
