<?php
if (!defined('ADMIN_LOGIN_PAGE')) {
    return;
}
$is_interim = isset($_REQUEST['interim']);
get_admin_header_template();
?>
    <div class="row login-wrap-row<?= $is_interim ? ' interim' : '';?>">
        <?php if (!$is_interim) : ?>
        <div class="col-lg-8 col-md-6 login-form-wrap-col-left">
            <?php
            hook_run('admin_login_form_left');
            ?>
        </div>
        <?php endif;?>
        <div class="<?= $is_interim ? 'offset-lg-4 offset-md-4' : '';?> col-lg-4 col-md-6 col-sm-12 login-form-wrap-col-right">
            <div class="container">
                <div class="login-form-wrap">
                    <?php hook_run('admin_login_before_login_text'); ?>
                    <div class="form-group">
                        <h2 class="admin-login-text">
                            <?= hook_apply('admin_login_text', trans('LOGIN')); ?>
                        </h2>
                    </div>
                    <?php hook_run('admin_login_after_login_text'); ?>
                    <?php
                        $message_error = null;
                        switch (query_param('error')) {
                            case 'empty_username':
                                $message_error = trans('Username Could Not Be Empty');
                                break;
                            case 'empty_password':
                                $message_error = trans('Password Could Not Be Empty');
                                break;
                            case 'invalid_token':
                                $message_error = trans('Token Is Invalid');
                                break;
                            case 'invalid_user':
                                $message_error = trans('User Does Not Exists');
                                break;
                            case 'invalid_password':
                                $message_error = trans('Invalid Password');
                                break;
                        }
                        if ($message_error !== null) {
                            $message_err = hook_apply('login_message_error', $message_error, query_param('error'));
                            $message_err = !is_string($message_err) ? $message_error : $message_err;
                            printf(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">%s<button class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>',
                                $message_err
                            );
                        }
                    ?>
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
