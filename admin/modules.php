<?php
require __DIR__ . '/init.php';

if (!admin_is_allowed(__FILE__)) {
    return load_admin_denied();
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
$moduleStatusName = 'status';

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
    if (!is_super_admin() && ! $module->isSiteWide()) {
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
                        <label class="custom-label-radio">
                            <input name="<?= $moduleInputStatusName; ?>" type="radio" class="module-status-filter" value="<?php esc_attr_e($item); ?>"<?= $chosenSearchStatus === $item ? ' checked' : '' ?>>
                            <span><?php esc_attr_trans_e(sprintf('%s Modules', ucwords($item))); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="module-form-search-input" class="sr-only">Search Module</label>
                        <input class="form-control" id="module-form-search-input" type="search" name="<?= esc_attr(PARAM_SEARCH_QUERY); ?>" value="<?= esc_attr($moduleSearch); ?>" placeholder="<?php esc_attr_trans_e('Type To Search ...'); ?>">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-area standard-card">
        <div class="card-columns">
<?php
    $regex_1 = '#'.preg_quote($moduleSearch, '#').'#i';
    $regex_2 = '#'.preg_quote(preg_replace('#\s+#', '', $moduleSearch), '#').'#i';
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

            <div class="<?= $class;?>" id="<?= $item['id'];?>" data-status="<?= $item['active'] ? 'active' : 'inactive';?>">
                <div class="card-image"<?= $bg;?>>
                    <?= !empty($item['description']) ? sprintf('<div class="card-description">%s</div>', esc_html($item['description'])) : '';?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= esc_attr($item['name']);?></h5>
                    <div class="module-info">
                        <div class="float-left text-muted">
                            <small>
                                <?php esc_html_trans_e('Version');?> : <?= $item['version'];?>
                            </small>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="badge badge-<?= $item['active'] ? 'primary' : 'secondary';?>">
                        <?= $item['active'] ? trans('Active') : trans('Inactive');?>
                    </span>
                </div>
            </div>
<?php
    endforeach;
    // for compat display
//    if (($count % 3) === 1) {
//        echo '<div class="card" style="opacity: 0"><div class="card-image"></div><div class="card-body"></div><div class="card-footer"></div></div>';
//    }
?>
        </div>
    </div>
    <script type="text/javascript">
        (function ($) {
            if (!$) {
                return;
            }
            var $chosen_status = <?= json_ns($chosenSearchStatus);?>;
            var $card_area = $('.card-area');
            var $form = $('form.module-form');
            var $formSearch = $form.find(' #module-form-search-input');
            if ($chosen_status !== 'active' && $chosen_status !== 'inactive') {
                $chosen_status = 'all';
            }
            $('.module-form input[type=radio].module-status-filter')
                .on('change', function (e) {
                var must_change = false;
                switch (this.value) {
                    case 'active':
                    case 'inactive':
                        must_change = true;
                        $chosen_status = this.value;
                        break;
                    default:
                        must_change = 'all';
                        $chosen_status = 'all';
                }
                $formSearch.trigger('keyup');
            });

            $formSearch.on('keyup', function (e) {
                // e.preventDefault();
                var val = this.value ? this.value.trim() : '';
                var $card;
                if (val === '') {
                    if ($chosen_status === 'all') {
                        $card_area.find('.card').removeClass('hide');
                        return;
                    }
                    $card_area.find('.card').not('[data-status=' + $chosen_status + ']').addClass('hide');
                    $card_area.find('.card[data-status=' + $chosen_status + ']').removeClass('hide');
                    return;
                }

                val = val.replace(/\s+/, ' ');
                $card = $chosen_status !== 'all'
                    ? $card_area.find('.card[data-status=' + $chosen_status + ']')
                    : $card_area.find('.card');
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
                        return;
                    }
                    $(this).addClass('hide');
                });
            });
            $form.on('submit', function (e) {
                // e.preventDefault();
            });
            $(document).ready(function () {
                if ($formSearch.val().toString().trim() !== '') {
                    // $formSearch.trigger('keyup');
                }
            });
        })(window.jQuery);
    </script>
<?php
get_admin_footer_template();
