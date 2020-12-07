<?php

namespace ArrayIterator;

use ArrayIterator\Helper\Path;

/**
 * Class Modules
 * @package ArrayIterator
 */
final class Modules
{
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
                    }

                    if ($mods[$entry]['plugin']) {
                        $mods[$entry]['info'] = null;
                        $this->completed++;
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
        if ($data['type'] === 'f' || !$data['plugin']) {
            return false;
        }
        $this->completed--;
        unset($this->invalidList[$name]);
        $path = $this->modulesDir . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $data['plugin'];
        if (!is_array($data['info'])) {
            $data['info'] = $this->readPluginInfo($path);
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

        $this->modules = [];
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

    protected function readPluginInfo(string $fileName): array
    {
        $info = $this->readData($fileName, $this->headers);
        if (in_array(strtolower($info['site_wide']), ['yes', 'true'])) {
            $info['site_wide'] = true;
        } else {
            $info['site_wide'] = false;
        }

        return $info;
    }

    /**
     * @param string $file
     * @param array $default_headers
     * @return mixed
     */
    protected function readData(string $file, array $default_headers): array
    {
        // We don't need to write to the file, so just open for reading.
        $fp = fopen($file, 'r');

        // Pull only the first 8 KB of the file in.
        $file_data = fread($fp, 8 * KB_IN_BYTES);

        // PHP will close file handle, but we are good citizens.
        fclose($fp);

        // Make sure we catch CR-only line endings.
        $file_data = str_replace("\r", "\n", $file_data);

        foreach ($default_headers as $field => $regex) {
            if (!$regex) {
                continue;
            }
            if (is_array($regex)) {
                $prv = '';
                $regex = '(?:' . implode('|', array_map(function ($regex) {
                        $regex = preg_quote($regex, '/');
                        $prv = $regex[0] === '@' ? '(?:[:]*|[ ]*)' : '[ ]*:';
                        return $regex . $prv;
                    }, $regex)) . ')';
            } else {
                $regex = preg_quote($regex, '/');
                $prv = $regex[0] === '@' ? '(?:[:]*|[ ]*)' : '[ ]*:';
            }
            if (preg_match('/^[ \t\/*#@]*' . $regex . $prv . '(.*)$/mi', $file_data, $match) && $match[1]) {
                $default_headers[$field] = trim(preg_replace('/\s*(?:\*\/|\?>).*/', '', $match[1]));
            } else {
                $default_headers[$field] = '';
            }
        }

        return $default_headers;
    }
}
