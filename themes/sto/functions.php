<?php
// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

function sto_render_script()
{
    hook_remove('assets_enqueue_scripts', 'sto_render_script');
    assets_style_add('theme-css', get_css_url(), ['bootstrap']);
    assets_style_enqueue('icofont');
    assets_script_enqueue('bootstrap');
    assets_style_enqueue('theme-css');
}

hook_add('assets_enqueue_scripts', 'sto_render_script');
