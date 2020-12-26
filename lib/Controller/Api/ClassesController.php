<?php
namespace ArrayIterator\Controller\Api;

use ArrayIterator\Controller\BaseController;
use ArrayIterator\Helper\NormalizerData;
use ArrayIterator\Route;
use ArrayIterator\RouteStorage;
use Exception;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/*
SELECT
    class.*,
    supervisor.*,
    COUNT(class.id) OVER() AS total
FROM
    sto_classes as class

LEFT JOIN
	(
        SELECT
        	sto_classes_teacher.class_id as class_id,
        	sto_classes_teacher.year as class_year,
        	sto_supervisor.id as teacher_id,
        	sto_supervisor.full_name as teacher_full_name,
        	sto_supervisor.role as teacher_role,
	        sto_supervisor.site_id as teacher_site_id
        FROM
        	sto_classes_teacher
        LEFT JOIN sto_supervisor ON
        	sto_supervisor.role = 'teacher' AND sto_classes_teacher.teacher = sto_supervisor.id
        WHERE role = 'teacher'
        	AND (sto_supervisor.status = '' OR sto_supervisor.status = 'active')
    ) as supervisor
    	ON supervisor.teacher_site_id = class.site_id
        AND supervisor.class_id = class.id

WHERE
    class.site_id IS NOT NULL AND class.site_id > 0

ORDER BY
    class.id,
    class.site_id
LIMIT 10
 */
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
        $siteIds = get_super_admin_site_ids_params();
        $hasSiteIds = is_super_admin() && has_query_param('site_ids');
        $originalLimit = $limit;

        $filter = query_param('filter');
        $filter = !is_string($filter) ? null : trim($filter);
        $filter = $filter ? explode(',', $filter) : [];
        foreach ($filter as $key => $item) {
            if (!in_array($item, ['code', 'name', 'id', 'note', 'teachers', 'site_id'])) {
                unset($filter[$key]);
            }
        }

        $filter = array_values($filter);
        $filter = array_flip($filter);

        $data = [
            'total' => 0,
            'count' => 0,
            'page' => [
                'total' => 0,
                'current' => 0,
            ],
            'next' => [
                'offset' => null,
                'limit' => null,
                'url' => null,
            ],
            'query' => [
                'site_id' => $siteIds,
                'limit' => $limit,
                'offset' => $offset,
                'query' => null,
                'type' => null,
                'filters' => array_keys($filter)
            ],
            'results' => []
        ];

        try {
            $data = get_classes_data($limit, $offset);
            $offset = $data['meta']['query']['offset'];
            if (!empty($filter)) {
                foreach ($data['results'] as $key => &$item) {
                    foreach ($item as $keyName => $v) {
                        /**
                         * @var string $keyName
                         */
                        if ($keyName !== 'id' && !isset($filter[$keyName])) {
                            unset($item[$keyName]);
                        }
                    }
                }
            }
            $meta = $data['meta'];
            $data = [
                'total' => $data['total'],
                'count' => $data['count'],
                'page' => [
                    'total' => 0,
                    'current' => 0,
                ],
                'next' => [
                    'offset' => null,
                    'limit' => null,
                    'url' => null,
                ],
                'pagination' => null,
                'query' => [
                    'site_id' => $siteIds,
                    'limit' => $meta['query']['limit'],
                    'offset' => $meta['query']['offset'],
                    'query' => null,
                    'type' => null,
                    'filters' => array_keys($filter)
                ],
                'results' => $data['results']
            ];

            if ($data['total'] === null) {
                $data['total'] = $data['count'];
            }

            $total = $data['total'];
            $offset = $offset+$data['count'];
            $nextTotal = $total - $offset;
            $dataLimit = $meta['query']['limit'];
            if ($originalLimit !== $limit && $dataLimit > $total) {
                $data['query']['limit'] = $data['total'];
            }

            if ($total > 0) {
                $page_total = $dataLimit >= $total ? 1 : ceil($total / $dataLimit);
                $data['page']['total'] = $page_total;
                $current_page = $page_total - ceil($total / $offset) + 1;
                $data['page']['current'] = $current_page;
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
                    build_query($args)
                );
            }
        } catch (Exception $e) {
            json(500, $e);
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
        $siteIds = get_super_admin_site_ids_params();
        $method = null;
        if (isset($params['id'])) {
            $method = 'get_class_by_id';
            $param = $params['id']??null;
            $param = is_numeric($param) ? abs($param) : $param;
            if (!is_int($param)) {
                route_not_found();
            }
        } elseif (isset($params['name'])) {
            $method = 'get_class_by_name';
            $param = is_string($params['name']) ? trim($params['name']) : null;
            $param = $param !== '' ? $param : null;
        } elseif (isset($params['code'])) {
            $method = 'get_class_by_code';
            $param = is_string($params['code']) ? trim($params['code']) : null;
            $param = $param !== '' ? $param : null;
        } else {
            route_not_found();
            return;
        }

        $result = $method($param, $siteIds);
        $currentSiteId = get_current_site_id();
        if (!is_super_admin()) {
            foreach ($result as $i => $item) {
                if ($item['site_id'] <> $currentSiteId) {
                    unset($result[$i]);
                }
            }
        }

        json(200, $result);
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
        $original = is_int($id) && $id > 0 ? get_classes_by_id($id) : null;
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

        unset($original);
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
                $data['text'] = trans_sprintf(
                    'Class %s successfully updated!',
                    post('code')
                );
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
                $data['text'] = trans_sprintf(
                    'Class %s successfully saved!', $response['code']
                );
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

        $filter = query_param('filter');
        $filter = !is_string($filter) ? null : trim($filter);
        $filter = $filter ? explode(',', $filter) : [];
        foreach ($filter as $key => $item) {
            if (!in_array($item, ['code', 'name', 'id', 'note', 'teachers', 'site_id'])) {
                unset($filter[$key]);
            }
        }

        $filter = array_values($filter);
        $filter = array_flip($filter);
        $offset = query_param_int('offset');
        $limit = query_param('limit');
        $originalLimit = $limit;
        $limit = !is_numeric($limit) ? MYSQL_DEFAULT_SEARCH_LIMIT : abs(intval($limit));
        $limit = $limit <= 1 ? 1 : (
            $limit > MYSQL_MAX_SEARCH_LIMIT
                ? MYSQL_MAX_SEARCH_LIMIT
                : $limit
            );

        $data = $type === 'name'
            ? search_class_by_name($search, $siteIds, $limit, $offset)
            : search_class_by_code($search, $siteIds, $limit, $offset);
        if (!empty($filter)) {
            foreach ($data['results'] as $key => &$item) {
                foreach ($item as $k => $v) {
                    if (!isset($filter[$k]) && $k !== 'id') {
                        unset($item[$k]);
                    }
                }
            }
        }

        $nextTotal = $data['meta']['next']['total'];
        $data = [
            'total' => $data['total'],
            'count' => $data['count'],
            'page' => [
                'total' => $data['meta']['page']['total'],
                'current' => $data['meta']['page']['current'],
            ],
            'current' => [
                'site_id' => $siteIds,
                'limit' => $data['meta']['query']['limit'],
                'offset' => $data['meta']['query']['offset'],
                'query' => $search,
                'type' => $type,
                'filters' => array_keys($filter),
            ],
            'next' => [
                'offset' => $data['meta']['next']['offset'],
                'limit' => $data['meta']['next']['limit'],
                'url' => null,
            ],
            'results' => $data['results']
        ];


        if ($nextTotal > 0) {
            $args = $data['next'];
            unset($args['url']);
            if (!empty($siteIds)) {
                $key = $hasSiteIds ? 'site_id' : 'site_ids';
                $args[$key] = implode(',', $siteIds);
            }
            $args['type'] = $type;
            $args['q'] = $search;
            $data['next']['url'] = sprintf(
                '%s?%s',
                get_current_uri()->getPath(),
                NormalizerData::buildQuery($args)
            );
        }

        json(200, $data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
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
            '/classes/id/{id: [0-9]+}[/]',
            [$this, 'getClass']
        );
        $route->get(
            '/classes/name/{name: .+}[/]',
            [$this, 'getClass']
        );
        $route->get(
            '/classes/code/{name: .+}[/]',
            [$this, 'getClass']
        );

        $route->get(
            '/classes/search[/]',
            [$this, 'searchClasses']
        );
    }
}
