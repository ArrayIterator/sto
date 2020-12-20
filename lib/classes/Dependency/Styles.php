<?php

namespace ArrayIterator\Dependency;

use ArrayIterator\Helper\NormalizerData;
use ArrayIterator\Helper\Path;
use ArrayIterator\Helper\StringFilter;
use ArrayIterator\Hooks;

/**
 * Class Styles
 * @package ArrayIterator\Dependency
 */
class Styles extends AbtsractDependencies
{
    /**
     * Base URL for styles.
     *
     * Full URL with trailing slash.
     * @var string
     */
    public $base_url;

    /**
     * @var string
     */
    public $content_url;

    /**
     * @var string
     */
    public $default_version;

    /**
     * @var string
     */
    public $text_direction = 'ltr';

    /**
     * @var array
     */
    public $default_dirs;

    /**
     * @var string
     */
    private $type_attr = ' type="text/css"';
    /**
     * @var Hooks|null
     */
    protected $hooks;

    /**
     * Styles constructor.
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
     * @param string $handle
     * @param int|null $group
     * @return bool
     */
    public function doItem(string $handle, int $group = null): bool
    {
        if (!parent::doItem($handle)) {
            return false;
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

        $inline_style = $this->printInline($handle, false);

        if ($inline_style) {
            $inline_style_tag = sprintf(
                "<style id=\"%s-inline-css\"%s>\n%s\n</style>\n",
                htmlspecialchars($handle, ENT_QUOTES | ENT_COMPAT),
                $this->type_attr,
                $inline_style
            );
        } else {
            $inline_style_tag = '';
        }

        if (isset($obj->args)) {
            $media = htmlspecialchars($obj->args, ENT_QUOTES | ENT_COMPAT);
        } else {
            $media = 'all';
        }

        // A single item may alias a set of items, by having dependencies, but no source.
        if (!$src) {
            if ($inline_style_tag) {
                echo $inline_style_tag;
            }

            return true;
        }

        $href = $this->cssHref($src, $ver, $handle);
        if (!$href) {
            return true;
        }

        $rel = isset($obj->extra['alt']) && $obj->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
        $title = isset($obj->extra['title']) ? sprintf(
            'title="%s"',
            htmlspecialchars($obj->extra['title'], ENT_QUOTES | ENT_COMPAT)
        ) : '';

        $tag = sprintf(
            "<link rel=\"%s\" id=\"%s-css\"%s href=\"%s\"%s media=\"%s\" />\n",
            $rel,
            $handle,
            $title ? " {$title}" : '',
            $href,
            $this->type_attr,
            $media
        );

        $tag = $this->hooks
            ? $this->hooks->apply('style_loader_tag', $tag, $handle, $href, $media)
            : $this;

        if ('rtl' === $this->text_direction && isset($obj->extra['rtl']) && $obj->extra['rtl']) {
            if (is_bool($obj->extra['rtl']) || 'replace' === $obj->extra['rtl']) {
                $suffix = isset($obj->extra['suffix']) ? $obj->extra['suffix'] : '';
                $rtl_href = str_replace("{$suffix}.css", "-rtl{$suffix}.css",
                    $this->cssHref($src, $ver, "$handle-rtl"));
            } else {
                $rtl_href = $this->cssHref($obj->extra['rtl'], $ver, "$handle-rtl");
            }

            $rtl_tag = sprintf(
                "<link rel=\"%s\" id=\"%s-rtl-css\" %s href=\"%s\"%s media=\"%s\" />\n",
                $rel,
                $handle,
                $title,
                $rtl_href,
                $this->type_attr,
                $media
            );

            /** This filter is documented in wp-includes/class.wp-styles.php */
            $rtl_tag = $this->hooks
                ? $this->hooks->apply('style_loader_tag', $rtl_tag, $handle, $rtl_href, $media)
                : $rtl_tag;
            if ('replace' === $obj->extra['rtl']) {
                $tag = $rtl_tag;
            } else {
                $tag .= $rtl_tag;
            }
        }

        echo $cond_before;
        echo $tag;
        $this->printInline($handle);
        echo $cond_after;

        return true;
    }

    /**
     * @param string $handle
     * @param string $code
     * @return bool
     */
    public function addInline(string $handle, string $code): bool
    {
        if (!$code) {
            return false;
        }

        $after = $this->getData($handle, 'after');
        if (!$after) {
            $after = array();
        }
        $after[] = $code;

        return $this->addData($handle, 'after', $after);
    }

    /**
     * @param string $handle
     * @param bool $echo
     * @return bool|string
     */
    public function printInline(string $handle, bool $echo = true)
    {
        $output = $this->getData($handle, 'after');

        if (empty($output)) {
            return false;
        }

        $output = implode("\n", $output);

        if (!$echo) {
            return $output;
        }

        printf(
            "<style id=\"%s-inline-css\"%s>\n%s\n</style>\n",
            htmlspecialchars($handle, ENT_QUOTES | ENT_COMPAT),
            $this->type_attr,
            $output
        );

        return true;
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
                ? $this->hooks->apply('print_styles_array', $this->to_do)
                : $this->to_do;
        }
        return $r;
    }

    /**
     * @param string $src
     * @param string $ver
     * @param string $handle
     * @return string
     */
    public function cssHref(string $src, string $ver, string $handle): string
    {
        if (!is_bool($src)
            && !preg_match('|^(https?:)?//|', $src)
            && !($this->content_url && 0 === strpos($src, $this->content_url))
        ) {
            if ($src && $src[0] === '/') {
                $src = substr($src, 1);
            }
            $src = Path::slashIt($this->base_url) . $src;
        }

        if (!empty($ver)) {
            $src = NormalizerData::addQueryArgs('ver', $ver, $src);
        }
        $src = $this->hooks
            ? $this->hooks->apply('style_loader_src', $src, $handle)
            : $src;
        return StringFilter::escapeUrl($src);
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

    /**
     * @return string[]
     */
    public function doFooterItems(): array
    {
        $this->doItems(false, 1);
        return $this->done;
    }
}