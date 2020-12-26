<?php

namespace ArrayIterator\Info;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

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
