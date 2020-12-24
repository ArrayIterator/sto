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
    <script type="text/template" id="underscore_template_modal">
        <div class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <% if (typeof title !== "undefined" ) { %>
                        <h5 class="modal-title"><%= title %></h5>
                        <% %>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <% if (typeof content !== "undefined" ) { %>
                            <%= content %>
                        <% %>
                    </div>
                    <div class="modal-footer">
                        <% if (typeof button !== "undefined" ) { %>
                            <%= button %>
                        <% %>
                    </div>
                </div>
            </div>
        </div>
    </script>
<?php endif; ?>

</div>
<!-- #page -->
<?php admin_html_footer() ?>
</body>
</html><?php
// always call do exit
do_exit(0);
