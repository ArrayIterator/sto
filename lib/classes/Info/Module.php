<?php

namespace ArrayIterator\Info;

use ArrayIterator\Helper\Path;

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
     * @var string|null
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
     * Module constructor.
     * @param string $path
     * @param array $info
     */
    public function __construct(string $path, array $info)
    {
        parent::__construct($path, $info);
        $this->valid = $this->info['name'] ?? '';
        $this->valid = !empty($this->valid)
            && is_string($this->valid)
            && trim($this->info['name']) !== '';
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
}
