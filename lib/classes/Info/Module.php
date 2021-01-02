<?php

namespace ArrayIterator\Info;

use ArrayIterator\Helper\Path;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class Module
 * @package ArrayIterator\Info
 */
class Module extends AbstractInfo
{
    /**
     * @var bool
     */
    protected $valid;

    /**
     * @var Module[]
     */
    protected static $loaded = [];

    /**
     * @var array|null
     */
    protected $logo = null;

    /**
     * @var string[]
     */
    protected $logo_paths = [
        'logo.png',
        'logo.webp',
        'logo.jpg',
        'logo.gif',
    ];

    /**
     * @var string
     */
    private $className;

    /**
     * @var int|null
     */
    protected $loaded_time = null;

    /**
     * @var bool
     */
    protected $active_time = null;

    /**
     * Module constructor.
     * @param string $path
     * @param array $info
     */
    public function __construct(
        string $path,
        array $info
    ) {
        $this->className = get_class($this);
        if (!isset(self::$loaded[$this->className])) {
            self::$loaded[$this->className] = [];
        }

        parent::__construct($path, $info);
        $this->valid = isset($this->info['name']) && is_string($this->info['name']);
        $this->valid = $this->valid && trim($this->info['name']) !== '';
    }

    /**
     * @return false|int
     */
    public function getActiveTime()
    {
        return $this->active_time;
    }

    /**
     * @return bool
     */
    final public function isLoaded() : bool
    {
        if (!isset(self::$loaded[$this->className])) {
            return false;
        }

        $path = $this->getDirname();
        return (self::$loaded[$this->className][$path]??null) instanceof $this;
    }

    /**
     * @return int|false
     */
    public function getLoadedTime()
    {
        return $this->loaded_time;
    }

    /**
     * @return string
     */
    public function getDirname() : string
    {
        return basename($this->getPath());
    }

    /**
     * @return string
     */
    public function getBaseModuleName() : string
    {
        if (!isset($this->info['base_module_name'])) {
            $this->info['base_module_name'] = basename($this->getDirname());
        }
        return $this->info['base_module_name'];
    }

    /**
     * @return array
     */
    public function getLoadedModules() : array
    {
        return self::$loaded;
    }

    final public function load($active_time = null)
    {
        $path = $this->getDirname();
        if (isset(self::$loaded[$this->className][$path])) {
            return self::$loaded[$this->className][$path];
        }

        unset(self::$loaded[$this->className][$path]);
        if (file_exists($this->getPath())) {
            $active_time = !is_numeric($active_time) ? null : $active_time;
            $active_time = $active_time ? abs($active_time) : null;
            $this->loaded_time = strtotime(gmdate('Y-m-d H:i:s'));
            $this->active_time = $active_time??$this->loaded_time;
            self::$loaded[$this->className][$path] =& $this;
            /** @noinspection PhpIncludeInspection */
            require_once $this->getPath();
            return $this;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->loaded_time && $this->active_time;
    }

    /**
     * @return string
     */
    public function getDirectory() : string
    {
        return dirname($this->getPath());
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return bool
     */
    public function isSiteWide(): bool
    {
        return (bool)($this->info['site_wide'] ?? false);
    }

    /**
     * @return array|false
     */
    public function getLogo()
    {
        if ($this->logo !== null) {
            return $this->logo;
        }

        $dir = Path::slashIt(Path::normalize(dirname($this->getPath())));
        $this->logo = false;
        foreach ($this->logo_paths as $item) {
            $path = $dir.$item;
            if (!is_file($path)) {
                continue;
            }

            $size = @getimagesize($path);
            if (false === $size) {
                continue;
            }
            list($width, $height) = $size;
            $this->logo = [
                'path'   => $item,
                'width'  => $width,
                'height' => $height,
            ];
        }

        return $this->logo;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getBaseModuleName(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'license' => $this->getLicense(),
            'version' => $this->getVersion(),
            'uri' => $this->getUri(),
            'author' => $this->getAuthor(),
            'author_uri' => $this->getAuthorUri(),
            'site_wide' => $this->isSiteWide(),
            'valid' => $this->isValid(),
            'active' => $this->isActive(),
            'active_time' => $this->getActiveTime(),
            'loaded_time' => $this->getLoadedTime(),
        ];
    }
}
