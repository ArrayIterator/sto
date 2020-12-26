<?php
require __DIR__ . '/init.php';

if (!admin_is_allowed(__FILE__)) {
    return load_admin_denied();
}

set_admin_title('All Classes');
$page = query_param_int('page');
$page = $page < 1 ? 1 : $page;

$perPage = query_param_int('limit');
$perPage = $perPage < 1 ? MYSQL_DEFAULT_DISPLAY_LIMIT : $perPage;
$perPage = $perPage > MYSQL_MAX_RESULT_LIMIT ? MYSQL_MAX_RESULT_LIMIT : $perPage;

$offset = ($page - 1) * $perPage;

$result = get_classes_data($perPage, $offset);

$total_data = $result['total'];
$total_result = $result['count'];
$meta = $result['meta'];
$total_page = $meta['page']['total'];
$current_page = $meta['page']['current'] ?? $total_page;
$result = $result['results'];
$can_edit_class = current_supervisor_can('edit_class');
if ($total_data > 0 && $total_result === 0 && (!$meta['page']['current'] || $meta['page']['current'] > $total_page)) {
    redirect(add_query_args(['page' => $total_page], get_current_url()));
    do_exit();
}

get_admin_header_template();

?>
    <style type="text/css">
        .row [class*=col-] .select2 {
            width:100%!important;
        }
        table.table > tbody > tr > td.row-checkbox + td {
            width: 200px;
            table-layout: auto;
        }
        table.table > tbody > tr > td.row-checkbox + td + td {
            width: 300px;
            table-layout: auto;
        }
    </style>
    <div class="card card-list">
        <div class="card-body p-0">
            <table class="table table-striped table-list">
                <thead class="thead-light">
                    <th scope="col" colspan="1" class="row-checkbox">
                        <label class="hide text-hide" for="checkbox-select-all"></label>
                        <input type="checkbox" id="checkbox-select-all" class="checkbox-input" data-action="check" data-target="class-id">
                    </th>
                    <th scope="col"><?= esc_html_trans('Code'); ?></th>
                    <th scope="col"><?= esc_html_trans('Class Name'); ?></th>
                    <th scope="col" class="hidden d-md-table-cell"><?= esc_html_trans('Note'); ?></th>
                </thead>
                <tbody data-content="table">
                <?php foreach ($result as $row) :
                        $identifier = 'class-id-'.$row['id'];
                        // data-teachers="<?= json_ns($row['teachers']);
                    ?>
                    <tr id="<?= $identifier;?>" data-site-id="<?= $row['site_id']; ?>" data-id="<?= $row['id']; ?>">
                        <td class="row-checkbox">
                            <label for="table-class-id-<?= $row['id'] ?>" class="hide text-hide"></label>
                            <input type="checkbox" id="table-class-id-<?= $row['id'] ?>" data-source="class-id" value="<?= $row['id']; ?>">
                        </td>
                        <td class="cell-title">
                            <div class="row-title">
                                <a data-action="<?= $can_edit_class ? 'edit': 'preview';?>" data-link-id="<?= $row['id'];?>" href="<?= esc_attr(
                                    $can_edit_class
                                        ? add_query_args(['action' => 'edit', 'id' => $row['id']], get_admin_url('class-new.php'))
                                        : "#{$identifier}"
                                );?>">
                                    <?= esc_html_trans($row['code']); ?>
                                </a>
                            </div>
                            <div class="row-action">
                                <?php if ($can_edit_class) {?>
                                <a data-link-id="<?= $row['id'];?>" href="<?= esc_attr(add_query_args(['action' => 'edit', 'id' => $row['id']], get_admin_url('class-new.php'))) ?>"><?php trans_e('Edit');?></a>
                                <span class="row-sep">|</span>
                                <?php } ?>
                                <a data-action="preview" data-link-id="<?= $row['id'];?>" href="#<?= $identifier;?>"><?= esc_html_trans('Preview'); ?></a>
                            </div>
                            <div class="row-content"></div>
                        </td>
                        <td><?= $row['name']; ?></td>
                        <td class="hidden d-md-table-cell"><?= substr_tag_strip($row['note'], 0, 80, '...'); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot class="thead-light">
                    <th scope="col" colspan="1" class="row-checkbox">
                        <label class="hide text-hide" for="checkbox-select-all"></label>
                        <input type="checkbox" id="checkbox-select-all" class="checkbox-input" data-action="check" data-target="class-id">
                    </th>
                    <th scope="col"><?= esc_html_trans('Code'); ?></th>
                    <th scope="col"><?= esc_html_trans('Class Name'); ?></th>
                    <th scope="col" class="hidden d-md-table-cell"><?= esc_html_trans('Note'); ?></th>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <form method="get" id="result-classes" action="<?= get_current_url(); ?>">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="">
                            <div class="form-group">
                                <label for="result-per-page" class="col-form-label sr-only"><?php esc_html_trans_e('Result per page'); ?></label>
                                <select id="result-per-page" class="custom-select custom-select-sm" name="limit" data-change-submit="true" data-select="select2" data-placeholder="<?php esc_attr_trans_e('Result per page'); ?>">
                                    <option disabled selected><?php esc_html_trans_e('Result per page'); ?></option>
<?php
    $range = range(1, MYSQL_MAX_RESULT_LIMIT / 10);
    $range = array_map(function ($e) {
        return $e*5;
    }, $range);
    $range[] = $perPage;
    $range = array_unique($range);
    asort($range);
    foreach ($range as $select) {
        $selected = $select === $perPage ? ' selected' : ''; ?>
                                    <option value="<?= $select; ?>"<?= $selected; ?>><?= $select; ?></option>
<?php } ?>
                                </select>
                                &nbsp;
                                <button type="submit" class="hide-if-js btn btn-primary btn-sm"><?php esc_html_trans_e('Submit'); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="offset-md-4 col-md-4 col-sm-6">
                        <div class="" aria-label="<?php esc_attr_trans_e('Classes pagination'); ?>">
                            <div class="form-group">
                                <label for="result-class-page" class="sr-only col-form-label"><small><?php esc_html_trans_e('Current Page'); ?></small></label>
                                <select name="page" id="result-class-page" class="form-control custom-select-sm" data-change-submit="true" data-select="select2">
                                    <option disabled selected><?php esc_html_trans_e('Select Page'); ?></option>
                                    <?php
                                    foreach (range(1, $total_page) as $item) {
                                        $selected = $item === $current_page ? ' selected' : '';
                                        ?>
                                        <option value="<?= $item;?>"<?= $selected;?>><?= $item;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="clearfix"></div>
        </div>
    </div>
    <script type="text/template" id="underscore_template_classes">
        <% _.each(items, function(item, key, arr) { %>
        <?php
            // data-teachers="<%= json(item.teachers) %>
        ?>
        <tr data-site-id="<%= item.site_id %>"">
            <td class="row-checkbox">
                <label for="table-class-id-<%= item.id %>" class="hide text-hide"></label>
                <input type="checkbox" id="table-class-id-<%= item.id %>" data-source="class-id" value="<%= item.id %>">
            </td>
            <td class="cell-title">
                <div class="row-title">
                    <a data-action="<?= $can_edit_class ? 'edit': 'preview';?>" data-link-id="<%= item.id %>" href="<?=
                    $can_edit_class ?
                        esc_attr(
                        add_query_args(
                            [
                                'action' => 'edit',
                                'id' => false
                            ],
                            get_admin_url('class-new.php')
                        )
                        ).'&id=<%= item.id %>' : "#class-id-<%= item.id %>";
                    ?>"><%= item.code %></a>
                </div>
                <div class="row-action">
                    <?php if ($can_edit_class) { ?>
                        <a data-link-id="<%= item.id %>" href="<?= esc_attr(
                            add_query_args(
                                [
                                    'action' => 'edit',
                                    'id' => false
                                ],
                                get_admin_url('class-new.php')
                            )
                        ). '&id=<%= item.id %>'
                        ;?>"><?php trans_e('Edit');?></a>
                        <span class="row-sep">|</span>
                    <?php } ?>
                    <a data-action="preview" data-link-id="<%= item.id %>" href="#class-id-<%= item.id %>">
                        <?= esc_html_trans('Preview'); ?>
                    </a>
                </div>
                <div class="row-content"></div>
            </td>
            <td><%= item.name %></td>
            <td class="hidden d-md-table-cell"><%= strip(item.note, 0, 80, '...') %></td>
        </tr>
        <% }); %>
    </script>
    <script type="text/template" id="underscore_template_class_page">
        <% _.range(1, items).map(function (item) {%>
            <option value="<%= item %>"<%= selected(item) %>><%= item %></option>
        <% }); %>
    </script>
    <script type="text/javascript">
;(function ($) {
    if (!$) {
        return;
    }

    $(document).ready(function () {
        var content_table = $('[data-content=table]');
        if (!content_table.length) {
            return;
        }

        var href = window.location.href;
        var template = $('script[type="text/template"]#underscore_template_classes');
        var templateOption = $('script[type="text/template"]#underscore_template_class_page');
        if (!template.length || !templateOption.length) {
            return;
        }
        template = template.html();
        templateOption = templateOption.html();

        var per_page = <?= $perPage;?>,
            total_page = <?= $total_page;?>,
            total_data = <?= $total_data;?>,
            total_result = <?= $total_result;?>,
            current_page = <?= $page;?>,
            class_url = <?= json_ns(get_api_url('/classes/'));?>,
            InProgress = null;
        var storage_data = {};
        $(window).bind('beforeunload', function(){
            storage_data = {};
        });
        function get_data(page, per_page, callback, err)
        {
            if (InProgress) {
                return;
            }
            var fallback = function (e) {
                    var html = _.template(
                        template
                    )({
                        json: function (e) {
                            return JSON.stringify(e);
                        },
                        strip: function (str, start, end, add) {
                            str = $('<div>'+str+'</div>').text();
                            var new_str = str.replace(/([\s])+/, '$1').trim();
                            new_str = new_str.substr(start, end);
                            if (add && typeof add === 'string' && new_str !== str) {
                                new_str += add;
                            }
                            return new_str;
                        },
                        items: e.data.results,
                    });

                    content_table.html(html);
                    callback && callback(e);
                },
                always = function () {
                    InProgress = null;
                    $('#overlay-result-class').fadeOut();
                };
            var offset = (page * per_page) - per_page;
            var key = 'classes_api:limit:' + per_page + '|offset:'+offset;
            // freed 10737418240 = 10MB
            if (Object.size(storage_data) > 512 && JSON.stringify(storage_data).length > 10737418240) {
                var c = 0,i;
                // reduce 32 keys
                for (i in storage_data) {
                    if (c++ < 32) {
                        delete storage_data[i];
                    }
                }
            }
            if (storage_data[key]) {
                fallback(storage_data[key]);
                always();
                return;
            }

            InProgress = page;
            $.get(class_url, {
                limit: per_page,
                offset: offset,
                },
                function (e) {
                    storage_data[key] = e;
                    fallback(e);
                }
            ).fail(function(xhr) {
                err && err(xhr);
            }).always(always)
        }

        // if (total_result >= total_data) {
        //     return;
        // }

        $('form#result-classes').on('submit', function (e) {
            e.preventDefault();
            var $this = $(this);
            var $page = $this.find('select[name=page]');
            var $optPage = $page.find('option:selected');
            var $limit = $this.find('select[name=limit]');
            var $optLimit = $limit.find('option:selected');
            var _current_page = $optPage.val();
            var _per_page = $optLimit.val();
            if (typeof _current_page === "number"
                || typeof _current_page === 'string' && _current_page.trim().match(/^[0-9]$/g)
            ) {
                _current_page = parseInt(_current_page);
            } else {
                _current_page = current_page;
            }
            if (typeof _per_page === "number"
                || typeof _per_page === 'string' && _per_page.trim().match(/^[0-9]$/g)
            ) {
                _per_page = typeof _current_page === "number" ? _per_page : parseInt(_per_page);
            } else {
                // _per_page = per_page;
            }

            _current_page = _current_page > total_page ? total_page : _current_page;
            _current_page = _current_page < 1 ? 1 : _current_page;
            _per_page = _per_page > total_data
                ? (<?= MYSQL_DEFAULT_DISPLAY_LIMIT;?> > _per_page ? _per_page : <?= MYSQL_DEFAULT_DISPLAY_LIMIT;?>)
                : _per_page;
            _per_page = _per_page < 1 ? 1 : _per_page;
            if (_per_page >= total_data) {
                _current_page = 1;
            }
            var key = 'classes_api:limit:' + _current_page + '|offset:'+_per_page;
            $('#overlay-result-class').remove();
            if (!storage_data[key]) {
                content_table.closest('.card-body').prepend(
                    '<div id="overlay-result-class"><div class="overlay-inner loading"><div class="lds-dual-ring"></div></div></div>'
                );
            }

            get_data(_current_page, _per_page, function (e) {
                var old_total_page = total_page;
                total_data = e.data.total;
                total_result = e.data.count;
                total_page = e.data.page.total;
                current_page = e.data.page.current;
                per_page = _per_page;
                var html = _.template(
                    templateOption
                )({
                    selected: function (e) {
                        // console.log(e);
                        return e === current_page ? ' selected' : '';
                    },
                    items: total_page+1
                });

                if (old_total_page !== total_page) {
                    $page.find('option').not(':first').remove();
                    $page.append(html);
                }

                content_table
                    .closest('table')
                    .find('[type=checkbox][data-action=check]')
                    .each(function (e) {
                        this.checked = false;
                    }).trigger('change');

                window.history.pushState(
                    {},
                    '',
                    window.Sto.url.add_query({
                        page: current_page,
                        limit: _per_page
                    }, href)
                );
            }, function () {
                $page.find('option').each(function () {
                    var attr = parseInt($(this).val());
                    if (attr === current_page) {
                        $(this).attr('selected', true);
                        return;
                    }
                    $(this).attr('selected', false);
                });

                $limit.find('option').each(function () {
                    var attr = parseInt($(this).val());
                    if (attr === _per_page) {
                        return;
                    }
                    $(this).attr('selected', true);
                });
            });
        });
    })
})(window.jQuery);
    </script>
<?php
get_admin_footer_template();
