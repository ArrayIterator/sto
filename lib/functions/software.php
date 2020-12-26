<?php
/**
 * @return bool
 */
function is_mobile_device(): bool
{
    static $is_mobile = null;
    if ($is_mobile === null) {
        $is_mobile = false;
        $userAgent = get_user_agent();
        if (empty($userAgent)) {
            return $is_mobile = false;
        }
        if (strpos($userAgent, 'Mobile') !== false // Many mobile devices (all iPhone, iPad, etc.)
            || strpos($userAgent, 'Android') !== false
            || strpos($userAgent, 'Silk/') !== false
            || strpos($userAgent, 'Kindle') !== false
            || strpos($userAgent, 'BlackBerry') !== false
            || strpos($userAgent, 'Opera Mini') !== false
            || strpos($userAgent, 'Opera Mobi') !== false
        ) {
            $is_mobile = true;
        }
    }

    return $is_mobile;
}

/**
 * @return bool
 */
function is_mobile(): bool
{
    $is_mobile = is_mobile_device();
    return (bool)hook_apply('is_mobile', $is_mobile);
}

/**
 * @return string|null
 */
function get_device_browser()
{
    static $currentBrowser;
    if ($currentBrowser !== null) {
        return $currentBrowser ?: null;
    }

    $currentBrowser = false;
    $userAgent = get_user_agent();
    if (!$userAgent) {
        return null;
    }
    if (strpos($userAgent, 'Lynx') !== false) {
        return $currentBrowser = BROWSER_LYNX;
    }
    if (strpos($userAgent, 'Edge') !== false) {
        return $currentBrowser = BROWSER_EDGE;
    }
    if (stripos($userAgent, 'chrome') !== false) {
        if (stripos($userAgent, 'chromeframe')) {
            return $currentBrowser = BROWSER_CHROME_FRAME;
        }
        // chromeframe
        // header( 'X-UA-Compatible: chrome=1');
        return $currentBrowser = BROWSER_CHROME;
    }
    if (stripos($userAgent, 'safari') !== false) {
        return $currentBrowser = BROWSER_SAFARI;
    }
    if ((strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false)
        && (strpos($userAgent, 'Win') !== false || strpos($userAgent, 'Mac'))
    ) {
        return $currentBrowser = BROWSER_IE;
    }

    if (strpos($userAgent, 'Gecko') !== false) {
        return $currentBrowser = strpos($userAgent, 'Firefox') ? BROWSER_FIREFOX : BROWSER_GECKO;
    }
    if (strpos($userAgent, 'Opera Mobi') !== false) {
        return $currentBrowser = BROWSER_OPERA_MINI;
    }
    if (strpos($userAgent, 'Opera') !== false) {
        return $currentBrowser = BROWSER_OPERA;
    }
    if (strpos($userAgent, 'Nav') !== false && strpos($userAgent, 'Mozilla/4.') !== false) {
        return $currentBrowser = BROWSER_NETSCAPE_4;
    }

    return $currentBrowser ?: null;
}

/**
 * @return string
 */
function get_web_server(): string
{
    static $software = null;

    if (is_string($software)) {
        return $software;
    }

    $server = get_server_environment('SERVER_SOFTWARE') ?? '';
    if (!$server) {
        return $software;
    }
    $software = $server;
    if (strpos($server, 'LiteSpeed') !== false) {
        return $software = WEBSERVER_LITESPEED;
    }
    if (strpos($server, 'Apache')) {
        return $software = stripos($server, 'Tomcat') ? WEBSERVER_APACHE_TOMCAT : WEBSERVER_APACHE;
    }

    if (strpos($server, 'nginx')) {
        return $software = WEBSERVER_NGINX;
    }
    if (strpos($server, 'Microsoft-IIS') !== false || strpos($server, 'ExpressionDevServer') !== false) {
        return $software = WEBSERVER_IIS;
    }
    if (stripos($server, 'unicorn')) {
        return $software = WEBSERVER_UNICORN;
    }

    if (stripos($server, 'lighttpd')) {
        return $software = WEBSERVER_APACHE_LIGHTTPD;
    }

    if (stripos($server, 'hiawatha')) {
        return $software = WEBSERVER_HIAWATHA;
    }

    if (stripos($server, 'unicorn')) {
        return $software = WEBSERVER_UNICORN;
    }

    return $software;
}

/**
 * @return bool
 */
function is_windows(): bool
{
    static $win = null;
    if ($win === null) {
        $win = stripos(PHP_OS, 'WIN') === 0;
    }
    return $win;
}

/**
 * @return bool
 */
function is_unix(): bool
{
    return !is_windows();
}
