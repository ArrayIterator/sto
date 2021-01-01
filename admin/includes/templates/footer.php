<?php
if (!defined('ADMIN_AREA')) {
    return;
}
?>
        </div>
        <!-- #content -->
<?php if (!is_admin_login_page()) : ?>
        <footer id="footer-bottom">
            <?php hook_run('admin_footer_bottom_html');?>
        </footer>
    </div>
    <!-- #right-area -->
<?php endif; ?>

</div>
<!-- #page -->
<?php admin_html_footer() ?>
</body>
</html><?php
// always call do exit
do_exit(0);
