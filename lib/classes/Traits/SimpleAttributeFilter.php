<?php

namespace ArrayIterator\Traits;

use JsonSerializable;

/**
 * Trait SimpleAttributeFilter
 * @package ArrayIterator\Traits
 */
trait SimpleAttributeFilter
{
    /**
     * @param mixed $name
     * @param mixed $value
     * @return false|string[]
     */
    protected function filterAttribute($name, $value)
    {
        if (!is_string($name)) {
            return false;
        }

        if (preg_match('#[^a-z0-9_\-]#i', trim($name))) {
            return false;
        }

        if (is_bool($value)) {
            $value = ($value ? 'true' : 'false');
        } elseif (is_null($value) || $value === '') {
            $value = '';
        } elseif (is_numeric($value)) {
            $value = (string)$value;
        } elseif (is_object($value)) {
            if ($value instanceof JsonSerializable) {
                $value = json_ns($value);
            } elseif (method_exists($value, '__tostring')) {
                $value = (string)$value;
            }
        }

        if (!is_string($value)) {
            return false;
        }

        return [
            $name,
            $value
        ];
    }
}
