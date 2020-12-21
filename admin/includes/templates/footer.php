<?php
if (!defined('ADMIN_AREA')) {
    return;
}
?>
    </div>
    <!-- #content -->
<?php if (!is_admin_login_page()) : ?>
    <footer id="footer-bottom">
        <div class="copy">
            <p class="text-muted">
                <small><strong><?= APP_NAME;?></strong> - <?= trans('Version');?> : <?= VERSION;?></small>
            </p>
        </div>
    </footer>
    </div>
    <!-- #right-area -->
<?php endif; ?>

</div>
<!-- #page -->
<?php admin_html_footer() ?>
</body>
</html>
