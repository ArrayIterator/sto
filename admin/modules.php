<?php
require __DIR__ . '/init.php';

if (!admin_is_allowed(__FILE__) || !is_admin()) {
    return load_admin_denied();
}

$canActivatedModule = current_supervisor_can('activate_module');
$canDeActivatedModule = current_supervisor_can('deactivate_module');
/*!
 * MODULE ACTIVATION
 */
if (is_method_post()) {
    $current_url = get_current_url();
    $action = post_param_string(PARAM_ACTION_QUERY, '', true);
    $module = post_param_string('module', '', true);
    $hasAction = (has_post_param(PARAM_ACTION_QUERY)
        && in_array(
            post_param_string(PARAM_ACTION_QUERY, '', true),
            [PARAM_ACTION_ACTIVATE, PARAM_ACTION_DEACTIVATE]
        )
    );

    $current_url = remove_query_args(PARAM_RESPONSE_QUERY, $current_url);
    if (!$hasAction || $module === '') {
        redirect($current_url);
        return;
    }

    if (!$canDeActivatedModule && !$canActivatedModule) {
        flash_set(
            'module_message',
            [
                PARAM_STATUS_QUERY => PARAM_ERROR_QUERY,
                PARAM_MESSAGE_QUERY => trans('Permission denied to change module status')
            ],
            FLASH_PREFIX
        );
        redirect(add_query_args([
            PARAM_RESPONSE_QUERY => PARAM_RESPONSE_DENIED
        ], $current_url));
        return;
    }
    $mod = module_get($module);
    if (!$mod) {
        flash_set(
            'module_message',
            [
                PARAM_STATUS_QUERY => PARAM_ERROR_QUERY,
                PARAM_MESSAGE_QUERY => trans_sprintf('Module %s has not exists', $module)
            ],
            FLASH_PREFIX
        );
        redirect(
            add_query_args([
                PARAM_RESPONSE_QUERY => PARAM_RESPONSE_EMPTY
            ], $current_url)
        );
        return;
    }
    $id_tag = post_param_string(PARAM_ID_QUERY, '');
    if ($id_tag !== '') {
        $current_url .= "#{$id_tag}";
    }

    switch ($action) {
        case PARAM_ACTION_ACTIVATE:
            if (!$canActivatedModule) {
                flash_set(
                    'module_message',
                    [
                        PARAM_STATUS_QUERY => PARAM_ERROR_QUERY,
                        PARAM_MESSAGE_QUERY => trans('Permission denied to change module status')
                    ],
                    FLASH_PREFIX
                );
                redirect(add_query_args([
                    PARAM_RESPONSE_QUERY => PARAM_RESPONSE_DENIED
                ], $current_url));
                return;
            }
            $isGlobal = is_super_admin()
                && is_true_value(post_param_string('global'))
                && $mod->isSiteWide();
            $err = null;
            $processed = false;
            set_error_handler(function ($errNo, $errStr, $errFile, $errLine) use (&$err) {
                $err = new ErrorException(
                    $errStr,
                    $errNo,
                    1,
                    $errFile,
                    $errLine
                );
            }, E_PARSE | E_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
            try {
                register_shutdown_function(function () use ($current_url, $mod, &$err, &$processed) {
                    if ($err || $processed) {
                        return;
                    }
                    $error = error_get_last();
                    if (!$error || !in_array($error['type'], [E_ERROR, E_PARSE])) {
                        return;
                    }
                    $path = realpath($mod->getPath()) ?: normalize_directory($mod->getPath());
                    if ($path !== $error['file']) {
                        return;
                    }
                    $err = new ErrorException(
                        $error['message'],
                        $error['type'],
                        1,
                        $error['file'],
                        $error['line']
                    );
                    flash_set(
                        'module_message',
                        [
                            PARAM_STATUS_QUERY => PARAM_ERROR_QUERY,
                            PARAM_MESSAGE_QUERY => $err
                        ]
                    );
                    redirect(
                        add_query_args([
                            PARAM_RESPONSE_QUERY => PARAM_ERROR_QUERY
                        ], $current_url)
                    );
                    exit;
                });
                $mod->load();
            } catch (Throwable $e) {
                $err = $e;
            }

            if (ob_get_length()) {
                clean_buffer();
            }

            restore_error_handler();
            if ($err) {
                flash_set(
                    'module_message',
                    [
                        PARAM_STATUS_QUERY => PARAM_ERROR_QUERY,
                        PARAM_MESSAGE_QUERY => $err
                    ]
                );
                redirect(
                    add_query_args([
                        PARAM_ACTION_QUERY => PARAM_FAILED_QUERY,
                        PARAM_RESPONSE_QUERY => PARAM_ERROR_QUERY
                    ], $current_url)
                );
                return;
            }

            hook_run(
                'before_module_activated',
                $mod,
                $mod->getBaseModuleName(),
                $isGlobal
            );
            $status = $isGlobal
                ? set_globals_active_module($mod)
                : set_site_active_module($mod);

            flash_set(
                'module_message',
                [
                    PARAM_STATUS_QUERY => PARAM_SUCCESS_QUERY,
                    PARAM_MESSAGE_QUERY => $isGlobal ? trans_sprintf(
                            'Module %s successfully activated as global',
                            $mod->getName()
                        ) : trans_sprintf(
                        'Module %s successfully activated',
                        $mod->getName()
                    )
                ]
            );

            redirect(
                add_query_args(
                    [
                        PARAM_RESPONSE_QUERY => PARAM_SUCCESS_QUERY,
                        PARAM_ACTION_QUERY => PARAM_RESPONSE_ACTIVATED,
                        PARAM_STATUS_QUERY => PARAM_ALL_QUERY,
                    ],
                    $current_url
                )
            );
            return;
        case PARAM_ACTION_DEACTIVATE:
            if (!$canDeActivatedModule) {
                flash_set(
                    'module_message',
                    [
                        PARAM_STATUS_QUERY => PARAM_ERROR_QUERY,
                        PARAM_MESSAGE_QUERY => trans('Permission denied to change module status')
                    ],
                    FLASH_PREFIX
                );
                redirect(add_query_args([
                    PARAM_RESPONSE_QUERY => PARAM_RESPONSE_DENIED,
                ], $current_url));
            }

            if (!$mod->isLoaded()) {
                redirect(
                    add_query_args(
                        [
                            PARAM_RESPONSE_QUERY => PARAM_SUCCESS_QUERY,
                            PARAM_ACTION_QUERY => PARAM_RESPONSE_DEACTIVATED,
                            PARAM_STATUS_QUERY => PARAM_ALL_QUERY
                        ],
                        $current_url
                    )
                );

                return;
            }

            $isGlobal = is_super_admin()
                && is_true_value(post_param_string('global'))
                && module_is_global($mod)
                && $mod->isSiteWide();
            hook_run(
                'before_module_deactivated',
                $mod,
                $mod->getBaseModuleName(),
                $isGlobal
            );
            if ($isGlobal) {
                remove_global_active_module($mod);
                set_site_active_module($mod);
            } else {
                if ($mod->isSiteWide() && is_super_admin()) {
                    remove_global_active_module($mod);
                }
                remove_site_active_module($mod);
            }
            flash_set(
                'module_message',
                [
                    PARAM_STATUS_QUERY => PARAM_SUCCESS_QUERY,
                    PARAM_MESSAGE_QUERY => $isGlobal ? trans_sprintf(
                        'Module %s successfully deactivated from global',
                        $mod->getName()
                    ) : trans_sprintf(
                        'Module %s successfully deactivated',
                        $mod->getName()
                    )
                ]
            );

            redirect(
                add_query_args(
                    [
                        PARAM_RESPONSE_QUERY => PARAM_SUCCESS_QUERY,
                        PARAM_ACTION_QUERY => PARAM_RESPONSE_DEACTIVATED,
                        PARAM_STATUS_QUERY => PARAM_ALL_QUERY
                    ],
                    $current_url
                )
            );
            break;
    }
    redirect($current_url);
    return;
}

$messages = flash_get('module_message', FLASH_PREFIX);
if (is_array($messages) && isset($messages[PARAM_STATUS_QUERY], $messages[PARAM_MESSAGE_QUERY])) {
    if ($messages[PARAM_STATUS_QUERY] === PARAM_SUCCESS_QUERY) {
        add_admin_success_message(
            'module_status',
            $messages[PARAM_MESSAGE_QUERY]
        );
    } elseif ($messages[PARAM_STATUS_QUERY] === PARAM_ERROR_QUERY) {
        $message = $messages[PARAM_MESSAGE_QUERY];
        if ($message instanceof Throwable) {
            $message_ = sprintf('<p>%s</p>', trans('There was an error'));
            if (DEBUG && is_super_admin()) {
                $message_ .= '<pre class="pre-code-notice">' . replace_root_dir_string((string) $message) . '</pre>';
            } else {
                $message_ .= sprintf('<p>%s</p>', replace_root_dir_string((string) $message));
            }

            $message = $message_;
            unset($message_);
        }
        if (is_string($message)) {
            add_admin_error_message('module_status', $message);
        }
    }
}

switch (query_param(PARAM_STATUS_QUERY)) {
    case STATUS_ACTIVE:
        set_admin_title('Active Modules');
        break;
    case 'inactive':
        set_admin_title('Inactive Modules');
        break;
    default:
        set_admin_title('All Modules');
}

get_admin_header_template();

$moduleInputStatusName = 'module_status';
$moduleStatusName = PARAM_STATUS_QUERY;

$moduleStatus = query_param($moduleStatusName);
$moduleSearchStatus = query_param($moduleInputStatusName);
$moduleSearch = query_param(PARAM_SEARCH_QUERY);

$moduleStatus = !is_string($moduleStatus) ? '' : $moduleStatus;
$moduleSearchStatus = !is_string($moduleSearchStatus) ? '' : $moduleSearchStatus;
$moduleSearch = !is_string($moduleSearch) ? '' : trim($moduleSearch);

$moduleStatusesLists = (array)hook_apply('module_status_list', ['active', 'inactive']);
$url = get_current_url();
if ($moduleStatus) {
    $url = add_query_args(
        $url,
        [$moduleInputStatusName => $moduleStatus]
    );
}
array_unshift($moduleStatusesLists, 'all');

$chosenStatus = in_array($moduleStatus, $moduleStatusesLists) ? $moduleStatus : 'all';
$chosenSearchStatus = in_array($moduleSearchStatus, $moduleStatusesLists) ? $moduleSearchStatus : 'all';
if ($chosenStatus !== 'all') {
    $chosenSearchStatus = $moduleStatus;
    $moduleInputStatusName = $moduleStatusName;
    $moduleStatusesLists = [$moduleStatus];
}

// escape
$moduleInputStatusName = esc_attr($moduleInputStatusName);
$modules = [];
$ids = [];
foreach (modules()->getModules() as $name => $module) {
    if (!$module->isValid() || !is_super_admin() && !$module->isSiteWide()) {
        continue;
    }

    $logo = $module->getLogo() ?: null;
    $logo_width = 0;
    $logo_height = 0;
    if (is_array($logo)) {
        $logo_width = $logo['width'];
        $logo_height = $logo['height'];
        $logo = $logo['url'];
    }
    $originalId = sprintf(
        'module-mod-%s',
        normalize_html_class($name)
    );
    $id = $originalId;
    $c = 0;
    while (isset($ids[$id])) {
        $c++;
        $id = sprintf('%s-%d', $originalId, $c);
    }
    $ids[$id] = true;
    // ref for js
    $modules[$name] = [
        'name' => $module->getName(),
        'id' => $id,
        'site_wide' => $module->isSiteWide(),
        'description' => $module->getDescription(),
        'author' => $module->getAuthor(),
        'author_uri' => $module->getAuthorUri(),
        'uri' => $module->getUri(),
        'license' => $module->getLicense(),
        'logo' => $logo,
        'logo_width' => $logo_width,
        'logo_height' => $logo_height,
        'version' => $module->getVersion(),
        'active' => $module->isLoaded(),
        'loaded_time' => $module->getLoadedTime(),
        'active_time' => $module->getActiveTime(),
    ];
}

/*
    <script type="text/javascript">
        <?php

        ?>
        var modules = <?= json_ns($modules, true);?>;

    </script>
*/
?>
    <div class="filter-area">
        <form method="get" class="module-form mt-2" action="<?= esc_attr($url); ?>">
            <div class="row">
                <div class="col-md-8">
                    <?php foreach ($moduleStatusesLists as $item) : ?>
                        <label class="custom-label-radio" data-filter="<?php esc_attr_e($item); ?>" data-wrap="modules" data-class="hidden hide">
                            <input name="<?= $moduleInputStatusName; ?>" type="radio" class="module-status-filter"
                                   value="<?php esc_attr_e($item); ?>"<?= $chosenSearchStatus === $item ? ' checked' : '' ?>>
                            <span><?php esc_attr_trans_e(sprintf('%s Modules', ucwords($item))); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="module-form-search-input" class="sr-only">Search Module</label>
                        <input class="form-control" id="module-form-search-input" maxlength="30" type="search"
                               name="<?= esc_attr(PARAM_SEARCH_QUERY); ?>" value="<?= esc_attr($moduleSearch); ?>"
                               placeholder="<?php esc_attr_trans_e('Type To Search ...'); ?>">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-area standard-card">
        <div data-wrap-target="modules" class="card-columns">
            <?php
            $counted = 0;
            $regex_1 = '#' . preg_quote($moduleSearch, '#') . '#i';
            $regex_2 = '#' . preg_quote(preg_replace('#\s+#', '', $moduleSearch), '#') . '#i';
            $count = 0;
            foreach ($modules as $k => $item) :
                unset($modules[$k]); // freed
                $bg = '';
                if ($item['logo']) {
                    $bg = sprintf(' style="background-color:url(\'%s\')"', esc_attr($item['logo']));
                }
                $class = 'card';
                if ($chosenStatus === 'active' && !$item['active']) {
                    continue;
                }
                if ($chosenStatus === 'inactive' && $item['active']) {
                    continue;
                }
                $counted++;
                $identifier = $k;
                $class = 'card';
                $isHide = false;
                if ($chosenSearchStatus !== 'all') {
                    if ($chosenSearchStatus == 'active' && !$item['active']
                        || $chosenSearchStatus == 'inactive' && $item['active']
                    ) {
                        $class .= ' hide';
                        $isHide = true;
                    }
                }

                if (!$isHide && $moduleSearch) {
                    // doing simple
                    $name = preg_replace('#\s+#', ' ', $item['name']);
                    $name2 = preg_replace('#\s+#', '', $name);
                    if (preg_match($regex_1, $name)
                        || preg_match($regex_2, $name)
                        || preg_match($regex_1, $name2)
                        || preg_match($regex_2, $name2)
                    ) {
                        $class = 'card';
                    } else {
                        $class .= ' hide';
                    }
                }

                $item['active'] && $class .= ' active';

                $author = esc_html($item['author']);
                if ($item['author_uri'] && filter_var($item['author_uri'])) {
                    $author = sprintf(
                        '<a href="%s" target="_blank" class="module-author-uri-link">%s</a>',
                        esc_attr($item['author_uri']),
                        $author
                    );
                }
                $startDiv = $count === 0 || $count % 3 === 0;
                $count++;
                ?>

                <div class="<?= $class; ?>" id="<?= $item['id']; ?>" data-filter-source="<?= $item['active'] ? 'active' : 'inactive'; ?>" data-status="<?= $item['active'] ? 'active' : 'inactive'; ?>">
                    <div class="card-image"<?= $bg; ?>>
                        <?= !empty($item['description']) ? sprintf('<div class="card-description">%s</div>',
                            esc_html($item['description'])) : ''; ?>
                    </div>
                    <div class="card-body">
                        <div class="site-wide-badge-status"
                             data-sidewide="<?= $item['site_wide'] ? 'true' : 'false'; ?>"></div>
                        <h5 class="card-title"><?= esc_attr($item['name']); ?></h5>
                        <div class="module-info">
                            <div class="float-left text-muted">
                                <small>
                                    <?php esc_html_trans_e('Version'); ?> : <?= $item['version']; ?>
                                </small>
                            </div>
                            <div class="float-right text-muted">
                            <span class="badge badge-<?= $item['active'] ? 'primary' : 'secondary'; ?>">
                            <?= $item['active'] ? trans('Active') : trans('Inactive'); ?>
                            </span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if (!$item['active'] && $canActivatedModule || $item['active'] && $canDeActivatedModule) { ?>
                            <?php if (is_super_admin() && site_is_global() && $item['active']) { ?>
                                <?php if (module_is_global($identifier)) { ?>
                                <form method="post">
                                    <div class="form-group">
                                    <input type="hidden" name="<?= PARAM_ACTION_QUERY; ?>" value="<?= PARAM_ACTION_DEACTIVATE; ?>">
                                    <input type="hidden" class="hidden hide" name="module" value="<?= esc_attr($identifier); ?>">
                                    <input type="hidden" class="hidden hide" name="global" value="yes">
                                    <input type="hidden" class="hidden hide" name="global_only" value="yes">
                                    <button type="submit" name="<?= PARAM_ID_QUERY;?>" class="btn btn-sm btn-dark btn-block">
                                        <?php esc_html_trans_e('Deactivate Global Only'); ?>
                                    </button>
                                    </div>
                                </form>
                                <?php } elseif ($item['site_wide']) { ?>
                                    <form method="post">
                                        <div class="form-group">
                                            <input type="hidden" name="<?= PARAM_ACTION_QUERY; ?>" value="<?= PARAM_ACTION_ACTIVATE; ?>">
                                            <input type="hidden" class="hidden hide" name="module" value="<?= esc_attr($identifier); ?>">
                                            <input type="hidden" class="hidden hide" name="global" value="yes">
                                            <button type="submit" name="<?= PARAM_ID_QUERY;?>" value="<?= $item['id'];?>" class="btn btn-sm btn-success btn-block">
                                                <?php esc_html_trans_e('Activate Global'); ?>
                                            </button>
                                        </div>
                                    </form>
                                <?php } else { ?>
                                    <div class="form-group pt-1 pb-1">
                                        <div class="pt-1 pb-1 mt-2 mb-2"></div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <form method="post">
                                <input type="hidden" name="<?= PARAM_ACTION_QUERY; ?>" value="<?= $item['active'] ? PARAM_ACTION_DEACTIVATE : PARAM_ACTION_ACTIVATE; ?>">
                                <input type="hidden" class="hidden hide" name="module" value="<?= esc_attr($identifier); ?>">
                                <?php if (is_super_admin() && site_is_global()) { ?>
                                        <?php if (!$item['active']) { ?>
                                        <div class="form-group pt-1 pb-1">
                                            <?php if ($item['site_wide']) { ?>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="global" value="yes" class="custom-control-input" id="checkbox-<?= $item['id']; ?>"<?= $item['active'] && module_is_global($identifier) ? ' checked' : ''; ?><?= $item['site_wide'] ? '' : ' disabled';?>>
                                                <label class="custom-control-label text-muted" for="checkbox-<?= $item['id']; ?>">
                                                    <?php trans_e('Global'); ?>
                                                </label>
                                            </div>
                                            <?php } else { ?>
                                                <div class="pt-1 pb-1 mt-2 mb-2"></div>
                                            <?php } ?>
                                        </div>
                                        <?php } ?>
                                <?php } ?>
                                <button type="submit" name="<?= PARAM_ID_QUERY;?>" value="<?= $item['id'] ;?>" class="btn btn-sm btn-<?= !$item['active'] ? 'primary' : 'danger'; ?> btn-block">
                                    <?php $item['active'] ? esc_html_trans_e('Deactivate') : esc_html_trans_e('Activate'); ?>
                                </button>
                            </form>
                        <?php } else { ?>
                            <div class="form-group">
                                <button type="button" class="btn btn-sm btn-secondary btn-block disabled" disabled><?= $item['active'] ? trans('Active') : trans('Inactive'); ?></button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php
            endforeach;
            ?>
        </div>
        <?php if ($counted === 0) { ?>
        <div class="alert alert-danger mt-4 text-center">
            <h5><?php esc_attr_trans_sprintf_e('%s Module is empty', ucwords($chosenStatus));?></h5>
        </div>
        <?php } ?>
    </div>
    <script type="text/template" id="underscore_template_empty">
        <div class="alert alert-danger mt-4 text-center" id="alert-not-found-module">
            <h5><?php trans_printf('Module \'%s\' has not found', '<%= value %>');?></h5>
        </div>
    </script>
    <script type="text/javascript">
        (function ($) {
            if (!$) {
                return;
            }

            var $card_area = $('.card-area'),
                empty_template = $('script#underscore_template_empty').html(),
                $form = $('form.module-form'),
                $formSearch = $form.find(' #module-form-search-input');
            $form.on('submit', function (e) {
                // e.preventDefault();
            });
            <?php if ($counted > 0) { ?>
            $formSearch.on('keyup', function (e) {
                // e.preventDefault();
                var val = this.value ? this.value.trim() : '';
                var $card;
                var $chosen_status = $('.module-form input[type=radio]:checked').val();
                $chosen_status = $chosen_status !== 'inactive' && $chosen_status !== 'active' ? 'all' : $chosen_status;
                if (val === '') {
                    if ($chosen_status === 'all') {
                        $card_area.find('.card').removeClass('hide');
                        return;
                    }
                    $card_area.find('.card').not('[data-status=' + $chosen_status + ']').addClass('hide');
                    $card_area.find('.card[data-status=' + $chosen_status + ']').removeClass('hide');
                    return;
                }
                $('#alert-not-found-module').remove();
                val = val.replace(/\s+/, ' ');
                $card = $chosen_status !== 'all'
                    ? $card_area.find('.card[data-status=' + $chosen_status + ']')
                    : $card_area.find('.card');
                var found = 0;
                $card.each(function () {
                    var $name = $(this).find('.card-title');
                    if (!$name.length) {
                        return;
                    }
                    $name = $name.html().trim();
                    if ($name === '') {
                        $(this).addClass('hide');
                        return;
                    }
                    $name = $name.replace(/\s+/g, ' ');
                    var re = new RegExp(val, 'ig');
                    var re2 = new RegExp(val.replace(/\s/, ''), 'ig');
                    var $name2 = $name.replace(/\s/g, '');
                    if (re.exec($name) || re2.exec($name) || re.exec($name2) || re2.exec($name2)) {
                        $(this).removeClass('hide');
                        found++;
                        return;
                    }
                    $(this).addClass('hide');
                });
                if (found === 0) {
                    $card_area.append(_.template(empty_template)({
                        chosen: $chosen_status,
                        value: val,
                    }));
                }
            });
            <?php } ?>
        })(window.jQuery);
    </script>
<?php
get_admin_footer_template();
