<?php
namespace ArrayIterator\Controller\Common;

/**
 * Class Common
 * @package ArrayIterator\Controller\Common
 */
class Common
{
    public static function favicon()
    {
        $data = base64_decode(
            // blank icon deflated
            'Y2BgBEIBAQYwyGBlYBADsjSAWACIFYCYkYEDSIIZEBqJ/f//f4TYKGs0BEZDYDQERkOA6BAAAA=='
        );
        //get the HTTP_IF_MODIFIED_SINCE header if set
        $ifModifiedSince = get_server_environment('HTTP_IF_MODIFIED_SINCE')??false;
        //get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
        $etagHeader = get_server_environment('HTTP_IF_NONE_MATCH')??false;
        $etagFile   = md5( $data . date('Y') );
        if ($ifModifiedSince && $etagHeader === $etagFile) {
            clean_buffer(false);
            set_status_header(304);
            do_exit();
        }

        $lastMod    = gmdate('D, d M Y H:i:s \G\M\T', filemtime(__FILE__));
        set_header('Accept-Ranges', 'bytes');
        set_header('Content-Type', 'image/x-icon');
        set_header('Content-Encoding', 'deflate');
        // remove_header('Content-Length');
        set_header('Content-Length', strlen($data));
        set_header('Cache-Control', 'max-age=315360000, public');
        set_header('Last-Modified', $lastMod);
        set_header('Etag', $etagFile);
        do_exit($data);
    }
}