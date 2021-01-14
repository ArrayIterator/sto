<?php
/**
 * @param string $name
 * @param string $formTarget
 * @param array $radios
 * @param string $prefixId
 */
function create_filter_search_form_radio(
    string $name,
    string $formTarget,
    array $radios = [],
    string $prefixId = 'input-result-filter-'
) {
    $formTarget = esc_attr($formTarget);
    $value      = query_param_string($name);
    $name       = esc_attr($name);
    $searchData = trim(query_param_string(PARAM_SEARCH_QUERY, ''));
    $searchId   = sprintf('%s%s', $prefixId, PARAM_SEARCH_QUERY);
?>
    <div class="row" data-filter-form="<?=$formTarget;?>">
        <div class="col-lg-6 col-md-4">
            <div class="input-group mt-2 mb-2">
                <div class="text-muted"><?php esc_html_trans_e('Search By:');?></div>
<?php
    $items = [];
    foreach ($radios as $item => $text) {
        if (!is_string($item)) {
            continue;
        }
        $origin = normalize_html_class($item);
        $keyId = $origin;
        $c = 1;
        while (isset($items[$keyId])) {
            $keyId = $origin .'-' . $c++;
        }
        $items[$keyId] = true;
        $id = esc_attr(sprintf('%s%s', $prefixId, $keyId));
        $named = is_string($text) ? esc_html($text) : esc_html(ucwords($item));
?>
                <div class="custom-control custom-radio custom-control-inline">
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" form="<?= $formTarget; ?>" id="<?= $id;?>" type="radio" name="<?= $name;?>" value="<?= $item;?>"<?= $value === $item ? ' checked':'';?>>
                        <label class="custom-control-label" for="<?= $id;?>"><?= $named;?></label>
                    </div>
                </div>
<?php } ?>
            </div>
        </div>
        <div class="col-lg-6 col-md-8">
            <label class="text-hide sr-only" for="<?= $searchId;?>>"></label>
            <div class="input-group">
                <input form="<?= $formTarget;?>" id="<?= $searchId;?>" type="search" class="form-control" name="<?= PARAM_SEARCH_QUERY;?>" value="<?= esc_attr($searchData);?>" placeholder="<?php esc_attr_trans_e('Type Keyword');?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit" form="<?= $formTarget;?>" title="<?php esc_attr_trans_e('Search'); ?>">
                        <i class="icofont icofont-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php
}

/**
 * @param string $name
 * @param string $formTarget
 * @param array $options
 * @param string $prefixId
 * @param bool $onChangeSubmit
 * @param string|null $selectedDefault
 */
function create_filter_search_form_select(
    string $name,
    string $formTarget,
    array $options = [],
    string $prefixId = 'input-result-filter-',
    bool $onChangeSubmit = true,
    string $selectedDefault = null
) {
    $formTarget = esc_attr($formTarget);
    $value      = query_param_string($name);
    $class_site_id = query_param_int(PARAM_SITE_ID);
    $name       = esc_attr($name);
    $searchData = query_param_string(PARAM_SEARCH_QUERY, '', true);
    $searchId   = sprintf('%s%s', $prefixId, PARAM_SEARCH_QUERY);
    $id = esc_attr(sprintf('%s%s', $prefixId, $name));
    $is_super_admin = is_super_admin();
    $all_sites = [];
    if ($is_super_admin) {
        $all_sites = get_all_sites();
    }
?>
    <div class="row" data-filter-form="<?=$formTarget;?>">
        <div class="col-lg-6 col-md-4">
            <?php
            if ($is_super_admin) {
                $selected = !$class_site_id || !isset($all_sites[$class_site_id]) ? ' selected': '';
            ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label sr-only" for="site_id_<?= $id;?>"></label>
                            <select form="<?= $formTarget; ?>" id="site_id_<?= $id;?>" class="custom-select" data-select="select2"
                                data-tag="true" data-options="{tags: true}" name="site_id"
                                data-info-text="<?= esc_attr_trans('Host:'); ?>" data-change="true"
                                data-target=".target-host"
                                data-template="<div class='message-inner'><span class='message-name'><%= data.infoText %></span><span class='message-value'><code><%= data.host %></code></span></div>">
                            <option value=""<?=$selected;?>
                                    data-host="<?php esc_attr_trans_e('All Sites');?>"
                                    data-site-id="&radic;"
                                    data-name="<?php esc_attr_trans_e('All Sites');?>"
                                    data-template="<span class='site-id-sep'><i class='icofont-check'></i></span><span class='site-name-sep'><%= data.name || ''%></span>">
                                    [ &radic; ] <?php esc_html_trans_e('All Site'); ?>
                            </option>
                            <?php
                            $selectedHost = '';
                            foreach ($all_sites as $siteId => $site) {
                                unset($all_sites[$siteId]);
                                $selected = $class_site_id === $siteId ? ' selected' : '';
                                if ($class_site_id === $siteId) {
                                    $selectedHost = $site->get('host');
                                }
                                ?>
                                <option value="<?= $siteId; ?>"<?= $selected; ?>
                                        data-host="<?= $site->get('host'); ?>"
                                        data-site-id="<?= $site->getSiteId(); ?>"
                                        data-name="<?= esc_attr($site->get(PARAM_NAME)); ?>"
                                        data-template="<span class='site-id-sep'><%= data['site-id'] || '' %></span><span class='site-name-sep'><%= data.name || ''%></span>">
                                    [ <?= $site->getSiteId(); ?> ] <?php esc_html_e($site->get(PARAM_NAME) ?? ''); ?>
                                </option>
                            <?php } ?>
                        </select>
                        </div>
                    </div>
                    <div class="col-md-6">
            <?php } ?>
            <div class="form-group">
                <label class="col-form-label sr-only" for="<?= $id;?>"></label>
                <select form="<?= $formTarget; ?>" id="<?= $id;?>" name="<?= $name;?>" class="form-inline custom-select" data-change-submit="<?= $onChangeSubmit ? 'true' : 'false';?>" data-select="select2">
                    <option disabled selected><?php esc_html_trans_e('Search By:');?></option>
<?php
    if($selectedDefault && !isset($options[$name])) {
        $value = $selectedDefault;
    }

    foreach ($options as $item => $text) {
        if (!is_string($item)) {
            continue;
        }

        $origin = normalize_html_class($item);
        $named = is_string($text) ? esc_html($text) : esc_html(ucwords($item));
        $selected = $value === $item ? ' selected' : '';
?>
        <option value="<?= $item; ?>"<?= $selected; ?>><?= $named; ?></option>
<?php } ?>
                </select>
            </div>
            <?php if ($is_super_admin) { ?></div></div><?php } ?>
        </div>
        <div class="col-lg-6 col-md-8">
            <label class="text-hide sr-only" for="<?= $searchId;?>>"></label>
            <div class="input-group">
                <label class="col-form-label sr-only" for="<?= $searchId;?>"></label>
                <input form="<?= $formTarget;?>" id="<?= $searchId;?>" type="search" class="form-control" name="<?= PARAM_SEARCH_QUERY;?>" value="<?= esc_attr($searchData);?>" placeholder="<?php esc_attr_trans_e('Type Keyword & Enter...');?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit" form="<?= $formTarget;?>" title="<?php esc_attr_trans_e('Search'); ?>">
                        <i class="icofont icofont-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php
}

/**
 * @param string $formTarget
 * @param int $current_page
 * @param int $total_page
 * @param int $perPage
 * @param string $prefixId
 * @param bool $onChangeSubmit
 */
function create_filter_paging_form(
    string $formTarget,
    int $current_page,
    int $total_page,
    int $perPage = 20,
    string $prefixId = 'input-result-filter-',
    bool $onChangeSubmit = true
) {
    $formTarget = esc_attr($formTarget);
    $perPageId   = esc_attr(sprintf('%s%s', $prefixId, '-per-page'));
    $resultId   = esc_attr(sprintf('%s%s', $prefixId, '-paged'));
?>
    <div class="row" data-filter-form="<?=$formTarget;?>">
        <div class="col-md-4 col-sm-6 mt-2 mb-2">
            <label for="<?= $perPageId;?>" class="col-form-label sr-only"><?php esc_html_trans_e('Result per page'); ?></label>
            <select form="<?= $formTarget;?>" id="<?= $perPageId;?>" class="custom-select custom-select-sm" name="limit" data-change-submit="true" data-select="select2" data-placeholder="<?php esc_attr_trans_e('Result per page'); ?>">
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
        </div>
        <div class="offset-md-4 col-md-4 col-sm-6 mt-2 mb-2">
            <div class="form-group">
                <label for="<?= $resultId;?>" class="sr-only col-form-label"><small><?php esc_html_trans_e('Current Page'); ?></small></label>
                <select form="<?= $formTarget;?>" name="page" id="<?= $resultId;?>" class="form-control custom-select-sm" data-change-submit="<?= $onChangeSubmit ? 'true' : 'false';?>" data-select="select2">
                    <option disabled selected><?php esc_html_trans_e('Select Page'); ?></option>
                    <?php
                    $range = [];
                    if ($total_page > 0) {
                        $range = range(1, $total_page);
                    }
                    foreach ($range as $item) {
                        $selected = $item === $current_page ? ' selected' : '';
                        ?>
                        <option value="<?= $item;?>"<?= $selected;?>><?= $item;?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
<?php
}
