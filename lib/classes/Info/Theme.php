<?php

namespace ArrayIterator\Info;

/**
 * Class Theme
 * @package ArrayIterator\Info
 */
class Theme extends AbstractInfo
{
    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return !empty($this->info) && is_dir($this->path);
    }
}
