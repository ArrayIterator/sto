<?php

use ArrayIterator\Helper\Normalizer;

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
            array_map('ArrayIterator\Helper\Normalizer::normalizeHtmlClass', $html_class)
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
            htmlspecialchars($key, ENT_QUOTES | ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES | ENT_COMPAT)
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
    $body_class = hook_apply('admin_login_form_class', $body_class);
    $attribute = [
        'class' => implode(
            ' ',
            array_map('ArrayIterator\Helper\Normalizer::normalizeHtmlClass', $body_class)
        ),
        'id' => 'login-form',
        'method' => 'post',
        'action' => get_admin_login_url(),
    ];
    $attribute = hook_apply('admin_login_form_attributes', $attribute);
    if (isset($attribute['id'])) {
        $attribute['id'] = Normalizer::normalizeHtmlClass($attribute['id']);
    }
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            htmlspecialchars($key, ENT_QUOTES | ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES | ENT_COMPAT)
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
    if ($isLogin) {
        $body_class[] = 'logged';
        $body_class[] = sprintf('user-id-%d', get_current_user_id());
        $body_class[] = sprintf('user-%s', get_current_user_type());
        $body_class[] = sprintf('user-status-%s', get_current_user_status());
        if (is_admin_page()) {
            $body_class[] = 'admin-page';
            $body_class[] = sprintf('user-status-%s', get_current_supervisor_role());
        }
    } else {
        $body_class[] = 'guess';
        if (is_admin_login_page()) {
            $body_class[] = 'login-page';
        }
    }

    $siteId = get_current_site_id();
    $body_class = hook_apply('admin_body_class', $body_class);
    $body_class[] = sprintf('current-site-%d', $siteId);
    $body_class = array_filter(array_unique($body_class));
    $attribute = [
        'class' => implode(
            ' ',
            array_map('ArrayIterator\Helper\Normalizer::normalizeHtmlClass', $body_class)
        ),
        'data-site-id' => $siteId,
    ];

    $attribute = hook_apply('body_attributes', $attribute);
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            htmlspecialchars($key, ENT_QUOTES | ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES | ENT_COMPAT)
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
    $loaded = true;
    load_admin_template('footer');
}

/**
 * @return string
 */
function get_admin_title(): string
{
    $title = is_admin_login() ? 'Dashboard' : get_admin_login_title();
    $title = (string)hook_apply('admin_title', $title);
    return $title;
}

/**
 * Hook render title
 */
function render_admin_title_tag()
{
    $title = get_admin_title();
    echo '<title>' . esc_html_trans($title) . '</title>' . "\n";
}

/**
 * @return string
 */
function get_admin_login_title(): string
{
    return (string)hook_apply('admin_login_title', 'Login To Admin Area');
}

/**
 * @return string
 */
function get_admin_button_submit(): string
{
    return hook_apply(
        'admin_button_submit',
        sprintf(
            '<button type="submit" class="btn-primary btn btn-block admin-submit-button">%s</button>',
            trans('Sign in')
        )
    );
}

/**
 *
 */
function admin_login_form()
{
    ?>
    <form<?= get_admin_login_form_attributes(); ?>>
        <?php hook_run('admin_login_form_before'); ?>
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
                htmlspecialchars(
                    (string)hook_apply('admin_input_username', (string)post('username')),
                    ENT_QUOTES | ENT_COMPAT
                );
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
            <div class="form-check admin-checkbox-input">
                <input type="checkbox" name="remember" id="remember" class="form-check-input"
                       value="yes"<?= hook_apply('remember_me',
                    post('remember') === 'yes') === true ? ' checked' : ''; ?>>
                <label class="form-check-label" for="remember"><?php trans_e('Remember Me'); ?></label>
            </div>
        </div>
        <?php hook_run('admin_login_form_after'); ?>

        <div class="form-group">
            <?= get_admin_button_submit(); ?>
        </div>
    </form>
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
            array_map('ArrayIterator\Helper\Normalizer::normalizeHtmlClass', $html_class)
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
            htmlspecialchars($key, ENT_QUOTES | ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES | ENT_COMPAT)
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
    if (is_login()) {
        $body_class[] = 'logged';
        $body_class[] = sprintf('user-id-%d', get_current_user_id());
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
    $body_class = hook_apply('body_class', $body_class);
    $body_class[] = sprintf('current-site-%d', $siteId);
    $body_class = array_filter(array_unique($body_class));
    $attribute = [
        'class' => implode(
            ' ',
            array_map('ArrayIterator\Helper\Normalizer::normalizeHtmlClass', $body_class)
        ),
        'data-site-id' => $siteId,
    ];
    $attribute = hook_apply('body_attributes', $attribute);
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            htmlspecialchars($key, ENT_QUOTES | ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES | ENT_COMPAT)
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
            array_map('ArrayIterator\Helper\Normalizer::normalizeHtmlClass', $body_class)
        ),
        'id' => 'login-form',
        'method' => 'post',
        'action' => get_login_url(),
    ];
    $attribute = hook_apply('login_form_attributes', $attribute);
    if (isset($attribute['id'])) {
        $attribute['id'] = Normalizer::normalizeHtmlClass($attribute['id']);
    }
    $attr = '';
    foreach ((array)$attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            htmlspecialchars($key, ENT_QUOTES | ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES | ENT_COMPAT)
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
function get_template_header(): bool
{
    return load_template('header');
}

/**
 * @return bool
 */
function get_template_footer(): bool
{
    return load_template('footer');
}

/**
 * @return string
 */
function get_title(): string
{
    $title = get_option('site_title', null, get_current_site_id(), $found);
    if (!$found || !is_string($title)) {
        $title = '';
    }
    if (is_404()) {
        $title = hook_apply('title_404', '404 Page Not Found');
    } elseif (is_405()) {
        $title = hook_apply('title_405', '405 Method Not Allowed');
    }

    $title = hook_apply('title', (string)$title);
    return $title;
}

/**
 * Hook render title
 */
function render_title_tag()
{
    $title = get_title();
    echo '<title>' . esc_html_trans($title) . '</title>';
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
