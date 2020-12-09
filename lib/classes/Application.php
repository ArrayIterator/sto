<?php

namespace ArrayIterator;

use ArrayIterator\Dependency\Translation;
use ArrayIterator\Helper\Area\TimeZone;
use ArrayIterator\Helper\TimeZoneConvert;
use ArrayIterator\Model\TranslationsDictionary;
use ArrayIterator\Model\Option;
use ArrayIterator\Model\Site;
use ArrayIterator\Model\StudentOnline;
use ArrayIterator\Model\SupervisorOnline;

/**
 * Class Application
 * @package ArrayIterator
 */
class Application
{
    const DEFAULT_SITE_ID = 1;

    /**
     * @var Application
     */
    protected static $instance;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var Translation
     */
    protected $translation;

    /**
     * @var TranslationsDictionary
     */
    protected $languages;

    /**
     * @var Option
     */
    protected $option;

    /**
     * @var StudentOnline
     */
    protected $studentOnline;

    /**
     * @var SupervisorOnline
     */
    protected $supervisorOnline;

    /**
     * @var int
     */
    protected $siteId = self::DEFAULT_SITE_ID;

    /**
     * @var Hooks
     */
    protected $hooks;

    /**
     * @var Route
     */
    protected $route;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var TimeZoneConvert
     */
    protected $timeZoneConvert;

    /**
     * @var TimeZone
     */
    protected $timezone;

    /**
     * Application constructor.
     */
    final private function __construct()
    {
        self::$instance = $this;
    }

    public function getDefaultSiteId(): int
    {
        return $this->getHooks()->apply('default_site_id', self::DEFAULT_SITE_ID);
    }

    /**
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->getHooks()->apply('site_id', $this->siteId);
    }

    /**
     * @param int $siteId
     */
    public function setSiteId(int $siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * @return Application|static
     */
    final public static function getInstance(): Application
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @return TimeZoneConvert
     */
    public function getTimeZoneConvert(): TimeZoneConvert
    {
        if (!$this->timeZoneConvert) {
            $this->timeZoneConvert = new TimeZoneConvert(\TIMEZONE);
        }

        return $this->timeZoneConvert;
    }

    /**
     * @return TimeZone
     */
    public function getTimezone(): TimeZone
    {
        if (!$this->timezone) {
            $this->timezone = new TimeZone();
        }

        return $this->timezone;
    }

    public function getDatabase(): Database
    {
        if (!$this->database) {
            $this->database = new Database(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME,
                DB_PORT
            );
        }

        return $this->database;
    }

    /**
     * @return Translation
     */
    public function getTranslation(): Translation
    {
        if (!$this->translation) {
            $this->translation = new Translation($this->getTranslationDictionary());
        }
        return $this->translation;
    }

    /**
     * @return TranslationsDictionary
     */
    public function getTranslationDictionary(): TranslationsDictionary
    {
        if (!$this->languages) {
            $this->languages = new TranslationsDictionary(
                $this->getDatabase()
            );
        }
        return $this->languages;
    }

    /**
     * @return Option
     */
    public function getOption(): Option
    {
        if (!$this->option) {
            $this->option = new Option(
                $this->getDatabase(),
                $this->getSiteId()
            );
        }

        return $this->option;
    }

    /**
     * @return StudentOnline
     */
    public function getStudentOnline(): StudentOnline
    {
        if (!$this->studentOnline) {
            $this->studentOnline = new StudentOnline(
                $this->getDatabase(),
                $this->getSiteId()
            );
        }
        return $this->studentOnline;
    }

    /**
     * @return SupervisorOnline
     */
    public function getSupervisorOnline(): SupervisorOnline
    {
        if (!$this->supervisorOnline) {
            $this->supervisorOnline = new SupervisorOnline(
                $this->getDatabase(),
                $this->getSiteId()
            );
        }
        return $this->supervisorOnline;
    }

    /**
     * @return Hooks
     */
    public function getHooks(): Hooks
    {
        if (!$this->hooks) {
            $this->hooks = new Hooks();
        }
        return $this->hooks;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        if (!$this->route) {
            $this->route = new Route();
        }
        return $this->route;
    }

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        if (!$this->site) {
            $this->site = new Site($this->getDatabase(), $this->getSiteId());
        }

        return $this->site;
    }
}
