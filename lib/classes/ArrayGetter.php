<?php

namespace ArrayIterator;

use ArrayAccess;
use Countable;

/**
 * Class ArrayGetter
 * @package ArrayIterator
 */
class ArrayGetter implements ArrayAccess, Countable
{
    protected $data;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return array_key_exists($name, $this->data);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function offsetExists($offset): bool
    {
        return $this->exist($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->data)
            ? $this->data[$name]
            : $default;
    }

    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function exist($name): bool
    {
        return array_key_exists($name, $this->data);
    }

    public function remove($name)
    {
        unset($this->data[$name]);
    }

    public function count(): int
    {
        return count($this->data);
    }
}
