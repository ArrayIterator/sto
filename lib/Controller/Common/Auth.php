<?php
namespace ArrayIterator\Controller\Common;

use ArrayIterator\Route;

/**
 * Class Auth
 * @package ArrayIterator\Controller\Common
 */
class Auth
{
    /**
     * Route Handle Login Form
     *
     * @param Route $route
     */
    public static function reset(Route $route)
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
    public static function login(Route $route, array $params = [])
    {
        // redirect if login
        if (is_login()) {
            redirect(get_site_url());
            do_exit(0);
        }

        set_title('Login To Dashboard');
        load_template('login.php');
    }
}