<?php

use ArrayIterator\Application;
use ArrayIterator\Cache\Adapter\ObjectCache;
use ArrayIterator\Database;
use ArrayIterator\Dependency\Translation;
use ArrayIterator\Helper\Area\TimeZone;
use ArrayIterator\Helper\TimeZoneConvert;
use ArrayIterator\Hooks;
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
use FastRoute\RouteCollector;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\UriInterface;

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
    return \application()->getTimeZoneConvert();
}

/**
 * @return TimeZone
 */
function timezone(): TimeZone
{
    return \application()->getTimezone();
}

/**
 * @return Database
 */
function database(): Database
{
    return \application()->getDatabase();
}

/**
 * @return TranslationsDictionary
 */
function translation_dictionary(): TranslationsDictionary
{
    return \application()->getTranslationDictionary();
}

/**
 * @return Translation
 */
function translation(): Translation
{
    return \application()->getTranslation();
}

/**
 * @return Option
 */
function option(): Option
{
    return \application()->getOption();
}

/**
 * @return Site
 */
function site(): Site
{
    return \application()->getSite();
}

/**
 * @return Hooks
 */
function hooks(): Hooks
{
    return \application()->getHooks();
}

/**
 * @return StudentOnline
 */
function student_online(): StudentOnline
{
    return \application()->getStudentOnline();
}

/**
 * @return SupervisorOnline
 */
function supervisor_online(): SupervisorOnline
{
    return \application()->getSupervisorOnline();
}

/**
 * @return Student
 */
function student(): Student
{
    return student_online()->getUserObject();
}

/**
 * @return Supervisor
 */
function supervisor(): Supervisor
{
    return supervisor_online()->getUserObject();
}

/**
 * @return Route
 */
function route(): Route
{
    return \application()->getRoute();
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
function route_api() : RouteApi
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
function route_public() : RouteApi
{
    static $route_api;
    if (!$route_api) {
        $route_api = new RouteApi(
            \route(),
            '{path: (?!'.get_route_api_path().')}',
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
