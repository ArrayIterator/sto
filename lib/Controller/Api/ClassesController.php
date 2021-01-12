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

/**
 * Class ClassesController
 * @package ArrayIterator\Controller\Api
 */
class ClassesController extends BaseController
{
    public function getClasses(Route $r)
    {
        $type = query_param_string(PARAM_TYPE);
        $search = query_param_string(PARAM_SEARCH_QUERY, '', true);
        // fallback to query
        if (in_array($type, ['name', 'code']) && $search !== '') {
            $this->searchClasses(
                $r,
                [
                    PARAM_TYPE => $type,
                    PARAM_SEARCH_QUERY => $search
                ]
            );
            return;
        }

        if (!current_supervisor_can('view_classes')) {
            json(HTTP_CODE_UNAUTHORIZED, trans('Access Denied'));
            return;
        }

        $limit  = query_param_int(PARAM_LIMIT);
        $offset = query_param_int(PARAM_OFFSET);
        $siteIds = get_super_admin_site_ids_params();
        $hasSiteIds = is_super_admin() && has_query_param(PARAM_SITE_IDS);
        $originalLimit = $limit;

        $filter = query_param(PARAM_FILTER);
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
                PARAM_SEARCH_QUERY => null,
                'type' => null,
                'filters' => array_keys($filter)
            ],
            'results' => []
        ];

        try {
            $data = get_classes_data($limit, $offset, $siteIds);
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
                    PARAM_SEARCH_QUERY => null,
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
            if ($meta['query']['limit'] < 1) {
                $meta['query']['limit'] = get_generate_max_search_result_limit(
                    $meta['query']['limit']
                );
            }

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
        json($data);
    }

    /**
     * @param Route $r
     * @param array $params
     */
    public function getClass(Route $r, array $params = [])
    {
        if (!current_supervisor_can('view_class')) {
            json(HTTP_CODE_UNAUTHORIZED, trans('Access Denied'));
            return;
        }
        $siteIds = get_super_admin_site_ids_params();
        $method = null;
        $current = null;
        if (isset($params['id'])) {
            $method = 'get_classes_by_id';
            $current = 'id';
            $param = $params['id']??null;
            $param = is_numeric($param) ? abs($param) : $param;
            if (!is_int($param)) {
                route_not_found();
            }
        } elseif (isset($params['name'])) {
            $current = 'name';
            $method = 'get_classes_by_name';
            $param = is_string($params['name']) ? trim($params['name']) : null;
            $param = $param !== '' ? $param : null;
        } elseif (isset($params['code'])) {
            $current = 'code';
            $method = 'get_classes_by_code';
            $param = is_string($params['code']) ? trim($params['code']) : null;
            $param = $param !== '' ? $param : null;
        } else {
            route_not_found();
            return;
        }
        if (!$param) {
            json(
                HTTP_CODE_PRECONDITION_REQUIRED,
                trans(
                    'Request parameter is empty'
                )
            );
        }

        $result = $method($param, $siteIds);
        $currentSiteId = get_current_site_id();
        if (!is_super_admin()) {
            foreach ($result as $key => $item) {
                if ($item['site_id'] <> $currentSiteId) {
                    unset($result[$key]);
                }
            }
        }

        if (empty($result)) {
            json(
                HTTP_CODE_NOT_FOUND,
                trans_sprintf(
                    'Class %s : %s is not exists',
                    ucwords($current),
                    $param
                )
            );
            return;
        }

        json($result);
    }

    /**
     * @param Route $r
     * @param array $params
     */
    public function saveClass(Route $r, array $params = [])
    {
        if (!current_supervisor_can('edit_class')
            && !current_supervisor_can('add_class')
        ) {
            json(HTTP_CODE_UNAUTHORIZED, trans('Access Denied'));
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
                json(HTTP_CODE_PRECONDITION_FAILED, trans_sprintf(
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
                json(HTTP_CODE_UNAUTHORIZED, trans('Access Denied'));
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
                json($data);
                return;
            }
        } else {
            if (!current_supervisor_can('add_class')) {
                json(HTTP_CODE_UNAUTHORIZED, trans('Access Denied'));
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

                json($data);
                return;
            }
        }

        if ($response === -4) {
            $data = trans_sprintf(
                'Class %s could not be empty!',
                trans('Code')
            );
            json(HTTP_CODE_PRECONDITION_REQUIRED, $data);
            return;
        }
        if ($response === -3) {
            $data = trans_sprintf(
                'Class %s could not be empty!',
                trans('Name')
            );
            json(HTTP_CODE_PRECONDITION_REQUIRED, $data);
            return;
        }

        if ($response === -2) {
            $data = trans_sprintf(
                'Class %s is duplicate!',
                sprintf('%s %s', trans('Code'), post('code'))
            );
            json(HTTP_CODE_CONFLICT, $data);
            return;
        }

        if ($response === -1) {
            $data = trans_sprintf(
                'Class %s is duplicate!',
                sprintf('%s %s', trans('Name'), post('name'))
            );
            json(HTTP_CODE_CONFLICT, $data);
            return;
        }

        json(HTTP_CODE_NOT_ACCEPTABLE, trans('Error save data!'));
    }

    public function searchClasses(Route $route, array $params = [])
    {
        if (!current_supervisor_can('view_classes')) {
            json(HTTP_CODE_UNAUTHORIZED, trans('Access Denied'));
            return;
        }

        $type = $params[PARAM_TYPE]??query_param(PARAM_TYPE);
        $type = is_string($type) ? trim(strtolower($type)) : '';
        if (!$type || !in_array($type, ['name', 'code'])) {
            json(HTTP_CODE_PRECONDITION_FAILED, trans('Search type is invalid!'));
        }
        $search = $params[PARAM_SEARCH_QUERY]??query_param(PARAM_SEARCH_QUERY);
        if (!$search || !is_string($search) || trim($search) === '') {
            json(HTTP_CODE_PRECONDITION_REQUIRED, trans('Search query could not be empty!'));
            return;
        }

        $siteIds = [];
        $hasSiteIds = false;

        if (has_query_param(PARAM_SITE_IDS)) {
            $hasSiteIds = true;
            $siteIds = get_super_admin_site_ids_param();
        }

        if (has_query_param(PARAM_SITE_ID)) {
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

        $filter = query_param(PARAM_FILTER);
        $filter = !is_string($filter) ? null : trim($filter);
        $filter = $filter ? explode(',', $filter) : [];
        foreach ($filter as $key => $item) {
            if (!in_array($item, ['code', 'name', 'id', 'note', 'teachers', 'site_id'])) {
                unset($filter[$key]);
            }
        }

        $filter = array_values($filter);
        $filter = array_flip($filter);
        $offset = query_param_int(PARAM_OFFSET);
        $limit = query_param(PARAM_LIMIT);
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
                PARAM_SEARCH_QUERY => $search,
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
            $args[PARAM_SEARCH_QUERY] = $search;
            $data['next']['url'] = sprintf(
                '%s?%s',
                get_current_uri()->getPath(),
                NormalizerData::buildQuery($args)
            );
        }

        json($data);
    }

    protected function registerController(RouteStorage $route)
    {
        if (!current_supervisor_can('view_classes')) {
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
