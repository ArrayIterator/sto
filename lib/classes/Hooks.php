<?php

namespace ArrayIterator;

use ArrayAccess;

/**
 * Class Hooks
 * @package ArrayAccess\CHM\Library
 */
final class Hooks implements ArrayAccess
{
    /**
     * @var Hook[]
     */
    public $filters = [];

    /**
     * @var int[]
     */
    protected $actions = [];

    /**
     * @var array
     */
    protected $currents = [];

    /**
     * Hooks constructor.
     */
    public function __construct()
    {
        $this->filters = $this->preInitializedHooks($this->filters);
    }

    /**
     * Normalizes filters set up before WordPress has initialized to WP_Hook objects.
     *
     * @param array $filters Filters to normalize.
     * @return Hook[] Array of normalized filters.
     */
    public function preInitializedHooks(array $filters): array
    {
        /** @var Hook[] $normalized */
        $normalized = [];

        foreach ($filters as $tag => $callback_groups) {
            if (is_object($callback_groups) && $callback_groups instanceof Hook) {
                $normalized[$tag] = $callback_groups;
                continue;
            }

            $hook = new Hook($this);
            // Loop through callback groups.
            foreach ($callback_groups as $priority => $callbacks) {
                // Loop through callbacks.
                foreach ($callbacks as $cb) {
                    $hook->add($tag, $cb['function'], $priority, $cb['accepted_args']);
                }
            }

            $normalized[$tag] = $hook;
        }

        return $normalized;
    }

    /**
     * @param string $tag
     * @param callable $function
     * @param ?int $priority
     * @return string|null
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function buildUniqueId(string $tag, callable $function, int $priority = 10)
    {
        if (is_string($function)) {
            return $function;
        }

        if (is_object($function)) {
            // Closures are currently implemented as objects.
            $function = [$function, ''];
        } else {
            $function = (array)$function;
        }

        if (is_object($function[0])) {
            // Object class calling.
            return spl_object_hash($function[0]) . $function[1];
        } elseif (is_string($function[0])) {
            // Static calling.
            return $function[0] . '::' . $function[1];
        }
        return null;
    }

    /**
     * @param string $tag
     * @param callable $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool
     */
    public function add(
        string $tag,
        callable $function_to_add,
        int $priority = 10,
        int $accepted_args = 1
    ): bool {
        if (!isset($this->filters[$tag])) {
            $this->filters[$tag] = new Hook($this);
        }
        $this->filters[$tag]->add($tag, $function_to_add, $priority, $accepted_args);
        return true;
    }

    /**
     * @param string $tag
     * @param false $function_to_check
     * @return bool
     */
    public function exist(string $tag, $function_to_check = false): bool
    {
        if (!isset($this->filters[$tag])) {
            return false;
        }

        return $this->filters[$tag]->exist($tag, $function_to_check);
    }

    /**
     * @return Hook|false
     */
    public function getCurrent()
    {
        return end($this->currents);
    }

    /**
     * @return array
     */
    public function getCurrents(): array
    {
        return $this->currents;
    }

    public function run(string $tag, ...$arg)
    {
        if (!isset($this->actions[$tag])) {
            $this->actions[$tag] = 1;
        } else {
            ++$this->actions[$tag];
        }

        // Do 'all' actions first.
        if (isset($this->filters['all'])) {
            $this->filters[] = $tag;
            $all_args = func_get_args();
            $this->callAll($all_args);
        }

        if (!isset($this->filters[$tag])) {
            if (isset($this->filters['all'])) {
                array_pop($this->currents);
            }
            return;
        }

        if (!isset($this->filters['all'])) {
            $this->currents[] = $tag;
        }

        if (empty($arg)) {
            $arg[] = '';
        } elseif (is_array($arg[0]) && 1 === count($arg[0]) && isset($arg[0][0]) && is_object($arg[0][0])) {
            // Backward compatibility for PHP4-style passing of `array( &$this )` as action `$arg`.
            $arg[0] = $arg[0][0];
        }

        $this->filters[$tag]->run($arg);
        array_pop($this->currents);
    }

    /**
     * @param string $tag
     * @param mixed ...$arg
     */
    public function runOnceAndRemove(string $tag, ...$arg)
    {
        $this->run($tag, ...$arg);
        $this->removeAll($tag);
    }

    /**
     * @param $tag
     * @return int
     */
    public function hasRun($tag): int
    {
        if (!isset($this->actions[$tag])) {
            return 0;
        }

        return $this->actions[$tag];
    }

    /**
     * @param ?string $filter
     * @return bool
     */
    public function inStack(string $filter = null): bool
    {
        if (null === $filter) {
            return !empty($this->currents);
        }
        return in_array($filter, $this->currents, true);
    }

    /**
     * @param string $tag
     * @param array $args
     */
    public function runRefArray(string $tag, array $args)
    {
        if (!isset($this->actions[$tag])) {
            $this->actions[$tag] = 1;
        } else {
            ++$this->actions[$tag];
        }

        // Do 'all' actions first.
        if (isset($this->filters['all'])) {
            $this->currents[] = $tag;
            $all_args = func_get_args();
            $this->callAll($all_args);
        }

        if (!isset($this->filters[$tag])) {
            if (isset($this->filters['all'])) {
                array_pop($this->currents);
            }
            return;
        }

        if (!isset($this->filters['all'])) {
            $this->currents[] = $tag;
        }

        $this->filters[$tag]->run($args);

        array_pop($this->currents);
    }

    /**
     * @param string $tag
     * @param callable $function_to_remove
     * @param int $priority
     * @return bool
     */
    public function remove(string $tag, callable $function_to_remove, int $priority = 10): bool
    {
        $r = false;
        if (isset($this->filters[$tag])) {
            $r = $this->filters[$tag]->remove($tag, $function_to_remove, $priority);
            if (!$this->filters[$tag]->callbacks) {
                unset($this->filters[$tag]);
            }
        }

        return $r;
    }

    /**
     * @param string $tag
     * @param int|null $priority
     * @return bool
     */
    public function removeAll(string $tag, int $priority = null): bool
    {
        if (isset($this->filters[$tag])) {
            $this->filters[$tag]->removeAllFilters($priority);
            if (!$this->filters[$tag]->exist()) {
                unset($this->filters[$tag]);
            }
        }

        return true;
    }

    /**
     * @param string $tag
     * @param mixed $value
     * @return mixed
     */
    public function apply(string $tag, $value)
    {

        $args = func_get_args();

        // Do 'all' actions first.
        if (isset($this->filters['all'])) {
            $this->currents[] = $tag;
            $this->callAll($args);
        }

        if (!isset($this->filters[$tag])) {
            if (isset($this->filters['all'])) {
                array_pop($this->currents);
            }
            return $value;
        }

        if (!isset($this->filters['all'])) {
            $this->currents[] = $tag;
        }

        // Don't pass the tag name to WP_Hook.
        array_shift($args);

        $filtered = $this->filters[$tag]->apply($value, $args);

        array_pop($this->currents);

        return $filtered;
    }

    protected function callAll(array $args)
    {
        if (isset($this->filters['all'])) {
            $this->filters['all']->runAll($args);
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->filters[$offset]);
    }

    /**
     * @param mixed $offset
     * @return Hook|mixed
     */
    public function offsetGet($offset): Hook
    {
        return $this->filters[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if ($value instanceof Hook) {
            $this->filters[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->filters[$offset]);
    }
}
