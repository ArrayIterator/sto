<?php

namespace ArrayIterator\Dependency;

/**
 * Class Dependency
 * @package ArrayIterator\Dependency
 */
class Dependency
{
    /**
     * The handle name.
     * @var string
     */
    public $handle;

    /**
     * The handle source.
     * @var string
     */
    public $src;

    /**
     * An array of handle dependencies.
     * @var string[]
     */
    public $deps = [];

    /**
     * The handle version.
     *
     * Used for cache-busting.
     * @var bool|string
     */
    public $ver = false;

    /**
     * Additional arguments for the handle.
     * @var array
     */
    public $args = null;  // Custom property, such as $in_footer or $media.

    /**
     * Extra data to supply to the handle.
     * @var array
     */
    public $extra = [];

    /**
     * Setup dependencies.
     *
     * @param string $handle
     * @param string $src
     * @param array $deps
     * @param string $ver
     * @param mixed ...$args Dependency information.
     */
    public function __construct(
        string $handle,
        string $src = null,
        array $deps = [],
        string $ver = null,
        $args = null
    ) {
        $this->handle = $handle;
        $this->src = $src;
        $this->deps = $deps;
        $this->ver = $ver;
        $this->args = $args;
    }

    /**
     * Add handle data.
     *
     * @param string $name The data key to add.
     * @param mixed $data The data value to add.
     * @return bool False if not scalar, true otherwise.
     */
    public function addData(string $name, $data): bool
    {
        if (!is_scalar($name)) {
            return false;
        }
        $this->extra[$name] = $data;
        return true;
    }
}
