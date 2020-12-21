<?php
namespace ArrayIterator\Controller\Api;

use ArrayIterator\Controller\BaseController;
use ArrayIterator\RouteStorage;

/**
 * Class Status
 * @package ArrayIterator\Controller\Api
 */
class StatusController extends BaseController
{
    const PING_PATH = '/ping';

    /**
     * Ping Route
     */
    public function ping()
    {
        // # Sample
        //create_cookie(COOKIE_STUDENT_NAME, create_json_hash(1, 'student'));
        //create_cookie(COOKIE_SUPERVISOR_NAME, create_json_hash(1, 'supervisor'));

        $loggedAs = [];
        $data = get_current_student_data();
        if ($data) {
            $loggedAs['student'] = [
                'id' => $data['user_id'],
                'uuid' => $data['uuid'],
                'username' => $data['user']->username,
            ];
        }

        $data = get_current_supervisor_data();
        if ($data) {
            $loggedAs['supervisor'] = [
                'id' => $data->getUserId(),
                'uuid' => $data->getUuid(),
                'username' => $data->getUser()->get('username'),
            ];
        }

        json(200, hook_apply('route_ping_result', [
            'site_id' => get_current_site_id(),
            'login' => !empty($loggedAs),
            'as' => $loggedAs,
            'online' => [
                'student' => student_online()->count(),
                'supervisor' => supervisor_online()->count(),
            ],
            'response_time' => microtime(true) - MICRO_TIME_FLOAT,
            'time' => time(),
            'timezone' => date_default_timezone_get(),
        ]));
    }

    /**
     * @param RouteStorage $route
     */
    protected function registerController(RouteStorage $route)
    {
        $route->any(route_slash_it(self::PING_PATH), [$this, 'ping']);
    }
}
