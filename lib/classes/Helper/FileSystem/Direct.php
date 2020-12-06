<?php

namespace ArrayIterator\Helper\FileSystem;

use ArrayIterator\Helper\AbstractFileSystem;
use ArrayIterator\Helper\Path;
use ArrayIterator\Helper\StringFilter;

/**
 * Class Direct
 * @package ArrayIterator\Helper\FileSystem
 */
class Direct extends AbstractFileSystem
{
    public function __construct()
    {
        $this->method = 'direct';
        $this->errors = null;
        parent::__construct();
    }

    public function getContents(string $file)
    {
        return $this->isFile($file)
            ? @file_get_contents($file)
            : false;
    }

    public function getContentsArray(string $file)
    {
        return $this->isFile($file)
            ? @file($file)
            : false;
    }

    public function putContents(string $file, string $contents, int $mode = null): bool
    {
        if (!$this->isFile($file)) {
            return false;
        }
        $fp = @fopen($file, 'wb');
        if (!$fp) {
            return false;
        }

        StringFilter::mbStringBinarySafeEncoding();

        $data_length = strlen($contents);
        $bytes_written = fwrite($fp, $contents);
        StringFilter::resetMbStringEncoding();

        fclose($fp);

        if ($data_length !== $bytes_written) {
            return false;
        }

        $this->chmod($file, $mode);

        return true;
    }

    public function cwd()
    {
        return getcwd();
    }

    public function chdir(string $dir): bool
    {
        return $this->exists($dir)
            ? @chdir($dir)
            : false;
    }

    public function chgrp(string $file, string $group, bool $recursive = false): bool
    {
        if (!$this->exists($file)) {
            return false;
        }
        if (!$recursive) {
            return chgrp($file, $group);
        }
        if (!$this->isDir($file)) {
            return chgrp($file, $group);
        }
        // Is a directory, and we want recursive.
        $file = Path::slashIt($file);
        $fileList = $this->dirList($file);
        foreach ($fileList as $filename) {
            $this->chgrp($file . $filename, $group, $recursive);
        }

        return true;
    }

    public function chmod(string $file, int $mode = null, bool $recursive = false): bool
    {
        if (!$mode) {
            if ($this->isFile($file)) {
                $mode = FS_CHMOD_FILE;
            } elseif ($this->isDir($file)) {
                $mode = FS_CHMOD_DIR;
            } else {
                return false;
            }
        }

        if (!$recursive || !$this->isDir($file)) {
            return chmod($file, $mode);
        }
        // Is a directory, and we want recursive.
        $file = Path::slashIt($file);
        $fileList = $this->dirList($file);
        foreach ((array)$fileList as $filename => $filemeta) {
            $this->chmod($file . $filename, $mode, $recursive);
        }

        return true;
    }

    public function chown(string $file, $owner, bool $recursive = false): bool
    {
        if (!$this->exists($file)) {
            return false;
        }
        if (!$recursive) {
            return chown($file, $owner);
        }
        if (!$this->isDir($file)) {
            return chown($file, $owner);
        }
        // Is a directory, and we want recursive.
        $fileList = $this->dirList($file);
        foreach ($fileList as $filename) {
            $this->chown($file . '/' . $filename, $owner, $recursive);
        }
        return true;
    }

    public function owner(string $file)
    {
        if (!$this->exists($file)) {
            return false;
        }
        $uid = @fileowner($file);
        if (!$uid) {
            return false;
        }
        if (!function_exists('posix_getpwuid')) {
            return $uid;
        }
        $owner = posix_getpwuid($uid);
        return $owner['name'];
    }

    public function getChmod(string $file)
    {
        if (!$this->exists($file)) {
            return false;
        }
        $file = @fileperms($file);
        if (!$file) {
            return false;
        }
        return substr(decoct($file), -3);
    }

    public function group(string $file)
    {
        if (!$this->exists($file)) {
            return false;
        }
        $gid = @filegroup($file);
        if (!$gid) {
            return false;
        }
        if (!function_exists('posix_getgrgid')) {
            return $gid;
        }
        $group = posix_getgrgid($gid);
        return $group['name'];
    }

    public function copy(string $source, string $destination, bool $overwrite = false, int $mode = null): bool
    {
        if (!$overwrite && $this->exists($destination)
            || !$this->exists($source)
        ) {
            return false;
        }

        $retVal = copy($source, $destination);
        if ($mode) {
            $this->chmod($destination, $mode);
        }
        return $retVal;
    }

    public function move(string $source, string $destination, bool $overwrite = false): bool
    {
        if (!$overwrite && $this->exists($destination)
            | !$this->exists($destination)
        ) {
            return false;
        }

        // Try using rename first. if that fails (for example, source is read only) try copy.
        if (@rename($source, $destination)) {
            return true;
        }

        if ($this->copy($source, $destination, $overwrite) && $this->exists($destination)) {
            $this->delete($source);
            return true;
        } else {
            return false;
        }
    }

    public function delete(string $file, bool $recursive = false, string $type = null): bool
    {
        if (empty($file)) {
            // Some filesystems report this as /, which can cause non-expected recursive deletion of all files in the filesystem.
            return false;
        }

        $file = str_replace('\\', '/', $file); // For Win32, occasional problems deleting files otherwise.

        if ('f' == $type || $this->isFile($file)) {
            return @unlink($file);
        }
        if (!$recursive && $this->isDir($file)) {
            return @rmdir($file);
        }

        // At this point it's a folder, and we're in recursive mode.
        $file = Path::slashIt($file);
        $fileList = $this->dirList($file, true);

        $retVal = true;
        if (is_array($fileList)) {
            foreach ($fileList as $filename => $fileInfo) {
                if (!$this->delete($file . $filename, $recursive, $fileInfo['type'])) {
                    $retVal = false;
                }
            }
        }

        if (file_exists($file) && !@rmdir($file)) {
            $retVal = false;
        }

        return $retVal;
    }

    public function exists(string $file): bool
    {
        return @file_exists($file);
    }

    public function isFile(string $file): bool
    {
        return @is_file($file);
    }

    public function isDir(string $path): bool
    {
        return @is_dir($path);
    }

    public function isReadable(string $file): bool
    {
        return @is_readable($file);
    }

    public function isWritable(string $file): bool
    {
        return @is_writable($file);
    }

    public function atime(string $file)
    {
        return @fileatime($file);
    }

    public function mtime(string $file)
    {
        return @filemtime($file);
    }

    public function size(string $file)
    {
        return @filesize($file);
    }

    public function touch(string $file, int $time = 0, int $atime = 0): bool
    {
        if (0 == $time) {
            $time = time();
        }
        if (0 == $atime) {
            $atime = time();
        }
        return touch($file, $time, $atime);
    }

    public function mkdir(string $path, int $chmod = null, $chown = null, $chgrp = null): bool
    {

        // Safe mode fails with a trailing slash under certain PHP versions.
        $path = Path::slashIt($path);
        if (empty($path)) {
            return false;
        }

        if (!$chmod) {
            $chmod = FS_CHMOD_DIR;
        }

        if (!@mkdir($path)) {
            return false;
        }
        $this->chmod($path, $chmod);
        if ($chown) {
            $this->chown($path, $chown);
        }
        if ($chgrp) {
            $this->chgrp($path, $chgrp);
        }
        return true;
    }

    public function rmdir(string $path, bool $recursive = false): bool
    {
        return $this->delete($path, $recursive);
    }

    public function dirList(string $path, bool $include_hidden = true, bool $recursive = false)
    {
        if ($this->isFile($path)) {
            $limit_file = basename($path);
            $path = dirname($path);
        } else {
            $limit_file = false;
        }

        if (!$this->isDir($path) || !$this->isReadable($path)) {
            return false;
        }

        $dir = dir($path);
        if (!$dir) {
            return false;
        }

        $ret = [];

        while (false !== ($entry = $dir->read())) {
            $strUC = [];
            $strUC['name'] = $entry;

            if ('.' == $strUC['name'] || '..' == $strUC['name']) {
                continue;
            }

            if (!$include_hidden && '.' == $strUC['name'][0]) {
                continue;
            }

            if ($limit_file && $strUC['name'] != $limit_file) {
                continue;
            }

            $strUC['perms'] = $this->getHChmod($path . '/' . $entry);
            $strUC['permsn'] = $this->getNumChmodFromH($strUC['perms']);
            $strUC['number'] = false;
            $strUC['owner'] = $this->owner($path . '/' . $entry);
            $strUC['group'] = $this->group($path . '/' . $entry);
            $strUC['size'] = $this->size($path . '/' . $entry);
            $strUC['lastmodunix'] = $this->mtime($path . '/' . $entry);
            $strUC['lastmod'] = gmdate('M j', $strUC['lastmodunix']);
            $strUC['time'] = gmdate('h:i:s', $strUC['lastmodunix']);
            $strUC['type'] = $this->isDir($path . '/' . $entry) ? 'd' : 'f';

            if ('d' == $strUC['type']) {
                if ($recursive) {
                    $strUC['files'] = $this->dirList($path . '/' . $strUC['name'], $include_hidden, $recursive);
                } else {
                    $strUC['files'] = [];
                }
            }

            $ret[$strUC['name']] = $strUC;
        }
        $dir->close();
        unset($dir);
        return $ret;
    }
}
