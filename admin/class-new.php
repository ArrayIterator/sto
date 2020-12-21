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
$matchReferer = $referer
    && (string) (new Uri($referer))->withQuery('')->withFragment('') === get_admin_current_file_url();
if (is_method_get() && query_param('action') === 'edit' && has_query_param('id')) {
    $id = query_param_int('id');
    if ($id > 0) {
        $result = get_class_by_id($id);
        if ($result && ($is_super_admin || $result['site_id'] === $current_site_id)) {
            $is_edit = !empty($result);
            $posts = array_merge($posts, $result);
        }
    }
    if (!$is_edit) {
        return redirect(get_admin_current_file_url());
    } else {
        set_admin_title(
            trans_sprintf(
                'Edit Class: %s',
                $posts['code']
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
                    NormalizerData::addQueryArgs(
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
                    NormalizerData::addQueryArgs(
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
                    NormalizerData::addQueryArgs(
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
                return redirect(
                    NormalizerData::addQueryArgs(
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
                    NormalizerData::addQueryArgs(
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
                    'class_update',
                    $messageStatus
                );
            }

            /*
            if ($response === -1) {
                return redirect(
                    NormalizerData::addQueryArgs(
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
                    NormalizerData::addQueryArgs(
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
                    NormalizerData::addQueryArgs(
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
if ($allowed_message && $referer && $is_edit && ($statusSuccess = query_param('success'))) {
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
        <form method="post" action="<?= esc_attr(NormalizerData::removeQueryArg(['success', 'response'],get_current_url()));?>">
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
                                    '^[a-z0-9][a-z0-9_-]*[a-zA-Z0-9]+'
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
            var search_url = <?= json_encode(get_api_url('/classes/search/'), JSON_UNESCAPED_SLASHES);?>;
            function search_by(type, q, succeed, fail) {
                $.get(search_url + '?type='+type+'&q='+q+'&limit=10', {}, succeed).fail(fail);
            }
            var top_message = $('#top-message');
            var site_id = <?= get_current_site_id();?>;
            var id = <?= $posts['id']??'null';?>;
            var $progress = {};
            var lastRes = {
                'code' : {},
                'name' : {},
            };
            function exist_selector(selector) {
                selector[0].setCustomValidity(<?= json_encode(trans('Data Exists'));?>);
                // selector.focus();
                selector.removeClass('border-success');
                selector.addClass('border-danger');
            }

            function empty_selector(selector) {
                selector[0].setCustomValidity(<?= json_encode(trans('Data Could Not Be Empty'));?>);
                selector.removeClass('border-success');
                selector.addClass('border-danger');
            }
            function clear_selector(selector) {
                selector[0].setCustomValidity('');
                selector.removeClass('border-danger');
                selector.addClass('border-success');
            }
            function clear_all_selector(selector) {
                selector[0].setCustomValidity('');
                selector.removeClass('border-danger');
                selector.removeClass('border-success');
            }
            function add_validity(e) {;
                var $this = $(this);
                var _sel = $this.attr('name');
                $progress[_sel] = false
                if (_sel !== 'code' && _sel!=='name') {
                    return;
                }
                var originVal = $this.val() || '',
                    val = originVal.trim();
                if (originVal !== val) {
                    $this.val(val);
                }
                $this.removeClass('border-danger');
                $this.removeClass('border-success');
                val = val.toLowerCase();
                if (typeof lastRes[_sel][val] === 'boolean') {
                    if (!lastRes[_sel][val]) {
                        exist_selector($this);
                        return;
                    }
                    clear_selector($this);
                    return;
                }
                if (val) {
                    search_by(
                        'code',
                        val,
                        function (e) {
                            e = e.data.result;
                            for (var i in e) {
                                if (!e.hasOwnProperty(i)) {
                                    continue;
                                }
                                var ie = e[i];
                                if (ie.code.toLowerCase() === val) {
                                    lastRes[_sel][val] = ie.id === id;
                                    if (!lastRes[_sel][val]) {
                                        exist_selector($this);
                                        return;
                                    }
                                    clear_selector($this);
                                    return;
                                }
                            }
                            lastRes[_sel][val] = true;
                            clear_selector($this);
                        },
                        function () {

                        }
                    );
                } else {
                    empty_selector($this)
                }
            }
            var cleared_top = false;
            function clear_top_message()
            {
                if (cleared_top) {
                    return;
                }
                cleared_top = true;
                top_message.find('.alert-dismissible .close').click();
            }
            var $code = $('[name=code]');
            var $name = $('[name=name]');
            $code.on('focusout', add_validity);
            $name.on('focusout', add_validity);
            $code.on('keyup', function () {
                clear_top_message();
                var $name = $(this).attr('name');
                if ($progress[$name] === true) {
                    return;
                }
                $progress[$name] = true;
                clear_all_selector($(this));
            });
            $name.on('keyup', function () {
                clear_top_message();
                var $name = $(this).attr('name');
                if ($progress[$name] === true) {
                    return;
                }
                $progress[$name] = true;
                clear_all_selector($(this));
            });
        })(window.jquery);
    </script>
<?php
get_admin_footer_template();
