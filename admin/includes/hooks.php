<?php

use ArrayIterator\Controller\Api\StatusController;

// end here cause I don't want throw error
if (!defined('ROOT_DIR') || !defined('ADMIN_AREA')) {
    return;
}

/**
 * Default Assets
 */
function hook_admin_default_assets()
{
    hook_remove('assets_admin_enqueue_scripts', __FUNCTION__);
    $is_login_page = is_admin_login_page();
    if (!$is_login_page) {
        assets_style_enqueue('select2');
        assets_script_enqueue('select2');
    }
    assets_style_enqueue('admin');
    assets_script_enqueue($is_login_page ? 'admin-login' : 'admin');
    if (!$is_login_page) {
        assets_script_enqueue('moment');
    }
}

function hook_admin_login_js()
{
    $js = get_js_core_by_name('ping');
    if (!is_string($js)) {
        return;
    }
    render("<script type=\"text/javascript\">\n{$js}</script>\n");
    unset($js);
}

/**
 * Render Admin JS Footer
 */
function hook_admin_js_footer()
{
    if (!is_admin_login_page()) {
        hook_add('assets_admin_print_footer_scripts', 'hook_admin_login_js');
    }
}

/**
 * Admin JS Header
 */
function hook_admin_js_header()
{
    $tz   = timezone()->getDateTime('UTC');
    $time = (int) ceil(abs($tz->getTimestamp().$tz->format('.u'))*1000);
    $ping_url = json_ns(get_api_url(StatusController::PING_PATH));
    $api_url = json_ns(get_api_url());
    $login_url = json_ns(get_admin_login_url());
    $user_id = get_current_user_id();
    $cookie_domain = json_ns(COOKIE_DOMAIN);
    $time_utc = json_ns($time);
    $rendered = '';
    $current_utc_date = json_ns($tz->format('c'));
    $timezone_name = json_ns(timezone_convert()->getName());
    $upload_file_size_limit = get_max_upload_file_size();
    if (!is_admin_login_page()) {
        $rendered = <<<JS

            w.ping_url  = {$ping_url};
            w.api_url  = {$api_url};
            w.login_url = {$login_url};
            w.user_id   = $user_id;
            w.max_upload_size = {$upload_file_size_limit};
JS;

    }
    render(<<<JS

    <script type="text/javascript">
        (function (w, d) {
            d.remove('no-js');
            d.add('js');{$rendered}
            w.cookie_domain = {$cookie_domain};
            w.time_start_utc = {$time_utc};
            w.current_gmt_time = time_start_utc;
            w.current_date_string = {$current_utc_date};
            w.timezone_string = {$timezone_name};
            w.interval_time_stop = false;
            var _int = setInterval(function () {
                w.current_gmt_time += 1000;
                if (w.interval_time_stop === true) {
                    clearInterval(_int);
                }
            }, 1000);
        })(window, document.documentElement.classList);
    </script>

JS
    );
}

function hook_admin_meta_header()
{
    render(<<<HTML
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">.hide-if-js{display:none}</style>
HTML
    );
}

/**
 * Hook Add Login Notice
 */
function hook_admin_init_login()
{
    hook_remove('admin_init', 'hook_admin_init_login');
    $is_exists = has_cookie_succeed();
    if (!$is_exists
        || query_param('login') !== 'success'
        || !is_numeric(query_param('user_id'))
    ) {
        return;
    }

    $user_id = query_param_int('user_id');
    $current_id = get_current_user_id();
    if ($current_id > 0 && $user_id === $current_id) {
        add_admin_success_message(
            'login_success',
            trans('You have successfully logged in')
        );
    }
}

// add admin render
hook_add('admin_html_head', 'hook_admin_meta_header', 1);
hook_add('admin_html_head', 'hook_admin_js_header', 1);
hook_add('assets_admin_enqueue_scripts', 'hook_admin_default_assets');
hook_add('admin_html_footer', 'hook_admin_js_footer');
hook_add('admin_top_message', 'render_admin_message');
hook_add('admin_init', 'hook_admin_init_login');

// class hooks
require_once __DIR__ .'/hooks/classes.php';
