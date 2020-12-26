<?php

namespace ArrayIterator\Dependency;

/**
 * Class AbstractDependencies
 * @package ArrayIterator\Dependency
 */
class AbstractDependencies
{
    /**
     * An array of registered handle objects.
     * @var array
     */
    public $registered = [];

    /**
     * An array of handles of queued objects.
     * @var string[]
     */
    public $queue = [];

    /**
     * An array of handles of objects to queue.
     * @var string[]
     */
    public $to_do = [];

    /**
     * An array of handles of objects already queued.
     * @var string[]
     */
    public $done = [];

    /**
     * An array of additional arguments passed when a handle is registered.
     *
     * Arguments are appended to the item query string.
     * @var array
     */
    public $args = [];

    /**
     * An array of handle groups to enqueue.
     * @var array
     */
    public $groups = [];

    /**
     * A handle group to enqueue.
     * @var int
     */
    public $group = 0;

    /**
     * Cached lookup array of flattened queued items and dependencies.
     * @var array
     */
    private $all_queued_deps;

    /**
     * Processes the items and dependencies.
     *
     * Processes the items passed to it or the queue, and their dependencies.
     *
     * @param string|string[]|false $handles Optional. Items to be processed: queue (false),
     *                                       single item (string), or multiple items (array of strings).
     *                                       Default false.
     * @param int|false $group Optional. Group level: level (int), no groups (false).
     * @return string[] Array of handles of items that have been processed.
     */
    public function doItems($handles = false, int $group = null): array
    {
        /*
         * If nothing is passed, print the queue. If a string is passed,
         * print that item. If an array is passed, print those items.
         */
        $handles = false === $handles ? $this->queue : (array)$handles;
        $this->allDependencies($handles);

        foreach ($this->to_do as $key => $handle) {
            if (!in_array($handle, $this->done, true) && isset($this->registered[$handle])) {
                /*
                 * Attempt to process the item. If successful,
                 * add the handle to the done array.
                 *
                 * Unset the item from the to_do array.
                 */
                if ($this->doItem($handle, $group)) {
                    $this->done[] = $handle;
                }

                unset($this->to_do[$key]);
            }
        }

        return $this->done;
    }

    /**
     * Processes a dependency.
     *
     * @param string $handle Name of the item. Should be unique.
     * @param int|false $group Optional. Group level: level (int), no groups (false).
     *                          Default false.
     * @return bool True on success, false if not set.
     */
    public function doItem(string $handle, int $group = null): bool
    {
        return isset($this->registered[$handle]);
    }

    /**
     * @param string|string[] $handles
     * @param bool $recursion
     * @param int|null $group
     * @return bool
     */
    public function allDependencies(
        $handles,
        bool $recursion = false,
        int $group = null
    ): bool {
        $handles = (array)$handles;
        if (!$handles) {
            return false;
        }

        foreach ($handles as $handle) {
            $handle_parts = explode('?', $handle);
            $handle = $handle_parts[0];
            $queued = in_array($handle, $this->to_do, true);

            if (in_array($handle, $this->done, true)) { // Already done.
                continue;
            }

            $moved = $this->setGroup($handle, $recursion, $group);
            $new_group = $this->groups[$handle];

            if ($queued && !$moved) { // Already queued and in the right group.
                continue;
            }

            $keep_going = true;
            if (!isset($this->registered[$handle])) {
                $keep_going = false; // Item doesn't exist.
            } elseif ($this->registered[$handle]->deps && array_diff($this->registered[$handle]->deps,
                    array_keys($this->registered))) {
                $keep_going = false; // Item requires dependencies that don't exist.
            } elseif ($this->registered[$handle]->deps && !$this->allDependencies($this->registered[$handle]->deps,
                    true,
                    $new_group)) {
                $keep_going = false; // Item requires dependencies that don't exist.
            }

            if (!$keep_going) { // Either item or its dependencies don't exist.
                if ($recursion) {
                    return false; // Abort this branch.
                } else {
                    continue; // We're at the top level. Move on to the next one.
                }
            }

            if ($queued) { // Already grabbed it and its dependencies.
                continue;
            }

            if (isset($handle_parts[1])) {
                $this->args[$handle] = $handle_parts[1];
            }

            $this->to_do[] = $handle;
        }

        return true;
    }

    /**
     * @param string $handle
     * @param string|null $src
     * @param array $deps
     * @param string|null $ver
     * @param null $args
     * @return bool
     */
    public function add(
        string $handle,
        string $src = null,
        array $deps = [],
        string $ver = null,
        $args = null
    ): bool {
        if (isset($this->registered[$handle])) {
            return false;
        }

        $this->registered[$handle] = new Dependency($handle, $src, $deps, $ver, $args);
        return true;
    }

    /**
     * Add extra item data.
     *
     * Adds data to a registered item.
     *
     * @param string $handle Name of the item. Should be unique.
     * @param string $key The data key.
     * @param mixed $value The data value.
     * @return bool True on success, false on failure.
     */
    public function addData(string $handle, string $key, $value): bool
    {
        if (!isset($this->registered[$handle])) {
            return false;
        }

        return $this->registered[$handle]->addData($key, $value);
    }

    /**
     * Get extra item data.
     *
     * Gets data associated with a registered item.
     *
     * @param string $handle Name of the item. Should be unique.
     * @param string $key The data key.
     * @return mixed|string|bool Extra item data (string), false otherwise.
     *
     */
    public function getData(string $handle, string $key)
    {
        if (!isset($this->registered[$handle])) {
            return false;
        }

        if (!isset($this->registered[$handle]->extra[$key])) {
            return false;
        }

        return $this->registered[$handle]->extra[$key];
    }

    /**
     * @param string|string[] $handles
     */
    public function remove($handles)
    {
        foreach ((array)$handles as $handle) {
            unset($this->registered[$handle]);
        }
    }

    /**
     * Queue an item or items.
     *
     * Decodes handles and arguments, then queues handles and stores
     * arguments in the class property $args. For example in extending
     * classes, $args is appended to the item url as a query string.
     * Note $args is NOT the $args property of items in the $registered array.
     *
     * @param string|string[] $handles Item handle (string) or item handles (array of strings).
     */
    public function queue($handles)
    {
        foreach ((array)$handles as $handle) {
            $handle = explode('?', $handle);

            if (!in_array($handle[0], $this->queue, true) && isset($this->registered[$handle[0]])) {
                $this->queue[] = $handle[0];

                // Reset all dependencies so they must be recalculated in recurse_deps().
                $this->all_queued_deps = null;

                if (isset($handle[1])) {
                    $this->args[$handle[0]] = $handle[1];
                }
            }
        }
    }

    /**
     * Dequeue an item or items.
     *
     * Decodes handles and arguments, then dequeues handles
     * and removes arguments from the class property $args.
     *
     * @param string|string[] $handles Item handle (string) or item handles (array of strings).
     */
    public function dequeue($handles)
    {
        foreach ((array)$handles as $handle) {
            $handle = explode('?', $handle);
            $key = array_search($handle[0], $this->queue, true);

            if (false !== $key) {
                // Reset all dependencies so they must be recalculated in recurse_deps().
                $this->all_queued_deps = null;

                unset($this->queue[$key]);
                unset($this->args[$handle[0]]);
            }
        }
    }

    /**
     * Recursively search the passed dependency tree for $handle.
     *
     * @param string[] $queue An array of queued Dependency handles.
     * @param string $handle Name of the item. Should be unique.
     * @return bool Whether the handle is found after recursively searching the dependency tree.
     */
    protected function recurseDependencies(array $queue, string $handle): bool
    {
        if (isset($this->all_queued_deps)) {
            return isset($this->all_queued_deps[$handle]);
        }

        $all_deps = array_fill_keys($queue, true);
        $queues = [];
        $done = [];

        while ($queue) {
            foreach ($queue as $queued) {
                if (!isset($done[$queued]) && isset($this->registered[$queued])) {
                    $deps = $this->registered[$queued]->deps;
                    if ($deps) {
                        $all_deps += array_fill_keys($deps, true);
                        array_push($queues, $deps);
                    }
                    $done[$queued] = true;
                }
            }
            $queue = array_pop($queues);
        }

        $this->all_queued_deps = $all_deps;

        return isset($this->all_queued_deps[$handle]);
    }

    /**
     * @param string $handle
     * @param string $list
     * @return bool
     */
    public function query(string $handle, string $list = 'registered'): bool
    {
        switch ($list) {
            case 'registered':
            case 'scripts': // Back compat.
                if (isset($this->registered[$handle])) {
                    return $this->registered[$handle];
                }
                return false;

            case 'enqueued':
            case 'queue':
                if (in_array($handle, $this->queue, true)) {
                    return true;
                }
                return $this->recurseDependencies($this->queue, $handle);

            case 'to_do':
            case 'to_print': // Back compat.
                return in_array($handle, $this->to_do, true);

            case 'done':
            case 'printed': // Back compat.
                return in_array($handle, $this->done, true);
        }
        return false;
    }

    /**
     * @param string $handle
     * @param bool $recursion
     * @param int|null $group
     * @return bool
     */
    public function setGroup(string $handle, bool $recursion, int $group = null): bool
    {
        $group = (int)$group;

        if (isset($this->groups[$handle]) && $this->groups[$handle] <= $group) {
            return false;
        }

        $this->groups[$handle] = $group;

        return true;
    }
}
