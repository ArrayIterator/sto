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