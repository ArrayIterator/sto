<?php
require __DIR__ . '/init.php';

if (!admin_is_allowed(__FILE__)) {
    return load_admin_denied();
}

set_admin_title('All Classes');
$page = query_param_int(PARAM_PAGE);
$page = $page < 1 ? 1 : $page;

$perPage = query_param_int(PARAM_LIMIT);
$perPage = $perPage < 1 ? MYSQL_DEFAULT_DISPLAY_LIMIT : $perPage;
$perPage = $perPage > MYSQL_MAX_RESULT_LIMIT ? MYSQL_MAX_RESULT_LIMIT : $perPage;
$offset = ($page - 1) * $perPage;
$is_super_admin = is_super_admin();
$is_admin = $is_super_admin || is_admin();
$class_site_id = $is_super_admin ? query_param_int(PARAM_SITE_ID) : null;
$class_site_id = $class_site_id ?: null;
$searchData = trim(query_param_string(PARAM_SEARCH_QUERY, ''));
$filter = query_param_string(PARAM_TYPE, 'name');
$filter = $filter === 'code' ? $filter : 'name';
$is_search = ($searchData) !== '';
$result = !$is_search
    ? get_classes_data($perPage, $offset, $class_site_id)
    : (
         $filter === 'name'
         ? search_class_by_name($searchData, $class_site_id, $perPage, $offset)
         : search_class_by_code($searchData, $class_site_id, $perPage, $offset)
    );

$total_data = $result['total'];
$total_result = $result['count'];
$meta = $result['meta'];
$total_page = $meta['page']['total'];
$current_page = $meta['page']['current'] ?? $total_page;
$result = $result['results'];
$can_edit_class = current_supervisor_can('edit_class');
if ($total_data > 0 && $total_result === 0 && (!$meta['page']['current'] || $meta['page']['current'] > $total_page)) {
    if ($total_page !== $current_page) {
        redirect(add_query_args(['page' => $total_page], get_current_url()));
        do_exit();
    }
}

get_admin_header_template();

?>
    <style type="text/css">
        /*.row [class*=col-] .select2 {*/
        /*    width:100%!important;*/
        /*}*/
        table.table > tbody > tr > td.row-checkbox + td {
            width: 200px;
            table-layout: auto;
        }
        table.table > tbody > tr > td.row-checkbox + td + td {
            width: 300px;
            table-layout: auto;
        }
    </style>
    <form method="get" id="result-filter" action="<?= get_current_url(); ?>">
    </form>
    <div class="card card-list">
        <div class="card-header">
            <?php
                create_filter_search_form_select(
                    PARAM_TYPE,
                    'result-filter',
                    [PARAM_NAME => trans('Name'), PARAM_CODE => trans('Code')],
                    'input-result-filter-',
                    false,
                    PARAM_NAME
                );
            ?>
        </div>
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
                <?php if (empty($result)) { ?>
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="mt-2 mb-2"><?php esc_html_trans_e('DATA NOT FOUND');?></div>
                    </td>
                </tr>
                <?php } ?>
                <?php foreach ($result as $row) : $identifier = 'class-id-'.$row['id']; ?>
                    <tr id="<?= $identifier;?>" data-site-id="<?= $row['site_id']; ?>" data-id="<?= $row['id']; ?>">
                        <td class="row-checkbox">
                            <label for="table-class-id-<?= $row['id'] ?>" class="hide text-hide"></label>
                            <input type="checkbox" id="table-class-id-<?= $row['id'] ?>" data-source="class-id" value="<?= $row['id']; ?>">
                        </td>
                        <td class="cell-title">
                            <div class="row-title">
                                <a data-action="<?=
                                    $can_edit_class ? PARAM_EDIT : PARAM_PREVIEW;
                                ?>" data-link-id="<?= $row['id'];?>" href="<?= esc_attr(
                                    $can_edit_class
                                        ? add_query_args([PARAM_ACTION => PARAM_EDIT, PARAM_ID => $row['id']], get_admin_url('class-new.php'))
                                        : "#{$identifier}"
                                );?>"<?php if (!$can_edit_class) { ?> data-action="preview" data-title="<?php trans_e('Code');?> <?php esc_attr_e($row['code']);?>" data-modal="true" data-cache="true" data-api="/classes/id/<?= $row['id'];?>" data-template-id="underscore_template_class_preview"<?php } ?>>
                                    <?= esc_html($row[PARAM_CODE]); ?>
                                </a>
                            </div>
                            <div class="row-action">
                                <?php if ($can_edit_class) : ?>
                                <a data-link-id="<?= $row['id'];?>" href="<?=
                                    esc_attr(
                                        add_query_args(
                                            [PARAM_ACTION => PARAM_EDIT, PARAM_ID => $row['id']],
                                            get_admin_url('class-new.php')
                                        )
                                    ) ?>"><?php trans_e('Edit');?></a>
                                <span class="row-sep">|</span>
                                <?php endif; ?>
                                <a data-action="preview" data-link-id="<?= $row['id'];?>" href="#<?= $identifier;?>"  data-title="<?php trans_e('Code');?> <?php esc_attr_e($row['code']);?>" data-modal="true" data-cache="true" data-api="/classes/id/<?= $row['id'];?>" data-template-id="underscore_template_class_preview"><?= esc_html_trans('Preview'); ?></a>
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
            <?php create_filter_paging_form('result-filter', $current_page, $total_page, $perPage); ?>
        </div>
    </div>

    <script type="text/template" data-template="list">
    <% _.each(items, function(item, key, arr) { %>
        <?php
            // data-teachers="<%= JSON.stringify(item.teachers) %>
        ?>
        <tr data-site-id="<%= item.site_id %>">
            <td class="row-checkbox">
                <label for="table-class-id-<%= item.id %>" class="hide text-hide"></label>
                <input type="checkbox" id="table-class-id-<%= item.id %>" data-source="class-id" value="<%= item.id %>">
            </td>
            <td class="cell-title">
                <div class="row-title">
                    <a data-action="<?= $can_edit_class ? PARAM_EDIT : PARAM_PREVIEW;?>" data-link-id="<%= item.id %>" href="<?=
                    $can_edit_class ?
                        esc_attr(
                        add_query_args(
                            [
                                PARAM_ACTION => PARAM_EDIT,
                                PARAM_ID => false
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
                                    PARAM_ACTION => PARAM_EDIT,
                                    PARAM_ID => false
                                ],
                                get_admin_url('class-new.php')
                            )
                        ). '&id=<%= item.id %>'
                        ;?>"><?php trans_e('Edit');?></a>
                        <span class="row-sep">|</span>
                    <?php } ?>
                    <a data-action="preview" data-link-id="<%= item.id %>" href="#class-id-<%= item.id %>" data-title="<?php trans_e('Code');?> <%= item.code %>" data-method="GET" data-params="{}" data-modal="true" data-cache="true" data-api="/classes/id/<%= item.id %>" data-template-id="underscore_template_class_preview"><?= esc_html_trans('Preview'); ?></a>
                </div>
                <div class="row-content"></div>
            </td>
            <td><%= item.name %></td>
            <td class="hidden d-md-table-cell"><%= strip(item.note, 0, 80, '...') %></td>
        </tr>
    <% }); %>
    </script>
    <script type="text/template" data-template="option">
        <% _.range(1, items).map(function (item) {%>
        <option value="<%= item %>"<%= selected(item) %>><%= item %></option>
        <% }); %>
    </script>
    <script type="text/template" data-template="empty">
        <tr>
            <td colspan="4" class="text-center">
                <div class="mt-2 mb-2"><?php esc_html_trans_e('DATA NOT FOUND');?></div>
            </td>
        </tr>
    </script>
    <script type="text/template" id="underscore_template_class_preview" data-template="preview">
        <%
            if (typeof data === "object" && data.length && (data[0] && typeof data[0] === 'object')) {
                var newData = data[0];
        %>
            <table class="table table-striped font-weight-lighter">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="2" class="font-weight-lighter">
                            <?php esc_html_trans_e('Class Detail');?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th><?php esc_html_trans_e('Code');?></th>
                        <td><%= newData.code %></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_trans_e('Name');?></th>
                        <td><%= newData.name %></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_trans_e('Status');?></th>
                        <td><%= newData.status %></td>
                    </tr>
                <?php if ($is_super_admin) { ?>
                    <tr>
                        <th><?php esc_html_trans_e('Site Id');?></th>
                        <td><%= newData.site_id %></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <table class="table <%= newData.created_by ? 'table-striped': '' %> ">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="2" class="font-weight-lighter"><?php esc_html_trans_e('Teachers');?></th>
                    </tr>
                    <% if (newData.teachers.length) { %>
                        <tr>
                            <th class="bg-light text-dark font-weight-bolder"><?php esc_html_trans_e('Name');?></th>
                            <th class="bg-light text-dark font-weight-bolder"><?php esc_html_trans_e('Teach Year');?></th>
                        </tr>
                    <% } %>
                </thead>
                <tbody>
                <% if (newData.teachers.length) { %>
                    <% _.each(newData.teachers, function(item, key, arr) { %>
                        <tr>
                            <td><%= item.name %></td>
                            <td><%= item.teach_year || '' %></td>
                        </tr>
                    <% }); %>
                <% } %>
                </tbody>
            </table>
            <% if (!newData.teachers.length) { %>
                <div class="alert alert-info"><?php esc_html_trans_e('Class does not assigned teachers yet.');?></div>
            <% } %>
            <div class="note">
                <table class="table">
                    <thead class="thead-dark">
                    <tr><th class="font-weight-lighter"><?php esc_html_trans_e('Note'); ?></th></tr>
                    </thead>
                    <tbody>
                    <% if (newData.note && newData.note.trim() !== '') { %>
                        <tr>
                            <td>
                                <div class="text-wrap small text-monospace"><%= newData.note %></div>
                            </td>
                        </tr>
                    <% } %>
                    </tbody>
                </table>
                <% if (!newData.note || newData.note.trim() === '') { %>
                    <div class="alert alert-info"><?php esc_html_trans_e('Class does not assigned note yet.');?></div>
                <% } %>
            </div>
        <?php if ($is_admin) { ?>
            <table class="table font-weight-lighter <%= newData.created_by ? 'table-striped': '' %>">
                <thead class="thead-dark">
                <tr>
                    <th colspan="2" class="font-weight-lighter">
                        <?php esc_html_trans_e('Created By');?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <% if (newData.created_by) { %>
                    <tr>
                        <th><?php esc_html_trans_e('Username');?></th>
                        <td><%= newData.creator_username %></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_trans_e('Id');?></th>
                        <td><%= newData.created_by %></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_trans_e('Name');?></th>
                        <td><%= newData.creator_full_name %></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_trans_e('Site Id');?></th>
                        <td><%= newData.creator_site_id %></td>
                    </tr>
                <% } %>
                </tbody>
            </table>
            <% if (!newData.created_by) { %>
                <div class="alert alert-info"><?php esc_html_trans_e('Creator of class is unknown');?></div>
            <% } %>
        <?php } ?>
        <% } else { %>
            <div class="alert alert-info text-center"><?php esc_html_trans_e('Data not found');?></div>
        <% } %>
    </script>
    <script type="text/javascript">
;(function ($) {
    if (!$) return;

    var data = {
        pp: <?= $perPage;?>,
        tp: <?= $total_page;?>,
        td: <?= $total_data;?>,
        tr: <?= $total_result;?>,
        cp: <?= $page;?>,
        dl: <?= MYSQL_DEFAULT_DISPLAY_LIMIT;?>,
        hr: window.location.href,
        ip: null,
        sl: 0,
        sd: {}
    };
    $(document).ready(function () {
        var key,
            $table = $('[data-content=table]'),
            template = $('script[data-template=list]').html(),
            opt = $('script[data-template=option]').html(),
            empty = $('script[data-template=empty]').html(),
            asIntDefault = function (val, defaultData) {
                if (typeof val === "number"
                    || typeof val === 'string' && val.trim().match(/^[0-9]$/g)
                ) {
                    val = typeof val === "number" ? val : parseInt(val);
                } else {
                    val = defaultData;
                }

                return val;
            },
            asOneOrMore = function (val) {
                return val < 1 ? 1 : val
            },
            strip = function (str, start, end, add) {
                str = $('<div>' + str + '</div>').text();
                var _str = str.replace(/([\s])+/, '$1').trim().substr(start, end);
                    _str += _str !== str && add && typeof add === 'string' ? add : '';
                return _str;
            };
        $(window).bind('beforeunload', function () { data.sd = {}});
        if (!$table.length || !template || !opt || !empty) {
            return;
        }
        var inLoopSubmit = false;
        $('form#result-filter')
            .on('submit', function (e) {
                e.preventDefault();
                if (data.ip) {
                    if (typeof data.ip.abort === "function") {
                        data.ip.abort();
                    }
                    data.ip = null;
                }
                var c = 0, i,
                    $this = $(this),
                    $cBody = $table.closest('.card-body'),
                    $serialize = $.parse_url($this.serialize()),
                    $overlay = $('#overlay-result-class'),
                    offset = asIntDefault($serialize['page'], data.cp),
                    limit = asIntDefault($serialize['limit'], data.pp),
                    fallback = function (e) {
                        var $selectPage = $('select[name=page]');
                        if (!inLoopSubmit && e.data.total > 0 && offset >= e.data.total) {
                            var $cY = $selectPage.find('option[value=1]');
                            if ($cY.length) {
                                $selectPage.find('option').not($cY).attr('selected', false);
                                $cY.attr('selected', true);
                                inLoopSubmit = true;
                                $this.trigger('submit');
                                return;
                            }
                        }

                        inLoopSubmit = false;
                        data.sd[key] = e;
                        data.td = e.data.total;
                        data.tr = e.data.count;
                        data.tp = e.data.page.total;
                        data.cp = e.data.page.current;
                        data.sl = JSON.stringify(data.sd).length;
                        $table.html(
                            _.template(
                                data.tr < 1 ? empty : template
                            )({strip, items:e.data.results})
                        );
                        $selectPage.find('option:not([disabled])').remove();
                        $selectPage.find('option[disabled]').attr('selected', true);
                        $selectPage.append(
                            _.template(opt)({
                                selected: function (e) {return e === data.cp ? ' selected' : ''},
                                items: data.tp+1
                            })
                        );
                        $('[type=checkbox][data-action=check]')
                            .each(function () {this.checked = false})
                            .trigger('change');
                        window.history.pushState({}, '', window.Sto.url.add_query($serialize, data.hr));
                    };

                key = window.Sto.hash.sha1(JSON.stringify($serialize));
                offset = asOneOrMore(offset > data.tp ? data.tp : offset);
                offset = offset >= data.td ? 0 : offset;
                offset = (offset * limit) - limit;
                offset = offset > data.td || offset < 0 ? 0 : offset;
                limit = asOneOrMore(
                    limit > data.td
                        ? (data.dl > limit ? limit : data.dl)
                        : limit
                );

                // freed 10737418240 = 10MB
                if (data.sl >= 10737418240) {
                    // reduce 32 keys
                    for (i in data.sd) {
                        if (c++ >= 32) {
                            break;
                        }
                        delete data.sd[i];
                    }
                }

                $overlay.remove();
                $overlay = $('<div id="overlay-result-class"><div class="overlay-inner loading"><div class="lds-dual-ring"></div></div></div>');
                $cBody.prepend($overlay);
                $serialize.limit = limit;
                $serialize.offset = offset;

                if (data.sd[key]) {
                    $overlay.fadeOut();
                    fallback(data.sd[key]);
                    return;
                }

                data.ip = $.get(
                    <?= json_ns(get_api_url('/classes/'));?>,
                    $serialize,
                    fallback
                ).fail(function () {
                    $('select[name=page] option')
                        .each(function () {
                            $(this)
                                .attr('selected', parseInt($(this).val()) === data.cp)
                        });
                    $('select[name=limit] option')
                        .each(function () {
                            $(this)
                                .attr('selected', parseInt($(this).val()) === limit)
                        })
                }).always(function () {
                    data.ip = null;
                    $overlay.fadeOut();
                });
            });
    });
})(window.jQuery);
    </script>
<?php
get_admin_footer_template();
