<?php

use ArrayIterator\Application;
use ArrayIterator\Cache\Adapter\ObjectCache;
use ArrayIterator\Database;
use ArrayIterator\Dependency\Scripts;
use ArrayIterator\Dependency\Styles;
use ArrayIterator\Dependency\Translation;
use ArrayIterator\Helper\Area\TimeZone;
use ArrayIterator\Helper\TimeZoneConvert;
use ArrayIterator\Hooks;
use ArrayIterator\Menus;
use ArrayIterator\Model\StudentLogs;
use ArrayIterator\Model\SupervisorLogs;
use ArrayIterator\Model\TranslationsDictionary;
use ArrayIterator\Model\Option;
use ArrayIterator\Model\Site;
use ArrayIterator\Model\Student;
use ArrayIterator\Model\StudentOnline;
use ArrayIterator\Model\Supervisor;
use ArrayIterator\Model\SupervisorOnline;
use ArrayIterator\Modules;
use ArrayIterator\Route;
use ArrayIterator\RouteApi;
use ArrayIterator\Themes;
use FastRoute\RouteCollector;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\UriInterface;
use UAParser\DeviceParser;
use UAParser\OperatingSystemParser;
use UAParser\Parser;
use UAParser\Result;
use UAParser\Result\Client;
use UAParser\UserAgentParser;

/**
 * @return Application
 */
function application(): Application
{
    return Application::getInstance();
}

/**
 * @return ServerRequest
 */
function server_request(): ServerRequest
{
    static $request;
    if (!$request) {

        $request = ServerRequest::fromGlobals();
        $server = $request->getServerParams();
        $host = $server['HTTP_HOST'] ?? $server['SERVER_NAME'];
        $port = $server['HTTP_X_FORWARDED_PORT'] ?? $server['SERVER_PORT'] ?? null;
        if ($port && $port != 443 && $port != 80) {
            $host = sprintf('%s:%d', $host, $port);
        }
        $uri = $request->getUri()->withHost($host);
        if (($server['HTTPS'] ?? null) == 'on'
            || ($server['HTTP_X_FORWARDED_PROTO'] ?? null) == 'https'
            || ($server['SERVER_PORT'] ?? null) == 443
        ) {
            $uri = $uri->withScheme('https');
        }

        $request = $request->withUri($uri);
    }

    return $request;
}

/**
 * @return UriInterface
 */
function get_uri(): UriInterface
{
    return server_request()->getUri();
}

/**
 * @return TimeZoneConvert
 */
function timezone_convert(): TimeZoneConvert
{
    return application()->getTimeZoneConvert();
}

/**
 * @return TimeZone
 */
function timezone(): TimeZone
{
    return application()->getTimezone();
}

/**
 * @return Result\Client
 */
function user_agent_parsed() : Result\Client
{
    static $client;
    if ($client) {
        return $client;
    }
    /**
     * @var Parser $ua
     */
    $userAgent = get_user_agent();
    try {
        $ua = Parser::create();
        $client = $ua->parse(get_user_agent());
    } catch (Exception $e) {
        $regexes = require __DIR__.'/../includes/user_agent_regexes.php';
        $client = new Client($userAgent);
        $client->ua = (new UserAgentParser($regexes))->parseUserAgent($userAgent, []);
        $client->os = (new OperatingSystemParser($regexes))->parseOperatingSystem($userAgent);
        $client->device = (new DeviceParser($regexes))->parseDevice($userAgent);
    }

    return $client;
}

/**
 * @return Database
 */
function database(): Database
{
    return application()->getDatabase();
}

/**
 * @return TranslationsDictionary
 */
function translation_dictionary(): TranslationsDictionary
{
    return application()->getTranslationDictionary();
}

/**
 * @return Translation
 */
function translation(): Translation
{
    return application()->getTranslation();
}

/**
 * @return Option
 */
function option(): Option
{
    return application()->getOption();
}

/**
 * @return Site
 */
function site(): Site
{
    return application()->getSite();
}

/**
 * @return Menus
 */
function menus() : Menus
{
    static $menus;
    if (!$menus) {
        $menus = new Menus(get_site_url());
    }
    return $menus;
}

/**
 * @return Hooks
 */
function hooks(): Hooks
{
    return application()->getHooks();
}

/**
 * @return StudentOnline
 */
function student_online(): StudentOnline
{
    return application()->getStudentOnline();
}

/**
 * @return SupervisorOnline
 */
function supervisor_online(): SupervisorOnline
{
    return application()->getSupervisorOnline();
}

/**
 * @return Student
 */
function student(): Student
{
    return student_online()->getUserObject();
}

/**
 * @return StudentLogs
 */
function student_log() : StudentLogs
{
    return student()->getObjectUserLog();
}

/**
 * @return Supervisor
 */
function supervisor(): Supervisor
{
    return supervisor_online()->getUserObject();
}

/**
 * @return SupervisorLogs
 */
function supervisor_log() : SupervisorLogs
{
    return supervisor()->getObjectUserLog();
}

/**
 * @return Route
 */
function route(): Route
{
    return application()->getRoute();
}

/**
 * @return RouteCollector
 */
function route_collector(): RouteCollector
{
    return application()->getRoute()->getRouteCollector();
}

/**
 * @return RouteApi
 */
function route_api(): RouteApi
{
    static $route_api;
    if (!$route_api) {
        $route_api = new RouteApi(
            \route(),
            get_route_api_path(),
            true
        );
    }

    return $route_api;
}

/**
 * @return RouteApi
 */
function route_public(): RouteApi
{
    static $route_api;

    if (!$route_api) {
        // $admin = preg_quote(get_route_api_path(), '#');
        $api = preg_quote(get_admin_path(), '#');
        $route_api = new RouteApi(
            \route(),
            "{__not_admin: (?!{$api})}",
            true
        );
    }

    return $route_api;
}

/**
 * @return ObjectCache
 */
function object_cache(): ObjectCache
{
    static $cache;
    if (!$cache) {
        $cache = new ObjectCache(get_current_site_id());
    }

    return $cache;
}

/**
 * @return Modules
 */
function modules(): Modules
{
    static $modules;
    if (!$modules) {
        $modules = new Modules(get_modules_dir());
    }

    return $modules;
}

/**
 * @return Themes
 */
function themes(): Themes
{
    static $themes;
    if (!$themes) {
        $themes = new Themes(get_themes_dir());
    }
    return $themes;
}

/**
 * @return Scripts
 */
function assets_scripts(): Scripts
{
    static $scripts;
    if (!$scripts) {
        $scripts = new Scripts(get_site_url(), hooks());
        hook_run_ref_array('assets_default_scripts', [&$scripts]);
    }
    return $scripts;
}

/**
 * @return Styles
 */
function assets_styles(): Styles
{
    static $styles;
    if (!$styles) {
        $styles = new Styles(get_site_url(), hooks());
        hook_run_ref_array('assets_default_styles', [&$styles]);
    }

    return $styles;
}
