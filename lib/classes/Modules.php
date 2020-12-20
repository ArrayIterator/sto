<?php

namespace ArrayIterator;

use ArrayIterator\Helper\Path;
use ArrayIterator\Info\Module;
use ArrayIterator\Traits\DataReaderTrait;

/**
 * Class Modules
 * @package ArrayIterator
 */
final class Modules
{
    use DataReaderTrait;

    /**
     * @var string
     */
    protected $modulesDir;
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
        'name' => ['Name', 'Module Name', 'ModuleName'],
        'uri' => ['URI', '@link'],
        'description' => ['Description', '@description'],
        'author_uri' => ['Author URI', 'AuthorURI'],
        'version' => ['Version', '@version'],
        'license' => ['License', '@license'],
        'author' => ['Author', '@author'],
        'site_wide' => ['SiteWide', 'Site Wide'],
    ];

    /**
     * @var array
     */
    protected $modules = null;

    /**
     * @var array[]
     */
    private static $fileLists = [];

    /**
     * Modules constructor.
     * @param string $modulesDir
     */
    public function __construct(string $modulesDir)
    {
        $this->modulesDir = Path::normalizeDirectory(realpath($modulesDir) ?: $modulesDir);
    }

    /**
     * @return string
     */
    public function getModulesDir(): string
    {
        return $this->modulesDir;
    }

    /**
     * @return array
     */
    public function scan(): array
    {
        if (isset(self::$fileLists[$this->modulesDir])) {
            return self::$fileLists[$this->modulesDir];
        }

        self::$fileLists[$this->modulesDir] = [];
        if (!is_dir($this->modulesDir)) {
            return [];
        }

        $dir = dir($this->modulesDir);
        if (!$dir) {
            return [];
        }
        $mods =& self::$fileLists[$this->modulesDir];
        while (false !== ($entry = $dir->read())) {
            if ('.' == $entry || '..' == $entry || $entry[0] == '.') {
                continue;
            }
            $path = $this->modulesDir . DIRECTORY_SEPARATOR . $entry;
            $mods[$entry] = [
                'type' => is_dir($path) ? 'd' : 'f',
                'name' => $entry,
            ];
            if ($mods[$entry]['type'] === 'd') {
                $dir2 = dir($path);
                if (!$dir2) {
                    continue;
                }
                $mods[$entry]['plugin'] = false;
                $mods[$entry]['info'] = [];
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

                    if (!$mods[$entry]['plugin'] && $entry . '.php' === $name) {
                        $mods[$entry]['plugin'] = $name;
                        $mods[$entry]['info'] = null;
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
     * @return Module|false
     */
    public function getModule(string $name)
    {
        return $this->getMod($name);
    }

    /**
     * @param string $name
     * @param array|null $data
     * @return Module|false|mixed
     */
    private function getMod(string $name, array $data = null)
    {
        if (isset($this->modules[$name])) {
            return $this->modules[$name];
        }

        if (isset($this->invalidList[$name])) {
            return false;
        }

        $this->invalidList[$name] = false;
        $data = $data === null ? ($this->scan()[$name] ?? null) : $data;
        if ($data === null) {
            return false;
        }
        if ($data['type'] === 'f' || empty($data['plugin'])) {
            return false;
        }

        $this->completed--;
        unset($this->invalidList[$name]);
        $path = $this->modulesDir . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $data['plugin'];
        if (!is_array($data['info'])) {
            $data['info'] = $this->readModuleInfo($path);
            $data['base_module_name'] = $name;
            self::$fileLists[$this->modulesDir][$name]['info'] = $data['info'];
        }

        $this->modules[$name] = new Module(
            $path,
            $data['info']
        );

        return $this->modules[$name];
    }

    /**
     * @return Module[]
     */
    public function getModules(): array
    {
        if ($this->completed < 1 && is_array($this->modules)) {
            return $this->modules;
        }
        if (!is_array($this->modules)) {
            $this->modules = [];
        }
        foreach ($this->scan() as $item => $key) {
            $this->getMod($item, $key);
        }

        return $this->modules;
    }

    /**
     * @return Module[]
     */
    public function getValidModules(): array
    {
        $modules = [];
        foreach ($this->getModules() as $name => $item) {
            if ($item->isValid()) {
                $modules[$name] = $item;
            }
        }

        return $modules;
    }

    protected function readModuleInfo(string $fileName): array
    {
        $info = $this->readData($fileName, $this->headers);
        if (in_array(strtolower($info['site_wide']), ['yes', 'true', '1'])) {
            $info['site_wide'] = true;
        } else {
            $info['site_wide'] = false;
        }

        return $info;
    }
}
