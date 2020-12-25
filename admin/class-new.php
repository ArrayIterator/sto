<?php

use ArrayIterator\Helper\NormalizerData;
use GuzzleHttp\Psr7\Uri;

require __DIR__ . '/init.php';

if (!admin_is_allowed(__FILE__)) {
    return load_admin_denied();
}

set_admin_title('Add New Class');

$posts = array_map_string_empty(posts());
$current_site_id = get_current_site_id();
$is_super_admin = is_super_admin();
$is_edit = false;
$referer = get_referer();
$allowed_message = true;
$id = query_param_int('id');
$result = null;
if ($id > 0) {
    $result = get_class_by_id($id);
}

$matchReferer = $referer
    && (string) (new Uri($referer))->withQuery('')->withFragment('') === get_admin_current_file_url();
if (is_method_get() && query_param('action') === 'edit' && has_query_param('id')) {
    if ($result && isset($result['id']) && ($is_super_admin || $result['site_id'] === $current_site_id)) {
        $is_edit = !empty($result);
        $posts = array_merge($posts, $result);
    }

    if (!$is_edit) {
        return redirect(get_admin_current_file_url());
    } else {
        set_admin_title(
            trans_sprintf(
                'Edit Class: %s',
                $posts['code']??''
            )
        );
    }
}


if (is_method_post()) {
    if (($posts['action']??null) === 'edit') {
        $id = $posts['id'] ?? null;
        if ($id && $id > 0) {
            $is_edit = true;
            if (!current_supervisor_can('edit_class')) {
                return redirect(
                    add_query_args(
                        [
                            'action' => 'edit',
                            'id' => $id,
                            'success' => 'false',
                            'response' => 'denied',
                        ],
                        get_admin_current_file_url()
                    )
                );
            }

            $dataResult = get_classes_by_id($id);
            if (!$dataResult) {
                return redirect(
                    add_query_args(
                        [
                            'action' => 'edit',
                            'id' => $id,
                            'success' => 'false',
                            'response' => 'empty',

                        ],
                        get_admin_current_file_url()
                    )
                );
            }

            if (!$is_super_admin && $current_site_id !== $dataResult['site_id']) {
                return redirect(
                    add_query_args(
                        [
                            'action' => 'edit',
                            'id' => $id,
                            'success' => 'false',
                            'response' => 'denied',
                        ],
                        get_admin_current_file_url()
                    )
                );
            }

            $response = update_class_data($id, $posts);
            if ($response === true || $response === 1) {
                create_cookie_succeed();
                return redirect(
                    add_query_args(
                        [
                            'action' => 'edit',
                            'id' => $id,
                            'success' => 'true',
                            'response' => 'true',
                        ],
                        get_admin_current_file_url()
                    )
                );
            } elseif ($response === false) {
                return redirect(
                    add_query_args(
                        [
                            'action' => 'edit',
                            'id' => $id,
                            'success' => 'false'
                        ],
                        get_admin_current_file_url()
                    )
                );
            }
            $messageStatus = null;
            if ($response === -4) {
                $posts['name'] = $result['name']??'';
                $messageStatus = trans_sprintf('Class %s could not be empty!', trans('Code'));
            } elseif ($response === -3) {
                $posts['name'] = $result['name']??'';
                $messageStatus = trans_sprintf('Class %s could not be empty!', trans('Name'));
            } elseif ($response === -2) {
                $posts['code'] = $result['code']??$posts['code'];
                $messageStatus = trans_sprintf('Class code %s is duplicate!', post('code'));
            } elseif ($response === -1) {
                $posts['name'] = $result['name']??$posts['name'];
                $messageStatus = trans_sprintf('Class name %s is duplicate!', post('name'));
            } elseif ($response === false) {
                $messageStatus = trans('Error save data!');
            }

            if ($messageStatus) {
                $allowed_message = false;
                add_admin_error_message(
                    'class_update',
                    $messageStatus
                );
            }

            /*
            if ($response === -1) {
                return redirect(
                    add_query_args(
                        [
                            'action' => 'edit',
                            'id' => $id,
                            'success' => 'false',
                            'response' => 'name',
                        ],
                        get_admin_current_file_url()
                    )
                );
            }
            if ($response === -2) {
                return redirect(
                    add_query_args(
                        [
                            'action' => 'edit',
                            'id' => $id,
                            'success' => 'false',
                            'response' => 'code',
                        ],
                        get_admin_current_file_url()
                    )
                );
            }
            */
        }
    } else {
        $is_edit = false;
        $messageStatus = null;
        if (!current_supervisor_can('add_class')) {
            $messageStatus = sprintf(
                '%s. <strong>%s</strong>',
                trans('Error save data!'),
                trans('Access Denied')
            );
        } else {
            $response = insert_class_data($posts);
            if (is_array($response)) {
                return redirect(
                    add_query_args(
                        [
                            'action' => 'edit',
                            'id' => $response['id'],
                            'success' => 'true',
                            'response' => 'saved',
                        ],
                        get_admin_current_file_url()
                    )
                );
            } elseif ($response === -4) {
                $messageStatus = trans_sprintf('Class %s could not be empty!', trans('Code'));
            } elseif ($response === -3) {
                $messageStatus = trans_sprintf('Class %s could not be empty!', trans('Name'));
            } elseif ($response === -2) {
                $messageStatus = trans_sprintf('Class code %s is duplicate!', post('code'));
            } elseif ($response === -1) {
                $messageStatus = trans_sprintf('Class name %s is duplicate!', post('name'));
            } elseif ($response === false) {
                $messageStatus = trans('Error save data!');
            }

            if ($messageStatus) {
                $allowed_message = false;
                add_admin_error_message(
                    'class_insert',
                    $messageStatus
                );
            }
        }
    }
}

$is_from_referer_update = false;
if (has_cookie_succeed() && $allowed_message && $referer && $is_edit && ($statusSuccess = query_param('success'))) {
    if ($matchReferer) {
        $statusResponse = query_param('response');
        $messageStatus = null;
        if ($statusResponse === 'true' || $statusResponse === '1') {
            add_admin_success_message(
                'class_update',
                trans_sprintf('Class %s successfully updated!', $posts['code'])
            );
        } elseif ($statusResponse === 'saved') {
            add_admin_success_message(
                'class_insert',
                trans_sprintf('Class %s successfully saved!', $posts['code'])
            );
        } elseif ($statusResponse && is_string($statusResponse)) {
            switch ($statusResponse) {
                case 'code':
                    $messageStatus = trans_sprintf(
                        'Class %s is duplicate! Data has been reverted!',
                        trans('code')
                    );
                    break;
                case 'name':
                    $messageStatus = trans_sprintf(
                        'Class %s is duplicate! Data has been reverted!',
                        trans('name')
                    );
                    break;
                case 'denied':
                    $messageStatus = sprintf(
                        '%s. <strong>%s</strong>',
                        trans('Error save data!'),
                        trans('Access Denied')
                    );
                    break;
            }
        } elseif ($statusSuccess === 'false') {
            $messageStatus = trans('Error save data!');
        }

        if ($messageStatus) {
            add_admin_error_message(
                'class_update',
                $messageStatus
            );
        }
    }
}

get_admin_header_template();
?>
    <div class="form-post panel-form">
        <form method="post" id="form-edit-class" action="<?= esc_attr(NormalizerData::removeQueryArg(['success', 'response'],get_current_url()));?>">
            <div class="card">
                <div class="card-header">
                    <div class="text-muted small"><?php trans_e(get_admin_title());?></div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="class-code"><?php esc_html_trans_e('Class Code');?></label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="class-code" maxlength="60" minlength="1" pattern="[a-zA-Z0-9]([a-zA-Z0-9_-]*[a-zA-Z0-9])*" name="code" value="<?php esc_attr_e($posts['code']??'');?>" placeholder="<?php esc_attr_trans_printf('eg: %s', 'IX-2');?>" required>
                            <small class="form-text text-muted">
                                <?php trans_printf(
                                    'Class code must be contains only alpha numeric, underscore and dashes. Must be started and ending with alphabet or numeric with format: <code>%s</code>',
                                    '^[a-zA-Z0-9][a-zA-Z0-9_-]*[a-zA-Z0-9]+$'
                                );?>
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="class-name"><?php esc_html_trans_e('Class Name');?></label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="class-name" maxlength="255" name="name" value="<?php esc_attr_e($posts['name']??'');?>" placeholder="<?php esc_attr_trans_printf('eg: %s', 'Math');?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="class-note"><?php esc_html_trans_e('Class Note');?></label>
                        </div>
                        <div class="col-sm-9">
                            <textarea rows="10" class="form-control" id="class-note" maxlength="1000" name="note" placeholder="<?php esc_attr_trans_printf('eg: %s', 'Class Description');?>"><?= trim(esc_attr($posts['note']??''));?></textarea>
                        </div>
                    </div>
                    <?php
                        if ($is_edit) :
                    ?>
                        <input type="hidden" name="id" value="<?= $posts['id'];?>">
                        <input type="hidden" name="action" value="edit">
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <?= get_button_submit_small('right');?>
                    <div class="clearfix"></div>
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        (function () {
            var $form = $('form#form-edit-class'),
                search_url = <?= json_ns(get_api_url('/classes/search/'));?>,
                data_empty_text = <?= json_ns(trans('Data could not be empty'));?>,
                data_invalid_text = <?= json_ns(trans('Invalid format'));?>,
                data_duplicate_text = <?= json_ns(trans('Data is duplicate'));?>,
                submitBtn = $('form [type=submit]'),
                regexVal = {
                    'code' : /^[a-zA-Z0-9]([a-zA-Z0-9_-]*[a-zA-Z0-9]+)?$/g,
                    'name' : /^(.+)$/g,
                },
                top_message = $('#top-message'),
                site_id = <?= get_current_site_id();?>,
                id = <?= $posts['id']??'null';?>,
                bSuccess = 'border-success',
                bDanger = 'border-danger',
                top_cleared = false,
                formHasChange = false,
                lastRes = {
                    'code' : {},
                    'name' : {},
                },
                inType = false,
                set_alert = function (_, alert) {
                    _ = $(_[0] || _);
                    _[0].setCustomValidity(alert);
                    _.addClass(bDanger);
                    _.removeClass(bSuccess);
                },
                get_size = function (obj) {
                    var size = 0, key;
                    for (key in obj) {
                        if (obj.hasOwnProperty(key)) size++;
                    }
                    return size;
                },
                set_succeed = function (_) {
                    _ = $(_[0] || _);
                    _[0].setCustomValidity('');
                    _.removeClass('border-danger');
                    _.addClass('border-success');
                },
                check_validity = function () {
                    if (formHasChange) {
                        formHasChange = false;
                        for (var name in dataStorage) {
                            if (!dataStorage.hasOwnProperty(name)) {
                                continue;
                            }
                            var val = $('[name='+ $.escapeSelector(name)+']');
                            if (val.val() !== dataStorage[name]) {
                                formHasChange = true;
                                break;
                            }
                        }
                    }

                    for (var i in validity) {
                        if (validity[i] === false || ! formHasChange) {
                            submitBtn.attr('disabled', true);
                            submitBtn.addClass('disabled');
                            return false;
                        }
                    }
                    submitBtn.removeClass('disabled');
                    submitBtn.attr('disabled', false);
                    return true;
                },
                search_by = function (type, q, succeed, fail) {
                    return $.get(search_url + '?type='+type+'&q='+q+'&limit=10', {}, succeed).fail(fail);
                },
                current_ajax = {},
                validity = {},
                dataStorage = {};
            $(window).on('beforeunload', function () {
                if (formHasChange) {
                    var formChange = false;
                    for (var name in dataStorage) {
                        if (!dataStorage.hasOwnProperty(name)) {
                            continue;
                        }
                        var val = $('[name='+ $.escapeSelector(name)+']');
                        if (val.val() !== dataStorage[name]) {
                            formChange = true;
                            break;
                        }
                    }
                    if (formChange) {
                        return <?= json_ns(trans('Do you really want to leave this page?'));?>
                    }
                }
                dataStorage = {};
            });

            $form.on('submit', function () {
               formHasChange = false;
            });

            var selectorNotType = 'input[name]:not([type=button]):not([type=submit]), textarea[name]';
            var hasOut = false;
            $form
                .find(selectorNotType)
                .each(function () {
                    var $this = $(this),
                        name = $this.attr('name'),
                        originalValues = ($this.val() || '').toString(),
                        values = originalValues.replace(/\s+/g, ' ').trim();
                    dataStorage[name] = values;
                    validity[name] = $this.attr('required') === undefined || values.length > 0;
                    check_validity();
                })
                .on('keyup', function () {
                    var name = $(this).attr('name');
                    if (dataStorage[name] !== undefined && dataStorage[name] !== $(this).val()) {
                        formHasChange = true;
                    }
                    if (!hasOut) {
                        check_validity();
                    }
                    hasOut = false;
                }).on('change', function () {
                    hasOut = true;
                });
            $form
                .find('textarea[name]:not([required]),input[name]:not([required])')
                .on('focusout', function () {
                    var $this = $(this),
                        name = $this.attr('name'),
                        originalValues = ($this.val() || '').toString(),
                        values = originalValues.replace(/\s+/g, ' ').trim();
                    $this.addClass(bSuccess);
                    validity[name] = true;
                    var isTextarea = $this.prop('tagName') === 'TEXTAREA';
                    if (isTextarea) {
                        values = originalValues
                            .replace(/([\n]+)\s*/g, "\n")
                            .replace(/[\r ]+/g, ' ')
                            .replace(/(^[\n]+|[\n]+$)/g, "");
                        if (values === originalValues) {
                            $this.val(values);
                            formHasChange = true;
                        }
                    }
                    if (isTextarea && values !== originalValues) {
                        $this.val(values);
                        formHasChange = true;
                    }
                    if (!top_cleared) {
                        top_cleared = true;
                        top_message.find('.alert .close').click();
                    }
                });
            $('textarea[name][required],input[name][required]')
                .on('keyup focusout', function (e) {
                    if (!top_cleared) {
                        top_cleared = true;
                        top_message.find('.alert .close').click();
                    }
                    var $this = $(this),
                        originalValues = ($this.val() || '').toString(),
                        values = originalValues.replace(/\s+/g, ' ').trim(),
                        lowerValue = values.toString().toLowerCase().trim(),
                        name = $this.attr('name');
                    var re = name ? regexVal[name] : null,
                        valid  = (values !== '' && (re === null || !!(values.match(re))));
                        valid  = valid && (
                            lastRes[name] === undefined
                            || lastRes[name][lowerValue] === undefined
                            || lastRes[name][lowerValue] === true
                        );

                    validity[name] = valid;
                    $this.addClass(valid ? bSuccess : bDanger);
                    $this.removeClass(!valid ? bSuccess : bDanger);
                    if (inType === false) {
                        inType = true;
                    }

                    if (dataStorage[name] !== undefined && dataStorage[name] !== values) {
                        formHasChange = true;
                    }

                    if (e.type === 'focusout') {
                        inType = false;
                        var isTextarea = $this.prop('tagName') === 'TEXTAREA';
                        if (isTextarea) {
                            values = originalValues
                                .replace(/([\n])+\s*/g, '$1')
                                .replace(/[\r ]+/g, ' ')
                                .replace(/(^[\n]+|[\n]+$)/g, "");
                            if (values === originalValues) {
                                $this.val(values);
                                formHasChange = true;
                            }
                        }
                        if (isTextarea && values !== originalValues) {
                            $this.val(values);
                            formHasChange = true;
                        }

                        if (values === '') {
                            set_alert($this, data_empty_text);
                        } else if (!valid) {
                            if (lastRes[name][lowerValue] === false) {
                                set_alert($this, data_duplicate_text);
                                return;
                            }
                            set_alert($this, data_invalid_text);
                        } else if (valid && lastRes[name][lowerValue] === true) {
                            set_succeed($this);
                        } else {
                            if (name === 'code' || name === 'name') {
                                if (typeof lastRes[name][lowerValue] === 'boolean') {
                                    if (lastRes[name][lowerValue]) {
                                        set_succeed($this);
                                        return;
                                    }
                                    set_alert($this, data_duplicate_text)
                                    return;
                                }
                                if (name && current_ajax[name]) {
                                    current_ajax[name].abort()
                                }
                                current_ajax[name] = search_by(
                                    name,
                                    values,
                                    function(e) {
                                        delete current_ajax[name];
                                        if (!e.data || !e.data.results) {
                                            return;
                                        }

                                        var valuesLow = values.toString().toLowerCase().trim()
                                        if (get_size(lastRes[name]) > 1000) {
                                            var count_ = 0;
                                            for (var k in lastRes[name]) {
                                                if (!lastRes[name].hasOwnProperty(k)) {
                                                    continue;
                                                }

                                                delete lastRes[name][k];
                                                if (count_++ > 20) {
                                                    break;
                                                }
                                            }
                                        }
                                        if (e.data.results.length === 0) {
                                            lastRes[name][valuesLow] = true;
                                            set_succeed($this);
                                            return;
                                        }
                                        for (var i in e.data.results) {
                                            if (!e.data.results.hasOwnProperty(i)) {
                                                continue;
                                            }
                                            var ie = e.data.results[i],
                                                iN = ie[name].toString().toLowerCase().trim();
                                            lastRes[name][iN] = iN !== values;
                                            if (id !== null) {
                                                lastRes[name][iN] = lastRes[name][iN] && ie[id] === id;
                                            }
                                        }
                                        lastRes[name][valuesLow] = !(lastRes[name][valuesLow]);
                                        validity[name] = lastRes[name][valuesLow] === true;
                                        if (lastRes[name][valuesLow] !== true) {
                                            set_alert($this, data_duplicate_text);
                                        } else {
                                            set_succeed($this);
                                        }
                                        check_validity();
                                    },
                                    function (e) {
                                        validity[name] = true;
                                        delete current_ajax[name];
                                        check_validity();
                                    },
                                );

                                check_validity();
                                return;
                            }
                            set_succeed(this);
                        }
                    }
                    check_validity();
                });
        })(window.jquery);
    </script>
<?php
get_admin_footer_template();
