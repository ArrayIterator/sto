<?php

namespace ArrayIterator;

use ArrayIterator\Traits\SimpleAttributeFilter;
use Serializable;

/**
 * Class Menu
 * @package ArrayIterator
 */
class Menu implements Serializable
{
    use SimpleAttributeFilter;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var array
     */
    protected $link_attributes = [];

    /**
     * @var Menu[]
     */
    protected $menus = [];

    /**
     * @var int
     */
    protected $position = 10;

    /**
     * Menu constructor.
     * @param string $id
     * @param string $name
     * @param string $url
     * @param array $attr
     * @param array $attrLink
     * @param int $position
     */
    public function __construct(
        string $id,
        string $name,
        string $url = '',
        array $attr = [],
        array $attrLink = [],
        int $position = 10
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        foreach ($attr as $key => $val) {
            $this->setAttributes($key, $val);
        }
        foreach ($attrLink as $key => $val) {
            $this->setLinkAttribute($key, $val);
        }

        $this->position = $position;
    }

    /**
     * @return array
     */
    public function getLinkAttributes(): array
    {
        return $this->link_attributes;
    }

    public function setLinkAttribute($name, $value): bool
    {
        $val = $this->filterAttribute($name, $value);
        if (!is_array($val)) {
            return false;
        }

        $this->link_attributes[$val[0]] = $val[1];
        return true;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     * @return bool
     */
    public function setAttributes($name, $value): bool
    {
        $val = $this->filterAttribute($name, $value);
        if (!is_array($val)) {
            return false;
        }

        $this->attributes[$val[0]] = $val[1];
        return true;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return Menu[]
     */
    public function getMenus(): array
    {
        return $this->menus;
    }

    public function add(Menu $menu)
    {
        $this->menus[$menu->id] = $menu;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function exist(string $id): bool
    {
        return isset($this->menus[$id]);
    }

    /**
     * @param string|Menu $menu
     * @return bool
     */
    public function remove($menu): bool
    {
        if ($menu instanceof Menu) {
            $menu = $menu->id;
        }
        if (!is_string($menu)) {
            return false;
        }
        // remove
        unset($this->menus[$menu]);
        return true;
    }

    public function serialize(): string
    {
        return serialize(get_object_vars($this));
    }

    /**
     * @param $serialized
     */
    public function unserialize($serialized)
    {
        if (!is_string($serialized)) {
            return;
        }
        $serialized = @unserialize($serialized);
        if (!is_array($serialized)) {
            return;
        }

        foreach ($serialized as $key => $val) {
            $this->$key = $val;
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
