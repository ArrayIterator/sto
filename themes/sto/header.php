<?php
// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

?><!DOCTYPE html>
<html<?= get_html_attributes();?>>
<head>
<?php html_head();?>

</head>
<body<?=get_body_attributes();?>>
    <?php body_open();?>
    <div id="page" class="flex-wrap">

