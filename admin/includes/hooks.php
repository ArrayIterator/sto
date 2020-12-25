<?php

use ArrayIterator\Controller\Api\StatusController;

if (!defined('ADMIN_AREA')) {
    return;
}

/**
 * Default Assets
 */
function hook_admin_default_assets()
{
    hook_remove('assets_admin_enqueue_scripts', __FUNCTION__);
    $is_login_page = is_admin_login_page();
    assets_style_enqueue('admin');
    assets_script_enqueue($is_login_page ? 'admin-login' : 'admin');
    if (!$is_login_page) {
        assets_script_enqueue('moment');
    }
    if (!$is_login_page) {
        assets_style_enqueue('select2');
        assets_script_enqueue('select2');
    }
}

function hook_admin_login_js()
{
    $js = get_js_core_by_name('ping');
    if (!is_string($js)) {
        return;
    }
?>
<script type="text/javascript">
<?= $js; ?>

</script>
<?php
    unset($js);
}

function hook_admin_js_footer()
{
    if (!is_admin_login_page()) {
        hook_add('assets_admin_print_footer_scripts', 'hook_admin_login_js');
    }
}

function hook_admin_js_header()
{
    $tz   = timezone()->getDateTime('UTC');
    $time = (int) ceil(abs($tz->getTimestamp().$tz->format('.u'))*1000);
?>
    <script type="text/javascript">
        (function (w, d) {
            d.remove('no-js');
            d.add('js');
<?php if (!is_admin_login_page()) : ?>
            w.ping_url  = <?= json_ns(get_api_url(StatusController::PING_PATH));?>;
            w.login_url = <?= json_ns(get_admin_login_url());?>;
            w.user_id   = <?= get_current_user_id();?>;
<?php endif;?>
            w.cookie_domain = <?= json_ns(COOKIE_DOMAIN);?>;
            w.time_start_utc = <?= json_ns($time);?>;
            w.current_gmt_time = time_start_utc;
            w.current_date_string = <?= json_ns($tz->format('c'));?>;
            w.timezone_string = <?= json_ns(timezone_convert()->getName());?>;
            w.interval_time_stop = false;
            var _int = setInterval(function () {
                w.current_gmt_time += 1000;
                if (w.interval_time_stop === true) {
                    clearInterval(_int);
                }
            }, 1000);
        })(window, document.documentElement.classList);
    </script>

<?php
}

function hook_admin_meta_header()
{
?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">.hide-if-js{display:none}</style>
<?php
}

// add admin render
hook_add('admin_html_head', 'hook_admin_meta_header', 1);
hook_add('admin_html_head', 'hook_admin_js_header', 1);
hook_add('assets_admin_enqueue_scripts', 'hook_admin_default_assets');
hook_add('admin_html_footer', 'hook_admin_js_footer');
hook_add('admin_top_message', 'render_admin_message');
