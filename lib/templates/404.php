<!DOCTYPE html>
<html<?=get_html_attributes();?>>
<head>
    <title><?= htmlentities(trans('404 Page Not Found'));?></title>
<?php hook_run('html_head'); ?>
</head>
<body<?=get_body_attributes();?>>
<?php hook_run('html_body_open'); ?>
<h1>404</h1>
<h3><?= trans('Page Not Found');?></h3>
<?php hook_run('html_footer'); ?>
</body>
</html>