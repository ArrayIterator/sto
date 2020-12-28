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
$class_site_id = $current_site_id;
$is_super_admin = is_super_admin();
$is_edit = false;
$referer = get_referer();
$allowed_message = true;
$id = query_param_int('id');
$result = null;
if ($id > 0) {
    $result = get_class_by_id($id);
    $class_site_id = $result ? $result['site_id'] : $class_site_id;
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

// if cannot edit
if ($is_edit && !current_supervisor_can('edit_class', query_param('id'))) {
    return load_admin_denied();
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

            $dataResult = get_class_by_id($id);
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

            $class_site_id = $dataResult['site_id'];
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
            if ($response === RESULT_ERROR_EMPTY_CODE) {
                $posts['code'] = $result['code']??'';
                $messageStatus = trans_sprintf('Class %s could not be empty!', trans('Code'));
            } elseif ($response === RESULT_ERROR_EMPTY_CODE) {
                $posts['name'] = $result['name']??'';
                $messageStatus = trans_sprintf('Class %s could not be empty!', trans('Name'));
            } elseif ($response === RESULT_ERROR_EXIST_CODE) {
                $posts['code'] = $result['code']??$posts['code'];
                $messageStatus = trans_sprintf('Class code %s is duplicate!', post('code'));
            } elseif ($response === RESULT_ERROR_EXIST_NAME) {
                $posts['name'] = $result['name']??$posts['name'];
                $messageStatus = trans_sprintf('Class name %s is duplicate!', post('name'));
            } elseif ($response === RESULT_ERROR_FAIL) {
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
            } elseif ($response === RESULT_ERROR_EMPTY_CODE) {
                $messageStatus = trans_sprintf('Class %s could not be empty!', trans('Code'));
            } elseif ($response === RESULT_ERROR_EMPTY_NAME) {
                $messageStatus = trans_sprintf('Class %s could not be empty!', trans('Name'));
            } elseif ($response === RESULT_ERROR_EXIST_CODE) {
                $messageStatus = trans_sprintf(
                    'Class %s is duplicate!',
                    sprintf('%s %s', trans('Code'), post('code'))
                );
            } elseif ($response === RESULT_ERROR_EXIST_NAME) {
                $messageStatus = trans_sprintf(
                    'Class %s is duplicate!',
                    sprintf('%s %s', trans('Name'), post('name'))
                );
            } elseif ($response === RESULT_ERROR_FAIL) {
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
                    <?php if (is_super_admin() && enable_multisite()) { ?>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="class-site-id"><?php esc_html_trans_e('Site Id');?></label>
                        </div>
                        <div class="col-sm-9">
                            <select id="class-site-id" class="custom-select custom-select-sm" data-select="select2" data-tag="true" data-options="{tags: true}" name="site_id" data-info-text="<?= esc_attr_trans('Host:');?>" data-change="true" data-target=".target-host" data-template="<div class='message-inner'><span class='message-name'><%= data.infoText %></span><span class='message-value'><code><%= data.host %></code></span></div>">
                                <option selected disabled><?php esc_html_e('Select Site');?></option>
                            <?php
                                $selectedHost = '';
                                foreach (get_all_sites() as $siteId => $site) {
                                    $selected = $class_site_id  === $siteId ? ' selected' : '';
                                    if ($class_site_id === $siteId) {
                                        $selectedHost = $site->get('host');
                                    }
                            ?>
                                <option value="<?= $siteId;?>"<?=$selected;?> data-host="<?= $site->get('host');?>" data-site-id="<?= $site->getSiteId();?>" data-name="<?= esc_attr($site->get('name'));?>" data-template="<span class='site-id-sep'><%= data['site-id'] || '' %></span><span class='site-name-sep'><%= data.name || ''%></span>">[ <?= $site->getSiteId();?> ] <?php esc_html_e($site->get('name')??'');?></option>
                            <?php } ?>
                            </select>
                            <div class="target-host message-render form-text text-muted">
                                <div class='message-inner'>
                                    <span class='message-name'><?= esc_attr_trans('Host:');?></span>
                                    <span class='message-value'><code><?= esc_html($selectedHost);?></code></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
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
                data_invalid_text = <?= json_ns(trans('Invalid Format'));?>,
                data_duplicate_text = <?= json_ns(trans('Duplicate Data'));?>,
                submitBtn = $('form [type=submit]'),
                regexVal = {
                    'code' : /^[a-zA-Z0-9]([a-zA-Z0-9_-]*[a-zA-Z0-9]+)?$/g,
                    'name' : /^(.+)$/g,
                },
                top_message = $('#top-message'),
                site_id = <?= $class_site_id;?>,
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
                    var alert_m = _.parent().find('.message-alert');
                    if (!alert_m.length) {
                        alert_m = $('<span class="message-alert text-danger small"></span>');
                        _.before(alert_m);
                    }
                    alert_m.html(alert);
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
                    _.closest('.message-alert').remove();
                    _[0].setCustomValidity('');
                    _.removeClass('border-danger');
                    _.addClass('border-success');
                },
                disable_button = function () {
                    submitBtn.attr('disabled', true);
                    submitBtn.addClass('disabled');
                },
                enable_button = function () {
                    submitBtn.removeClass('disabled');
                    submitBtn.attr('disabled', false);
                },
                check_validity = function () {
                    if (formHasChange) {
                        formHasChange = false;
                        for (var name in dataStorage) {
                            if (!dataStorage.hasOwnProperty(name)) {
                                continue;
                            }
                            var $val = $('[name='+ $.escapeSelector(name)+']'),
                                newData;
                            if ($val.is('select')) {
                                newData = $val.find('option:selected').val();
                            } else if ($val.prop('type') === 'checkbox') {
                                newData = $val.prop('checked');
                            } else {
                                newData = $val.val();
                            }
                            if (typeof newData === 'string') {
                                newData = newData.replace(/\s+/g, ' ').trim();
                            }
                            if (name === 'site_id') {
                                if (typeof newData === 'string' && /^[0-9]+$/.test(newData)) {
                                    newData = parseInt(newData);
                                }

                                if (typeof newData === "number") {
                                    site_id = newData;
                                }
                            }

                            if (newData !== dataStorage[name]) {
                                formHasChange = true;
                                break;
                            }
                        }
                    }

                    for (var i in validity) {
                        if (validity[i] === false || ! formHasChange) {
                            disable_button();
                            return false;
                        }
                    }

                    enable_button();
                    return true;
                },
                search_by = function (type, q, succeed, fail, site_id) {
                    var url = search_url + '?type='+type+'&q='+q+'&limit=10';
                    if (typeof site_id === "number") {
                        url += "&site_id="+site_id;
                    }
                    return $.get(url, {}, succeed).fail(fail);
                },
                current_ajax = {},
                validity = {},
                codeHasChange = false,
                nameHasChange = false,
                dataStorage = {};
            // if on submit - do reverse
            $form.on('submit', function () {
                formHasChange = false;
            });
            var hasOut = false,
                inp = $form
                .find('input[name],textarea[name],select[name]')
                .not('button,[type=submit],[type=reset]');
            var detect_change = function (e) {
                var $this = $(this),
                    name = $this.attr('name'),
                    isTextarea = $this.is('textarea'),
                    isRequired = $this.is('required')
                        || name === 'code'
                        || name === 'name';

                if (!top_cleared) {
                    top_cleared = true;
                    top_message.find('.alert .close').click();
                }

                $this.parent().find('.message-alert').remove();
                if (!isRequired && dataStorage[name] === undefined) {
                    check_validity();
                    return;
                }

                var newData;
                if ($this.is('select')) {
                    newData = $this.find('option:selected').val();
                } else if ($this.prop('type') === 'checkbox') {
                    newData = $this.prop('checked');
                } else {
                    newData = $this.val();
                }

                if (name === 'site_id' && e.type === 'change') {
                    if (typeof newData === 'string' && /^[0-9]+$/.test(newData)) {
                        newData = parseInt(newData);
                    }

                    if (typeof newData === "number") {
                        site_id = newData;
                    }
                }

                if (newData !== dataStorage[name]) {
                    formHasChange = true;
                }

                if ($this.is('input')
                    && $this.prop('type') === 'checkbox'
                    && ! $this.is('textarea')
                    || $this.is('select')
                ) {
                    if (name === 'site_id' && $this.is('select')) {
                        $('[name=code], [name=name]').trigger('focusout');
                        hasOut = false;
                        return;
                    }
                    if (!hasOut) {
                        check_validity();
                    }

                    hasOut = false;
                    return;
                }

                var values = newData.replace(/\s+/g, ' ').trim(),
                    lowerValue = values.toLowerCase().trim(),
                    re = name ? regexVal[name] : null,
                    valid  = (!isRequired
                            || values !== '' && (re !== null ? values.match(re) !== null : true)
                        ) && (
                            lastRes[name] === undefined
                            || lastRes[name][site_id] === undefined
                            || lastRes[name][site_id][lowerValue] === undefined
                            || lastRes[name][site_id][lowerValue] === true
                        );
                validity[name] = valid;
                $this.addClass(valid ? bSuccess : bDanger);
                $this.removeClass(!valid ? bSuccess : bDanger);
                if (inType === false) {
                    inType = true;
                }

                formHasChange = dataStorage[name] !== values;
                if (e.type !== 'focusout') {
                    check_validity();
                    return;
                }

                inType = false;
                if (isTextarea) {
                    values = values
                        .replace(/[ ]*([\n])+[ ]*/g, '$1')
                        .replace(/[\r ]+/g, ' ')
                        .replace(/(^[\n]+|[\n]+$)/g, "");
                    if (values === newData) {
                        $this.val(values);
                        formHasChange = true;
                    }
                }

                if (!isRequired) {
                    check_validity();
                    return;
                }

                if (values === '') {
                    set_alert($this, data_empty_text);
                    disable_button();
                    return;
                }

                if (!valid) {
                    set_alert(
                        $this,
                        lastRes[name] === undefined
                            || lastRes[name][site_id] === undefined
                            || typeof lastRes[name][site_id][lowerValue] !== "boolean"
                            ? data_invalid_text
                            : data_duplicate_text
                    );
                    disable_button();
                    return;
                }

                if (valid && name !== 'code' && name !== 'name') {
                    set_succeed($this);
                    check_validity();
                    return;
                }

                if (!lastRes[name]) {
                    lastRes[name] = {};
                }
                if (lastRes[name][site_id] === undefined) {
                    lastRes[name][site_id] = {};
                }

                if (typeof lastRes[name][site_id][lowerValue] === 'boolean') {
                    if (lastRes[name][site_id][lowerValue]) {
                        set_succeed($this);
                    } else {
                        set_alert($this, data_duplicate_text)
                    }
                    check_validity();
                    return;
                }
                if (current_ajax[name]) {
                    current_ajax[name].abort()
                }
                if (name === 'name') {
                    nameHasChange = true;
                } else if (name === 'code') {
                    codeHasChange = true;
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
                            lastRes[name][site_id][valuesLow] = true;
                            set_succeed($this);
                            check_validity();
                            return;
                        }

                        for (var i in e.data.results) {
                            if (!e.data.results.hasOwnProperty(i)) {
                                continue;
                            }
                            var ie = e.data.results[i],
                                iN = ie[name].toString().toLowerCase().trim(),
                                sid = ie['site_id'];
                            if (lastRes[name][sid] === undefined) {
                                lastRes[name][sid] = {};
                            }
                            lastRes[name][sid][iN] = ie.id === id;
                        }

                        validity[name] = lastRes[name][site_id][valuesLow] === true
                            || lastRes[name][site_id][valuesLow] === undefined;
                        if (!validity[name]) {
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
                    site_id
                );
            };

            inp.each(function () {
                    var $this = $(this),
                        name = $this.attr('name');
                    if ($this.is('select')) {
                        dataStorage[name] = $this.find('option:selected').val().replace(/\s+/g, ' ').trim();
                    } else if ($this.prop('type') === 'checkbox') {
                        dataStorage[name] = $this.prop('checked');
                    } else {
                        dataStorage[name] = $this.val().replace(/\s+/g, ' ').trim();
                    }

                    if (name === 'site_id') {
                        if (typeof dataStorage[name] === 'string' && /^[0-9]+$/.test(dataStorage[name])) {
                            dataStorage[name] = parseInt(dataStorage[name]);
                        }
                        if (typeof dataStorage[name] === "number") {
                            site_id = dataStorage[name];
                        }
                    }

                    validity[name] = $this.is('required')
                        || dataStorage[name] !== undefined && dataStorage[name] !== '';
                })
                .on('change', detect_change)
                .on('focusout',detect_change)
                .on('keyup', detect_change);
            $(window).on('beforeunload', function () {
                if (!formHasChange) {
                    dataStorage = {};
                    return;
                }
                var formChange = false,
                    name;
                for (name in dataStorage) {
                    if (!dataStorage.hasOwnProperty(name)) {
                        continue;
                    }
                    var $val = $('[name='+ $.escapeSelector(name)+']'),
                        newData;
                    if ($val.is('select')) {
                        newData = $val.find('option:selected').val();
                    } else if ($val.prop('type') === 'checkbox') {
                        newData = $val.prop('checked');
                    } else {
                        newData = $val.val();
                    }
                    if (newData !== dataStorage[name]) {
                        formChange = true;
                        break;
                    }
                }
                if (formChange) {
                    return <?= json_ns(trans('Do you really want to leave this page?'));?>
                }
                dataStorage = {};
            });
            check_validity();
        })(window.jquery);
    </script>
<?php
get_admin_footer_template();
