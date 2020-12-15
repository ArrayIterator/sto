<?php

namespace ArrayIterator;

use ArrayIterator\Helper\Normalizer;
use ArrayIterator\Traits\SimpleAttributeFilter;
use Serializable;

/**
 * Class Menus
 * @package ArrayIterator
 */
class Menus implements Serializable
{
    use SimpleAttributeFilter;

    const MAX_ALLOWED_DEPTH = 10;
    const DEFAULT_MAX_DEPTH = 5;
    const DEFAULT_DEPTH = 2;

    /**
     * @var int
     */
    protected $max_depth = self::MAX_ALLOWED_DEPTH;

    /**
     * @var string
     */
    protected $default_main_menu_class = '';

    /**
     * @var string|null|false
     */
    protected $siteUrl;

    /**
     * @var Menu[]
     */
    protected $menus = [];

    /**
     * Menus constructor.
     * @param string|null $siteUrl
     * @param int $max_depth
     * @param string $default_main_menu_class
     */
    public function __construct(
        string $siteUrl = null,
        int $max_depth = self::DEFAULT_MAX_DEPTH,
        string $default_main_menu_class = 'nav-menu'
    ) {
        $this->siteUrl = $siteUrl;
        $this->max_depth = $max_depth < 0 ? 0 : (
        $max_depth > self::MAX_ALLOWED_DEPTH
            ? self::MAX_ALLOWED_DEPTH
            : $max_depth
        );
        $default_main_menu_class = trim($default_main_menu_class);
        $default_main_menu_class = explode(' ', $default_main_menu_class);
        foreach ($default_main_menu_class as $k => $item) {
            unset($default_main_menu_class[$k]);
            $default_main_menu_class[] = Normalizer::normalizeHtmlClass($item);
        }
        $default_main_menu_class = array_unique(array_filter($default_main_menu_class));
        $this->default_main_menu_class = implode(' ', $default_main_menu_class);
    }

    /**
     * @return int
     */
    public function getMaxDepth(): int
    {
        return $this->max_depth;
    }

    /**
     * @return Menu[]
     */
    public function getMenus(): array
    {
        return $this->menus;
    }

    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * @param string|null $siteUrl
     */
    public function setSiteUrl(string $siteUrl)
    {
        $this->siteUrl = $siteUrl;
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $url
     * @param array $attr
     * @param array $link_attr
     * @param int $position
     * @param bool $show
     * @return Menu
     */
    public function createMenu(
        string $id,
        string $name,
        string $url = '',
        array $attr = [],
        array $link_attr = [],
        int $position = 10,
        bool $show = true
    ): Menu {
        return new Menu($id, $name, $url, $attr, $link_attr, $position, $show);
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $url
     * @param array $attr
     * @param array $link_attr
     * @param int $position
     * @param bool $show
     * @return Menu
     */
    public function addMenu(
        string $id,
        string $name,
        string $url = '',
        array $attr = [],
        array $link_attr = [],
        int $position = 10,
        bool $show = true
    ): Menu {
        $menu = $this->createMenu($id, $name, $url, $attr, $link_attr, $position, $show);
        $this->add($menu);
        return $menu;
    }

    /**
     * @param Menu $menu
     */
    public function add(Menu $menu)
    {
        $id = $menu->getId();
        $this->menus[$id] = $menu;
    }

    /**
     * @param Menu $menu
     * @return bool
     */
    public function delete(Menu $menu): bool
    {
        return $this->deleteMenu($menu->getId());
    }

    /**
     * @param string $id
     * @return bool
     */
    public function deleteMenu(string $id): bool
    {
        if (!isset($this->menus[$id])) {
            return false;
        }
        unset($this->menus[$id]);
        return true;
    }

    /**
     * @param array $attrs
     * @return array
     */
    protected function sanitizeAttribute(array $attrs): array
    {
        foreach ($attrs as $k => $v) {
            unset($attrs[$k]);
            $v = $this->filterAttribute($k, $v);
            if (!is_array($v)) {
                continue;
            }
            $attrs[$v[0]] = htmlspecialchars($v[1], ENT_QUOTES | ENT_COMPAT);
        }

        return $attrs;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function sanitizeUrl(string $url): string
    {
        if (!$this->siteUrl) {
            return $url;
        }

        if ($url && strpos($url, '#') !== 0 && !preg_match('#^https?://#i', $url)) {
            $url = slash_it($this->siteUrl) . ltrim($url, '/');
        }

        return $url;
    }

    /**
     * @param Menu $parentMenu
     * @param int $maxDepth
     * @param int $deep
     * @param string $globalTag
     * @param callable|null $fallBack
     * @param string|null $currentUrl
     * @return string
     */
    protected function buildMenu(
        Menu $parentMenu,
        int $maxDepth,
        int $deep,
        string $globalTag,
        callable $fallBack = null,
        string $currentUrl = null
    ): string {
        if ($deep > $maxDepth) {
            return '';
        }
        if (!isset($this->matches) || !is_array($this->matches)) {
            $this->matches = [];
        }

        /*!
         * SANITIZE LINK ATTRIBUTES
         * ------------------------------------------------------*/
        $url = $this->sanitizeUrl($parentMenu->getUrl());
        // if ($url === '') {
        //    $url = $this->getSiteUrl();
        // }
        $match = $url && $currentUrl && rtrim($currentUrl, '/') === rtrim($url, '/');
        if ($match) {
            $this->matches[$deep][$parentMenu->getId()] = [];
        }

        /*!
         * SANITIZE TAG ATTRIBUTES
         * ------------------------------------------------------*/
        $currentTag = !in_array($globalTag, ['ol', 'ul']) ? $globalTag : 'li';
        $menuId = Normalizer::normalizeHtmlClass($parentMenu->getId());
        $attrs = $this->sanitizeAttribute($parentMenu->getAttributes());

        if ($deep === 0) {
            $classes = ['parent-menu'];
        } else {
            $classes = ['submenu'];
        }

        $classes[] = sprintf(
            'menu-id-%s',
            $menuId
        );

        if (count($parentMenu->getMenus()) > 0 && $deep < $maxDepth) {
            $classes[] = 'has-submenu';
        }

        if (isset($attrs['class'])) {
            $array = explode(' ', $attrs['class'] ?? '');
            foreach ($array as $menu) {
                $classes[] = Normalizer::normalizeHtmlClass($menu);
            }
        }

        if ($match) {
            $classes[] = 'has-active-submenu';
        }

        $attrLinks = $parentMenu->getLinkAttributes();
        $classesLink = ['menu-link'];
        if (isset($attrLinks['class'])) {
            if (is_string($attrLinks['class'])) {
                $attrLinks['class'] = explode(' ', $attrLinks['class']);
            }
            if (!is_array($attrLinks['class'])) {
                $attrLinks['class'] = (array)$attrLinks['class'];
            }
            foreach ($attrLinks['class'] as $menu) {
                if (!is_string($menu)) {
                    continue;
                }
                $classesLink[] = Normalizer::normalizeHtmlClass($menu);
            }
        }

        if ($match) {
            $classesLink[] = 'current-menu';
            $classesLink[] = 'active';
        }
        $classesLink = array_filter(array_unique($classesLink));
        $linkAttrString = '';
        $linkAttr = [
            'href' => htmlspecialchars($url, ENT_QUOTES | ENT_COMPAT),
            'class' => implode(' ', $classesLink),
        ];

        unset($attrLinks['href'], $attrLinks['id'], $attrLinks['class']);
        foreach ($attrLinks as $menu => $v) {
            $linkAttr[$menu] = $v;
        }

        $linkAttr = $this->sanitizeAttribute($linkAttr);
        foreach ($linkAttr as $k => $menu) {
            $linkAttrString .= " {$k}=\"{$menu}\"";
        }
        unset($linkAttr);

        /*!
         * END
         * ------------------------------------------------------*/
        $subDeep = $deep + 1;
        // $name  = htmlentities($menu->getName());
        $name = $parentMenu->getName();
        $menuStr = '';
        $ids = [];
        foreach ($parentMenu->getMenus() as $menu) {
            $item = $fallBack ? $fallBack(
                $menu,
                $maxDepth,
                $subDeep,
                $currentTag,
                $currentUrl,
                $parentMenu,
                $this
            ) : ($menu->isShown() ? $menu : false);
            if (!$item instanceof Menu && $item !== true) {
                continue;
            }
            $menu = $item instanceof Menu ? $item : $menu;
            $ids[] = $menu->getId();
            $menu = $this->buildMenu(
                $menu,
                $maxDepth,
                $subDeep,
                $globalTag,
                $fallBack,
                $currentUrl
            );

            if ($menu) {
                $menuStr .= "{$menu}\n";
            }
        }

        if (!empty($this->matches) && $deep === 0) {
            $classes[] = 'has-active-submenu';
        }
        if ($deep > 0) {
            foreach ($ids as $idx) {
                if (isset($this->matches[$deep + 1][$idx])) {
                    $classes[] = 'has-active-submenu';
                    break;
                }
            }
        }

        $classes = array_filter(array_unique($classes));
        $attrs['class'] = implode(' ', $classes);
        $attr = '';
        unset($attrs['href'], $attrs['id']);
        foreach ($attrs as $menu => $k) {
            $attr .= " {$menu}=\"{$k}\"";
        }

        unset($attrs, $classes);
        $html = "<{$currentTag}{$attr}>\n";
        $html .= "<a{$linkAttrString}>{$name}</a>\n";
        if ($menuStr !== '') {
            $classes = [
                'nav-menu',
                "sub-menu-id-{$menuId}",
                "sub-nav-menu",
                "sub-nav-menu-level-{$subDeep}"
            ];
            $find = false;
            foreach ($this->matches as $d => $menu) {
                if ($find) {
                    break;
                }
                if ($d <= $deep) {
                    continue;
                }
                foreach ($ids as $idx) {
                    if (isset($menu[$idx])) {
                        $classes[] = 'has-active-submenu';
                        $find = true;
                        break;
                    }
                }
            }

            $classes = implode(' ', $classes);
            $attribute = " class=\"{$classes}\"";
            $attribute .= " data-depth=\"{$subDeep}\"";
            $html .= "<{$globalTag}{$attribute}>\n";
            $html .= "{$menuStr}";
            $html .= "</{$globalTag}>\n";
        }

        unset($menuStr);

        $html .= "</{$currentTag}>";
        return $html;
    }

    /**
     * @param string $tag
     * @param array $attrs
     * @param int|null $maxDepth
     * @param bool $sort
     * @param callable|null $fallBack
     * @param string|null $currentUrl
     * @return array
     */
    public function build(
        string $tag = 'ul',
        array $attrs = [],
        int $maxDepth = null,
        bool $sort = true,
        callable $fallBack = null,
        string $currentUrl = null
    ): array {
        $tag = strtolower(trim($tag));
        if (!$tag || !preg_match('#([uo]l|div)#i', $tag)) {
            $tag = 'ul';
        }

        $maxDepth = $maxDepth === null
            ? self::DEFAULT_DEPTH
            : ($maxDepth < 1 ? 0 : $maxDepth);
        $maxDepth = $maxDepth > $this->max_depth
            ? $this->max_depth
            : $maxDepth;

        $attrs = $this->sanitizeAttribute($attrs);
        $classes = explode(' ', $this->default_main_menu_class);

        foreach ($classes as $k => $item) {
            unset($classes[$k]);
            $classes[] = Normalizer::normalizeHtmlClass($item);
        }

        $newClasses = [];
        if (isset($attrs['class'])) {
            $array = explode(' ', $attrs['class'] ?? '');
            foreach ($array as $item) {
                $newClasses[] = Normalizer::normalizeHtmlClass($item);
            }
        }
        if (empty($newClasses)) {
            $newClasses = $classes;
        }

        $classes = array_filter(array_unique($newClasses));
        $attrs['class'] = implode(' ', $classes);
        unset($newClasses);

        $attr = '';
        foreach ($attrs as $item => $k) {
            $attr .= " {$item}=\"{$k}\"";
        }

        $maxDepth = $maxDepth < 0 ? 0 : $maxDepth;
        $maxDepth = $maxDepth > $this->max_depth ? $this->max_depth : $maxDepth;
        if ($maxDepth > self::MAX_ALLOWED_DEPTH) {
            $maxDepth = self::MAX_ALLOWED_DEPTH;
        }
        $html = [];
        $html[] = sprintf(
            '<%s%s>%s',
            $tag,
            $attr,
            "\n"
        );

        $currentUrl = $currentUrl !== null
            ? $this->sanitizeUrl($currentUrl)
            : $currentUrl;
        $depth = 0;
        $found = false;
        foreach (($sort ? $this->sortMenu($this->menus) : $this->menus) as $menu) {
            $item = $fallBack ?  $fallBack(
                    $menu,
                    $maxDepth,
                    $depth,
                    $tag,
                    $currentUrl,
                    $menu,
                    $this
            ) : ($menu->isShown() ? $menu : false);
            if (!$item instanceof Menu && $item !== true) {
                continue;
            }
            $menu = $item instanceof Menu ? $item : $menu;
            $menu = $this->buildMenu(
                $menu,
                $maxDepth,
                $depth,
                $tag,
                $fallBack,
                $currentUrl
            );
            unset($this->matches);
            if ($menu) {
                $found = true;
                $html[] = $menu;
            }
        }

        // fallback found
        // @todo
        $html[] = sprintf(
            '</%s>',
            $tag
        );

        return $html;
    }


    /**
     * @param Menu[] $menu
     * @return Menu[]
     */
    protected function sortMenu(array $menu): array
    {
        $menus = [];
        foreach ($menu as $item) {
            $menus[$item->getPosition()] = $menu;
        }
        ksort($menus, SORT_ASC);
        foreach ($menu as $k => $item) {
            unset($menu[$k]);
            $menu[$item->getId()] = $item;
        }
        return $menu;
    }

    public function serialize()
    {
        return serialize(get_object_vars($this));
    }

    public function unserialize($serialized)
    {
        if (!is_string($serialized)) {
            return;
        }
        $serialized = @unserialize($serialized);
        if (!is_array($serialized)) {
            return;
        }

        foreach ($serialized as $item => $value) {
            $this->$item = $value;
        }
    }

    /**
     * @return string
     */
    public function __tostring(): string
    {
        return serialize($this);
    }

    /**
     * @param array $menus
     * @return Menus
     */
    public function fromArray(array $menus): Menus
    {
        $clones = clone $this;
        $clones->menus = [];
        foreach ($menus as $key => $item) {
            if ($item instanceof Menu) {
                $clones->add($item);
                continue;
            }

            if (is_string($item)) {
                if (is_string($key) && !isset($clones->menus[$key])) {
                    $clones->addMenu($key, $item);
                }
                continue;
            }

            if (!is_array($item)
                || !isset($item['name'])
                || !is_string($item['name'])
            ) {
                continue;
            }

            $name = $item['name']??(
                    $item['menu_name']??(
                        $item['title']??null
                    )
                );
            if (!is_string($name)) {
                continue;
            }

            $attr = $item['attributes'] ?? (
                    $item['attribute'] ?? (
                        $item['attribute'] ?? (
                            $item['attrs'] ?? ($item['attr'] ?? [])
                        )
                    )
                );
            if (!is_array($attr)) {
                $attr = [];
            }
            $linkAttr = $item['link_attributes'] ?? (
                    $item['link_attribute'] ?? (
                        $item['link_attrs'] ?? (
                            $item['link_attr'] ?? (
                                $item['linkattributes']??(
                                    $item['linkattribute']??[]
                                )
                            )
                        )
                    )
                );
            if (!is_array($linkAttr)) {
                $linkAttr = [];
            }
            $link = $item['href'] ?? (
                    $item['link'] ?? (
                        $linkAttr['href'] ?? (
                            $linkAttr['link'] ?? (
                                $link['url']??(
                                    $link['uri']??''
                                )
                            )
                        )
                    )
                );
            if (!is_string($link)) {
                $link = $clones->getSiteUrl();
            }
            $position = $item['position'] ?? (
                    $item['pos'] ?? (
                        $item['priority'] ?? 10
                    )
                );
            if (!is_numeric($position)) {
                $position = 10;
            }
            if (!is_int($position)) {
                $position = abs(intval($position));
            }
            $subMenu = $item['menus'] ?? (
                    $item['sub_menus'] ?? (
                        $item['sub_menus'] ?? (
                            $item['sub_menu']??(
                                $item['submenus']??(
                                    $item['submenu']??null
                                )
                            )
                        )
                    )
                );
            $show = true;
            if (isset($item['shown']) && is_bool($item['shown'])) {
                $show = $item['shown'];
            } elseif (isset($item['show']) && is_bool($item['show'])) {
                $show = $item['show'];
            } elseif (isset($item['display']) && is_bool($item['display'])) {
                $show = $item['display'];
            }
            // prior
            if (isset($item['hide']) && is_bool($item['hide'])) {
                $show = !$item['hide'];
            } elseif (isset($item['hidden']) && is_bool($item['hidden'])) {
                $show = !$item['hidden'];
            }

            $sub = $clones->addMenu(
                $key,
                $name,
                $link,
                $attr,
                $linkAttr,
                $position,
                $show
            );
            unset($item);
            if (!is_array($subMenu)) {
                continue;
            }

            $subMenu = $this->fromArray($subMenu);
            foreach ($subMenu->getMenus() as $i => $m) {
                $sub->add($m);
            }
        }

        return $clones;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
