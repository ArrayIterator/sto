<?php
if (!defined('ADMIN_AREA')) {
    return;
}
?><!DOCTYPE html>
<html<?= get_admin_html_attributes(); ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript">
        document.documentElement.className = document.documentElement.className.replace('no-js', 'js');
    </script>
    <?php admin_html_head(); ?>
</head>
<body<?= get_admin_body_attributes(); ?>>
<div id="page">
    <?php hook_run('admin_body_open'); ?>
    <?php if (!is_admin_login_page()) : ?>
    <div id="left-area"<?php if (cookie('sidebar_closed') === 'true') {echo ' class="closed"';}?>>
        <div id="admin-sidebar">
            <?php
                if (hook_apply('enable_admin_sidebar_logo', true) === true) :
                    $logo = get_site_logo();
                    if (is_array($logo) && is_string(($logo['url']??null))) :
            ?>
            <div class="admin-logo">
                <img src="<?= htmlspecialchars($logo['url'], ENT_QUOTES|ENT_COMPAT);?>" class="admin-logo" alt="logo">
            </div>
            <?php
                    endif;
                endif;
            ?>

            <?= admin_sidebar_menu_navigation(); ?>
        </div>
        <div id="sidebar-switch"><i class="icofont-listing-box"></i></div>
    </div>
    <div id="right-area">
    <?php endif; ?>
