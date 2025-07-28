<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Meeting Management
Description: A module to manage meeting agendas, minutes of meetings, task assignments, and attendance tracking.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('MEETING_MANAGEMENT_MODULE_NAME', 'meeting_management');
define('MEETING_MANAGEMENT_MOM_ERROR', 'modules/meeting_management/uploads/import_items_mom_error/');
require_once __DIR__ . '/vendor/autoload.php';


$CI = &get_instance();

/**
 * Load the module helper file
 */
$CI->load->helper(MEETING_MANAGEMENT_MODULE_NAME . '/meeting_management');

/**
 * Register activation module hook
 */
register_activation_hook(MEETING_MANAGEMENT_MODULE_NAME, 'meeting_management_module_activation_hook');

function meeting_management_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files
 */
register_language_files(MEETING_MANAGEMENT_MODULE_NAME, [MEETING_MANAGEMENT_MODULE_NAME]);

/**
 * Add necessary menu items in the admin and client areas
 */
hooks()->add_action('admin_init', 'meeting_management_module_init_menu_items');
hooks()->add_action('clients_init', 'meeting_management_client_module_init_menu_items');

/**
 * Add permissions for staff
 */
hooks()->add_action('admin_init', 'meeting_management_register_user_permissions');

function meeting_management_register_user_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('meeting_permission_view'),
    ];

    register_staff_capabilities('meeting_management', $capabilities, _l('meeting_management_module'));
}

/**
 * Initialize module menu items in the admin area
 */
function meeting_management_module_init_menu_items()
{
    $CI = &get_instance();

    // Add the main "Meeting Management" menu item
    $CI->app_menu->add_sidebar_menu_item('meeting_management_menu', [
        'name'     => _l('meeting_management'), // Menu name from language file
        'collapse' => true,                     // Collapsible menu
        'position' => 10,                       // Menu position
        'icon'     => 'fa fa-calendar',         // Icon for the menu
    ]);

    // Add sub-menu items based on staff permissions and roles
    if (staff_can('view')) {
        $CI->app_menu->add_sidebar_children_item('meeting_management_menu', [
            'slug' => 'meeting-dashboard',
            'name' => _l('Dashboard'),
            'icon' => 'fa fa-home menu-icon',
            'href' => admin_url('meeting_management/dashboard/index'),
            'position' => 1,
        ]);
        // View Agendas
        $CI->app_menu->add_sidebar_children_item('meeting_management_menu', [
            'slug'     => 'view-agendas',
            'name'     => _l('meeting_agenda'), // Menu name from language file
            'icon' => 'fa fa-book',
            'href'     => admin_url('meeting_management/agendaController/index'), // Add module name in the URL
            'position' => 5,
        ]);

        // Create New Agenda
        $CI->app_menu->add_sidebar_children_item('meeting_management_menu', [
            'slug'     => 'create-agenda',
            'name'     => _l('meeting_create_agenda'), // Menu name from language file
            'icon' => 'fa fa-add',
            'href'     => admin_url('meeting_management/minutesController/convert_to_minutes'), // Add module name in the URL
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item('meeting_management_menu', [
            'slug'     => 'critical-agenda',
            'name'     => _l('meeting_critical_agenda'), // Menu name from language file
            'icon' => 'fa fa-exclamation-triangle',
            'href'     => admin_url('meeting_management/minutesController/critical_agenda'), // Add module name in the URL
            'position' => 5,
        ]);
    }
}

/**
 * Initialize module menu items in the client area
 */
function meeting_management_client_module_init_menu_items()
{
    if (is_client_logged_in()) {
        add_theme_menu_item('meeting_management-meeting', [
            'name'     => _l('meeting_minutes'),
            'href'     => site_url('meeting_management/clients/meeting_notes'),
            'position' => 4,
        ]);
    }
}
