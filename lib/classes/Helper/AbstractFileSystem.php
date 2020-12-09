<?php
/** @noinspection PhpUnusedParameterInspection */

namespace ArrayIterator\Helper;

use Exception;

/**
 * Class FileSystem
 * @package ArrayIterator\Helper
 */
abstract class AbstractFileSystem
{
    /**
     * @var bool
     */
    public $verbose = false;

    /**
     * @var array
     */
    public $cache = [];

    /**
     * @var string
     */
    protected $method = '';

    /**
     * @var Exception
     */
    public $errors = null;

    /**
     * @var array
     */
    public $options = [];

    /**
     * AbstractFileSystem constructor.
     */
    public function __construct()
    {
        // Set the permission constants if not already set.
        if (!defined('FS_CHMOD_DIR')) {
            define('FS_CHMOD_DIR', (fileperms(ROOT_DIR) & 0777 | 0755));
        }
        if (!defined('FS_CHMOD_FILE')) {
            define('FS_CHMOD_FILE', (fileperms(__FILE__) & 0777 | 0644));
        }
    }

    public function rootDir(): string
    {
        $folder = $this->findFolder(ROOT_DIR);
        // Perhaps the FTP folder is rooted at the WordPress install.
        // Check for wp-includes folder in root. Could have some false positives, but rare.
        if (!$folder) {
            $folder = '/';
        }
        return $folder;
    }

    public function uploadsDir()
    {
        return $this->findFolder(UPLOADS_DIR);
    }

    public function modulesDir()
    {
        return $this->findFolder(MODULES_DIR);
    }

    public function themesDir()
    {
        $theme_root = THEMES_PATH;
        // Account for relative theme roots.
        if ('/themes' == THEMES_PATH || !is_dir(THEMES_PATH)) {
            $theme_root = slash_it(ROOT_DIR) . '/' . un_slash_it(THEMES_PATH);
        }

        return $this->findFolder($theme_root);
    }

    public function cacheDir()
    {
        return $this->findFolder(CACHE_DIR);
    }

    public function findFolder(string $folder)
    {
        if (isset($this->cache[$folder])) {
            return $this->cache[$folder];
        }

        if (stripos($this->method, 'ftp') !== false) {
            $constant_overrides = [
                'FTP_BASE' => Path::slashIt(Path::normalize(ROOT_DIR)),
                'FTP_UPLOADS_DIR' => Path::slashIt(Path::normalize(UPLOADS_DIR)),
                'FTP_MODULES_DIR' => Path::slashIt(Path::normalize(MODULES_DIR)),
                'FTP_CACHE_DIR' => Path::slashIt(Path::normalize(CACHE_DIR)),
            ];

            // Direct matches ( folder = CONSTANT/ ).
            foreach ($constant_overrides as $constant => $dir) {
                if (!defined($constant)) {
                    continue;
                }
                if ($folder === $dir) {
                    return Path::slashIt(constant($constant));
                }
            }

            // Prefix matches ( folder = CONSTANT/sub dir ),
            foreach ($constant_overrides as $constant => $dir) {
                if (!defined($constant)) {
                    continue;
                }
                if (0 === stripos($folder, $dir)) { // $folder starts with $dir.
                    $potential_folder = preg_replace('#^' . preg_quote($dir, '#') . '/#i',
                        Path::slashIt(constant($constant)), $folder);
                    $potential_folder = Path::slashIt($potential_folder);

                    if ($this->isDir($potential_folder)) {
                        $this->cache[$folder] = $potential_folder;
                        return $potential_folder;
                    }
                }
            }
        } elseif ('direct' == $this->method) {
            $folder = str_replace('\\', '/', $folder); // Windows path sanitization.
            return Path::slashIt($folder);
        }

        $folder = preg_replace('|^([a-z]):|i', '', $folder); // Strip out Windows drive letter if it's there.
        $folder = str_replace('\\', '/', $folder); // Windows path sanitization.

        if (isset($this->cache[$folder])) {
            return $this->cache[$folder];
        }

        if ($this->exists($folder)) { // Folder exists at that absolute path.
            $folder = Path::slashIt($folder);
            $this->cache[$folder] = $folder;
            return $folder;
        }
        $return = $this->searchForFolder($folder);
        if ($return) {
            $this->cache[$folder] = $return;
        }
        return $return;
    }

    public function searchForFolder(string $folder, string $base = '.', bool $loop = false)
    {
        if (empty($base) || '.' == $base) {
            $base = Path::slashIt($this->cwd());
        }

        $folder = Path::slashIt($folder);

        if ($this->verbose) {
            /* translators: 1: Folder to locate, 2: Folder to start searching from. */
            printf("\n" . 'Looking for %1$s in %2$s' . "<br/>\n", $folder, $base);
        }

        $folder_parts = explode('/', $folder);
        $folder_part_keys = array_keys($folder_parts);
        $last_index = array_pop($folder_part_keys);
        $last_path = $folder_parts[$last_index];

        $files = $this->dirList($base);

        foreach ($folder_parts as $index => $key) {
            if ($index == $last_index) {
                continue; // We want this to be caught by the next code block.
            }

            /*
             * Working from /home/ to /user/ to /wordpress/ see if that file exists within
             * the current folder, If it's found, change into it and follow through looking
             * for it. If it can't find WordPress down that route, it'll continue onto the next
             * folder level, and see if that matches, and so on. If it reaches the end, and still
             * can't find it, it'll return false for the entire function.
             */
            if (isset($files[$key])) {

                // Let's try that folder:
                $newDir = Path::slashIt(Path::join($base, $key));
                if ($this->verbose) {
                    /* translators: %s: Directory name. */
                    printf("\n" . ('Changing to %s') . "<br/>\n", $newDir);
                }

                // Only search for the remaining path tokens in the directory, not the full path again.
                $newFolder = implode('/', array_slice($folder_parts, $index + 1));
                $ret = $this->searchForFolder($newFolder, $newDir, $loop);
                if ($ret) {
                    return $ret;
                }
            }
        }

        // Only check this as a last resort, to prevent locating the incorrect install.
        // All above procedures will fail quickly if this is the right branch to take.
        if (isset($files[$last_path])) {
            if ($this->verbose) {
                /* translators: %s: Directory name. */
                printf("\n" . ('Found %s') . "<br/>\n", $base . $last_path);
            }
            return Path::slashIt($base . $last_path);
        }

        // Prevent this function from looping again.
        // No need to proceed if we've just searched in `/`.
        if ($loop || '/' == $base) {
            return false;
        }

        // As an extra last resort, Change back to / if the folder wasn't found.
        // This comes into effect when the CWD is /home/user/ but WP is at /var/www/....
        return $this->searchForFolder($folder, '/', true);

    }

    /**
     * Returns the *nix-style file permissions for a file.
     *
     * From the PHP documentation page for fileperms().
     *
     * @link https://www.php.net/manual/en/function.fileperms.php
     *
     * @param string $file String filename.
     * @return string The *nix-style representation of permissions.
     */
    public function getHChmod(string $file): string
    {
        $perms = intval($this->getChmod($file), 8);
        if (($perms & 0xC000) == 0xC000) { // Socket.
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) { // Symbolic Link.
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) { // Regular.
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) { // Block special.
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) { // Directory.
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) { // Character special.
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) { // FIFO pipe.
            $info = 'p';
        } else { // Unknown.
            $info = 'u';
        }

        // Owner.
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x') :
            (($perms & 0x0800) ? 'S' : '-'));

        // Group.
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x') :
            (($perms & 0x0400) ? 'S' : '-'));

        // World.
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x') :
            (($perms & 0x0200) ? 'T' : '-'));
        return $info;
    }

    /**
     * @param string $file
     * @return string|false
     */
    public function getChmod(string $file)
    {
        return '777';
    }

    /**
     * @param string $mode
     * @return int
     */
    public function getNumChmodFromH(string $mode): int
    {
        $realMode = '';
        $legal = ['', 'w', 'r', 'x', '-'];
        $attArray = preg_split('//', $mode);

        for ($i = 0, $c = count($attArray); $i < $c; $i++) {
            $key = array_search($attArray[$i], $legal);
            if ($key) {
                $realMode .= $legal[$key];
            }
        }

        $mode = str_pad($realMode, 10, '-', STR_PAD_LEFT);
        $trans = [
            '-' => '0',
            'r' => '4',
            'w' => '2',
            'x' => '1',
        ];
        $mode = strtr($mode, $trans);

        $newMode = $mode[0];
        $newMode .= $mode[1] + $mode[2] + $mode[3];
        $newMode .= $mode[4] + $mode[5] + $mode[6];
        $newMode .= $mode[7] + $mode[8] + $mode[9];
        return $newMode;
    }

    /**
     * @param string $text
     * @return bool
     */
    public function isBinary(string $text): bool
    {
        return (bool)preg_match('|[^\x20-\x7E]|', $text); // chr(32)..chr(127)
    }

    /**
     * @param string $file
     * @param $owner
     * @param bool $recursive
     * @return bool
     */
    public function chown(string $file, $owner, bool $recursive = false): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function connect(): bool
    {
        return true;
    }

    /**
     * @param string $file
     * @return string|false
     */
    public function getContents(string $file)
    {
        return false;
    }

    /**
     * @param string $file
     * @return array|false
     */
    public function getContentsArray(string $file)
    {
        return false;
    }

    /**
     * @param string $file
     * @param string $contents
     * @param int|null $mode
     * @return bool
     */
    public function putContents(string $file, string $contents, int $mode = null): bool
    {
        return false;
    }

    /**
     * @return string|false
     */
    public function cwd()
    {
        return false;
    }

    /**
     * @param string $dir
     * @return bool
     */
    public function chdir(string $dir): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @param string $group
     * @param bool $recursive
     * @return bool
     */
    public function chgrp(string $file, string $group, bool $recursive = false): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @param int|null $mode
     * @param bool $recursive
     * @return bool
     */
    public function chmod(string $file, int $mode = null, bool $recursive = false): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @return bool|int|mixed|string
     */
    public function owner(string $file)
    {
        return false;
    }

    /**
     * @param string $file
     * @return bool|int|mixed|string
     */
    public function group(string $file)
    {
        return false;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param bool $overwrite
     * @param int|null $mode
     * @return bool
     */
    public function copy(
        string $source,
        string $destination,
        bool $overwrite = false,
        int $mode = null
    ): bool {
        return false;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param bool $overwrite
     * @return bool
     */
    public function move(string $source, string $destination, bool $overwrite = false): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @param bool $recursive
     * @param string|null $type
     * @return bool
     */
    public function delete(string $file, bool $recursive = false, string $type = null): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function exists(string $file): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isFile(string $file): bool
    {
        return false;
    }


    /**
     * @param string $path
     * @return bool
     */
    public function isDir(string $path): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isReadable(string $file): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isWritable(string $file): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @return int|false
     */
    public function atime(string $file)
    {
        return false;
    }

    /**
     * @param string $file
     * @return int|false
     */
    public function mtime(string $file)
    {
        return false;
    }

    /**
     * @param string $file
     * @return int|false
     */
    public function size(string $file)
    {
        return false;
    }

    /**
     * @param string $file
     * @param int $time
     * @param int $atime
     * @return bool
     */
    public function touch(
        string $file,
        int $time = 0,
        int $atime = 0
    ): bool {
        return false;
    }

    /**
     * @param string $path
     * @param int|null $chmod
     * @param string|null $chown
     * @param string|null $chgrp
     * @return bool
     */
    public function mkdir(
        string $path,
        int $chmod = null,
        $chown = null,
        $chgrp = null
    ): bool {
        return false;
    }

    /**
     * @param string $path
     * @param bool $recursive
     * @return bool
     */
    public function rmdir(string $path, bool $recursive = false): bool
    {
        return false;
    }

    /**
     * @param string $path
     * @param bool $include_hidden
     * @param bool $recursive
     * @return array|false
     */
    public function dirList(
        string $path,
        bool $include_hidden = true,
        bool $recursive = false
    ) {
        return false;
    }
}
