<?php
if (!defined('ADMIN_AREA')) {
    return;
}
?><!DOCTYPE html>
<html<?=get_admin_html_attributes();?>>
<head>
    <meta charset="utf-8">
    <title><?=get_admin_title();?></title>
<?php hook_run('html_admin_head');?>
</head>
<body<?= get_admin_body_attributes();?>>
<?php hook_run('html_admin_body_open'); ?>
