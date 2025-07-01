<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;

/**
 * document_management
 */
class document_management extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('document_management_model');
		$this->load->model('departments_model');
		hooks()->do_action('document_management_init');
	}

	/* index */
	public function index()
	{
		$default_project = get_default_project();
		if (!(has_permission('document_management_file_management', '', 'view') || has_permission('document_management_file_management', '', 'view_own'))) {
			access_denied('document_management');
		}
		$data['title']                 = _l('dmg_file_management');
		init_fist_item();
		$user_id = get_staff_user_id();
		$master_parent_id = '';
		$id = $this->input->get('id') ?? $this->document_management_model->get_default_dmg_project($default_project);
		$edit = $this->input->get('edit');
		$share_to_me = $this->input->get('share_to_me');
		$my_approval = $this->input->get('my_approval');
		$electronic_signing = $this->input->get('electronic_signing');
		$data['edit'] = ($edit == null ? 0 : $edit);
		$data['share_to_me'] = ($share_to_me == null ? 0 : $share_to_me);
		$data['my_approval'] = ($my_approval == null ? 0 : $my_approval);
		$data['electronic_signing'] = ($electronic_signing == null ? 0 : $electronic_signing);
		$query = 'approve = -1 and id in (SELECT rel_id FROM ' . db_prefix() . 'dmg_approval_details where staffid = ' . $user_id . ' and approve is null and rel_type = \'document\')';
		$data['approve_items'] = $this->document_management_model->get_item('', $query, 'name, id, dateadded, filetype, hash');
		$query = 'sign_approve = -1 and id in (SELECT rel_id FROM ' . db_prefix() . 'dmg_approval_detail_eids where staffid = ' . $user_id . ' and approve is null and rel_type = \'document\')';
		$data['approve_item_eids'] = $this->document_management_model->get_item('', $query, 'name, id, dateadded, filetype, hash');
		$data_root_folder = $this->document_management_model->get_root_item($user_id, $default_project);

		// Added project member check similar to the reference code
		if (!empty($data_root_folder)) {
			foreach ($data_root_folder as $key => $value) {
				if ($value['project_id'] != 0 && !is_admin()) {
					$project_member = $this->document_management_model->check_project_member_exist($value['project_id']);
					if (empty($project_member)) {
						unset($data_root_folder[$key]);
					}
				}
			}
			$data_root_folder = array_values($data_root_folder);
		}

		if ($id == null) {
			$id = '';
			foreach ($data_root_folder as $key => $value) {
				if ($id == '') {
					if ($key == 0) {
						$id = $value['id'];
						$master_parent_id = $id;
					}
				}
			}
		} else {
			$master_parent_id = $this->document_management_model->get_master_id($id);
		}
		
		$data['root_folder'] = $data_root_folder;
		$file_locked = false;

		$data['parent_id'] = $id;
		$data['master_parent_id'] = $master_parent_id;
		if (is_numeric($id) && $id > 0) {
			$file_locked = check_file_locked($id);
			if ($data['edit'] == 1 && $file_locked) {
				access_denied('document_management');
			}
			$data['item'] = $this->document_management_model->get_item($id);
			if ($data['item'] == null) {
				redirect(admin_url('document_management'));
			}
		}
		$data['share_id'] = $this->document_management_model->get_item_share_to_me(true);
		$data['file_locked'] = $file_locked;
		$data['staffs'] = $this->staff_model->get();
		$this->load->model('clients_model');
		$data['customers'] = $this->clients_model->get();
		$this->load->model('client_groups_model');
		$data['customer_groups'] = $this->client_groups_model->get_groups();

		$this->load->view('file_managements/file_management.php', $data);
	}

	/**
	 * create new section
	 */
	public function create_new_section()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$parent_id = '';
			if (isset($data['default_parent_id'])) {
				$parent_id = $data['default_parent_id'];
				unset($data['default_parent_id']);
			}
			$check_result = $this->document_management_model->check_duplicate_name($data['parent_id'], $data['name'], $data['id'], 'folder', false, get_staff_user_id());
			if ($check_result) {
				if ($data['parent_id'] == '0') {
					// Section
					set_alert('warning', _l('dmg_section_name_has_duplicated'));
				} else {
					//Folder
					set_alert('warning', _l('dmg_folder_name_has_duplicated'));
				}
			} else {
				if ($data['id'] == '') {
					$res = $this->document_management_model->create_item($data);
					if ($res) {
						set_alert('success', _l('dmg_created_successfully'));
						// Section
						if ($data['parent_id'] == '0') {
							redirect(admin_url('document_management?id=' . $res));
						} else {
							//Folder
							redirect(admin_url('document_management?id=' . $data['parent_id']));
						}
					} else {
						set_alert('danger', _l('dmg_create_failure'));
					}
				} else {
					$id = $data['id'];
					$res = $this->document_management_model->update_item($data);
					if ($res) {
						set_alert('success', _l('dmg_updated_successfully'));
					} else {
						set_alert('danger', _l('dmg_update_failure'));
					}
					redirect(admin_url('document_management?id=' . $parent_id));
				}
			}
		}
		redirect(admin_url('document_management'));
	}

	/**
	 * delete section
	 * @param  integer $id 
	 */
	public function delete_section($id, $parent_id = '')
	{
		$result = false;
		if ($id != '') {
			$result =  $this->document_management_model->delete_item($id);
			if ($result) {
				set_alert('success', _l('dmg_deleted_successfully'));
			} else {
				set_alert('danger', _l('dmg_deleted_fail'));
			}
		}
		echo html_entity_decode($result);
	}

	/**
	 * upload file
	 */
	public function upload_file($id, $redirect_type = '')
	{
		$result =  $this->document_management_model->upload_file($id, 'files');
		if ($result) {
			set_alert('success', _l('dmg_uploaded_successfully'));
		} else {
			set_alert('danger', _l('dmg_upload_failed'));
		}
		if ($redirect_type == 'share_to_me') {
			redirect(admin_url('document_management?share_to_me=1&id=' . $id));
		} else {
			redirect(admin_url('document_management?id=' . $id));
		}
	}

	/**
	 * edit
	 * @param  integer $id 
	 */
	public function settings()
	{
		if (!is_admin()) {
			access_denied('document_management');
		}
		$data['title']                 = _l('dmg_settings');
		$data['tab'] = $this->input->get('tab');
		if ($data['tab'] == null) {
			$data['tab'] = 'custom_field';
		}
		if ($data['tab'] == 'approval_setting') {
			$this->load->model('staff_model');
			$data['staffs'] = $this->staff_model->get();
		}
		$this->load->view('settings/manage_setting.php', $data);
	}

	/**
	 * edit file
	 * @return [type] 
	 */
	public function edit_file()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$share_to_me = $this->document_management_model->check_permission_share_to_me($data['id']);

			if (!(has_permission('document_management_file_management', '', 'edit') || $share_to_me)) {
				access_denied('document_management');
			}
			$parent_id = '';
			if (isset($data['all_custom_field'])) {
				unset($data['all_custom_field']);
			}
			if (isset($data['all_file'])) {
				unset($data['all_file']);
			}
			if (isset($data['field_id'])) {
				unset($data['field_id']);
			}
			if (isset($data['default_parent_id'])) {
				$parent_id = $data['default_parent_id'];
				unset($data['default_parent_id']);
			}
			if ($data['id'] != '') {
				$redirect_type = '';
				if (isset($data['redirect_type'])) {
					$redirect_type = $data['redirect_type'];
					unset($data['redirect_type']);
				}
				$custom_field = '';
				$related_file = '';
				if (isset($data['related_file'])) {
					$related_file = implode(',', $data['related_file']);
				}
				$data['custom_field'] = $custom_field;
				$data['related_file'] = $related_file;
				$res = $this->document_management_model->update_item($data);
				if ($res) {
					set_alert('success', _l('dmg_updated_successfully'));
				} else {
					set_alert('danger', _l('dmg_update_failure'));
				}
				if ($redirect_type == 'share_to_me') {
					redirect(admin_url('document_management?share_to_me=1&id=' . $parent_id));
				} else {
					redirect(admin_url('document_management?id=' . $parent_id));
				}
			}
		}
		redirect(admin_url('document_management'));
	}


	/**
	 * custom_field_table
	 * @return json 
	 */
	public function custom_field_table()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				$select = [
					'id',
					'id',
					'id'
				];
				$where        = [];
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'dmg_custom_fields';
				$join         = [];
				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'id',
					'title',
					'type',
					db_prefix() . 'dmg_custom_fields.option',
					'required',
					'default_value'
				]);


				$output  = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];
					$_data = '';
					$name_s = '<a href="javascript:void(0)" onclick="edit(' . $aRow['id'] . ');">' . $aRow['title'] . '</a>';
					$_data .= '<div class="row-options">';
					$_data .= '<a href="javascript:void(0)" onclick="edit(' . $aRow['id'] . '); return false;" class="text-danger">' . _l('dmg_edit') . '</a>';
					$_data .= ' | <a href="' . admin_url('document_management/delete_custom_field/' . $aRow['id']) . '" data-id="' . $aRow['id'] . '" class="text-danger _swaldelete">' . _l('dmg_delete') . '</a>';
					$_data .= '</div>';

					$row[] = $name_s . $_data;
					$row[] = _l('dmg_' . $aRow['type']);
					$option_list = '';
					if ($aRow['option'] != '' && $aRow['option'] != null) {
						$decode_option = json_decode($aRow['option']);
						if (is_array($decode_option)) {
							foreach ($decode_option as $option) {
								$option_list .= '<span class="label label-success mright5">' . $option . '</span>';
							}
						}
					}
					$row[] = $option_list;

					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * add custom field 
	 */
	public function add_custom_field()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if ($data['id'] == '') {
				unset($data['id']);
				$result =  $this->document_management_model->add_custom_field($data);
				if (is_numeric($result)) {
					set_alert('success', _l('dmg_added_successfully', _l('dmg_custom_field')));
				} else {
					set_alert('danger', _l('dmg_added_fail', _l('dmg_custom_field')));
				}
			} else {
				$result =  $this->document_management_model->update_custom_field($data);
				if ($result) {
					set_alert('success', _l('dmg_updated_successfully', _l('dmg_custom_field')));
				} else {
					set_alert('danger', _l('dmg_no_data_changes', _l('dmg_custom_field')));
				}
			}
		}
		redirect(admin_url('document_management/settings?tab=custom_field'));
	}

	/**
	 * delete custom field
	 * @param  integer $fieldset_id 
	 * @param  integer $id          
	 */
	public function delete_custom_field($id)
	{
		$result = false;
		if ($id != '') {
			$result =  $this->document_management_model->delete_custom_field($id);
			if ($result) {
				set_alert('success', _l('dmg_deleted_successfully'));
			} else {
				set_alert('danger', _l('dmg_deleted_fail'));
			}
		}
		echo html_entity_decode($result);
	}

	/**
	 * get custom field data
	 * @param  integer $id 
	 * @return integer     
	 */
	public function get_custom_field_data($id)
	{
		$data = $this->document_management_model->get_custom_fields($id);
		echo json_encode($data);
		die;
	}

	/**
	 * get custom field
	 * @param  integer $id 
	 * @return integer     
	 */
	public function get_custom_field($id = '')
	{
		if ($id == '') {
			echo json_encode('');
			die;
		}
		$required = 1;
		$html = '';
		$customfield = $this->document_management_model->get_custom_fields($id);
		if ($customfield) {
			switch ($customfield->type) {
				case 'select':
					$data['option'] = $customfield->option;
					$data['title'] = $customfield->title;
					$data['id'] = $customfield->id;
					$data['required'] = $required;
					$data['select'] = '';
					$html .= $this->load->view('includes/controls/select', $data, true);
					break;
				case 'multi_select':
					$data['option'] = $customfield->option;
					$data['title'] = $customfield->title;
					$data['id'] = $customfield->id;
					$data['required'] = $required;
					$data['select'] = '';
					$html .= $this->load->view('includes/controls/multi_select', $data, true);
					break;
				case 'checkbox':
					$data['option'] = $customfield->option;
					$data['title'] = $customfield->title;
					$data['id'] = $customfield->id;
					$data['required'] = $required;
					$data['select'] = '';
					$html .= $this->load->view('includes/controls/checkbox', $data, true);
					break;
				case 'radio_button':
					$data['option'] = $customfield->option;
					$data['title'] = $customfield->title;
					$data['id'] = $customfield->id;
					$data['required'] = $required;
					$data['select'] = '';
					$html .= $this->load->view('includes/controls/radio_button', $data, true);
					break;
				case 'textarea':
					$data['id'] = $customfield->id;
					$data['title'] = $customfield->title;
					$data['required'] = $required;
					$data['value'] = '';
					$html .= $this->load->view('includes/controls/textarea', $data, true);
					break;
				case 'numberfield':
					$data['id'] = $customfield->id;
					$data['title'] = $customfield->title;
					$data['required'] = $required;
					$data['value'] = '';
					$html .= $this->load->view('includes/controls/numberfield', $data, true);
					break;
				case 'textfield':
					$data['id'] = $customfield->id;
					$data['title'] = $customfield->title;
					$data['required'] = $required;
					$data['value'] = '';
					$html .= $this->load->view('includes/controls/textfield', $data, true);
					break;
			}
		}
		echo json_encode($html);
		die;
	}

	/* preview */
	public function preview()
	{
		if (!(has_permission('document_management_file_management', '', 'view') || has_permission('document_management_file_management', '', 'view_own'))) {
			access_denied('document_management');
		}
		$data['title']                 = _l('dmg_file_management');
		$master_parent_id = '';
		$id = $this->input->get('id');
		$data['file'] = $this->document_management_model->get_item($id);
		$this->load->view('file_managements/preview_file.php', $data);
	}

	/**
	 * download folder
	 * @param  integer $id 
	 */
	public function download_folder($id)
	{
		$data_item = $this->document_management_model->get_item($id, '', 'name, filetype');
		if ($data_item && $data_item->filetype == 'folder') {
			// Delete folder with old file
			$delete_old_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/temps/' . $id;
			if (file_exists($delete_old_path)) {
				delete_files($delete_old_path, true);
			}
			// Create folder and download
			$root_folder_name = $data_item->name;
			$this->document_management_model->create_folder($id);
			$this->load->library('zip');
			$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/temps/' . $id . '/' . $root_folder_name . '/';
			$this->zip->read_dir($path, false);
			$this->zip->download($root_folder_name . '.zip');
			$this->zip->clear_data();
		}
	}

	public function bulk_delete_item()
	{
		$id = $this->input->get('id');
		$success = false;
		if ($id != '') {
			$affectedRows = 0;
			$id_list = explode(',', $id);
			foreach ($id_list as $key => $_id) {
				if ($_id) {
					$result =  $this->document_management_model->delete_item($_id);
					if ($result) {
						$affectedRows++;
					}
				}
			}
			if ($affectedRows > 0) {
				$success = true;
			}
		}

		if ($success) {
			set_alert('success', _l('dmg_deleted_successfully'));
		} else {
			set_alert('danger', _l('dmg_deleted_fail'));
		}
		echo html_entity_decode($success);
		die;
	}

	public function bulk_download_item()
	{
		$parent_id = $this->input->get('parent_id');
		$id = $this->input->get('id');
		$success = false;
		if ($id != '') {
			$root_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/temps/bulk_downloads/';


			$current_timest = strtotime(date('Y-m-d H:i:s'));


			// Create folder and download
			$folder_name = 'Document-Management-' . $current_timest;
			$save_path = $root_path . $folder_name . '/';
			$this->document_management_model->create_folder_bulk_download($id, $folder_name);
			$this->load->library('zip');
			$this->zip->read_dir($save_path, false);
			$this->zip->download($folder_name . '.zip');
			$this->zip->clear_data();
		}
	}

	/**
	 * get folder list
	 * @return string 
	 */
	public function get_folder_list()
	{
		$parent = $this->input->get('parent');
		$selected_folder = $this->input->get('selected_folder');
		$selected_item = $this->input->get('selected_item');

		$data['main_tree'] = 1;
		$data['parent_id'] = $parent;
		$data['selected_folder'] = $selected_folder;
		$data['selected_item'] = explode(',', $selected_item);
		$data['creator_type'] = 'staff';
		$data['user_id'] = get_staff_user_id();
		$html = '<div class="row"><div class="col-md-12 overflow-x-auto"><div class="filetree">';
		$html .= $this->load->view('includes/modal_contents/item_list.php', $data, true);
		$html .= '</div></div></div>';
		echo html_entity_decode($html);
		die;
	}

	/**
	 * bulk duplicate item
	 * @return string 
	 */
	public function bulk_duplicate_item()
	{
		$selected_folder = $this->input->get('selected_folder');
		$selected_item = $this->input->get('selected_item');
		$selected_array = explode(',', $selected_item);
		$success = false;
		$affectedRows = 0;
		foreach ($selected_array as $key => $item_id) {
			$res = $this->document_management_model->duplicate_item($selected_folder, $item_id);
			if ($res) {
				$affectedRows++;
			}
		}
		if ($affectedRows > 0) {
			$success = true;
		}
		if ($success) {
			set_alert('success', _l('dmg_successfully_duplicated'));
		} else {
			set_alert('danger', _l('dmg_duplicate_failure'));
		}
		echo html_entity_decode($success);
		die;
	}

	/**
	 * bulk move item
	 * @return string 
	 */
	public function bulk_move_item()
	{
		$selected_folder = $this->input->get('selected_folder');
		$selected_item = $this->input->get('selected_item');
		$selected_array = explode(',', $selected_item);
		$success = false;
		$affectedRows = 0;
		foreach ($selected_array as $key => $item_id) {
			$res = $this->document_management_model->move_item($selected_folder, $item_id);
			if ($res) {
				$affectedRows++;
			}
		}
		if ($affectedRows > 0) {
			$success = true;
		}
		if ($success) {
			set_alert('success', _l('dmg_successfully_moved'));
		} else {
			set_alert('danger', _l('dmg_move_failure'));
		}
		echo html_entity_decode($success);
		die;
	}

	/* preview */
	public function editdocument2()
	{
		$id = $this->input->get('id');
		if (!(has_permission('document_management_file_management', '', 'edit')) && check_file_locked($id)) {
			access_denied('document_management');
		}
		$data_item = $this->document_management_model->get_item($id);
		if ($data_item && is_object($data_item)) {
			$data['file'] = $data_item;
			$data['title'] = $data_item->name;
			$data['id'] = $id;
			$this->load->view('file_managements/edit_file.php', $data);
		} else {
			redirect(admin_url('document_management'));
		}
	}

	/* preview */
	public function editdocument()
	{
		$id = $this->input->get('id');
		if (!(has_permission('document_management_file_management', '', 'edit')) && check_file_locked($id)) {
			access_denied('document_management');
		}
		require_once(module_dir_path(DOCUMENT_MANAGEMENT_MODULE_NAME) . '/third_party/vendor/autoload.php');

		$data_item = $this->document_management_model->get_item($id);
		if ($data_item && is_object($data_item)) {
			$data['file'] = $data_item;
			$data['title'] = $data_item->name;

			$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/' . $data_item->name;
			//Load docx file
			$phpWord = IOFactory::load($path);
			$save_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . "/html_files/" . $id;
			dmg_create_folder($save_path);
			$file_path = $save_path . '/index.html';

			// Convert to html file
			$objWriter = IOFactory::createWriter($phpWord, 'HTML');
			$objWriter->save($file_path);

			$data_html = file_get_contents($file_path);

			$doc = new DOMDocument();
			$doc->loadHTML($data_html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
			$xpath = new DOMXPath($doc);

			foreach ($xpath->evaluate("//head") as $node) {
				$node->parentNode->removeChild($node);
			}

			$output = $doc->saveHTML();

			$data['html'] = $output;
			$data['id'] = $id;
			$this->load->view('file_managements/edit_file.php', $data);
		} else {
			redirect(admin_url('document_management'));
		}
	}

	/**
	 * upload version file
	 * @param  integer $id 
	 */
	public function upload_version_file($id)
	{
		$result =  $this->document_management_model->upload_version_file($id);
		if ($result) {
			set_alert('success', _l('dmg_uploaded_successfully'));
		} else {
			set_alert('danger', _l('dmg_upload_failed'));
		}
		if ($this->input->post('redirect') == 'share_to_me') {
			redirect(admin_url('document_management?share_to_me=1&id=' . $id));
		} else {
			redirect(admin_url('document_management?id=' . $id));
		}
	}

	/**
	 * delete log
	 * @param  integer $id 
	 */
	public function delete_log($id, $parent_id = '')
	{
		$result = false;
		if ($id != '') {
			$result =  $this->document_management_model->delete_log_version($id);
			if ($result) {
				set_alert('success', _l('dmg_deleted_successfully'));
			} else {
				set_alert('danger', _l('dmg_deleted_fail'));
			}
		}
		echo html_entity_decode($result);
	}


	/**
	 * restore item
	 * @return boolean 
	 */
	public function restore_item($id)
	{
		$success = $this->document_management_model->restore_item($id);
		if ($success) {
			set_alert('success', _l('dmg_successfully_restored'));
		} else {
			set_alert('danger', _l('dmg_restore_failed'));
		}
		echo html_entity_decode($success);
		die;
	}

	/**
	 * create remider
	 * @param  integer $file_id 
	 */
	public function create_remider($file_id)
	{
		$redirect = '';
		if ($this->input->post()) {
			$data = $this->input->post();
			if (isset($data['redirect'])) {
				$redirect = $data['redirect'];
				unset($data['redirect']);
			}

			if ($data['id'] == '') {
				$result =  $this->document_management_model->create_remider($data);
				if ($result > 0) {
					set_alert('success', _l('dmg_created_successfully'));
				} else {
					set_alert('danger', _l('dmg_create_failure'));
				}
			} else {
				$result =  $this->document_management_model->update_remider($data);
				if ($result) {
					set_alert('success', _l('dmg_updated_successfully'));
				} else {
					set_alert('danger', _l('dmg_update_failure'));
				}
			}
		}
		if ($redirect == 'share_to_me') {
			redirect(admin_url('document_management?share_to_me=1&id=' . $file_id));
		} else {
			redirect(admin_url('document_management?id=' . $file_id));
		}
	}

	/**
	 * create remider
	 * @param  integer $id 
	 * @param  integer $file_id 
	 */
	public function delete_remider($id, $file_id)
	{
		$result =  $this->document_management_model->delete_remider($id);
		if ($result) {
			set_alert('success', _l('dmg_deleted_successfully'));
		} else {
			set_alert('danger', _l('dmg_deleted_fail'));
		}
		echo html_entity_decode($result);
	}

	/**
	 * save document
	 * @return string 
	 */
	public function save_document()
	{
		$id = $this->input->post('id');
		$data_item = $this->document_management_model->get_item($id);
		if ($data_item && is_object($data_item)) {
			$html = urldecode($this->input->post('html_content', false));
			$html = '<!DOCTYPE html>
    		<html>
    		<head>
    		<meta charset="UTF-8" />
    		<title></title>
    		</head>
    		<body>
    		' . $html . '
    		</body>
    		</html>';

			$path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/' . $data_item->parent_id . '/' . $data_item->name;

			$save_path = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . "/html_files/" . $id;
			dmg_create_folder($save_path);
			$file_path = $save_path . '/index.html';

			$myfile = fopen($file_path, "w") or die("Unable to open file!");
			fwrite($myfile, $html);
			$this->document_management_model->convert_html_file_to_word_api($file_path, $path);
			echo 'true';
			die;
		}
	}

	/**
	 * lock unlock item
	 * @param  integer $id   
	 * @param  string $type 
	 */
	public function lock_unlock_item($id, $type)
	{
		$data['id'] = $id;
		$data['locked'] = ($type == 'lock' ? 1 : 0);
		$data['lock_user'] = get_staff_user_id();
		$success = $this->document_management_model->update_item($data);
		if ($type == 'lock') {
			if ($success) {
				set_alert('success', _l('dmg_locked_successfully'));
			} else {
				set_alert('danger', _l('dmg_lock_failure'));
			}
		}
		if ($type == 'unlock') {
			if ($success) {
				set_alert('success', _l('dmg_unlocked_successfully'));
			} else {
				set_alert('danger', _l('dmg_unlock_failure'));
			}
		}
		echo html_entity_decode($success);
		die;
	}

	/**
	 * share document
	 */
	public function share_document()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$parent_id = $data['parent_id'];
			unset($data['parent_id']);

			$redirect = $data['redirect'];
			unset($data['redirect']);

			if ($data['id'] == '') {
				unset($data['id']);
				$result =  $this->document_management_model->add_share_document($data);
				if (is_numeric($result)) {
					set_alert('success', _l('dmg_shared_successfully'));
				} else {
					set_alert('danger', _l('dmg_share_fail'));
				}
			} else {
				$result =  $this->document_management_model->update_share_document($data);
				if ($result) {
					set_alert('success', _l('dmg_updated_successfully'));
				} else {
					set_alert('danger', _l('dmg_update_failure'));
				}
			}
			if ($redirect == 'share_to_me') {
				redirect(admin_url('document_management?share_to_me=1&id=' . $parent_id));
			} else {
				redirect(admin_url('document_management?id=' . $parent_id));
			}
		}
		redirect(admin_url('document_management?id='));
	}

	/**
	 * create share
	 * @param  integer $id 
	 * @param  integer $file_id 
	 */
	public function delete_share($id, $file_id)
	{
		$result =  $this->document_management_model->delete_share($id);
		if ($result) {
			set_alert('success', _l('dmg_deleted_successfully'));
		} else {
			set_alert('danger', _l('dmg_deleted_fail'));
		}
		echo html_entity_decode($result);
	}

	/**
	 * approve setting table
	 */
	public function approve_setting_table()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$select = [
					'name',
					'related'
				];
				$where        = [];
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'dmg_approval_setting';
				$join         = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'id',
					'name',
					'related'
				]);


				$output  = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];
					$_data = '';
					$_data .= '<div class="row-options">';
					$_data .= '<a href="javascript:void(0)" onclick="edit(' . $aRow['id'] . '); return false;" class="text-danger">' . _l('dmg_edit') . '</a>';
					$_data .= ' | <a href="' . admin_url('document_management/delete_approve_setting/' . $aRow['id'] . '') . '" class="text-danger _swaldelete">' . _l('dmg_delete') . '</a>';
					$_data .= '</div>';

					$row[] = $aRow['name'] . $_data;
					$row[] = _l('dmg_' . $aRow['related']);

					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * delete approve setting
	 * @param  integer $id 
	 * @return integer     
	 */
	public function delete_approve_setting($id)
	{
		$result = false;
		if ($id != '') {
			$result =  $this->document_management_model->delete_approve_setting($id);
			if ($result) {
				set_alert('success', _l('dmg_deleted_successfully', _l('dmg_approval_process')));
			} else {
				set_alert('danger', _l('dmg_deleted_fail', _l('dmg_approval_process')));
			}
		}
		echo html_entity_decode($result);
	}

	/**
	 * approval setting
	 * @param  string $id 
	 * @return redirect
	 */
	public function approver_setting($id = '')
	{
		if ($this->input->post()) {
			$data                = $this->input->post();
			$id = $data['approval_setting_id'];
			unset($data['approval_setting_id']);
			if ($id == '') {
				$id = $this->document_management_model->add_approval_process($data);
				if ($id > 0) {
					set_alert('success', _l('dmg_added_successfully', _l('dmg_approval_process')));
				} else {
					set_alert('danger', _l('dmg_added_fail', _l('dmg_approval_process')));
				}
			} else {
				$success = $this->document_management_model->update_approval_process($id, $data);
				if ($success) {
					set_alert('success', _l('dmg_updated_successfully', _l('dmg_approval_process')));
				} else {
					set_alert('danger', _l('dmg_updated_fail', _l('dmg_approval_process')));
				}
			}
			redirect(admin_url('document_management/settings?tab=approval_setting'));
		}
	}


	/**
	 * get approve setting
	 * @param  integer $id 
	 * @return json     
	 */
	public function get_approve_setting($id)
	{
		$data_setting = $this->document_management_model->get_approval_setting($id);
		$data_setting->notification_recipient = array_map('intval', explode(',', $data_setting->notification_recipient));
		echo json_encode([
			'success' => true,
			'data_setting' => $data_setting
		]);
		die();
	}

	/**
	 * other setting
	 */
	public function other_setting()
	{
		$data = $this->input->post();
		$affected_row = 0;

		if (isset($data['dmg_allows_customers_to_manage_documents'])) {
			$res = update_option('dmg_allows_customers_to_manage_documents', $data['dmg_allows_customers_to_manage_documents']);
			if ($res) {
				$affected_row++;
			}
		} else {
			$res = update_option('dmg_allows_customers_to_manage_documents', 0);
			if ($res) {
				$affected_row++;
			}
		}

		if ($affected_row > 0) {
			set_alert('success', _l('dmg_saved_successfully', _l('dmg_settings')));
		} else {
			set_alert('danger', _l('dmg_save_fail', _l('dmg_settings')));
		}
		redirect(admin_url('document_management/settings?tab=other_setting'));
	}

	/**
	 * create audit request
	 */
	public function send_request_approve($id)
	{
		if ($this->input->post()) {
			$data =  $this->input->post();
			if (isset($data['select_folder'])) {
				$data['folder_after_approval'] = $data['select_folder'];
				unset($data['select_folder']);
			}

			$approve_type = 'normal';
			if (isset($data['approve_type'])) {
				$approve_type = $data['approve_type'];
				unset($data['approve_type']);
			}
			if ($approve_type == 'normal') {
				$data['approve'] = -1;
				$success = $this->document_management_model->update_item($data);
				if ($success) {
					// Approve
					$staff_id = get_staff_user_id();
					$rel_type = 'document';
					$check_proccess = $this->document_management_model->get_approve_setting($rel_type, false);
					$process = '';
					if ($check_proccess) {
						$this->document_management_model->send_request_approve($id, $rel_type, $staff_id);
						set_alert('success', _l('dmg_successful_submission_of_approval_request'));
					} else {
						// Auto checkout if not approve process
						// Change status
						$this->db->where('id', $id);
						$this->db->update(db_prefix() . 'dmg_items', ['approve' => 1]);
						set_alert('success', _l('dmg_approved'));
					}
				} else {
					set_alert('danger', _l('dmg_request_failed'));
				}
			} else {
				$data['sign_approve'] = -1;
				$success = $this->document_management_model->update_item($data);
				if ($success) {
					// Approve
					$staff_id = get_staff_user_id();
					$rel_type = 'document';
					$check_proccess = $this->document_management_model->get_approve_setting($rel_type, false);
					$process = '';
					if ($check_proccess) {
						$this->document_management_model->send_request_approve_eid($id, $rel_type, $staff_id);
						set_alert('success', _l('dmg_successful_submission_of_approval_request'));
					} else {
						// Auto checkout if not approve process
						// Change status
						$this->db->where('id', $id);
						$this->db->update(db_prefix() . 'dmg_items', ['approve' => 1]);
						set_alert('success', _l('dmg_approved'));
					}
				} else {
					set_alert('danger', _l('dmg_request_failed'));
				}
			}
		}
		redirect(admin_url('document_management?id=' . $id));
	}

	/**
	 * detail approve
	 * @param  string $hash 
	 */
	public function detail_approve($hash)
	{
		$data_item = $this->document_management_model->get_item_from_hash($hash);
		if ($data_item) {
			$id = $data_item->id;
			$data['id'] = $id;
			$data['item'] = $data_item;
			$data['title'] = $data_item->name;

			$rel_type = 'document';
			$data['data_approve'] = $this->document_management_model->get_approval_details($id, $rel_type);
			$process = '';
			$check_proccess = $this->document_management_model->get_approve_setting($rel_type, false);
			if ($check_proccess) {
				if ($check_proccess->choose_when_approving == 0) {
					$process = 'not_choose';
				} else {
					$process = 'choose';
				}
			} else {
				$process = 'no_proccess';
			}
			$data['process'] = $process;

			$this->load->view('file_managements/includes/detail_request.php', $data);
		} else {
			redirect(admin_url('document_management'));
		}
	}


	/**
	 * change approve document
	 * @return json 
	 */
	public function change_approve_document()
	{
		$data = $this->input->post();
		$data['date'] = date('Y-m-d');
		$data['staffid'] = get_staff_user_id();
		$success = $this->document_management_model->change_approve_document($data);
		$message = '';
		if ($success == true) {
			if ($data['approve'] == 1) {
				$message = _l('dmg_approved');
			} else {
				$message = _l('dmg_rejected');
			}
		} else {
			$message = _l('dmg_approve_fail');
		}
		echo json_encode([
			'success' => $success,
			'message' => $message,
		]);
		die();
	}

	/**
	 * detail approve
	 * @param  string $hash 
	 */
	public function detail_sign_approve($hash)
	{
		$data_item = $this->document_management_model->get_item_from_hash($hash);
		if ($data_item) {
			$id = $data_item->id;
			$data['id'] = $id;
			$data['item'] = $data_item;
			$data['title'] = $data_item->name;

			$rel_type = 'document';
			$data['data_approve'] = $this->document_management_model->get_approval_detail_eids($id, $rel_type);
			$process = '';
			$check_proccess = $this->document_management_model->get_approve_setting($rel_type, false);
			if ($check_proccess) {
				if ($check_proccess->choose_when_approving == 0) {
					$process = 'not_choose';
				} else {
					$process = 'choose';
				}
			} else {
				$process = 'no_proccess';
			}
			$data['process'] = $process;

			$this->load->view('file_managements/includes/detail_sign_request.php', $data);
		} else {
			redirect(admin_url('document_management'));
		}
	}

	/**
	 * staff sign document
	 */
	public function staff_sign_document()
	{
		if ($this->input->post()) {
			$id = $this->input->post('id');
			$rel_id = $this->input->post('rel_id');
			process_digital_signature_image($this->input->post('signature', false), DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/sign_documents/' . $id);
			$data_update['firstname'] = $this->input->post('firstname');
			$data_update['lastname'] = $this->input->post('lastname');
			$data_update['email'] = $this->input->post('email');
			$data_update['ip_address'] = doc_get_client_ip();
			$data_update['date_of_signing'] = date('Y-m-d H:i:s');
			$data_update['rel_id'] = $rel_id;
			$data_update['rel_type'] = 'document';
			$data_update['approve'] = 1;

			$result = $this->document_management_model->update_signer_info($id, $data_update);
			if ($result) {
				set_alert('success', _l('dmg_signed_successfully'));
			} else {
				set_alert('danger', _l('dmg_sign_failed'));
			}
		}
		$hash =  '';
		$data_item = $this->document_management_model->get_item($rel_id, '', 'hash');
		if ($data_item) {
			redirect(admin_url('document_management/detail_sign_approve/' . $data_item->hash));
		} else {
			redirect(admin_url('document_management'));
		}
	}

	public function get_file_and_folder()
	{
		$query = $this->input->get('query');  // Get the search term from AJAX
		$folder_id = $this->input->get('id');
		$folder_id = is_numeric($folder_id) ? (int)$folder_id : null;

		if (strlen($query) >= 3) {  // Only search if the query is at least 3 characters

			$results = $this->document_management_model->searchFilesAndFolders($query, $folder_id);
			echo json_encode($results);  // Return results as JSON
		} else {
			echo json_encode([]);  // Return an empty array if query is too short
		}
	}

	public function update_order()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$order = $this->input->post('order');
		if (!is_array($order)) {
			echo json_encode(['success' => false, 'message' => 'Invalid order']);
			return;
		}

		foreach ($order as $position => $id) {
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'dmg_items', ['position' => $position]);
		}

		echo json_encode(['success' => true]);
	}

	public function check_files_size()
	{
		$basePath = DOCUMENT_MANAGEMENT_MODULE_UPLOAD_FOLDER . '/files/';

		$minSize = 50 * 1024 * 1024;   // 50 MB
		$maxSize = 100 * 1024 * 1024;  // 100 MB
		$targetFiles = [];
		$totalSizeMB = 0; // Initialize total size counter

		if (!is_dir($basePath)) {
			die("Directory not found: $basePath");
		}

		$dir = new DirectoryIterator($basePath);

		foreach ($dir as $folder) {
			if (!$folder->isDir() || $folder->isDot()) {
				continue;
			}

			$folderName = $folder->getFilename();
			$folderPath = $basePath . $folderName . '/';

			if (!is_numeric($folderName)) {
				continue;
			}

			if (is_dir($folderPath)) {
				$files = new DirectoryIterator($folderPath);

				foreach ($files as $file) {
					if ($file->isFile()) {
						$fileSize = $file->getSize();

						if ($fileSize) {
							$fileSizeMB = round($fileSize / (1024 * 1024), 2);
							$totalSizeMB += $fileSizeMB; // Add to total

							$targetFiles[] = [
								'folder_id' => $folderName,
								'file_name' => $file->getFilename(),
								'file_size' => $fileSizeMB . ' MB',
								'file_size_raw' => $fileSizeMB, // Store numeric value for sorting
								'file_path' => $folderPath . $file->getFilename()
							];
						}
					}
				}
			}
		}

		// Output results
		if (empty($targetFiles)) {
			echo "No files between 50 MB and 100 MB found.";
		} else {
			// Sort by file size (descending)
			usort($targetFiles, function ($a, $b) {
				return $b['file_size_raw'] <=> $a['file_size_raw'];
			});

			echo "<h2>Files Between 50 MB and 100 MB</h2>";
			echo "<table border='1' style='width:100%'>";
			echo "<tr>
                <th>Folder ID</th>
                <th>File Name</th>
                <th>File Size</th>
                <th>File Path</th>
              </tr>";

			foreach ($targetFiles as $file) {
				echo "<tr>";
				echo "<td>{$file['folder_id']}</td>";
				echo "<td>{$file['file_name']}</td>";
				echo "<td style='text-align:right'>{$file['file_size']}</td>";
				echo "<td>{$file['file_path']}</td>";
				echo "</tr>";
			}

			// Display total row
			echo "<tr style='font-weight:bold; background-color:#f2f2f2'>
                <td colspan='2'>Total Files: " . count($targetFiles) . "</td>
                <td style='text-align:right'>" . round($totalSizeMB, 2) . " MB</td>
                <td></td>
              </tr>";
			echo "</table>";

			// CSV Report
			$csvFile = 'medium_files_report_' . date('Y-m-d') . '.csv';
			$csvHandle = fopen($csvFile, 'w');
			fputcsv($csvHandle, ['Folder ID', 'File Name', 'File Size (MB)', 'File Path']);

			foreach ($targetFiles as $file) {
				fputcsv($csvHandle, [
					$file['folder_id'],
					$file['file_name'],
					$file['file_size_raw'], // Numeric value for Excel
					$file['file_path']
				]);
			}

			// Add total to CSV
			fputcsv($csvHandle, []);
			fputcsv($csvHandle, ['Total Files:', count($targetFiles)]);
			fputcsv($csvHandle, ['Total Size:', round($totalSizeMB, 2) . ' MB']);

			fclose($csvHandle);
			echo "<p>Report saved to: <a href='$csvFile' download>$csvFile</a></p>";
		}
	}

	public function delete_pdf_attachment($id)
	{

		$result =  $this->document_management_model->delete_pdf_attachment($id);
		if ($result) {
			set_alert('success', _l('Attachment deleted successfully'));
		} else {
			set_alert('danger', _l('Attachment deleted failed'));
		}

		redirect($_SERVER['HTTP_REFERER']);
	}
}
