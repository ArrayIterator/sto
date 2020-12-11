<?php

namespace ArrayIterator\Traits;

/**
 * Trait DataReaderTrait
 * @package ArrayIterator\Traits
 */
trait DataReaderTrait
{
    /**
     * @param string $file
     * @param array $default_headers
     * @return mixed
     */
    protected function readData(string $file, array $default_headers): array
    {
        if (!file_exists($file)) {
            foreach ($default_headers as $field => $item) {
                $default_headers[$field] = '';
            }
            return $default_headers;
        }

        // We don't need to write to the file, so just open for reading.
        $fp = fopen($file, 'r');

        // Pull only the first 8 KB of the file in.
        $file_data = fread($fp, 8 * KB_IN_BYTES);

        // PHP will close file handle, but we are good citizens.
        fclose($fp);

        // Make sure we catch CR-only line endings.
        $file_data = str_replace("\r", "\n", $file_data);
        foreach ($default_headers as $field => $regex) {
            $default_headers[$field] = '';
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
            }
        }

        return $default_headers;
    }
}

