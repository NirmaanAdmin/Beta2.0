<?php
defined('BASEPATH') or exit('No direct script access allowed');
hooks()->add_action('after_email_templates', 'add_document_management_email_templates');

if (!function_exists('add_document_management_email_templates')) {
	/**
	 * Init appointly email templates and assign languages
	 * @return void
	 */
	function add_document_management_email_templates()
	{
		$CI = &get_instance();

		$data['document_management_templates'] = $CI->emails_model->get(['type' => 'document_management', 'language' => 'english']);

		$CI->load->view('document_management/email_templates', $data);
	}
}

function init_fist_item($type = 'staff')
{
	$CI = &get_instance();
	$user_id = 0;
	if ($type == 'staff') {
		$user_id = get_staff_user_id();
		$CI->db->where('creator_id', $user_id);
		$CI->db->where('creator_type', $type);
	} elseif ($type == 'customer') {
		$user_id = get_client_user_id();
		$CI->db->where('creator_id', $user_id);
		$CI->db->where('creator_type', $type);
	}
	if ($CI->db->get(db_prefix() . 'dmg_items')->num_rows() == 0) {
		$data['name'] = 'Inbox';
		$data['approve'] = 1;
		$data['version'] = '1.0.0';
		$data['parent_id'] = '';
		$data['hash'] = app_generate_hash();
		$data['creator_id'] = $user_id;
		$data['creator_type'] = $type;
		$data['signed_by'] = '';
		$data['tag'] = '';
		$data['note'] = '';
		$data['is_primary'] = 1;
		$CI->db->insert(db_prefix() . 'dmg_items', $data);
	}

	$CI->db->where('creator_id', '0');
	if ($CI->db->get(db_prefix() . 'dmg_items')->num_rows() == 0) {
		$data['name'] = '#' . _l('dmg_team');
		$data['approve'] = 1;
		$data['version'] = '1.0.0';
		$data['parent_id'] = '';
		$data['hash'] = app_generate_hash();
		$data['creator_id'] = 0;
		$data['creator_type'] = 'public';
		$data['signed_by'] = '';
		$data['tag'] = '';
		$data['note'] = '';
		$data['is_primary'] = 1;
		$CI->db->insert(db_prefix() . 'dmg_items', $data);
	}

	$CI->db->select('id, name');
	$projects = $CI->db->get(db_prefix() . 'projects')->result_array();
	if (!empty($projects)) {
		foreach ($projects as $key => $value) {
			$CI->db->where('project_id', $value['id']);
			if ($CI->db->get(db_prefix() . 'dmg_items')->num_rows() == 0) {
				$data = array();
				$data['name'] = $value['name'];
				$data['project_id'] = $value['id'];
				$data['approve'] = 1;
				$data['version'] = '1.0.0';
				$data['parent_id'] = '';
				$data['hash'] = app_generate_hash();
				$data['creator_id'] = 0;
				$data['creator_type'] = 'public';
				$data['signed_by'] = '';
				$data['tag'] = '';
				$data['note'] = '';
				$data['is_primary'] = 1;
				$CI->db->insert(db_prefix() . 'dmg_items', $data);
			}
		}
	}
}

function dmg_get_file_name($id)
{
	$CI = &get_instance();
	$CI->db->select('name');
	$CI->db->where('id', $id);
	$data = $CI->db->get(db_prefix() . 'dmg_items')->row();
	if ($data) {
		return $data->name;
	}
	return '';
}

/**
 * convert custom field value to string
 * @param  string $value 
 * @param  string $type  
 * @return string        
 */
function dmg_convert_custom_field_value_to_string($value, $type)
{
	$string_content = dmg_check_content($value);
	if ($type == 'date') {
		$string_content = _d($string_content);
	}
	if ($type == 'datetime') {
		$string_content = _dt($string_content);
	}
	if ($type == 'radio_button') {
		if ($string_content == '[]') {
			$string_content = '';
		}
	}
	return trim($string_content);
}

/**
 * check content
 * @param  string $selected 
 * @return string           
 */
function dmg_check_content($selected)
{
	$result = '';
	if ($selected != null) {
		if (is_array($selected)) {
			if (count($selected) > 0) {
				$result = implode(', ', $selected);
			}
		} else {
			$selected_s = json_decode($selected);
			if (is_array($selected_s) && isset($selected_s[0])) {
				if (is_array($selected_s[0])) {
					$result = parse_array_multi_to_string($selected_s);
				} else {
					$temp_str = trim($selected_s[0]);
					if ($temp_str != '') {
						$result = implode(', ', $selected_s);
					}
				}
			} else {
				if (is_object($selected_s)) {
					$selected_s = (array)$selected_s;
					$result = parse_array_multi_to_string($selected_s);
				} else {
					if ($selected == '[]') {
						$result = '';
					} else {
						$temp_str = trim($selected);
						if ($temp_str != '') {
							$result = $selected;
						}
					}
				}
			}
		}
	}
	return rtrim($result, ', ');
}

/**
 * parse array multi to string
 * @param  array $array 
 * @return string        
 */
function parse_array_multi_to_string($array)
{
	$string = '';
	if (is_array($array)) {
		foreach ($array as $key_text => $sub_qs) {
			if ($key_text != '') {
				$sub_string = '';
				if (is_array($sub_qs) && count($sub_qs) > 0) {
					foreach ($sub_qs as $sub_text) {
						if ($sub_text != '') {
							$sub_string .= $sub_text . ', ';
						}
					}
				}
				$string .= $key_text . '' . ($sub_string != '' ? ' (' . rtrim($sub_string, ', ') . ')' : '') . ', ';
			}
		}
	}
	return $string;
}

/**
 * Check if path exists if not exists will create one
 * This is used when uploading files
 * @param  string $path path to check
 * @return null
 */
function dmg_create_folder($path)
{
	if (!file_exists($path)) {
		mkdir($path, 0755);
	}
}

/**
 * get audit log file
 * @param  integer $item_id 
 * @return integer          
 */
function get_audit_log_file($item_id)
{
	$CI = &get_instance();
	$CI->db->where('item_id', $item_id);
	$CI->db->order_by('date', 'desc');
	return $CI->db->get(db_prefix() . 'dmg_audit_logs')->result_array();
}

/**
 * check file locked
 * @param  integer $item_id 
 * @return boolean          
 */
function check_file_locked($item_id)
{
	$CI = &get_instance();
	$CI->db->select('locked, lock_user');
	$CI->db->where('id', $item_id);
	$item = $CI->db->get(db_prefix() . 'dmg_items')->row();
	if ($item && is_object($item) && $item->locked != 1 || ($item->locked == 1 && $item->lock_user == get_staff_user_id())) {
		return false;
	}
	return true;
}

/**
 * reformat currency asset
 * @param  string $str 
 * @return string        
 */
function dmg_reformat_currency_asset($str)
{
	$f_dot =  str_replace(',', '', $str);
	return ((float)$f_dot + 0);
}

/**
 * check format date ymd
 * @param  date $date 
 * @return boolean       
 */
function dmg_check_format_date_ymd($date)
{
	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
		return true;
	} else {
		return false;
	}
}
/**
 * check format date
 * @param  date $date 
 * @return boolean 
 */
function dmg_check_format_date($date)
{
	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4]):?((0|[0-5][0-9]):?(0|[0-5][0-9])|6000|60:00)$/", $date)) {
		return true;
	} else {
		return false;
	}
}
/**
 * format date
 * @param  date $date     
 * @return date           
 */
function dmg_format_date($date)
{
	if (!dmg_check_format_date_ymd($date)) {
		$date = to_sql_date($date);
	}
	return $date;
}

/**
 * format date time
 * @param  date $date     
 * @return date           
 */
function dmg_format_date_time($date)
{
	if (!dmg_check_format_date($date)) {
		$date = to_sql_date($date, true);
	}
	return $date;
}

/**
 * get file type
 * @param  integer $id 
 * @return integer     
 */
function dmg_get_file_type($id)
{
	$CI = &get_instance();
	$CI->db->select('filetype');
	$CI->db->where('id', $id);
	$data = $CI->db->get(db_prefix() . 'dmg_items')->row();
	if ($data) {
		return $data->filetype;
	}
	return '';
}

/**
 * get permission item share to me
 * @param  integer $id 
 * @return integer     
 */
function get_permission_item_share_to_me($id)
{
	$CI = &get_instance();
	return $CI->document_management_model->get_permission_item_share_to_me($id);
}

/**
 * check share permission
 * @param  integer $item_id    
 * @param  string $permission 
 * @return boolean             
 */
function check_share_permission($item_id, $permission = 'preview', $creator_type = 'staff')
{
	$CI = &get_instance();
	$data_item = $CI->document_management_model->get_permission_item_share_to_me($item_id, $creator_type);
	if ($data_item) {
		return in_array($permission, $data_item);
	} else {
		$data_item = $CI->document_management_model->get_item($item_id, '', 'parent_id');
		if ($data_item) {
			return check_share_permission($data_item->parent_id, $permission, $creator_type);
		} else {
			return false;
		}
	}
}

/**
 * space to nbsp
 */
function dmg_space_to_nbsp($data)
{
	$exp = "/((?:<\\/?\\w+)(?:\\s+\\w+(?:\\s*=\\s*(?:\\\".*?\\\"|'.*?'|[^'\\\">\\s]+)?)+\\s*|\\s*)\\/?>)([^<]*)?/";
	$ex1 = "/^([^<>]*)(<?)/i";
	$ex2 = "/(>)([^<>]*)$/i";
	$data = preg_replace_callback($exp, function ($matches) {
		return $matches[1] . str_replace(" ", "&nbsp;", $matches[2]);
	}, $data);
	$data = preg_replace_callback($ex1, function ($matches) {
		return str_replace(" ", "&nbsp;", $matches[1]) . $matches[2];
	}, $data);
	$data = preg_replace_callback($ex2, function ($matches) {
		return $matches[1] . str_replace(" ", "&nbsp;", $matches[2]);
	}, $data);
	return $data;
}

function ufirst($string)
{
	return ucfirst($string ?? '');
}
function nlbr($string)
{
	return nl2br($string ?? '');
}
function htmldecode($string)
{
	return html_entity_decode($string ?? '');
}

/**
 * get client IP
 * @return string
 */
function doc_get_client_ip()
{
	//whether ip is from the share internet
	$ip = '';
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
function update_document_last_action($id)
{
	$CI = &get_instance();
	if (!empty($id)) {
		// First update the current item
		$CI->db->where('id', $id);
		$CI->db->update(db_prefix() . 'dmg_items', [
			'last_action' => get_staff_user_id()
		]);

		// Check if this item has any parents that need updating
		$current_id = $id;
		$max_depth = 10; // Prevent infinite loops in case of circular references
		$processed_ids = [$id]; // Track processed IDs to avoid duplicates

		while ($max_depth-- > 0) {
			// Get parent of current item
			$CI->db->select('parent_id');
			$CI->db->where('id', $current_id);
			$parent = $CI->db->get(db_prefix() . 'dmg_items')->row();

			// If no parent or parent_id is 0, we're done
			if (empty($parent) || $parent->parent_id <= 0) {
				break;
			}

			// Avoid processing the same ID twice
			if (in_array($parent->parent_id, $processed_ids)) {
				break;
			}

			// Update the parent's last_action
			$CI->db->where('id', $parent->parent_id);
			$CI->db->update(db_prefix() . 'dmg_items', [
				'last_action' => get_staff_user_id()
			]);

			// Add to processed IDs and move up the tree
			$processed_ids[] = $parent->parent_id;
			$current_id = $parent->parent_id;
		}
	}
	return true;
}

function add_dmg_activity_log($id)
{
	$CI = &get_instance();
	if (!empty($id)) {
		$CI->db->where('id', $id);
		$dmg_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

		if (!empty($dmg_item)) {
			// Build the complete path by traversing parent hierarchy
			$path_names = [];
			$current_item = $dmg_item;

			// Start with current item name
			$current_name = $current_item->name;

			// Traverse up the parent hierarchy until parent_id = 0 or no parent found
			while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
				$CI->db->where('id', $current_item->parent_id);
				$parent_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

				if (!empty($parent_item) && $parent_item->parent_id != 0) {
					$path_names[] = $parent_item->name;
					$current_item = $parent_item;
				} else {
					break; // Parent not found, exit loop
				}
			}

			// Reverse the array to show from top-level parent to current item
			$path_names = array_reverse($path_names);

			// Build the description with the complete path
			$path_string = implode(' > ', $path_names);
			$description = "Document <b>" . $current_name . "</b> has been uploaded at <b>" . $path_string . "</b>.";
			$CI->load->model('projects_model');
			$project_id = get_default_project();
			$CI->db->insert(db_prefix() . 'module_activity_log', [
				'module_name' => 'dmg',
				'description' => $description,
				'date' => date('Y-m-d H:i:s'),
				'staffid' => get_staff_user_id(),
				'project_id' => $project_id
			]);
		}
	}
	return true;
}

function duplicate_dmg_activity_log($parent_id, $item_id)
{
	$CI = &get_instance();
	if (!empty($item_id)) {
		$CI->db->where('id', $item_id);
		$dmg_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

		if (!empty($dmg_item)) {
			// Build the complete path for source item (item_id)
			$source_path_names = [];
			$current_item = $dmg_item;

			// Start with current item name
			$current_name = $current_item->name;

			// Traverse up the parent hierarchy until parent_id = 0 or no parent found
			while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
				$CI->db->where('id', $current_item->parent_id);
				$parent_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

				if (!empty($parent_item) && $parent_item->parent_id != 0) {
					$source_path_names[] = $parent_item->name;
					$current_item = $parent_item;
				} else {
					break; // Parent not found, exit loop
				}
			}

			// Reverse the array to show from top-level parent to current item
			$source_path_names = array_reverse($source_path_names);
			$source_path_string = implode(' > ', $source_path_names);

			// Build the complete path for destination folder (parent_id)
			$destination_path_names = [];
			if (!empty($parent_id) && $parent_id != 0) {
				$CI->db->where('id', $parent_id);
				$parent_folder = $CI->db->get(db_prefix() . 'dmg_items')->row();

				if (!empty($parent_folder)) {
					$current_parent = $parent_folder;

					// Start with parent folder name
					$destination_path_names[] = $current_parent->name;

					// Traverse up the parent hierarchy until parent_id = 0 or no parent found
					while (!empty($current_parent->parent_id) && $current_parent->parent_id != 0) {
						$CI->db->where('id', $current_parent->parent_id);
						$grandparent_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

						if (!empty($grandparent_item) && $grandparent_item->parent_id != 0) {
							$destination_path_names[] = $grandparent_item->name;
							$current_parent = $grandparent_item;
						} else {
							break; // Parent not found, exit loop
						}
					}

					// Reverse the array to show from top-level parent to current parent
					$destination_path_names = array_reverse($destination_path_names);
				}
			}

			$destination_path_string = implode(' > ', $destination_path_names);

			// Build the description with both paths
			$description = "Document <b>" . $current_name . "</b> has been duplicated from <b>" . $source_path_string . "</b> to <b>" . $destination_path_string . "</b>.";
			$CI->load->model('projects_model');
			$project_id = get_default_project();
			$CI->db->insert(db_prefix() . 'module_activity_log', [
				'module_name' => 'dmg',
				'description' => $description,
				'date' => date('Y-m-d H:i:s'),
				'staffid' => get_staff_user_id(),
				'project_id' => $project_id
			]);
		}
	}
	return true;
}

function moved_dmg_activity_log($data_item, $insert_id)
{
	$CI = &get_instance();
	if (!empty($insert_id)) {
		$CI->db->where('id', $insert_id);
		$dmg_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

		if (!empty($dmg_item)) {
			// Build the complete path for new location (destination)
			$destination_path_names = [];
			$current_item = $dmg_item;

			// Start with current item name
			$current_name = $current_item->name;

			// Traverse up the parent hierarchy until parent_id = 0 or no parent found
			while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
				$CI->db->where('id', $current_item->parent_id);
				$parent_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

				if (!empty($parent_item) && $parent_item->parent_id != 0) {
					$destination_path_names[] = $parent_item->name;
					$current_item = $parent_item;
				} else {
					break; // Parent not found, exit loop
				}
			}

			// Reverse the array to show from top-level parent to current item
			$destination_path_names = array_reverse($destination_path_names);
			$destination_path_string = implode(' > ', $destination_path_names);

			// Build the complete path for old location (source from $data_item)
			$source_path_names = [];
			if (!empty($data_item->parent_id) && $data_item->parent_id != 0) {
				$CI->db->where('id', $data_item->parent_id);
				$old_parent = $CI->db->get(db_prefix() . 'dmg_items')->row();

				if (!empty($old_parent)) {
					$current_old_parent = $old_parent;

					// Start with old parent name
					$source_path_names[] = $current_old_parent->name;

					// Traverse up the parent hierarchy until parent_id = 0 or no parent found
					while (!empty($current_old_parent->parent_id) && $current_old_parent->parent_id != 0) {
						$CI->db->where('id', $current_old_parent->parent_id);
						$old_grandparent = $CI->db->get(db_prefix() . 'dmg_items')->row();

						if (!empty($old_grandparent) && $old_grandparent->parent_id != 0) {
							$source_path_names[] = $old_grandparent->name;
							$current_old_parent = $old_grandparent;
						} else {
							break; // Parent not found, exit loop
						}
					}

					// Reverse the array to show from top-level parent to old parent
					$source_path_names = array_reverse($source_path_names);
				}
			}

			$source_path_string = implode(' > ', $source_path_names);

			// Build the description with both paths
			$description = "Document <b>" . $current_name . "</b> has been moved from <b>" . $source_path_string . "</b> to <b>" . $destination_path_string . "</b>.";
			$CI->load->model('projects_model');
			$project_id = get_default_project();
			$CI->db->insert(db_prefix() . 'module_activity_log', [
				'module_name' => 'dmg',
				'description' => $description,
				'date' => date('Y-m-d H:i:s'),
				'staffid' => get_staff_user_id(),
				'project_id' => $project_id
			]);
		}
	}
	return true;
}

function update_dmg_activity_log($id, $data, $original_data)
{
	$CI = &get_instance();

	if (!empty($id)) {
		$CI->db->where('id', $id);
		$dmg_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

		if (!empty($dmg_item)) {
			$changes = [];

			// Define fields to track
			$tracked_fields = [
				'name',
				'signed_by',
				'dateadded',
				'duedate',
				'ocr_language',
				'document_number',
				'note'
			];

			foreach ($tracked_fields as $field) {
				if (isset($data[$field]) && array_key_exists($field, $original_data)) {
					$new_value = $data[$field];
					$old_value = $original_data[$field];

					// Check if the value has actually changed
					if ($new_value != $old_value) {

						// Format values for display - handle empty/0 values
						$formatted_old = format_value_for_display_dmg($old_value, $field);
						$formatted_new = format_value_for_display_dmg($new_value, $field);

						$changes[] = [
							'field' => $field,
							'old' => $formatted_old,
							'new' => $formatted_new
						];
					}
				}
			}

			// If there are changes, log them
			if (!empty($changes)) {
				$change_descriptions = [];
				foreach ($changes as $change) {
					$field_name = str_replace('_', ' ', ucfirst($change['field']));
					$change_descriptions[] = "{$field_name} changed from '{$change['old']}' to '{$change['new']}'";
				}

				// Build hierarchical path for the item
				$path_names = [];
				$current_item = $dmg_item;

				// Start with current item name
				$current_item_name = $current_item->name;

				// Traverse up the parent hierarchy
				while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
					$parent = $CI->db->where('id', $current_item->parent_id)->get(db_prefix() . 'dmg_items')->row();
					if (!empty($parent) && $parent->parent_id != 0) {
						$path_names[] = $parent->name;
						$current_item = $parent;
					} else {
						break;
					}
				}

				// Reverse the array to show from top-level parent to current item
				$path_names = array_reverse($path_names);
				$path_string = implode(' > ', $path_names);

				// Add current item to the path if it's not empty
				if (!empty($path_string)) {
					$full_path = $path_string;
				} else {
					$full_path = $current_item_name;
				}

				if ($dmg_item->filetype == 'folder') {
					$description = "Folder <b>{$dmg_item->name}</b> located at <b>{$full_path}</b> has been updated. Changes: <b>" . implode(', ', $change_descriptions) . "</b>";
				} else {
					$description = "Document <b>{$dmg_item->name}</b> located at <b>{$full_path}</b> has been updated. Changes: <b>" . implode(', ', $change_descriptions) . "</b>";
				}

				$CI->load->model('projects_model');
				$project_id = get_default_project();
				$CI->db->insert(db_prefix() . 'module_activity_log', [
					'module_name' => 'dmg',
					'description' => $description,
					'date' => date('Y-m-d H:i:s'),
					'staffid' => get_staff_user_id(),
					'project_id' => $project_id
				]);
			}
		}
	}
	return true;
}

function format_value_for_display_dmg($value, $field)
{
	// Check if value is empty, null, or 0
	if ($value === '' || $value === null || $value === 0 || $value === '0') {
		return 'Empty';
	}

	// Handle specific field types
	switch ($field) {


		case 'dateadded':
			// Handle date fields - if empty date, return Empty
			if ($value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
				return 'Empty';
			}
			return $value;
		case 'duedate':
			// Handle date fields - if empty date, return Empty
			if ($value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
				return 'Empty';
			}
			return $value;

		default:
			return $value;
	}
}

function create_folder_dmg_activity_log($id)
{
	$CI = &get_instance();
	if (!empty($id)) {
		$CI->db->where('id', $id);
		$dmg_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

		if (!empty($dmg_item)) {
			// Build the complete path by traversing parent hierarchy
			$path_names = [];
			$current_item = $dmg_item;

			// Start with current item name
			$current_name = $current_item->name;

			// Traverse up the parent hierarchy until parent_id = 0 or no parent found
			while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
				$CI->db->where('id', $current_item->parent_id);
				$parent_item = $CI->db->get(db_prefix() . 'dmg_items')->row();

				if (!empty($parent_item) && $parent_item->parent_id != 0) {
					$path_names[] = $parent_item->name;
					$current_item = $parent_item;
				} else {
					break; // Parent not found, exit loop
				}
			}

			// Reverse the array to show from top-level parent to current item
			$path_names = array_reverse($path_names);

			// Build the description with the complete path
			$path_string = implode(' > ', $path_names);
			$description = "Folder <b>" . $current_name . "</b> has been created at <b>" . $path_string . "</b>.";
			$CI->load->model('projects_model');
			$project_id = get_default_project();
			$CI->db->insert(db_prefix() . 'module_activity_log', [
				'module_name' => 'dmg',
				'description' => $description,
				'date' => date('Y-m-d H:i:s'),
				'staffid' => get_staff_user_id(),
				'project_id' => $project_id
			]);
		}
	}
	return true;
}

function create_project_based_order_documents_folder()
{
    $CI = &get_instance();
    $CI->load->model('document_management/document_management_model');
    $order_documents_title = 'Order Documents';
    $CI->db->select('id, name');
    $CI->db->from(db_prefix() . 'projects');
    $CI->db->order_by('id', 'asc');
    $projects = $CI->db->get()->result_array();
    if (empty($projects)) {
        return true;
    }
    foreach ($projects as $project) {
        $CI->db->select('id, name');
        $CI->db->from(db_prefix() . 'dmg_items');
        $CI->db->where('name', $project['name']);
        $project_item = $CI->db->get()->row();
        if (empty($project_item)) {
            continue;
        }
        $CI->db->select('id, name');
        $CI->db->from(db_prefix() . 'dmg_items');
        $CI->db->where('name', $order_documents_title);
        $CI->db->where('parent_id', $project_item->id);
        $order_documents_item = $CI->db->get()->row();
        if (empty($order_documents_item)) {
            $CI->document_management_model->create_item([
                'parent_id' => $project_item->id,
                'name' => $order_documents_title,
            ]);
        }
    }
    return true;
}

function create_pur_order_folders_in_documents($rel_id, $rel_type)
{
	$CI = &get_instance();
	if (!in_array($rel_type, ['pur_order', 'wo_order'])) {
        return '';
    }
	$default_project = get_default_project();
	$CI->db->select('id, name');
    $CI->db->from(db_prefix() . 'dmg_items');
    $CI->db->where('project_id', $default_project);
    $default_project_item = $CI->db->get()->row();
    if(!empty($default_project_item)) {
    	$CI->db->select('id, name');
	    $CI->db->from(db_prefix() . 'dmg_items');
	    $CI->db->where('parent_id', $default_project_item->id);
	    $CI->db->where('name', 'Order Documents');
	    $order_documents_item = $CI->db->get()->row();
	    if(!empty($order_documents_item)) {
	    	if($rel_type == 'pur_order') {
	    		$CI->db->where('id', $rel_id);
        		$order_data = $CI->db->get(db_prefix() . 'pur_orders')->row();
        		$po_id = $order_data->id;
        		$wo_id = NULL;
        		$order_number = $order_data->pur_order_number;
        		$order_name = $order_data->pur_order_number . " - " . $order_data->pur_order_name;
	    	} else if($rel_type == 'wo_order') {
	    		$CI->db->where('id', $rel_id);
        		$order_data = $CI->db->get(db_prefix() . 'wo_orders')->row();
        		$wo_id = $order_data->id;
        		$po_id = NULL;
        		$order_number = $order_data->wo_order_number;
        		$order_name = $order_data->wo_order_number . " - " . $order_data->wo_order_name;
	    	} else {
	    		$order_data = array();
	    		$po_id = NULL;
	    		$wo_id = NULL;
	    		$order_number = NULL;
	    		$order_name = NULL;
	    	}
	    	if(!empty($order_data)) {
	    		$CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('rel_id', $rel_id);
				$CI->db->where('rel_type', $rel_type);
				$CI->db->order_by('id', 'ASC');
				$CI->db->limit(1);
				$pur_order_folder = $CI->db->get()->row();
				if(empty($pur_order_folder)) {
			    	$pur_order_folder_id = $CI->document_management_model->create_item([
		                'parent_id' => $order_documents_item->id,
		                'name' => $order_name,
		                'po_id' => $po_id,
		                'wo_id' => $wo_id,
		                'rel_id' => $rel_id,
		                'rel_type' => $rel_type,
		            ]);
		            if($pur_order_folder_id) {
		            	$CI->document_management_model->create_item([
			                'parent_id' => $pur_order_folder_id,
			                'name' => $order_number.'.pdf',
			                'filetype' => 'application/pdf',
			                'po_id' => $po_id,
		                	'wo_id' => $wo_id,
			                'rel_id' => $rel_id,
			                'rel_type' => $rel_type,
			            ]);
		            }
		            return $pur_order_folder_id;
		        } else {
		        	return $pur_order_folder->id;
		        }
	        }
	    }
    }
    return '';
}

function delete_pur_order_folders_in_documents($rel_id, $rel_type)
{
	$CI = &get_instance();
	if (!in_array($rel_type, ['pur_order', 'wo_order'])) {
        return true;
    }
	if($rel_type == 'pur_order') {
		$CI->db->where('po_id', $rel_id);
		$CI->db->delete(db_prefix() . 'dmg_items');
	    return true;
	} else if($rel_type == 'wo_order') {
		$CI->db->where('wo_id', $rel_id);
		$CI->db->delete(db_prefix() . 'dmg_items');
	    return true;
	} else {
    	return true;
    }
}

function create_pur_order_attachments_in_documents($purchase_file_id)
{
	$CI = &get_instance();
	$CI->db->where('id', $purchase_file_id);
	$purchase_file = $CI->db->get(db_prefix() . 'purchase_files')->row();
	if(!empty($purchase_file)) {
	    $rel_id = $purchase_file->rel_id;
	    $rel_type = $purchase_file->rel_type;
		$pur_order_folder_id = create_pur_order_folders_in_documents($rel_id, $rel_type);
		if(!empty($pur_order_folder_id)) {
			$order_attachments_title = 'Order Attachments';
			$CI->db->select('id, name');
			$CI->db->from(db_prefix() . 'dmg_items');
			$CI->db->where('parent_id', $pur_order_folder_id);
			$CI->db->where('name', $order_attachments_title);
			$pur_order_attachments = $CI->db->get()->row();
			if(empty($pur_order_attachments)) {
				$pur_order_attachments_folder_id = $CI->document_management_model->create_item([
		            'parent_id' => $pur_order_folder_id,
		            'name' => $order_attachments_title,
		            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
		            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
		            'rel_id' => $rel_id,
		            'rel_type' => $rel_type,
		        ]);
			} else {
				$pur_order_attachments_folder_id = $pur_order_attachments->id;
			}
			$CI->document_management_model->create_item([
		        'parent_id' => $pur_order_attachments_folder_id,
		        'name' => $purchase_file->file_name,
		        'filetype' => $purchase_file->filetype,
		        'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
	            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
		        'rel_id' => $purchase_file->id,
		        'rel_type' => $rel_type.'_attachment',
		    ]);
		}
	}
	return '';
}

function delete_pur_order_attachments_in_documents($id, $rel_type)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', $rel_type.'_attachment');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}

function create_payment_certificates_in_documents($payment_certificate_id)
{
	$CI = &get_instance();
	$CI->db->where('id', $payment_certificate_id);
	$payment_certificate = $CI->db->get(db_prefix() . 'payment_certificate')->row();
	if(!empty($payment_certificate)) {
		if(!empty($payment_certificate->po_id)) {
			$rel_id = $payment_certificate->po_id;
	    	$rel_type = 'pur_order';
		} else if(!empty($payment_certificate->wo_id)) {
			$rel_id = $payment_certificate->wo_id;
	    	$rel_type = 'wo_order';
		} else {
			$rel_id = NULL;
	    	$rel_type = NULL;
		}
		if(!empty($rel_id) && !empty($rel_type)) {
			$pur_order_folder_id = create_pur_order_folders_in_documents($rel_id, $rel_type);
			if(!empty($pur_order_folder_id)) {
				$payment_certificate_title = 'Payment certificates';
				$CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('parent_id', $pur_order_folder_id);
				$CI->db->where('name', $payment_certificate_title);
				$payment_certificate_item = $CI->db->get()->row();
				if(empty($payment_certificate_item)) {
					$payment_certificate_item_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $pur_order_folder_id,
			            'name' => $payment_certificate_title,
			            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $rel_id,
			            'rel_type' => $rel_type,
			        ]);
			    } else {
			    	$payment_certificate_item_folder_id = $payment_certificate_item->id;
			    }
			    $CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('filetype', 'folder');
				$CI->db->where('rel_id', $payment_certificate->id);
				$CI->db->where('rel_type', 'payment_certificate');
				$CI->db->order_by('id', 'ASC');
				$CI->db->limit(1);
				$pc_number_item_folder = $CI->db->get()->row();
				if(empty($pc_number_item_folder)) {
			        $pc_number_item_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $payment_certificate_item_folder_id,
			            'name' => $payment_certificate->pc_number,
			            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $payment_certificate->id,
			            'rel_type' => 'payment_certificate',
			        ]);
			        $CI->document_management_model->create_item([
		                'parent_id' => $pc_number_item_folder_id,
		                'name' => $payment_certificate->pc_number.'.pdf',
		                'filetype' => 'application/pdf',
		                'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $payment_certificate->id,
			            'rel_type' => 'payment_certificate',
		            ]);
		            return $pc_number_item_folder_id;
		        } else {
		        	return $pc_number_item_folder->id;
		        }
			}
		}
	}
	return '';
}

function delete_payment_certificate_in_documents($id)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$payment_certificate_files = $CI->db->get(db_prefix() . 'payment_certificate_files')->result_array();
	if(!empty($payment_certificate_files)) {
		$file_ids = array_column($payment_certificate_files, 'id');
		if (!empty($file_ids)) {
			$CI->db->where_in('rel_id', $file_ids);
            $CI->db->where('rel_type', 'payment_certificate_attachment');
            $CI->db->delete(db_prefix() . 'dmg_items');
        }
	}
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', 'payment_certificate');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}

function create_payment_certificate_attachments_in_documents($payment_certificate_file_id)
{
	$attachments_title = 'Attachments';
	$CI = &get_instance();
	$CI->db->where('id', $payment_certificate_file_id);
	$payment_certificate_file = $CI->db->get(db_prefix() . 'payment_certificate_files')->row();
	if(!empty($payment_certificate_file)) {
		$CI->db->where('id', $payment_certificate_file->rel_id);
		$payment_certificate = $CI->db->get(db_prefix() . 'payment_certificate')->row();
		if(!empty($payment_certificate->po_id)) {
			$order_rel_id = $payment_certificate->po_id;
	    	$order_rel_type = 'pur_order';
		} else if(!empty($payment_certificate->wo_id)) {
			$order_rel_id = $payment_certificate->wo_id;
	    	$order_rel_type = 'wo_order';
		} else {
			$order_rel_id = NULL;
	    	$order_rel_type = NULL;
		}
		if(!empty($order_rel_id) && !empty($order_rel_type)) {
			$CI->db->select('id, name');
			$CI->db->from(db_prefix() . 'dmg_items');
			$CI->db->where('rel_id', $payment_certificate_file->rel_id);
			$CI->db->where('name !=', $attachments_title);
			$CI->db->where('rel_type', 'payment_certificate');
			$CI->db->where('filetype', 'folder');
			$CI->db->order_by('id', 'ASC');
			$CI->db->limit(1);
			$pc_number_item_folder = $CI->db->get()->row();
			if(empty($pc_number_item_folder)) {
				$pc_number_item_folder_id = create_payment_certificates_in_documents($payment_certificate_file->rel_id);
			} else {
				$pc_number_item_folder_id = $pc_number_item_folder->id;
			}
			if(!empty($pc_number_item_folder_id)) {
				$CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('parent_id', $pc_number_item_folder_id);
				$CI->db->where('name', $attachments_title);
				$attachments = $CI->db->get()->row();
				if(empty($attachments)) {
					$attachments_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $pc_number_item_folder_id,
			            'name' => $attachments_title,
			            'po_id' => $order_rel_type == 'pur_order' ? $order_rel_id : NULL,
			            'wo_id' => $order_rel_type == 'wo_order' ? $order_rel_id : NULL,
			            'rel_id' => $payment_certificate_file->rel_id,
			            'rel_type' => 'payment_certificate',
			        ]);
				} else {
					$attachments_folder_id = $attachments->id;
				}
				$CI->document_management_model->create_item([
			        'parent_id' => $attachments_folder_id,
			        'name' => $payment_certificate_file->file_name,
			        'filetype' => $payment_certificate_file->filetype,
			        'po_id' => $order_rel_type == 'pur_order' ? $order_rel_id : NULL,
			        'wo_id' => $order_rel_type == 'wo_order' ? $order_rel_id : NULL,
			        'rel_id' => $payment_certificate_file->id,
			        'rel_type' => 'payment_certificate_attachment',
			    ]);
			}
		}
	}
	return '';
}

function delete_payment_certificate_attachments_in_documents($id)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', 'payment_certificate_attachment');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}

function create_pur_bill_in_documents($pur_bill_id)
{
	$CI = &get_instance();
	$CI->db->where('id', $pur_bill_id);
	$pur_bill = $CI->db->get(db_prefix() . 'pur_bills')->row();
	if(!empty($pur_bill)) {
		if(!empty($pur_bill->pur_order)) {
			$rel_id = $pur_bill->pur_order;
	    	$rel_type = 'pur_order';
		} else if(!empty($pur_bill->wo_order)) {
			$rel_id = $pur_bill->wo_order;
	    	$rel_type = 'wo_order';
		} else {
			$rel_id = NULL;
	    	$rel_type = NULL;
		}
		if(!empty($rel_id) && !empty($rel_type)) {
			$pur_order_folder_id = create_pur_order_folders_in_documents($rel_id, $rel_type);
			if(!empty($pur_order_folder_id)) {
				$pur_bill_title = 'Bill Bifurcation';
				$pur_bill_item_folder_id = $CI->document_management_model->create_item([
		            'parent_id' => $pur_order_folder_id,
		            'name' => $pur_bill_title,
		            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
		            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
		            'rel_id' => $rel_id,
		            'rel_type' => $rel_type,
		        ]);
		        $CI->document_management_model->create_item([
	                'parent_id' => $pur_bill_item_folder_id,
	                'name' => $pur_bill->bill_code.'.pdf',
	                'filetype' => 'application/pdf',
	                'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
		            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
		            'rel_id' => $pur_bill->id,
		            'rel_type' => 'pur_bill',
	            ]);
			}
		}
	}
	return '';
}

function delete_pur_bill_in_documents($id)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', 'pur_bill');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}

function create_co_order_in_documents($co_order_id)
{
	$CI = &get_instance();
	$CI->db->where('id', $co_order_id);
	$co_order = $CI->db->get(db_prefix() . 'co_orders')->row();
	if(!empty($co_order)) {
		if(!empty($co_order->po_order_id)) {
			$rel_id = $co_order->po_order_id;
	    	$rel_type = 'pur_order';
		} else if(!empty($co_order->wo_order_id)) {
			$rel_id = $co_order->wo_order_id;
	    	$rel_type = 'wo_order';
		} else {
			$rel_id = NULL;
	    	$rel_type = NULL;
		}
		if(!empty($rel_id) && !empty($rel_type)) {
			$pur_order_folder_id = create_pur_order_folders_in_documents($rel_id, $rel_type);
			if(!empty($pur_order_folder_id)) {
				$change_order_title = 'Change orders';
				$CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('parent_id', $pur_order_folder_id);
				$CI->db->where('name', $change_order_title);
				$change_order_item = $CI->db->get()->row();
				if(empty($change_order_item)) {
					$change_order_item_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $pur_order_folder_id,
			            'name' => $change_order_title,
			            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $rel_id,
			            'rel_type' => $rel_type,
			        ]);
			    } else {
			    	$change_order_item_folder_id = $change_order_item->id;
			    }
			    $CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('filetype', 'folder');
				$CI->db->where('rel_id', $co_order->id);
				$CI->db->where('rel_type', 'co_order');
				$CI->db->order_by('id', 'ASC');
				$CI->db->limit(1);
				$co_number_item_folder = $CI->db->get()->row();
				if(empty($co_number_item_folder)) {
			        $co_number_item_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $change_order_item_folder_id,
			            'name' => $co_order->pur_order_number,
			            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $co_order->id,
			            'rel_type' => 'co_order',
			        ]);
			        $CI->document_management_model->create_item([
		                'parent_id' => $co_number_item_folder_id,
		                'name' => $co_order->pur_order_number.'.pdf',
		                'filetype' => 'application/pdf',
		                'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $co_order->id,
			            'rel_type' => 'co_order',
		            ]);
		            return $co_number_item_folder_id;
		        } else {
		        	return $co_number_item_folder->id;
		        }
			}
		}
	}
	return '';
}

function delete_co_order_in_documents($id)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$changee_files = $CI->db->get(db_prefix() . 'changee_files')->result_array();
	if(!empty($changee_files)) {
		$file_ids = array_column($changee_files, 'id');
		if (!empty($file_ids)) {
			$CI->db->where_in('rel_id', $file_ids);
            $CI->db->where('rel_type', 'co_order_attachment');
            $CI->db->delete(db_prefix() . 'dmg_items');
        }
	}
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', 'co_order');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}

function create_co_order_attachments_in_documents($co_order_file_id)
{
	$attachments_title = 'Attachments';
	$CI = &get_instance();
	$CI->db->where('id', $co_order_file_id);
	$co_order_file = $CI->db->get(db_prefix() . 'changee_files')->row();
	if(!empty($co_order_file)) {
		$CI->db->where('id', $co_order_file->rel_id);
		$co_order = $CI->db->get(db_prefix() . 'co_orders')->row();
		if(!empty($co_order->po_order_id)) {
			$order_rel_id = $co_order->po_order_id;
	    	$order_rel_type = 'pur_order';
		} else if(!empty($co_order->wo_order_id)) {
			$order_rel_id = $co_order->wo_order_id;
	    	$order_rel_type = 'wo_order';
		} else {
			$order_rel_id = NULL;
	    	$order_rel_type = NULL;
		}
		if(!empty($order_rel_id) && !empty($order_rel_type)) {
			$CI->db->select('id, name');
			$CI->db->from(db_prefix() . 'dmg_items');
			$CI->db->where('rel_id', $co_order_file->rel_id);
			$CI->db->where('name !=', $attachments_title);
			$CI->db->where('rel_type', 'co_order');
			$CI->db->where('filetype', 'folder');
			$CI->db->order_by('id', 'ASC');
			$CI->db->limit(1);
			$co_number_item_folder = $CI->db->get()->row();
			if(empty($co_number_item_folder)) {
				$co_number_item_folder_id = create_co_order_in_documents($co_order_file->rel_id);
			} else {
				$co_number_item_folder_id = $co_number_item_folder->id;
			}
			if(!empty($co_number_item_folder_id)) {
				$CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('parent_id', $co_number_item_folder_id);
				$CI->db->where('name', $attachments_title);
				$attachments = $CI->db->get()->row();
				if(empty($attachments)) {
					$attachments_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $co_number_item_folder_id,
			            'name' => $attachments_title,
			            'po_id' => $order_rel_type == 'pur_order' ? $order_rel_id : NULL,
			            'wo_id' => $order_rel_type == 'wo_order' ? $order_rel_id : NULL,
			            'rel_id' => $co_order_file->rel_id,
			            'rel_type' => 'co_order',
			        ]);
				} else {
					$attachments_folder_id = $attachments->id;
				}
				$CI->document_management_model->create_item([
			        'parent_id' => $attachments_folder_id,
			        'name' => $co_order_file->file_name,
			        'filetype' => $co_order_file->filetype,
			        'po_id' => $order_rel_type == 'pur_order' ? $order_rel_id : NULL,
			        'wo_id' => $order_rel_type == 'wo_order' ? $order_rel_id : NULL,
			        'rel_id' => $co_order_file->id,
			        'rel_type' => 'co_order_attachment',
			    ]);
			}
		}
	}
	return '';
}

function delete_co_order_attachments_in_documents($id)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', 'co_order_attachment');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}

function create_goods_receipt_in_documents($goods_receipt_id)
{
	$CI = &get_instance();
	$CI->db->where('id', $goods_receipt_id);
	$goods_receipt = $CI->db->get(db_prefix() . 'goods_receipt')->row();
	if(!empty($goods_receipt)) {
		if(!empty($goods_receipt->pr_order_id)) {
			$rel_id = $goods_receipt->pr_order_id;
	    	$rel_type = 'pur_order';
		} else if(!empty($goods_receipt->wo_order_id)) {
			$rel_id = $goods_receipt->wo_order_id;
	    	$rel_type = 'wo_order';
		} else {
			$rel_id = NULL;
	    	$rel_type = NULL;
		}
		if(!empty($rel_id) && !empty($rel_type)) {
			$pur_order_folder_id = create_pur_order_folders_in_documents($rel_id, $rel_type);
			if(!empty($pur_order_folder_id)) {
				$stock_received_title = 'Stock Received';
				$CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('parent_id', $pur_order_folder_id);
				$CI->db->where('name', $stock_received_title);
				$stock_received_item = $CI->db->get()->row();
				if(empty($stock_received_item)) {
					$stock_received_item_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $pur_order_folder_id,
			            'name' => $stock_received_title,
			            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $rel_id,
			            'rel_type' => $rel_type,
			        ]);
			    } else {
			    	$stock_received_item_folder_id = $stock_received_item->id;
			    }
			    $CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('filetype', 'folder');
				$CI->db->where('rel_id', $goods_receipt->id);
				$CI->db->where('rel_type', 'goods_receipt');
				$CI->db->order_by('id', 'ASC');
				$CI->db->limit(1);
				$gr_number_item_folder = $CI->db->get()->row();
				if(empty($gr_number_item_folder)) {
			        $gr_number_item_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $stock_received_item_folder_id,
			            'name' => $goods_receipt->goods_receipt_code,
			            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $goods_receipt->id,
			            'rel_type' => 'goods_receipt',
			        ]);
			        $CI->document_management_model->create_item([
		                'parent_id' => $gr_number_item_folder_id,
		                'name' => $goods_receipt->goods_receipt_code.'.pdf',
		                'filetype' => 'application/pdf',
		                'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $goods_receipt->id,
			            'rel_type' => 'goods_receipt',
		            ]);
		            return $gr_number_item_folder_id;
		        } else {
		        	return $gr_number_item_folder->id;
		        }
			}
		}
	}
	return '';
}

function delete_goods_receipt_in_documents($id)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$invetory_files = $CI->db->get(db_prefix() . 'invetory_files')->result_array();
	if(!empty($invetory_files)) {
		$file_ids = array_column($invetory_files, 'id');
		if (!empty($file_ids)) {
			$CI->db->where_in('rel_id', $file_ids);
            $CI->db->where('rel_type', 'goods_receipt_attachment');
            $CI->db->delete(db_prefix() . 'dmg_items');
        }
	}
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', 'goods_receipt');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}

function create_goods_receipt_attachments_in_documents($goods_receipt_file_id)
{
	$attachments_title = 'Attachments';
	$CI = &get_instance();
	$CI->db->where('id', $goods_receipt_file_id);
	$goods_receipt_file = $CI->db->get(db_prefix() . 'invetory_files')->row();
	if(!empty($goods_receipt_file)) {
		$CI->db->where('id', $goods_receipt_file->rel_id);
		$goods_receipt = $CI->db->get(db_prefix() . 'goods_receipt')->row();
		if(!empty($goods_receipt->pr_order_id)) {
			$order_rel_id = $goods_receipt->pr_order_id;
	    	$order_rel_type = 'pur_order';
		} else if(!empty($goods_receipt->wo_order_id)) {
			$order_rel_id = $goods_receipt->wo_order_id;
	    	$order_rel_type = 'wo_order';
		} else {
			$order_rel_id = NULL;
	    	$order_rel_type = NULL;
		}
		if(!empty($order_rel_id) && !empty($order_rel_type)) {
			$CI->db->select('id, name');
			$CI->db->from(db_prefix() . 'dmg_items');
			$CI->db->where('rel_id', $goods_receipt_file->rel_id);
			$CI->db->where('name !=', $attachments_title);
			$CI->db->where('rel_type', 'goods_receipt');
			$CI->db->where('filetype', 'folder');
			$CI->db->order_by('id', 'ASC');
			$CI->db->limit(1);
			$gr_number_item_folder = $CI->db->get()->row();
			if(empty($gr_number_item_folder)) {
				$gr_number_item_folder_id = create_goods_receipt_in_documents($goods_receipt_file->rel_id);
			} else {
				$gr_number_item_folder_id = $gr_number_item_folder->id;
			}
			if(!empty($gr_number_item_folder_id)) {
				$CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('parent_id', $gr_number_item_folder_id);
				$CI->db->where('name', $attachments_title);
				$attachments = $CI->db->get()->row();
				if(empty($attachments)) {
					$attachments_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $gr_number_item_folder_id,
			            'name' => $attachments_title,
			            'po_id' => $order_rel_type == 'pur_order' ? $order_rel_id : NULL,
			            'wo_id' => $order_rel_type == 'wo_order' ? $order_rel_id : NULL,
			            'rel_id' => $goods_receipt_file->rel_id,
			            'rel_type' => 'goods_receipt',
			        ]);
				} else {
					$attachments_folder_id = $attachments->id;
				}
				$CI->document_management_model->create_item([
			        'parent_id' => $attachments_folder_id,
			        'name' => $goods_receipt_file->file_name,
			        'filetype' => $goods_receipt_file->filetype,
			        'po_id' => $order_rel_type == 'pur_order' ? $order_rel_id : NULL,
			        'wo_id' => $order_rel_type == 'wo_order' ? $order_rel_id : NULL,
			        'rel_id' => $goods_receipt_file->id,
			        'rel_type' => 'goods_receipt_attachment',
			    ]);
			}
		}
	}
	return '';
}

function delete_goods_receipt_attachments_in_documents($id)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', 'goods_receipt_attachment');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}

function create_goods_delivery_in_documents($goods_delivery_id)
{
	$CI = &get_instance();
	$CI->db->where('id', $goods_delivery_id);
	$goods_delivery = $CI->db->get(db_prefix() . 'goods_delivery')->row();
	if(!empty($goods_delivery)) {
		if(!empty($goods_delivery->pr_order_id)) {
			$rel_id = $goods_delivery->pr_order_id;
	    	$rel_type = 'pur_order';
		} else if(!empty($goods_delivery->wo_order_id)) {
			$rel_id = $goods_delivery->wo_order_id;
	    	$rel_type = 'wo_order';
		} else {
			$rel_id = NULL;
	    	$rel_type = NULL;
		}
		if(!empty($rel_id) && !empty($rel_type)) {
			$pur_order_folder_id = create_pur_order_folders_in_documents($rel_id, $rel_type);
			if(!empty($pur_order_folder_id)) {
				$stock_issued_title = 'Stock Issued';
				$CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('parent_id', $pur_order_folder_id);
				$CI->db->where('name', $stock_issued_title);
				$stock_issued_item = $CI->db->get()->row();
				if(empty($stock_issued_item)) {
					$stock_issued_item_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $pur_order_folder_id,
			            'name' => $stock_issued_title,
			            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $rel_id,
			            'rel_type' => $rel_type,
			        ]);
			    } else {
			    	$stock_issued_item_folder_id = $stock_issued_item->id;
			    }
			    $CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('filetype', 'folder');
				$CI->db->where('rel_id', $goods_delivery->id);
				$CI->db->where('rel_type', 'goods_delivery');
				$CI->db->order_by('id', 'ASC');
				$CI->db->limit(1);
				$gd_number_item_folder = $CI->db->get()->row();
				if(empty($gd_number_item_folder)) {
			        $gd_number_item_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $stock_issued_item_folder_id,
			            'name' => $goods_delivery->goods_delivery_code,
			            'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $goods_delivery->id,
			            'rel_type' => 'goods_delivery',
			        ]);
			        $CI->document_management_model->create_item([
		                'parent_id' => $gd_number_item_folder_id,
		                'name' => $goods_delivery->goods_delivery_code.'.pdf',
		                'filetype' => 'application/pdf',
		                'po_id' => $rel_type == 'pur_order' ? $rel_id : NULL,
			            'wo_id' => $rel_type == 'wo_order' ? $rel_id : NULL,
			            'rel_id' => $goods_delivery->id,
			            'rel_type' => 'goods_delivery',
		            ]);
		            return $gd_number_item_folder_id;
		        } else {
		        	return $gd_number_item_folder->id;
		        }
			}
		}
	}
	return '';
}

function delete_goods_delivery_in_documents($id)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$invetory_files = $CI->db->get(db_prefix() . 'invetory_files')->result_array();
	if(!empty($invetory_files)) {
		$file_ids = array_column($invetory_files, 'id');
		if (!empty($file_ids)) {
			$CI->db->where_in('rel_id', $file_ids);
            $CI->db->where('rel_type', 'goods_delivery_attachment');
            $CI->db->delete(db_prefix() . 'dmg_items');
        }
	}
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', 'goods_delivery');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}

function create_goods_delivery_attachments_in_documents($goods_delivery_file_id)
{
	$attachments_title = 'Attachments';
	$CI = &get_instance();
	$CI->db->where('id', $goods_delivery_file_id);
	$goods_delivery_file = $CI->db->get(db_prefix() . 'invetory_files')->row();
	if(!empty($goods_delivery_file)) {
		$CI->db->where('id', $goods_delivery_file->rel_id);
		$goods_delivery = $CI->db->get(db_prefix() . 'goods_delivery')->row();
		if(!empty($goods_delivery->pr_order_id)) {
			$order_rel_id = $goods_delivery->pr_order_id;
	    	$order_rel_type = 'pur_order';
		} else if(!empty($goods_delivery->wo_order_id)) {
			$order_rel_id = $goods_delivery->wo_order_id;
	    	$order_rel_type = 'wo_order';
		} else {
			$order_rel_id = NULL;
	    	$order_rel_type = NULL;
		}
		if(!empty($order_rel_id) && !empty($order_rel_type)) {
			$CI->db->select('id, name');
			$CI->db->from(db_prefix() . 'dmg_items');
			$CI->db->where('rel_id', $goods_delivery_file->rel_id);
			$CI->db->where('name !=', $attachments_title);
			$CI->db->where('rel_type', 'goods_delivery');
			$CI->db->where('filetype', 'folder');
			$CI->db->order_by('id', 'ASC');
			$CI->db->limit(1);
			$gd_number_item_folder = $CI->db->get()->row();
			if(empty($gd_number_item_folder)) {
				$gd_number_item_folder_id = create_goods_delivery_in_documents($goods_delivery_file->rel_id);
			} else {
				$gd_number_item_folder_id = $gd_number_item_folder->id;
			}
			if(!empty($gd_number_item_folder_id)) {
				$CI->db->select('id, name');
				$CI->db->from(db_prefix() . 'dmg_items');
				$CI->db->where('parent_id', $gd_number_item_folder_id);
				$CI->db->where('name', $attachments_title);
				$attachments = $CI->db->get()->row();
				if(empty($attachments)) {
					$attachments_folder_id = $CI->document_management_model->create_item([
			            'parent_id' => $gd_number_item_folder_id,
			            'name' => $attachments_title,
			            'po_id' => $order_rel_type == 'pur_order' ? $order_rel_id : NULL,
			            'wo_id' => $order_rel_type == 'wo_order' ? $order_rel_id : NULL,
			            'rel_id' => $goods_delivery_file->rel_id,
			            'rel_type' => 'goods_delivery',
			        ]);
				} else {
					$attachments_folder_id = $attachments->id;
				}
				$CI->document_management_model->create_item([
			        'parent_id' => $attachments_folder_id,
			        'name' => $goods_delivery_file->file_name,
			        'filetype' => $goods_delivery_file->filetype,
			        'po_id' => $order_rel_type == 'pur_order' ? $order_rel_id : NULL,
			        'wo_id' => $order_rel_type == 'wo_order' ? $order_rel_id : NULL,
			        'rel_id' => $goods_delivery_file->id,
			        'rel_type' => 'goods_delivery_attachment',
			    ]);
			}
		}
	}
	return '';
}

function delete_goods_delivery_attachments_in_documents($id)
{
	$CI = &get_instance();
	$CI->db->where('rel_id', $id);
	$CI->db->where('rel_type', 'goods_delivery_attachment');
	$CI->db->delete(db_prefix() . 'dmg_items');
	return true;
}