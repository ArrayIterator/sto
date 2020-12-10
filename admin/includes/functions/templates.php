<?php

use ArrayIterator\Helper\Path;

/**
 * @param string $file
 * @return bool
 */
function load_admin_template(string $file) : bool
{
    if (substr($file, -4) !== '.php') {
        $file .= '.php';
    }
    $path = normalize_path(dirname(__DIR__) . '/templates/');
    $file = normalize_path($file);
    if (strpos($file, $path) !== 0) {
        $file = $path . $file;
    }

    if (file_exists($file)) {
        /** @noinspection PhpIncludeInspection */
        require $file;
        return true;
    }

    return false;
}

/**
 * @param bool $reLoad
 */
function get_admin_header_template(bool $reLoad = false)
{
    static $loaded;
    if (!$reLoad && $loaded) {
        return;
    }

    $loaded = true;
    load_admin_template('header');
}

/**
 * @param bool $reLoad
 */
function get_admin_footer_template(bool $reLoad = false)
{
    static $loaded;
    if (!$reLoad && $loaded) {
        return;
    }
    $loaded = true;
    load_admin_template('footer');
}

/**
 * @return string
 */
function get_admin_title() : string
{
    $title = is_admin_login() ? 'Dashboard' : get_admin_login_title();
    $title = (string) hook_apply(
        'admin_title',
        $title
    );
    return htmlentities(trans($title));
}

/**
 * @return string
 */
function get_admin_login_title() : string
{
    return (string) hook_apply('admin_login_title', 'Login To Admin Area');
}

/**
 * @return string
 */
function get_admin_button_submit() : string
{
    return hook_apply(
        'admin_button_submit',
        sprintf(
            '<button type="submit" class="btn-primary btn btn-block admin-submit-button">%s</button>',
            trans('Sign in')
        )
    );
}

/**
 *
 */
function admin_login_form()
{
?>
    <form<?= get_admin_login_form_attributes();?>>
        <?php hook_run('admin_login_form_before');?>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label for="username" class="input-group-text col-form-label" title="<?php esc_attr_trans_e('Username');?>">
                        <i class="icofont icofont-user-alt-3"></i>
                    </label>
                </div>
                <input class="form-control" name="username" placeholder="<?php esc_attr_trans_e('Username');?>" type="text" id="username" value="<?=
                    htmlspecialchars(
                        (string) hook_apply('admin_input_username', (string) post('username')),
                        ENT_QUOTES|ENT_COMPAT
                    );
                ?>" required>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label for="password" class="input-group-text col-form-label" title="<?php esc_attr_trans_e('Password');?>">
                        <i class="icofont icofont-lock"></i>
                    </label>
                </div>
                <input class="form-control" name="password" placeholder="<?php esc_attr_trans_e('Password');?>" type="password" id="password" value="" required>
                <div class="input-group-append">
                    <button type="button" class="input-group-text no-outline btn no-shadow">
                        <i class="icofont icofont-eye" data-target="#password" data-switch='["icofont-eye", "icofont-eye-blocked"]'></i>
                    </button>
                </div>
            </div>
            <div class="form-check admin-checkbox-input">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" value="yes"<?= hook_apply('remember_me', post('remember') === 'yes') === true ? ' checked' : '';?>>
                <label class="form-check-label" for="remember"><?php trans_e('Remember Me');?></label>
            </div>
        </div>
        <?php hook_run('admin_login_form_after');?>

        <div class="form-group">
            <?= get_admin_button_submit(); ?>
        </div>
    </form>
<?php
}
