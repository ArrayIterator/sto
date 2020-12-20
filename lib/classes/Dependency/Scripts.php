<?php

namespace ArrayIterator\Dependency;

use ArrayIterator\Helper\NormalizerData;
use ArrayIterator\Helper\Path;
use ArrayIterator\Helper\StringFilter;
use ArrayIterator\Hooks;

/**
 * Class Scripts
 * @package ArrayIterator\Dependency
 */
class Scripts extends AbtsractDependencies
{
    /**
     * Base URL for scripts.
     *
     * Full URL with trailing slash.
     *
     * @var string
     */
    public $base_url;

    /**
     * @var string
     */
    public $assets_url;

    /**
     * @var string
     */
    public $default_version;

    /**
     * @var array
     */
    public $in_footer = [];

    /**
     * List of default directories.
     * @var array
     */
    public $default_dirs = [];

    /**
     * Holds a string which contains the type attribute for script tag.
     *
     * If the current theme does not declare HTML5 support for 'script',
     * then it initializes as `type='text/javascript'`.
     * @var string
     */
    private $type_attr = ' type="text/javascript"';

    /**
     * @var Hooks
     */
    protected $hooks;

    /**
     * Script constructor.
     * @param string $base_url
     * @param Hooks|null $hooks
     */
    public function __construct(string $base_url, Hooks $hooks = null)
    {
        $base_url = explode('?', $base_url)[0];
        $this->base_url = Path::slashIt($base_url);
        $this->hooks = $hooks;
    }

    /**
     * Prints scripts.
     *
     * Prints the scripts passed to it or the print queue. Also prints all necessary dependencies.
     *
     * @param string|string[]|false $handles Optional. Scripts to be printed: queue (false),
     *                                       single script (string), or multiple scripts (array of strings).
     *                                       Default false.
     * @param int|false $group Optional. Group level: level (int), no groups (false).
     *                                       Default false.
     * @return string[] Handles of scripts that have been printed.
     */
    public function prints($handles = false, int $group = null): array
    {
        return $this->doItems($handles, $group);
    }

    /**
     * @param string $handle
     * @param bool $echo
     * @return bool|mixed|string|void
     */
    public function printExtra(string $handle, bool $echo = true)
    {
        $output = $this->getData($handle, 'data');
        if (!$output) {
            return;
        }

        if (!$echo) {
            return $output;
        }

        echo "<script{$this->type_attr}>\n";
        echo "$output\n";
        echo "</script>\n";

        return true;
    }

    /**
     * @param string $handle
     * @param int|null $group
     * @return bool
     */
    public function doItem(string $handle, int $group = null): bool
    {
        if (!parent::doItem($handle)) {
            return false;
        }

        if (0 === $group && $this->groups[$handle] > 0) {
            $this->in_footer[] = $handle;
            return false;
        }

        if (null === $group && in_array($handle, $this->in_footer, true)) {
            $this->in_footer = array_diff($this->in_footer, (array)$handle);
        }

        $obj = $this->registered[$handle];

        if (null === $obj->ver) {
            $ver = '';
        } else {
            $ver = $obj->ver ? $obj->ver : $this->default_version;
        }

        if (isset($this->args[$handle])) {
            $ver = $ver ? $ver . '&amp;' . $this->args[$handle] : $this->args[$handle];
        }

        $src = $obj->src;
        $cond_before = '';
        $cond_after = '';
        $conditional = isset($obj->extra['conditional']) ? $obj->extra['conditional'] : '';

        if ($conditional) {
            $cond_before = "<!--[if {$conditional}]>\n";
            $cond_after = "<![endif]-->\n";
        }

        $before_handle = $this->printInline($handle, 'before', false);
        $after_handle = $this->printInline($handle, 'after', false);

        if ($before_handle) {
            $before_handle = sprintf("<script%s>\n%s\n</script>\n", $this->type_attr, $before_handle);
        }

        if ($after_handle) {
            $after_handle = sprintf("<script%s>\n%s\n</script>\n", $this->type_attr, $after_handle);
        }

        if ($before_handle || $after_handle) {
            $inline_script_tag = $cond_before . $before_handle . $after_handle . $cond_after;
        } else {
            $inline_script_tag = '';
        }

        $has_conditional_data = $conditional && $this->getData($handle, 'data');
        if ($has_conditional_data) {
            echo $cond_before;
        }

        $this->printExtra($handle);

        if ($has_conditional_data) {
            echo $cond_after;
        }

        // A single item may alias a set of items, by having dependencies, but no source.
        if (!$src) {
            if ($inline_script_tag) {
                echo $inline_script_tag;
            }

            return true;
        }

        if (!preg_match('|^(https?:)?//|', $src) && !($this->assets_url && 0 === strpos($src, $this->assets_url))) {
            if ($src[0] === '/') {
                $src = substr($src, 1);
            }
            $src = Path::slashIt($this->base_url) . $src;
        }

        if (!empty($ver)) {
            $src = NormalizerData::addQueryArgs('ver', $ver, $src);
        }

        $src = $this->hooks
            ? $this->hooks->apply('script_loader_src', $src, $handle)
            : $src;

        /** This filter is documented in wp-includes/class.wp-scripts.php */
        $src = StringFilter::escapeUrl($src);
        if (!$src) {
            return true;
        }

        $tag = $cond_before . $before_handle;
        $tag .= sprintf(
            "<script%s src=\"%s\"></script>\n",
            $this->type_attr,
            htmlspecialchars($src, ENT_QUOTES | ENT_COMPAT)
        );
        $tag .= $after_handle . $cond_after;

        echo $this->hooks
            ? $this->hooks->apply('script_loader_tag', $tag, $handle, $src)
            : $tag;
        return true;
    }

    /**
     * @param string $handle
     * @param string $data
     * @param string $position
     * @return bool
     */
    public function addInline(string $handle, string $data, string $position = 'after'): bool
    {
        if (!$data) {
            return false;
        }

        if ('after' !== $position) {
            $position = 'before';
        }

        $script = (array)$this->getData($handle, $position);
        $script[] = $data;

        return $this->addData($handle, $position, $script);
    }

    /**
     * @param string $handle
     * @param string $position
     * @param bool $echo
     * @return false|string
     */
    public function printInline(string $handle, string $position = 'after', bool $echo = true)
    {
        $output = $this->getData($handle, $position);

        if (empty($output)) {
            return false;
        }

        $output = trim(implode("\n", $output), "\n");

        if ($echo) {
            printf("<script%s>\n%s\n</script>\n", $this->type_attr, $output);
        }

        return $output;
    }

    /**
     * @param string $handle
     * @param bool $recursion
     * @param int|null $group
     * @return bool
     */
    public function setGroup(string $handle, bool $recursion, int $group = null): bool
    {
        if (isset($this->registered[$handle]->args) && 1 === $this->registered[$handle]->args) {
            $grp = 1;
        } else {
            $grp = (int)$this->getData($handle, 'group');
        }

        if (null !== $group && $grp > $group) {
            $grp = $group;
        }

        return parent::setGroup($handle, $recursion, $grp);
    }

    /**
     * @param string|string[] $handles
     * @param bool $recursion
     * @param int|null $group
     * @return bool
     */
    public function allDependencies($handles, bool $recursion = false, int $group = null): bool
    {
        $r = parent::allDependencies($handles, $recursion, $group);
        if (!$recursion) {
            $this->to_do = $this->hooks
                ? $this->hooks->apply('print_scripts_array', $this->to_do)
                : $this->to_do;
        }
        return $r;
    }

    /**
     * @return string[]
     */
    public function doHeadItems(): array
    {
        $this->doItems(false, 0);
        return $this->done;
    }

    /**
     * @return string[]
     */
    public function doFooterItems(): array
    {
        $this->doItems(false, 1);
        return $this->done;
    }

    /**
     * @param string $src
     * @return bool
     */
    public function inDefaultDir(string $src): bool
    {
        if (!$this->default_dirs) {
            return true;
        }

        foreach ((array)$this->default_dirs as $test) {
            if (0 === strpos($src, $test)) {
                return true;
            }
        }
        return false;
    }
}
