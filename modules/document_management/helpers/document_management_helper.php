<?php
defined('BASEPATH') or exit('No direct script access allowed');
hooks()->add_action('after_email_templates', 'add_document_management_email_templates');

if (!function_exists('add_document_management_email_templates')) {
	/**
	 * Init appointly email templates and assign languages
	 * @return void
	 */
	function add_document_management_email_templates() {
		$CI = &get_instance();

		$data['document_management_templates'] = $CI->emails_model->get(['type' => 'document_management', 'language' => 'english']);

		$CI->load->view('document_management/email_templates', $data);
	}
}

function init_fist_item($type = 'staff'){
	$CI = & get_instance();
	$user_id = 0;
	if($type == 'staff'){
		$user_id = get_staff_user_id();
		$CI->db->where('creator_id', $user_id);
		$CI->db->where('creator_type', $type);
	}
	elseif($type == 'customer'){
		$user_id = get_client_user_id();
		$CI->db->where('creator_id', $user_id);
		$CI->db->where('creator_type', $type);
	}
	if($CI->db->get(db_prefix().'dmg_items')->num_rows() == 0){
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
		$CI->db->insert(db_prefix().'dmg_items', $data);
	}

	$CI->db->where('creator_id', '0');
	if($CI->db->get(db_prefix().'dmg_items')->num_rows() == 0){
		$data['name'] = '#'._l('dmg_team');
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
		$CI->db->insert(db_prefix().'dmg_items', $data);
	} 

	$CI->db->select('id, name');
	$projects = $CI->db->get(db_prefix().'projects')->result_array();
	if(!empty($projects)) {
		foreach ($projects as $key => $value) {
			$CI->db->where('project_id', $value['id']);
			if($CI->db->get(db_prefix().'dmg_items')->num_rows() == 0){
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
				$CI->db->insert(db_prefix().'dmg_items', $data);
			}
		}
	}
}

function dmg_get_file_name($id){
	$CI = & get_instance();
	$CI->db->select('name');
	$CI->db->where('id', $id);
	$data = $CI->db->get(db_prefix().'dmg_items')->row();
	if($data){
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
function dmg_convert_custom_field_value_to_string($value, $type){
	$string_content = dmg_check_content($value);
	if($type == 'date'){
		$string_content = _d($string_content);
	}
	if($type == 'datetime'){
		$string_content = _dt($string_content);
	}
	if($type == 'radio_button'){
		if($string_content == '[]'){
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
function dmg_check_content($selected){
	$result = '';
	if($selected != null){
		if(is_array($selected)){
			if(count($selected) > 0){
				$result = implode(', ', $selected);
			}
		}
		else{			
			$selected_s = json_decode($selected);
			if(is_array($selected_s) && isset($selected_s[0])){		
				if(is_array($selected_s[0])){
					$result = parse_array_multi_to_string($selected_s);
				}	
				else{
					$temp_str = trim($selected_s[0]); 
					if($temp_str != ''){
						$result = implode(', ', $selected_s);
					}
				}	
			}
			else{
				if(is_object($selected_s)){
					$selected_s = (array)$selected_s;
					$result = parse_array_multi_to_string($selected_s);
				}
				else{
					if($selected == '[]'){
						$result = '';
					}
					else{
						$temp_str = trim($selected); 
						if($temp_str != ''){
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
function parse_array_multi_to_string($array){
	$string = '';
	if(is_array($array)){
		foreach($array as $key_text => $sub_qs){
			if($key_text != ''){
				$sub_string = '';
				if(is_array($sub_qs) && count($sub_qs) > 0){
					foreach($sub_qs as $sub_text){
						if($sub_text != ''){
							$sub_string .= $sub_text.', ';
						}
					}
				}
				$string .= $key_text.''.($sub_string != '' ? ' ('.rtrim($sub_string, ', ').')' : '').', ';
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
function get_audit_log_file($item_id){
	$CI = & get_instance();
	$CI->db->where('item_id', $item_id);
	$CI->db->order_by('date', 'desc');
	return $CI->db->get(db_prefix().'dmg_audit_logs')->result_array();
}

/**
 * check file locked
 * @param  integer $item_id 
 * @return boolean          
 */
function check_file_locked($item_id){
	$CI = & get_instance();
	$CI->db->select('locked, lock_user');
	$CI->db->where('id', $item_id);
	$item = $CI->db->get(db_prefix().'dmg_items')->row();
	if($item && is_object($item) && $item->locked != 1 || ($item->locked == 1 && $item->lock_user == get_staff_user_id())){
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
	$f_dot =  str_replace(',','', $str);
	return ((float)$f_dot + 0);
}

/**
* check format date ymd
* @param  date $date 
* @return boolean       
*/
function dmg_check_format_date_ymd($date) {
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
function dmg_check_format_date($date){
	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4]):?((0|[0-5][0-9]):?(0|[0-5][0-9])|6000|60:00)$/",$date)) {
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
function dmg_format_date($date){
	if(!dmg_check_format_date_ymd($date)){
		$date = to_sql_date($date);
	}
	return $date;
}            

/**
* format date time
* @param  date $date     
* @return date           
*/
function dmg_format_date_time($date){
	if(!dmg_check_format_date($date)){
		$date = to_sql_date($date, true);
	}
	return $date;
}

/**
 * get file type
 * @param  integer $id 
 * @return integer     
 */
function dmg_get_file_type($id){
	$CI = & get_instance();
	$CI->db->select('filetype');
	$CI->db->where('id', $id);
	$data = $CI->db->get(db_prefix().'dmg_items')->row();
	if($data){
		return $data->filetype;
	}
	return '';
}

/**
 * get permission item share to me
 * @param  integer $id 
 * @return integer     
 */
function get_permission_item_share_to_me($id){
	$CI = & get_instance();
	return $CI->document_management_model->get_permission_item_share_to_me($id);
}

/**
 * check share permission
 * @param  integer $item_id    
 * @param  string $permission 
 * @return boolean             
 */
function check_share_permission($item_id, $permission = 'preview', $creator_type = 'staff'){
	$CI = & get_instance();
	$data_item = $CI->document_management_model->get_permission_item_share_to_me($item_id, $creator_type);
	if($data_item){
		return in_array($permission, $data_item);		
	}
	else{
		$data_item = $CI->document_management_model->get_item($item_id, '', 'parent_id');
		if($data_item){
			return check_share_permission($data_item->parent_id, $permission, $creator_type);
		}
		else{
			return false;
		}
	}
}

/**
* space to nbsp
*/
function dmg_space_to_nbsp($data){
	$exp="/((?:<\\/?\\w+)(?:\\s+\\w+(?:\\s*=\\s*(?:\\\".*?\\\"|'.*?'|[^'\\\">\\s]+)?)+\\s*|\\s*)\\/?>)([^<]*)?/";
	$ex1="/^([^<>]*)(<?)/i";
	$ex2="/(>)([^<>]*)$/i";
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

function ufirst($string){
	return ucfirst($string ?? '');
}
function nlbr($string){
	return nl2br($string ?? '');
}
function htmldecode($string){
	return html_entity_decode($string ?? '');
}

/**
 * get client IP
 * @return string
 */
function doc_get_client_ip() {
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