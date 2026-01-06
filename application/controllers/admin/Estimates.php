<?php

use app\services\estimates\EstimatesPipeline;
use app\services\estimates\AllProjectTimelinesGantt;

defined('BASEPATH') or exit('No direct script access allowed');

class Estimates extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('estimates_model');
    }

    /* Get all estimates in case user go on index page */
    public function index($id = '')
    {
        $this->list_estimates($id);
    }

    /* List all estimates datatables */
    public function list_estimates($id = '')
    {
        $this->app_scripts->add('frappe-gantt-js', 'assets/plugins/frappe/frappe-gantt-es2015.js', 'admin', ['vendor-js']);
        $this->app_css->add('frappe-gantt-js', 'assets/plugins//frappe/frappe-gantt.css', 'admin', ['vendor-css']);
        if (staff_cant('view', 'estimates') && staff_cant('view_own', 'estimates') && get_option('allow_staff_view_estimates_assigned') == '0') {
            access_denied('estimates');
        }

        $isPipeline = $this->session->userdata('estimate_pipeline') == 'true';

        $data['estimate_statuses'] = $this->estimates_model->get_statuses();
        $data['estimates_table'] = App_table::find('estimates');
        
        if ($isPipeline && !$this->input->get('status') && !$this->input->get('filter')) {
            $data['title']           = _l('estimates_pipeline');
            $data['bodyclass']       = 'estimates-pipeline estimates-total-manual';
            $data['switch_pipeline'] = false;

            if (is_numeric($id)) {
                $data['estimateid'] = $id;
            } else {
                $data['estimateid'] = $this->session->flashdata('estimateid');
            }

            $this->load->view('admin/estimates/pipeline/manage', $data);
        } else {

            // Pipeline was initiated but user click from home page and need to show table only to filter
            if ($this->input->get('status') || $this->input->get('filter') && $isPipeline) {
                $this->pipeline(0, true);
            }

            $data['estimateid']            = $id;
            $data['switch_pipeline']       = true;
            $data['title']                 = _l('estimates');
            $data['bodyclass']             = 'estimates-total-manual';
            $data['estimates_years']       = $this->estimates_model->get_estimates_years();
            $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();
        
            $this->load->view('admin/estimates/manage', $data);
        }
    }

    public function table($clientid = '')
    {
        if (staff_cant('view', 'estimates') && staff_cant('view_own', 'estimates') && get_option('allow_staff_view_estimates_assigned') == '0') {
            ajax_access_denied();
        }

        App_table::find('estimates')->output([
            'clientid' => $clientid,
        ]);
    }

    /* Add new estimate or update existing */
    public function estimate($id = '')
    {
        $this->load->model('costplanning_model');
        if ($this->input->post()) {
            $estimate_data = $this->input->post();

            $save_and_send_later = false;
            if (isset($estimate_data['save_and_send_later'])) {
                unset($estimate_data['save_and_send_later']);
                $save_and_send_later = true;
            }

            if ($id == '') {
                if (staff_cant('create', 'estimates')) {
                    access_denied('estimates');
                }
                $id = $this->estimates_model->add($estimate_data);

                if ($id) {
                    set_alert('success', _l('added_successfully', _l('estimate')));

                    $redUrl = admin_url('estimates/list_estimates/' . $id);

                    if ($save_and_send_later) {
                        $this->session->set_userdata('send_later', true);
                        // die(redirect($redUrl));
                    }

                    redirect(
                        !$this->set_estimate_pipeline_autoload($id) ? $redUrl : admin_url('estimates/list_estimates/')
                    );
                }
            } else {
                if (staff_cant('edit', 'estimates')) {
                    access_denied('estimates');
                }
                $success = $this->estimates_model->update($estimate_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('estimate')));
                }
                // if ($this->set_estimate_pipeline_autoload($id)) {
                //     redirect(admin_url('estimates/list_estimates/'));
                // } else {
                //     redirect(admin_url('estimates/list_estimates/' . $id));
                // }
                redirect(admin_url('estimates/estimate/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('create_new_estimate');
        } else {
            $estimate = $this->estimates_model->get($id);

            if (!$estimate || !user_can_view_estimate($id)) {
                blank_page(_l('estimate_not_found'));
            }

            $data['estimate'] = $estimate;
            $data['estimate_budget_info'] = $this->estimates_model->get_estimate_budget_info($id);
            $data['edit']     = true;
            $data['annexure_estimate'] = $this->estimates_model->get_annexure_estimate_details($id);
            $data['all_area_working'] = $this->estimates_model->get_area_working($id);
            $data['area_statement_tabs'] = $this->estimates_model->get_area_statement_tabs($id);
            $data['all_area_summary'] = $this->estimates_model->get_area_summary($id);
            $data['last_revision'] = get_estimate_revision_no($id, 0, 1);
            $data['next_revision'] = !empty($data['last_revision']) ? $data['last_revision'] + 1 : 1;
            $data['milestones_exclude_completed_tasks'] = $this->input->get('exclude_completed') && $this->input->get('exclude_completed') == 'yes' || !$this->input->get('exclude_completed');
            $data['total_milestones'] = total_rows(db_prefix() . 'project_timelines', ['estimate_id' => $id]);
            $data['milestones_found'] = $data['total_milestones'] > 0 || (!$data['total_milestones'] && total_rows(db_prefix() . 'tasks', ['rel_id' => $id, 'rel_type' => 'budget', 'milestone' => 0]) > 0);
            $title            = _l('edit', _l('estimate_lowercase'));
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        if ($this->input->get('estimate_request_id')) {
            $data['estimate_request_id'] = $this->input->get('estimate_request_id');
        }

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $this->load->model('invoice_items_model');
        $this->load->model('invoices_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();
        $data['get_hsn_sac_code']  = $this->invoices_model->get_hsn_sac_code();
        $data['master_area'] = $this->costplanning_model->get_master_area();
        $data['functionality_area'] = $this->costplanning_model->get_functionality_area();
        $data['units'] = $this->costplanning_model->get_units();
        $data['area_summary_tabs'] = $this->estimates_model->get_area_summary_tabs();
        $data['sub_head'] = $this->costplanning_model->get_sub_group();
        $data['title']             = $title;
        $this->load->view('admin/estimates/estimate', $data);
    }

    public function clear_signature($id)
    {
        if (staff_can('delete',  'estimates')) {
            $this->estimates_model->clear_signature($id);
        }

        redirect(admin_url('estimates/list_estimates/' . $id));
    }

    public function update_number_settings($id)
    {
        $response = [
            'success' => false,
            'message' => '',
        ];
        if (staff_can('edit',  'estimates')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'estimates', [
                'prefix' => $this->input->post('prefix'),
            ]);
            if ($this->db->affected_rows() > 0) {
                $response['success'] = true;
                $response['message'] = _l('updated_successfully', _l('estimate'));
            }
        }

        echo json_encode($response);
        die;
    }

    public function validate_estimate_number()
    {
        $isedit          = $this->input->post('isedit');
        $number          = $this->input->post('number');
        $date            = $this->input->post('date');
        $original_number = $this->input->post('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');

        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }

        if (total_rows(db_prefix() . 'estimates', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->estimates_model->delete_attachment($id);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Get all estimate data used when user click on estimate number in a datatable left side*/
    public function get_estimate_data_ajax($id, $to_return = false)
    {
        if (staff_cant('view', 'estimates') && staff_cant('view_own', 'estimates') && get_option('allow_staff_view_estimates_assigned') == '0') {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No estimate found');
        }

        $estimate = $this->estimates_model->get($id);

        if (!$estimate || !user_can_view_estimate($id)) {
            echo _l('estimate_not_found');
            die;
        }

        $estimate->date       = _d($estimate->date);
        $estimate->expirydate = _d($estimate->expirydate);
        if ($estimate->invoiceid !== null) {
            $this->load->model('invoices_model');
            $estimate->invoice = $this->invoices_model->get($estimate->invoiceid);
        }

        if ($estimate->sent == 0) {
            $template_name = 'estimate_send_to_customer';
        } else {
            $template_name = 'estimate_send_to_customer_already_sent';
        }

        $data = prepare_mail_preview_data($template_name, $estimate->clientid);

        $data['activity']          = $this->estimates_model->get_estimate_activity($id);
        $data['estimate']          = $estimate;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();
        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'estimate']);
        $data['co_total']          = $this->estimates_model->get_co_total_for_estimate($id);
        $data['cost_planning_details'] = $this->estimates_model->get_cost_planning_details($id);
        $root_estimate = get_root_estimate_id($id);
        $data['root_estimate_data'] = $this->estimates_model->get_cost_planning_details($root_estimate);
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }
        $this->load->model('purchase/purchase_model');
        $data['sub_groups_pur'] = $this->purchase_model->get_sub_group();
        $data['estimate_budget_listing'] = $this->estimates_model->get_estimate_budget_listing($id);
        $data['gantt_data'] = (new AllProjectTimelinesGantt([
            'estimate_id' => $id,
        ]))->get();

        if ($to_return == false) {
            $this->load->view('admin/estimates/estimate_preview_template', $data);
        } else {
            return $this->load->view('admin/estimates/estimate_preview_template', $data, true);
        }
    }

    public function get_estimates_total()
    {
        if ($this->input->post()) {
            $data['totals'] = $this->estimates_model->get_estimates_total($this->input->post());

            $this->load->model('currencies_model');

            if (!$this->input->post('customer_id')) {
                $multiple_currencies = call_user_func('is_using_multiple_currencies', db_prefix() . 'estimates');
            } else {
                $multiple_currencies = call_user_func('is_client_using_multiple_currencies', $this->input->post('customer_id'), db_prefix() . 'estimates');
            }

            if ($multiple_currencies) {
                $data['currencies'] = $this->currencies_model->get();
            }

            $data['estimates_years'] = $this->estimates_model->get_estimates_years();

            if (
                count($data['estimates_years']) >= 1
                && !\app\services\utilities\Arr::inMultidimensional($data['estimates_years'], 'year', date('Y'))
            ) {
                array_unshift($data['estimates_years'], ['year' => date('Y')]);
            }

            $data['_currency'] = $data['totals']['currencyid'];
            unset($data['totals']['currencyid']);
            $this->load->view('admin/estimates/estimates_total_template', $data);
        }
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_estimate($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'estimate', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_estimate($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'estimate');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function mark_action_status($status, $id)
    {
        if (staff_cant('edit', 'estimates')) {
            access_denied('estimates');
        }
        $success = $this->estimates_model->mark_action_status($status, $id);
        if ($success) {
            set_alert('success', _l('estimate_status_changed_success'));
        } else {
            set_alert('danger', _l('estimate_status_changed_fail'));
        }
        if ($this->set_estimate_pipeline_autoload($id)) {
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('estimates/list_estimates/' . $id));
        }
    }

    public function send_expiry_reminder($id)
    {
        $canView = user_can_view_estimate($id);
        if (!$canView) {
            access_denied('Estimates');
        } else {
            if (staff_cant('view', 'estimates') && staff_cant('view_own', 'estimates') && $canView == false) {
                access_denied('Estimates');
            }
        }

        $success = $this->estimates_model->send_expiry_reminder($id);
        if ($success) {
            set_alert('success', _l('sent_expiry_reminder_success'));
        } else {
            set_alert('danger', _l('sent_expiry_reminder_fail'));
        }
        if ($this->set_estimate_pipeline_autoload($id)) {
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('estimates/list_estimates/' . $id));
        }
    }

    /* Send estimate to email */
    public function send_to_email($id)
    {
        $canView = user_can_view_estimate($id);
        if (!$canView) {
            access_denied('estimates');
        } else {
            if (staff_cant('view', 'estimates') && staff_cant('view_own', 'estimates') && $canView == false) {
                access_denied('estimates');
            }
        }

        try {
            $success = $this->estimates_model->send_estimate_to_client($id, '', $this->input->post('attach_pdf'), $this->input->post('cc'));
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('estimate_sent_to_client_success'));
        } else {
            set_alert('danger', _l('estimate_sent_to_client_fail'));
        }
        if ($this->set_estimate_pipeline_autoload($id)) {
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('estimates/list_estimates/' . $id));
        }
    }

    /* Convert estimate to invoice */
    public function convert_to_invoice($id)
    {
        if (staff_cant('create', 'invoices')) {
            access_denied('invoices');
        }
        if (!$id) {
            die('No estimate found');
        }
        $draft_invoice = false;
        if ($this->input->get('save_as_draft')) {
            $draft_invoice = true;
        }
        $invoiceid = $this->estimates_model->convert_to_invoice($id, false, $draft_invoice);
        if ($invoiceid) {
            set_alert('success', _l('estimate_convert_to_invoice_successfully'));
            redirect(admin_url('invoices/list_invoices/' . $invoiceid));
        } else {
            if ($this->session->has_userdata('estimate_pipeline') && $this->session->userdata('estimate_pipeline') == 'true') {
                $this->session->set_flashdata('estimateid', $id);
            }
            if ($this->set_estimate_pipeline_autoload($id)) {
                redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('estimates/list_estimates/' . $id));
            }
        }
    }

    public function copy($id)
    {
        if (staff_cant('create', 'estimates')) {
            access_denied('estimates');
        }
        if (!$id) {
            die('No estimate found');
        }
        $new_id = $this->estimates_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('estimate_copied_successfully'));
            if ($this->set_estimate_pipeline_autoload($new_id)) {
                redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('estimates/estimate/' . $new_id));
            }
        }
        set_alert('danger', _l('estimate_copied_fail'));
        if ($this->set_estimate_pipeline_autoload($id)) {
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('estimates/estimate/' . $id));
        }
    }

    /* Delete estimate */
    public function delete($id)
    {
        if (staff_cant('delete', 'estimates')) {
            access_denied('estimates');
        }
        if (!$id) {
            redirect(admin_url('estimates/list_estimates'));
        }
        $success = $this->estimates_model->delete($id);
        if (is_array($success)) {
            set_alert('warning', _l('is_invoiced_estimate_delete_error'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('estimate')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('estimate_lowercase')));
        }
        redirect(admin_url('estimates/list_estimates'));
    }

    public function clear_acceptance_info($id)
    {
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'estimates', get_acceptance_info_array(true));
        }

        redirect(admin_url('estimates/list_estimates/' . $id));
    }

    /* Generates estimate PDF and senting to email  */
    public function pdf($id)
    {
        $canView = user_can_view_estimate($id);
        if (!$canView) {
            access_denied('Estimates');
        } else {
            if (staff_cant('view', 'estimates') && staff_cant('view_own', 'estimates') && $canView == false) {
                access_denied('Estimates');
            }
        }
        if (!$id) {
            redirect(admin_url('estimates/list_estimates'));
        }
        $estimate        = $this->estimates_model->get($id);
        $estimate_number = format_estimate_number($estimate->id);

        try {
            $pdf = estimate_pdf($estimate);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $fileNameHookData = hooks()->apply_filters('estimate_file_name_admin_area', [
                            'file_name' => mb_strtoupper(slug_it($estimate_number)) . '.pdf',
                            'estimate'  => $estimate,
                        ]);

        $pdf->Output($fileNameHookData['file_name'], $type);
    }

    // Pipeline
    public function get_pipeline()
    {
        if (staff_can('view',  'estimates') || staff_can('view_own',  'estimates') || get_option('allow_staff_view_estimates_assigned') == '1') {
            $data['estimate_statuses'] = $this->estimates_model->get_statuses();
            $this->load->view('admin/estimates/pipeline/pipeline', $data);
        }
    }

    public function pipeline_open($id)
    {
        $canView = user_can_view_estimate($id);
        if (!$canView) {
            access_denied('Estimates');
        } else {
            if (staff_cant('view', 'estimates') && staff_cant('view_own', 'estimates') && $canView == false) {
                access_denied('Estimates');
            }
        }

        $data['id']       = $id;
        $data['estimate'] = $this->get_estimate_data_ajax($id, true);
        $this->load->view('admin/estimates/pipeline/estimate', $data);
    }

    public function update_pipeline()
    {
        if (staff_can('edit',  'estimates')) {
            $this->estimates_model->update_pipeline($this->input->post());
        }
    }

    public function pipeline($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
            'estimate_pipeline' => $set,
        ]);
        if ($manual == false) {
            redirect(admin_url('estimates/list_estimates'));
        }
    }

    public function pipeline_load_more()
    {
        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $estimates = (new EstimatesPipeline($status))
            ->search($this->input->get('search'))
            ->sortBy(
                $this->input->get('sort_by'),
                $this->input->get('sort')
            )
            ->page($page)->get();

        foreach ($estimates as $estimate) {
            $this->load->view('admin/estimates/pipeline/_kanban_card', [
                'estimate' => $estimate,
                'status'   => $status,
            ]);
        }
    }

    public function set_estimate_pipeline_autoload($id)
    {
        if ($id == '') {
            return false;
        }

        if ($this->session->has_userdata('estimate_pipeline')
                && $this->session->userdata('estimate_pipeline') == 'true') {
            $this->session->set_flashdata('estimateid', $id);

            return true;
        }

        return false;
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date    = $this->input->post('date');
            $duedate = '';
            if (get_option('estimate_due_after') != 0) {
                $date    = to_sql_date($date);
                $d       = date('Y-m-d', strtotime('+' . get_option('estimate_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }

    public function update_area_statement_tabs()
    {
        $area_id = null;
        if ($this->input->post()) {
            $data = $this->input->post();
            $area_id = $this->estimates_model->update_area_statement_tabs($data);
        }
        echo json_encode(['group_id' => $area_id]);
    }

    public function add_area_statement_tabs() 
    {
        $area_id = null;
        if ($this->input->post()) {
            $data = $this->input->post();
            $area_id = $this->estimates_model->add_area_statement_tabs($data);
        }
        echo json_encode(['group_id' => $area_id]);
    }

    public function delete_area_statement_tabs() 
    {
        $area_id = null;
        if ($this->input->post()) {
            $data = $this->input->post();
            $area_id = $this->estimates_model->delete_area_statement_tabs($data);
        }
        echo json_encode(['group_id' => $area_id]);
    }

    public function get_estimate_purchase_items()
    {
        $name    = $this->input->post('name');
        $value = $this->input->post('value');
        echo pur_get_item_selcted_select($name, $value);
    }

    public function create_new_revision()
    {
        $data = $this->input->post();
        $new_revision_id = $this->estimates_model->create_new_revision($data);
        echo json_encode(['new_revision_id' => $new_revision_id]);
        exit;
    }

    public function assign_unawarded_capex()
    { 
        $data = $this->input->post();
        $response = $this->estimates_model->assign_unawarded_capex($data);
        echo json_encode($response);
    }

    public function add_assign_unawarded_capex()
    {
        $data = $this->input->post();
        $estimate_id = isset($data['estimate_id']) ? $data['estimate_id'] : 0;
        $this->estimates_model->add_assign_unawarded_capex($data);
        set_alert('success', 'Assign Unawarded Capex is updated successfully');
        redirect($_SERVER['HTTP_REFERER'].'#'.$estimate_id);
    }

    public function view_package()
    {
        $data = $this->input->post();
        $response = $this->estimates_model->view_package($data);
        echo json_encode($response);
    }

    public function save_package()
    {
        $data = $this->input->post();
        $package_id = isset($data['package_id']) ? $data['package_id'] : NULL;
        $estimate_id = isset($data['estimate_id']) ? $data['estimate_id'] : NULL;
        $this->estimates_model->save_package($data);
        if(!empty($package_id)) {
            set_alert('success', 'Package is updated successfully');
        } else {
            set_alert('success', 'Package is added successfully');
        }
        redirect($_SERVER['HTTP_REFERER'].'#'.$estimate_id);
    }

    public function delete_package($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/unawarded_tracker'));
        }
        $used_package = $this->estimates_model->check_used_package($id);
        if(!empty($used_package)) {
            set_alert('warning', 'This package has already been used in the tender strategy.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $response = $this->estimates_model->delete_package($id);
        if ($response == true) {
            set_alert('success', 'Package is deleted successfully');
        } else {
            set_alert('warning', 'Something went wrong');
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function update_lock_budget()
    {
        $data = $this->input->post();
        $id = $this->estimates_model->update_lock_budget($data);
        echo json_encode(['id' => $id]);
        exit;
    }

    public function cost_control_sheet()
    {
        $data = $this->input->post();
        $response = $this->estimates_model->cost_control_sheet($data);
        echo json_encode($response);
    }

    public function get_area_dropdown()
    {
        $name    = $this->input->post('name');
        $value = $this->input->post('value');
        echo get_area_list($name, $value);
    }

    public function get_package_budget_head_dropdown()
    {
        $estimateid = $this->input->post('estimateid');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        echo get_package_budget_head_dropdown($estimateid, $name, $value);
    }

    public function get_package_kind_dropdown()
    {
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        echo get_package_kind_dropdown($name, $value);
    }

    public function get_package_rli_filter_dropdown()
    {
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        echo get_package_rli_filter_dropdown($name, $value);
    }

    public function add_bulk_package()
    {
        $data = $this->input->post();
        $estimate_id = isset($data['bulk_estimate_id']) ? $data['bulk_estimate_id'] : NULL;
        $this->estimates_model->add_bulk_package($data);
        set_alert('success', 'Package is added successfully');
        redirect($_SERVER['HTTP_REFERER'].'#'.$estimate_id);
    }

    public function table_estimate_items($estimate_id = '', $budget_head_id = '')
    {
        $output = $this->estimates_model->table_estimate_items($estimate_id, $budget_head_id);
        echo json_encode($output);
        die();
    }

    public function table_unawarded_capex_items($estimate_id = '')
    {
        $output = $this->estimates_model->table_unawarded_capex_items($estimate_id);
        echo json_encode($output);
        die();
    }

    public function milestone($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $id = $this->estimates_model->add_milestone($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('project_milestone')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->estimates_model->update_milestone($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('project_milestone')));
                }
            }
        }
        redirect(admin_url('estimates/estimate/' . $this->input->post('estimate_id')));
    }

    public function delete_milestone($estimate_id, $id)
    {
        if ($this->estimates_model->delete_milestone($id)) {
            set_alert('deleted', 'project_milestone');
        }
        redirect(admin_url('estimates/estimate/' . $estimate_id));
    }

    public function milestones_kanban()
    {
        $data['milestones_exclude_completed_tasks'] = $this->input->get('exclude_completed_tasks') && $this->input->get('exclude_completed_tasks') == 'yes';
        $data['estimate_id'] = $this->input->get('estimate_id');
        $data['milestones'] = [];
        $_milestones = $this->estimates_model->get_milestones($data['estimate_id']);
        foreach ($_milestones as $m) {
            $data['milestones'][] = $m;
        }
        echo $this->load->view('admin/estimates/milestones_kan_ban', $data, true);
    }

    public function update_milestones_order()
    {
        if ($post_data = $this->input->post()) {
            $this->estimates_model->update_milestones_order($post_data);
        }
    }

    public function update_task_milestone()
    {
        if ($this->input->post()) {
            $this->estimates_model->update_task_milestone($this->input->post());
        }
    }

    public function project_timelines($estimate_id)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('project_timelines', [
                'estimate_id' => $estimate_id,
            ]);
        }
    }

    public function change_milestone_color()
    {
        if ($this->input->post()) {
            $this->estimates_model->update_milestone_color($this->input->post());
        }
    }

    public function get_rel_estimate_data($id, $task_id = '')
    {
        if ($this->input->is_ajax_request()) {
            $selected_milestone = '';
            if ($task_id != '' && $task_id != 'undefined') {
                $task               = $this->tasks_model->get($task_id);
                $selected_milestone = $task->milestone;
            }
            echo json_encode([
                'milestones'          => render_select('milestone', $this->estimates_model->get_milestones($id), [
                    'id',
                    'name',
                ], 'task_milestone', $selected_milestone),
            ]);
        }
    }

    public function milestones_kanban_load_more()
    {
        $milestones_exclude_completed_tasks = $this->input->get('exclude_completed_tasks') && $this->input->get('exclude_completed_tasks') == 'yes';

        $status     = $this->input->get('status');
        $page       = $this->input->get('page');
        $estimate_id = $this->input->get('estimate_id');
        $where      = [];
        if ($milestones_exclude_completed_tasks) {
            $where['status !='] = Tasks_model::STATUS_COMPLETE;
        }
        $tasks = $this->estimates_model->do_milestones_kanban_query($status, $estimate_id, $page, $where);
        foreach ($tasks as $task) {
            $this->load->view('admin/estimates/_milestone_kanban_card', ['task' => $task, 'milestone' => $status]);
        }
    }

    public function table_budget_usage($estimate_id = 0)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('budget_usage', ['estimate_id' => $estimate_id]);
        }
    }

    public function update_estimate_item_package()
    {
        $data = $this->input->post();
        $this->estimates_model->add_assign_unawarded_capex($data);
        echo json_encode(['success' => true, 'message' => 'Assign Unawarded Capex is updated successfully']);
    }
}
