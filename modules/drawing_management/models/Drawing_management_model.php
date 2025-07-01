<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use \Convertio\Convertio;

/**
 * Document management model
 */
class drawing_management_model extends app_model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * get items
	 * @param  integer $id     
	 * @param  string $where  
	 * @param  string $select 
	 * @return array or object         
	 */
	public function get_item($id, $where = '', $select = '')
	{
		if ($select != '') {
			$this->db->select($select);
		}
		if ($id != '') {
			$this->db->order_by('position', 'ASC');
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'dms_items')->row();
		} else {
			$this->db->order_by('position', 'ASC');
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dms_items')->result_array();
		}
	}


	public function get_root_item($user_id, $project_id = 0)
	{
		$this->db->where('parent_id = 0');
		$this->db->group_start();
		$this->db->where('project_id', 0);
		$this->db->or_where('project_id', $project_id);
		$this->db->group_end();
		$this->db->group_start();
		$this->db->where('(creator_id = ' . $user_id . ' and creator_type = "staff")');
		$this->db->or_where('(creator_id = 0 and creator_type = "public")');
		$this->db->group_end();
		$this->db->order_by("creator_id", "desc");
		return $this->db->get(db_prefix() . 'dms_items')->result_array();

	}

	public function check_project_member_exist($project_id)
	{
		$this->db->where('project_id', $project_id);
		$this->db->where('staff_id', get_staff_user_id());
		return $this->db->get(db_prefix() . 'project_members')->row();
	}

	/**
	 * delete item
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_item($id)
	{
		$data_item = $this->get_item($id, '', 'filetype, parent_id, name');
		if ($data_item) {
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'dms_items');
			if ($this->db->affected_rows() > 0) {
				if ($data_item->filetype != 'folder') {
					// Delete physical file
					$this->delete_file_item(DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/' . $data_item->name);
					// Delete all version file
					$data_log_version = $this->get_log_version_by_parent($id, '', 'id');
					foreach ($data_log_version as $key => $value) {
						$this->delete_log_version($value['id']);
					}
				} else {
					// Delete child item of folder
					$child_data = $this->get_item('', 'parent_id = ' . $id, 'id');
					foreach ($child_data as $key => $value) {
						$this->delete_item($value['id']);
					}
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * create new folder
	 * @param array $data 
	 * @return boolean 
	 */
	public function create_item($data)
	{
		if (is_client_logged_in()) {
			$data['creator_id'] = get_client_user_id();
			$data['creator_type'] = 'customer';
		} else {
			$data['creator_id'] = get_staff_user_id();
			$data['creator_type'] = 'staff';
		}

		if (isset($data['parent_id'])) {
			$data['master_id'] = $this->get_master_id($data['parent_id']);
		}
		$data['dateadded'] = date('Y-m-d H:i:s');
		$data['hash'] = app_generate_hash();
		$this->db->insert(db_prefix() . 'dms_items', $data);
		return $this->db->insert_id();
	}
	/**
	 * update folder
	 * @param array $data 
	 * @return boolean 
	 */
	public function update_item($data)
	{

		if (isset($data['controlled_document'])) {
			if ($data['controlled_document'] == 'on') {
				echo $data['controlled_document'] = 1;
			}
		} else {
			echo $data['controlled_document'] = 0;
		}

		if (isset($data['duedate']) && $data['duedate'] == '') {
			$data['duedate'] = null;
		}
		if (isset($data['dateadded']) && $data['dateadded'] == '') {
			$data['dateadded'] = null;
		}
		if (isset($data['dateadded']) && $data['dateadded'] != '') {
			$data['dateadded'] = date('Y-m-d H:i:s', strtotime($data['dateadded']));
		}
		if (isset($data['issue_date']) && $data['issue_date'] != '') {
			$data['issue_date'] = date('Y-m-d H:i:s', strtotime($data['issue_date']));
		}
		if (isset($data['discipline']) && $data['discipline'] != '') {
			$data['discipline'] = implode(",", $data['discipline']);
		}
		$customfield = [];
		if (isset($data['customfield'])) {
			$customfield = $data['customfield'];
			unset($data['customfield']);
		}
		$affectedRows = 0;
		$id = $data['id'];
		$data_item = $this->get_item($id, '', 'name');
		if ($data_item) {
			if (isset($data['parent_id'])) {
				$data['master_id'] = $this->get_master_id($data['parent_id']);
			}

			if (!empty($_FILES['pdf_attachment']['name'])) {
				$uploadDir = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/pdf_attachments/' . $id . '/';

				// Create directory if it doesn't exist
				if (!file_exists($uploadDir)) {
					mkdir($uploadDir, 0755, true);
				}

				$allowedExtensions = ['dwg', 'xref'];
				$fileExtension = pathinfo($_FILES['pdf_attachment']['name'], PATHINFO_EXTENSION);
				$originalFileName = basename($_FILES['pdf_attachment']['name']);

				// Validate file type
				if (in_array(strtolower($fileExtension), $allowedExtensions)) {
					$targetPath = $uploadDir . $originalFileName;

					// Check if file already exists and handle accordingly
					if (file_exists($targetPath)) {
						// Option 1: Append timestamp to filename to avoid overwriting
						$fileNameParts = pathinfo($originalFileName);
						$targetPath = $uploadDir . $fileNameParts['filename'] . '.' . $fileNameParts['extension'];

						// Option 2: Uncomment below to simply overwrite existing file
						// unlink($targetPath);
					}

					if (move_uploaded_file($_FILES['pdf_attachment']['tmp_name'], $targetPath)) {
						// Save relative path in database (including the subfolder structure)
						$data['pdf_attachment'] =  basename($targetPath);
					} else {
						// set_alert('warning', _l('file_upload_failed'));
					}
				} else {
					// set_alert('danger', _l('only_dwg_xref_files_allowed'));
				}
			}
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'dms_items', $data);

			if ($this->db->affected_rows() > 0) {
				// Rename file if name has been changed
				if (isset($data['name']) && ($data_item->name != $data['name'])) {
					$this->change_file_name($id, $data['name']);
				}
				$affectedRows++;
			}
		}
		// Add or update custom field
		if (count($customfield) > 0) {
			$data_field = [];
			foreach ($customfield as $customfield_id => $field_value) {
				$field_value = (is_array($field_value) ? json_encode($field_value) : $field_value);
				$data_customfield = $this->get_custom_fields($customfield_id);
				if ($data_customfield) {
					$data_field_item['title'] = $data_customfield->title;
					$data_field_item['type'] = $data_customfield->type;
					$data_field_item['option'] = $data_customfield->option;
					$data_field_item['required'] = $data_customfield->required;
					$data_field_item['value'] = $field_value;
					$data_field_item['custom_field_id'] = $data_customfield->id;
					$data_field[] = $data_field_item;
				}
			}
			$data_field = json_encode($data_field);
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'dms_items', ['custom_field' => $data_field]);
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
		}
		if ($affectedRows > 0) {
			$this->add_audit_log($id, _l('dmg_updated_file'));
			return true;
		}
		return false;
	}

	/**
	 * get master item id
	 * @param  integer $id     
	 * @return integer         
	 */
	public function get_master_id($id)
	{
		$master_id = 0;
		$this->db->select('master_id');
		$this->db->where('id', $id);
		$data = $this->db->get(db_prefix() . 'dms_items')->row();
		if ($data) {
			if ($data->master_id == 0) {
				$master_id = $id;
			} else {
				$master_id = $data->master_id;
			}
		}
		return $master_id;
	}

	/**
	 * breadcrum array
	 * @param  integer $id 
	 * @return array     
	 */
	public function breadcrum_array($id, $array = [])
	{
		$data_item = $this->get_item($id, '', 'master_id, parent_id, name, id');
		if ($data_item && is_object($data_item)) {
			$array[] = ['id' => $id, 'parent_id' => $data_item->parent_id, 'name' => $data_item->name];
			if ($data_item->parent_id > 0 && $id = $data_item->parent_id) {
				$array = $this->breadcrum_array($id, $array);
			}
		}
		return $array;
	}

	public function get_document_number($id)
	{

		$file = $this->db->where('id', $id)->get(db_prefix() . 'dms_items')->row();

		if (!$file) {
			return "Document type not found.";
		}

		// Fetch the type of document (last folder) by id
		$doc_type = $this->db->where('id', $file->parent_id)->get(db_prefix() . 'dms_items')->row();

		if (!$doc_type) {
			return "Discipline not found.";
		}

		// Fetch project name using parent_id of the discipline
		$discipline = $this->db->where('id', $doc_type->parent_id)->get(db_prefix() . 'dms_items')->row();

		if (!$discipline) {
			return "Project not found.";
		}

		// Fetch project name using parent_id of the discipline
		$project = $this->db->where('id', $discipline->parent_id)->get(db_prefix() . 'dms_items')->row();

		if (!$project) {
			return "Project not found.";
		}

		// Get the first 3 letters of project name, discipline, and document type
		$project_code = strtoupper(substr($project->name, 0, 3));
		$discipline_code = strtoupper(substr($discipline->name, 0, 3));
		$type_code = strtoupper(substr($doc_type->name, 0, 3));

		// Construct the base document prefix to search for existing document numbers
		$prefix = "{$project_code}-{$discipline_code}-{$type_code}-";

		// Find the last used number for this document type
		$last_document = $this->db->like('document_number', "{$prefix}", 'after')
			->order_by('document_number', 'desc')
			->get(db_prefix() . 'dms_items')
			->row();

		// Extract the last number from the document_number if it exists, otherwise start from 0
		if ($last_document && preg_match('/(\d+)$/', $last_document->document_number, $matches)) {
			$last_number = (int)$matches[1];
		} else {
			$last_number = 0;
		}

		// Increment the last number by 1 and format it to be 3 digits with leading zeros
		$new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);

		// Concatenate to create the document number
		$document_number = "{$prefix}{$new_number}";
		if ($file->parent_id == 25) {
			// Prepare data to update
			$data = [
				'document_number' => $document_number,
				'controlled_document' => 1,
				'purpose' => 'Issued for construction'
			];
		} else {
			// Prepare data to update
			$data = ['document_number' => $document_number];
		}


		// Update the document number in the record
		$this->db->where('id', $id)->update(db_prefix() . 'dms_items', $data);

		return $document_number;
	}


	/**
	 * upload file
	 * @param  integer $id     
	 * @param  string $folder 
	 * @return boolean         
	 */
	public function upload_file($id, $type, $version = '1.0.0')
	{
		$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/' . $type . '/' . $id . '/';
		$totalUploaded = 0;
		if (
			isset($_FILES['file']['name'])
			&& ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
		) {
			if (!is_array($_FILES['file']['name'])) {
				$_FILES['file']['name'] = [$_FILES['file']['name']];
				$_FILES['file']['type'] = [$_FILES['file']['type']];
				$_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
				$_FILES['file']['error'] = [$_FILES['file']['error']];
				$_FILES['file']['size'] = [$_FILES['file']['size']];
			}
			_file_attachments_index_fix('file');
			for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
				// Get the temp file path
				$tmpFilePath = $_FILES['file']['tmp_name'][$i];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					if (
						_perfex_upload_error($_FILES['file']['error'][$i])
						|| !_upload_extension_allowed($_FILES['file']['name'][$i])
					) {
						continue;
					}

					_maybe_create_upload_path($path);
					$filename = $this->check_duplicate_file_name($id, $_FILES['file']['name'][$i]);
					$newFilePath = $path . $filename;
					// Upload the file into the temp dir
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$creator_type = 'staff';
						if (is_client_logged_in()) {
							$creator_type = 'customer';
						}
						$inserted_id = $this->add_attachment_file_to_database($filename, $id, $version, $_FILES['file']['type'][$i], '', '', '', $creator_type);
						$totalUploaded++;
					}
				}
			}
		}

		return $inserted_id;
	}

	/**
	 * add attachment file to database
	 * @param [type] $name      
	 * @param [type] $parent_id 
	 * @param [type] $version   
	 * @param [type] $filetype  
	 */
	public function add_attachment_file_to_database($name, $parent_id, $version, $filetype, $log_text = '', $old_item_id = '', $creator_id = '', $creator_type = 'staff')
	{
		if (is_numeric($old_item_id) && $old_item_id > 0) {
			$data_item = $this->get_item($old_item_id);
			if ($data_item) {
				$data = (array)$data_item;
				$data['id'] = '';
				$data['parent_id'] = $parent_id;
				$data['version'] = $version;
				$data['master_id'] = $this->get_master_id($parent_id);
			}
		} else {
			$data['dateadded'] = date('Y-m-d H:i:s');
			if ($creator_type == 'staff') {
				if ($creator_id == '') {
					$data['creator_id'] = get_staff_user_id();
				} else {
					$data['creator_id'] = $creator_id;
				}
			} else {
				if ($creator_id == '') {
					$data['creator_id'] = get_client_user_id();
				} else {
					$data['creator_id'] = $creator_id;
				}
			}
			$data['creator_type'] = $creator_type;
			$data['name'] = $name;
			$data['parent_id'] = $parent_id;
			$data['version'] = $version;
			$data['filetype'] = $filetype;
			$data['hash'] = app_generate_hash();
			$data['master_id'] = $this->get_master_id($parent_id);
		}
		$this->db->insert(db_prefix() . 'dms_items', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			if ($log_text == '') {
				$this->add_audit_log($insert_id, _l('dmg_added_file'));
			} else {
				$this->add_audit_log($insert_id, $log_text);
			}
			if (is_numeric($old_item_id) && $old_item_id > 0) {
				$this->change_log_item_id($old_item_id, $insert_id);
				$this->change_version_item_id($old_item_id, $insert_id);
				$this->change_reminder_item_id($old_item_id, $insert_id);
				$this->change_share_to_item_id($old_item_id, $insert_id);
				$this->change_approve_item_id($old_item_id, $insert_id);
				$this->change_sign_approve_item_id($old_item_id, $insert_id);
			}
		}
		return $insert_id;
	}

	/**
	 * get log version
	 * @param  integer $id     
	 * @param  string $where  
	 * @param  string $select 
	 * @return array or object         
	 */
	public function get_log_version($id, $where = '', $select = '')
	{
		if ($select != '') {
			$this->db->select($select);
		}
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'dms_file_versions')->row();
		} else {
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dms_file_versions')->result_array();
		}
	}


	/**
	 * delete log version
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_log_version($id, $audit_log = true)
	{
		$data_log = $this->get_log_version($id, '', 'name, parent_id');
		if ($data_log) {
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'dms_file_versions');
			if ($this->db->affected_rows() > 0) {
				//Delete physiscal file
				$this->delete_file_item(DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $data_log->parent_id . '/' . $data_log->name);
				// Add audit log
				if ($audit_log) {
					$this->add_audit_log($data_log->parent_id, _l('dmg_deleted_version') . ': ' . $data_log->name);
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * get log version by parent
	 * @param  integer $parent_id     
	 * @param  string $where  
	 * @param  string $select 
	 * @return array    
	 */
	public function get_log_version_by_parent($parent_id, $where = '', $select = '')
	{
		if ($select != '') {
			$this->db->select($select);
		}
		if ($where != '') {
			$this->db->where($where);
		}
		$this->db->where('parent_id', $parent_id);
		$this->db->order_by('dateadded', 'desc');
		return $this->db->get(db_prefix() . 'dms_file_versions')->result_array();
	}

	/**
	 * delete file item
	 * @param  string $path 
	 */
	public function delete_file_item($path)
	{
		if (file_exists($path)) {
			unlink($path);
		}
	}

	/**
	 * change file name
	 * @param  integer $id       
	 * @param  string $new_name 
	 * @return boolean           
	 */
	public function change_file_name($id, $new_name)
	{
		$data_item = $this->get_item($id, '', 'name, parent_id');
		if ($data_item) {
			$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/';
			$new_path = $path . $new_name;
			$old_path = $path . $data_item->name;
			if (file_exists($old_path)) {
				rename($old_path, $new_path);
				return true;
			}
		}
		return false;
	}

	/**
	 * add custom_field
	 * @param array $data 
	 * @return integer $insert id 
	 */
	public function add_custom_field($data)
	{
		$data['option'] = is_array($data['option']) ? json_encode($data['option']) : null;
		if (!isset($data['required'])) {
			$data['required'] = 0;
		}
		$this->db->insert(db_prefix() . 'dms_custom_fields', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			return $insert_id;
		}
		return 0;
	}
	/**
	 * update custom_field
	 * @param  array $data 
	 * @return boolean     
	 */
	public function update_custom_field($data)
	{
		$data['option'] = is_array($data['option']) ? json_encode($data['option']) : null;
		if (!isset($data['required'])) {
			$data['required'] = 0;
		}
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'dms_custom_fields', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete custom_field
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_custom_field($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'dms_custom_fields');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get custom_fields
	 * @param  integer $id 
	 * @return array or object    
	 */
	public function get_custom_fields($id = '')
	{
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'dms_custom_fields')->row();
		} else {
			return $this->db->get(db_prefix() . 'dms_custom_fields')->result_array();
		}
	}

	/**
	 * copy file
	 * @param  integer $id            
	 * @param  string $save_path        
	 * @param  string $file_name 
	 * @return string $new_file_name                
	 */
	public function copy_file($from_path, $save_path)
	{
		try {
			if (file_exists($from_path)) {
				// copy($from_path, $save_path);

				$arrContextOptions = array(
					"ssl" => array(
						"verify_peer" => false,
						"verify_peer_name" => false,
					),
				);
				$file_content = file_get_contents($from_path, false, stream_context_create($arrContextOptions));
				file_put_contents($save_path, $file_content);
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * create folder
	 * @param  integer $id   
	 * @param  string $path 
	 */
	public function create_folder($id, $path = '')
	{
		if ($path == '') {
			$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/temps/' . $id;
			drawing_dmg_create_folder($path);
			$path = $path . '/' . drawing_dmg_get_file_name($id);
			drawing_dmg_create_folder($path);
		}
		$data_child = $this->get_item('', 'parent_id = ' . $id, 'id, name, filetype, parent_id');
		if ($data_child) {
			foreach ($data_child as $key => $value) {
				if ($value['filetype'] == 'folder') {
					$new_path = $path . '/' . $value['name'];
					drawing_dmg_create_folder($new_path);
					$this->create_folder($value['id'], $new_path);
				} else {
					$path1 = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $value['parent_id'] . '/' . $value['name'];
					$path2 = $path . '/' . $value['name'];
					$this->copy_file($path1, $path2);
				}
			}
		}
	}

	/**
	 * check duplicate name
	 * @param  integer $parent_id 
	 * @param  string $name      
	 * @param  string $id        
	 * @return boolean            
	 */
	public function check_duplicate_name($parent_id, $name, $id = '', $filetype = '', $filetype_negative = false, $creator_id = '', $creator_type = 'staff')
	{
		$query = 'name = \'' . $name . '\' and parent_id = ' . $parent_id;
		if ($creator_id != '' && $creator_type != '') {
			$query .= ' and creator_id = ' . $creator_id . ' and creator_type = \'' . $creator_type . '\'';
		} else {
			if (is_client_logged_in()) {
				$query .= ' and creator_id = ' . get_client_user_id() . ' and creator_type = \'customer\'';
			} else {
				$query .= ' and creator_id = ' . get_staff_user_id() . ' and creator_type = \'staff\'';
			}
		}

		if (is_numeric($id) && $id > 0) {
			$query .= ' and id != ' . $id;
		}
		if ($filetype != '') {
			if (!$filetype_negative) {
				$query .= ' and filetype = \'' . $filetype . '\'';
			} else {
				$query .= ' and filetype != \'' . $filetype . '\'';
			}
		}
		$data_item = $this->get_item('', $query, 'id');
		if (is_array($data_item) && count($data_item) > 0) {
			return true;
		}
		return false;
	}

	/**
	 * check duplicate file name
	 * @param  integer  $parent_id 
	 * @param  string  $name      
	 * @param  integer $count     
	 * @return string             
	 */
	public function check_duplicate_file_name($parent_id, $name, $count = 0)
	{
		$new_name = $name;
		if ($count > 0) {
			$split_name = explode('.', $name);
			if (count($split_name) > 1 && isset($split_name[count($split_name) - 1])) {
				$ext = '.' . $split_name[count($split_name) - 1];
				$new_name = str_replace($ext, '', $name) . ' (' . $count . ')' . $ext;
			} else {
				$new_name = $name . ' (' . $count . ')';
			}
		}
		if ($this->check_duplicate_name($parent_id, $new_name, '', 'folder', true)) {
			return $this->check_duplicate_file_name($parent_id, $name, $count + 1);
		} else {
			return $new_name;
		}
	}

	/**
	 * check duplicate folder name
	 * @param  integer  $parent_id 
	 * @param  string  $name      
	 * @param  integer $count     
	 * @return string             
	 */
	public function check_duplicate_folder_name($parent_id, $name, $count = 0)
	{
		$new_name = $name;
		if ($count > 0) {
			$new_name = $name . ' (' . $count . ')';
		}
		if ($this->check_duplicate_name($parent_id, $new_name, '', 'folder')) {
			return $this->check_duplicate_folder_name($parent_id, $name, $count + 1);
		} else {
			return $new_name;
		}
	}

	/**
	 * create folder bulk download
	 * @param  integer $parent_id  
	 * @param  array $id_lever_1 
	 * @param  string $save_path  
	 */
	public function create_folder_bulk_download($id_lever_1, $folder_name)
	{
		// Create root folder
		$root = rtrim(DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER, '/') . '/temps/bulk_downloads/' . $folder_name;
		drawing_dmg_create_folder($root);

		$data_child = $this->get_item('', 'id IN (' . $id_lever_1 . ')', 'id, name, filetype, parent_id, pdf_attachment');
		if (!$data_child) {
			return;
		}

		foreach ($data_child as $value) {
			if ($value['filetype'] === 'folder') {
				// recursion into sub-folder
				$new_folder = $root . '/' . $value['name'];
				drawing_dmg_create_folder($new_folder);
				$this->create_folder_bulk_download($value['id'], $folder_name . '/' . $value['name']);
			} else {
				// 1) copy the original file
				$sourceFile = rtrim(DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER, '/') . '/files/'
					. $value['parent_id'] . '/' . $value['name'];

				$destFile   = $root . '/' . $value['name'];
				$this->copy_file($sourceFile, $destFile);

				// 2) if there’s a PDF‐attachment, copy that too (same folder, same name)
				if (!empty($value['pdf_attachment'])) {
					$sourcePdf = rtrim(DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER, '/')
						. '/pdf_attachments/' . $value['id'] . '/' . $value['pdf_attachment'];

					if (file_exists($sourcePdf)) {
						$pdfName     = basename($sourcePdf);
						$destPdfPath = $root . '/' . $pdfName;
						$this->copy_file($sourcePdf, $destPdfPath);
					}
				}
			}
		}
	}


	/**
	 * duplicate item
	 * @param  string $folder_id 
	 * @param  string $item_id   
	 * @return boolean            
	 */
	public function duplicate_item($folder_id, $item_id)
	{
		$affectedRows = 0;
		$data_item = $this->get_item($item_id);
		if ($data_item) {
			$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $folder_id . '/';
			_maybe_create_upload_path($path);
			if ($data_item->filetype == 'folder') {
				$data["parent_id"] = $folder_id;
				$data["name"] = $this->check_duplicate_folder_name($folder_id, $data_item->name);
				$insert_id = $this->create_item($data);
				$new_path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $insert_id . '/';
				_maybe_create_upload_path($new_path);
				$data_child = $this->get_item('', 'parent_id = ' . $item_id, 'id, name, filetype, parent_id');
				foreach ($data_child as $key => $value) {
					$this->duplicate_item($insert_id, $value['id']);
				}
				$affectedRows++;
			} else {
				$oldFilePath = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/' . $data_item->name;
				$filename = $this->check_duplicate_file_name($folder_id, $data_item->name);
				$newFilePath = $path . $filename;
				// Upload the file into the temp dir
				if ($this->copy_file($oldFilePath, $newFilePath)) {
					$this->add_attachment_file_to_database($filename, $folder_id, $data_item->version, $data_item->filetype, '', '', $data_item->creator_id, $data_item->creator_type);
					$affectedRows++;
				}
			}
		}
		if ($affectedRows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * move item
	 * @param  string $folder_id 
	 * @param  string $item_id   
	 * @return boolean            
	 */
	public function move_item($folder_id, $item_id)
	{
		$affectedRows = 0;
		$data_item = $this->get_item($item_id);
		if ($data_item) {
			$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $folder_id . '/';
			_maybe_create_upload_path($path);
			if ($data_item->filetype == 'folder') {
				$data["parent_id"] = $folder_id;
				$data["name"] = $this->check_duplicate_folder_name($folder_id, $data_item->name);
				$insert_id = $this->create_item($data);
				$new_path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $insert_id . '/';
				_maybe_create_upload_path($new_path);
				$data_child = $this->get_item('', 'parent_id = ' . $item_id, 'id, name, filetype, parent_id');
				foreach ($data_child as $key => $value) {
					$this->move_item($insert_id, $value['id']);
				}
				$affectedRows++;
			} else {

				$oldFilePath = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/' . $data_item->name;
				$filename = $this->check_duplicate_file_name($folder_id, $data_item->name);
				$newFilePath = $path . $filename;
				// Upload the file into the temp dir
				if ($this->copy_file($oldFilePath, $newFilePath)) {
					$log_text = _l('dmg_moved_file_from') . ' ' . drawing_dmg_get_file_name($data_item->parent_id) . ' ' . _l('dmg_to') . ' ' . drawing_dmg_get_file_name($folder_id);
					$this->add_attachment_file_to_database($filename, $folder_id, $data_item->version, $data_item->filetype, $log_text, $item_id, $data_item->creator_id, $data_item->creator_type);
					$affectedRows++;
				}
			}
		}
		if ($affectedRows > 0) {
			$this->delete_item($item_id);
			return true;
		}
		return false;
	}

	/**
	 * add audit log
	 * @param string $action 
	 */
	public function add_audit_log($item_id, $action)
	{
		if (is_client_logged_in()) {
			$userid = get_client_user_id();
			$data['user_id'] = $userid;
			$data['user_name'] = get_company_name($userid);
		} else {
			$userid = get_staff_user_id();
			$data['user_id'] = $userid;
			$data['user_name'] = get_staff_full_name($userid);
		}
		$data['date'] = date('Y-m-d H:i:s');
		$data['action'] = $action;
		$data['item_id'] = $item_id;
		$this->db->insert(db_prefix() . 'dms_audit_logs', $data);
		return $this->db->insert_id();
	}

	/**
	 * get items
	 * @param  integer $id     
	 * @param  string $where  
	 * @param  string $select 
	 * @return array or object         
	 */
	public function get_audit_log($id, $where = '', $select = '')
	{
		if ($select != '') {
			$this->db->select($select);
		}
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'dms_audit_logs')->row();
		} else {
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dms_audit_logs')->result_array();
		}
	}

	/**
	 * change log item id
	 * @param  integer $old_item_id 
	 * @param  integer $new_item_id 
	 * @return boolean              
	 */
	public function change_log_item_id($old_item_id, $new_item_id)
	{
		$this->db->where('item_id', $old_item_id);
		$this->db->update(db_prefix() . 'dms_audit_logs', ['item_id' => $new_item_id]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * change version item id
	 * @param  integer $old_item_id 
	 * @param  integer $new_item_id 
	 * @return boolean              
	 */
	public function change_version_item_id($old_item_id, $new_item_id)
	{
		$data_log_version = $this->get_log_version_by_parent($old_item_id);
		$this->db->where('parent_id', $old_item_id);
		$this->db->update(db_prefix() . 'dms_file_versions', ['parent_id' => $new_item_id]);
		if ($this->db->affected_rows() > 0) {
			// Move previous file to log folder
			$old_log_path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $old_item_id . '/';
			$new_log_path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $new_item_id . '/';
			_maybe_create_upload_path($new_log_path);
			foreach ($data_log_version as $key => $log_version) {
				$from_path = $old_log_path . $log_version['name'];
				$to_path = $new_log_path . $log_version['name'];
				$this->move_file_to_folder($from_path, $to_path);
			}
			return true;
		}
		return false;
	}

	/**
	 * upload file
	 * @param  integer $id     
	 * @param  string $folder 
	 * @return boolean         
	 */
	public function upload_version_file($id, $version = '1.0.0')
	{
		$totalUploaded = 0;
		$data_item = $this->get_item($id);
		if ($data_item) {
			$parent_id = $data_item->parent_id;
			$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $parent_id . '/';
			if (
				isset($_FILES['file']['name'])
				&& ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
			) {
				if (!is_array($_FILES['file']['name'])) {
					$_FILES['file']['name'] = [$_FILES['file']['name']];
					$_FILES['file']['type'] = [$_FILES['file']['type']];
					$_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
					$_FILES['file']['error'] = [$_FILES['file']['error']];
					$_FILES['file']['size'] = [$_FILES['file']['size']];
				}
				_file_attachments_index_fix('file');
				for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
					// Get the temp file path
					$tmpFilePath = $_FILES['file']['tmp_name'][$i];
					// Make sure we have a filepath
					if (!empty($tmpFilePath) && $tmpFilePath != '') {
						if (
							_perfex_upload_error($_FILES['file']['error'][$i])
							|| !_upload_extension_allowed($_FILES['file']['name'][$i])
						) {
							continue;
						}

						_maybe_create_upload_path($path);
						$filename = $this->check_duplicate_file_name($parent_id, $_FILES['file']['name'][$i]);
						$newFilePath = $path . $filename;
						// Upload the file into the temp dir
						if (move_uploaded_file($tmpFilePath, $newFilePath)) {

							$version_data['name'] = $data_item->name;
							$version_data['version'] = $data_item->version;
							$version_data['filetype'] = $data_item->filetype;
							$version_data['parent_id'] = $id;
							$res_vs = $this->create_version_file($version_data);
							if ($res_vs) {

								// Move previous file to log folder
								$from_path = $path . $data_item->name;
								$log_path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $id . '/';
								_maybe_create_upload_path($log_path);
								$to_path = $log_path . $data_item->name;
								$this->move_file_to_folder($from_path, $to_path);

								// Update name and version of new file to database
								$this->update_change_version_to_database($filename, $id, $version, $_FILES['file']['type'][$i]);
								$totalUploaded++;
							}
						}
					}
				}
			}
		}
		return (bool) $totalUploaded;
	}

	/**
	 * update change version to database
	 * @param string $name      
	 * @param integer $item_id 
	 * @param string $version   
	 * @param string $filetype  
	 */
	public function update_change_version_to_database($name, $item_id, $version, $filetype)
	{
		$data['name'] = $name;
		$data['version'] = $version;
		$data['filetype'] = $filetype;
		$this->db->where('id', $item_id);
		$this->db->update(db_prefix() . 'dms_items', $data);
		if ($this->db->affected_rows() > 0) {
			$this->add_audit_log($item_id, _l('dmg_uploaded_new_version') . ': ' . $name);
			return true;
		}
		return false;
	}

	/**
	 * create version file
	 * @param array $data 
	 * @return boolean 
	 */
	public function create_version_file($data)
	{
		$data['dateadded'] = date('Y-m-d H:i:s');
		$this->db->insert(db_prefix() . 'dms_file_versions', $data);
		return $this->db->insert_id();
	}

	/**
	 * move file to folder
	 * @param  string $oldFilePath 
	 * @param  string $newFilePath 
	 * @return boolean              
	 */
	public function move_file_to_folder($oldFilePath, $newFilePath)
	{
		if ($this->copy_file($oldFilePath, $newFilePath)) {
			// Delete physical file
			$this->delete_file_item($oldFilePath);
			return true;
		}
		return false;
	}

	public function restore_item($version_id)
	{
		$data_log_version = $this->get_log_version($version_id);
		if ($data_log_version) {
			$id = $data_log_version->parent_id;
			$data_item = $this->get_item($id);
			if ($data_item) {
				// Update version infor
				$data['name'] = $data_log_version->name;
				$data['version'] = $data_log_version->version;
				$data['filetype'] = $data_log_version->filetype;
				$this->db->where('id', $id);
				$this->db->update(db_prefix() . 'dms_items', $data);
				if ($this->db->affected_rows() > 0) {
					$this->add_audit_log($id, _l('dmg_restored_version') . ': ' . $data_log_version->name);
				}
				// Create log for old file
				$version_data['name'] = $data_item->name;
				$version_data['version'] = $data_item->version;
				$version_data['filetype'] = $data_item->filetype;
				$version_data['parent_id'] = $id;
				$res_vs = $this->create_version_file($version_data);
				if ($res_vs) {
					// Move previous file to log folder
					$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/';
					$log_path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $id . '/';
					_maybe_create_upload_path($log_path);
					$from_path = $path . $data_item->name;
					$to_path = $log_path . $data_item->name;

					// Change physical file location between two folder
					$this->move_file_to_folder($from_path, $to_path);

					$from_path = $log_path . $data_log_version->name;
					$to_path = $path . $data_log_version->name;
					$this->move_file_to_folder($from_path, $to_path);

					//Delete log has been restore
					$this->delete_log_version($version_id, false);
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * create remider
	 * @param  array $data 
	 * @return integer       
	 */
	public function create_remider($data)
	{
		$data['dateadded'] = date('Y-m-d H:i:s');
		$this->db->insert(db_prefix() . 'dms_remiders', $data);
		return $this->db->insert_id();
	}

	/**
	 * update remider
	 * @param  array $data 
	 * @return integer       
	 */
	public function update_remider($data)
	{
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'dms_remiders', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get remider
	 * @param  integer $id     
	 * @param  string $where  
	 * @param  string $select 
	 * @return array or object         
	 */
	public function get_remider($id, $where = '', $select = '')
	{
		if ($select != '') {
			$this->db->select($select);
		}
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'dms_remiders')->row();
		} else {
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dms_remiders')->result_array();
		}
	}

	public function get_file_reminder($file_id)
	{
		$this->db->where('file_id', $file_id);
		return $this->db->get(db_prefix() . 'dms_remiders')->result_array();
	}

	/**
	 * delete remider
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_remider($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'dms_remiders');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * auto remider
	 * @return [type] 
	 */
	public function auto_drawing_remider()
	{
		$data = $this->get_remider('', 'date <= \'' . date('Y-m-d H:i:s') . '\'', 'id, email, file_id, date, message');
		foreach ($data as $key => $value) {
			$this->send_mail_remider($value['email'], $value['file_id'], $value['message']);
			$this->delete_remider($value['id']);
		}
	}

	/**
	 * send mail remider
	 * @param  string $email   
	 * @param  integer $file_id 
	 * @param  string $message 
	 */
	public function send_mail_remider($email, $file_id, $message)
	{
		if ($email != '') {
			$data_send_mail = new stdClass();
			$data_send_mail->email = trim($email);
			$data_send_mail->link = '<a href="' . admin_url('drawing_management?id=' . $file_id) . '">' . drawing_dmg_get_file_name($file_id) . '</a>';
			$data_send_mail->message = $message;
			$template = mail_template('reminder', 'drawing_management', $data_send_mail);
			$template->send();
		}
	}

	public function get_file_transmittal($transmittal_id)
	{
		// Initialize an array to store the final file records.
		$files = array();

		// Get all records from tbldms_items where parent_id matches the provided transmittal ID.
		$this->db->where('parent_id', $transmittal_id);
		$query = $this->db->get('tbldms_items');
		$records = $query->result();

		// Iterate over each record.
		foreach ($records as $record) {
			// Check if the record is a folder.
			if ($record->filetype === 'folder') {
				// If it's a folder, recursively retrieve its non-folder files.
				$folder_files = $this->get_file_transmittal($record->id);
				// Merge the retrieved files from the folder into the results array.
				if (!empty($folder_files)) {
					$files = array_merge($files, $folder_files);
				}
			} else {
				// If it's not a folder, add the record directly to the files array.
				$files[] = $record;
			}
		}

		// Return the complete array of non-folder files.
		return $files;
	}
	/**
	 * Retrieves a single file from a transmittal based on discipline.
	 *
	 * Given a transmittal ID, this method will search for a file within the
	 * transmittal that matches the current user's discipline. If a matching file
	 * is found, it is returned. If not, the method returns null.
	 *
	 * @param int $transmittal_id The ID of the transmittal to search.
	 *
	 * @return object|null The matching file record from tbldms_items, or null if none is found.
	 */
	public function get_file_transmittal_with_discipline($transmittal_id)
	{
		// Fetch records with the given parent ID
		$this->db->where('parent_id', $transmittal_id);
		$query = $this->db->get('tbldms_items');
		$records = $query->result();

		foreach ($records as $record) {
			// If it's an empty discipline folder, recurse into it
			if ($record->filetype === 'folder' && empty($record->discipline)) {
				$result = $this->get_file_transmittal_with_discipline($record->id);
				if (!empty($result)) {
					return $result;
				}
			}
			// Check if it's a file with non-empty discipline
			elseif ($record->filetype !== 'folder' && !empty($record->discipline)) {
				return $record;
			}
		}

		// Return null if no matching record is found
		return null;
	}

	public function get_dms_project($transmittal_id)
	{
		// Fetch the record where id equals the provided transmittal_id.
		$this->db->where('id', $transmittal_id);
		$query = $this->db->get('tbldms_items');
		$records = $query->result();

		// Check if the initial record was found.
		if (empty($records)) {
			return null; // or return a default value if needed
		}

		// Retrieve the parent_id from the fetched record.
		$parent_id = $records[0]->parent_id;

		// Fetch the record from tbldms_items where id equals the parent_id.
		$this->db->where('id', $parent_id);
		$query = $this->db->get('tbldms_items');
		$records2 = $query->result();

		// Check if the parent record exists.
		if (empty($records2)) {
			return null; // or return a default value if needed
		}

		// Return the 'name' from the parent record.
		return $records2[0]->name;
	}

	public function get_vendor_name($transmittal_id)
	{
		$this->db->where('item_id', $transmittal_id);
		$query = $this->db->get('tbldms_share_logs');
		$records = $query->result();

		$vendor_id = $records[0]->vendor;

		$this->db->where('userid', $vendor_id);
		$query = $this->db->get('tblpur_vendor');
		$records2 = $query->result();

		// Check if the parent record exists.
		if (empty($records2)) {
			return null; // or return a default value if needed
		}

		// Return the 'company' from the parent record.
		return $records2[0]->company;
	}

	/**
	 * Retrieves the count of records for each item associated with a given vendor.
	 *
	 * This function queries the 'tbldms_share_logs' table to obtain the count of records
	 * for each item_id where the 'vendor' matches the provided id. The results are grouped
	 * by item_id, allowing for a distinct count of records associated with each item.
	 *
	 * @param int $id The ID of the vendor to filter the records.
	 * @return array An array of results, each containing an item_id and its corresponding record count.
	 */


	public function get_vendor_item_counts($id)
	{
		// Select the item_id and the number of records for each item_id.
		$this->db->select('item_id, COUNT(item_id) as record_count');

		// Filter records where the vendor column equals the provided id.
		$this->db->where('vendor', $id);

		// Group the results by item_id to get a distinct count for each item.
		$this->db->group_by('item_id');

		// Execute the query from tbldms_share_logs.
		$query = $this->db->get('tbldms_share_logs');

		// Return the resulting array.
		return $query->result_array();
	}


	public function dms_get_table_data($transmittal_id)
	{
		$this->load->model('staff_model');
		$get_all_file = $this->get_file_transmittal($transmittal_id);
		$html = '';
		$get_project = $this->get_dms_project($transmittal_id);
		$get_vendor_name = $this->get_vendor_name($transmittal_id);
		$get_staff_name = $this->staff_model->get(get_staff_user_id());
		$discipline_id =  $this->get_file_transmittal_with_discipline($transmittal_id);

		$get_discipline = $this->get_discipline($discipline_id->discipline);
		$project_prefix = strtoupper(substr($get_project, 0, 3));
		$discipline_prefix = strtoupper(substr($get_discipline[0]['name'], 0, 3));
		$count = $this->get_vendor_item_counts(361);


		$company_logo = get_option('company_logo_dark');
		if (!empty($company_logo)) {
			$logo = '<img src="' . base_url('uploads/company/' . $company_logo) . '" width="130" height="100">';
		}
		$html = '<!DOCTYPE html>
					<html lang="en">
					<head>
					<meta charset="UTF-8">
					<title>Document Transmittal</title>
					<style>
						body {
						font-family: Arial, sans-serif;
						margin: 20px;
						color: #000;
						}
						.header,
						.footer,
						.additional-info,
						.page-info {
						margin-bottom: 20px;
						}
						.header {
						display: flex;
						justify-content: space-between;
						align-items: flex-start;
						border-bottom: 2px solid #000;
						padding-bottom: 10px;
						}
						.logo {
						font-size: 20px;
						font-weight: bold;
						}
						.transmittal-info {
						text-align: right;
						}
						.transmittal-info h1 {
						font-size: 24px;
						margin: 0 0 5px;
						text-transform: uppercase;
						}
						.content p {
						margin: 5px 0;
						}
						.table {
						width: 100%;
						border-collapse: collapse;
						margin-top: 20px;
						}
						.table th,
						.table td {
						border: 1px solid #000;
						padding: 8px;
						text-align: left;
						}
						.table th {
						background-color: #eee;
						}
						.footer p, .additional-info p, .page-info p {
						margin: 5px 0;
						}
					</style>
					</head>
					<body><table style="width: 100%; border-collapse: collapse;">
					<tr>
						<td style="vertical-align: top; width: 40%;">
						' . $logo . '
						</td>
						<td style="vertical-align: top; text-align: right; width: 60%;">
						<h1>Drawing Transmittal</h1>
						<p><strong>Transmittal No.:</strong> ' . $project_prefix . '-BIL-' . $discipline_prefix . '-TRS-' . date('Y') . '00' . count($count) . '</p>
						<p><strong>Date of Issue:</strong> ' . date('d M, Y') . '</p>
						</td>
					</tr>
					</table>
					<table style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
						<tr>
							<td style="vertical-align: top; width: 40%;">
								<p style="margin-bottom: 0px;"><strong>Project Name :</strong> ' . $get_project . '</p>
								<p><strong>Issuer :</strong> ' . $get_staff_name->firstname . ' ' . $get_staff_name->lastname . '</p>
							</td>
						
							<td style="vertical-align: top; text-align: right; width: 60%;">
								<p><strong>Discipline :</strong> ' . $get_discipline[0]['name'] . '</p>
								<p><strong>Recipient :</strong> ' . $get_vendor_name . '</p>
							</td>
						</tr>
					</table><br><br>
					<!-- Table header for document details -->
					<table class="table" style="width: 100%; border-collapse: collapse; border: none;">
					<thead>
						<tr style="border-bottom: 1px solid #000;">
						<th style="width:5%;">#</th>
						<th style="width:95%;">Document Title</th>
						</tr>
					</thead><tbody>';
		$sr = 1;
		foreach ($get_all_file as $key => $value) {
			$html .= '<tr style="border-bottom: 1px solid #000;">
						<td style="width:5%;">' . $sr++ . '</td>
						<td style="width:95%;">' . $value->name . '</td>
					</tr>';
		}
		$html .= '</tbody>
					</table><br><br>
					<div class="footer">
						<table style="width: 100%; border-collapse: collapse;">
						<tr>
						<td style="width: 50%; vertical-align: top; padding: 5px;">
							<p><strong>Sent by:</strong> Basilius Internațional LLP</p>
						</td>
						<td style="width: 50%; vertical-align: top; padding: 5px;text-align:right">
							<p><strong>Received by:</strong> ' . $get_vendor_name . '</p>
						</td>
						</tr>
						<tr>
						<td style="width: 50%; vertical-align: top; padding: 5px;">
							<p><strong>Date:</strong> ' . date('d M, Y') . '</p>
						</td>
						<td style="width: 50%; vertical-align: top; padding: 5px;">
							
						</td>
						</tr>
						<tr>
						<td colspan="2" style="text-align: center; padding: 10px 5px 5px;">
							<p>Please acknowledge receipt by signing, dating, and returning a copy of this Transmittal.</p>
						</td>
						</tr>
						</table>
						</div>


					</body>
					</html>
					';

		return $html;
	}


	/**
	 * add share document
	 * @param array $data 
	 */
	public function add_share_document($data)
	{
		if (isset($data['expiration_date']) && $data['expiration_date'] != '') {
			$data['expiration_date'] = drawing_dmg_format_date_time($data['expiration_date']);
		}
		if (!isset($data['expiration'])) {
			$data['expiration'] = 0;
		}
		if (isset($data['staff']) && $data['staff'] != '') {
			$data['staff'] = implode(',', $data['staff']);
		}
		if (isset($data['vendor']) && $data['vendor'] != '') {
			$data['vendor'] = implode(',', $data['vendor']);
		}
		if (isset($data['vendor_contact']) && $data['vendor_contact'] != '') {
			$vendor_contact = $data['vendor_contact'];
			$data['vendor_contact'] = implode(',', $data['vendor_contact']);
		}
		if (isset($data['customer_group']) && $data['customer_group'] != '') {
			$data['customer_group'] = implode(',', $data['customer_group']);
		}
		$this->db->insert(db_prefix() . 'dms_share_logs', $data);

		if ($data['share_to'] == 'staff') {
			if (!empty($data['staff'])) {
				$staff_list = $data['staff'];
				$staff_list = explode(',', $staff_list);
				$this->db->where_in('staffid', $staff_list);
				$staff_list = $this->db->get(db_prefix() . 'staff')->result_array();
				foreach ($staff_list as $key => $value) {
					$data_send_mail = new stdClass();
					$data_send_mail->email = trim($value['email']);
					$data_send_mail->link = '<a href="' . admin_url('drawing_management?id=' . $data['item_id']) . '">' . drawing_dmg_get_file_name($data['item_id']) . '</a>';
					$data_send_mail->message = $data['message'];
					$template = mail_template('share', 'drawing_management', $data_send_mail);
					$template->send();
				}
			}
		}
		if ($data['share_to'] == 'vendor') {
			if (isset($vendor_contact) && !empty($vendor_contact)) {
				$this->db->select('id, email');
				$this->db->where_in('id', $vendor_contact);
				$pur_contacts = $this->db->get(db_prefix() . 'pur_contacts')->result_array();
				if (!empty($pur_contacts)) {

					foreach ($pur_contacts as $key => $con) {

						// Generate the transmittal HTML table data using your DMS function.
						$transmittal = $this->dms_get_table_data($data['item_id']);

						try {
							$pdf = $this->dmshare_pdf($transmittal);
						} catch (Exception $e) {
							echo pur_html_entity_decode($e->getMessage());
							die;
						}

						// Output the PDF in the browser as 'test.pdf'
						$attach = $pdf->Output('Transmittal.pdf', 'S');

						$data_send_mail = new stdClass();
						$data_send_mail->email = trim($con['email']);
						$data_send_mail->link = '<a href="' . admin_url('drawing_management?id=' . $data['item_id']) . '">' . drawing_dmg_get_file_name($data['item_id']) . '</a>';
						$data_send_mail->message = $data['message'];
						$template = mail_template('share', 'drawing_management', $data_send_mail);
						$template->add_attachment([
							'attachment' => $attach,
							'filename'   => str_replace('/', '-', 'Transmittal.pdf'),
							'type'       => 'application/pdf',
						]);
						$template->send();

						if (!is_dir(DRAWING_MANAGEMENT_PATH . 'transmittal')) {
							mkdir(DRAWING_MANAGEMENT_PATH . 'transmittal', 0755, true);
						}
						$pdf_filename = 'Transmittal_' . time() . '.pdf';
						$pdf_path = DRAWING_MANAGEMENT_PATH . 'transmittal/' . $pdf_filename;
						file_put_contents($pdf_path, $attach);

						$transmittal_data = array();
						$transmittal_data['vendor_contact'] = $con['id'];
						$transmittal_data['pdf_filename'] = $pdf_filename;
						$transmittal_data['created'] = date('Y-m-d H:i:s');
						$this->db->insert(db_prefix() . 'dms_share_transmittal', $transmittal_data);
					}
				}
			}
		}
		return $this->db->insert_id();
	}
	public function dmshare_pdf($transmittal)
	{
		return app_pdf(
			'transmittal',
			module_dir_path(DRAWING_MANAGEMENT_MODULE_NAME, 'libraries/pdf/Dms_share_pdf'),
			$transmittal
		);
	}

	/**
	 * update share document
	 * @param array $data 
	 */
	public function update_share_document($data)
	{
		if (isset($data['expiration_date']) && $data['expiration_date'] != '') {
			$data['expiration_date'] = drawing_dmg_format_date_time($data['expiration_date']);
		}
		if (!isset($data['expiration'])) {
			$data['expiration'] = 0;
		}
		if (isset($data['staff']) && $data['staff'] != '') {
			$data['staff'] = implode(',', $data['staff']);
		}
		if (isset($data['vendor']) && $data['vendor'] != '') {
			$data['vendor'] = implode(',', $data['vendor']);
		}
		if (isset($data['vendor_contact']) && $data['vendor_contact'] != '') {
			$data['vendor_contact'] = implode(',', $data['vendor_contact']);
		}
		if (isset($data['customer_group']) && $data['customer_group'] != '') {
			$data['customer_group'] = implode(',', $data['customer_group']);
		}
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'dms_share_logs', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}


	/**
	 * get share_logs
	 * @param  integer $id     
	 * @param  string $where  
	 * @param  string $select 
	 * @return array or object         
	 */
	public function get_share_log($id, $where = '', $select = '')
	{
		if ($select != '') {
			$this->db->select($select);
		}
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'dms_share_logs')->row();
		} else {
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dms_share_logs')->result_array();
		}
	}

	/**
	 * delete share
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_share($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'dms_share_logs');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	public function get_share_user_list($data)
	{
		$result = '';
		if ($data['share_to'] == 'staff') {
			$staff_arr = explode(',', $data['staff']);
			foreach ($staff_arr as $key => $id) {
				$result .= get_staff_full_name($id) . ', ';
			}
			if ($result != '') {
				$result = '<i class="fa fa-user-circle"></i> ' . rtrim($result, ', ');
			}
		}
		if ($data['share_to'] == 'customer') {
			$staff_arr = explode(',', $data['customer']);
			foreach ($staff_arr as $key => $id) {
				$result .= get_company_name($id) . ', ';
			}
			if ($result != '') {
				$result = '<i class="fa fa-user-o"></i> ' . rtrim($result, ', ');
			}
		}
		if ($data['share_to'] == 'vendor') {
			$staff_arr = explode(',', $data['vendor']);
			foreach ($staff_arr as $key => $id) {
				$result .= get_vendor_company_name($id) . ', ';
			}
			if ($result != '') {
				$result = '<i class="fa fa-user-o"></i> ' . rtrim($result, ', ');
			}
		}
		if ($data['share_to'] == 'customer_group') {
			$staff_arr = explode(',', $data['customer_group']);
			foreach ($staff_arr as $key => $id) {
				$this->db->select('name');
				$this->db->where('id', $id);
				$group_data = $this->db->get(db_prefix() . 'customers_groups')->row();
				if ($group_data) {
					$result .= $group_data->name . ', ';
				}
			}
			if ($result != '') {
				$result = '<i class="fa fa-users" aria-hidden="true"></i> ' . rtrim($result, ', ');
			}
		}
		return $result;
	}

	/**
	 * get item share to me
	 * @param  string $type 
	 */
	public function get_item_share_to_me($parse_string = false, $type = 'staff')
	{
		$current_date = date('Y-m-d H:i:s');
		$list = [];
		if ($type == 'staff') {
			$userid = get_staff_user_id();
			$data = $this->db->query('select distinct(item_id) as id from ' . db_prefix() . 'dms_share_logs where share_to = "staff" AND ((find_in_set(' . $userid . ', staff) AND expiration = 1 AND expiration_date > \'' . $current_date . '\') OR (find_in_set(' . $userid . ', staff) AND expiration = 0))')->result_array();
			foreach ($data as $key => $value) {
				$list[] = $value['id'];
			}
		}
		if ($type == 'customer') {
			$userid = get_client_user_id();
			$groups_query = '';
			$client_groups = $this->client_groups_model->get_customer_groups($userid);
			if (is_array($client_groups) && count($client_groups) > 0) {
				foreach ($client_groups as $key => $group) {
					$groups_query .= '((find_in_set(' . $group['groupid'] . ', customer_group) AND expiration = 1 AND expiration_date > \'' . $current_date . '\') OR (find_in_set(' . $group['groupid'] . ', customer_group) AND expiration = 0))  OR ';
				}
				if ($groups_query != '') {
					$groups_query = rtrim($groups_query, ' OR ');
					$groups_query = ' OR (share_to = "customer_group" AND (' . $groups_query . '))';
				}
			}
			$customer_query = ' (share_to = "customer" AND ((find_in_set(' . $userid . ', customer) AND expiration = 1 AND expiration_date > \'' . $current_date . '\') OR (find_in_set(' . $userid . ', customer) AND expiration = 0)))';
			$data = $this->db->query('select distinct(item_id) as id from ' . db_prefix() . 'dms_share_logs where' . $customer_query . $groups_query)->result_array();
			foreach ($data as $key => $value) {
				$list[] = $value['id'];
			}
		}
		if ($parse_string == false) {
			return $list;
		} else {
			if (count($list) > 0) {
				return implode(',', $list);
			} else {
				return '0';
			}
		}
	}

	/**
	 * get child id list from parent
	 * @param  integer $parent_id 
	 * @param  array  $result    
	 * @return array            
	 */
	public function get_child_id_list_from_parent($parent_id, $result = [])
	{
		$data_item = $this->get_item('', 'parent_id = ' . $parent_id);
		foreach ($data_item as $key => $value) {
			$result[] = $value['id'];
			$result = $this->get_child_id_list_from_parent($value['id'], $result);
		}
		return $result;
	}

	/**
	 * check permission share to me
	 * @param  integer $item_id 
	 * @param  string $type    
	 */
	public function check_permission_share_to_me($item_id, $type = 'staff')
	{
		$array = [];
		$share_to_me = $this->drawing_management_model->get_item_share_to_me(false, $type);
		foreach ($share_to_me as $key => $id) {
			$array[] = $id;
			$array = $this->get_child_id_list_from_parent($id, $array);
		}
		if (in_array($item_id, $array)) {
			return true;
		}
		return false;
	}

	/**
	 * getpermissionitemsharetome
	 * @param  integer $item_id 
	 * @param  string $type    
	 * @return [type]          
	 */
	public function drawing_get_permission_item_share_to_me($item_id, $type = 'staff')
	{
		$current_date = date('Y-m-d H:i:s');
		$list = [];
		if ($type == 'staff') {
			$userid = get_staff_user_id();
			$data = $this->db->query('select permission from ' . db_prefix() . 'dms_share_logs where item_id = ' . $item_id . ' AND share_to = "staff" AND ((find_in_set(' . $userid . ', staff) AND expiration = 1 AND expiration_date > \'' . $current_date . '\') OR (find_in_set(' . $userid . ', staff) AND expiration = 0))')->result_array();
			foreach ($data as $key => $value) {
				$list[] = $value['permission'];
			}
		}
		if ($type == 'customer') {
			$userid = get_client_user_id();
			$groups_query = '';
			$client_groups = $this->client_groups_model->get_customer_groups($userid);
			if (is_array($client_groups) && count($client_groups) > 0) {
				foreach ($client_groups as $key => $group) {
					$groups_query .= '((find_in_set(' . $group['groupid'] . ', customer_group) AND expiration = 1 AND expiration_date > \'' . $current_date . '\') OR (find_in_set(' . $group['groupid'] . ', customer_group) AND expiration = 0))  OR ';
				}
				if ($groups_query != '') {
					$groups_query = rtrim($groups_query, ' OR ');
					$groups_query = ' OR (share_to = "customer_group" AND (' . $groups_query . '))';
				}
			}
			$customer_query = ' (share_to = "customer" AND ((find_in_set(' . $userid . ', customer) AND expiration = 1 AND expiration_date > \'' . $current_date . '\') OR (find_in_set(' . $userid . ', customer) AND expiration = 0)))';
			$data = $this->db->query('select permission from ' . db_prefix() . 'dms_share_logs where item_id = ' . $item_id . ' AND (' . $customer_query . $groups_query . ')')->result_array();
			foreach ($data as $key => $value) {
				$list[] = $value['permission'];
			}
		}
		return $list;
	}

	/**
	 * breadcrum array for share
	 * @param  integer $id 
	 * @return array     
	 */
	public function breadcrum_array_for_share($id, $share_id, $array = [])
	{
		$data_item = $this->get_item($id, '', 'master_id, parent_id, name, id');
		if ($data_item && is_object($data_item)) {
			$array[] = ['id' => $id, 'parent_id' => $data_item->parent_id, 'name' => $data_item->name];
			if (is_numeric($data_item->parent_id) && $data_item->parent_id > 0 && $id = $data_item->parent_id) {
				if (!in_array($data_item->parent_id, $share_id)) {
					return $array;
				}
				$array = $this->breadcrum_array_for_share($id, $share_id, $array);
			}
		}
		return $array;
	}

	/**
	 * breadcrum array
	 * @param  integer $id 
	 * @return array     
	 */
	public function breadcrum_array2($id, $creator_type = 'staff')
	{
		$array = [];
		$share_id = $this->get_item_share_to_me(false, $creator_type);
		if (is_array($share_id) && count($share_id) > 0) {
			$array = $this->breadcrum_array_for_share($id, $share_id);
		}
		return $array;
	}

	/**
	 * delete approval setting
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_approve_setting($id)
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'dms_approval_setting');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
		}
		return false;
	}

	/**
	 * add approval process
	 * @param array $data 
	 * @return boolean 
	 */
	public function add_approval_process($data)
	{
		unset($data['approval_setting_id']);
		if (isset($data['staff'])) {
			$setting = [];
			foreach ($data['staff'] as $key => $value) {
				$node = [];
				$node['approver'] = 'specific_personnel';
				$node['staff'] = $data['staff'][$key];

				$setting[] = $node;
			}
			unset($data['approver']);
			unset($data['staff']);
		}
		if (!isset($data['choose_when_approving'])) {
			$data['choose_when_approving'] = 0;
		}
		if (isset($data['departments'])) {
			$data['departments'] = implode(',', $data['departments']);
		}
		if (isset($data['job_positions'])) {
			$data['job_positions'] = implode(',', $data['job_positions']);
		}
		$data['setting'] = json_encode($setting);
		if (isset($data['notification_recipient'])) {
			$data['notification_recipient'] = implode(",", $data['notification_recipient']);
		}
		$this->db->insert(db_prefix() . 'dms_approval_setting', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			return true;
		}
		return false;
	}

	/**
	 * update approval process
	 * @param  integer $id   
	 * @param  array $data 
	 * @return boolean       
	 */
	public function update_approval_process($id, $data)
	{
		if (isset($data['staff'])) {
			$setting = [];
			foreach ($data['staff'] as $key => $value) {
				$node = [];
				$node['approver'] = 'specific_personnel';
				$node['staff'] = $data['staff'][$key];

				$setting[] = $node;
			}
			unset($data['approver']);
			unset($data['staff']);
		}

		if (!isset($data['choose_when_approving'])) {
			$data['choose_when_approving'] = 0;
		}
		$data['setting'] = json_encode($setting);
		if (isset($data['departments'])) {
			$data['departments'] = implode(',', $data['departments']);
		} else {
			$data['departments'] = '';
		}
		if (isset($data['job_positions'])) {
			$data['job_positions'] = implode(',', $data['job_positions']);
		} else {
			$data['job_positions'] = '';
		}
		if (isset($data['notification_recipient'])) {
			$data['notification_recipient'] = implode(",", $data['notification_recipient']);
		}
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'dms_approval_setting', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get approval setting
	 * @param  integer $id 
	 * @return integer     
	 */
	public function get_approval_setting($id)
	{
		if ($id != '') {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'dms_approval_setting')->row();
		} else {
			return $this->db->get(db_prefix() . 'dms_approval_setting')->result_array();
		}
	}


	/**
	 * get approve setting
	 * @param  string  $type         
	 * @param  boolean $only_setting 
	 * @return boolean                
	 */
	public function get_approve_setting($type, $only_setting = true)
	{
		$this->db->select('*');
		$this->db->where('related', $type);
		$approval_setting = $this->db->get(db_prefix() . 'dms_approval_setting')->row();
		if ($approval_setting) {
			if ($only_setting == false) {
				return $approval_setting;
			} else {
				return json_decode($approval_setting->setting);
			}
		} else {
			return false;
		}
	}

	/**
	 * send request approve
	 * @param  array $data     
	 * @param  integer $staff_id 
	 * @return bool           
	 */
	public function send_request_approve($rel_id, $rel_type, $staff_id = '')
	{
		$data_new = $this->get_approve_setting($rel_type, true);
		$data_setting = $this->get_approve_setting($rel_type, false);
		$this->delete_approval_details($rel_id, $rel_type);
		$date_send = date('Y-m-d H:i:s');
		foreach ($data_new as $value) {
			$row = [];
			$row['notification_recipient'] = $data_setting->notification_recipient;
			$row['approval_deadline'] = date('Y-m-d', strtotime(date('Y-m-d') . ' +' . $data_setting->number_day_approval . ' day'));
			$row['staffid'] = $value->staff;
			$row['date_send'] = $date_send;
			$row['rel_id'] = $rel_id;
			$row['rel_type'] = $rel_type;
			$row['sender'] = $staff_id;
			$this->db->insert(db_prefix() . 'dms_approval_details', $row);
		}

		$this->db->where('rel_type', $rel_type);
		$this->db->where('rel_id', $rel_id);
		$existing_task = $this->db->get(db_prefix() . 'tasks')->row();
		if (!$existing_task) {
			foreach ($data_new as $value) {
				$taskDetail = $this->get_item($rel_id);
				$taskName = 'Approve [' . $taskDetail->name . ']';
				$taskData = [
					'name'      => $taskName,
					'is_public' => 1,
					'startdate' => _d(date('Y-m-d')),
					'duedate'   => _d(date('Y-m-d', strtotime('+3 day'))),
					'priority'  => 3,
					'rel_type'  => $rel_type,
					'rel_id'    => $rel_id,
				];
				$task_id =  $this->tasks_model->add($taskData);
				$assignss = [
					'staffid' => $value->staff,
					'taskid'  =>  $task_id
				];
				$this->db->insert('tbltask_assigned', $assignss);
			}
		}
		return true;
	}

	/**
	 * delete approval details
	 * @param  string $rel_id   
	 * @param  string $rel_type 
	 * @return boolean           
	 */
	public function delete_approval_details($rel_id, $rel_type)
	{
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', $rel_type);
		$this->db->delete(db_prefix() . 'dms_approval_details');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get item from hash
	 * @param  string $hash 
	 */
	public function get_item_from_hash($hash)
	{
		$this->db->where('hash', $hash);
		return $this->db->get(db_prefix() . 'dms_items')->row();
	}

	/**
	 * get approval details
	 * @param  integer $rel_id   
	 * @param  string $rel_type 
	 * @return integer           
	 */
	public function get_approval_details($rel_id, $rel_type)
	{
		if ($rel_id != '') {
			$this->db->where('rel_id', $rel_id);
			$this->db->where('rel_type', $rel_type);
			$this->db->order_by('id');
			return $this->db->get(db_prefix() . 'dms_approval_details')->result_array();
		} else {
			return $this->db->get(db_prefix() . 'dms_approval_details')->result_array();
		}
	}

	/**
	 * change approve document
	 * @param  array $data 
	 * @return boolean       
	 */
	public function change_approve_document($data)
	{
		$this->db->where('rel_id', $data['rel_id']);
		$this->db->where('rel_type', $data['rel_type']);
		$this->db->where('staffid', $data['staffid']);
		$this->db->update(db_prefix() . 'dms_approval_details', $data);
		if ($this->db->affected_rows() > 0) {
			// If has rejected then change status to finish approve
			if ($data['approve'] == 2) {
				$this->db->where('id', $data['rel_id']);
				$this->db->update(db_prefix() . 'dms_items', ['approve' => 2]);
				return true;
			}

			$count_approve_total = $this->count_approve($data['rel_id'], $data['rel_type'])->count;
			$count_approve = $this->count_approve($data['rel_id'], $data['rel_type'], 1)->count;
			$count_rejected = $this->count_approve($data['rel_id'], $data['rel_type'], 2)->count;

			if (($count_approve + $count_rejected) == $count_approve_total) {
				if ($count_approve_total == $count_approve) {
					$this->db->where('id', $data['rel_id']);
					$this->db->update(db_prefix() . 'dms_items', ['approve' => 1]);

					// Move items
					$data_item = $this->get_item($data['rel_id']);
					if ($data_item && $data_item->move_after_approval == 1 && is_numeric($data_item->folder_after_approval) && $data_item->folder_after_approval > 0) {
						$this->move_item($data_item->folder_after_approval, $data['rel_id']);
					}
				} else {
					$this->db->where('id', $data['rel_id']);
					$this->db->update(db_prefix() . 'dms_items', ['approve' => 2]);
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * count approve
	 * @param integer $rel_id   
	 * @param integer $rel_type 
	 * @param  string $approve  
	 * @return object        
	 */
	public function count_approve($rel_id, $rel_type, $approve = '')
	{
		if ($approve == '') {
			return $this->db->query('SELECT count(distinct(staffid)) as count FROM ' . db_prefix() . 'dms_approval_details where rel_id = ' . $rel_id . ' and rel_type = \'' . $rel_type . '\'')->row();
		} else {
			return $this->db->query('SELECT count(distinct(staffid)) as count FROM ' . db_prefix() . 'dms_approval_details where rel_id = ' . $rel_id . ' and rel_type = \'' . $rel_type . '\' and approve = ' . $approve . '')->row();
		}
	}


	/**
	 * send request approve
	 * @param  array $data     
	 * @param  integer $staff_id 
	 * @return bool           
	 */
	public function send_request_approve_eid($rel_id, $rel_type, $staff_id = '')
	{
		$data_new = $this->get_approve_setting($rel_type, true);
		$data_setting = $this->get_approve_setting($rel_type, false);
		$this->delete_approval_details($rel_id, $rel_type);
		$date_send = date('Y-m-d H:i:s');
		foreach ($data_new as $value) {
			$row = [];
			$row['notification_recipient'] = $data_setting->notification_recipient;
			$row['approval_deadline'] = date('Y-m-d', strtotime(date('Y-m-d') . ' +' . $data_setting->number_day_approval . ' day'));
			$row['staffid'] = $value->staff;
			$row['date_send'] = $date_send;
			$row['rel_id'] = $rel_id;
			$row['rel_type'] = $rel_type;
			$row['sender'] = $staff_id;
			$this->db->insert(db_prefix() . 'dms_approval_detail_eids', $row);
		}
		return true;
	}

	/**
	 * get approval details
	 * @param  integer $rel_id   
	 * @param  string $rel_type 
	 * @return integer           
	 */
	public function get_approval_detail_eids($rel_id, $rel_type)
	{
		if ($rel_id != '') {
			$this->db->where('rel_id', $rel_id);
			$this->db->where('rel_type', $rel_type);
			$this->db->order_by('id');
			return $this->db->get(db_prefix() . 'dms_approval_detail_eids')->result_array();
		} else {
			return $this->db->get(db_prefix() . 'dms_approval_detail_eids')->result_array();
		}
	}

	/**
	 * update signer info
	 * @param  integer $id   
	 * @param  array $data 
	 * @return boolean       
	 */
	public function update_signer_info($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'dms_approval_detail_eids', $data);
		if ($this->db->affected_rows() > 0) {
			$this->db->where('id', $id);
			$signer_data = $this->db->get(db_prefix() . 'dms_approval_detail_eids')->row();
			if ($signer_data) {
				$count_approve_total = $this->count_approve_eids($data['rel_id'], $data['rel_type'])->count;
				$count_approve = $this->count_approve_eids($data['rel_id'], $data['rel_type'], 1)->count;
				$count_rejected = $this->count_approve_eids($data['rel_id'], $data['rel_type'], 2)->count;
				if (($count_approve + $count_rejected) == $count_approve_total) {
					if ($count_approve_total == $count_approve) {
						$this->db->where('id', $data['rel_id']);
						$this->db->update(db_prefix() . 'dms_items', ['sign_approve' => 1]);
						// Move items
						$data_item = $this->get_item($data['rel_id']);
						if ($data_item && $data_item->move_after_approval == 1 && is_numeric($data_item->folder_after_approval) && $data_item->folder_after_approval > 0) {
							$this->move_item($data_item->folder_after_approval, $data['rel_id']);
						}
					} else {
						$this->db->where('id', $data['rel_id']);
						$this->db->update(db_prefix() . 'dms_items', ['sign_approve' => 2]);
					}
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * count approve
	 * @param integer $rel_id   
	 * @param integer $rel_type 
	 * @param  string $approve  
	 * @return object        
	 */
	public function count_approve_eids($rel_id, $rel_type, $approve = '')
	{
		if ($approve == '') {
			return $this->db->query('SELECT count(distinct(staffid)) as count FROM ' . db_prefix() . 'dms_approval_detail_eids where rel_id = ' . $rel_id . ' and rel_type = \'' . $rel_type . '\'')->row();
		} else {
			return $this->db->query('SELECT count(distinct(staffid)) as count FROM ' . db_prefix() . 'dms_approval_detail_eids where rel_id = ' . $rel_id . ' and rel_type = \'' . $rel_type . '\' and approve = ' . $approve . '')->row();
		}
	}

	/**
	 * change reminder item id
	 * @param  integer $old_item_id 
	 * @param  integer $new_item_id 
	 * @return boolean              
	 */
	public function change_reminder_item_id($old_item_id, $new_item_id)
	{
		$this->db->where('file_id', $old_item_id);
		$this->db->update(db_prefix() . 'dms_remiders', ['file_id' => $new_item_id]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * change share to item id
	 * @param  integer $old_item_id 
	 * @param  integer $new_item_id 
	 * @return boolean              
	 */
	public function change_share_to_item_id($old_item_id, $new_item_id)
	{
		$this->db->where('item_id', $old_item_id);
		$this->db->update(db_prefix() . 'dms_share_logs', ['item_id' => $new_item_id]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * change share to item id
	 * @param  integer $old_item_id 
	 * @param  integer $new_item_id 
	 * @return boolean              
	 */
	public function change_approve_item_id($old_item_id, $new_item_id, $rel_type = 'document')
	{
		$this->db->where('rel_id', $old_item_id);
		$this->db->where('rel_type', $rel_type);
		$this->db->update(db_prefix() . 'dms_approval_details', ['rel_id' => $new_item_id]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * change share to item id
	 * @param  integer $old_item_id 
	 * @param  integer $new_item_id 
	 * @return boolean              
	 */
	public function change_sign_approve_item_id($old_item_id, $new_item_id, $rel_type = 'document')
	{
		$this->db->where('rel_id', $old_item_id);
		$this->db->where('rel_type', $rel_type);
		$this->db->update(db_prefix() . 'dms_approval_detail_eids', ['rel_id' => $new_item_id]);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * convert html to word
	 * @param  string $html 
	 * @param  string $path 
	 */
	public function convert_html_to_word($html, $path)
	{
		require_once(module_dir_path(DRAWING_MANAGEMENT_MODULE_NAME) . '/third_party/vendor/autoload.php');
		$phpWord = new PhpWord();
		$section = $phpWord->addSection();
		Html::addHtml($section, $html);
		$phpWord->save($path, 'Word2007');
	}

	public function convert_html_file_to_word_api($from_path, $to_path)
	{
		require_once(module_dir_path(DRAWING_MANAGEMENT_MODULE_NAME) . '/third_party/convertio/autoload.php');
		$API = new Convertio("9cebcf2b7088b5c95a637a07cf395936");
		$API->settings(array('api_protocol' => 'http', 'http_timeout' => 10));
		$API->start($from_path, 'docx')->wait()->download($to_path)->delete();
	}

	public function get_discipline($id = '')
	{
		if (!empty($id)) {
			$this->db->where('id', $id);
		}
		return $this->db->get(db_prefix() . 'dms_discipline')->result_array();
	}

	public function searchFilesAndFolders($query)
	{

		// Fetch all items matching the search query
		$this->db->like('name', $query);
		$query = $this->db->get(db_prefix() . 'dms_items');
		$results = $query->result();

		// Fetch root folder (folder without a parent_id)
		$this->db->where('filetype', 'folder');
		$this->db->where('parent_id', NULL);
		$rootFolderQuery =  $this->db->get(db_prefix() . 'dms_items');
		$rootFolder = $rootFolderQuery->row();

		// Add breadcrumb path for each result
		foreach ($results as $key => $item) {
			$breadcrumbs = [];
			if ($rootFolder) {
				$breadcrumbs[] = $rootFolder->name;  // Add root folder to every breadcrumb
			}
			$breadcrumbs = array_merge($breadcrumbs, $this->getLimitedBreadcrumb($item->parent_id));  // Get parent and grandparent
			$results[$key]->breadcrumb = $breadcrumbs;
		}

		return $results;
	}
	public function filterFilesAndFolders($design_stage = null, $discipline = null, $purpose = null, $status = null, $controlled_document = null)
	{

		$module_name = 'drawing_management';
		$ds = 'design_stage';
		$design_stage = !empty($design_stage) ? $design_stage : NULL;
		update_module_filter($module_name, $ds, $design_stage);
		$d = 'discipline';
		$discipline_string = !empty($discipline) ? implode(',', $discipline) : NULL;
		update_module_filter($module_name, $d, $discipline_string);
		$p = 'purpose';
		$purpose = !empty($purpose) ? $purpose : NULL;
		update_module_filter($module_name, $p, $purpose);
		$s = 'status';
		$status = !empty($status) ? $status : NULL;
		update_module_filter($module_name, $s, $status);
		$cd = 'controlled_document';
		$controlled_document = !empty($controlled_document) ? $controlled_document : NULL;
		update_module_filter($module_name, $cd, $controlled_document);
		$this->db->select('*');
		$this->db->from(db_prefix() . 'dms_items');

		// Group conditions so that at least one filter matches
		if (!empty($design_stage) || (!empty($discipline) && is_array($discipline)) || !empty($purpose) || !empty($status) || !empty($controlled_document)) {

			// Apply design stage filter if provided
			if (!empty($design_stage)) {
				$this->db->where('design_stage', $design_stage);
			}

			// Apply discipline filter if provided (handling multiple values)
			if (!empty($discipline) && is_array($discipline)) {
				$this->db->group_start();
				foreach ($discipline as $d) {
					$this->db->or_where("FIND_IN_SET(" . $this->db->escape($d) . ", discipline) >", 0);
				}
				$this->db->group_end();
			}

			// Apply purpose stage filter if provided
			if (!empty($purpose)) {
				$this->db->where('purpose', $purpose);
			}
			// Apply status stage filter if provided
			if (!empty($status)) {
				$this->db->where('status', $status);
			}

			// Apply controlled document filter if provided
			if (!empty($controlled_document)) {
				if ($controlled_document == 1) {
					$this->db->where('controlled_document', $controlled_document);
				}
			}

			$query = $this->db->get();
			$results = $query->result();
		}

		if ($results) {
			// Fetch root folder (folder without a parent_id)
			$this->db->where('filetype', 'folder');
			$this->db->where('parent_id', NULL);
			$rootFolderQuery = $this->db->get(db_prefix() . 'dms_items');
			$rootFolder = $rootFolderQuery->row();

			// Add breadcrumb path for each result
			foreach ($results as $key => $item) {
				$breadcrumbs = [];

				if ($rootFolder) {
					$breadcrumbs[] = $rootFolder->name;  // Add root folder to every breadcrumb
				}

				$breadcrumbs = array_merge($breadcrumbs, $this->getLimitedBreadcrumb($item->parent_id));  // Get parent and grandparent
				$results[$key]->breadcrumb = $breadcrumbs;
			}

			return $results;
		}
	}


	private function getLimitedBreadcrumb($parent_id)
	{
		$breadcrumb = [];
		$level = 0;

		// Loop to find up to 2 levels of breadcrumbs
		while ($parent_id !== NULL && $level < 2) {
			$this->db->where('id', $parent_id);
			$query = $this->db->get('tbldms_items');
			$parent = $query->row();

			if ($parent) {
				$breadcrumb[] = $parent->name;
				$parent_id = $parent->parent_id;
				$level++;
			} else {
				break;
			}
		}

		return array_reverse($breadcrumb);  // Reverse to show root first
	}

	public function get_primary_vendors($data)
	{
		$response = '';
		$this->db->select('id, email');
		$this->db->where_in('userid', $data);
		$pur_contacts = $this->db->get(db_prefix() . 'pur_contacts')->result_array();
		if (!empty($pur_contacts)) {
			$selected_contacts = array_column($pur_contacts, 'id');
			$response = render_select('vendor_contact[]', $pur_contacts, array('id', 'email'), '' . _l('vendor_contact'), $selected_contacts, ['multiple' => 1, 'data-actions-box' => true], [], '', '', false);
		}
		return $response;
	}

	public function delete_pdf_attachment($id)
	{
		// echo $id;
		// die;
		$get_pdf_attachment = $this->get_item($id);
		if ($get_pdf_attachment->pdf_attachment != '') {

			$path = DRAWING_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/pdf_attachments/' . $id . '/' . $get_pdf_attachment->pdf_attachment;

			if (file_exists($path)) {

				unlink($path);
			}

			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'dms_items', ['pdf_attachment' => '']);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
		}
	}

	public function get_default_dms_project($project_id)
	{
		$master_id = 3;
		$this->db->select('id');
		$this->db->where('project_id', $project_id);
		$data = $this->db->get(db_prefix() . 'dms_items')->row();
		if(!empty($data)) {
			$master_id = $data->id;
		}
		return $master_id;
	}
}
