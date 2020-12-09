<?php
$error = $error ?? error_get_last();
?>
<!DOCTYPE html>
<html<?=get_html_attributes();?>>
<head>
    <title><?= htmlentities(trans('500 Internal Server Error'));?></title>
<?php hook_run('html_head'); ?>
</head>
<body<?=get_body_attributes();?>>
<?php hook_run('html_body_open'); ?>
<?php print_r($error); ?>
<?php hook_run('html_footer'); ?>
</body>
</html>