<?php
// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

return [
    'jquery' => [null, ['jquery-core']],
    'jquery-core' => [js_a('jquery'), [], VERSION_JQUERY],

    // chart js
    'chart' => [js_a('Chart'), ['moment'], VERSION_CHART_JS],

    // moment js
    'moment' => [js_a('moment-locales-timezone.js'), [], VERSION_MOMENT_JS],

    // underscore js
    'underscore' => [js_a('underscore'), [], VERSION_MOMENT_JS],

    // crypto js
    'crypto' => [js_a('crypto'), [], VERSION_MOMENT_JS],
    // popper
    'popper' => [js_a('popper'), [], VERSION_POPPER_JS],

    // quill
    'quill' => [
        get_assets_vendor_url('quill.min.js', 'quill'),
        [],
        VERSION_QUILL
    ],

    'bootstrap' => [
        get_assets_vendor_url('js/bootstrap.min.js', 'bootstrap'),
        ['jquery', 'popper'],
        VERSION_BOOTSTRAP
    ],

    'select2' => [
        get_assets_vendor_url('select2.js', 'select2'),
        ['jquery'],
        VERSION_SELECT2
    ],

    'core' => [
        js_a('core.min'),
        ['crypto'],
        VERSION
    ],

    'admin' => [
        js_a('admin'),
        ['core', 'jquery', 'bootstrap', 'underscore', 'select2'],
        VERSION
    ],

    'admin-login' => [
        js_a('login.js'),
        ['core', 'jquery', 'bootstrap'],
        VERSION
    ]
];
