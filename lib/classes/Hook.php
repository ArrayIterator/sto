<?php

namespace ArrayIterator;

use ArrayAccess;
use Iterator;

/**
 * Class Hook
 * @package ArrayIterator
 */
final class Hook implements Iterator, ArrayAccess
{
    /**
     * Hook callbacks.
     * @var array
     */
    public $callbacks = [];

    /**
     * The priority keys of actively running iterations of a hook.
     * @var array
     */
    private $iterations = [];

    /**
     * The current priority of actively running iterations of a hook.
     * @var array
     */
    private $current_priority = [];

    /**
     * Number of levels this hook can be recursively called.
     * @var int
     */
    private $nesting_level = 0;

    /**
     * Flag for if we're current doing an action, rather than a filter.
     * @var bool
     */
    private $doing_action = false;

    /**
     * @var Hooks
     */
    protected $hooks;

    /**
     * CoreHooks constructor.
     * @param Hooks $hooks
     */
    public function __construct(Hooks $hooks)
    {
        $this->hooks = $hooks;
    }

    /**
     * Hooks a function or method to a specific filter action.
     *
     * @param string $tag The name of the filter to hook the $function_to_add callback to.
     * @param callable $function_to_add The callback to be run when the filter is applied.
     * @param int $priority The order in which the functions associated with a particular action
     *                                  are executed. Lower numbers correspond with earlier execution,
     *                                  and functions with the same priority are executed in the order
     *                                  in which they were added to the action.
     * @param int $accepted_args The number of arguments the function accepts.
     */
    public function add(string $tag, callable $function_to_add, int $priority = 10, int $accepted_args = 1)
    {
        $idx = $this->hooks->buildUniqueId($tag, $function_to_add, $priority);

        $priority_existed = isset($this->callbacks[$priority]);

        $this->callbacks[$priority][$idx] = [
            'function' => $function_to_add,
            'accepted_args' => $accepted_args,
        ];

        // If we're adding a new priority to the list, put them back in sorted order.
        if (!$priority_existed && count($this->callbacks) > 1) {
            ksort($this->callbacks, SORT_NUMERIC);
        }

        if ($this->nesting_level > 0) {
            $priority = $priority === false ? null : $priority;
            $this->resortActiveIterations($priority, $priority_existed);
        }
    }

    /**
     * Handles resetting callback priority keys mid-iteration.
     *
     * @param ?int $new_priority Optional. The priority of the new filter being added. Default false,
     *                                   for no priority being added.
     * @param bool $priority_existed Optional. Flag for whether the priority already existed before the new
     *                                   filter was added. Default false.
     */
    private function resortActiveIterations(int $new_priority = null, bool $priority_existed = false)
    {
        $new_priority = $new_priority ?? false;
        $new_priorities = array_keys($this->callbacks);

        // If there are no remaining hooks, clear out all running iterations.
        if (!$new_priorities) {
            foreach ($this->iterations as $index => $iteration) {
                $this->iterations[$index] = $new_priorities;
            }
            return;
        }

        $min = min($new_priorities);
        foreach ($this->iterations as $index => &$iteration) {
            $current = current($iteration);
            // If we're already at the end of this iteration, just leave the array pointer where it is.
            if (false === $current) {
                continue;
            }

            $iteration = $new_priorities;

            if ($current < $min) {
                array_unshift($iteration, $current);
                continue;
            }

            while (current($iteration) < $current) {
                if (false === next($iteration)) {
                    break;
                }
            }

            // If we have a new priority that didn't exist,
            // but ::apply_filters() or ::do_action() thinks it's the current priority...
            if ($new_priority === $this->current_priority[$index] && !$priority_existed) {
                /*
                 * ...and the new priority is the same as what $this->iterations thinks is the previous
                 * priority, we need to move back to it.
                 */

                if (false === current($iteration)) {
                    // If we've already moved off the end of the array, go back to the last element.
                    $prev = end($iteration);
                } else {
                    // Otherwise, just go back to the previous element.
                    $prev = prev($iteration);
                }
                if (false === $prev) {
                    // Start of the array. Reset, and go about our day.
                    reset($iteration);
                } elseif ($new_priority !== $prev) {
                    // Previous wasn't the same. Move forward again.
                    next($iteration);
                }
            }
        }
        unset($iteration);
    }

    /**
     * @param string $tag
     * @param callable $function_to_remove
     * @param int $priority
     * @return bool
     */
    public function remove(string $tag, callable $function_to_remove, int $priority = 10): bool
    {
        $function_key = $this->hooks->buildUniqueId($tag, $function_to_remove, $priority);

        $exists = isset($this->callbacks[$priority][$function_key]);
        if ($exists) {
            unset($this->callbacks[$priority][$function_key]);
            if (!$this->callbacks[$priority]) {
                unset($this->callbacks[$priority]);
                if ($this->nesting_level > 0) {
                    $this->resortActiveIterations();
                }
            }
        }
        return $exists;
    }

    /**
     * Checks if a specific action has been registered for this hook.
     *
     * @param string $tag Optional. The name of the filter hook. Default empty.
     * @param callable|bool $function_to_check Optional. The callback to check for. Default false.
     * @return bool|int The priority of that hook is returned, or false if the function is not attached.
     */
    public function exist(string $tag = '', $function_to_check = false)
    {
        if (false === $function_to_check) {
            return $this->notEmpty();
        }

        $function_key = $this->hooks->buildUniqueId($tag, $function_to_check, null);
        if (!$function_key) {
            return false;
        }

        foreach ($this->callbacks as $priority => $callbacks) {
            if (isset($callbacks[$function_key])) {
                return $priority;
            }
        }

        return false;
    }

    /**
     * Checks if any callbacks have been registered for this hook.
     *
     * @return bool True if callbacks have been registered for the current hook, otherwise false.
     */
    public function notEmpty(): bool
    {
        foreach ($this->callbacks as $callbacks) {
            if ($callbacks) {
                return true;
            }
        }
        return false;
    }

    /**
     * Removes all callbacks from the current filter.
     *
     * @param int|null $priority Optional. The priority number to remove. Default false.
     */
    public function removeAllFilters(int $priority = null)
    {
        if (!$this->callbacks) {
            return;
        }

        $priority = $priority ?? false;
        if (false === $priority) {
            $this->callbacks = [];
        } elseif (isset($this->callbacks[$priority])) {
            unset($this->callbacks[$priority]);
        }

        if ($this->nesting_level > 0) {
            $this->resortActiveIterations();
        }
    }

    /**
     * Calls the callback functions that have been added to a filter hook.
     *
     * @param mixed $value The value to filter.
     * @param array $args Additional parameters to pass to the callback functions.
     *                     This array is expected to include $value at index 0.
     * @return mixed The filtered value after all hooked functions are applied to it.
     */
    public function apply($value, array $args = [])
    {
        if (!$this->callbacks) {
            return $value;
        }

        $nesting_level = $this->nesting_level++;

        $this->iterations[$nesting_level] = array_keys($this->callbacks);
        $num_args = count($args);

        do {
            $this->current_priority[$nesting_level] = current($this->iterations[$nesting_level]);
            $priority = $this->current_priority[$nesting_level];

            foreach ($this->callbacks[$priority] as $the_) {
                if (!$this->doing_action) {
                    $args[0] = $value;
                }

                // Avoid the array_slice() if possible.
                if (0 == $the_['accepted_args']) {
                    $value = call_user_func($the_['function']);
                } elseif ($the_['accepted_args'] >= $num_args) {
                    $value = call_user_func_array($the_['function'], $args);
                } else {
                    $value = call_user_func_array(
                        $the_['function'],
                        array_slice($args, 0, (int)$the_['accepted_args'])
                    );
                }
            }
        } while (false !== next($this->iterations[$nesting_level]));

        unset($this->iterations[$nesting_level]);
        unset($this->current_priority[$nesting_level]);

        $this->nesting_level--;

        return $value;
    }

    /**
     * Calls the callback functions that have been added to an action hook.
     *
     * @param array $args Parameters to pass to the callback functions.
     */
    public function run(array $args)
    {
        $this->doing_action = true;
        $this->apply('', $args);

        // If there are recursive calls to the current action, we haven't finished it until we get to the last one.
        if (!$this->nesting_level) {
            $this->doing_action = false;
        }
    }

    /**
     * Processes the functions hooked into the 'all' hook.
     *
     * @param mixed $args Arguments to pass to the hook callbacks. Passed by reference.
     */
    public function runAll($args)
    {
        $nesting_level = $this->nesting_level++;
        $this->iterations[$nesting_level] = array_keys($this->callbacks);

        do {
            $priority = current($this->iterations[$nesting_level]);
            foreach ($this->callbacks[$priority] as $the_) {
                call_user_func_array($the_['function'], $args);
            }
        } while (false !== next($this->iterations[$nesting_level]));

        unset($this->iterations[$nesting_level]);
        $this->nesting_level--;
    }

    /**
     * Return the current priority level of the currently running iteration of the hook.
     *
     * @return int|false If the hook is running, return the current priority level. If it isn't running, return false.
     */
    public function currentPriority()
    {
        if (false === ($iter = current($this->iterations))) {
            return false;
        }

        return $iter;
    }

    /**
     * Determines whether an offset value exists.
     *
     * @param mixed $offset An offset to check for.
     * @return bool True if the offset exists, false otherwise.
     *
     * @link https://www.php.net/manual/en/arrayaccess.offsetexists.php
     *
     */
    public function offsetExists($offset): bool
    {
        return isset($this->callbacks[$offset]);
    }

    /**
     * Retrieves a value at a specified offset.
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed If set, the value at the specified offset, null otherwise.
     * @link https://www.php.net/manual/en/arrayaccess.offsetget.php
     *
     */
    public function offsetGet($offset)
    {
        return isset($this->callbacks[$offset]) ? $this->callbacks[$offset] : null;
    }

    /**
     * Sets a value at a specified offset.
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * @link https://www.php.net/manual/en/arrayaccess.offsetset.php
     *
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->callbacks[] = $value;
        } else {
            $this->callbacks[$offset] = $value;
        }
    }

    /**
     * Unsets a specified offset.
     *
     * @param mixed $offset The offset to unset.
     * @link https://www.php.net/manual/en/arrayaccess.offsetunset.php
     */
    public function offsetUnset($offset)
    {
        unset($this->callbacks[$offset]);
    }

    /**
     * Returns the current element.
     *
     * @return array|false Of callbacks at current priority.
     * @link https://www.php.net/manual/en/iterator.current.php
     */
    public function current()
    {
        return current($this->callbacks);
    }

    /**
     * Moves forward to the next element.
     *
     * @return array|false Of callbacks at next priority.
     * @link https://www.php.net/manual/en/iterator.next.php
     */
    public function next()
    {
        return next($this->callbacks);
    }

    /**
     * Returns the key of the current element.
     *
     * @return mixed Returns current priority on success, or NULL on failure
     * @link https://www.php.net/manual/en/iterator.key.php
     */
    public function key()
    {
        return key($this->callbacks);
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     * @link https://www.php.net/manual/en/iterator.valid.php
     */
    public function valid(): bool
    {
        return key($this->callbacks) !== null;
    }

    /**
     * Rewinds the Iterator to the first element.
     *
     * @link https://www.php.net/manual/en/iterator.rewind.php
     */
    public function rewind()
    {
        reset($this->callbacks);
    }
}
