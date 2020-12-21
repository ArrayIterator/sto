<?php
namespace ArrayIterator\Controller\Common;

use ArrayIterator\Controller\BaseController;
use ArrayIterator\Route;
use ArrayIterator\RouteStorage;

/**
 * Class Auth
 * @package ArrayIterator\Controller\Common
 */
class Auth extends BaseController
{
    /**
     * Route Handle Login Form
     *
     * @param Route $route
     */
    public function reset(Route $route)
    {
        // redirect if login
        if (is_login()) {
            redirect(get_site_url());
            do_exit(0);
        }

        if (!allow_student_reset_password()) {
            redirect(get_login_url());
            do_exit(0);
        }

        set_title('Reset Your Password');
        load_template('forgot.php');
    }

    /**
     * Handle Login Route
     *
     * @param Route $route
     * @param array $params
     */
    public function login(Route $route, array $params = [])
    {
        // redirect if login
        if (is_login()) {
            redirect(get_site_url());
            do_exit(0);
        }

        set_title('Login To Dashboard');
        load_template('login.php');
    }

    /**
     * @param RouteStorage $route
     */
    protected function registerController(RouteStorage $route)
    {
        $route->any(route_slash_it(get_login_path()), [$this, 'login']);
        $route->any(route_slash_it(get_reset_password_path()), [$this, 'login']);
    }
}
