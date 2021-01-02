<?php
namespace ArrayIterator\Controller\Api;

use ArrayIterator\Controller\BaseController;
use ArrayIterator\Info\Module;
use ArrayIterator\Route;
use ArrayIterator\RouteStorage;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class ModuleController
 * @package ArrayIterator\Controller\Api
 */
class ModuleController extends BaseController
{
    public function getModule(Route $r, array $params = [])
    {
        if (!current_supervisor_can('view_modules')) {
            json(HTTP_CODE_UNAUTHORIZED, trans('Access Denied'));
        }

        $name = $params['name']??null;
        if ($name === null) {
            $data = [];
            foreach (modules()->getModules() as $module) {
                $data[] = $module->toArray();
            }
            json($data);
            return;
        }

        if (!is_string($name) || trim($name) === '') {
            json(
                HTTP_CODE_PRECONDITION_FAILED,
                trans('Please Insert Module Name')
            );
            return;
        }

        $mod = modules()->getModule($name);
        if (!$mod) {
            json(
                HTTP_CODE_EXPECTATION_FAILED,
                trans_sprintf('Module %s has not found', $name)
            );
        }

        json($mod->toArray());
    }

    /**
     * @param RouteStorage $route
     */
    protected function registerController(RouteStorage $route)
    {
        if (!current_supervisor_can('view_modules')) {
            return;
        }

        $route->get('/modules[/[{name: [^/]+}[/]]]', [$this, 'getModule']);
    }
}
