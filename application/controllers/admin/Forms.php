<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property Forms_model $forms_model
 */
class Forms extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (get_option('access_forms_to_none_staff_members') == 0 && !is_staff_member()) {
            redirect(admin_url());
        }
        $this->load->model('forms_model');
    }

    public function index($status = '', $userid = '')
    {
        close_setup_menu();

        if (!is_numeric($status)) {
            $status = '';
        }

        $data['table'] = App_table::find('forms');

        if ($this->input->is_ajax_request()) {
            if (!$this->input->post('via_form')) {
                $tableParams = [
                    'status' => $status,
                    'userid' => $userid,
                ];
            } else {
                // request for othes forms when single form is opened
                $tableParams = [
                    'userid'        => $this->input->post('via_form_userid'),
                    'via_form' => $this->input->post('via_form'),
                ];

                if ($tableParams['userid'] == 0) {
                    unset($tableParams['userid']);
                    $tableParams['by_email'] = $this->input->post('via_form_email');
                }
            }
            $data['table']->output($tableParams);
        }

        $data['chosen_form_status']              = $status;
        $data['weekly_forms_opening_statistics'] = json_encode($this->forms_model->get_weekly_forms_opening_statistics());
        $data['title']                             = _l('support_forms');
        $this->load->model('departments_model');
        $data['statuses']             = $this->forms_model->get_form_status();
        $data['staff_deparments_ids'] = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
        $data['departments']          = $this->departments_model->get();
        $data['priorities']           = $this->forms_model->get_priority();
        $data['services']             = $this->forms_model->get_service();
        $data['form_assignees']     = $this->forms_model->get_forms_assignes_disctinct();
        $data['bodyclass']            = 'forms-page';
        add_admin_forms_js_assets();
        $data['default_forms_list_statuses'] = hooks()->apply_filters('default_forms_list_statuses', [1, 2, 4]);
        $this->load->view('admin/forms/list', $data);
    }

    public function add($userid = false)
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            $data['message'] = html_purify($this->input->post('message', false));
            $id              = $this->forms_model->add($data, get_staff_user_id());
            if ($id) {
                set_alert('success', _l('new_form_added_successfully', $id));
                redirect(admin_url('forms/form/' . $id));
            }
        }
        if ($userid !== false) {
            $data['userid'] = $userid;
            $data['client'] = $this->clients_model->get($userid);
        }
        // Load necessary models
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');

        $data['departments']        = $this->departments_model->get();
        $data['predefined_replies'] = $this->forms_model->get_predefined_reply();
        $data['priorities']         = $this->forms_model->get_priority();
        $data['services']           = $this->forms_model->get_service();
        $whereStaff                 = [];
        if (get_option('access_forms_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
        $data['staff']     = $this->staff_model->get('', $whereStaff);
        $data['articles']  = $this->knowledge_base_model->get();
        $data['bodyclass'] = 'form';
        $data['title']     = _l('new_form');

        if ($this->input->get('project_id') && $this->input->get('project_id') > 0) {
            // request from project area to create new form
            $data['project_id'] = $this->input->get('project_id');
            $data['userid']     = get_client_id_by_project_id($data['project_id']);
            if (total_rows(db_prefix() . 'contacts', ['active' => 1, 'userid' => $data['userid']]) == 1) {
                $contact = $this->clients_model->get_contacts($data['userid']);
                if (isset($contact[0])) {
                    $data['contact'] = $contact[0];
                }
            }
        } elseif ($this->input->get('contact_id') && $this->input->get('contact_id') > 0 && $this->input->get('userid')) {
            $contact_id = $this->input->get('contact_id');
            if (total_rows(db_prefix() . 'contacts', ['active' => 1, 'id' => $contact_id]) == 1) {
                $contact = $this->clients_model->get_contact($contact_id);
                if ($contact) {
                    $data['contact'] = (array) $contact;
                }
            }
        }
        $data['projects'] = $this->projects_model->get_items();
        $data['form_listing'] = $this->forms_model->get_form_listing();
        add_admin_forms_js_assets();
        $this->load->view('admin/forms/add', $data);
    }

    public function delete($formid)
    {
        if (!$formid) {
            redirect(admin_url('forms'));
        }

        if (!can_staff_delete_form()) {
            access_denied('delete form');
        }

        $response = $this->forms_model->delete($formid);

        if ($response == true) {
            set_alert('success', _l('deleted', _l('form')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('form_lowercase')));
        }

        // ensure if deleted from single form page, user is redirected to index
        if (str_contains(previous_url(), 'form/' . $formid)) {
            redirect(admin_url('forms'));
            return;
        }
        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }

    public function delete_attachment($id)
    {
        if (is_admin() || (!is_admin() && get_option('allow_non_admin_staff_to_delete_form_attachments') == '1')) {
            if (get_option('staff_access_only_assigned_departments') == 1 && !is_admin()) {
                $attachment = $this->forms_model->get_form_attachment($id);
                $form     = $this->forms_model->get_form_by_id($attachment->formid);

                $this->load->model('departments_model');
                $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                if (!in_array($form->department, $staff_departments)) {
                    set_alert('danger', _l('form_access_by_department_denied'));
                    redirect(admin_url('access_denied'));
                }
            }

            $this->forms_model->delete_form_attachment($id);
        }

        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }

    public function update_staff_replying($formId, $userId = '')
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $this->forms_model->update_staff_replying($formId, $userId)]);
            die;
        }
    }

    public function check_staff_replying($formId)
    {
        if ($this->input->is_ajax_request()) {
            $form            = $this->forms_model->get_staff_replying($formId);
            $isAnotherReplying = $form->staff_id_replying !== null && $form->staff_id_replying !== get_staff_user_id();
            echo json_encode([
                'is_other_staff_replying' => $isAnotherReplying,
                'message'                 => $isAnotherReplying ? e(_l('staff_is_currently_replying', get_staff_full_name($form->staff_id_replying))) : '',
            ]);
            die;
        }
    }

    public function form($id)
    {
        if (!$id) {
            redirect(admin_url('forms/add'));
        }

        $data['form']         = $this->forms_model->get_form_by_id($id);
        $data['merged_forms'] = $this->forms_model->get_merged_forms_by_primary_id($id);

        if (!$data['form']) {
            blank_page(_l('form_not_found'));
        }

        if (get_option('staff_access_only_assigned_departments') == 1) {
            if (!is_admin()) {
                $this->load->model('departments_model');
                $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                if (!in_array($data['form']->department, $staff_departments)) {
                    set_alert('danger', _l('form_access_by_department_denied'));
                    redirect(admin_url('access_denied'));
                }
            }
        }

        if ($this->input->post()) {
            $returnToFormList = false;
            $data               = $this->input->post();

            if (isset($data['form_add_response_and_back_to_list'])) {
                $returnToFormList = true;
                unset($data['form_add_response_and_back_to_list']);
            }

            $data['message'] = html_purify($this->input->post('message', false));
            $replyid         = $this->forms_model->add_reply($data, $id, get_staff_user_id());

            if ($replyid) {
                set_alert('success', _l('replied_to_form_successfully', $id));
            }
            if (!$returnToFormList) {
                redirect(admin_url('forms/form/' . $id));
            } else {
                set_form_open(0, $id);
                redirect(admin_url('forms'));
            }
        }
        // Load necessary models
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');

        $data['statuses']                       = $this->forms_model->get_form_status();
        $data['statuses']['callback_translate'] = 'form_status_translate';

        $data['departments']        = $this->departments_model->get();
        $data['predefined_replies'] = $this->forms_model->get_predefined_reply();
        $data['priorities']         = $this->forms_model->get_priority();
        $data['services']           = $this->forms_model->get_service();
        $whereStaff                 = [];
        if (get_option('access_forms_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
        $data['staff']                = $this->staff_model->get('', $whereStaff);
        $data['articles']             = $this->knowledge_base_model->get();
        $data['form_replies']       = $this->forms_model->get_form_replies($id);
        $data['bodyclass']            = 'top-tabs form single-form';
        $data['title']                = $data['form']->subject;
        $data['form']->form_notes = $this->misc_model->get_notes($id, 'form');
        $data['projects'] = $this->projects_model->get_items();
        $data['form_listing'] = $this->forms_model->get_form_listing();
        add_admin_forms_js_assets();
        $this->load->view('admin/forms/single', $data);
    }

    public function edit_message()
    {
        if (!can_staff_edit_form_message()) {
            access_denied();
        }

        if ($this->input->post()) {
            $data         = $this->input->post();
            $data['data'] = html_purify($this->input->post('data', false));

            if ($data['type'] == 'reply') {
                $this->db->where('id', $data['id']);
                $this->db->update(db_prefix() . 'form_replies', [
                    'message' => $data['data'],
                ]);
            } elseif ($data['type'] == 'form') {
                $this->db->where('formid', $data['id']);
                $this->db->update(db_prefix() . 'forms', [
                    'message' => $data['data'],
                ]);
            }
            if ($this->db->affected_rows() > 0) {
                set_alert('success', _l('form_message_updated_successfully'));
            }
            redirect(admin_url('forms/view_edit_dpr/' . $data['main_form'] . '?tab=settings'));
        }
    }

    public function delete_form_reply($form_id, $reply_id)
    {
        if (!$reply_id) {
            redirect(admin_url('forms'));
        }

        if (!can_staff_delete_form_reply()) {
            access_denied('delete form');
        }

        $response = $this->forms_model->delete_form_reply($form_id, $reply_id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('form_reply')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('form_reply')));
        }
        redirect(admin_url('forms/form/' . $form_id));
    }

    public function change_status_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->forms_model->change_form_status($id, $status));
        }
    }

    public function update_single_form_settings()
    {
        if ($this->input->post()) {
            $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_settings');

            if ($this->input->post('merge_form_ids') !== 0) {
                $formsToMerge = explode(',', $this->input->post('merge_form_ids'));

                $alreadyMergedForms = $this->forms_model->get_already_merged_forms($formsToMerge);
                if (count($alreadyMergedForms) > 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => _l('cannot_merge_forms_with_ids', implode(',', $alreadyMergedForms)),
                    ]);

                    die();
                }
            }
            // $data = $this->input->post();
            // dd($data);
            $success = $this->forms_model->update_single_form_settings($this->input->post());
            if ($success) {
                $this->session->set_flashdata('active_tab', true);
                $this->session->set_flashdata('active_tab_settings', true);
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $form = $this->forms_model->get_form_by_id($this->input->post('formid'));
                    $this->load->model('departments_model');
                    $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    if (!in_array($form->department, $staff_departments) && !is_admin()) {
                        set_alert('success', _l('form_settings_updated_successfully_and_reassigned', $form->department_name));
                        echo json_encode([
                            'success'               => $success,
                            'department_reassigned' => true,
                        ]);
                        die();
                    }
                }
                set_alert('success', _l('form_settings_updated_successfully'));
            }
            echo json_encode([
                'success' => $success,
            ]);
            die();
        }
    }

    // Priorities
    /* Get all form priorities */
    public function priorities()
    {
        if (!is_admin()) {
            access_denied('Form Priorities');
        }
        $data['priorities'] = $this->forms_model->get_priority();
        $data['title']      = _l('form_priorities');
        $this->load->view('admin/forms/priorities/manage', $data);
    }

    /* Add new priority od update existing*/
    public function priority()
    {
        if (!is_admin()) {
            access_denied('Form Priorities');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->forms_model->add_priority($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('form_priority')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->forms_model->update_priority($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('form_priority')));
                }
            }
            die;
        }
    }

    /* Delete form priority */
    public function delete_priority($id)
    {
        if (!is_admin()) {
            access_denied('Form Priorities');
        }
        if (!$id) {
            redirect(admin_url('forms/priorities'));
        }
        $response = $this->forms_model->delete_priority($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('form_priority_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('form_priority')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('form_priority_lowercase')));
        }
        redirect(admin_url('forms/priorities'));
    }

    /* List all form predefined replies */
    public function predefined_replies()
    {
        if (!is_admin()) {
            access_denied('Predefined Replies');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = [
                'name',
            ];
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'forms_predefined_replies';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
                'id',
            ]);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . admin_url('forms/predefined_reply/' . $aRow['id']) . '">' . e($_data) . '</a>';
                    }
                    $row[] = $_data;
                }

                $options = '<div class="tw-flex tw-items-center tw-space-x-3">';
                $options .= '<a href="' . admin_url('forms/predefined_reply/' . $aRow['id']) . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                </a>';

                $options .= '<a href="' . admin_url('forms/delete_predefined_reply/' . $aRow['id']) . '"
                class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                    <i class="fa-regular fa-trash-can fa-lg"></i>
                </a>';
                $options .= '</div>';
                $row[]              = $options;
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('predefined_replies');
        $this->load->view('admin/forms/predefined_replies/manage', $data);
    }

    public function get_predefined_reply_ajax($id)
    {
        echo json_encode($this->forms_model->get_predefined_reply($id));
    }

    public function form_change_data()
    {
        if ($this->input->is_ajax_request()) {
            $contact_id = $this->input->post('contact_id');
            echo json_encode([
                'contact_data'          => $this->clients_model->get_contact($contact_id),
                'customer_has_projects' => customer_has_projects(get_user_id_by_contact_id($contact_id)),
            ]);
        }
    }

    /* Add new reply or edit existing */
    public function predefined_reply($id = '')
    {
        if (!is_admin() && get_option('staff_members_save_forms_predefined_replies') == '0') {
            access_denied('Predefined Reply');
        }
        if ($this->input->post()) {
            $data              = $this->input->post();
            $data['message']   = html_purify($this->input->post('message', false));
            $formAreaRequest = isset($data['form_area']);

            if (isset($data['form_area'])) {
                unset($data['form_area']);
            }

            if ($id == '') {
                $id = $this->forms_model->add_predefined_reply($data);
                if (!$formAreaRequest) {
                    if ($id) {
                        set_alert('success', _l('added_successfully', _l('predefined_reply')));
                        redirect(admin_url('forms/predefined_reply/' . $id));
                    }
                } else {
                    echo json_encode(['success' => $id ? true : false, 'id' => $id]);
                    die;
                }
            } else {
                $success = $this->forms_model->update_predefined_reply($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('predefined_reply')));
                }
                redirect(admin_url('forms/predefined_reply/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('predefined_reply_lowercase'));
        } else {
            $predefined_reply         = $this->forms_model->get_predefined_reply($id);
            $data['predefined_reply'] = $predefined_reply;
            $title                    = _l('edit', _l('predefined_reply_lowercase')) . ' ' . $predefined_reply->name;
        }
        $data['title'] = $title;
        $this->load->view('admin/forms/predefined_replies/reply', $data);
    }

    /* Delete form reply from database */
    public function delete_predefined_reply($id)
    {
        if (!is_admin()) {
            access_denied('Delete Predefined Reply');
        }
        if (!$id) {
            redirect(admin_url('forms/predefined_replies'));
        }
        $response = $this->forms_model->delete_predefined_reply($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('predefined_reply')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('predefined_reply_lowercase')));
        }
        redirect(admin_url('forms/predefined_replies'));
    }

    // Form statuses
    /* Get all form statuses */
    public function statuses()
    {
        if (!is_admin()) {
            access_denied('Form Statuses');
        }
        $data['statuses'] = $this->forms_model->get_form_status();
        $data['title']    = 'Form statuses';
        $this->load->view('admin/forms/forms_statuses/manage', $data);
    }

    /* Add new or edit existing status */
    public function status()
    {
        if (!is_admin()) {
            access_denied('Form Statuses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->forms_model->add_form_status($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('form_status')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->forms_model->update_form_status($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('form_status')));
                }
            }
            die;
        }
    }

    /* Delete form status from database */
    public function delete_form_status($id)
    {
        if (!is_admin()) {
            access_denied('Form Statuses');
        }
        if (!$id) {
            redirect(admin_url('forms/statuses'));
        }
        $response = $this->forms_model->delete_form_status($id);
        if (is_array($response) && isset($response['default'])) {
            set_alert('warning', _l('cant_delete_default', _l('form_status_lowercase')));
        } elseif (is_array($response) && isset($response['referenced'])) {
            set_alert('danger', _l('is_referenced', _l('form_status_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('form_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('form_status_lowercase')));
        }
        redirect(admin_url('forms/statuses'));
    }

    /* List all form services */
    public function services()
    {
        if (!is_admin()) {
            access_denied('Form Services');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = [
                'serviceid',
                'name',
            ];
            $sIndexColumn = 'serviceid';
            $sTable       = db_prefix() . 'services';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
                'serviceid',
            ]);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="#" onclick="edit_service(this,' . $aRow['serviceid'] . ');return false" data-name="' . $aRow['name'] . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }
                $options = icon_btn('#', 'fa-regular fa-pen-to-square', 'btn-default', [
                    'data-name' => $aRow['name'],
                    'onclick'   => 'edit_service(this,' . $aRow['serviceid'] . '); return false;',
                ]);
                $row[]              = $options .= icon_btn('forms/delete_service/' . $aRow['serviceid'], 'fa fa-remove', 'btn-danger _delete');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('services');
        $this->load->view('admin/forms/services/manage', $data);
    }

    /* Add new service od delete existing one */
    public function service($id = '')
    {
        if (!is_admin() && get_option('staff_members_save_forms_predefined_replies') == '0') {
            access_denied('Form Services');
        }

        if ($this->input->post()) {
            $post_data = $this->input->post();
            if (!$this->input->post('id')) {
                $requestFromFormArea = isset($post_data['form_area']);
                if (isset($post_data['form_area'])) {
                    unset($post_data['form_area']);
                }
                $id = $this->forms_model->add_service($post_data);
                if (!$requestFromFormArea) {
                    if ($id) {
                        set_alert('success', _l('added_successfully', _l('service')));
                    }
                } else {
                    echo json_encode(['success' => $id ? true : false, 'id' => $id, 'name' => $post_data['name']]);
                }
            } else {
                $id = $post_data['id'];
                unset($post_data['id']);
                $success = $this->forms_model->update_service($post_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('service')));
                }
            }
            die;
        }
    }

    /* Delete form service from database */
    public function delete_service($id)
    {
        if (!is_admin()) {
            access_denied('Form Services');
        }
        if (!$id) {
            redirect(admin_url('forms/services'));
        }
        $response = $this->forms_model->delete_service($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('service_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('service')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('service_lowercase')));
        }
        redirect(admin_url('forms/services'));
    }

    public function block_sender()
    {
        if ($this->input->post()) {
            $this->load->model('spam_filters_model');
            $sender  = $this->input->post('sender');
            $success = $this->spam_filters_model->add(['type' => 'sender', 'value' => $sender], 'forms');
            if ($success) {
                set_alert('success', _l('sender_blocked_successfully'));
            }
        }
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_forms');
        if ($this->input->post()) {
            $ids      = $this->input->post('ids');
            $is_admin = is_admin();
            $staffCanDeleteForm = can_staff_delete_form();

            if (!is_array($ids)) {
                return;
            }

            if ($this->input->post('merge_forms')) {
                $primary_form = $this->input->post('primary_form');
                $status         = $this->input->post('primary_form_status');

                if ($this->forms_model->is_merged($primary_form)) {
                    set_alert('warning', _l('cannot_merge_into_merged_form'));

                    return;
                }

                $total_merged = $this->forms_model->merge($primary_form, $status, $ids);
            } elseif ($this->input->post('mass_delete')) {
                $total_deleted = 0;
                if ($is_admin || $staffCanDeleteForm) {
                    foreach ($ids as $id) {
                        if ($this->forms_model->delete($id)) {
                            $total_deleted++;
                        }
                    }
                } else {
                    ajax_access_denied();
                    return;
                }
            } else {
                $status     = $this->input->post('status');
                $department = $this->input->post('department');
                $service    = $this->input->post('service');
                $priority   = $this->input->post('priority');
                $tags       = $this->input->post('tags');

                foreach ($ids as $id) {
                    if ($status) {
                        $this->db->where('formid', $id);
                        $this->db->update(db_prefix() . 'forms', [
                            'status' => $status,
                        ]);
                    }
                    if ($department) {
                        $this->db->where('formid', $id);
                        $this->db->update(db_prefix() . 'forms', [
                            'department' => $department,
                        ]);
                    }
                    if ($priority) {
                        $this->db->where('formid', $id);
                        $this->db->update(db_prefix() . 'forms', [
                            'priority' => $priority,
                        ]);
                    }

                    if ($service) {
                        $this->db->where('formid', $id);
                        $this->db->update(db_prefix() . 'forms', [
                            'service' => $service,
                        ]);
                    }
                    if ($tags) {
                        handle_tags_save($tags, $id, 'form');
                    }
                }
            }

            if ($this->input->post('mass_delete')) {
                set_alert('success', _l('total_forms_deleted', $total_deleted));
            } elseif ($this->input->post('merge_forms') && $total_merged > 0) {
                set_alert('success', _l('forms_merged'));
            }
        }
    }

    public function find_project_contact()
    {
        $response = array();
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!empty($data['project_id'])) {
                $response = $this->forms_model->find_project_contact($data['project_id']);
            }
        }
        echo json_encode($response);
    }

    public function find_form_design($form_type, $form_id = 0)
    {

        if ($form_type == "dpr") {
            $dpr_row_template = $this->forms_model->create_dpr_row_template();
            if ($form_id != 0) {
                $dpr_form = $this->forms_model->get_dpr_form($form_id);
                $dpr_form_detail = $this->forms_model->get_dpr_form_detail($form_id);
                if (!empty($dpr_form_detail)) {
                    $index_order = 0;
                    foreach ($dpr_form_detail as $value) {
                        $index_order++;
                        $dpr_row_template .= $this->forms_model->create_dpr_row_template(
                            'items[' . $index_order . ']',
                            $value['location'],
                            $value['agency'],
                            $value['type'],
                            $value['work_execute'],
                            $value['material_consumption'],
                            $value['machinery'],
                            $value['skilled'],
                            $value['unskilled'],
                            $value['depart'],
                            $value['total'],
                            $value['male'],
                            $value['female'],
                            true,
                            $value['id']
                        );
                    }
                }
                $data['dpr_form'] = $dpr_form;
            }
            $data['dpr_row_template'] = $dpr_row_template;
            $this->load->view('admin/forms/form_design/dpr', $data);
        } elseif ($form_type == "qcr") {
            $qcr_row_template = $this->forms_model->create_qcr_row_template();
            if ($form_id != 0) {
                $qcr_form = $this->forms_model->get_qcr_form($form_id);
                $qcr_form_detail = $this->forms_model->get_qcr_form_detail($form_id);
                if (!empty($qcr_form_detail)) {
                    $index_order = 0;
                    foreach ($qcr_form_detail as $value) {
                        $index_order++;
                        $qcr_row_template .= $this->forms_model->create_qcr_row_template(
                            'items[' . $index_order . ']',
                            $value['date'],
                            $value['floor'],
                            $value['location'],
                            $value['observation'],
                            $value['category'],
                            $value['photograph'],
                            $value['compliance_photograph'],
                            $value['compliance_detail'],
                            $value['status'],
                            $value['remarks'],
                            true,
                            $value['id'],
                            $value,
                        );
                    }
                }
                $data['qcr_form'] = $qcr_form;
            }
            $data['qcr_row_template'] = $qcr_row_template;
            $this->load->view('admin/forms/form_design/qcr', $data);
        } else {
            $formConfigs = [
                'apc' => ['has_attachments' => true],
                'wpc' => ['has_attachments' => true],
                'mfa' => ['has_attachments' => false],
                'mlg' => ['has_attachments' => true],
                'msh' => ['has_attachments' => true],
                'sca' => ['has_attachments' => true],
                'esc' => ['has_attachments' => true],
                'cfwas' => ['has_attachments' => true],
                'cflc' => ['has_attachments' => true],
                'facc' => ['has_attachments' => true],
                'cosc' => ['has_attachments' => true],
                'qor'  => ['has_attachments' => true],
            ];

            if (isset($formConfigs[$form_type])) {
                $this->handleCommonForm(
                    $form_type,
                    $formConfigs[$form_type]['has_attachments'],
                    $form_id
                );
            } else {
                show_error('Invalid form type specified.');
            }
        }
    }
    private function handleCommonForm($form_type, $has_attachments, $form_id)
    {
        $form_items = $this->forms_model->get_form_items($form_type);
        $data = [];
        $data['isedit'] = 0;
        if ($form_id != 0) {

            $getFormMethod = "get_{$form_type}_form";
            $data["{$form_type}_form"] = $this->forms_model->$getFormMethod($form_id);

            $getDetailMethod = "get_{$form_type}_form_detail";
            $data["{$form_type}_form_detail"] = $this->forms_model->$getDetailMethod($form_id);

            if ($has_attachments) {
                $getAttachmentsMethod = "get_{$form_type}_form_attachments";
                $data["{$form_type}_attachments"] = $this->forms_model->$getAttachmentsMethod($form_id);
            }
            $data['isedit'] = 1;
            $data['form_id'] = $form_id;
        }
        $data['form_items'] = $form_items;
        $this->load->view("admin/forms/form_design/{$form_type}", $data);
    }

    public function get_qcr_row_template()
    {
        $name = $this->input->post('name');
        $date = $this->input->post('date');
        $floor = $this->input->post('floor');
        $location = $this->input->post('location');
        $observation = $this->input->post('observation');
        $category = $this->input->post('category');
        $photograph = $this->input->post('photograph');
        $compliance_photograph = $this->input->post('compliance_photograph');
        $compliance_detail = $this->input->post('compliance_detail');
        $status = $this->input->post('status');
        $remarks = $this->input->post('remarks');
        $item_key = $this->input->post('item_key');

        echo $this->forms_model->create_qcr_row_template($name, $date, $floor, $location, $observation, $category, $photograph, $compliance_photograph, $compliance_detail, $status, $remarks, false, $item_key);
    }
    public function delete_apc_attachment($id)
    {
        $this->forms_model->delete_apc_attachment($id);
    }
    public function delete_msh_attachment($id)
    {
        $this->forms_model->delete_msh_attachment($id);
    }
    public function delete_sca_attachment($id)
    {
        $this->forms_model->delete_sca_attachment($id);
    }
    public function delete_mlg_attachment($id)
    {
        $this->forms_model->delete_mlg_attachment($id);
    }
    public function delete_wpc_attachment($id)
    {
        $this->forms_model->delete_wpc_attachment($id);
    }
    public function delete_esc_attachment($id)
    {
        $this->forms_model->delete_esc_attachment($id);
    }

    public function delete_cfwas_attachment($id)
    {
        $this->forms_model->delete_cfwas_attachment($id);
    }
    public function delete_cflc_attachment($id)
    {
        $this->forms_model->delete_cflc_attachment($id);
    }
    public function delete_facc_attachment($id)
    {
        $this->forms_model->delete_facc_attachment($id);
    }
    public function delete_cosc_attachment($id)
    {
        $this->forms_model->delete_cosc_attachment($id);
    }

    public function delete_qor_attachment($id)
    {
        $this->forms_model->delete_qor_attachment($id);
    }
    /**
     * Generate a PDF file for the given form
     *
     * If a form ID is not provided, redirect to the form list page.
     *
     * @param int $id form ID
     *
     * @throws Exception
     */
    public function pdf($id)
    {
        if (!$id) {
            redirect(admin_url('forms'));
        }

        $form = $this->forms_model->get_form_by_id($id);

        try {
            $pdf = form_pdf($form);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'I';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'D';
        }

        $pdf->Output(mb_strtoupper(slug_it($form->subject)) . '.pdf', $type);
    }
    public function pdf_qor($id)
    {
        if (!$id) {
            redirect(admin_url('forms'));
        }

        $form = $this->forms_model->get_form_by_id($id);

        try {
            $pdf = form_pdf_qor($form);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'I';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'D';
        }

        $pdf->Output(mb_strtoupper(slug_it($form->subject)) . '.pdf', $type);
    }

    public function progress_report_listing($module = 'dpr')
    {
        $status = '';
        $userid = '';
        if (!is_numeric($status)) {
            $status = '';
        }

        $data['table'] = App_table::find('preports');

        if ($this->input->is_ajax_request()) {
            if (!$this->input->post('via_form')) {
                $tableParams = [
                    'status' => $status,
                    'userid' => $userid,
                ];
            } else {
                // request for othes forms when single form is opened
                $tableParams = [
                    'userid'        => $this->input->post('via_form_userid'),
                    'via_form' => $this->input->post('via_form'),
                ];

                if ($tableParams['userid'] == 0) {
                    unset($tableParams['userid']);
                    $tableParams['by_email'] = $this->input->post('via_form_email');
                }
            }
            $tableParams['module'] = $module;
            $data['table']->output($tableParams);
        }

        $data['chosen_form_status']              = $status;
        $data['weekly_forms_opening_statistics'] = json_encode($this->forms_model->get_weekly_forms_opening_statistics());
        $data['title']                             = _l('support_forms');
        $this->load->model('departments_model');
        $data['statuses']             = $this->forms_model->get_form_status();
        $data['staff_deparments_ids'] = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
        $data['departments']          = $this->departments_model->get();
        $data['priorities']           = $this->forms_model->get_priority();
        $data['services']             = $this->forms_model->get_service();
        $data['form_assignees']     = $this->forms_model->get_forms_assignes_disctinct();
        $data['bodyclass']            = 'forms-page';
        add_admin_progress_reports_js_assets();
        $data['default_forms_list_statuses'] = hooks()->apply_filters('default_forms_list_statuses', [1, 2, 4]);
        $data['module'] = $module;
        $this->load->view('admin/progress_reports/report_listing', $data);
    }

    public function find_dpr_design($form_id = 0)
    {
        $dpr_row_template = $this->forms_model->create_dpr_row_template();
        if ($form_id != 0) {
            $dpr_main_form = $this->forms_model->get_form($form_id);
            $dpr_form = $this->forms_model->get_dpr_form($form_id);
            $dpr_form_detail = $this->forms_model->get_dpr_form_detail($form_id);
            if (!empty($dpr_form_detail)) {
                $index_order = 0;
                foreach ($dpr_form_detail as $value) {
                    $index_order++;
                    $dpr_row_template .= $this->forms_model->create_dpr_row_template(
                        'items[' . $index_order . ']',
                        $value['location'],
                        $value['agency'],
                        $value['type'],
                        $value['sub_type'],
                        $value['work_execute'],
                        $value['material_consumption'],
                        $value['male'],
                        $value['female'],
                        $value['total'],
                        $value['machinery'],
                        $value['total_machinery'],
                        true,
                        $value['id']
                    );
                }
            }
            $data['dpr_form'] = $dpr_form;
            $data['dpr_main_form'] = $dpr_main_form;
        }
        $data['dpr_row_template'] = $dpr_row_template;
        $this->load->view('admin/progress_reports/dpr/dpr_form_design', $data);
    }

    /**
     * Gets the Daily Progress Report row template.
     */
    public function get_dpr_row_template()
    {
        $name = $this->input->post('name');
        $location = $this->input->post('location');
        $agency = $this->input->post('agency');
        $type = $this->input->post('type');
        $sub_type = $this->input->post('sub_type');
        $work_execute = $this->input->post('work_execute');
        $material_consumption = $this->input->post('material_consumption');
        $male = $this->input->post('male');
        $female = $this->input->post('female');
        $total = $this->input->post('total');
        $machinery = $this->input->post('machinery');
        $total_machinery = $this->input->post('total_machinery');
        $item_key = $this->input->post('item_key');

        echo $this->forms_model->create_dpr_row_template($name, $location, $agency, $type, $sub_type, $work_execute, $material_consumption, $male, $female, $total, $machinery, $total_machinery, false, $item_key);
    }

    public function add_dpr($userid = false)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['form_type'] = 'dpr';

            $data['message'] = html_purify($this->input->post('message', false));
            $id              = $this->forms_model->add($data, get_staff_user_id());
            if ($id) {
                set_alert('success', _l('dpr_added_successfully', $id));
                redirect(admin_url('forms/progress_report_setting/dpr'));
            }
        }
        if ($userid !== false) {
            $data['userid'] = $userid;
            $data['client'] = $this->clients_model->get($userid);
        }
        // Load necessary models
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');

        $data['departments']        = $this->departments_model->get();
        $data['predefined_replies'] = $this->forms_model->get_predefined_reply();
        $data['priorities']         = $this->forms_model->get_priority();
        $data['services']           = $this->forms_model->get_service();
        $whereStaff                 = [];
        if (get_option('access_forms_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
        $data['staff']     = $this->staff_model->get('', $whereStaff);
        $data['articles']  = $this->knowledge_base_model->get();
        $data['bodyclass'] = 'form';
        $data['title']     = _l('new_form');

        if ($this->input->get('project_id') && $this->input->get('project_id') > 0) {
            // request from project area to create new form
            $data['project_id'] = $this->input->get('project_id');
            $data['userid']     = get_client_id_by_project_id($data['project_id']);
            if (total_rows(db_prefix() . 'contacts', ['active' => 1, 'userid' => $data['userid']]) == 1) {
                $contact = $this->clients_model->get_contacts($data['userid']);
                if (isset($contact[0])) {
                    $data['contact'] = $contact[0];
                }
            }
        } elseif ($this->input->get('contact_id') && $this->input->get('contact_id') > 0 && $this->input->get('userid')) {
            $contact_id = $this->input->get('contact_id');
            if (total_rows(db_prefix() . 'contacts', ['active' => 1, 'id' => $contact_id]) == 1) {
                $contact = $this->clients_model->get_contact($contact_id);
                if ($contact) {
                    $data['contact'] = (array) $contact;
                }
            }
        }
        $data['projects'] = $this->projects_model->get_items();
        $data['form_listing'] = $this->forms_model->get_form_listing();
        add_admin_progress_reports_js_assets();
        $this->load->view('admin/progress_reports/dpr/add', $data);
    }

    public function view_edit_dpr($id)
    {
        if (!$id) {
            redirect(admin_url('forms/add'));
        }

        $data['form']         = $this->forms_model->get_form_by_id($id);
        $data['merged_forms'] = $this->forms_model->get_merged_forms_by_primary_id($id);

        if (!$data['form']) {
            blank_page(_l('form_not_found'));
        }

        if (get_option('staff_access_only_assigned_departments') == 1) {
            if (!is_admin()) {
                $this->load->model('departments_model');
                $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                if (!in_array($data['form']->department, $staff_departments)) {
                    set_alert('danger', _l('form_access_by_department_denied'));
                    redirect(admin_url('access_denied'));
                }
            }
        }

        if ($this->input->post()) {
            $returnToFormList = false;
            $data               = $this->input->post();

            if (isset($data['form_add_response_and_back_to_list'])) {
                $returnToFormList = true;
                unset($data['form_add_response_and_back_to_list']);
            }

            $data['message'] = html_purify($this->input->post('message', false));
            $replyid         = $this->forms_model->add_reply($data, $id, get_staff_user_id());

            if ($replyid) {
                set_alert('success', _l('replied_to_form_successfully', $id));
            }
            if (!$returnToFormList) {
                redirect(admin_url('forms/form/' . $id));
            } else {
                set_form_open(0, $id);
                redirect(admin_url('forms'));
            }
        }
        // Load necessary models
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');

        $data['statuses']                       = $this->forms_model->get_form_status();
        $data['statuses']['callback_translate'] = 'form_status_translate';

        $data['departments']        = $this->departments_model->get();
        $data['predefined_replies'] = $this->forms_model->get_predefined_reply();
        $data['priorities']         = $this->forms_model->get_priority();
        $data['services']           = $this->forms_model->get_service();
        $whereStaff                 = [];
        if (get_option('access_forms_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
        $data['staff']                = $this->staff_model->get('', $whereStaff);
        $data['articles']             = $this->knowledge_base_model->get();
        $data['form_replies']       = $this->forms_model->get_form_replies($id);
        $data['bodyclass']            = 'top-tabs form single-form';
        $data['title']                = $data['form']->subject;
        $data['form']->form_notes = $this->misc_model->get_notes($id, 'form');
        $data['projects'] = $this->projects_model->get_items();
        $data['form_listing'] = $this->forms_model->get_form_listing();
        $data['daily_labor_report'] = $this->forms_model->get_daily_labor_report($id);
        $data['labor_report_machinery'] = $this->forms_model->get_labor_report_machinery($id);
        add_admin_progress_reports_js_assets();
        $this->load->view('admin/progress_reports/dpr/view_edit', $data);
    }

    public function update_dpr_changes()
    {
        if ($this->input->post()) {
            $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_settings');

            if ($this->input->post('merge_form_ids') !== 0) {
                $formsToMerge = explode(',', $this->input->post('merge_form_ids'));

                $alreadyMergedForms = $this->forms_model->get_already_merged_forms($formsToMerge);
                if (count($alreadyMergedForms) > 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => _l('cannot_merge_forms_with_ids', implode(',', $alreadyMergedForms)),
                    ]);

                    die();
                }
            }
            // $data = $this->input->post();
            // dd($data);
            $success = $this->forms_model->update_single_form_settings($this->input->post());
            if ($success) {
                $this->session->set_flashdata('active_tab', true);
                $this->session->set_flashdata('active_tab_settings', true);
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $form = $this->forms_model->get_form_by_id($this->input->post('formid'));
                    $this->load->model('departments_model');
                    $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    if (!in_array($form->department, $staff_departments) && !is_admin()) {
                        set_alert('success', _l('form_settings_updated_successfully_and_reassigned', $form->department_name));
                        echo json_encode([
                            'success'               => $success,
                            'department_reassigned' => true,
                        ]);
                        die();
                    }
                }
                set_alert('success', _l('dpr_updated_successfully'));
            }
            echo json_encode([
                'success' => $success,
            ]);
            die();
        }
    }

    public function progress_report_setting()
    {
        $data['group'] = $this->input->get('group');
        $data['title'] = _l('setting');
        $data['tab'][] = 'progress_report_type';
        $data['tab'][] = 'progress_report_sub_type';
        $data['tab'][] = 'progress_report_machinary';
        if ($data['group'] == '') {
            $data['group'] = 'progress_report_type';
        }
        $data['tabs']['view'] = 'admin/progress_reports/includes/' . $data['group'];
        $data['progress_report_type'] = $this->forms_model->get_progress_report_type();
        $data['progress_report_sub_type'] = $this->forms_model->get_progress_report_sub_type();
        $data['progress_report_machinary'] = $this->forms_model->get_progress_report_machinary();

        $this->load->view('admin/progress_reports/manage_setting', $data);
    }

    public function progress_report_type()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $id = $this->forms_model->add_progress_report_type($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('progress_report_type'));
                    set_alert('success', $message);
                }
                redirect(admin_url('forms/progress_report_setting?group=progress_report_type'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->forms_model->update_progress_report_type($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('progress_report_type'));
                    set_alert('success', $message);
                }
                redirect(admin_url('forms/progress_report_setting?group=progress_report_type'));
            }
            die;
        }
    }

    public function delete_progress_report_type($id)
    {
        if (!$id) {
            redirect(admin_url('forms/progress_report_setting?group=progress_report_type'));
        }
        $response = $this->forms_model->delete_progress_report_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('progress_report_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('progress_report_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('progress_report_type')));
        }
        redirect(admin_url('forms/progress_report_setting?group=progress_report_type'));
    }

    public function progress_report_sub_type()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $id = $this->forms_model->add_progress_report_sub_type($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('progress_report_sub_type'));
                    set_alert('success', $message);
                }
                redirect(admin_url('forms/progress_report_setting?group=progress_report_sub_type'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->forms_model->update_progress_report_sub_type($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('progress_report_sub_type'));
                    set_alert('success', $message);
                }
                redirect(admin_url('forms/progress_report_setting?group=progress_report_sub_type'));
            }
            die;
        }
    }

    public function delete_progress_report_sub_type($id)
    {
        if (!$id) {
            redirect(admin_url('forms/progress_report_setting?group=progress_report_sub_type'));
        }
        $response = $this->forms_model->delete_progress_report_sub_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('progress_report_sub_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('progress_report_sub_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('progress_report_sub_type')));
        }
        redirect(admin_url('forms/progress_report_setting?group=progress_report_sub_type'));
    }

    public function progress_report_machinary()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $id = $this->forms_model->add_progress_report_machinary($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('progress_report_machinary'));
                    set_alert('success', $message);
                }
                redirect(admin_url('forms/progress_report_setting?group=progress_report_machinary'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->forms_model->update_progress_report_machinary($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('progress_report_machinary'));
                    set_alert('success', $message);
                }
                redirect(admin_url('forms/progress_report_setting?group=progress_report_machinary'));
            }
            die;
        }
    }

    public function delete_progress_report_machinary($id)
    {
        if (!$id) {
            redirect(admin_url('forms/progress_report_setting?group=progress_report_machinary'));
        }
        $response = $this->forms_model->delete_progress_report_machinary($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('progress_report_machinary')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('progress_report_machinary')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('progress_report_machinary')));
        }
        redirect(admin_url('forms/progress_report_setting?group=progress_report_machinary'));
    }

    public function dpr_dashboard()
    {
        $data = array();
        $data['projects'] = $this->forms_model->get_dpr_projects();
        $this->load->view('admin/progress_reports/dpr/dpr_dashboard', $data);
    }

    public function get_dpr_dashboard()
    {
        $data = $this->input->post();
        $result = $this->forms_model->get_dpr_dashboard($data);
        echo json_encode($result);
        die;
    }

    public function form_dpr_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('forms'));
        }

        $form_drp_data = $this->forms_model->get_form_dpr_pdf_data($id);

        if (!empty($form_drp_data)) {
            $pdf = create_dpr_form_pdf($form_drp_data);
            $type = 'D';
            if ($this->input->get('output_type')) {
                $type = $this->input->get('output_type');
            }
            if ($this->input->get('print')) {
                $type = 'I';
            }
            $pdf->Output('DPR' . date('d-m') . 'pdf', $type);
        } else {
            echo "PDF have not created yet.";
        }
    }
}
