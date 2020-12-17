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
        <?php if (!is_admin_login_page()) : ?>
        const ping_url = <?= json_encode(get_api_url('/ping'), JSON_UNESCAPED_SLASHES);?>,
            login_url = <?= json_encode(get_admin_login_url(), JSON_UNESCAPED_SLASHES);?>,
            user_id = <?= get_current_user_id();?>;
        var translation_text = <?= json_encode(
            [
                'You seem to be offline' => trans('You seem to be offline'),
                'Reconnecting...' => trans('Reconnecting...'),
            ],
            JSON_UNESCAPED_SLASHES
        );?>;
        <?php endif;?>
    </script>
    <?php admin_html_head(); ?>
</head>
<body<?= get_admin_body_attributes(); ?>>
<div id="page">
    <?php hook_run('admin_body_open'); ?>
    <?php if (!is_admin_login_page()) : ?>
    <div id="left-area"<?php if (cookie('sidebar_closed') === 'true') {
        echo ' class="closed"';
    } ?>>
        <div id="admin-sidebar">
            <?php
            if (hook_apply('enable_admin_sidebar_logo', true) === true) :
                $logo = get_site_logo();
                if (is_array($logo) && is_string(($logo['url'] ?? null))) :
                    ?>
                    <div class="admin-logo">
                        <img src="<?= htmlspecialchars($logo['url'], ENT_QUOTES | ENT_COMPAT); ?>" class="admin-logo"
                             alt="logo">
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
        <div class="admin-top-bar">
            <div class="top-bar">
                <?=
                // admin top menu
                admin_top_bar_menu_navigation();
                ?>
                <ul class="navbar-nav navbar-account">
                    <li>
                        <label for="account-top-bar"><?= trans('Account'); ?></label>
                        <input type="checkbox" id="account-top-bar" class="hide">
                        <ul>
                            <li class="profile-picture">
                                <div class="img-avatar">
                                    <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png"
                                         alt="avatar">
                                </div>
                            </li>
                            <li>
                                <?= htmlentities(get_current_supervisor_full_name()); ?>
                            </li>
                            <li>
                                <a class="logout-link" href="<?= get_admin_url('logout.php'); ?>"
                                   onclick="return confirm(<?= htmlspecialchars(json_encode(trans('Are You Sure ... ?')),
                                       ENT_QUOTES | ENT_COMPAT); ?>)"><?= trans('Logout'); ?></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div id="global-message"></div>
        <div class="admin-title">
            <h2 class="page-title"><?= htmlentities(get_admin_title()); ?></h2>
        </div>
        <?php endif; ?>
