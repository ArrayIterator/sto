<?php
// DENY DIRECT ACCESS
if (($_SERVER['SCRIPT_FILENAME'] ?? null) === __FILE__) {
    !headers_sent() && header('Location: ../', true, 302);
    exit(0);
}
if (!function_exists('hook_add')) {
    return;
}

// use limit as last init on high priority
hook_add('admin_init', 'init', 99999);

hook_add('assets_default_scripts', 'assets_default_scripts');
hook_add('assets_default_styles', 'assets_default_styles');

hook_add('html_head', 'render_title_tag', 1);
hook_add('admin_html_head', 'render_admin_title_tag', 1);

hook_add('html_head', 'assets_enqueue_scripts', 1);
hook_add('html_head', 'assets_print_styles', 8);
hook_add('html_head', 'assets_print_head_scripts', 9);

hook_add('admin_html_head', 'assets_admin_enqueue_scripts', 1);
hook_add('admin_html_head', 'assets_admin_print_styles', 8);
hook_add('admin_html_head', 'assets_print_head_scripts', 9);
hook_add('assets_admin_print_styles', 'do_assets_admin_print_styles', 9);

hook_add('html_footer', 'assets_print_footer_scripts', 20);
hook_add('html_footer', 'assets_print_footer_scripts', 20);
hook_add('print_footer_scripts', 'do_assets_footer_scripts');

// add admin
hook_add('assets_admin_enqueue_scripts', 'admin_default_assets');
