<?php

use ArrayIterator\Menu;
use ArrayIterator\Menus;

function admin_sidebar_menu_array(): array
{
    $menus = [
        'dashboard' => [
            'name' => trans('Dashboard'),
            'link' => get_admin_url(),
            'hide' => !admin_is_allowed('index.php'),
//            'icon' => 'dashboard',
            'icon' => 'speed-meter',
            'position' => 1,
            'menus' => [
                'dashboard' => [
                    'name' => trans('Dashboard'),
                    'link' => get_admin_url(),
                    'hide' => !admin_is_allowed('index.php'),
                    'position' => 1,
                ],
                'site-status' => [
                    'name' => trans('Site Status'),
                    'link' => get_admin_url('status.php'),
                    'hide' => !admin_is_allowed('status.php'),
                    'position' => 10,
                ],
                'about' => [
                    'name' => trans('About'),
                    'link' => get_admin_url('about.php'),
                    'hide' => !admin_is_allowed('about.php'),
                    'position' => 20,
                ],
            ]
        ],
        'report' => [
            'name' => trans('Report'),
            'link' => get_admin_url('report.php'),
            'hide' => !admin_is_allowed('report.php'),
            'icon' => 'chart-line',
//            'icon' => 'signal',
            'position' => 100,
            'menus' => [],
        ],
        'classes' => [
            'name' => trans('Classes'),
            'link' => get_admin_url('classes.php'),
            'hide' => !admin_is_allowed('classes.php'),
            'icon' => 'institution',
            'position' => 150,
            'menus' => [
                'classes' => [
                    'name' => trans('All Classes'),
                    'link' => get_admin_url('classes.php'),
                    'hide' => !admin_is_allowed('classes.php'),
                    'position' => 150,
                ],
                'class_new' => [
                    'name' => trans('Add New Class'),
                    'link' => get_admin_url('class-new.php'),
                    'hide' => !admin_is_allowed('class-new.php'),
                    'position' => 150,
                ],
            ],
        ],
        // EXAMS
        'tasks' => [
            'name' => trans('Tasks'),
            'link' => get_admin_url('tasks.php'),
            'hide' => !admin_is_allowed('tasks.php'),
            'icon' => 'tasks',
            'position' => 200,
            'menus' => [
                'tasks' => [
                    'name' => trans('All Tasks'),
                    'link' => get_admin_url('tasks.php'),
                    'hide' => !admin_is_allowed('tasks.php'),
                    'position' => 200,
                ],
                'new_task' => [
                    'name' => trans('Add New Task'),
                    'link' => get_admin_url('task-new.php'),
                    'hide' => !admin_is_allowed('task-new.php'),
                    'position' => 220,
                ],
            ]
        ],
        'questions' => [
            'name' => trans('Questions'),
            'link' => get_admin_url('questions.php'),
            'hide' => !admin_is_allowed('questions.php'),
            'icon' => 'unique-idea',
//            'icon' => 'question',
//            'icon' => 'question-circle',
//            'icon' => 'ruler-pencil',
            'position' => 300,
            'menus' => [
                'tasks' => [
                    'name' => trans('All Questions'),
                    'link' => get_admin_url('questions.php'),
                    'hide' => !admin_is_allowed('questions.php'),
                    'position' => 300,
                ],
                'new_task' => [
                    'name' => trans('Add New Question'),
                    'link' => get_admin_url('question-new.php'),
                    'hide' => !admin_is_allowed('question-new.php'),
                    'position' => 320,
                ],
            ]
        ],
        'exams' => [
            'name' => trans('Exams'),
            'link' => get_admin_url('exams.php'),
            'hide' => !admin_is_allowed('exams.php'),
            'icon' => 'ruler-pencil',
            'position' => 350,
            'menus' => [
                'tasks' => [
                    'name' => trans('All Exam Schedules'),
                    'link' => get_admin_url('exams.php'),
                    'hide' => !admin_is_allowed('exams.php'),
                    'position' => 350,
                ],
                'new_task' => [
                    'name' => trans('Add New Exam'),
                    'link' => get_admin_url('exam-new.php'),
                    'hide' => !admin_is_allowed('exam-new.php'),
                    'position' => 360,
                ],
                'rooms' => [
                    'name' => trans('All Exam Rooms'),
                    'link' => get_admin_url('rooms.php'),
                    'hide' => !admin_is_allowed('rooms.php'),
                    'position' => 370,
                ],
                'new_room' => [
                    'name' => trans('Add New Room'),
                    'link' => get_admin_url('room-new.php'),
                    'hide' => !admin_is_allowed('room-new.php'),
                    'position' => 380,
                ],
            ]
        ],

        // USERS
        'teachers' => [
            'name' => trans('Teachers'),
            'link' => get_admin_url('teachers.php'),
            'hide' => !admin_is_allowed('teachers.php'),
            'icon' => 'teacher',
            'position' => 400,
            'menus' => [
                'teachers' => [
                    'name' => trans('All Teachers'),
                    'link' => get_admin_url('teachers.php'),
                    'hide' => !admin_is_allowed('teachers.php'),
                    'position' => 400,
                ],
                'new_teacher' => [
                    'name' => trans('Add New Teacher'),
                    'link' => get_admin_url('teacher-new.php'),
                    'hide' => !admin_is_allowed('teacher-new.php'),
                    'position' => 420,
                ],
            ],
        ],
        'students' => [
            'name' => trans('Students'),
            'link' => get_admin_url('students.php'),
            'hide' => !admin_is_allowed('students.php'),
            'icon' => 'group-students',
            'position' => 450,
            'menus' => [
                'students' => [
                    'name' => trans('All Students'),
                    'link' => get_admin_url('students.php'),
                    'hide' => !admin_is_allowed('students.php'),
                    'position' => 450,
                ],
                'new_student' => [
                    'name' => trans('Add New Student'),
                    'link' => get_admin_url('student-new.php'),
                    'hide' => !admin_is_allowed('student-new.php'),
                    'position' => 470,
                ],
            ]
        ],
        'invigilator' => [
            'name' => trans('Invigilators'),
            'link' => get_admin_url('invigilators.php'),
            'hide' => !admin_is_allowed('invigilators.php'),
            'icon' => 'investigator',
            'position' => 500,
            'menus' => [
                'students' => [
                    'name' => trans('All Invigilators'),
                    'link' => get_admin_url('invigilators.php'),
                    'hide' => !admin_is_allowed('invigilators.php'),
                    'position' => 500,
                ],
                'new_student' => [
                    'name' => trans('Add New Invigilator'),
                    'link' => get_admin_url('invigilator-new.php'),
                    'hide' => !admin_is_allowed('invigilator-new.php'),
                    'position' => 520,
                ],
            ]
        ],
        'tools' => [
            'name' => trans('Tools'),
            'link' => get_admin_url('tools.php'),
            'hide' => !admin_is_allowed('tools.php'),
            'icon' => 'tools-alt-2',
//            'icon' => 'tools',
            'position' => 600,
            'menus' => [

            ]
        ],
        // SITE SETUP
        'modules' => [
            'name' => trans('Modules'),
            'link' => get_admin_url('modules.php'),
            'hide' => !admin_is_allowed('modules.php'),
//            'icon' => 'addons',
            'icon' => 'plugin',
            'position' => 700,
            'menus' => [
                'all_modules' => [
                    'name' => trans('All Modules'),
                    'link' => get_admin_url('modules.php'),
                    'hide' => !admin_is_allowed('modules.php'),
                    'position' => 700,
                ],
                'active_modules' => [
                    'name' => trans('Active Modules'),
                    'link' => get_admin_url('modules.php?status=active'),
                    'hide' => !admin_is_allowed('modules.php'),
                    'position' => 720,
                ],
                'inactive_modules' => [
                    'name' => trans('Inactive Modules'),
                    'link' => get_admin_url('modules.php?status=inactive'),
                    'hide' => !admin_is_allowed('modules.php'),
                    'position' => 730,
                ],
            ]
        ],
        'themes' => [
            'name' => trans('Themes'),
            'link' => get_admin_url('themes.php'),
            'hide' => !admin_is_allowed('themes.php'),
            'icon' => 'paint',
            'position' => 800,
        ],
        'settings' => [
            'name' => trans('Settings'),
            'link' => get_admin_url('settings.php'),
            'hide' => !admin_is_allowed('settings.php'),
//            'icon' => 'gears',
            'icon' => 'settings',
            'position' => 900,
            'menus' => [
                'settings' => [
                    'name' => trans('General Settings'),
                    'link' => get_admin_url('settings.php'),
                    'hide' => !admin_is_allowed('settings.php'),
                    'position' => 900,
                ],
                'admin-settings' => [
                    'name' => trans('Global Settings'),
                    'link' => get_admin_url('admin.php'),
                    'hide' => !admin_is_allowed('admin.php'),
                    'position' => 930,
                ]
            ]
        ],
        'profile' => [
            'name' => trans('Accounts'),
            'link' => get_admin_url('profile.php'),
            'hide' => !admin_is_allowed('profile.php'),
            'icon' => 'key',
            'position' => 1100,
            'menus' => [
                'profile' => [
                    'name' => trans('Profile'),
                    'link' => get_admin_url('profile.php'),
                    'hide' => !admin_is_allowed('profile.php'),
                    'position' => 1100
                ],
                'logout' => [
                    'name' => trans('Logout'),
                    'link' => get_admin_url('logout.php'),
                    'hide' => false,
                    'icon' => 'power',
                    'attr' => [
                        'class' => 'logout color-red',
                        'onclick' => 'return confirm(' . json_encode(trans('Are You Sure ... ?')) . ');'
                    ],
                    'position' => 1100
                ],
            ]
        ],
    ];

    return hook_apply('admin_sidebar_menu_array', $menus);
}

/**
 * @return array
 */
function admin_top_bar_menu_array(): array
{
    return hook_apply('admin_top_bar_menu_array', []);
}

/**
 * @return Menus
 */
function admin_sidebar_menu(): Menus
{
    /**
     * @var Menus $menu
     */
    $menu = hook_apply(
        'admin_sidebar_menu',
        menus()->fromArray(admin_sidebar_menu_array())
    );
    $menu->setSiteUrl(get_admin_url());
    return $menu;
}

/**
 * @return Menus
 */
function admin_top_bar_menu(): Menus
{
    $menu = hook_apply(
        'admin_top_bar_menu',
        menus()->fromArray(admin_top_bar_menu_array())
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

    if (rtrim($menu->getUrl(), '/') . '/index.php' === $currentUrl) {
        $classes = $menu->getLinkAttributes()['class'] ?? '';
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
        '#/' . preg_quote(get_admin_base_name_file()) . '$#',
        explode('?', $url)[0]
    )) {
        $classes = $parentMenu->getAttributes()['class'] ?? '';
        $classes .= ' has-active-submenu';
        $parentMenu->setAttributes('class', $classes);
        if (get_admin_base_name_file() === 'modules.php') {
            $classes = $menu->getLinkAttributes()['class'] ?? '';
            $args = explode('?', $menu->getUrl());
            array_shift($args);
            $args = implode('?', $args);
            parse_str($args, $q);
            if (($q['status']??null) === query_param('status')) {
                $classes .= ' current-menu active';
                $menu->setLinkAttribute('class', $classes);
                $classes = $menu->getAttributes()['class'] ?? '';
                $classes .= ' has-active-submenu';
                $menu->setAttributes('class', $classes);
            } else {
                $parentMenu->setLinkAttribute('class', $classes);
            }
        } else {
            $classes = $menu->getLinkAttributes()['class'] ?? '';
            $classes .= ' current-menu active';
            $menu->setLinkAttribute('class', $classes);

            $classes = $menu->getAttributes()['class'] ?? '';
            $classes .= ' has-active-submenu';
            $menu->setAttributes('class', $classes);
        }
    }

    return hook_apply(
        'admin_sidebar_menu_callback',
        $menu,
        $maxDepth,
        $deep,
        $currentTag,
        $currentUrl,
        $parentMenu,
        $menus
    );
}

/**
 * @param callable|null $callback
 * @return string
 */
function admin_sidebar_menu_navigation(callable $callback = null): string
{
    $menu = admin_sidebar_menu()->build(
        'ul',
        [
            'id' => 'navigation-sidebar',
            'class' => 'sidebar-menu nav-menu admin-sidebar-menu'
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

/**
 * @param callable|null $callback
 * @return string
 */
function admin_top_bar_menu_navigation(callable $callback = null): string
{
    $menu = admin_top_bar_menu()->build(
        'ul',
        [
            'id' => 'navigation-top',
            'class' => 'top-menu nav-menu admin-top-menu'
        ],
        4,
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
