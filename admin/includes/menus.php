<?php

use ArrayIterator\Menu;
use ArrayIterator\Menus;

function admin_menu_array() : array
{
    $menus = [
        'dashboard' => [
            'name' => trans('Dashboard'),
            'link' => get_admin_url(),
            'hide' => !admin_is_allowed('index.php'),
            'menus' => [
                'dashboard' => [
                    'name' => trans('Dashboard'),
                    'link' => get_admin_url(),
                    'hide' => !admin_is_allowed('index.php'),
                ],
                'about' => [
                    'name' => trans('About'),
                    'link' => get_admin_url('about.php'),
                    'hide' => !admin_is_allowed('index.php'),
                ],
            ]
        ],
        'modules' => [
            'name' => trans('Modules'),
            'link' => get_admin_url('modules.php'),
            'hide' => !admin_is_allowed('modules.php'),
            'menus' => [
                'all_modules' => [
                    'name' => trans('All Modules'),
                    'link' => get_admin_url('modules.php'),
                    'hide' => !admin_is_allowed('modules.php'),
                ],
                'active_modules' => [
                    'name' => trans('Active Modules'),
                    'link' => get_admin_url('modules.php?status=active'),
                    'hide' => !admin_is_allowed('modules.php'),
                ],
                'inactive_modules' => [
                    'name' => trans('Inactive Modules'),
                    'link' => get_admin_url('modules.php?status=inactive'),
                    'hide' => !admin_is_allowed('modules.php'),
                ],
            ]
        ],
        'users' => [
            'name' => trans('Users'),
            'link' => get_admin_url('profile.php'),
            'hide' => !admin_is_allowed('profile.php'),
            'menus' => [
                'profile' => [
                    'name' => trans('Profile'),
                    'link' => get_admin_url('profile.php'),
                    'hide' => !admin_is_allowed('profile.php'),
                ],
                'supervisors' => [
                    'name' => trans('Supervisors'),
                    'link' => get_admin_url('supervisors.php'),
                    'hide' => !admin_is_allowed('supervisors.php'),
                ],
                'students' => [
                    'name' => trans('Students'),
                    'link' => get_admin_url('students.php'),
                    'hide' => !admin_is_allowed('students.php'),
                ],
            ]
        ],
        'settings' => [
            'name' => trans('Settings'),
            'link' => get_admin_url('settings.php'),
            'hide' => !admin_is_allowed('settings'),
            'menus' => [
            ]
        ]
    ];

    return hook_apply('admin_menu_array', $menus);
}

/**
 * @return Menus
 */
function admin_sidebar_menu() : Menus
{
    /**
     * @var Menus $menu
     */
    $menu = hook_apply(
        'admin_sidebar_menu',
        \menus()->fromArray(admin_menu_array())
    );
    $menu->setSiteUrl(get_admin_url());
    return $menu;
}

/**
 * @param Menu $menu
 * @param int $maxDepth
 * @param int $deep
 * @param string $currentTag
 * @param string $currentUrl
 * @param Menu $parentMenu
 * @param Menus $menus
 * @return Menu|false
 */
function admin_sidebar_menu_callback(
    Menu $menu,
    int $maxDepth,
    int $deep,
    string $currentTag,
    string $currentUrl,
    Menu $parentMenu,
    Menus $menus
) {
    if (!is_admin_login() || !$menu->isShown()) {
        return false;
    }

    if (rtrim($menu->getUrl(), '/').'/index.php' === $currentUrl) {
        $classes = $menu->getLinkAttributes()['class']??'';
        $classes .= ' active current-menu';
        $menu->setLinkAttribute('class', $classes);
    }

    if (!is_admin()) {
        switch ($menu->getId()) {
            case 'modules':
            case 'settings':
            case 'themes':
                return false;
        }

        if (!is_admin_active()) {
            if ($menu->getId() === 'users') {
                $menu->setName(trans('Profile'));
            }
            if ($menu->getId() === 'dashboard') {
                $menu->setUrl(get_admin_url('quarantined.php'));
            }

            return in_array($menu->getId(), ['dashboard', 'users'])
                ? (
                $menu->getId() === 'dashboard'
                && $deep !== 0
                    ? false
                    : $menu
                )
                : false;
        }
        if ($menu->getId() === 'supervisors') {
            if (is_teacher() && !teacher_can_see_supervisor()) {
                return false;
            }
            if (is_invigilator() && !invigilator_can_see_supervisor()) {
                return false;
            }
        }
    }
    $url = $menu->getUrl();
    if ($url === get_admin_url()) {
        $url = get_admin_url('index.php');
    }
    if (preg_match(
        '#/'.preg_quote(get_admin_base_name_file()).'$#',
        explode('?', $url)[0]
    )) {
        $classes = $parentMenu->getAttributes()['class']??'';
        $classes .= ' has-active-submenu';
        $parentMenu->setAttributes('class', $classes);
        if (get_admin_base_name_file() === 'modules.php') {
            $classes = $menu->getLinkAttributes()['class']??'';
            $args = explode('?', $menu->getUrl());
            array_shift($args);
            $args = implode('?', $args);
            parse_str($args, $q);
            if ($q === query_param('status')) {
                $menu->setLinkAttribute('class', $classes);
                $classes = $menu->getAttributes()['class']??'';
                $classes .= ' has-active-submenu';
                $menu->setAttributes('class', $classes);
            } else {
                $parentMenu->setLinkAttribute('class', $classes);
            }
        } else {

            $classes = $menu->getLinkAttributes()['class']??'';
            $classes.= ' current-menu active';
            $menu->setLinkAttribute('class', $classes);

            $classes = $menu->getAttributes()['class']??'';
            $classes .= ' has-active-submenu';
            $menu->setAttributes('class', $classes);
        }
    }

    return $menu;
}

/**
 * @return string
 */
function admin_sidebar_menu_navigation(callable $callback = null) : string
{
    $menu = admin_sidebar_menu()->build(
        'ul',
        [
            'id' => 'navigation-sidebar',
            'class'=> 'sidebar-menu nav-menu admin-sidebar-menu'
        ],
        1,
        true,
        'admin_sidebar_menu_callback',
        get_current_url()
    );
    $newMenu = $callback ? $callback($menu) : $menu;
    if (!is_array($newMenu)) {
        $newMenu = $menu;
    }
    unset($menu);
    return implode("\n", $newMenu);
}
