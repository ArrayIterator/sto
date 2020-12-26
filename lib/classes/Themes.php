<?php

namespace ArrayIterator;

use ArrayIterator\Helper\Path;
use ArrayIterator\Info\Theme;
use ArrayIterator\Traits\DataReaderTrait;

/**
 * Class Themes
 * @package ArrayIterator
 */
final class Themes
{
    use DataReaderTrait;

    /**
     * File To Read
     */
    const DATA_READ_FILE = 'theme.css';

    /**
     * @var string
     */
    protected $themeDir;
    /**
     * @var bool
     */
    protected $completed = 0;

    /**
     * @var array
     */
    protected $invalidList = [];

    /**
     * @var string[][]
     */
    protected $headers = [
        'name' => ['Name', 'Theme Name', 'ThemeName'],
        'uri' => ['URI', '@link'],
        'author_uri' => ['Author URI', 'AuthorURI'],
        'version' => ['Version', '@version'],
        'license' => ['License', '@license'],
        'author' => ['Author', '@author'],
        // 'site_wide' => ['SiteWide', 'Site Wide'],
    ];

    /**
     * @var string[]
     */
    protected $filesDefault = [
        self::DATA_READ_FILE,
        'functions.php',
        'header.php',
        'footer.php',
        'home.php',
        'login.php',
        'register.php',
        'forgot.php',
        'post.php',
        '404.php',
        '405.php',
        '500.php',
    ];

    /**
     * @var array
     */
    protected $themes = null;

    /**
     * @var array[]
     */
    private static $fileLists = [];

    /**
     * Modules constructor.
     * @param string $themeDir
     */
    public function __construct(string $themeDir)
    {
        $this->themeDir = Path::normalizeDirectory(realpath($themeDir) ?: $themeDir);
    }

    /**
     * @return array
     */
    public function getInvalidList(): array
    {
        return $this->invalidList;
    }

    /**
     * @return string[]
     */
    public function getFilesDefault(): array
    {
        return $this->filesDefault;
    }

    /**
     * @return string
     */
    public function getThemeDir(): string
    {
        return $this->themeDir;
    }

    /**
     * @return array
     */
    public function scan(): array
    {
        if (isset(self::$fileLists[$this->themeDir])) {
            return self::$fileLists[$this->themeDir];
        }

        self::$fileLists[$this->themeDir] = [];
        if (!is_dir($this->themeDir)) {
            return [];
        }

        $dir = dir($this->themeDir);
        if (!$dir) {
            return [];
        }
        $mods =& self::$fileLists[$this->themeDir];
        while (false !== ($entry = $dir->read())) {
            if ('.' == $entry || '..' == $entry || $entry[0] == '.') {
                continue;
            }
            $path = $this->themeDir . DIRECTORY_SEPARATOR . $entry;
            $mods[$entry] = [
                'type' => is_dir($path) ? 'd' : 'f',
                'name' => $entry,
            ];

            if ($mods[$entry]['type'] === 'd') {
                $dir2 = dir($path);
                if (!$dir2) {
                    continue;
                }
                $mods[$entry]['style'] = false;
                $mods[$entry]['info'] = null;
                $mods[$entry]['sub'] = [];
                while (false !== ($name = $dir2->read())) {
                    if ('.' == $name || '..' == $name || $name[0] == '.') {
                        continue;
                    }

                    $subPath = $path . DIRECTORY_SEPARATOR . $name;
                    $mods[$entry]['sub'][$name] = [
                        'type' => is_dir($subPath) ? 'd' : 'f',
                        'name' => $name,
                    ];
                    if (!$mods[$entry]['style'] && self::DATA_READ_FILE === $name) {
                        $mods[$entry]['style'] = $name;
                        $this->completed++;
                        continue;
                    }
                }

                $dir2->close();
            }
        }

        $dir->close();

        ksort($mods);
        return $mods;
    }

    /**
     * @param string $name
     * @return Theme|false
     */
    public function getTheme(string $name)
    {
        return $this->getThm($name);
    }

    /**
     * @param string $name
     * @param array|null $data
     * @return Theme|false|mixed
     */
    private function getThm(string $name, array $data = null)
    {
        if (isset($this->themes[$name])) {
            return $this->themes[$name];
        }

        if (isset($this->invalidList[$name])) {
            return false;
        }

        $this->invalidList[$name] = false;
        $data = $data === null ? ($this->scan()[$name] ?? null) : $data;
        if ($data === null) {
            return false;
        }

        if ($data['type'] === 'f' || empty($data['style'])) {
            return false;
        }

        if (empty($data['sub'])) {
            return false;
        }

        $diff = array_diff($this->filesDefault, array_keys($data['sub']));
        if (!empty($diff)) {
            $this->invalidList[$name] = array_values($diff);
            return false;
        }

        $this->completed--;
        unset($this->invalidList[$name]);
        $path = $this->themeDir . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . self::DATA_READ_FILE;
        if (!is_array($data['info'])) {
            $data['info'] = $this->readModuleInfo($path);
            self::$fileLists[$this->themeDir][$name]['info'] = $data['info'];
        }

        $this->themes[$name] = new Theme(
            slash_it(dirname($path)),
            $data['info']
        );

        return $this->themes[$name];
    }

    /**
     * @return Theme[]
     */
    public function getThemes(): array
    {
        if ($this->completed < 1 && is_array($this->themes)) {
            return $this->themes;
        }

        $this->themes = [];
        foreach ($this->scan() as $item => $key) {
            $this->getThm($item, $key);
        }

        return $this->themes;
    }

    /**
     * @return Theme[]
     */
    public function getValidThemes(): array
    {
        return $this->getThemes();
    }

    protected function readModuleInfo(string $fileName): array
    {
        return $this->readData($fileName, $this->headers);
    }
}
