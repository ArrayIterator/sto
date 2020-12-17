<?php
define('ADMIN_LOGIN_PAGE', true);

require __DIR__ . '/init.php';

// set no cache
set_no_cache_header();

if (http_method() === 'POST') {
    $token = post('token');
    $username = post('username');
    $password = post('password');
    $remember = !!post('remember');
    $is_interim = isset($_REQUEST['interim']);
    if (!is_string($username) || trim($username) === '') {
        redirect(get_admin_login_url() . '?error=empty_username' . ($is_interim ? '&interim=1' : ''));
        do_exit(0);
    }
    if (!is_string($password) || trim($password) === '') {
        redirect(get_admin_login_url() . '?error=empty_password' . ($is_interim ? '&interim=1' : ''));
        do_exit(0);
    }
    if (!is_string($token) || trim($token) === '') {
        redirect(get_admin_login_url() . '?error=empty_token' . ($is_interim ? '&interim=1' : ''));
        do_exit(0);
    }
    if (!validate_form_token($token, get_token_cookie())) {
        redirect(get_admin_login_url() . '?error=invalid_token' . ($is_interim ? '&interim=1' : ''));
        do_exit(0);
    }
    $user = get_supervisor_by_username($username);
    if (!$user) {
        redirect(get_admin_login_url() . '?error=invalid_user' . ($is_interim ? '&interim=1' : ''));
        do_exit(0);
    }

    if (!$user->isPasswordMatch($password)) {
        redirect(get_admin_login_url() . '?error=invalid_password' . ($is_interim ? '&interim=1' : ''));
        do_exit(0);
    }

    if (hook_apply('admin_login_success', true, $user) === true) {
        send_user_cookie($user->getId(), SUPERVISOR, $remember);
        redirect(get_admin_url('?login=success' . ($is_interim ? '&interim=1&user_id=' . $user->getId() : '')));
        do_exit(0);
    }
}

load_admin_template('login');
