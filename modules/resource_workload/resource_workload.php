<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Staff workload
Description: Workload management module for PerfexCRM.
Version: 1.0.7
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
 */

define('RESOURCE_WORKLOAD_MODULE_NAME', 'resource_workload');
define('STAFF_WORKLOAD_REVISION', 107);

hooks()->add_action('admin_init', 'resource_workload_module_init_menu_items');
hooks()->add_action('admin_init', 'resource_workload_permissions');
hooks()->add_action('app_admin_head', 'resource_workload_head_components');
hooks()->add_action('app_admin_footer', 'resource_workload_add_footer_components');
hooks()->add_action('task_status_changed', 'task_complete');


/**
 * Register activation module hook
 */
register_activation_hook(RESOURCE_WORKLOAD_MODULE_NAME, 'resource_workload_module_activation_hook');

function resource_workload_module_activation_hook() {
	$CI = &get_instance();
	require_once __DIR__ . '/install.php';
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(RESOURCE_WORKLOAD_MODULE_NAME, [RESOURCE_WORKLOAD_MODULE_NAME]);

/**
 * Init resource_workload module menu items in setup in admin_init hook
 * @return null
 */
function resource_workload_module_init_menu_items() {
	if (has_permission('pw_rp_resource_workload', '', 'view')) {
		$CI = &get_instance();
		$CI->app_menu->add_sidebar_menu_item('resource_workload', [
			'name' => _l('resource_workload'),
			'icon' => 'fa fa-calendar',
			'position' => 30,
		]);

		$CI->app_menu->add_sidebar_children_item('resource_workload', [
			'slug' => 'manage-resource-workload',
			'name' => _l('overview'),
			'icon' => 'fa fa-bar-chart',
			'href' => admin_url('resource_workload'),
			'position' => 1,
		]);

		$CI->app_menu->add_sidebar_children_item('resource_workload', [
			'slug' => 'applicable_staff',
			'name' => _l('settings'),
			'icon' => 'fa fa-cog',
			'href' => admin_url('resource_workload/setting'),
			'position' => 2,
		]);
	}

}


/**
 * resource workload permissions
 * @return
 */
function resource_workload_permissions() {
	$capabilities = [];

	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
	];

	// register_staff_capabilities('pw_rp_resource_workload', $capabilities, _l('resource_workload'));
}

/**
 * add head components
 */
function resource_workload_head_components() {
	$CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	if (!(strpos($viewuri, '/admin/resource_workload') === false)) {
		echo '<link href="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/frappe-gantt/frappe-gantt.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/css/style.css') .'?v=' . STAFF_WORKLOAD_REVISION. '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
	}
	if (!(strpos($viewuri, '/admin/resource_workload/setting') === false)) {
		echo '<link href="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
	}
}

/**
 * add footer components
 * @return
 */
function resource_workload_add_footer_components() {
	$CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	if (!(strpos($viewuri, '/admin/resource_workload') === false)) {
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/frappe-gantt/frappe-gantt.min.js') . '"></script>';
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/admin/resource_workload/setting') === false)) {
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . module_dir_url(RESOURCE_WORKLOAD_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}
}

/**
 * task complete
 *
 * @param      array  $data   The data
 *
 * @return     array
 */
function task_complete($data){
	if($data['status'] == 5){
		$CI = &get_instance();
		$CI->db->where('id', $data['task_id']);
        $task = $CI->db->get(db_prefix() . 'tasks')->row();

        if($task->duedate == null || $task->duedate == ''){
			$CI->db->where('id', $data['task_id']);
	        $CI->db->update(db_prefix() . 'tasks', [
	            'duedate' => date('Y-m-d'),
	        ]);
        }
	}
	return $data;
}