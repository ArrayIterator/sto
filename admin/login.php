<?php
define('ADMIN_LOGIN_PAGE', true);

require __DIR__ . '/init.php';

// set no cache
set_no_cache_header();

use \ArrayIterator\Helper\NormalizerData;

if (http_method() === 'POST') {
    $cookie = cookies();
    $token = post('token');
    $username = post('username');
    $password = post('password');
    $remember = !!post('remember');
    $is_interim = isset($_REQUEST['interim']);
    if (empty($cookie)) {
        $cookie = [];
        redirect(get_admin_login_redirect_url(['error' => 'cookie_disabled']));
        do_exit(0);
    }

    if (!is_string($username) || trim($username) === '') {
        redirect(get_admin_login_redirect_url(['error' => 'empty_username']));
        do_exit(0);
    }
    if (!is_string($password) || trim($password) === '') {
        redirect(get_admin_login_redirect_url(['error' => 'empty_password']));
        do_exit(0);
    }
    if (!is_string($token) || trim($token) === '') {
        redirect(get_admin_login_redirect_url(['error' => 'empty_token']));
        do_exit(0);
    }
    if (!validate_form_token($token, get_token_cookie())) {
        redirect(get_admin_login_redirect_url(['error' => 'invalid_token']));
        do_exit(0);
    }
    $user = get_supervisor_by_username($username);
    if (!$user) {
        redirect(get_admin_login_redirect_url(['error' => 'invalid_user']));
        do_exit(0);
    }
    $status = ($userData['status'] ?? false)?: false;
    if ($status !== false || in_array(strtolower($status), ['delete', 'deleted'])) {
        redirect(get_admin_login_redirect_url([
            'error' => 'invalid_user',
            'status' => 'user_deleted'
        ]));

        do_exit(0);
    }

    if (!$user->isPasswordMatch($password)) {
        redirect(get_admin_login_redirect_url(['error' => 'invalid_password']));
        do_exit(0);
    }

    if (hook_apply('admin_login_success', true, $user) === true) {
        if (create_user_session($user, $remember)) {
            $params = get_admin_param_redirect();
            $redirectLoginUrl =  get_admin_url($params['redirect']??'');
            unset($params['redirect']);
            if ($is_interim) {
                $params['interim'] = 1;
                $params['login'] = 'success';
                $params['user_id'] = $user->getId();
            }
            $redirectLoginUrl = NormalizerData::addQueryArgs($params, $redirectLoginUrl);
            $redirectLoginUrl = hook_apply(
                'redirect_success_login_url',
                $redirectLoginUrl
            );
            if ($is_interim) {
                // back compat
                $redirectLoginUrl = NormalizerData::addQueryArgs([
                    'interim' => 1,
                    'login' => 'success',
                    'user_id' => $user->getId()
                ], $redirectLoginUrl);
            }

            redirect($redirectLoginUrl);
        } else {
            redirect(get_admin_login_url() . '?error=fail_login' . ($is_interim ? '&interim=1' : ''));
            do_exit(0);
        }

        do_exit(0);
    }
}

load_admin_template('login');
