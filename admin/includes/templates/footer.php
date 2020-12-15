<?php
if (!defined('ADMIN_AREA')) {
    return;
}
?>
<?php if (!is_admin_login_page()) : ?>
    </div>
    <!-- #right-area -->
<?php endif; ?>

</div>
<!-- #page -->
<?php hook_run('html_admin_footer'); ?>
</body>
</html>
