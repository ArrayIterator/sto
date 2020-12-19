<?php
if (!defined('ROOT_DIR')) {
    return;
}
$is_interim = isset($_REQUEST['interim']);
?>
<?php get_header_template(); ?>
    <div class="container">
        <div class="row login-wrap-row<?= $is_interim ? ' interim' : ''; ?>">
            <div class="offset-lg-4 col-lg-4 col-md-6 offset-md-3 col-sm-8 offset-sm-2 col-xs-12">
                <div class="login-form-wrap">
                    <div class="login-form-wrapper">
                        <?php hook_run('login_before_login_text'); ?>
                        <div class="form-group">
                            <h2 class="login-text">
                                <?= hook_apply('login_text', trans('LOGIN')); ?>
                            </h2>
                        </div>
                        <?php hook_run('admin_login_after_login_text'); ?>
                        <?php login_form(); ?>
                        <?php if (allow_student_reset_password()) { ?>
                            <div class="forgot-password">
                                <a href="<?= get_reset_password_url();?>" class="forgot-password-link"><?php esc_attr_trans_e('Reset Your Password');?></a>
                            </div>
                        <?php  } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer_template();
