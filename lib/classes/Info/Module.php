<?php

namespace ArrayIterator\Info;

/**
 * Class Module
 * @package ArrayIterator\Info
 */
class Module extends AbstractInfo
{

    /**
     * @var bool
     */
    protected $valid;

    /**
     * Module constructor.
     * @param string $path
     * @param array $info
     */
    public function __construct(string $path, array $info)
    {
        parent::__construct($path, $info);
        $this->valid = $this->info['name'] ?? '';
        $this->valid = !empty($this->valid)
            && is_string($this->valid)
            && trim($this->info['name']) !== '';
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return bool
     */
    public function isSiteWide(): bool
    {
        return (bool)($this->info['site_wide'] ?? false);
    }
}
