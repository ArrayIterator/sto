<?php
// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}
return [
    'admin' => [
         css_a('admin'),
        ['bootstrap', 'icofont'],
        VERSION
    ],
    'bootstrap' => [
        get_assets_vendor_url('css/bootstrap.min.css', 'bootstrap'),
        [],
        VERSION_BOOTSTRAP
    ],
    'quill' => [
        get_assets_vendor_url('quill.snow.css', 'quill'),
        [],
        VERSION_QUILL
    ],
    'select2' => [
        get_assets_vendor_url('select2.css', 'select2'),
        [],
        VERSION_SELECT2
    ],
    'icofont' => [
        get_assets_vendor_url('icofont.min.css', 'icofont'),
        [],
        VERSION_ICOFONT_CSS
    ],
];
