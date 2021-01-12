<?php
define('ADMIN_AREA', true);
define('ADMIN_DIR', __DIR__);

// DENY DIRECT ACCESS
if (($_SERVER['SCRIPT_FILENAME'] ?? null) === __FILE__) {
    !headers_sent() && header('Location: ./', true, 302);
    exit(0);
}

// LOAD LOADER
require_once dirname(__DIR__) . '/lib/load.php';

require_once __DIR__ . '/includes/forms.php';
require_once __DIR__ . '/includes/menus.php';
require_once __DIR__ . '/includes/pages.php';
require_once __DIR__ . '/includes/hooks.php';

// SET NO ROBOTS INDEX
set_no_index_header();

// check login
if (!is_login() && !is_admin_login_page() && !is_install_page()) {
    redirect(get_admin_login_redirect_url());
    do_exit(0);
}

if (is_supervisor()) {
    if (is_admin_login_page()) {
        // check if login and is on login page
        redirect(get_admin_url());
        do_exit(0);
    }

    if (!is_admin_quarantine_page() && !is_admin() && !is_admin_active()) {
        if (!is_admin_profile_page()) {
            redirect(get_admin_url('quarantined.php'));
            do_exit(0);
        }
    }
}

hook_run('admin_init');

if (!is_admin_login_page()) {
    $is_interim = isset($_REQUEST['interim']);
    $is_success = query_param(PARAM_LOGIN) === 'success';
    $referer = get_referer() ?: '';
    $login_page = explode('?', get_admin_login_url())[0];
    if ($is_interim && $is_success && preg_match('#' . preg_quote($login_page) . '#', get_admin_login_url())) {
        set_status_header(200);
        render(
            '<!DOCTYPE html><html><head>'
            . '<script>try {if (parent && parent.call_iframe_stat.call_iframe_stat) parent.call_iframe_stat(this.location.href, true)} catch (e) {if (!parent || parent === window) {window.location.href = window.location.href.replace(/([\?|&])interim=[^&]+&?/gi, "$1");}}</script>'
            . '<style>body{padding:0;margin:0}.load{position:absolute;height:100px;width:100px;top:50%;margin-top:-50px;left:50%;margin-left:-50px}.loading{height:100%;width:100%;position:relative;text-align:center}.loading .lds-dual-ring{position:absolute;z-index:20;display:block;'
            . 'width:90px;height:90px;overflow:hidden;margin:-40px 0 0 -40px;top:50%;left:50%}.loading .lds-dual-ring::after{content:"";display:block;width:64px;height:64px;margin:8px;border-radius:50%;animation:lds-dual-ring 1.2s linear infinite;border:6px solid #343a40;border-right-color:transparent;border-left-color:transparent}@keyframes lds-dual-ring{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}</style>'
            .'</head><body><div class="load"><div class="loading"><div class="lds-dual-ring"></div></div></div></body></html>',
            true
        );
    }

    unset($is_interim, $is_success, $referer, $login_page);
}
