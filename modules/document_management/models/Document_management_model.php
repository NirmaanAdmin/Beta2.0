<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use \Convertio\Convertio;

/**
 * Document management model
 */
class document_management_model extends app_model
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
			return $this->db->get(db_prefix() . 'dmg_items')->row();
		} else {
			$this->db->order_by('position', 'ASC');
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dmg_items')->result_array();
		}
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
			$this->db->delete(db_prefix() . 'dmg_items');
			if ($this->db->affected_rows() > 0) {
				if ($data_item->filetype != 'folder') {
					// Delete physical file
					$this->delete_file_item(DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/' . $data_item->name);
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
		$this->db->insert(db_prefix() . 'dmg_items', $data);
		return $this->db->insert_id();
	}
	/**
	 * update folder
	 * @param array $data 
	 * @return boolean 
	 */
	public function update_item($data)
	{
		if (isset($data['duedate']) && $data['duedate'] == '') {
			$data['duedate'] = null;
		}
		if (isset($data['dateadded']) && $data['dateadded'] == '') {
			$data['dateadded'] = null;
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
				$uploadDir = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/pdf_attachments/' . $id . '/';

				// Create directory if it doesn't exist
				if (!file_exists($uploadDir)) {
					mkdir($uploadDir, 0755, true);
				}

				$allowedExtensions = ['dwg', 'xref'];
				$fileExtension = pathinfo($_FILES['pdf_attachment']['name'], PATHINFO_EXTENSION);
				$originalFileName = basename($_FILES['pdf_attachment']['name']);

				// Validate file type

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
			}
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'dmg_items', $data);
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
			$this->db->update(db_prefix() . 'dmg_items', ['custom_field' => $data_field]);
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
		$data = $this->db->get(db_prefix() . 'dmg_items')->row();
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

	public function upload_file($id, $type, $version = '1.0.0')
	{
		$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/' . $type . '/' . $id . '/';
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
				$tmpFilePath = $_FILES['file']['tmp_name'][$i];

				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					if (
						_perfex_upload_error($_FILES['file']['error'][$i])
						|| !_upload_extension_allowed($_FILES['file']['name'][$i])
					) {
						continue;
					}

					// Check if file is a ZIP
					if (strtolower(pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION)) === 'zip') {
						$zipFolderName = pathinfo($_FILES['file']['name'][$i], PATHINFO_FILENAME);
						$totalUploaded += $this->handle_zip_upload($tmpFilePath, $id, $type, $version, $zipFolderName);
					} else {
						// Regular file upload
						_maybe_create_upload_path($path);
						$filename = $this->check_duplicate_file_name($id, $_FILES['file']['name'][$i]);
						$newFilePath = $path . $filename;

						if (move_uploaded_file($tmpFilePath, $newFilePath)) {
							$creator_type = 'staff';
							if (is_client_logged_in()) {
								$creator_type = 'customer';
							}
							$this->add_attachment_file_to_database(
								$filename,
								$id,
								$version,
								$_FILES['file']['type'][$i],
								'',
								'',
								'',
								$creator_type
							);
							$totalUploaded++;
						}
					}
				}
			}
		}
		return (bool) $totalUploaded;
	}

	/**
	 * Handle ZIP file upload and extract contents
	 */
	private function handle_zip_upload($zipPath, $parent_id, $type, $version, $zipFolderName)
	{
		$totalUploaded = 0;
		$basePath = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/' . $type . '/' . $parent_id . '/';

		_maybe_create_upload_path($basePath);

		$zip = new ZipArchive;
		if ($zip->open($zipPath) === TRUE) {
			// Track folders we've created in database (path => folder_id)
			$createdFolders = [];

			for ($i = 0; $i < $zip->numFiles; $i++) {
				$filename = $zip->getNameIndex($i);

				// Skip MAC OSX hidden files
				if (strpos($filename, '__MACOSX/') !== false || strpos($filename, '.DS_Store') !== false) {
					continue;
				}

				// Skip empty directories
				if (substr($filename, -1) === '/' && $zip->getFromIndex($i) === false) {
					continue;
				}

				$fileinfo = pathinfo($filename);
				$isDirectory = substr($filename, -1) === '/';
				$relativePath = trim($filename, '/');
				$pathParts = explode('/', $relativePath);

				// For files not in any folder
				if (count($pathParts) === 1 && !$isDirectory) {
					// Extract file directly
					$fullPath = $basePath . $filename;

					if ($zip->extractTo($basePath, $filename)) {
						$this->add_attachment_file_to_database(
							$filename,
							$parent_id, // Parent is the original parent
							$version,
							mime_content_type($fullPath),
							'',
							'',
							'',
							is_client_logged_in() ? 'customer' : 'staff'
						);
						$totalUploaded++;
					}
					continue;
				}

				// Handle files in folders
				if (!$isDirectory) {
					$currentPath = '';
					$currentParentId = $parent_id;

					// Process each folder in the path (except the filename)
					for ($j = 0; $j < count($pathParts) - 1; $j++) {
						$currentPath .= ($currentPath ? '/' : '') . $pathParts[$j];

						if (!isset($createdFolders[$currentPath])) {
							// Create folder in database
							$newFolderId = $this->add_attachment_file_to_database(
								$pathParts[$j],
								$currentParentId,
								$version,
								'folder',
								'',
								'',
								'',
								is_client_logged_in() ? 'customer' : 'staff'
							);
							$createdFolders[$currentPath] = $newFolderId;
							$totalUploaded++;
						}

						$currentParentId = $createdFolders[$currentPath];
					}

					// Extract the file
					$fullPath = $basePath . $filename;
					$dirPath = dirname($fullPath);

					// Ensure physical directory exists
					if (!is_dir($dirPath)) {
						mkdir($dirPath, 0755, true);
					}

					if ($zip->extractTo($basePath, $filename)) {
						// Add file to database
						$this->add_attachment_file_to_database(
							$pathParts[count($pathParts) - 1], // filename
							$currentParentId, // parent is the deepest folder
							$version,
							mime_content_type($fullPath),
							'',
							'',
							'',
							is_client_logged_in() ? 'customer' : 'staff'
						);
						$totalUploaded++;
					}
				}
			}
			$zip->close();
		}

		return $totalUploaded;
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
		$this->db->insert(db_prefix() . 'dmg_items', $data);
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
			return $this->db->get(db_prefix() . 'dmg_file_versions')->row();
		} else {
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dmg_file_versions')->result_array();
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
			$this->db->delete(db_prefix() . 'dmg_file_versions');
			if ($this->db->affected_rows() > 0) {
				//Delete physiscal file
				$this->delete_file_item(DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $data_log->parent_id . '/' . $data_log->name);
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
		return $this->db->get(db_prefix() . 'dmg_file_versions')->result_array();
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
			$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/';
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
		$this->db->insert(db_prefix() . 'dmg_custom_fields', $data);
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
		$this->db->update(db_prefix() . 'dmg_custom_fields', $data);
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
		$this->db->delete(db_prefix() . 'dmg_custom_fields');
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
			return $this->db->get(db_prefix() . 'dmg_custom_fields')->row();
		} else {
			return $this->db->get(db_prefix() . 'dmg_custom_fields')->result_array();
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
			$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/temps/' . $id;
			dmg_create_folder($path);
			$path = $path . '/' . dmg_get_file_name($id);
			dmg_create_folder($path);
		}
		$data_child = $this->get_item('', 'parent_id = ' . $id, 'id, name, filetype, parent_id');
		if ($data_child) {
			foreach ($data_child as $key => $value) {
				if ($value['filetype'] == 'folder') {
					$new_path = $path . '/' . $value['name'];
					dmg_create_folder($new_path);
					$this->create_folder($value['id'], $new_path);
				} else {
					$path1 = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $value['parent_id'] . '/' . $value['name'];
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
		$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/temps/bulk_downloads/' . $folder_name . '/';
		dmg_create_folder($path);
		$data_child = $this->get_item('', 'id IN (' . $id_lever_1 . ')', 'id, name, filetype, parent_id');
		if ($data_child) {
			foreach ($data_child as $key => $value) {
				if ($value['filetype'] == 'folder') {
					$new_path = $path . '/' . $value['name'];
					dmg_create_folder($new_path);
					$this->create_folder($value['id'], $new_path);
				} else {
					$path1 = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $value['parent_id'] . '/' . $value['name'];
					$path2 = $path . '/' . $value['name'];
					$this->copy_file($path1, $path2);
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
			$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $folder_id . '/';
			_maybe_create_upload_path($path);
			if ($data_item->filetype == 'folder') {
				$data["parent_id"] = $folder_id;
				$data["name"] = $this->check_duplicate_folder_name($folder_id, $data_item->name);
				$insert_id = $this->create_item($data);
				$new_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $insert_id . '/';
				_maybe_create_upload_path($new_path);
				$data_child = $this->get_item('', 'parent_id = ' . $item_id, 'id, name, filetype, parent_id');
				foreach ($data_child as $key => $value) {
					$this->duplicate_item($insert_id, $value['id']);
				}
				$affectedRows++;
			} else {
				$oldFilePath = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/' . $data_item->name;
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
			$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $folder_id . '/';
			_maybe_create_upload_path($path);
			if ($data_item->filetype == 'folder') {
				$data["parent_id"] = $folder_id;
				$data["name"] = $this->check_duplicate_folder_name($folder_id, $data_item->name);
				$insert_id = $this->create_item($data);
				$new_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $insert_id . '/';
				_maybe_create_upload_path($new_path);
				$data_child = $this->get_item('', 'parent_id = ' . $item_id, 'id, name, filetype, parent_id');
				foreach ($data_child as $key => $value) {
					$this->move_item($insert_id, $value['id']);
				}
				$affectedRows++;
			} else {

				$oldFilePath = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/' . $data_item->name;
				$filename = $this->check_duplicate_file_name($folder_id, $data_item->name);
				$newFilePath = $path . $filename;
				// Upload the file into the temp dir
				if ($this->copy_file($oldFilePath, $newFilePath)) {
					$log_text = _l('dmg_moved_file_from') . ' ' . dmg_get_file_name($data_item->parent_id) . ' ' . _l('dmg_to') . ' ' . dmg_get_file_name($folder_id);
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
		$this->db->insert(db_prefix() . 'dmg_audit_logs', $data);
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
			return $this->db->get(db_prefix() . 'dmg_audit_logs')->row();
		} else {
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dmg_audit_logs')->result_array();
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
		$this->db->update(db_prefix() . 'dmg_audit_logs', ['item_id' => $new_item_id]);
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
		$this->db->update(db_prefix() . 'dmg_file_versions', ['parent_id' => $new_item_id]);
		if ($this->db->affected_rows() > 0) {
			// Move previous file to log folder
			$old_log_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $old_item_id . '/';
			$new_log_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $new_item_id . '/';
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
			$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $parent_id . '/';
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
								$log_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $id . '/';
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
		$this->db->update(db_prefix() . 'dmg_items', $data);
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
		$this->db->insert(db_prefix() . 'dmg_file_versions', $data);
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
				$this->db->update(db_prefix() . 'dmg_items', $data);
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
					$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/';
					$log_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/log_versions/' . $id . '/';
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
		$this->db->insert(db_prefix() . 'dmg_remiders', $data);
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
		$this->db->update(db_prefix() . 'dmg_remiders', $data);
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
			return $this->db->get(db_prefix() . 'dmg_remiders')->row();
		} else {
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dmg_remiders')->result_array();
		}
	}

	public function get_file_reminder($file_id)
	{
		$this->db->where('file_id', $file_id);
		return $this->db->get(db_prefix() . 'dmg_remiders')->result_array();
	}

	/**
	 * delete remider
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_remider($id)
	{
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'dmg_remiders');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * auto remider
	 * @return [type] 
	 */
	public function auto_remider()
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
			$data_send_mail->link = '<a href="' . admin_url('document_management?id=' . $file_id) . '">' . dmg_get_file_name($file_id) . '</a>';
			$data_send_mail->message = $message;
			$template = mail_template('reminder', 'document_management', $data_send_mail);
			$template->send();
		}
	}

	/**
	 * add share document
	 * @param array $data 
	 */
	public function add_share_document($data)
	{
		if (isset($data['date']) && $data['date'] != '') {
			$data['date'] = dmg_format_date_time($data['date']);
		}
		if (!isset($data['expiration'])) {
			$data['expiration'] = 0;
		}
		if (isset($data['staff']) && $data['staff'] != '') {
			$data['staff'] = implode(',', $data['staff']);
		}
		if (isset($data['customer']) && $data['customer'] != '') {
			$data['customer'] = implode(',', $data['customer']);
		}
		if (isset($data['customer_group']) && $data['customer_group'] != '') {
			$data['customer_group'] = implode(',', $data['customer_group']);
		}
		$this->db->insert(db_prefix() . 'dmg_share_logs', $data);
		return $this->db->insert_id();
	}

	/**
	 * update share document
	 * @param array $data 
	 */
	public function update_share_document($data)
	{
		if (isset($data['date']) && $data['date'] != '') {
			$data['date'] = dmg_format_date_time($data['date']);
		}
		if (!isset($data['expiration'])) {
			$data['expiration'] = 0;
		}
		if (isset($data['staff']) && $data['staff'] != '') {
			$data['staff'] = implode(',', $data['staff']);
		}
		if (isset($data['customer']) && $data['customer'] != '') {
			$data['customer'] = implode(',', $data['customer']);
		}
		if (isset($data['customer_group']) && $data['customer_group'] != '') {
			$data['customer_group'] = implode(',', $data['customer_group']);
		}
		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'dmg_share_logs', $data);
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
			return $this->db->get(db_prefix() . 'dmg_share_logs')->row();
		} else {
			if ($where != '') {
				$this->db->where($where);
			}
			return $this->db->get(db_prefix() . 'dmg_share_logs')->result_array();
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
		$this->db->delete(db_prefix() . 'dmg_share_logs');
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
			$data = $this->db->query('select distinct(item_id) as id from ' . db_prefix() . 'dmg_share_logs where share_to = "staff" AND ((find_in_set(' . $userid . ', staff) AND expiration = 1 AND expiration_date > \'' . $current_date . '\') OR (find_in_set(' . $userid . ', staff) AND expiration = 0))')->result_array();
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
			$data = $this->db->query('select distinct(item_id) as id from ' . db_prefix() . 'dmg_share_logs where' . $customer_query . $groups_query)->result_array();
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
		$share_to_me = $this->document_management_model->get_item_share_to_me(false, $type);
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
	public function get_permission_item_share_to_me($item_id, $type = 'staff')
	{
		$current_date = date('Y-m-d H:i:s');
		$list = [];
		if ($type == 'staff') {
			$userid = get_staff_user_id();
			$data = $this->db->query('select permission from ' . db_prefix() . 'dmg_share_logs where item_id = ' . $item_id . ' AND share_to = "staff" AND ((find_in_set(' . $userid . ', staff) AND expiration = 1 AND expiration_date > \'' . $current_date . '\') OR (find_in_set(' . $userid . ', staff) AND expiration = 0))')->result_array();
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
			$data = $this->db->query('select permission from ' . db_prefix() . 'dmg_share_logs where item_id = ' . $item_id . ' AND (' . $customer_query . $groups_query . ')')->result_array();
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
			$this->db->delete(db_prefix() . 'dmg_approval_setting');
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
		$this->db->insert(db_prefix() . 'dmg_approval_setting', $data);
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
		$this->db->update(db_prefix() . 'dmg_approval_setting', $data);
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
			return $this->db->get(db_prefix() . 'dmg_approval_setting')->row();
		} else {
			return $this->db->get(db_prefix() . 'dmg_approval_setting')->result_array();
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
		$approval_setting = $this->db->get(db_prefix() . 'dmg_approval_setting')->row();
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
			$this->db->insert(db_prefix() . 'dmg_approval_details', $row);
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
		$this->db->delete(db_prefix() . 'dmg_approval_details');
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
		return $this->db->get(db_prefix() . 'dmg_items')->row();
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
			return $this->db->get(db_prefix() . 'dmg_approval_details')->result_array();
		} else {
			return $this->db->get(db_prefix() . 'dmg_approval_details')->result_array();
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
		$this->db->update(db_prefix() . 'dmg_approval_details', $data);
		if ($this->db->affected_rows() > 0) {
			// If has rejected then change status to finish approve
			if ($data['approve'] == 2) {
				$this->db->where('id', $data['rel_id']);
				$this->db->update(db_prefix() . 'dmg_items', ['approve' => 2]);
				return true;
			}

			$count_approve_total = $this->count_approve($data['rel_id'], $data['rel_type'])->count;
			$count_approve = $this->count_approve($data['rel_id'], $data['rel_type'], 1)->count;
			$count_rejected = $this->count_approve($data['rel_id'], $data['rel_type'], 2)->count;

			if (($count_approve + $count_rejected) == $count_approve_total) {
				if ($count_approve_total == $count_approve) {
					$this->db->where('id', $data['rel_id']);
					$this->db->update(db_prefix() . 'dmg_items', ['approve' => 1]);

					// Move items
					$data_item = $this->get_item($data['rel_id']);
					if ($data_item && $data_item->move_after_approval == 1 && is_numeric($data_item->folder_after_approval) && $data_item->folder_after_approval > 0) {
						$this->move_item($data_item->folder_after_approval, $data['rel_id']);
					}
				} else {
					$this->db->where('id', $data['rel_id']);
					$this->db->update(db_prefix() . 'dmg_items', ['approve' => 2]);
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
			return $this->db->query('SELECT count(distinct(staffid)) as count FROM ' . db_prefix() . 'dmg_approval_details where rel_id = ' . $rel_id . ' and rel_type = \'' . $rel_type . '\'')->row();
		} else {
			return $this->db->query('SELECT count(distinct(staffid)) as count FROM ' . db_prefix() . 'dmg_approval_details where rel_id = ' . $rel_id . ' and rel_type = \'' . $rel_type . '\' and approve = ' . $approve . '')->row();
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
			$this->db->insert(db_prefix() . 'dmg_approval_detail_eids', $row);
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
			return $this->db->get(db_prefix() . 'dmg_approval_detail_eids')->result_array();
		} else {
			return $this->db->get(db_prefix() . 'dmg_approval_detail_eids')->result_array();
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
		$this->db->update(db_prefix() . 'dmg_approval_detail_eids', $data);
		if ($this->db->affected_rows() > 0) {
			$this->db->where('id', $id);
			$signer_data = $this->db->get(db_prefix() . 'dmg_approval_detail_eids')->row();
			if ($signer_data) {
				$count_approve_total = $this->count_approve_eids($data['rel_id'], $data['rel_type'])->count;
				$count_approve = $this->count_approve_eids($data['rel_id'], $data['rel_type'], 1)->count;
				$count_rejected = $this->count_approve_eids($data['rel_id'], $data['rel_type'], 2)->count;
				if (($count_approve + $count_rejected) == $count_approve_total) {
					if ($count_approve_total == $count_approve) {
						$this->db->where('id', $data['rel_id']);
						$this->db->update(db_prefix() . 'dmg_items', ['sign_approve' => 1]);
						// Move items
						$data_item = $this->get_item($data['rel_id']);
						if ($data_item && $data_item->move_after_approval == 1 && is_numeric($data_item->folder_after_approval) && $data_item->folder_after_approval > 0) {
							$this->move_item($data_item->folder_after_approval, $data['rel_id']);
						}
					} else {
						$this->db->where('id', $data['rel_id']);
						$this->db->update(db_prefix() . 'dmg_items', ['sign_approve' => 2]);
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
			return $this->db->query('SELECT count(distinct(staffid)) as count FROM ' . db_prefix() . 'dmg_approval_detail_eids where rel_id = ' . $rel_id . ' and rel_type = \'' . $rel_type . '\'')->row();
		} else {
			return $this->db->query('SELECT count(distinct(staffid)) as count FROM ' . db_prefix() . 'dmg_approval_detail_eids where rel_id = ' . $rel_id . ' and rel_type = \'' . $rel_type . '\' and approve = ' . $approve . '')->row();
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
		$this->db->update(db_prefix() . 'dmg_remiders', ['file_id' => $new_item_id]);
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
		$this->db->update(db_prefix() . 'dmg_share_logs', ['item_id' => $new_item_id]);
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
		$this->db->update(db_prefix() . 'dmg_approval_details', ['rel_id' => $new_item_id]);
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
		$this->db->update(db_prefix() . 'dmg_approval_detail_eids', ['rel_id' => $new_item_id]);
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
		require_once(module_dir_path(DOCUMENT_MANAGEMENT_MODULE_NAME) . '/third_party/vendor/autoload.php');
		$phpWord = new PhpWord();
		$section = $phpWord->addSection();
		Html::addHtml($section, $html);
		$phpWord->save($path, 'Word2007');
	}

	public function convert_html_file_to_word_api($from_path, $to_path)
	{
		require_once(module_dir_path(DOCUMENT_MANAGEMENT_MODULE_NAME) . '/third_party/convertio/autoload.php');
		$API = new Convertio("9cebcf2b7088b5c95a637a07cf395936");
		$API->settings(array('api_protocol' => 'http', 'http_timeout' => 10));
		$API->start($from_path, 'docx')->wait()->download($to_path)->delete();
	}

	public function searchFilesAndFolders($query, $folder_id = null)
	{
		// 1. Build the set of folder IDs to search under (recursive)
		$searchable_folder_ids = [];
		if ($folder_id) {
			// Include the current folder + all descendants
			$searchable_folder_ids = array_merge([$folder_id], $this->getAllDescendantIds($folder_id));
		} else {
			// If no folder_id is passed, we treat “root” as NULL
			$searchable_folder_ids = [null];
		}

		// ===== 2. SEARCH FOR FILES (in current folder + subfolders) =====
		// NOTE: We explicitly exclude folders here so that Step 2 (folder search) does not duplicate them.
		$this->db->group_start();
		$this->db->where_in('parent_id', $searchable_folder_ids);
		if (!$folder_id) {
			$this->db->or_where('parent_id IS NULL'); // Include root items
		}
		$this->db->group_end();

		// <---- Add this line to filter out any folder-type records -->
		$this->db->where('filetype !=', 'folder');

		$this->db->like('name', $query);
		$fileResults = $this->db->get(db_prefix() . 'dmg_items')->result();

		// ===== 3. SEARCH FOR FOLDERS (only direct children of $folder_id) =====
		$this->db->where('filetype', 'folder');
		$this->db->where('parent_id', $folder_id);
		$this->db->like('name', $query);
		$folderResults = $this->db->get(db_prefix() . 'dmg_items')->result();

		// ===== 4. TRACK ALL FOLDER IDs THAT ARE PARENTS OF FILES =====
		$allFolderIdsInResults = [];
		foreach ($fileResults as $file) {
			if ($file->parent_id && ! in_array($file->parent_id, $allFolderIdsInResults)) {
				$allFolderIdsInResults[] = $file->parent_id;
			}
		}

		// ===== 5. FILTER OUT ANY FOLDERS THAT ALSO ARE PARENTS OF FILES =====
		$uniqueFolderResults = [];
		foreach ($folderResults as $folder) {
			if (! in_array($folder->id, $allFolderIdsInResults)) {
				$uniqueFolderResults[] = $folder;
				$allFolderIdsInResults[] = $folder->id; // Mark as “already included”
			}
		}

		// ===== 6. MERGE BOTH SETS =====
		$merged = array_merge($uniqueFolderResults, $fileResults);

		// ===== 7. OPTIONAL DEDUPLICATION BY ID =====
		//    In case any record still got in twice, we make sure each ID appears only once.
		$deduped  = [];
		$seen_ids = [];
		foreach ($merged as $item) {
			if (! in_array($item->id, $seen_ids, true)) {
				$seen_ids[]  = $item->id;
				$deduped[]   = $item;
			}
		}

		// ===== 8. ADD BREADCRUMBS TO EVERY RESULT =====
		//    (exactly the same as before)
		$rootFolder = $this->db
			->where('filetype', 'folder')
			->where('parent_id', null)
			->get(db_prefix() . 'dmg_items')
			->row();

		foreach ($deduped as $key => $item) {
			$breadcrumbs = [];
			if ($rootFolder) {
				$breadcrumbs[] = $rootFolder->name;
			}
			$breadcrumbs = array_merge($breadcrumbs, $this->getLimitedBreadcrumb($item->parent_id));
			$deduped[$key]->breadcrumb = $breadcrumbs;
		}

		return $deduped;
	}


	/**
	 * Recursively build up to $max_levels parents for a breadcrumb trail.
	 */
	private function getLimitedBreadcrumb($parent_id, $max_levels = 2)
	{
		$breadcrumb       = [];
		$level            = 0;
		$fetched_parents  = [];

		while ($parent_id !== null && $level < $max_levels) {
			if (isset($fetched_parents[$parent_id])) {
				$parent = $fetched_parents[$parent_id];
			} else {
				$parent = $this->db
					->where('id', $parent_id)
					->get(db_prefix() . 'dmg_items')
					->row();
				if (! $parent) {
					break;
				}
				$fetched_parents[$parent_id] = $parent;
			}

			array_unshift($breadcrumb, $parent->name);
			$parent_id = $parent->parent_id;
			$level++;
		}

		return $breadcrumb;
	}


	/**
	 * Return an array of every descendant folder‐ID under $root_id.
	 */
	private function getAllDescendantIds($root_id)
	{
		$all_ids = [];
		$queue   = [$root_id];

		while (! empty($queue)) {
			$this->db->reset_query();
			$this->db->select('id');
			$this->db->from(db_prefix() . 'dmg_items');
			$this->db->where_in('parent_id', $queue);
			$this->db->where('filetype', 'folder');
			$query    = $this->db->get();
			$children = $query->result();

			$queue    = [];
			foreach ($children as $child) {
				$all_ids[] = $child->id;
				$queue[]   = $child->id;
			}
		}

		return $all_ids;
	}

	public function delete_pdf_attachment($id)
	{

		$get_pdf_attachment = $this->get_item($id);
		if ($get_pdf_attachment->pdf_attachment != '') {

			$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/pdf_attachments/' . $id . '/' . $get_pdf_attachment->pdf_attachment;

			if (file_exists($path)) {

				unlink($path);
			}

			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'dmg_items', ['pdf_attachment' => '']);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
		}
	}

	public function check_project_member_exist($project_id)
	{
		$this->db->where('project_id', $project_id);
		$this->db->where('staff_id', get_staff_user_id());
		return $this->db->get(db_prefix() . 'project_members')->row();
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
		return $this->db->get(db_prefix() . 'dmg_items')->result_array();

	}

	public function get_default_dmg_project($project_id)
	{
		$master_id = 2;
		$this->db->select('id');
		$this->db->where('project_id', $project_id);
		$data = $this->db->get(db_prefix() . 'dmg_items')->row();
		if(!empty($data)) {
			$master_id = $data->id;
		}
		return $master_id;
	}


}
