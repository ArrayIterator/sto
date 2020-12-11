<?php
if (!defined('ADMIN_AREA')) {
    return;
}
?><!DOCTYPE html>
<html<?= get_admin_html_attributes(); ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript">
        document.documentElement.className = document.documentElement.className.replace('no-js', 'js');
    </script>
    <?php admin_html_head(); ?>
</head>
<body<?= get_admin_body_attributes(); ?>>
<div id="page">
    <?php hook_run('admin_body_open'); ?>
