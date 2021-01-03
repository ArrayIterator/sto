<?php

use ArrayIterator\Info\Module;

/**
 * @param Module|string $module
 * @return bool
 */
function module_is_global($module) : bool
{
    if (is_string($module)) {
        $module = get_module($module);
    }
    if (!$module instanceof Module) {
        return false;
    }

    $module = $module->getBaseModuleName();
    return isset(get_globals_active_modules()[$module]);
}

/**
 * Modules that activate as global
 *
 * @return array
 */
function get_globals_active_modules(): array
{
    $modules = get_option('globals_active_modules', null, 1);
    $update = false;
    if (!is_array($modules)) {
        $modules = [];
    }
    $newModules = [];
    foreach ($modules as $key => $time) {
        if (!is_string($key) || !is_int($time)) {
            $update = true;
            unset($modules[$key]);
            continue;
        }
        if (module_exist($key)) {
            $newModules[$key] = $time;
        }
    }

    if ($update) {
        update_option('globals_active_modules', $modules, 1);
    }

    asort($modules);
    asort($newModules);
    return hook_apply('globals_active_modules', $modules, $newModules, get_current_site_id());
}

/**
 * @param string|Module $module
 * @param bool $replaceTime
 * @return bool
 */
function set_globals_active_module($module, bool $replaceTime = false): bool
{
    $modules = get_globals_active_modules();
    if (is_string($module)) {
        $module = get_module($module);
    }
    if (!$module instanceof Module || ! $module->isSiteWide()) {
        return false;
    }
    $module = $module->getBaseModuleName();
    if (isset($modules[$module]) && !$replaceTime) {
        return true;
    }
    $modules[$module] = time();
    return update_option('globals_active_modules', $modules, 1);
}

/**
 * @return array
 */
function get_site_active_modules(): array
{
    $modules = get_site_option('active_modules');

    if (!is_array($modules)) {
        $modules = [];
    }

    $update = false;
    if (!is_array($modules)) {
        $modules = [];
    }
    $newModules = [];
    foreach ($modules as $key => $time) {
        if (!is_string($key) || !is_int($time)) {
            $update = true;
            unset($modules[$key]);
        }
        if (module_exist($key)) {
            $newModules[$key] = $time;
        }
    }
    $siteId = get_current_site_id();
    if ($update) {
        update_option('active_modules', $modules, $siteId);
    }
    asort($modules);
    asort($newModules);
    return hook_apply('site_active_modules', $modules, $newModules, $siteId);
}

/**
 * @return array
 */
function get_main_site_active_modules(): array
{
    $modules = get_site_option('active_modules', 1);

    if (!is_array($modules)) {
        $modules = [];
    }

    $update = false;
    if (!is_array($modules)) {
        $modules = [];
    }
    $newModules = [];
    foreach ($modules as $key => $time) {
        if (!is_string($key) || !is_int($time)) {
            $update = true;
            unset($modules[$key]);
        }
        if (module_exist($key)) {
            $newModules[$key] = $time;
        }
    }

    if ($update) {
        update_option('active_modules', $modules, 1);
    }
    asort($modules);
    return hook_apply('site_active_modules', $modules, $newModules, 1, get_current_site_id());
}

/**
 * @param string|Module $module
 * @param bool $replaceTime
 * @return bool
 */
function set_main_site_active_module($module, bool $replaceTime = false): bool
{
    $modules = get_main_site_active_modules();
    if (is_string($module)) {
        $module = get_module($module);
    }
    if (!$module instanceof Module) {
        return false;
    }
    $module = $module->getBaseModuleName();
    if (isset($modules[$module]) && !$replaceTime) {
        return true;
    }
    $modules[$module] = time();
    return update_option('active_modules', $modules, 1);
}

/**
 * @param string|Module $module
 * @param bool $replaceTime
 * @return bool
 */
function set_site_active_module($module, bool $replaceTime = false): bool
{
    if (is_string($module)) {
        $module = get_module($module);
    }
    if (!$module instanceof Module) {
        return false;
    }

    $module = $module->getBaseModuleName();
    $modules = get_site_active_modules();
    if (isset($modules[$module]) && !$replaceTime) {
        return true;
    }

    $modules[$module] = time();
    return update_option('active_modules', $modules, get_current_site_id());
}

/**
 * @param string|Module $module
 * @return bool
 */
function remove_main_wide_active_module($module): bool
{
    $modules = get_main_site_active_modules();
    if (is_string($module)) {
        $module = get_module($module);
    }
    if (!$module instanceof Module) {
        return false;
    }
    $module = $module->getBaseModuleName();
    if (!isset($modules[$module])) {
        return true;
    }

    unset($modules[$module]);
    return update_option('active_modules', $modules, 1);
}

/**
 * @param $module
 * @return bool
 */
function remove_global_active_module($module): bool
{
    $modules = get_globals_active_modules();
    if (is_string($module)) {
        $module = get_module($module);
    }
    if (!$module instanceof Module) {
        return false;
    }
    $module = $module->getBaseModuleName();
    if (!isset($modules[$module])) {
        return true;
    }

    unset($modules[$module]);
    return update_option('globals_active_modules', $modules, 1);
}

/**
 * @param string|Module $module
 * @return bool
 */
function remove_site_active_module($module): bool
{
    $modules = get_site_active_modules();
    if (is_string($module)) {
        $module = get_module($module);
    }
    if (!$module instanceof Module) {
        return false;
    }
    $module = $module->getBaseModuleName();
    if (!isset($modules[$module])) {
        return true;
    }
    if (!isset($modules[$module])) {
        return true;
    }
    unset($modules[$module]);
    return update_option('active_modules', $modules, get_current_site_id());
}

/**
 * @return array
 */
function get_all_active_modules_list(): array
{
    $modules = get_globals_active_modules();
    $siteModules = get_site_active_modules();
    $siteWideModules = get_main_site_active_modules();
    foreach ($siteModules as $key => $item) {
        if (!isset($modules[$key])) {
            $modules[$key] = $item;
        }
    }
    foreach ($siteWideModules as $key => $siteWideModule) {
        if (!isset($modules[$key])) {
            $modules[$key] = $siteWideModule;
        }
    }
    asort($modules);
    return $modules;
}

/**
 * @return Module[]
 */
function get_all_active_modules(): array
{
    $modules = [];
    foreach (get_globals_active_modules() as $item => $module) {
        $module = get_module($module);
        if (!$module || !$module->isValid()) {
            continue;
        }
        $modules[$item] = $module;
    }
    foreach (get_site_active_modules() as $item => $module) {
        if (isset($modules[$item])) {
            continue;
        }

        $module = get_module($module);
        if (!$module || !$module->isValid()) {
            continue;
        }
        $modules[$item] = $module;
    }

    ksort($modules);
    return $modules;
}
