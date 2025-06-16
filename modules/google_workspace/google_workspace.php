<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Google Worksuite Integration
Module URI: https://codecanyon.net/item/google-sheets-module-for-perfex-crm-twoway-spreadsheets-synchronization/53297436
Description: Two-way Spreadsheets and Documents Synchronization between Perfex and Google Docs/Sheets
Version: 1.3.0
Requires at least: 1.0.*
Author: Themesic Interactive
Author URI: https://1.envato.market/themesic
*/

define('GOOGLE_WORKSPACE_MODULE_NAME', 'google_workspace');
define('GOOGLE_WORKSPACE_MODULE', 'google_workspace');

$CI = &get_instance();

require_once __DIR__.'/vendor/autoload.php';

/**
 * Load the module helper
 */
$CI->load->helper(GOOGLE_WORKSPACE_MODULE_NAME . '/google_workspace');



/**
 * Register activation module hook
 */
register_activation_hook(GOOGLE_WORKSPACE_MODULE_NAME, 'google_workspace_activation_hook');

function google_workspace_activation_hook()
{
    $CI = &get_instance();

    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(GOOGLE_WORKSPACE_MODULE_NAME, [GOOGLE_WORKSPACE_MODULE_NAME]);

/**
 * Actions for inject the custom styles
 */
hooks()->add_action('admin_init', 'google_workspace_init_menu_items');

hooks()->add_action('admin_init', 'google_workspace_permissions');

/**
 * Init theme style module menu items in setup in admin_init hook
 * @return null
 */
function google_workspace_init_menu_items()
{
    if (staff_can('setting', 'google_workspace') || staff_can('view', 'google_workspace') || staff_can('create', 'google_workspace') || staff_can('edit', 'google_workspace') || staff_can('delete', 'google_workspace')) {
        $CI = &get_instance();

        /**
         * If the logged in user is administrator, add custom menu in Setup
         */
        $CI->app_menu->add_sidebar_menu_item('google-drive', [
            'name'     => _l('google_workspace'),
            'icon'     => 'fa-solid fa-sheet-plastic',
            'collapse' => true,
            'position' => 65,
        ]);

        if (staff_can('setting', 'google_workspace')) {
            $CI->app_menu->add_sidebar_children_item('google-drive', [
                'slug'     => 'google-drive-settings',
                'name'     => _l('google_workspace_settings'),
                'href'     => admin_url('google_workspace/settings'),
                'position' => 10,
                'badge'    => [],
            ]);
        }

        $CI->app_menu->add_sidebar_children_item('google-drive', [
            'slug'     => 'google-drive-google-docs',
            'name'     => _l('google_workspace_google_docs'),
            'href'     => admin_url('google_workspace/docs'),
            'position' => 20,
            'badge'    => [],
        ]);

        $CI->app_menu->add_sidebar_children_item('google-drive', [
            'slug'     => 'google-drive-google-spreadsheets',
            'name'     => _l('google_workspace_google_sheets'),
            'href'     => admin_url('google_workspace/sheets'),
            'position' => 30,
            'badge'    => [],
        ]);

        $CI->app_menu->add_sidebar_children_item('google-drive', [
            'slug'     => 'google-drive-google-slides',
            'name'     => _l('google_workspace_google_slides'),
            'href'     => admin_url('google_workspace/slides'),
            'position' => 40,
            'badge'    => [],
        ]);

        $CI->app_menu->add_sidebar_children_item('google-drive', [
            'slug'     => 'google-drive-google-forms',
            'name'     => _l('google_workspace_google_forms'),
            'href'     => admin_url('google_workspace/forms'),
            'position' => 50,
            'badge'    => [],
        ]);

        $CI->app_menu->add_sidebar_children_item('google-drive', [
            'slug'     => 'google-drive-google-drives',
            'name'     => _l('google_workspace_google_drive'),
            'href'     => admin_url('google_workspace/drives'),
            'position' => 60,
            'badge'    => [],
        ]);
    }
}

hooks()->add_action('app_init', GOOGLE_WORKSPACE_MODULE . '_actLib');
function google_workspace_actLib()
{
    
}

hooks()->add_action('pre_activate_module', GOOGLE_WORKSPACE_MODULE . '_sidecheck');
function google_workspace_sidecheck($module_name)
{

}

hooks()->add_action('pre_deactivate_module', GOOGLE_WORKSPACE_MODULE . '_deregister');
function google_workspace_deregister($module_name)
{
    if (GOOGLE_WORKSPACE_MODULE == $module_name['system_name']) {

    }
}

function google_workspace_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'setting'   => _l('google_workspace_permission_settings'),
        'view'      => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create'    => _l('permission_create'),
        'edit'      => _l('permission_edit'),
        'delete'    => _l('permission_delete'),
    ];

    register_staff_capabilities('google_workspace', $capabilities, _l('google_workspace'));
}
