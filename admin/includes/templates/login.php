<?php
if (!defined('ADMIN_LOGIN_PAGE')) {
    return;
}
$is_interim = isset($_REQUEST['interim']);
get_admin_header_template();
?>
    <div class="row login-wrap-row<?= $is_interim ? ' interim' : ''; ?>">
        <?php if (!$is_interim) : ?>
            <div class="col-lg-8 col-md-6 login-form-wrap-col-left">
                <?php
                    if (hook_exist('admin_login_form_left')) {
                    hook_run('admin_login_form_left');
                    } else {
                ?>
                    <style>
                        .clock-indicator,
                        .clock-indicator-indicator,
                        .clock-indicator-hour,
                        .clock-indicator-minute {
                            border-color: #0b2e13;
                        }
                        .clock-indicator::after,
                        .clock-indicator-second {
                            border-color: #d42626;
                        }
                        .clock-section {
                            margin: 25% auto;
                            text-align: center;
                        }
                        .clock-section .position-relative {
                            margin: auto;
                        }
                        .text-quote {
                            font-weight: lighter;
                            margin: auto auto 0;
                            display: block;
                            text-align: center;
                            vertical-align: middle;
                            color: #fff;
                            letter-spacing: 2px;
                        }
                        .text-quote> div {
                            display: inline-block;
                            margin: auto;
                            background: #d42626;
                            padding: 10px;
                        }
                        .clock-digit {
                            position: absolute;
                            text-align: center;
                            margin: 0 auto;
                            z-index: 1;
                            display: block;
                            left: calc(50% - 50px);
                            top: 25%;
                            user-select: none;
                            appearance: none;
                        }
                        .clock-digit .digit-hour,
                        .clock-digit .digit-minute,
                        .clock-digit .digit-second {
                            position: relative;
                            display: inline-block;
                            padding: 4px 0;
                            background: rgba(255, 255,255,.2);
                            color: #666;
                            margin-right: 3px;
                            width: 30px;
                            left: 2px;
                            border: 0;
                            border-radius: 50%;
                            box-shadow: 0 0px 1px rgba(0,0,0, 0.1), inset -1px 3px 3px rgba(0,0,0,0.3);
                            font-family: monospace;
                        }
                        .clock-digit .digit-second {
                            margin-right: 0;
                        }
                    </style>
                    <div class="clock-section hidden d-md-block clock-fast">
                        <div class="position-relative d-inline-block">
                            <?= create_indicator_clock();?>
                            <div class="clock-digit"></div>
                        </div>
                        <div class="text-quote text-center text-uppercase mt-3 mb-3">
                            <div><?php esc_html_trans_e('Time Is Money');?></div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        (function (s) {
                            if (!s || !s.clock) {
                                return;
                            }
                            var hour = document.querySelector('.clock-section .clock-indicator-hour'),
                                minute = document.querySelector('.clock-section .clock-indicator-minute'),
                                second = document.querySelector('.clock-section .clock-indicator-second'),
                                digit = document.querySelector('.clock-section .clock-digit'),
                                date = new Date();
                            if (hour && minute && second) {
                                var calc = s.clock.calculate_delay();
                                date = calc.date;
                                function update_clock()
                                {
                                    date.setSeconds(date.getSeconds() + 1);
                                    var sec = date.getSeconds(),
                                        min = date.getMinutes();
                                    sec = sec < 10 ? '0' + sec : sec;
                                    min = min < 10 ? '0' + min : min;
                                    digit.innerHTML = '<span class="digit-hour">'+ date.getHours() + '</span>'
                                        + '<span class="digit-minute">'+ min + '</span>'
                                        + '<span class="digit-second">'+ sec + '</span>';
                                }
                                date.setSeconds(date.getSeconds() - 1);
                                update_clock();
                                hour.setAttribute('style', Object.join(calc.hours, ';', ':'));
                                minute.setAttribute('style', Object.join(calc.minutes, ';', ':'));
                                second.setAttribute('style', Object.join(calc.seconds, ';', ':'));
                                if (digit) {
                                    setInterval(update_clock, 1000);
                                }
                            }
                        })(window.Sto);
                    </script>
                <?php
                    }
                ?>
            </div>
        <?php endif; ?>
        <div class="<?= $is_interim ? 'offset-lg-4 offset-md-4' : ''; ?> col-lg-4 col-md-6 col-sm-12 login-form-wrap-col-right">
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
                        case 'fail_login':
                            $message_error = trans('Unknown Error');
                            break;
                        case 'cookie_disabled':
                            $message_error = count(cookies()) === 0 ? trans('Please Enable Cookie') : trans('Could Not Authenticated Cookie');
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
<?php
get_admin_footer_template();
