<?php

/* -------------------------------------------------
 *                     ADMIN
 * ------------------------------------------------*/

/**
 * @return string
 */
function get_admin_html_attributes(): string
{
    $html_class = hook_apply('admin_html_class', ['no-js']);
    $html_class = array_filter(array_unique($html_class));
    $attribute = [
        'lang' => get_selected_site_language(),
        'class' => implode(
            ' ',
            normalize_html_class($html_class)
        ),
    ];

    $attribute = hook_apply('admin_html_attribute', $attribute);
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            esc_attr($key),
            esc_attr($item)
        );
    }
    return $attr;
}

/**
 * @return string
 */
function get_admin_login_form_attributes(): string
{
    $body_class = ['login-form', 'admin-login-form'];
    $attribute = [
        'class' => $body_class,
        'id' => 'login-form',
        'method' => 'post',
        'action' => get_current_admin_login_url(),
    ];
    $body_class = hook_apply('admin_login_form_class', $body_class, $attribute);
    $attribute['class'] = implode(
        ' ',
        normalize_html_class($body_class)
    );
    $attribute = hook_apply('admin_login_form_attributes', $attribute);
    if (isset($attribute['id'])) {
        $attribute['id'] = normalize_html_class($attribute['id']);
    }
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            esc_attr($key),
            esc_attr($item)
        );
    }

    return $attr;
}

/**
 * @return string
 */
function get_admin_body_attributes(): string
{
    $body_class = [];
    $isLogin = is_login();
    $userId = $isLogin ? get_current_user_id() : null;
    if ($isLogin) {
        $body_class[] = 'logged';
        $body_class[] = sprintf('user-id-%d', $userId);
        $body_class[] = sprintf('user-%s', get_current_user_type());
        $body_class[] = sprintf('user-status-%s', get_current_user_status());
        if (is_admin_page()) {
            $body_class[] = 'admin-page';
            $body_class[] = sprintf('user-role-%s', get_current_supervisor_role());
        }
    } else {
        $body_class[] = 'guess';
        if (is_admin_login_page()) {
            $body_class[] = 'login-page';
        }
    }

    $siteId = get_current_site_id();
    $attribute = [
        'class' => $body_class,
        'data-site-id' => $siteId,
    ];
    $body_class = hook_apply('admin_body_class', $body_class, $attribute);
    $body_class[] = sprintf('current-site-%d', $siteId);
    $body_class = array_filter(array_unique($body_class));
    $attribute['class'] = implode(' ', normalize_html_class($body_class));

    if ($isLogin) {
        $attribute['data-user-id'] = $userId;
    }
    $attribute = hook_apply('body_attributes', $attribute);
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            esc_attr($key),
            esc_attr($item)
        );
    }
    return $attr;
}


/**
 * @param string $file
 * @return bool
 */
function load_admin_template(string $file): bool
{
    if (substr($file, -4) !== '.php') {
        $file .= '.php';
    }
    $path = normalize_path(get_admin_includes_directory() . '/templates/');
    $file = normalize_path($file);
    if (strpos($file, $path) !== 0) {
        $file = $path . $file;
    }

    if (file_exists($file)) {
        /** @noinspection PhpIncludeInspection */
        require $file;
        return true;
    }

    return false;
}

/**
 * @param bool $reLoad
 */
function get_admin_header_template(bool $reLoad = false)
{
    static $loaded;
    if (!$reLoad && $loaded) {
        return;
    }

    if (!$loaded) {
        ob_start(function ($e, $y) {
            return hook_apply('ob_header', $e, $y);
        });
    }

    $loaded = true;
    load_admin_template('header');
}

/**
 * @param bool $reLoad
 */
function get_admin_footer_template(bool $reLoad = false)
{
    static $loaded;
    if (!$reLoad && $loaded) {
        return;
    }
    hook_run('before_admin_footer_template', $loaded);
    load_admin_template('footer');
    hook_run('after_admin_footer_template', $loaded);
    $loaded = true;
}

/**
 * @return string
 */
function &global_admin_title() : string
{
    static $admin_title = null;
    if (!is_string($admin_title)) {
        $admin_title = 'Dashboard';
    }

    return $admin_title;
}

function &global_title() : string
{
    static $title;

    if (!is_string($title)) {
        $title = get_option('site_title', null, get_current_site_id(), $found);
        if (!$found || !is_string($title)) {
            $title = '';
        }
        if (is_404()) {
            $title = hook_apply('title_404', ('404 Page Not Found'));
        } elseif (is_405()) {
            $title = hook_apply('title_405', ('405 Method Not Allowed'));
        }
    }

    return $title;
}

/**
 * @return string
 */
function get_admin_title(): string
{
    $admin_title =& global_admin_title();
    $title = is_admin_login() ? $admin_title : get_admin_login_title();
    $admin_title = (string)hook_apply('admin_title', $title);
    return $admin_title;
}

/**
 * @param string $admin_title
 */
function set_admin_title(string $admin_title)
{
    $title =& global_admin_title();
    $title = $admin_title;
}

/**
 * Hook render title
 */
function render_admin_title_tag()
{
    $title = get_admin_title();
    render('<title>' . esc_html_trans($title) . '</title>' . "\n");
}

/**
 * @return string
 */
function get_admin_login_title(): string
{
    return (string)hook_apply('admin_login_title', 'Login To Admin Area');
}

/**
 * @param string|null $float
 * @return string
 */
function get_admin_button_submit_login(string $float = null): string
{
    $newFloat = $float === 'right' ? 'float-right' : ($float === 'left' ? 'float-left' : '');
    return hook_apply(
        'admin_button_submit_login',
        sprintf(
            '<button type="submit" class="btn-primary btn btn-block admin-submit-button%s">%s</button>',
            $newFloat !== '' ? " {$newFloat}" : '',
            trans('Sign In')
        ),
        $float
    );
}

/**
 * @param string|null $float
 * @return string
 */
function get_button_submit(string $float = null): string
{
    $newFloat = $float === 'right' ? 'float-right' : ($float === 'left' ? 'float-left' : '');
    return hook_apply(
        'button_submit',
        sprintf(
            '<button type="submit" class="btn-primary btn admin-submit-button%s">%s</button>',
            $newFloat !== '' ? " {$newFloat}" : '',
            trans('Submit')
        ),
        $float
    );
}

/**
 * @param string|null $float
 * @return string
 */
function get_button_submit_small(string $float = null): string
{
    $newFloat = $float === 'right' ? 'float-right' : ($float === 'left' ? 'float-left' : '');
    return hook_apply(
        'button_submit_small',
        sprintf(
            '<button type="submit" class="btn-primary btn btn-sm admin-submit-button%s">%s</button>',
            $newFloat !== '' ? " {$newFloat}" : '',
            trans('Submit')
        ),
        $float
    );
}

/**
 * @param string|null $float
 * @return string
 */
function get_button_reset(string $float = null): string
{
    $newFloat = $float === 'right' ? 'float-right' : ($float === 'left' ? 'float-left' : '');
    return hook_apply(
        'button_reset',
        sprintf(
            '<button type="reset" class="btn-secondary btn btn-block admin-submit-button%s">%s</button>',
            $newFloat !== '' ? " {$newFloat}" : '',
            trans('Reset')
        ),
        $float
    );
}

/**
 * @return string
 */
function get_button_submit_login(): string
{
    return hook_apply(
        'button_submit_login',
        sprintf(
            '<button type="submit" class="btn-primary btn btn-block admin-submit-button">%s</button>',
            trans('Sign In')
        )
    );
}

/**
 *
 */
function the_admin_login_form()
{
    $interim_login = isset($_REQUEST['interim']);
    ?>
    <form<?= get_admin_login_form_attributes(); ?>>
        <?php hook_run('admin_login_form_before'); ?>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label for="username" class="input-group-text col-form-label" title="<?php esc_attr_trans_e('Username'); ?>">
                        <i class="icofont icofont-user-alt-3"></i>
                    </label>
                </div>
                <input class="form-control" name="username" placeholder="<?php esc_attr_trans_e('Username'); ?>" type="text" id="username" value="<?=
                    esc_attr((string)hook_apply('admin_input_username', (string)post('username')));
                ?>" required>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label for="password" class="input-group-text col-form-label"
                           title="<?php esc_attr_trans_e('Password'); ?>">
                        <i class="icofont icofont-lock"></i>
                    </label>
                </div>
                <input class="form-control" name="password" placeholder="<?php esc_attr_trans_e('Password'); ?>" type="password" id="password" value="" required>
                <div class="input-group-append">
                    <button type="button" class="input-group-text no-outline btn no-shadow">
                        <i class="icofont icofont-eye" data-target="#password"
                           data-switch='["icofont-eye", "icofont-eye-blocked"]'></i>
                    </button>
                </div>
            </div>
            <input type="hidden" name="token" class="hide" value="<?= get_token_hash();?>">
            <?php if ($interim_login) : ?>
                <input type="hidden" name="interim" class="hide" value="1">
            <?php endif;?>
            <div class="form-check checkbox-input admin-checkbox-input">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" value="yes"<?= hook_apply(
                            'remember_me',
                            post('remember') === 'yes') === true ? ' checked' : '';
                       ?>>
                <label class="form-check-label" for="remember"><?php trans_e('Remember Me'); ?></label>
            </div>
        </div>
        <?php hook_run('admin_login_form_after'); ?>

        <div class="form-group">
            <?= get_admin_button_submit_login(); ?>
        </div>
    </form>
    <script type="text/javascript"><?php echo get_js_core_by_name('input-switch'); ?></script>
    <?php
}

/**
 * Login Form
 */
function the_login_form()
{
    $interim_login = isset($_REQUEST['interim']);
    ?>
    <form<?= get_login_form_attributes(); ?>>
        <?php hook_run('login_form_before'); ?>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label for="username" class="input-group-text col-form-label"
                           title="<?php esc_attr_trans_e('Username'); ?>">
                        <i class="icofont icofont-user-alt-3"></i>
                    </label>
                </div>
                <input class="form-control" name="username" placeholder="<?php esc_attr_trans_e('Username'); ?>"
                       type="text" id="username" value="<?=
                esc_attr((string)hook_apply('input_username', (string)post('username')));
                ?>" required>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label for="password" class="input-group-text col-form-label"
                           title="<?php esc_attr_trans_e('Password'); ?>">
                        <i class="icofont icofont-lock"></i>
                    </label>
                </div>
                <input class="form-control" name="password" placeholder="<?php esc_attr_trans_e('Password'); ?>"
                       type="password" id="password" value="" required>
                <div class="input-group-append">
                    <button type="button" class="input-group-text no-outline btn no-shadow">
                        <i class="icofont icofont-eye" data-target="#password"
                           data-switch='["icofont-eye", "icofont-eye-blocked"]'></i>
                    </button>
                </div>
            </div>
            <input type="hidden" name="token" class="hide" value="<?= get_token_hash();?>">
            <?php if ($interim_login) : ?>
                <input type="hidden" name="interim" class="hide" value="1">
            <?php endif;?>
            <div class="form-check checkbox-input">
                <input type="checkbox" name="remember" id="remember" class="form-check-input"
                       value="yes"<?= hook_apply(
                           'remember_me',
                            post('remember') === 'yes') === true ? ' checked' : '';
                       ?>>
                <label class="form-check-label" for="remember"><?php trans_e('Remember Me'); ?></label>
            </div>
        </div>
        <?php hook_run('login_form_after'); ?>

        <div class="form-group">
            <?= get_button_submit_login(); ?>
        </div>
    </form>
    <script type="text/javascript">
        ;(function () {
            try {
                var $switch = document.querySelector('[data-switch]');
                var $target = document.querySelector('input' + $switch.getAttribute('data-target')),
                    sq = JSON.parse($switch.getAttribute('data-switch')),
                    $current = 0;
                if (!$target) {
                    return;
                }
                $switch.parentElement.addEventListener('click', function (e) {
                    e.preventDefault();
                    $target.setAttribute('type', $current === 0 ? 'text' : 'password');
                    $switch.classList.replace(
                        sq[$current],
                        sq[$current ? 0 : 1]
                    );
                    $current = $current ? 0 : 1;
                });
            } catch (e) {
                // pass
            }
        })(window);
    </script>
    <?php
}

function admin_html_head()
{
    hook_run('admin_html_head');
}

function admin_body_open()
{
    hook_run('admin_body_open');
}

function admin_html_footer()
{
    hook_run('admin_html_footer');
}

/* -------------------------------------------------
 *                     PUBLIC
 * ------------------------------------------------*/

/**
 * @return string
 */
function get_html_attributes(): string
{
    $html_class = hook_apply('html_class', ['no-js']);
    $html_class = array_filter(array_unique($html_class));
    $attribute = [
        'lang' => get_selected_site_language(),
        'class' => implode(
            ' ',
            normalize_html_class($html_class)
        ),
    ];

    $attribute = hook_apply('html_attribute', $attribute);
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            esc_attr($key),
            esc_attr($item)
        );
    }
    return $attr;
}

/**
 * @return string
 */
function get_body_attributes(): string
{
    $body_class = [];
    $isLogin = is_login();
    $userId = $isLogin ? get_current_user_id() : null;
    if ($isLogin) {
        $body_class[] = 'logged';
        $body_class[] = sprintf('user-id-%d', $userId);
        $body_class[] = sprintf('user-%s', get_current_user_type());
        $body_class[] = sprintf('user-status-%s', get_current_user_status() ?: 'unknown');
    } else {
        $body_class[] = 'guess';
        if (is_login_page()) {
            $body_class[] = 'login-page';
        }
    }

    if (is_404()) {
        $body_class[] = 'not-found-page';
    }

    $siteId = get_current_site_id();
    $attribute = [
        'class' => $body_class,
        'data-site-id' => $siteId,
    ];
    $body_class = hook_apply('body_class', $body_class);
    $body_class[] = sprintf('current-site-%d', $siteId);
    $body_class = array_filter(array_unique($body_class));
    $attribute['class'] = implode(
        ' ',
        normalize_html_class($body_class)
    );
    if ($isLogin) {
        $attribute['data-user-id'] = $userId;
    }

    $attribute = hook_apply('body_attributes', $attribute);
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            esc_attr($key),
            esc_attr($item)
        );
    }
    return $attr;
}

/**
 * @return string
 */
function get_login_form_attributes(): string
{
    $body_class = ['login-form'];
    $body_class = hook_apply('login_form_class', $body_class);
    $body_class = array_filter(array_unique($body_class));
    $attribute = [
        'class' => implode(
            ' ',
            normalize_html_class($body_class)
        ),
        'id' => 'login-form',
        'method' => 'post',
        'action' => get_login_url(),
    ];
    $attribute = hook_apply('login_form_attributes', $attribute);
    if (isset($attribute['id'])) {
        $attribute['id'] = normalize_html_class($attribute['id']);
    }
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            esc_attr($key),
            esc_attr($item)
        );
    }
    return $attr;
}

/**
 * @param string $file
 * @return bool
 */
function load_template(string $file): bool
{
    if (substr($file, -4) !== '.php') {
        $file .= '.php';
    }

    $theme = get_active_theme();
    $file2 = normalize_path($file);
    $path = slash_it(normalize_path($theme->getPath()));
    if (strpos($file2, $path) !== 0) {
        $file = $path . $file;
    }

    if (file_exists($file)) {
        /** @noinspection PhpIncludeInspection */
        require $file;
        return true;
    }

    return false;
}

/**
 * @return bool
 */
function get_header_template(): bool
{
    hook_run('before_header_template');
    $res = load_template('header');
    hook_run('after_header_template');
    return $res;
}

/**
 * @return bool
 */
function get_footer_template(): bool
{
    hook_run('before_footer_template');
    $res = load_template('footer');
    hook_run('after_footer_template', $res);
    return $res;
}

/**
 * @return string
 */
function get_title(): string
{
    $title =& global_title();
    if (is_404()) {
        $title = hook_apply('title_404', ('404 Page Not Found'));
    } elseif (is_405()) {
        $title = hook_apply('title_405', ('405 Method Not Allowed'));
    }

    $title = hook_apply('title', (string)$title);
    return $title;
}

/**
 * @param string $the_title
 */
function set_title(string $the_title)
{
    $title =& global_title();
    $title = $the_title;
}

/**
 * Hook render title
 */
function render_title_tag()
{
    $title = get_title();
    render('<title>' . esc_html_trans($title) . "</title>\n");
}

/* -------------------------------------------------
 *                     HOOK
 * ------------------------------------------------*/

function html_head()
{
    hook_run('html_head');
}

function body_open()
{
    hook_run('body_open');
}

function html_footer()
{
    hook_run('html_footer');
}

/* -------------------------------------------------
 *                     HELPER
 * ------------------------------------------------*/

/**
 * @param int $total_page
 * @param int $current_page
 * @param int $max_shown
 * @return array
 */
function calculate_get_page_list(
    int $total_page,
    int $current_page,
    int $max_shown
) : array {
    if ($total_page <= $max_shown) {
        // no breaks in list
        return range(1, $total_page);
    }

    if ($current_page <= $max_shown - 5) {
        // no break on left of page
        return array_merge(range(1, $max_shown-3), array(0, $total_page-1, $total_page));
    }
    if ($current_page >= $total_page - $max_shown + 6) {
        // no break on right of page
        return array_merge(
            [1, 2, null],
            range($total_page - $max_shown + 4, $total_page)
        );
    }

    // breaks on both sides
    $size = $max_shown - 6;
    $first = $current_page - floor(($size-1)/2);
    return array_merge(
        [1, 2, null],
        range($first, $first + $size - 1),
        [null, $total_page-1, $total_page]
    );
}

/**
 * @param int $current_page
 * @param int $total_page
 * @param string $url
 * @param string $page
 * @param int $max_shown
 * @return string
 */
function html_pagination(
        int $total_page,
        int $current_page,
        string $url,
        string $page = 'page',
        int $max_shown = 4
) : string {

    $html = sprintf(
        '<li class="navigator-prev page-item%s" data-page="%d">',
        ($current_page <= 1 ? ' disabled' : ''),
        ($current_page <= 1 ? 1 : $current_page-1)
    );
    $currentUrl = $current_page > 1
        ? esc_attr(add_query_args([$page => $current_page-1], $url))
        : esc_attr(add_query_args([$page => 1], $url));

    $html .= sprintf(
        '<a class="page-link" href="%s" aria-label="%s">',
        $currentUrl,
        trans('Previous')
    );
    $html .= sprintf(
        '<i class="icofont-arrow-left"></i><span class="sr-only">%s</span></a></li>',
        trans('Previous')
    );
    $calc = calculate_get_page_list($total_page, $current_page, $max_shown);
    $sets = [];
    foreach ($calc as $item => $current_loop) {
        if (!$current_loop) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            continue;
        }
        if (isset($sets[$current_loop])) {
            continue;
        }
        $sets[$current_loop] = true;
        $html .= sprintf(
            '<li class="page-item%s" data-page="%d"><a class="page-link" href="%s">%d</a></li>',
            $current_loop === $current_page ? ' active' : '',
            $current_loop,
            esc_attr(add_query_args(['page' => $current_loop], $url)),
            $current_loop
        );
    }

    $html .= sprintf(
        '<li class="navigator-next page-item%s" data-page="%d">',
        $current_page >= $total_page ? ' disabled' : '',
        $current_page >= $total_page ? $total_page : $current_page+1
    );
    $currentUrl = $current_page >= $total_page
        ? add_query_args(['page' => $total_page], $url)
        : add_query_args(['page' => $current_page+1], $url);

    $html .= sprintf(
        '<a class="page-link" href="%s" aria-label="%s">',
        $currentUrl,
        trans('Next')
    );

    $html .= sprintf(
        '<i class="icofont-arrow-right"></i><span class="sr-only">%s</span></a></li>',
        trans('Next')
    );
    return $html;
}

/**
 * @param int|null $size
 * @param $date
 * @return string
 */
function create_indicator_clock(int $size = null, $date = null) : string
{
    list($hours, $minutes, $seconds) = calculate_clock_delay($date);
    $style = '';
    if ($size !== null && $size > 0) {
        $style = " style=\"width: {$size}px;height: {$size}px\"";
    }

    $html = "<div class=\"clock-indicator\"{$style}>"
        . "<div class=\"clock-indicator-hour\" style=\"animation-delay:{$hours}s\"></div>"
        . "<div class=\"clock-indicator-minute\" style=\"animation-delay:{$minutes}s\"></div>"
        . "<div class=\"clock-indicator-second\" style=\"animation-delay:{$seconds}s\"></div>"
        . "<div class=\"clock-indicator-step\">";
    foreach(range(1, 60) as $item) {
        $html .= "<section class=\"clock-indicator-indicator\" style=\"transform:rotateZ(calc(6deg * {$item}));\"></section>";
    }
    $html .= '</div></div>';
    return $html;
}

/**
 * @param int|null $size
 * @param null $date
 * @return string
 */
function create_no_indicator_clock(int $size = null, $date = null) : string
{
    list($hours, $minutes, $seconds) = calculate_clock_delay($date);
    $style = '';
    if ($size !== null && $size > 0) {
        $style = " style=\"width: {$size}px;height: {$size}px\"";
    }
    return "<div class=\"clock-no-indicator\" {$style}>
    <div class=\"clock-no-indicator-hour\" style=\"animation-delay:{$hours}s\"></div>
    <div class=\"clock-no-indicator-minute\" style=\"animation-delay:{$minutes}s\"></div>
    <div class=\"clock-no-indicator-second\" style=\"animation-delay:{$seconds}s\"></div>
</div>
";
}
