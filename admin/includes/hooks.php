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

    assets_style_enqueue('bootstrap');
    assets_style_enqueue('select2');
    assets_style_enqueue('admin');

    assets_script_enqueue_footer('crypto');
    assets_script_enqueue_footer('core');
    assets_script_enqueue_footer('popper');
    assets_script_enqueue_footer('bootstrap');
    assets_script_enqueue_footer('underscore');

    if (!$is_login_page) {
        assets_style_enqueue('quill');
        assets_script_enqueue_footer('select2');
        assets_script_enqueue_footer('quill');
        assets_script_enqueue_footer('moment');
    }

    assets_script_enqueue_footer($is_login_page ? 'admin-login' : 'admin');
}

function hook_admin_login_js()
{
    $js = get_js_core_by_name('ping');
    if (is_string($js)) {
        render("<script type=\"text/javascript\">\n{$js}</script>\n");
    }
    unset($js);
}

function hook_admin_modal_js()
{
    $js = get_js_core_by_name('ping');
    if (!is_string($js)) {
        return;
    }
    $close = esc_html_trans('Close');
    render(<<<PLAIN
<script type="text/template" id="underscore_template_modal">
    <div class="modal fade" tabindex="-1" role="dialog" data-template="underscore_template_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <% if (typeof title === "string" ) { %>
                    <h5 class="modal-title"><%= title %></h5>
                    <% } %>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <% if (typeof content !== "undefined" ) { %>
                    <%= content %>
                    <% } %>
                </div>
                <div class="modal-footer">
                    <% if (typeof button !== "undefined" ) { %>
                        <%= button %>
                    <% } else { %>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{$close}</button>
                    <% } %>
                </div>
            </div>
        </div>
    </div>
</script>

PLAIN
);
    unset($js);
}

/**
 * Render Admin JS Footer
 */
function hook_admin_js_footer()
{
    if (!is_admin_login_page()) {
        hook_add('assets_admin_print_footer_scripts', 'hook_admin_login_js');
        hook_add('assets_admin_print_footer_scripts', 'hook_admin_modal_js');
    }
}

function hook_admin_last_footer()
{
    $data = [
        'time' => [
            'start' => MICRO_TIME_FLOAT*1000,
            'end' => microtime(true)*1000,
        ],
        'memory' => [
            'peak' => [
                'emalloc' => memory_get_peak_usage(),
                'real' => memory_get_peak_usage(true),
            ],
            'usage' => [
                'emalloc' => memory_get_usage(),
                'real' => memory_get_usage(true),
            ]
        ]
    ];
    render("<script id='data-benchmark-script' data-benchmark='".esc_attr(json_ns($data))."'></script>\n");
    unset($data);
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

function hook_admin_footer_bottom()
{
    $id = 'random-bench-'.random_char(20, '0123456789abcdefghijklmnopqrstuvwxyz');
?>
    <div class="copy">
        <div class="float-md-left text-center text-md-left mt-md-0 mt-3">
            <div class="clock-time text-muted small">
                <span data-clock="text" data-format="D MMMM YYYY [-] H:mm:ss [(%location%)]"></span>
            </div>
            <div class="d-block text-muted" id="<?= $id;?>"></div>
        </div>
        <div class="float-md-right align-text-bottom mt-md-0 mt-3">
            <div class="text-muted text-center text-md-right align-text-bottom">
                <small><strong><?= APP_NAME;?></strong> - <?= trans('Version');?> : <?= VERSION;?></small>
            </div>
        </div>
        <script type="text/template" id="<?= $id;?>-template">
            <div class="small mt-1">
                <span class="copy-benchmark-name font-weight-bolder"><?= esc_html('Memory Usage:');?></span>
                <span class="copy-benchmark-value"><%= Math.round(
                        memory.usage.emalloc / 1024 / 1024 * 10000
                    ) / 10000 %> MB</span>
                |
                <span class="copy-benchmark-name font-weight-bolder"><?= esc_html('Memory Peak Usage:');?></span>
                <span class="copy-benchmark-value"><%= Math.round(
                        memory.peak.emalloc / 1024 / 1024 * 10000
                    ) / 10000 %> MB</span>
                <span class="copy-benchmark-name font-weight-bolder"><?= esc_html('Rendered In:');?></span>
                <span class="copy-benchmark-value"><%= Math.round(
                        (time.end - time.start) * 100
                    ) / 100000 %> <?= esc_html('Second');?></span>
            </div>
        </script>
        <script type="text/javascript">
            (function ($) {
                if (!$) return;
                var identifier = <?= json_ns($id);?>;
                $(document).ready(function () {
                    if (!_) return;

                    var $_ = $('script#data-benchmark-script').data('benchmark'),
                        $tpl = $('script#'+identifier+'-template').html();
                    if (!$tpl || $tpl.trim() === '' || !$_ || typeof $_ !== 'object') {
                        return;
                    }
                    $('#'+identifier).html(_.template($tpl)($_));
                })
            })(window.jQuery);
        </script>
    </div>
<?php
}

/**
 * Hook Add Login Notice
 */
function hook_admin_init_login()
{
    hook_remove('admin_init', 'hook_admin_init_login');
    $is_exists = has_cookie_succeed();
    if (!$is_exists
        || query_param(PARAM_LOGIN) !== 'success'
        || !is_numeric(query_param(PARAM_USER_ID))
    ) {
        return;
    }

    $user_id = query_param_int(PARAM_USER_ID);
    $current_id = get_current_user_id();
    if ($current_id > 0 && $user_id === $current_id) {
        add_admin_success_message(
            'login_success',
            trans('You have successfully logged in')
        );
    }
}

/**
 * @param array $classes
 * @return array
 */
function hook_admin_body_class(array $classes) : array
{
    // remove hooks
    hook_remove('admin_body_class', __FUNCTION__);
    if (is_true_value(cookie('sidebar-closed'))) {
        $classes[] = 'sidebar-closed';
    }

    return $classes;
}

// add admin render

hook_add('admin_html_head', 'hook_admin_meta_header', 1);
hook_add('admin_html_head', 'hook_admin_js_header', 1);
hook_add('admin_html_footer', 'hook_admin_js_footer', 1);
hook_add('admin_init', 'hook_admin_init_login', 1);
hook_add('admin_body_class', 'hook_admin_body_class'); // sidebar
hook_add('admin_top_message', 'render_admin_message'); // html message
hook_add('assets_admin_enqueue_scripts', 'hook_admin_default_assets', 20);
hook_add('admin_footer_bottom_html', 'hook_admin_footer_bottom', 20);
hook_add('admin_html_footer', 'hook_admin_last_footer', 9999);

// class hooks
require_once __DIR__ .'/hooks/classes.php';
