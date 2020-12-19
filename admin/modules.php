<?php
require __DIR__ . '/init.php';

use ArrayIterator\Helper\Normalizer;

if (!admin_is_allowed(__FILE__)) {
    return load_admin_denied();
}

switch (query_param('status')) {
    case 'active':
        set_admin_title(trans('Active Modules'));
        break;
    case 'inactive':
        set_admin_title(trans('Inactive Modules'));
        break;
    default:
        set_admin_title(trans('All Modules'));
}

get_admin_header_template();

$moduleInputSearchName = 's';
$moduleInputStatusName = 'module_status';
$moduleStatusName = 'status';

$moduleStatus = query_param($moduleStatusName);
$moduleSearchStatus = query_param($moduleInputStatusName);
$moduleSearch = query_param($moduleInputSearchName);

$moduleStatus = !is_string($moduleStatus) ? '' : $moduleStatus;
$moduleSearchStatus = !is_string($moduleSearchStatus) ? '' : $moduleSearchStatus;
$moduleSearch = !is_string($moduleSearch) ? '' : $moduleSearch;

$moduleStatusesLists = (array) hook_apply('module_status_list', ['active', 'inactive']);
$url = get_current_url();
if ($moduleStatus) {
    $url = Normalizer::addQueryArgs(
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

?>
<form method="get" class="module-form mt-2" action="<?= esc_attr($url);?>">
    <div class="row">
        <div class="col-md-8">
            <?php foreach ($moduleStatusesLists as $item) :?>
                <label class="custom-label-radio">
                    <input name="<?= $moduleInputStatusName;?>" type="radio" value="<?php esc_attr_e($item);?>"<?= $chosenSearchStatus === $item ? ' checked':''?>>
                    <span><?php esc_attr_trans_e(sprintf('%s Modules', ucwords($item)));?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="module-form-search-input" class="sr-only">Search Module</label>
                <input class="form-control" id="module-form-search-input" type="search" name="<?=esc_attr($moduleInputSearchName);?>" value="<?= esc_attr($moduleSearch);?>" placeholder="<?php esc_attr_trans_e('Type To Search ...');?>">
            </div>
        </div>
    </div>
</form>
<?php
get_admin_footer_template();
