<?php

use ArrayIterator\Application;
use ArrayIterator\Database;
use ArrayIterator\Dependency\Translation;
use ArrayIterator\Helper\Area\TimeZone;
use ArrayIterator\Helper\TimeZoneConvert;
use ArrayIterator\Hooks;
use ArrayIterator\Model\Languages;
use ArrayIterator\Model\Option;
use ArrayIterator\Model\Site;
use ArrayIterator\Model\Student;
use ArrayIterator\Model\StudentOnline;
use ArrayIterator\Model\Supervisor;
use ArrayIterator\Model\SupervisorOnline;
use ArrayIterator\Route;

/**
 * @return Application
 */
function application(): Application
{
    return Application::getInstance();
}

/**
 * @return TimeZoneConvert
 */
function timezone_convert() : TimeZoneConvert
{
    return \application()->getTimeZoneConvert();
}

/**
 * @return TimeZone
 */
function timezone() : TimeZone
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
 * @return Languages
 */
function languages(): Languages
{
    return \application()->getLanguages();
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
