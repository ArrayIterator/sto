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
?>
    <script type="text/javascript">
        document.documentElement.className = document.documentElement.className.replace('no-js', 'js');
<?php
if (!is_admin_login_page()) : ?>
        const
            ping_url  = <?= json_encode(get_api_url(StatusController::PING_PATH), JSON_UNESCAPED_SLASHES);?>,
            login_url = <?= json_encode(get_admin_login_url(), JSON_UNESCAPED_SLASHES);?>,
            user_id   = <?= get_current_user_id();?>;
<?php endif;?>
    </script>

<?php
}
// add admin render
hook_add('admin_html_head', 'hook_admin_js_header', 1);
hook_add('assets_admin_enqueue_scripts', 'hook_admin_default_assets');
hook_add('admin_html_footer', 'hook_admin_js_footer');
hook_add('admin_top_message', 'render_admin_message');
