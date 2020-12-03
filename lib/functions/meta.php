<?php

use ArrayIterator\Application;
use ArrayIterator\Database;
use ArrayIterator\Dependency\Translation;
use ArrayIterator\Hooks;
use ArrayIterator\Model\Languages;
use ArrayIterator\Model\Option;
use ArrayIterator\Model\Site;
use ArrayIterator\Model\Student;
use ArrayIterator\Model\StudentOnline;
use ArrayIterator\Model\Supervisor;
use ArrayIterator\Model\SupervisorOnline;
use ArrayIterator\Route;

function application() : Application
{
    return Application::getInstance();
}

/**
 * @return Database
 */
function database() : Database
{
    return \application()->getDatabase();
}

/**
 * @return Languages
 */
function languages() : Languages
{
    return \application()->getLanguages();
}

/**
 * @return Translation
 */
function translation() : Translation
{
    return \application()->getTranslation();
}

/**
 * @return Option
 */
function option() : Option
{
    return \application()->getOption();
}

/**
 * @return Site
 */
function site() : Site
{
    return \application()->getSite();
}

/**
 * @return Hooks
 */
function hooks() : Hooks
{
    return \application()->getHooks();
}

/**
 * @param string $tag
 * @param callable $function_to_add
 * @param int $priority
 * @param int $accepted_args
 * @return bool
 */
function hook_add(
    string $tag,
    callable $function_to_add,
    int $priority = 10,
    int $accepted_args = 1
) {
    return \hooks()->add($tag, $function_to_add, $priority, $accepted_args);
}

function hook_apply(string $tag, $valuue)
{
    return \hooks()->apply(...func_get_args());
}

function hook_remove_all(string $tag, int $priority = null)
{
    return \hooks()->removeAll($tag, $priority);
}

function hook_remove(string $tag, callable $function_to_remove, int $priority = 10)
{
    return \hooks()->remove($tag, $function_to_remove, $priority);
}

function hook_exist(string $tag, $function_to_check = false)
{
    return \hooks()->exist($tag, $function_to_check);
}
function hook_run(string $tag, ...$arg)
{
    return \hooks()->run($tag, ...$arg);
}

function hook_run_once(string $tag, ...$arg)
{
    return \hooks()->runOnceAndRemove($tag, ...$arg);
}
function hook_has_run(string $tag)
{
    return \hooks()->hasRun($tag);
}

function hook_is_in_stack(string $filter = null)
{
    return \hooks()->inStack($filter);
}

function hook_run_ref_array(string $tag, array $args)
{
    return \hooks()->runRefArray($tag, $args);
}

function student_online() : StudentOnline
{
    return \application()->getStudentOnline();
}

function supervisor_online() : SupervisorOnline
{
    return \application()->getSupervisorOnline();
}

/**
 * @return Student
 */
function student() : Student
{
    return student_online()->getUserObject();
}

/**
 * @return Supervisor
 */
function supervisor() : Supervisor
{
    return supervisor_online()->getUserObject();
}

/**
 * @return Route
 */
function route() : Route
{
    return \application()->getRoute();
}

function set_not_found_handler(callable $callback)
{
    \route()->setNotFoundHandler($callback);
}

function set_not_allowed_handler(callable $callback)
{
    \route()->setNotAllowedHandler($callback);
}
