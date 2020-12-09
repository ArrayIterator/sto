<?php

namespace ArrayIterator\Helper;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

/**
 * Class TimeZoneConvert
 * @package ArrayIterator\Helper
 */
class TimeZoneConvert
{
    protected $timezone;

    public function __construct(string $timezone = null)
    {
        $timezone = $timezone ?? date_default_timezone_get();
        $this->timezone = new DateTimeZone($timezone);
    }

    /**
     * @return array
     */
    public function getLocation(): array
    {
        return $this->timezone->getLocation();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->timezone->getName();
    }

    /**
     * @param DateTimeInterface $dateTime
     * @return int
     */
    public function getOffset(DateTimeInterface $dateTime): int
    {
        return $this->timezone->getOffset($dateTime);
    }

    /**
     * @param int|null $begin
     * @param int|null $end
     * @return array
     */
    public function getTransitions(int $begin = null, int $end = null): array
    {
        return $this->timezone->getTransitions($begin, $end);
    }

    /**
     * @return DateTimeZone
     */
    public function getTimezone(): DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCurrentTime(): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable('now', $this->getTimezone());
        } catch (Exception $e) {
            return (new DateTimeImmutable())->setTimezone($this->getTimezone());
        }
    }

    /**
     * @param string|null $date
     * @return DateTimeImmutable|false
     * @throws Exception
     */
    public function convert(string $date = null)
    {
        if ($date instanceof DateTimeInterface) {
            $date = new DateTimeImmutable($date->format('c'));
        }

        if (is_string($date)) {
            $date = new DateTimeImmutable($date);
        }

        if (!$date instanceof DateTimeImmutable) {
            return false;
        }

        return $date->setTimezone($this->getTimezone());
    }
}
