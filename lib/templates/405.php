<?php
// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

?><!DOCTYPE html>
<html<?= get_html_attributes(); ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php esc_attr_trans_e('405 Method Not Allowed'); ?></title>
</head>
<body<?= get_body_attributes(); ?>>
<div class="wrap" id="page">
    <h1>405</h1>
    <h3><?php esc_html_trans_e('Method Not Allowed'); ?></h3>
</div>
</body>
</html>