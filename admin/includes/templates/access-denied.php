<?php
if (!defined('ADMIN_AREA')) {
    return;
}
set_admin_title(trans('Access Denied'));
get_admin_header_template();
?>
    <div class="mt-5"></div>
    <div class="alert alert-danger">
        <?=
        // @todo completion templates
        trans('ACCESS DENIED');
        ?>
    </div>
<?php
get_admin_footer_template();
