<?php
defined('BASEPATH') or exit('No direct script access allowed');
hooks()->add_action('after_email_templates', 'add_drawing_management_email_templates');

if (!function_exists('add_drawing_management_email_templates')) {
	/**
	 * Init appointly email templates and assign languages
	 * @return void
	 */
	function add_drawing_management_email_templates()
	{
		$CI = &get_instance();

		$data['drawing_management_templates'] = $CI->emails_model->get(['type' => 'drawing_management', 'language' => 'english']);

		$CI->load->view('drawing_management/email_templates', $data);
	}
}

function init_drawing_fist_item($type = 'staff')
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
	if ($CI->db->get(db_prefix() . 'dms_items')->num_rows() == 0) {
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
		$CI->db->insert(db_prefix() . 'dms_items', $data);
	}

	$CI->db->where('creator_id', '0');
	if ($CI->db->get(db_prefix() . 'dms_items')->num_rows() == 0) {
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
		$CI->db->insert(db_prefix() . 'dms_items', $data);
	}

	$CI->db->select('id, name');
	$projects = $CI->db->get(db_prefix() . 'projects')->result_array();
	if (!empty($projects)) {
		foreach ($projects as $key => $value) {
			$CI->db->where('project_id', $value['id']);
			if ($CI->db->get(db_prefix() . 'dms_items')->num_rows() == 0) {
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
				$CI->db->insert(db_prefix() . 'dms_items', $data);
			}
		}
	}
}

function drawing_dmg_get_file_name($id)
{
	$CI = &get_instance();
	$CI->db->select('name');
	$CI->db->where('id', $id);
	$data = $CI->db->get(db_prefix() . 'dms_items')->row();
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
function drawing_dmg_convert_custom_field_value_to_string($value, $type)
{
	$string_content = drawing_dmg_check_content($value);
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
function drawing_dmg_check_content($selected)
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
					$result = drawing_parse_array_multi_to_string($selected_s);
				} else {
					$temp_str = trim($selected_s[0]);
					if ($temp_str != '') {
						$result = implode(', ', $selected_s);
					}
				}
			} else {
				if (is_object($selected_s)) {
					$selected_s = (array)$selected_s;
					$result = drawing_parse_array_multi_to_string($selected_s);
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
function drawing_parse_array_multi_to_string($array)
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
function drawing_dmg_create_folder($path)
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
function drawing_get_audit_log_file($item_id)
{
	$CI = &get_instance();
	$CI->db->where('item_id', $item_id);
	$CI->db->order_by('date', 'desc');
	return $CI->db->get(db_prefix() . 'dms_audit_logs')->result_array();
}

/**
 * check file locked
 * @param  integer $item_id 
 * @return boolean          
 */
function drawing_check_file_locked($item_id)
{
	$CI = &get_instance();
	$CI->db->select('locked, lock_user');
	$CI->db->where('id', $item_id);
	$item = $CI->db->get(db_prefix() . 'dms_items')->row();
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
function drawing_dmg_reformat_currency_asset($str)
{
	$f_dot =  str_replace(',', '', $str);
	return ((float)$f_dot + 0);
}

/**
 * check format date ymd
 * @param  date $date 
 * @return boolean       
 */
function drawing_drawing_dmg_check_format_date_ymd($date)
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
function drawing_dmg_check_format_date($date)
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
function drawing_dmg_format_date($date)
{
	if (!drawing_drawing_dmg_check_format_date_ymd($date)) {
		$date = to_sql_date($date);
	}
	return $date;
}

/**
 * format date time
 * @param  date $date     
 * @return date           
 */
function drawing_dmg_format_date_time($date)
{
	if (!drawing_dmg_check_format_date($date)) {
		$date = to_sql_date($date, true);
	}
	return $date;
}

/**
 * get file type
 * @param  integer $id 
 * @return integer     
 */
function drawing_dmg_get_file_type($id)
{
	$CI = &get_instance();
	$CI->db->select('filetype');
	$CI->db->where('id', $id);
	$data = $CI->db->get(db_prefix() . 'dms_items')->row();
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
function drawing_get_permission_item_share_to_me($id)
{
	$CI = &get_instance();
	return $CI->drawing_management_model->drawing_get_permission_item_share_to_me($id);
}

/**
 * check share permission
 * @param  integer $item_id    
 * @param  string $permission 
 * @return boolean             
 */
function drawing_check_share_permission($item_id, $permission = 'preview', $creator_type = 'staff')
{
	$CI = &get_instance();
	$data_item = $CI->drawing_management_model->drawing_get_permission_item_share_to_me($item_id, $creator_type);
	if ($data_item) {
		return in_array($permission, $data_item);
	} else {
		$data_item = $CI->drawing_management_model->get_item($item_id, '', 'parent_id');
		if ($data_item) {
			return drawing_check_share_permission($data_item->parent_id, $permission, $creator_type);
		} else {
			return false;
		}
	}
}

/**
 * space to nbsp
 */
function drawing_dmg_space_to_nbsp($data)
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

function drawing_ufirst($string)
{
	return ucfirst($string ?? '');
}
function drawing_nlbr($string)
{
	return nl2br($string ?? '');
}
function drawing_htmldecode($string)
{
	return html_entity_decode($string ?? '');
}

/**
 * get client IP
 * @return string
 */
function drawing_doc_get_client_ip()
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

function drawing_discipline($discipline)
{
	$discipline = explode(',', $discipline);
	$CI = &get_instance();
	$CI->db->select('name');
	$CI->db->where_in('id', $discipline);
	$data = $CI->db->get(db_prefix() . 'dms_discipline')->result_array();
	if ($data) {
		$data = array_column($data, 'name');
		$data = implode(",", $data);
		return $data;
	}
	return '';
}


function update_drawing_last_action($id)
{
	$CI = &get_instance();
	if (!empty($id)) {
		// First update the current item
		$CI->db->where('id', $id);
		$CI->db->update(db_prefix() . 'dms_items', [
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
			$parent = $CI->db->get(db_prefix() . 'dms_items')->row();

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
			$CI->db->update(db_prefix() . 'dms_items', [
				'last_action' => get_staff_user_id()
			]);

			// Add to processed IDs and move up the tree
			$processed_ids[] = $parent->parent_id;
			$current_id = $parent->parent_id;
		}
	}
	return true;
}

function add_dms_activity_log($id, $by_module = '')
{
	$CI = &get_instance();
	if (!empty($id)) {
		$CI->db->where('id', $id);
		$dms_item = $CI->db->get(db_prefix() . 'dms_items')->row();

		if (!empty($dms_item)) {
			// Build the complete path by traversing parent hierarchy
			$path_names = [];
			$current_item = $dms_item;

			// Start with current item name
			$current_name = $current_item->name;

			// Traverse up the parent hierarchy until parent_id = 0 or no parent found
			while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
				$CI->db->where('id', $current_item->parent_id);
				$parent_item = $CI->db->get(db_prefix() . 'dms_items')->row();

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
			$description = "Drawing <b>" . $current_name . "</b> has been uploaded" . $by_module . " at <b>" . $path_string . "</b>.";
			$CI->load->model('projects_model');
			$project_id = get_default_project();
			$CI->db->insert(db_prefix() . 'module_activity_log', [
				'module_name' => 'dms',
				'description' => $description,
				'date' => date('Y-m-d H:i:s'),
				'staffid' => get_staff_user_id(),
				'project_id' => $project_id
			]);
		}
	}
	return true;
}

function duplicate_dms_activity_log($parent_id, $item_id)
{
	$CI = &get_instance();
	if (!empty($item_id)) {
		$CI->db->where('id', $item_id);
		$dms_item = $CI->db->get(db_prefix() . 'dms_items')->row();

		if (!empty($dms_item)) {
			// Build the complete path for source item (item_id)
			$source_path_names = [];
			$current_item = $dms_item;

			// Start with current item name
			$current_name = $current_item->name;

			// Traverse up the parent hierarchy until parent_id = 0 or no parent found
			while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
				$CI->db->where('id', $current_item->parent_id);
				$parent_item = $CI->db->get(db_prefix() . 'dms_items')->row();

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
				$parent_folder = $CI->db->get(db_prefix() . 'dms_items')->row();

				if (!empty($parent_folder)) {
					$current_parent = $parent_folder;

					// Start with parent folder name
					$destination_path_names[] = $current_parent->name;

					// Traverse up the parent hierarchy until parent_id = 0 or no parent found
					while (!empty($current_parent->parent_id) && $current_parent->parent_id != 0) {
						$CI->db->where('id', $current_parent->parent_id);
						$grandparent_item = $CI->db->get(db_prefix() . 'dms_items')->row();

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
			$description = "Drawing <b>" . $current_name . "</b> has been duplicated from <b>" . $source_path_string . "</b> to <b>" . $destination_path_string . "</b>.";
			$CI->load->model('projects_model');
			$project_id = get_default_project();
			$CI->db->insert(db_prefix() . 'module_activity_log', [
				'module_name' => 'dms',
				'description' => $description,
				'date' => date('Y-m-d H:i:s'),
				'staffid' => get_staff_user_id(),
				'project_id' => $project_id
			]);
		}
	}
	return true;
}


function moved_dms_activity_log($data_item, $insert_id)
{
	$CI = &get_instance();
	if (!empty($insert_id)) {
		$CI->db->where('id', $insert_id);
		$dms_item = $CI->db->get(db_prefix() . 'dms_items')->row();

		if (!empty($dms_item)) {
			// Build the complete path for new location (destination)
			$destination_path_names = [];
			$current_item = $dms_item;

			// Start with current item name
			$current_name = $current_item->name;

			// Traverse up the parent hierarchy until parent_id = 0 or no parent found
			while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
				$CI->db->where('id', $current_item->parent_id);
				$parent_item = $CI->db->get(db_prefix() . 'dms_items')->row();

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
				$old_parent = $CI->db->get(db_prefix() . 'dms_items')->row();

				if (!empty($old_parent)) {
					$current_old_parent = $old_parent;

					// Start with old parent name
					$source_path_names[] = $current_old_parent->name;

					// Traverse up the parent hierarchy until parent_id = 0 or no parent found
					while (!empty($current_old_parent->parent_id) && $current_old_parent->parent_id != 0) {
						$CI->db->where('id', $current_old_parent->parent_id);
						$old_grandparent = $CI->db->get(db_prefix() . 'dms_items')->row();

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
			$description = "Drawing <b>" . $current_name . "</b> has been moved from <b>" . $source_path_string . "</b> to <b>" . $destination_path_string . "</b>.";
			$CI->load->model('projects_model');
			$project_id = get_default_project();
			$CI->db->insert(db_prefix() . 'module_activity_log', [
				'module_name' => 'dms',
				'description' => $description,
				'date' => date('Y-m-d H:i:s'),
				'staffid' => get_staff_user_id(),
				'project_id' => $project_id
			]);
		}
	}
	return true;
}


function update_dms_activity_log($id, $data, $original_data)
{
	$CI = &get_instance();

	if (!empty($id)) {
		$CI->db->where('id', $id);
		$dms_item = $CI->db->get(db_prefix() . 'dms_items')->row();

		if (!empty($dms_item)) {
			$changes = [];

			// Define fields to track
			$tracked_fields = [
				'name',
				'discipline',
				'signed_by',
				'issue_date',
				'design_stage',
				'purpose',
				'ocr_language',
				'document_number',
				'status',
				'controlled_document',
				'note'
			];

			// Get discipline names
			$disciplines = $CI->drawing_management_model->get_discipline();
			$discipline_names = [];
			foreach ($disciplines as $discipline) {
				$discipline_names[$discipline['id']] = $discipline['name'];
			}

			// Design stages array
			$design_stages = [
				1 => 'Documents Under Review',
				2 => 'Briefs',
				3 => 'Concept',
				4 => 'Schematic',
				5 => 'Design Development',
				6 => 'Tender Documents',
				7 => 'Construction Documents',
				8 => 'Shop Drawings',
				9 => 'As-Built'
			];

			foreach ($tracked_fields as $field) {
				if (isset($data[$field]) && array_key_exists($field, $original_data)) {
					$new_value = $data[$field];
					$old_value = $original_data[$field];

					// Check if the value has actually changed
					if ($new_value != $old_value) {

						// Format values for display - handle empty/0 values
						$formatted_old = format_value_for_display($old_value, $field, $discipline_names, $design_stages);
						$formatted_new = format_value_for_display($new_value, $field, $discipline_names, $design_stages);

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
				$current_item = $dms_item;
				
				// Start with current item name
				$current_item_name = $current_item->name;
				
				// Traverse up the parent hierarchy
				while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
					$parent = $CI->db->where('id', $current_item->parent_id)->get(db_prefix() . 'dms_items')->row();
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
					$full_path = $path_string . ' > ' . $current_item_name;
				} else {
					$full_path = $current_item_name;
				}

				if($dms_item->filetype == 'folder'){
					$description = "Folder <b>{$dms_item->name}</b> located at <b>{$full_path}</b> has been updated. Changes: <b>" . implode(', ', $change_descriptions)."</b>";
				}else{
					$description = "Drawing <b>{$dms_item->name}</b> located at <b>{$full_path}</b> has been updated. Changes: <b>" . implode(', ', $change_descriptions)."</b>";
				}
				
				$CI->load->model('projects_model');
				$project_id = get_default_project();
				$CI->db->insert(db_prefix() . 'module_activity_log', [
					'module_name' => 'dms',
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

// Helper function to format values for display
function format_value_for_display($value, $field, $discipline_names, $design_stages)
{
	// Check if value is empty, null, or 0
	if ($value === '' || $value === null || $value === 0 || $value === '0') {
		return 'Empty';
	}

	// Handle specific field types
	switch ($field) {
		case 'discipline':
			return isset($discipline_names[$value]) ? $discipline_names[$value] : 'Empty';

		case 'design_stage':
			return isset($design_stages[$value]) ? $design_stages[$value] : 'Empty';

		case 'controlled_document':
			return $value ? 'Yes' : 'No';

		case 'status':
			$status_names = [
				'under_review' => 'Under Review',
				'approved' => 'Approved',
				'rejected' => 'Rejected'
				// Add more status mappings as needed
			];
			return isset($status_names[$value]) ? $status_names[$value] : 'Empty';

		case 'issue_date':
			// Handle date fields - if empty date, return Empty
			if ($value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
				return 'Empty';
			}
			return $value;

		default:
			return $value;
	}
}

function create_folder_dms_activity_log($id, $by_module = '')
{
	$CI = &get_instance();
	if (!empty($id)) {
		$CI->db->where('id', $id);
		$dms_item = $CI->db->get(db_prefix() . 'dms_items')->row();

		if (!empty($dms_item)) {
			// Build the complete path by traversing parent hierarchy
			$path_names = [];
			$current_item = $dms_item;

			// Start with current item name
			$current_name = $current_item->name;

			// Traverse up the parent hierarchy until parent_id = 0 or no parent found
			while (!empty($current_item->parent_id) && $current_item->parent_id != 0) {
				$CI->db->where('id', $current_item->parent_id);
				$parent_item = $CI->db->get(db_prefix() . 'dms_items')->row();

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
				'module_name' => 'dms',
				'description' => $description,
				'date' => date('Y-m-d H:i:s'),
				'staffid' => get_staff_user_id(),
				'project_id' => $project_id
			]);
		}
	}
	return true;
}

function get_rfi_dms_items($id)
{
    $CI = &get_instance();
    $CI->db->select('ticketid, subject');
    $CI->db->where('FIND_IN_SET(' . $CI->db->escape($id) . ', dms_items) !=', 0, false);
    $tickets = $CI->db->get(db_prefix() . 'tickets')->result_array();
    return $tickets;
}