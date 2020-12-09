<?php

namespace ArrayIterator\Helper;

/**
 * Class Normalizer
 * @package ArrayIterator\Helper
 */
final class Normalizer
{
    protected static $conversionTables = [
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ð' => 'D',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        '×' => 'x',
        'Ø' => '0',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'b',
        'ß' => 'B',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'o',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        '÷' => '+',
        'ø' => 'o',
        'ù' => 'i',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ý' => 'y',
        'þ' => 'B',
        'ÿ' => 'y',
    ];

    /**
     * @return array
     */
    public static function getConversionTables(): array
    {
        return self::$conversionTables;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function normalizeFileName(
        string $string
    ): string {
        $contains = false;
        $string = preg_replace_callback('~[\xc0-\xff]+~', function ($match) use (&$contains) {
            $contains = true;
            return utf8_encode($match[0]);
        }, $string);
        $string = str_replace("\t", " ", $string);
        // replace whitespace except space to empty character
        $string = preg_replace('~\x0-\x31~', '', $string);
        if ($contains) {
            // normalize ascii extended to ascii utf8
            $string = str_replace(
                array_keys(self::$conversionTables),
                array_values(self::$conversionTables),
                $string
            );
        }

        return preg_replace(
            '~[^0-9A-Za-z\-_()@\~\x32.]~',
            '-',
            $string
        );
    }

    /**
     * @param string $class
     * @param string $fallback
     * @return null|string|string[]
     */
    public static function normalizeHtmlClass(
        string $class,
        string $fallback = ''
    ) {
        $sanitized = trim($class);
        if ($class) {
            $sanitized = preg_replace('|%[a-fA-F0-9][a-fA-F0-9]|', '', $class);
            //Limit to A-Z,a-z,0-9,_,-
            $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '', $sanitized);
        }

        if ('' === $sanitized && $fallback !== '') {
            return self::normalizeHtmlClass($fallback);
        }

        return $sanitized;
    }

    /**
     * @param string $data
     * @return null|string|string[]
     */
    public static function removeJSContent(string $data): string
    {
        return preg_replace(
            '/<(script)[^>]+?>.*?<\/\\1>/smi',
            '',
            $data
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public static function normalizeSeparator(string $path): string
    {
        return preg_replace('~[\\\|/]+~', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Balances tags of string using a modified stack.
     *
     * @param string $text Text to be balanced.
     * @return string Balanced text.
     *
     * Custom mods to be fixed to handle by system result output
     * @copyright November 4, 2001
     * @version 1.1
     *
     * Modified by Scott Reilly (coffee2code) 02 Aug 2004
     *      1.1  Fixed handling of append/stack pop order of end text
     *           Added Cleaning Hooks
     *      1.0  First Version
     *
     * @author Leonard Lin <leonard@acm.org>
     * @license GPL
     */
    public static function forceBalanceTags(string $text) : string
    {
        $tagStack = [];
        $stackSize = 0;
        $tagQueue = '';
        $newText = '';
        // Known single-entity/self-closing tags
        $single_tags = [
            'area',
            'base',
            'basefont',
            'br',
            'col',
            'command',
            'embed',
            'frame',
            'hr',
            'img',
            'input',
            'isindex',
            'link',
            'meta',
            'param',
            'source'
        ];
        $single_tags_2 = [
            'img',
            'meta',
            'link',
            'input'
        ];
        // Tags that can be immediately nested within themselves
        $nestable_tags = ['blockquote', 'div', 'object', 'q', 'span'];
        // check if contains <html> tag and split it
        // fix doctype
        $text = preg_replace('/<(\s+)?!(\s+)?(DOCTYPE)/i', '<!$3', $text);
        $rand = sprintf('%1$s_%2$s_%1$s', '%', mt_rand(10000, 50000));
        $randQuote = preg_quote($rand, '~');
        $text = str_replace('<!', '< ' . $rand, $text);
        // bug fix for comments - in case you REALLY meant to type '< !--'
        $text = str_replace('< !--', '<    !--', $text);
        // bug fix for LOVE <3 (and other situations with '<' before a number)
        $text = preg_replace('#<([0-9])#', '&lt;$1', $text);
        while (preg_match(
            "~<((?!(?:\s+){$randQuote})/?[\w:]*)\s*([^>]*)>~",
            $text,
            $regex
        )) {
            $newText .= $tagQueue;
            $i = strpos($text, $regex[0]);
            $l = strlen($regex[0]);
            // clear the shifter
            $tagQueue = '';
            // Pop or Push
            if (isset($regex[1][0]) && '/' == $regex[1][0]) { // End Tag
                $tag = strtolower(substr($regex[1], 1));
                // if too many closing tags
                if ($stackSize <= 0) {
                    $tag = '';
                    // or close to be safe $tag = '/' . $tag;
                } elseif ($tagStack[$stackSize - 1] == $tag) {
                    // if stack top value = tag close value then pop
                    // found closing tag
                    $tag = '</' . $tag . '>'; // Close Tag
                    // Pop
                    array_pop($tagStack);
                    $stackSize--;
                } else { // closing tag not at top, search for it
                    for ($j = $stackSize - 1; $j >= 0; $j--) {
                        if ($tagStack[$j] == $tag) {
                            // add tag to tag queue
                            for ($k = $stackSize - 1; $k >= $j; $k--) {
                                $tagQueue .= '</' . array_pop($tagStack) . '>';
                                $stackSize--;
                            }
                            break;
                        }
                    }
                    $tag = '';
                }
            } else { // Begin Tag
                $tag = strtolower($regex[1]);
                // Tag Cleaning
                // If it's an empty tag "< >", do nothing
                /** @noinspection PhpStatementHasEmptyBodyInspection */
                if ('' == $tag
                    // ElseIf it's a known single-entity tag but it doesn't close itself, do so
                    // $regex[2] .= '';
                    || in_array($tag, $single_tags_2)
                ) {
                    // do nothing
                } elseif (substr($regex[2], -1) == '/') {
                    // ElseIf it presents itself as a self-closing tag...
                    // ----
                    // ...but it isn't a known single-entity self-closing tag,
                    // then don't let it be treated as such and
                    // immediately close it with a closing tag (the tag will encapsulate no text as a result)
                    if (!in_array($tag, $single_tags)) {
                        $regex[2] = trim(substr($regex[2], 0, -1)) . "></$tag";
                    }
                } elseif (in_array($tag, $single_tags)) {
                    // ElseIf it's a known single-entity tag but it doesn't close itself, do so
                    $regex[2] .= '/';
                } else {
                    // Else it's not a single-entity tag
                    // ---------
                    // If the top of the stack is the same as the tag we want to push, close previous tag
                    if ($stackSize > 0 && !in_array($tag, $nestable_tags)
                        && $tagStack[$stackSize - 1] == $tag
                    ) {
                        $tagQueue = '</' . array_pop($tagStack) . '>';
                        /** @noinspection PhpUnusedLocalVariableInspection */
                        $stackSize--;
                    }
                    $stackSize = array_push($tagStack, $tag);
                }
                // Attributes
                $attributes = $regex[2];
                if (!empty($attributes) && $attributes[0] != '>') {
                    $attributes = ' ' . $attributes;
                }
                $tag = '<' . $tag . $attributes . '>';
                //If already queuing a close tag, then put this tag on, too
                if (!empty($tagQueue)) {
                    $tagQueue .= $tag;
                    $tag = '';
                }
            }
            $newText .= substr($text, 0, $i) . $tag;
            $text = substr($text, $i + $l);
        }
        // Clear Tag Queue
        $newText .= $tagQueue;
        // Add Remaining text
        $newText .= $text;
        unset($text); // freed memory
        // Empty Stack
        while ($x = array_pop($tagStack)) {
            $newText .= '</' . $x . '>'; // Add remaining tags to close
        }
        // fix for the bug with HTML comments
        $newText = str_replace("< {$rand}", "<!", $newText);
        $newText = str_replace("< !--", "<!--", $newText);
        $newText = str_replace("<    !--", "< !--", $newText);
        return $newText;
    }

    /**
     * Set cookie domain with .domain.ext for multi sub domain
     *
     * @param string $domain
     * @return string|null|false $return domain ( .domain.com )
     */
    public static function splitCrossDomain(string $domain)
    {
        // make it domain lower
        $domain = strtolower($domain);
        $domain = preg_replace('~^\s*(?:(http|ftp)s?|sftp|xmp)://~i', '', $domain);
        $domain = preg_replace('~/.*$~', '', $domain);
        $is_ip = filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        if (!$is_ip) {
            $is_ip = filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        }
        if (!$is_ip) {
            $parse = parse_url('http://' . $domain . '/');
            $domain = isset($parse['host']) ? $parse['host'] : null;
            if ($domain === null) {
                return null;
            }
        }
        if (!preg_match('/^((\[[0-9a-f:]+])|(\d{1,3}(\.\d{1,3}){3})|[a-z0-9\-.]+)(:\d+)?$/i', $domain)
            || $is_ip
            || $domain == '127.0.0.1'
            || $domain == 'localhost'
        ) {
            return $domain;
        }
        $domain = preg_replace('~[\~!@#$%^&*()+`{}\]\[/\';<>,\"?=|\\\]~', '', $domain);
        if (strpos($domain, '.') !== false) {
            if (preg_match('~(.*\.)+(.*\.)+(.*)~', $domain)) {
                $return = '.' . preg_replace('~(.*\.)+(.*\.)+(.*)~', '$2$3', $domain);
            } else {
                $return = '.' . $domain;
            }
        } else {
            $return = $domain;
        }
        return $return;
    }

    /**
     * @param string $slug
     * @return string
     */
    public static function normalizeSlug(string $slug): string
    {
        $slug = preg_replace('~[^a-z0-9\-_]~i', '-', trim($slug));
        $slug = preg_replace('~([\-_])+~', '$1', $slug);
        $slug = trim($slug, '-_');
        return $slug;
    }

    /**
     * @param string $slug
     * @param array $slugCollections
     * @return string
     */
    public static function uniqueSlug(string $slug, array $slugCollections) : string
    {
        $separator = '-';
        $inc = 1;
        $slug = self::normalizeSlug($slug);
        $baseSlug = $slug;
        while (in_array($slug, $slugCollections)) {
            $slug = $baseSlug . $separator . $inc++;
        }
        return $slug;
    }

    /**
     * @param string $slug
     * @param callable $callable must be returning true for valid
     * @return string
     */
    public static function uniqueSlugCallback(string $slug, callable $callable) : string
    {
        $separator = '-';
        $inc = 1;
        $slug = self::normalizeSlug($slug);
        $baseSlug = $slug;
        while (!$callable($slug)) {
            $slug = $baseSlug . $separator . $inc++;
        }

        return $slug;
    }
}
