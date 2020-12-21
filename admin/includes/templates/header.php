<?php
if (!defined('ADMIN_AREA')) {
    return;
}

?><!DOCTYPE html>
<html<?= get_admin_html_attributes(); ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">.hide-if-js{display:none}</style>
    <?php admin_html_head(); ?>
</head>
<body<?= get_admin_body_attributes(); ?>>
<div id="page"<?php if (cookie('sidebar_closed') === 'true') {
    echo ' class="sidebar-closed"';
} ?>>
    <?php admin_body_open(); ?>
    <?php if (!is_admin_login_page()) : ?>
    <div id="left-area">
        <div id="admin-sidebar">
            <div class="admin-logo">
            <?php
            $hasImage = false;
            if (hook_apply('enable_admin_sidebar_logo', true) === true) :
                $logo = get_site_logo();
                if (is_array($logo) && is_string(($logo['url'] ?? null))) :
                    $hasImage = true;
                    ?>
                        <img src="<?= esc_attr($logo['url']); ?>" class="admin-logo" alt="logo">
                <?php
                endif;
            endif;
            if (!$hasImage) : ?>
                <div class="logo-text">
                    <a href="<?= get_admin_url();?>"><?php esc_html_e(APP_SHORT_NAME);?></a>
                </div>
            <?php endif;?>
            </div>
            <?= admin_sidebar_menu_navigation(); ?>
        </div>
    </div>
    <div id="right-area">
        <div class="admin-top-bar">
            <div id="sidebar-switch">
                <div class="switcher" title="<?php esc_attr_trans_e('Toggle Sidebar');?>">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="top-bar">
                <?=
                // admin top menu
                admin_top_bar_menu_navigation();
                ?>
                <ul class="navbar-nav navbar-account">
                    <li class="notification-menu">
                        <label for="account-notification-bar" title="<?php esc_attr_trans_e('Notifications');?>"><i class="icofont-alarm"></i></label>
                        <input type="checkbox" id="account-notification-bar">
                        <ul class="notification-menu-list"></ul>
                    </li>
                    <li class="account-info-menu">
                        <label for="account-top-bar" title="<?php esc_attr_trans_e('Account');?>"><i class="icofont-user-alt-3"></i></label>
                        <input type="checkbox" id="account-top-bar" class="hide">
                        <ul>
                            <li class="profile-picture">
                                <div class="img-avatar">
                                    <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png"
                                         alt="avatar">
                                </div>
                            </li>
                            <li>
                                <?= esc_html(get_current_supervisor_full_name()); ?>
                            </li>
                            <li>
                                <a class="logout-link" href="<?= get_admin_url('logout.php'); ?>"
                                   onclick="return confirm(<?= esc_attr(json_encode(trans('Are You Sure ... ?'))); ?>)"><?= trans('Logout'); ?></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div class="hide-if-js mt-2">
            <div class="alert alert-danger">
                <?php esc_html_trans_e('Please Enable Javascript');?>
            </div>
        </div>
        <div id="global-message" class="mt-2"></div>
        <div id="top-message" class="mt-2"><?php hook_run('admin_top_message');?></div>
        <div class="admin-title">
            <h2 class="page-title"><?php esc_attr_trans_e(get_admin_title()); ?></h2>
        </div>
        <?php endif; ?>
        <div id="content" class="page-content">
