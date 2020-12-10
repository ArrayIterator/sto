<?php
if (!defined('ADMIN_AREA')) {
    return;
}
?><!DOCTYPE html>
<html<?=get_admin_html_attributes();?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=get_admin_title();?></title>
    <link rel="stylesheet" href="<?= get_assets_vendor_url('/bootstrap/css/bootstrap.min.css');?>">
    <link rel="stylesheet" href="<?= get_assets_vendor_url('/icofont/icofont.min.css');?>">
    <link rel="stylesheet" href="<?= get_assets_url('/css/admin.css');?>">
    <script type="application/javascript" src="<?= get_assets_url('/js/jquery.js');?>"></script>
<?php hook_run('html_admin_head');?>
</head>
<body<?= get_admin_body_attributes();?>>
<div id="page">
<?php hook_run('admin_body_open'); ?>
