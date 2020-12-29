<?php
// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

return [
    'jquery' => [null, ['jquery-core']],
    'jquery-core' => [js_a('jquery'), [], VERSION_JQUERY],

    // chart js
    'chart' => [null, ['chart-js']],
    'chart-js' => [js_a('Chart'), ['moment'], VERSION_CHART_JS],

    // moment js
    'moment' => [null, ['moment-js']],
    'moment-js' => [js_a('moment-locales-timezone.js'), [], VERSION_MOMENT_JS],

    // underscore js
    'underscore' => [null, ['underscore-js']],
    'underscore-js' => [js_a('underscore'), [], VERSION_MOMENT_JS],

    // crypto js
    'crypto' => [null, ['crypto-js']],
    'crypto-js' => [js_a('crypto'), [], VERSION_MOMENT_JS],

    'quill' => [null, ['quill-js']],
    'quill-js' => [
        get_assets_vendor_url('quill.min.js', 'quill'),
        [],
        VERSION_QUILL_JS
    ],

    'bootstrap' => [
        get_assets_vendor_url('/js/bootstrap.min.js', 'bootstrap'),
        ['jquery'],
        VERSION_BOOTSTRAP
    ],

    'select2' => [
        get_assets_vendor_url('select2.js', 'select2'),
        ['jquery'],
        VERSION_SELECT2
    ],

    'core' => [
        js_a('core'),
        ['crypto'],
        VERSION
    ],
    'admin' => [
        js_a('admin'),
        ['jquery', 'crypto', 'core', 'bootstrap', 'underscore'],
        VERSION
    ],
    'admin-login' => [
        js_a('login.js'),
        ['jquery', 'core', 'bootstrap'],
        VERSION
    ]
];
