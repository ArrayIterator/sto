<!DOCTYPE html>
<html<?=get_html_attributes();?>>
<head>
    <title><?= htmlentities(trans('405 Method Not Allowed'));?></title>
<?php hook_run('html_head'); ?>
</head>
<body<?=get_body_attributes();?>>
<?php hook_run('html_body_open'); ?>
<h1>405</h1>
<h3><?= trans('Method Not Allowed');?></h3>
<?php hook_run('html_footer'); ?>
</body>
</html>