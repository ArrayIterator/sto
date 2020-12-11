<?php
if (!defined('ADMIN_LOGIN_PAGE')) {
    return;
}
get_admin_header_template();
?>
    <div class="row login-wrap-row">
        <div class="col-lg-8 col-md-6 login-form-wrap-col-left">
            <?php
            hook_run('admin_login_form_left');
            ?>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 login-form-wrap-col-right">
            <div class="container">
                <div class="login-form-wrap">
                    <?php hook_run('admin_login_before_login_text'); ?>
                    <div class="form-group">
                        <h2 class="admin-login-text">
                            <?= hook_apply('admin_login_text', trans('LOGIN')); ?>
                        </h2>
                    </div>
                    <?php hook_run('admin_login_after_login_text'); ?>
                    <?php admin_login_form(); ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        ;(function () {
            try {
                var $switch = document.querySelector('[data-switch]');
                var $target = document.querySelector('input' + $switch.getAttribute('data-target')),
                    sq = JSON.parse($switch.getAttribute('data-switch')),
                    $current = 0;
                if (!$target) {
                    return;
                }
                $switch.parentElement.addEventListener('click', function (e) {
                    e.preventDefault();
                    $target.setAttribute('type', $current === 0 ? 'text' : 'password');
                    $switch.classList.replace(
                        sq[$current],
                        sq[$current ? 0 : 1]
                    );
                    $current = $current ? 0 : 1;
                });
            } catch (e) {
                // pass
            }
        })(window);
    </script>
<?php
get_admin_footer_template();
