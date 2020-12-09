<?php

/**
 * @return string
 */
function get_admin_html_attributes() : string
{
    $attribute = [
        'lang' => get_selected_site_language(),
        'class' => 'no-js'
    ];
    $attribute = hook_apply('admin_html_attribute', $attribute);
    $attr = '';
    foreach ((array) $attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            htmlspecialchars($key, ENT_QUOTES|ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES|ENT_COMPAT)
        );
    }
    return $attr;
}

/**
 * @return string
 */
function get_html_attributes() : string
{
    $attribute = [
        'lang' => get_selected_site_language(),
        'class' => 'no-js'
    ];
    $attribute = hook_apply('html_attribute', $attribute);
    $attr = '';
    foreach ((array) $attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            htmlspecialchars($key, ENT_QUOTES|ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES|ENT_COMPAT)
        );
    }
    return $attr;
}

/**
 * @return string
 */
function get_body_attributes() : string
{
    $body_class = [];
    if (is_login()) {
        $body_class[] = 'logged';
        $body_class[] = sprintf('user-id-%d', get_current_user_id());
        $body_class[] = sprintf('user-%d', get_current_user_type());
        $body_class[] = sprintf('user-status-%d', get_current_user_status());
    }

    if (is_404()) {
        $body_class[] = 'not-found-page';
    }

    $siteId = get_current_site_id();
    $body_class = hook_apply('body_class', $body_class);
    $body_class[] = sprintf('current-site-%d', $siteId);
    $attribute = [
        'class' => implode(
            ' ',
            array_map('ArrayIterator\Helper\Normalizer::normalizeHtmlClass', $body_class)
        ),
        'data-site-id' => $siteId,
    ];
    $attribute = hook_apply('body_attributes', $attribute);
    $attr = '';
    foreach ((array) $attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            htmlspecialchars($key, ENT_QUOTES|ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES|ENT_COMPAT)
        );
    }
    return $attr;
}

/**
 * @return string
 */
function get_admin_body_attributes() : string
{
    $body_class = [];
    $isLogin = is_login();
    if ($isLogin) {
        $body_class[] = 'logged';
        $body_class[] = sprintf('user-id-%d', get_current_user_id());
        $body_class[] = sprintf('user-%d', get_current_user_type());
        $body_class[] = sprintf('user-status-%d', get_current_user_status());
        if (is_admin_page()) {
            $body_class[] = 'admin-page';
            $body_class[] = sprintf('user-status-%d', get_current_supervisor_role());
        }
    }

    $siteId = get_current_site_id();
    $body_class = hook_apply('admin_body_class', $body_class);
    $body_class[] = sprintf('current-site-%d', $siteId);
    $attribute = [
        'class' => implode(
            ' ',
            array_map('ArrayIterator\Helper\Normalizer::normalizeHtmlClass', $body_class)
        ),
        'data-site-id' => $siteId,
    ];

    $attribute = hook_apply('body_attributes', $attribute);
    $attr = '';
    foreach ((array) $attribute as $key => $item) {
        if (!is_string($item) && !is_numeric($item)) {
            continue;
        }
        $attr .= sprintf(
            ' %s="%s"',
            htmlspecialchars($key, ENT_QUOTES|ENT_COMPAT),
            htmlspecialchars($item, ENT_QUOTES|ENT_COMPAT)
        );
    }
    return $attr;
}
