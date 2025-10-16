<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Changee
Description: Changee Management Module is a tool for managing your day-to-day changees. It is packed with all necessary features that are needed by any business, which has to buy raw material for manufacturing or finished good changees for trading
Version: 1.5.0
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

define('CHANGEE_MODULE_NAME', 'changee');
define('CHANGEE_MODULE_UPLOAD_FOLDER', module_dir_path(CHANGEE_MODULE_NAME, 'uploads'));
define('CHANGEE_ORDER_RETURN_MODULE_UPLOAD_FOLDER', module_dir_path(CHANGEE_MODULE_NAME, 'uploads/order_return/'));

hooks()->add_action('admin_init', 'changee_permissions');
hooks()->add_action('app_admin_footer', 'changee_head_components');
hooks()->add_action('app_admin_footer', 'changee_add_footer_components');
hooks()->add_action('app_admin_head', 'changee_add_head_components');
hooks()->add_action('admin_init', 'changee_module_init_menu_items');
hooks()->add_action('before_expense_form_namee','changee_init_vendor_option');
hooks()->add_action('after_custom_fields_select_options','changee_init_vendor_customfield');
hooks()->add_action('after_custom_fields_select_options','changee_init_po_customfield');
hooks()->add_action('after_custom_fields_select_options','changee_init_vendor_contacts_customfield');
hooks()->add_action('after_customer_admins_tab', 'changee_init_tab_pur_order');
hooks()->add_action('after_custom_profile_tab_content', 'changee_init_content_pur_order');

//PO task
hooks()->add_action('task_related_to_select', 'changee_po_related_to_select'); // old
//hooks()->add_filter('before_return_relation_values', 'changee_po_relation_values', 10, 2); // old
hooks()->add_filter('before_return_relation_data', 'changee_po_relation_data', 10, 4); // old
hooks()->add_action('task_modal_rel_type_select', 'changee_po_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'changee_po_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'changee_po_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'changee_po_add_table_row', 10, 3);

//Changee quotation task
hooks()->add_action('task_related_to_select', 'changee_pq_related_to_select'); // old
//hooks()->add_filter('before_return_relation_values', 'changee_pq_relation_values', 10, 2); // old
hooks()->add_filter('before_return_relation_data', 'changee_pq_relation_data', 10, 4); // old
hooks()->add_action('task_modal_rel_type_select', 'changee_pq_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'changee_pq_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'changee_pq_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'changee_pq_add_table_row', 10, 3);

//Changee contract task
hooks()->add_action('task_related_to_select', 'changee_pc_related_to_select'); // old
//hooks()->add_filter('before_return_relation_values', 'changee_pc_relation_values', 10, 2); // old
hooks()->add_filter('before_return_relation_data', 'changee_pc_relation_data', 10, 4); // old
hooks()->add_action('task_modal_rel_type_select', 'changee_pc_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'changee_pc_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'changee_pc_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'changee_pc_add_table_row', 10, 3);

//Changee invoice task
hooks()->add_action('task_related_to_select', 'changee_pi_related_to_select'); // old
//hooks()->add_filter('before_return_relation_values', 'changee_pi_relation_values', 10, 2); // old
hooks()->add_filter('before_return_relation_data', 'changee_pi_relation_data', 10, 4); // old
hooks()->add_action('task_modal_rel_type_select', 'changee_pi_task_modal_rel_type_select'); // new
hooks()->add_filter('relation_values', 'changee_pi_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'changee_pi_get_relation_data', 10, 4); // new
hooks()->add_filter('tasks_table_row_data', 'changee_pi_add_table_row', 10, 3);

//debit note relation value
hooks()->add_filter('relation_values', 'changee_debit_note_get_relation_values', 10, 2); // new
hooks()->add_filter('get_relation_data', 'changee_debit_note_relation_data', 10, 4); // new

//cronjob auto reset changee order/request number
hooks()->add_action('after_cron_run', 'changee_reset_pur_order_number');
hooks()->add_action('after_cron_run', 'changee_reset_co_request_number');

//cronjob recurring changee invoice
hooks()->add_action('after_cron_run', 'recurring_changee_invoice');

//get currency
hooks()->add_action('after_cron_run', 'changee_pur_cronjob_currency_rates');

// Changee dashboard widget
// hooks()->add_filter('get_dashboard_widgets', 'changee_add_dashboard_widget');
hooks()->add_action('app_admin_footer', 'changee_load_js');

//Filter sale upload path debit note
hooks()->add_filter('get_upload_path_by_type', 'changee_debit_note_upload_file_path', 10, 2);

// Changee invoice customfield
hooks()->add_action('after_custom_fields_select_options', 'changee_init_invoice_customfield');

// Reload language for vendor portal
hooks()->add_action('after_load_admin_language', 'changee_reload_language');

//Project hook
hooks()->add_filter('project_tabs', 'changee_init_po_project_tabs');

// Mail template language
hooks()->add_filter('email_template_language', 'changee_update_email_lang_for_vendor', 10, 2);

// Changee load theme style
hooks()->add_filter('get_styling_areas', 'changee_before_load_theme_style');

//Expense table vendor data
hooks()->add_filter('expenses_table_columnss', 'changee_add_vendor_column');
hooks()->add_filter('expenses_table_sql_columns', 'changee_add_vendor_sql_column');
hooks()->add_filter('expenses_table_row_data', 'changee_add_vendor_row_data', 10, 2);
hooks()->add_action('changee_init',CHANGEE_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', CHANGEE_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', CHANGEE_MODULE_NAME.'_predeactivate');
hooks()->add_action('pre_uninstall_module', CHANGEE_MODULE_NAME.'_uninstall');
//Changee mail template
register_merge_fields('changee/merge_fields/changee_order_merge_fields');
register_merge_fields('changee/merge_fields/changee_request_merge_fields');
register_merge_fields('changee/merge_fields/changee_quotation_merge_fields');
register_merge_fields('changee/merge_fields/debit_note_merge_fields');
register_merge_fields('changee/merge_fields/changee_statement_merge_fields');
register_merge_fields('changee/merge_fields/vendor_merge_fields');
register_merge_fields('changee/merge_fields/changee_contract_merge_fields');
register_merge_fields('changee/merge_fields/changee_approve_merge_fields');
register_merge_fields('changee/merge_fields/changee_request_approval_merge_fields');
register_merge_fields('changee/merge_fields/changee_request_to_approver_merge_fields');
register_merge_fields('changee/merge_fields/changee_request_to_sender_merge_fields');
register_merge_fields('changee/merge_fields/changee_order_to_approver_merge_fields');
register_merge_fields('changee/merge_fields/changee_order_to_sender_merge_fields');
register_merge_fields('changee/merge_fields/changee_quotation_to_approver_merge_fields');
register_merge_fields('changee/merge_fields/changee_quotation_to_sender_merge_fields');


hooks()->add_filter('other_merge_fields_available_for', 'changee_register_other_merge_fields');

define('CHANGEE_PATH', 'modules/changee/uploads/');
define('CHANGEE_MODULE_ITEM_UPLOAD_FOLDER', 'modules/changee/uploads/item_img/');

define('CHANGEE_REVISION', 150);
// define('COMMODITY_ERROR_PUR', FCPATH );
// define('COMMODITY_EXPORT_PUR', FCPATH );
define('CHANGEE_IMPORT_ITEM_ERROR', 'modules/changee/uploads/import_item_error/');
define('CHANGEE_IMPORT_VENDOR_ERROR', 'modules/changee/uploads/import_vendor_error/');

/**
* Register activation module hook
*/
register_activation_hook(CHANGEE_MODULE_NAME, 'changee_module_activation_hook');
/**
* Load the module helper
*/
$CI = & get_instance();
$CI->load->helper(CHANGEE_MODULE_NAME . '/changee');

//Vendor portal UI
if(changee_get_status_modules_pur('theme_style') == 1){
    hooks()->add_action('changee_app_vendor_head', 'changee_theme_style_vendor_area_head');
}

function changee_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
if (strpos($_SERVER['REQUEST_URI'], "changee") !== false) {
    register_language_files(CHANGEE_MODULE_NAME, [CHANGEE_MODULE_NAME]);
}

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function changee_module_init_menu_items() {

    $CI = &get_instance();
    if (has_permission('changee_items', '', 'view') || has_permission('changee_vendors', '', 'view') || has_permission('changee_vendor_items', '', 'view') || has_permission('changee_request', '', 'view') || has_permission('changee_quotations', '', 'view') || has_permission('changee_orders', '', 'view') || has_permission('changee_contracts', '', 'view') || has_permission('changee_invoices', '', 'view') || has_permission('changee_reports', '', 'view') || has_permission('changee_debit_notes', '', 'view') || has_permission('changee_settings', '', 'edit') || has_permission('changee_vendors', '', 'view_own') || has_permission('changee_vendor_items', '', 'view_own') || has_permission('changee_request', '', 'view_own') || has_permission('changee_quotations', '', 'view_own') || has_permission('changee_orders', '', 'view_own') || has_permission('changee_contracts', '', 'view_own') || has_permission('changee_invoices', '', 'view_own') || has_permission('changee_debit_notes', '', 'view_own') || has_permission('changee_order_return', '', 'view_own') || has_permission('changee_order_return', '', 'view') ) {
        $CI->app_menu->add_sidebar_menu_item('changee', [
            'name' => 'Change order',
            'icon' => 'fa fa-shopping-cart',
            'position' => 30,
        ]);
    }

    

        $CI->db->where('module_name', 'warehouse');
        $module = $CI->db->get(db_prefix() . 'modules')->row();

        // if(has_permission('changee_items', '', 'view') ){
        //     $CI->app_menu->add_sidebar_children_item('changee', [
        //         'slug' => 'changee-items',
        //         'name' => _l('items'),
        //         'icon' => 'fa fa-clone menu-icon',
        //         'href' => admin_url('changee/items'),
        //         'position' => 1,
        //     ]);
        // }

        // if(has_permission('changee_vendors', '', 'view') || has_permission('changee_vendors', '', 'view_own')){
        //     $CI->app_menu->add_sidebar_children_item('changee', [
        //         'slug' => 'vendors',
        //         'name' => _l('vendor'),
        //         'icon' => 'fa fa-users',
        //         'href' => admin_url('changee/vendors'),
        //         'position' => 2,
        //     ]);
        // }

        // if(has_permission('changee_vendor_items', '', 'view') || has_permission('changee_vendor_items', '', 'view_own')){
        //     $CI->app_menu->add_sidebar_children_item('changee', [
        //         'slug' => 'vendors-items',
        //         'name' => _l('vendor_item'),
        //         'icon' => 'fa fa-newspaper',
        //         'href' => admin_url('changee/vendor_items'),
        //         'position' => 3,
        //     ]);
        // }

        if(has_permission('changee_request', '', 'view') || has_permission('changee_request', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('changee', [
                'slug' => 'changee-request',
                'name' => 'Change request',
                'icon' => 'fa fa-shopping-basket',
                'href' => admin_url('changee/changee_request'),
                'position' => 4,
            ]);
        }

        if(has_permission('changee_quotations', '', 'view')  || has_permission('changee_quotations', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('changee', [
                'slug' => 'changee-quotation',
                'name' => 'Quotations',
                'icon' => 'fa fa-file-powerpoint',
                'href' => admin_url('changee/quotations'),
                'position' => 5,
            ]);
        }

        if(has_permission('changee_orders', '', 'view') || has_permission('changee_orders', '', 'view_own')){
            $CI->app_menu->add_sidebar_children_item('changee', [
                'slug' => 'changee-order',
                'name' => 'Change order',
                'icon' => 'fa fa-cart-plus',
                'href' => admin_url('changee/changee_order'),
                'position' => 6,
            ]);
        }

        // if(has_permission('changee_order_return', '', 'view') || has_permission('changee_order_return', '', 'view_own')){
        //     $CI->app_menu->add_sidebar_children_item('changee', [
        //         'slug' => 'return-order',
        //         'name' => _l('pur_return_orders'),
        //         'icon' => 'fa fa-reply-all',
        //         'href' => admin_url('changee/order_returns'),
        //         'position' => 7,
        //     ]);
        // }

        // if(has_permission('changee_contracts', '', 'view') || has_permission('changee_contracts', '', 'view_own')){
        //     $CI->app_menu->add_sidebar_children_item('changee', [
        //         'slug' => 'changee-contract',
        //         'name' => _l('contracts'),
        //         'icon' => 'fa fa-file-text',
        //         'href' => admin_url('changee/contracts'),
        //         'position' => 8,
        //     ]);
        // }

        // if(has_permission('changee_debit_notes', '', 'view') || has_permission('changee_debit_notes', '', 'view_own')){
        //     $CI->app_menu->add_sidebar_children_item('changee', [
        //         'slug'     => 'changee-debit-note',
        //         'name'     => _l('pur_debit_note'),
        //         'icon'     => 'fa fa-credit-card',
        //         'href'     => admin_url('changee/debit_notes'),
        //         'position' => 9,
        //     ]);
        // }

        // if(has_permission('changee_invoices', '', 'view') || has_permission('changee_invoices', '', 'view_own')){
        //     $CI->app_menu->add_sidebar_children_item('changee', [
        //         'slug' => 'changee-invoices',
        //         'name' => _l('invoices'),
        //         'icon' => 'fa fa-clipboard',
        //         'href' => admin_url('changee/invoices'),
        //         'position' => 10,
        //     ]);
        // }

        if(has_permission('changee_reports', '', 'view') ){
            $CI->app_menu->add_sidebar_children_item('changee', [
                'slug' => 'changee_reports',
                'name' => _l('reports'),
                'icon' => 'fa fa-bar-chart',
                'href' => admin_url('changee/reports'),
                'position' => 11,
            ]);
        }
    

    if (is_admin() || has_permission('changee_settings', '', 'edit')) {
        $CI->app_menu->add_sidebar_children_item('changee', [
            'slug' => 'changee-settings',
            'name' => _l('setting'),
            'icon' => 'fa fa-gears',
            'href' => admin_url('changee/setting'),
            'position' => 12,
        ]);
    }

}

/**
 * { changee add dashboard widget }
 *
 * @param        $widgets  The widgets
 *
 * @return       ( description_of_the_return_value )
 */
// function changee_add_dashboard_widget($widgets)
// {
//     if (has_permission('changee', '', 'view') || is_admin()) {
//         $widgets[] = [
//                 'path'      => 'changee/changee_widget',
//                 'container' => 'top-12',
//             ];
//     }

//     return $widgets;
// }

function changee_load_js($dashboard_js){
        $CI = &get_instance();
        $viewuri = $_SERVER['REQUEST_URI'];
        if (has_permission('changee', '', 'view') || is_admin()) {
            if(!(strpos($viewuri, '/admin') === false)){
                $dashboard_js .=  $CI->load->view('changee/changee_dashboard_js'); 
            }
        }
        return $dashboard_js;
}

/**
 * { changee permissions }
 */
function changee_permissions() {
    $capabilities = [];
    $capabilities_rp = [];
    $capabilities_own = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];


    $capabilities_rp['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
    ];

     $capabilities_setting['capabilities'] = [
        'edit' => _l('permission_edit'),
    ];

    $capabilities_own['capabilities'] = [
        'view_own' => _l('permission_view') . '(' . _l('permission_own') . ')',
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    // register_staff_capabilities('changee_items', $capabilities, _l('changee_items'));
    // register_staff_capabilities('changee_vendors', $capabilities_own, _l('changee_vendors'));
    // register_staff_capabilities('changee_vendor_items', $capabilities_own, _l('changee_vendor_items'));
    // register_staff_capabilities('changee_request', $capabilities_own, _l('changee_request'));
    // register_staff_capabilities('changee_quotations', $capabilities_own, _l('changee_quotations'));
    register_staff_capabilities('changee_orders', $capabilities_own, 'Change Orders');
    // register_staff_capabilities('changee_order_return', $capabilities_own, _l('changee_order_return'));
    // register_staff_capabilities('changee_contracts', $capabilities_own, _l('changee_contracts'));
    // register_staff_capabilities('changee_invoices', $capabilities_own, _l('changee_invoices'));
    // register_staff_capabilities('changee_debit_notes', $capabilities_own, _l('changee_debit_notes'));
    // register_staff_capabilities('changee_reports', $capabilities_rp, _l('changee_reports'));
    // register_staff_capabilities('changee_settings', $capabilities_setting, _l('changee_settings'));

    // register_staff_capabilities('changee_order_change_approve_status', $capabilities_setting, _l('changee_order_change_approve_status'));
    
    // register_staff_capabilities('changee_estimate_change_approve_status', $capabilities_setting, _l('changee_quotations_change_approve_status'));
    // register_staff_capabilities('changee_request_change_approve_status', $capabilities_setting, _l('changee_request_change_approve_status'));

}

/**
 * { changee_before_load_theme_style }
 *
 * @param      <type>  $area   The area
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function changee_before_load_theme_style($area){
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri, 'changee/vendors_portal') === false)) {
        $area['general'] = [];
        $area['tabs'] = [];
        $area['buttons'] = [];
        $area['admin'] = [];
        $area['modals'] = [];
        $area['tags'] = [];
        $area['customers'] = [];
    }

    return $area;
}

/**
 * changee add footer components
 * @return
 */
function changee_add_footer_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/admin/changee/vendors') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/vendor_manage.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/changee/changee_request') === false)){    
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/co_request_manage.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/changee/quotations') === false)){
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/quotation_manage.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/changee/co_request') === false)){
        
    }
    if(!(strpos($viewuri, '/admin/changee/view_co_request') === false)){
        echo '<link rel="stylesheet prefetch" href="'.base_url('modules/changee/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }
    if(!(strpos($viewuri, '/admin/changee/changee_order') === false)){
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/changee_order_manage.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/changee/contracts') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/contract_manage.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }
    if(!(strpos($viewuri, '/admin/changee/contract') === false)){
       
        echo '<script src="'.base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
    }
    if (!(strpos($viewuri, '/admin/changee/pur_order') === false)) {
        echo '<link rel="stylesheet prefetch" href="'.base_url('modules/changee/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/numbro/languages.min.js').'"></script>'; 
    }
    if (!(strpos($viewuri, '/admin/changee/estimate') === false)) {
        echo '<link rel="stylesheet prefetch" href="'.base_url('modules/changee/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }
    if (!(strpos($viewuri, 'changee/vendors_portal/add_update_quotation') === false)) {
        echo '<script type="text/javascript" src="' . site_url('assets/plugins/tinymce/tinymce.min.js') . '?v=' . CHANGEE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, 'changee/vendors_portal/add_update_quotation') === false)) {
        echo '<script type="text/javascript" src="' . site_url('assets/js/app.js') . '?v=' . CHANGEE_REVISION . '"></script>';
        echo '<script type="text/javascript" src="' . site_url('assets/plugins/accounting.js/accounting.js') . '?v=' . CHANGEE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, 'changee/vendors_portal/add_update_invoice') === false)) {
        echo '<script type="text/javascript" src="' . site_url('assets/js/app.js') . '?v=' . CHANGEE_REVISION . '"></script>';
        echo '<script type="text/javascript" src="' . site_url('assets/plugins/accounting.js/accounting.js') . '?v=' . CHANGEE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, 'changee/vendors_portal/pur_order') === false)) {
        echo '<link rel="stylesheet prefetch" href="'.base_url('modules/changee/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }

    if (!(strpos($viewuri, '/admin/changee/reports') === false)) {
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>'; 
    }

    if (!(strpos($viewuri, '/admin/changee/items') === false)) {
         echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
         
    }

    if (!(strpos($viewuri, '/admin/changee/new_vendor_items') === false)) {
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/vendor_items.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }

    if (!(strpos($viewuri, '/admin/changee/setting?group=commodity_group') === false)) {
       
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="https://momentjs.com/downloads/moment-timezone.min.js"></script>';
    }

    if (!(strpos($viewuri, '/admin/changee/setting?group=sub_group') === false)) {
        
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
         echo '<link rel="stylesheet prefetch" href="'.base_url('modules/changee/assets/plugins/handsontable/chosen.css').'">';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/chosen.jquery.js').'"></script>';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/handsontable-chosen-editor.js').'"></script>'; 
         echo '<script src="https://momentjs.com/downloads/moment-timezone.min.js"></script>';
    }

    if(!(strpos($viewuri, '/admin/changee/invoices') === false)){    
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/manage_invoices.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }



    if(!(strpos($viewuri, '/admin/changee/changee_invoice') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/pur_invoice_preview.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }

    if(!(strpos($viewuri, '/admin/changee/setting?group=currency_rates') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/currency_rate.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }
    

    if(!(strpos($viewuri, '/admin/projects/view') === false)  && !(strpos($viewuri, '?group=changee_order') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/po_on_project.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }

    if(!(strpos($viewuri, '/admin/projects/view') === false)  && !(strpos($viewuri, '?group=changee_contract') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/pur_contract_on_project.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }

    if(!(strpos($viewuri, '/admin/projects/view') === false)  && !(strpos($viewuri, '?group=changee_request') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/co_request_on_project.js') .'?v=' . CHANGEE_REVISION.'"></script>';
    }

}

/**
 * add changee add head components
 * @return
 */
function changee_add_head_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/admin/changee/co_request') === false)){
        
    }
    if(!(strpos($viewuri, '/admin/changee/view_co_request') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/estimate') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/changee/vendors_portal/add_update_quotation') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }

    if(!(strpos($viewuri, '/changee/vendors_portal/pur_order') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }

    if(!(strpos($viewuri, '/changee/vendors_portal/detail_item') === false)){
        

         echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
         echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/js/detail_vendor_item.js') .'?v=' . CHANGEE_REVISION.'"></script>';
     }

    if(!(strpos($viewuri, '/changee/vendors_portal/co_request') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="'.base_url('modules/changee/assets/plugins/handsontable/numbro/languages.min.js').'"></script>';
    }

    if(!(strpos($viewuri, '/admin/changee/pur_order') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/changee/setting?group=units') === false)){
        echo '<script src="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/setting.css') . '"  rel="stylesheet" type="text/css" />';
    }
}

/**
 * changee head components
 * @return
 */
function changee_head_components() {
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    
    if(!(strpos($viewuri, '/admin/changee') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/style.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/contract') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/contract.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/changee/setting') === false)) {
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/setting.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/commodity_list.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/changee_order') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/pur_order_manage.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/pur_order') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/pur_order.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/co_request') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/co_request.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/changee_request') === false)){    
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/co_request_manage.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/view_co_request') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/view_co_request.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/estimate') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/estimate_template.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'changee/vendors_portal/add_update_quotation') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/estimate_template.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'changee/vendors_portal/pur_order') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/estimate_template.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'changee/vendors_portal/order_return') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/estimate_template.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'changee/vendors_portal/add_update_invoice') === false)){ 
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/add_update_invoice.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'changee/vendors_portal/invoices') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/manage_vendor_invoice.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, 'changee/vendors_portal/items') === false)) {
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/vendor_item_style.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, 'changee/vendors_portal/detail_item') === false)) {
        echo '<link href="' . base_url('modules/warehouse/assets/css/styles.css') .'?v=' . CHANGEE_REVISION. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, 'changee/vendors_portal/invoice/') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/manage_vendor_invoice.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/quotations') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/estimate_preview_template.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
    if(!(strpos($viewuri, '/admin/changee/vendor') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/pur_order_manage.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/changee/items') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/commodity_list.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.css') . '"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/changee/pur_invoice') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/pur_invoice.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/changee/changee_invoice') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/pur_invoice.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/changee/payment_invoice') === false)){
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/payment_invoice.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/changee/order_returns') === false)){
        echo '<script src="'. base_url('assets/plugins/signature-pad/signature_pad.min.js').'"></script>';
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/style.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, 'changee/vendors_portal/add_update_items') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/vendor_item.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, 'changee/vendors_portal/') === false)){
        echo '<link href="' . module_dir_url(CHANGEE_MODULE_NAME, 'assets/css/vendor_style.css') .'?v=' . CHANGEE_REVISION.'"  rel="stylesheet" type="text/css" />';

    }
}   

/**
 * Initializes the vendor option.
 *
 * @param      string  $expense  The expense
 */
function changee_init_vendor_option($expense = ''){
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');
    $list_vendor = $CI->changee_model->get_vendor();
    $option = '';
    $option .= '<div class="row">';
    $option .= '<div class="col-md-12">';
    $option .= '<lable for="vendor">'._l('vendor').'</label>';
    $option .= '<select name="vendor" id="vendor" data-width="100%" class="selectpicker" data-live-search="true" data-none-selected-text="'. _l('ticket_settings_none_assigned').'">';
    $select = '';
    $option .= '<option value=""></option>';
    foreach($list_vendor as $ven){
        if( $expense != '' && $expense->vendor == $ven['userid']){
            $select = 'selected';
        }else{
            $select = ''; 
        }
        $option .= '<option value="'.$ven['userid'].'" '.$select.'>'. $ven['company'].'</option>';
    }
    $option .= '</select>';
    $option .= '</div>';
    $option .= '</div>';
    $option .= '<br>';
    echo changee_pur_html_entity_decode($option);
}

/**
 * Initializes the vendor customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function changee_init_vendor_customfield($custom_field = ''){
    $select = '';
    if($custom_field != ''){
        if($custom_field->fieldto == 'vendors'){
            $select = 'selected';
        }
    }

    $html = '<option value="vendors" '.$select.' >'. _l('vendors').'</option>';

    echo changee_pur_html_entity_decode($html);
}

/**
 * Initializes the changee order customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function changee_init_po_customfield($custom_field = ''){
    $select = '';
    if($custom_field != ''){
        if($custom_field->fieldto == 'pur_order'){
            $select = 'selected';
        }
    }

    $html = '<option value="pur_order" '.$select.' >'. _l('pur_order').'</option>';

    echo changee_pur_html_entity_decode($html);
}

/**
 * Initializes the changee order customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function changee_init_vendor_contacts_customfield($custom_field = ''){
    $select = '';
    if($custom_field != ''){
        if($custom_field->fieldto == 'vendor_contacts'){
            $select = 'selected';
        }
    }

    $html = '<option value="vendor_contacts" '.$select.' >'. _l('vendor_contacts').'</option>';

    echo changee_pur_html_entity_decode($html);
}

/**
 * Initializes the tab changee order in client.
 *
 *
 */
function changee_init_tab_pur_order() {
    echo '<li role="presentation">
                  <a href="#pur_order" aria-controls="pur_order" role="tab" data-toggle="tab">
                  ' . _l('pur_order') . '
                  </a>
               </li>';
}

/**
 * Initializes the tab content changee order.
 *
 *
 */
function changee_init_content_pur_order($client) {
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');
    if ($client) {
        echo '<div role="tabpanel" class="tab-pane" id="pur_order">';
        require "modules/changee/views/client_pur_order.php";
        echo '</div>';
    }
}

/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function changee_po_related_to_select($value)
{

    $selected = '';
    if($value == 'pur_order'){
        $selected = 'selected';
    }
    echo "<option value='pur_order' ".$selected.">".
                               _l('pur_order')."
                           </option>";

}

/**
 * PO relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function changee_po_relation_values($values, $relation = null)
{

    if ($values['type'] == 'pur_order' || $values['type'] == 'changee_order') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = $relation['pur_order_number'];
        } else {
            $values['id']   = $relation->id;
            $values['name'] = $relation->pur_order_number;
        }
        $values['link'] = admin_url('changee/changee_order/' . $values['id']);
    }

    return $values;
}

/**
 * PO relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function changee_po_relation_data($data, $type, $rel_id, $q = '')
{

    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if ($type == 'pur_order') {
        if ($rel_id != '') {
            $data = $CI->changee_model->get_pur_order($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * PO add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function changee_po_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if($aRow['rel_type'] == 'pur_order'){
        $po = $CI->changee_model->get_pur_order($aRow['rel_id']);

           if ($po) {

                 $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('changee/changee_order/' . $po->id) . '">' . $po->pur_order_number . '</a><br />';

                $row[2] =   $row[2].$str;
            }

    }

    return $row;
}

/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function changee_pq_related_to_select($value)
{

    $selected = '';
    if($value == 'pur_quotation'){
        $selected = 'selected';
    }
    echo "<option value='pur_quotation' ".$selected.">".
                               _l('changee_quotation')."
                           </option>";

}

/**
 * pq relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function changee_pq_relation_values($values, $relation = null)
{

    if ($values['type'] == 'pur_quotation') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = changee_format_pur_estimate_number($relation['id']);
        } else {
            $values['id']   = $relation->id;
            $values['name'] = changee_format_pur_estimate_number($relation->id);
        }
        $values['link'] = admin_url('changee/quotations/' . $values['id']);
    }

    return $values;
}

/**
 * pq relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function changee_pq_relation_data($data, $type, $rel_id, $q = '')
{

    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if ($type == 'pur_quotation') {
        if ($rel_id != '') {
            $data = $CI->changee_model->get_estimate($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * pq add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function changee_pq_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if($aRow['rel_type'] == 'pur_quotation'){
        $pq = $CI->changee_model->get_estimate($aRow['rel_id']);

           if ($pq) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('changee/quotations/' . $pq->id) . '">' . changee_format_pur_estimate_number($pq->id) . '</a><br />';

                $row[2] =  $row[2].$str;
            }

    }

    return $row;
}

/**
 * reset changee order number
 *  
 */
function changee_reset_pur_order_number($manually)
{
    $CI = &get_instance();

    if(get_option('reset_changee_order_number_every_month') == 1){
        if (date('d') == 1) {
            if(date('Y-m-d') != get_changee_option('date_reset_number')){
                $CI->db->where('option_name','next_po_number');
                $CI->db->update(db_prefix().'changee_option',['option_val' => '1']);
                if ($CI->db->affected_rows() > 0) {
                    $CI->db->where('option_name','date_reset_number');
                    $CI->db->update(db_prefix().'changee_option',['option_val' => date('Y-m-d')]);
                }
            }
        }
    }
}

/**
 * reset changee order number
 *  
 */
function changee_reset_co_request_number($manually)
{
    $CI = &get_instance();

    if (date('m-d') == '01-01') {
        if(date('Y-m-d') != get_changee_option('date_reset_pr_number')){
            $CI->db->where('option_name','next_pr_number');
            $CI->db->update(db_prefix().'changee_option',['option_val' => '1']);
            if ($CI->db->affected_rows() > 0) {
                $CI->db->where('option_name','date_reset_pr_number');
                $CI->db->update(db_prefix().'changee_option',['option_val' => date('Y-m-d')]);
            }
        }
    }
}

/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function changee_pc_related_to_select($value)
{

    $selected = '';
    if($value == 'pur_contract'){
        $selected = 'selected';
    }
    echo "<option value='pur_contract' ".$selected.">".
                               _l('changee_contract')."
                           </option>";

}

/**
 * changee contract relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function changee_pc_relation_values($values, $relation = null)
{

    if ($values['type'] == 'pur_contract') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = changee_get_pur_contract_number($relation['id']);
        } else {
            $values['id']   = $relation->id;
            $values['name'] = changee_get_pur_contract_number($relation->id);
        }
        $values['link'] = admin_url('changee/contract/' . $values['id']);
    }

    return $values;
}

/**
 * changee contract relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function changee_pc_relation_data($data, $type, $rel_id, $q = '')
{

    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if ($type == 'pur_contract') {
        if ($rel_id != '') {
            $data = $CI->changee_model->get_contract($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * pq add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function changee_pc_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if($aRow['rel_type'] == 'pur_contract'){
        $pc = $CI->changee_model->get_contract($aRow['rel_id']);

           if ($pc) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('changee/contract/' . $pc->id) . '">' . changee_get_pur_contract_number($pc->id) . '</a><br />';

                $row[2] =  $row[2].$str;
            }

    }

    return $row;
}


/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function changee_pi_related_to_select($value)
{

    $selected = '';
    if($value == 'pur_invoice'){
        $selected = 'selected';
    }
    echo "<option value='pur_invoice' ".$selected.">".
                               _l('pur_invoice')."
                           </option>";

}

/**
 * changee contract relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function changee_pi_relation_values($values, $relation = null)
{

    if ($values['type'] == 'pur_invoice') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = changee_get_pur_invoice_number($relation['id']);
        } else {
            $values['id']   = $relation->id;
            $values['name'] = changee_get_pur_invoice_number($relation->id);
        }
        $values['link'] = admin_url('changee/changee_invoice/' . $values['id']);
    }

    return $values;
}

/**
 * changee contract relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function changee_pi_relation_data($data, $type, $rel_id, $q = '')
{

    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if ($type == 'pur_invoice') {
        if ($rel_id != '') {
            $data = $CI->changee_model->get_pur_invoice($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * pq add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function changee_pi_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if($aRow['rel_type'] == 'pur_invoice'){
        $pc = $CI->changee_model->get_pur_invoice($aRow['rel_id']);

           if ($pc) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('changee/changee_invoice/' . $pc->id) . '">' . changee_get_pur_invoice_number($pc->id) . '</a><br />';

                $row[2] =  $row[2].$str;
            }

    }

    return $row;
}

/**
 * Register other merge fields for changee
 *
 * @param [array] $for
 * @return void
 */
function changee_register_other_merge_fields($for) {
    $for[] = 'changee_order';

    return $for;
}


if(changee_get_status_modules_pur('theme_style') == 1){
    /**
     * Clients area theme applied styles
     * @return null
     */
    function changee_theme_style_vendor_area_head()
    {   
        theme_style_render(['general', 'tabs', 'buttons', 'customers', 'modals']);
        changee_theme_style_custom_css_pur('theme_style_custom_clients_area');
    }

    /**
     * Custom CSS
     * @param  string $main_area clients or admin area options
     * @return null
     */
    function changee_theme_style_custom_css_pur($main_area)
    {
        $clients_or_admin_area             = get_option($main_area);
        $custom_css_admin_and_clients_area = get_option('theme_style_custom_clients_and_admin_area');
        if (!empty($clients_or_admin_area) || !empty($custom_css_admin_and_clients_area)) {
            echo '<style id="theme_style_custom_css">' . PHP_EOL;
            if (!empty($clients_or_admin_area)) {
                $clients_or_admin_area = clear_textarea_breaks($clients_or_admin_area);
                echo $clients_or_admin_area . PHP_EOL;
            }
            if (!empty($custom_css_admin_and_clients_area)) {
                $custom_css_admin_and_clients_area = clear_textarea_breaks($custom_css_admin_and_clients_area);
                echo $custom_css_admin_and_clients_area . PHP_EOL;
            }
            echo '</style>' . PHP_EOL;
        }
    }
}

/**
 * recurring changee invoice
 *
 */
function recurring_changee_invoice($manually) {
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');
    $CI->changee_model->recurring_changee_invoice($manually);
}

/**
 * { function_description }
 *
 * @param      <type>  $path   The path
 * @param      <type>  $type   The type
 */
function changee_debit_note_upload_file_path($path, $type){
    if($type == 'debit_note'){
        $path = CHANGEE_MODULE_UPLOAD_FOLDER. '/debit_notes/';
    }
    return $path;
}

/**
 * Initializes the changee order customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function changee_init_invoice_customfield($custom_field = '') {
    $select = '';
    if ($custom_field != '') {
        if ($custom_field->fieldto == 'pur_invoice') {
            $select = 'selected';
        }
    }

    $html = '<option value="pur_invoice" ' . $select . ' >' . _l('changee_invoice') . '</option>';

    echo changee_pur_html_entity_decode($html);
}

/**
 * po task modal rel type select
 * @param  object $value
 * @return
 */
function changee_po_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'changee_order') {
        $selected = 'selected';
    }
    echo "<option value='changee_order' " . $selected . ">" .
    _l('changee_order') . "
                           </option>";

}

/**
 * pq get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function changee_po_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'pur_order' || $values['type'] == 'changee_order') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = $relation['pur_order_number'];
        } else {
            $values['id'] = $relation->id;
            $values['name'] = $relation->pur_order_number;
        }
        $values['link'] = admin_url('changee/changee_order/' . $values['id']);
    }

    return $values;
}

/**
 * po get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function changee_po_get_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if ($type == 'pur_order' || $type == 'changee_order') {
        if ($rel_id != '') {
            $data = $CI->changee_model->get_pur_order($rel_id);
        } else {
            if($q != ''){
                $data = $CI->changee_model->get_pur_order_search($q);
            }
        }
    }
    return $data;
}

/**
 * PO relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function changee_debit_note_get_relation_values($values, $relation = null)
{

    if ($values['type'] == 'debit_note') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = $relation['number'];
        } else {
            $values['id']   = $relation->id;
            $values['name'] = changee_format_debit_note_number($relation->id);
        }
        $values['link'] = admin_url('changee/debit_notes/' . $values['id']);
    }

    return $values;
}

/**
 * po get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function changee_debit_note_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if ($type == 'debit_note') {
        if ($rel_id != '') {
            $data = $CI->changee_model->get_debit_note($rel_id);
        } else {
            if($q != ''){
                $data = $CI->changee_model->get_debit_note_search($q);
            }
        }
    }
    return $data;
}

/**
 * pq task modal rel type select
 * @param  object $value
 * @return
 */
function changee_pq_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'pur_quotation') {
        $selected = 'selected';
    }
    echo "<option value='pur_quotation' " . $selected . ">" .
    _l('changee_quotation') . "
                           </option>";

}

/**
 * pq get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function changee_pq_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'pur_quotation') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = changee_format_pur_estimate_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = changee_format_pur_estimate_number($relation->id);
        }
        $values['link'] = admin_url('changee/quotations/' . $values['id']);
    }

    return $values;
}

/**
 * pq get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function changee_pq_get_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if ($type == 'pur_quotation') {
        if ($rel_id != '') {
            $data = $CI->changee_model->get_estimate($rel_id);
        } else {
            if($q != ''){
                $data = $CI->changee_model->get_estimate_search($q);
            }
        }
    }
    return $data;
}

/**
 * pq task modal rel type select
 * @param  object $value
 * @return
 */
function changee_pc_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'pur_contract') {
        $selected = 'selected';
    }
    echo "<option value='pur_contract' " . $selected . ">" .
    _l('changee_contract') . "
                           </option>";

}

/**
 * pc get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function changee_pc_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'pur_contract') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = changee_get_pur_contract_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = changee_get_pur_contract_number($relation->id);
        }
        $values['link'] = admin_url('changee/contract/' . $values['id']);
    }

    return $values;
}

/**
 * pc get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function changee_pc_get_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if ($type == 'pur_contract') {
        if ($rel_id != '') {
            $data = $CI->changee_model->get_contract($rel_id);
        } else {
            if($q != ''){
                $data = $CI->changee_model->get_contract_seach($rel_id);
            }
        }
    }
    return $data;
}

/**
 * pi task modal rel type select
 * @param  object $value
 * @return
 */
function changee_pi_task_modal_rel_type_select($value) {
    $selected = '';
    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'pur_invoice') {
        $selected = 'selected';
    }
    echo "<option value='pur_invoice' " . $selected . ">" .
    _l('pur_invoice') . "
                           </option>";

}

/**
 * pi get relation values description
 * @param  object $values
 * @param  object $relation
 * @return
 */
function changee_pi_get_relation_values($values, $relation = null) {
    if ($values['type'] == 'pur_invoice') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = changee_get_pur_invoice_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = changee_get_pur_invoice_number($relation->id);
        }
        $values['link'] = admin_url('changee/changee_invoice/' . $values['id']);
    }

    return $values;
}

/**
 * pi get relation data
 * @param  object $data
 * @param  object $obj
 * @return
 */
function changee_pi_get_relation_data($data, $obj, $q = '') {
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');

    if ($type == 'pur_invoice') {
        if ($rel_id != '') {
            $data = $CI->changee_model->get_pur_invoice($rel_id);
        } else {
            if($q != ''){
                $data = $CI->changee_model->get_pur_invoice_search($rel_id);
            }
        }
    }
    return $data;
}

/**
 * reset changee order number
 *
 */
function changee_pur_cronjob_currency_rates($manually) {
    $CI = &get_instance();
    $CI->load->model('changee/changee_model');
    if (date('G') == '16' && get_option('cr_automatically_get_currency_rate') == 1) {
        if(date('Y-m-d') != get_option('cur_date_cronjob_currency_rates')){
            $CI->changee_model->cronjob_currency_rates($manually);
        }
    }
    

}

/**
 * Initializes the po project tabs.
 *
 * @param        $tabs   The tabs
 *
 * @return       ( tabs )
 */
function changee_init_po_project_tabs($tabs){
    $tabs['changee'] = [
        'slug' => 'changee',
        'name' => _l('changee'),
        'icon' => 'fa fa-cart-plus',
        'position' => 51,
        'collapse' => true,
        'visible' => true,
        'href' => '#',
        'badge' => [],
        'children' => [
            0 => [
                'parent_slug' => 'changee',
                'slug' => 'changee_request',
                'name' => _l('changee_request'),
                'view' => 'changee/co_request_on_project',
                'position' => 5,
                'visible' => true,
                'icon' => '',
                'href' => '#',
                'badge' => [],
            ],
            1 => [
                'parent_slug' => 'changee',
                'slug' => 'changee_order',
                'name' => _l('changee_order'),
                'view' => 'changee/po_on_project',
                'position' => 5,
                'visible' => true,
                'icon' => '',
                'href' => '#',
                'badge' => [],
            ],
            2 => [
                'parent_slug' => 'changee',
                'slug' => 'changee_contract',
                'name' => _l('changee_contract'),
                'view' => 'changee/pur_contract_on_project',
                'position' => 5,
                'visible' => true,
                'icon' => '',
                'href' => '#',
                'badge' => [],
            ],
        ],
    ];

    return $tabs;
}

/**
 * { update email language for vendor }
 */
function changee_update_email_lang_for_vendor($language, $data){

    $changee_slug_arr = [
        'changee-request-to-contact',
        'debit-note-to-contact',
        'changee-quotation-to-contact',
        'changee-request-to-contact',
        'changee-statement-to-contact',
        'vendor-registration-confirmed',
        'changee-order-to-contact',
        'changee-contract-to-contact',
    ];

    if( in_array($data['template']->slug, $changee_slug_arr)){
        if($data['template']->slug == 'vendor-registration-confirmed'){
            return $language;
        }else{
            $vendor_lang = changee_changee_get_vendor_language_by_email($data['email']);
            if($vendor_lang != ''){
                $language = $vendor_lang;
            }
        }
    }

    return $language;
}

/**
 * { reload language }
 */
function changee_reload_language($language){
    $CI = &get_instance();
    if($CI instanceof AdminController){
        $CI->lang->load($language . '_lang', $language);
        if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
            $CI->lang->load('custom_lang', $language);
        }

        $GLOBALS['language'] = $language;
        $GLOBALS['locale']   = get_locale_key($language);
    }else{
        if($CI instanceof Vendors_portal){
            $vendor_id = changee_get_vendor_user_id();

            if($vendor_id != 0){
                $CI->db->select('default_language');
                $CI->db->where('userid', $vendor_id);
                $lang = $CI->db->get(db_prefix().'pur_vendor')->row();
                if($lang && $lang->default_language != ''){
                    $CI->lang->load($lang->default_language . '_lang', $lang->default_language);
                    $CI->lang->load('changee' . '/' .'changee', $lang->default_language);

                    if (file_exists(APPPATH . 'language/' . $lang->default_language . '/custom_lang.php')) {
                        $CI->lang->load('custom_lang', $lang->default_language);
                    }
                    $GLOBALS['language'] = $lang->default_language;
                    $GLOBALS['locale']   = get_locale_key($lang->default_language);
                }else{
                    $CI->lang->load($language . '_lang', $language);
                    if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
                        $CI->lang->load('custom_lang', $language);
                    }
                    $GLOBALS['language'] = $language;
                    $GLOBALS['locale']   = get_locale_key($language);
                }
            }else{
                $CI->lang->load($language . '_lang', $language);
                if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
                    $CI->lang->load('custom_lang', $language);
                }
                $GLOBALS['language'] = $language;
                $GLOBALS['locale']   = get_locale_key($language);
            }
        }
    }
}

/**
 * { changee_add_vendor_column }
 *
 * @param      <type>  $table_data  The table data
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function changee_add_vendor_column($table_data){
    $table_data[] = _l('pur_vendor');
    return $table_data;
}

/**
 * { changee_add_vendor_sql_column }
 *
 * @param      <type>  $acolumn  The acolumn
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function changee_add_vendor_sql_column($acolumn){
    $acolumn[] = 'vendor';
    return $acolumn;
}

/**
 * { changee_add_vendor_row_data }
 *
 * @param      <type>  $row    The row
 * @param      <type>  $arow   The arow
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function changee_add_vendor_row_data($row, $arow){
    if(is_numeric($arow['vendor']) && $arow['vendor'] > 0){

        $row[] = '<a href="'.admin_url('changee/vendor/'.$arow['vendor']).'">'. changee_get_vendor_company_name($arow['vendor']).'</a>';
    }else{
        $row[] = '';
    }
    return $row;
}
function changee_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $changee_api = new ChangeeLic();
    $changee_gtssres = $changee_api->verify_license(true);    
    if(!$changee_gtssres || ($changee_gtssres && isset($changee_gtssres['status']) && !$changee_gtssres['status'])){
         // $CI->app_modules->deactivate(CHANGEE_MODULE_NAME);
        // set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        // redirect(admin_url('modules'));
    }    
}
function changee_preactivate($module_name){
    if ($module_name['system_name'] == CHANGEE_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $changee_api = new ChangeeLic();
        $changee_gtssres = $changee_api->verify_license();          
        if(!$changee_gtssres || ($changee_gtssres && isset($changee_gtssres['status']) && !$changee_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.CHANGEE_MODULE_NAME); 
            $data['module_name'] = CHANGEE_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            // echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            // exit();
        }        
    }
}
function changee_predeactivate($module_name){
    if ($module_name['system_name'] == CHANGEE_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $changee_api = new ChangeeLic();
        $changee_api->deactivate_license();
    }
}
function changee_uninstall($module_name){
    if ($module_name['system_name'] == CHANGEE_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $changee_api = new ChangeeLic();
        $changee_api->deactivate_license();
    }
}