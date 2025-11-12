<?php

use app\services\utilities\Arr;

defined('BASEPATH') or exit('No direct script access allowed');

class Expenses extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('expenses_model');
    }

    public function index($id = '')
    {
        $this->list_expenses($id);
    }

    public function list_expenses($id = '')
    {
        close_setup_menu();

        if (staff_cant('view', 'expenses') && staff_cant('view_own', 'expenses')) {
            access_denied('expenses');
        }

        $this->load->model('payment_modes_model');
        $this->load->model('expenses_model');
        $this->load->model('purchase/purchase_model');
        $data['payment_modes'] = $this->payment_modes_model->get_payment_modes();
        $data['expenseid']     = $id;
        $data['categories']    = $this->expenses_model->get_category();
        $data['years']         = $this->expenses_model->get_expenses_years();
        $data['vendors']       = $this->purchase_model->get_vendor();
        // $data['table']         = App_table::find('expenses');
        $data['title']         = _l('expenses');
        $expenses_model = $this->expenses_model->get('', [], 'category_name', true);

        // Initialize chart data array 
        $chart_data = [];

        // Format the result into Highcharts-friendly format
        foreach ($expenses_model as $row) {
            $chart_data[] = [
                'name' => $row['category_name'],  // category name
                'y' => (float) $row['total_amount']  // count as y value
            ];
        }
        // Pass the data to the view
        $data['chart_data'] = $chart_data;
        $data['order_tagged_detail'] = $this->purchase_model->get_order_tagged_detail();

        $this->load->view('admin/expenses/manage', $data);
    }

    public function table_expenses()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('expenses_new');
        }
    }

    public function get_expenses_chart_data_type_wise()
    {
        $this->load->model('expenses_model');
        $selected_type = $_GET['type'] ?? '0';  // Default to Category Wise
        $name_field = '';
        $amount_field = 'amount';  // Field to sum up
        // Fetch data based on the selected type
        if ($selected_type == '0') {
            // Category Wise data
            $expenses_model = $this->expenses_model->get('', [], 'category_name', true);
            $name_field = 'category_name';
        } elseif ($selected_type == '1') {
            // Payment Wise data
            $expenses_model = $this->expenses_model->get('', [], 'paymentmode', true);
            $name_field = 'payment_mode_name';
        } elseif ($selected_type == '2') {
            // Project Wise data
            $expenses_model = $this->expenses_model->get('', [], 'project_id', true);
            $name_field = 'project_name';
        }

        // Prepare chart data
        $chart_data = [];
        foreach ($expenses_model as $row) {
            $chart_data[] = [
                'name' => $row[$name_field],   // The name could be category, payment method, or project
                'y' => (float) $row['total_amount']  // The count corresponds to the selected type
            ];
        }

        // Return the chart data as JSON for the AJAX request
        header('Content-Type: application/json');
        echo json_encode($chart_data);
    }

    public function table($clientid = '')
    {
        if (staff_cant('view', 'expenses') && staff_cant('view_own', 'expenses')) {
            ajax_access_denied();
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

        App_table::find('expenses')->output([
            'clientid' => $clientid,
            'data'     => $data,
        ]);
    }

    public function expense($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (staff_cant('create', 'expenses')) {
                    set_alert('danger', _l('access_denied'));
                    echo json_encode([
                        'url' => admin_url('expenses/expense'),
                    ]);
                    die;
                }
                $id = $this->expenses_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('expense')));
                    echo json_encode([
                        'url'       => admin_url('expenses/list_expenses/' . $id),
                        'expenseid' => $id,
                    ]);
                    die;
                }
                echo json_encode([
                    'url' => admin_url('expenses/expense'),
                ]);
                die;
            }
            if (staff_cant('edit', 'expenses')) {
                set_alert('danger', _l('access_denied'));
                echo json_encode([
                    'url' => admin_url('expenses/expense/' . $id),
                ]);
                die;
            }
            $success = $this->expenses_model->update($this->input->post(), $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('expense')));
            }
            echo json_encode([
                'url'       => admin_url('expenses/list_expenses/' . $id),
                'expenseid' => $id,
            ]);
            die;
        }
        if ($id == '') {
            $title = _l('add_new', _l('expense'));
        } else {
            $data['expense'] = $this->expenses_model->get($id);
            $data['attachments'] = $this->expenses_model->get_all_expense_files($id);

            if (!$data['expense'] || (staff_cant('view', 'expenses') && $data['expense']->addedfrom != get_staff_user_id())) {
                blank_page(_l('expense_not_found'));
            }

            $title = _l('edit', _l('expense'));
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $this->load->model('taxes_model');
        $this->load->model('payment_modes_model');
        $this->load->model('currencies_model');

        $data['taxes']         = $this->taxes_model->get();
        $data['categories']    = $this->expenses_model->get_category();
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'invoices_only !=' => 1,
        ]);
        $data['bodyclass']  = 'expense';
        $data['currencies'] = $this->currencies_model->get();
        $data['projects']    = $this->projects_model->get_items();
        $data['title']      = $title;
        $this->load->view('admin/expenses/expense', $data);
    }

    public function import()
    {
        if (staff_cant('create', 'expenses')) {
            access_denied('Items Import');
        }

        $this->load->library('import/import_expenses', [], 'import');

        $this->import->setDatabaseFields($this->db->list_fields(db_prefix() . 'expenses'))
            ->setCustomFields(get_custom_fields('expenses'));

        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }

        if (
            $this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''
        ) {
            $this->import->setSimulation($this->input->post('simulate'))
                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                ->setFilename($_FILES['file_csv']['name'])
                ->perform();

            $data['total_rows_post'] = $this->import->totalRows();

            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }

        $data['title'] = _l('import');
        $this->load->view('admin/expenses/import', $data);
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_expenses');
        $total_deleted = 0;
        $total_updated = 0;

        if ($this->input->post()) {
            $ids         = $this->input->post('ids');
            $amount      = $this->input->post('amount');
            $date        = $this->input->post('date');
            $category    = $this->input->post('category');
            $paymentmode = $this->input->post('paymentmode');

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if (staff_can('delete', 'expenses')) {
                            if ($this->expenses_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    } else {
                        if (staff_can('edit', 'expenses')) {
                            $this->db->where('id', $id);
                            $this->db->update('expenses', array_filter([
                                'paymentmode' => $paymentmode ?: null,
                                'category'    => $category ?: null,
                                'date'        => $date ? to_sql_date($date) : null,
                                'amount'      => $amount ?: null,
                            ]));

                            if ($this->db->affected_rows() > 0) {
                                $total_updated++;
                            }
                        }
                    }
                }
            }

            if ($total_updated > 0) {
                set_alert('success', _l('updated_successfully', _l('expenses')));
            } elseif ($this->input->post('mass_delete')) {
                set_alert('success', _l('total_expenses_deleted', $total_deleted));
            }
        }
    }

    public function get_expenses_total()
    {
        if ($this->input->post()) {
            $data['totals'] = $this->expenses_model->get_expenses_total($this->input->post());

            if ($data['totals']['currency_switcher'] == true) {
                $this->load->model('currencies_model');
                $data['currencies'] = $this->currencies_model->get();
            }

            $data['expenses_years'] = $this->expenses_model->get_expenses_years();

            if (count($data['expenses_years']) >= 1 && $data['expenses_years'][0]['year'] != date('Y')) {
                array_unshift($data['expenses_years'], ['year' => date('Y')]);
            }

            $data['expenses_years'] = Arr::uniqueByKey($data['expenses_years'], 'year');

            $data['_currency'] = $data['totals']['currencyid'];
            $this->load->view('admin/expenses/expenses_total_template', $data);
        }
    }

    // Not used at this time
    public function pdf($id)
    {
        $expense = $this->expenses_model->get($id);

        if (staff_cant('view', 'expenses') && $expense->addedfrom != get_staff_user_id()) {
            access_denied();
        }

        $pdf = app_pdf('expense', LIBSPATH . 'pdf/Expense_pdf', $expense);
        // Output PDF to user
        $pdf->output('#' . slug_it($expense->category_name) . '_' . _d($expense->date) . '.pdf', 'I');
    }

    public function delete($id)
    {
        if (staff_cant('delete', 'expenses')) {
            access_denied('expenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/list_expenses'));
        }
        $response = $this->expenses_model->delete($id);
        if ($response === true) {
            set_alert('success', _l('deleted', _l('expense')));
        } else {
            if (is_array($response) && $response['invoiced'] == true) {
                set_alert('warning', _l('expense_invoice_delete_not_allowed'));
            } else {
                set_alert('warning', _l('problem_deleting', _l('expense_lowercase')));
            }
        }

        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }

    public function copy($id)
    {
        if (staff_cant('create', 'expenses')) {
            access_denied('expenses');
        }
        $new_expense_id = $this->expenses_model->copy($id);
        if ($new_expense_id) {
            set_alert('success', _l('expense_copy_success'));
            redirect(admin_url('expenses/expense/' . $new_expense_id));
        } else {
            set_alert('warning', _l('expense_copy_fail'));
        }
        redirect(admin_url('expenses/list_expenses/' . $id));
    }

    public function convert_to_invoice($id)
    {
        if (staff_cant('create', 'invoices')) {
            access_denied('Convert Expense to Invoice');
        }
        if (!$id) {
            redirect(admin_url('expenses/list_expenses'));
        }
        $draft_invoice = false;
        if ($this->input->get('save_as_draft')) {
            $draft_invoice = true;
        }

        $params = [];
        if ($this->input->get('include_note') == 'true') {
            $params['include_note'] = true;
        }

        if ($this->input->get('include_name') == 'true') {
            $params['include_name'] = true;
        }

        $invoiceid = $this->expenses_model->convert_to_invoice($id, $draft_invoice, $params);
        if ($invoiceid) {
            set_alert('success', _l('expense_converted_to_invoice'));
            redirect(admin_url('invoices/invoice/' . $invoiceid));
        } else {
            set_alert('warning', _l('expense_converted_to_invoice_fail'));
        }
        redirect(admin_url('expenses/list_expenses/' . $id));
    }

    public function get_expense_data_ajax($id)
    {
        if (staff_cant('view', 'expenses') && staff_cant('view_own', 'expenses')) {
            echo _l('access_denied');
            die;
        }
        $expense = $this->expenses_model->get($id);

        if (!$expense || (staff_cant('view', 'expenses') && $expense->addedfrom != get_staff_user_id())) {
            echo _l('expense_not_found');
            die;
        }

        $data['expense'] = $expense;
        $data['attachments'] = $this->expenses_model->get_all_expense_files($id);
        if ($expense->billable == 1) {
            if ($expense->invoiceid !== null) {
                $this->load->model('invoices_model');
                $data['invoice'] = $this->invoices_model->get($expense->invoiceid);
            }
        }

        $data['child_expenses'] = $this->expenses_model->get_child_expenses($id);
        $data['members']        = $this->staff_model->get('', ['active' => 1]);
        $this->load->view('admin/expenses/expense_preview_template', $data);
    }

    public function get_customer_change_data($customer_id = '')
    {
        echo json_encode([
            'customer_has_projects' => customer_has_projects($customer_id),
            'client_currency'       => $this->clients_model->get_customer_default_currency($customer_id),
        ]);
    }

    public function categories()
    {
        if (!is_admin()) {
            access_denied('expenses');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('expenses_categories');
        }
        $data['title'] = _l('expense_categories');
        $this->load->view('admin/expenses/manage_categories', $data);
    }

    public function category()
    {
        if (!is_admin() && get_option('staff_members_create_inline_expense_categories') == '0') {
            access_denied('expenses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->expenses_model->add_category($this->input->post());
                echo json_encode([
                    'success' => $id ? true : false,
                    'message' => $id ? _l('added_successfully', _l('expense_category')) : '',
                    'id'      => $id,
                    'name'    => $this->input->post('name'),
                ]);
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->expenses_model->update_category($data, $id);
                $message = _l('updated_successfully', _l('expense_category'));
                echo json_encode(['success' => $success, 'message' => $message]);
            }
        }
    }

    public function delete_category($id)
    {
        if (!is_admin()) {
            access_denied('expenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/categories'));
        }
        $response = $this->expenses_model->delete_category($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('expense_category_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('expense_category')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expense_category_lowercase')));
        }
        redirect(admin_url('expenses/categories'));
    }

    public function add_expense_attachment($id)
    {
        handle_expense_attachments($id);
        echo json_encode([
            'url' => admin_url('expenses/list_expenses/' . $id),
        ]);
    }

    public function delete_expense_attachment($id, $preview = '')
    {
        $this->db->where('id', $id);
        $this->db->where('rel_type', 'expense');
        $file = $this->db->get(db_prefix() . 'files')->row();

        if ($file->staffid == get_staff_user_id() || is_admin()) {
            $success = $this->expenses_model->delete_expense_attachment($file->rel_id, $id);
            if ($success) {
                set_alert('success', _l('deleted', _l('expense_receipt')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('expense_receipt_lowercase')));
            }
            if ($preview == '') {
                redirect(admin_url('expenses/expense/' . $file->rel_id));
            } else {
                redirect(admin_url('expenses/list_expenses/' . $file->rel_id));
            }
        } else {
            access_denied('expenses');
        }
    }

    public function applied_to_invoice()
    {
        $response = array();
        $data = $this->input->post();
        $invoiceid = $this->expenses_model->applied_to_invoice($data);
        if ($invoiceid) {
            $response['status'] = true;
            $response['message'] = _l('expense_applied_to_invoice');
            $response['url'] = admin_url('invoices/invoice/' . $invoiceid);
        } else {
            $response['status'] = false;
            $response['message'] = _l('expense_converted_to_invoice_fail');
            $response['url'] = admin_url('expenses/list_expenses/' . $data['expense_id']);
        }
        echo json_encode($response);
    }

    public function view_expense_file($id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->expenses_model->get_expense_file($id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('admin/expenses/preview_file', $data);
    }

    public function get_expenses_dashboard()
    {
        $data = $this->input->post();
        $result = $this->expenses_model->get_expenses_dashboard($data);
        echo json_encode($result);
        die;
    }

    public function convert_pur_invoice_from_expense($id, $rtype = 0, $expense_data = [])
    {
        if (!$id) {
            redirect(admin_url('expenses'));
        }

        $expense = $this->expenses_model->get($id);
        if (empty($expense)) {
            redirect(admin_url('expenses'));
        }

        $expense_vbt = $this->expenses_model->get_expense_with_vbt($id);
        if (!empty($expense_vbt)) {
            set_alert('warning', 'This expense has already been converted to a vendor bill.');
            redirect(admin_url('expenses'));
        }

        $input = array();
        $prefix = get_purchase_option('pur_inv_prefix');
        $next_number = get_purchase_option('next_inv_number');
        $invoice_number = $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);

        // Use data from $expense_data if available, otherwise fallback to $expense object
        $category = !empty($expense_data['category']) ? $expense_data['category'] : $expense->category;
        $expense_name = !empty($expense_data['expense_name']) ? $expense_data['expense_name'] : $expense->expense_name;
        $amount = !empty($expense_data['amount']) ? $expense_data['amount'] : $expense->amount;
        $expense_date = (!empty($expense_data['date']) && $expense_data['date'] != '0000-00-00')
        ? $expense_data['date'] : date('Y-m-d');
        $project_id = !empty($expense_data['project_id']) ? $expense_data['project_id'] : $expense->project_id;
        $pur_order = NULL;
        $wo_order = NULL;
        $order_tracker_id = NULL;
        if($expense_data['choose_from_order'] == "1") {
            $pur_order = $expense_data['order_list'];
        }
        if($expense_data['choose_from_order'] == "2") {
            $wo_order = $expense_data['order_list'];
        }
        if($expense_data['choose_from_order'] == "3") {
            $order_tracker_id = $expense_data['order_list'];
        }

        $group_pur = $this->expenses_model->find_budget_head_value($category);
        $vendor_submitted_amount_without_tax = !empty($amount) ? $amount : 0;
        $taxrate1 = ($amount * $expense->taxrate) / 100;
        $taxrate2 = ($amount * $expense->taxrate2) / 100;
        $vendor_submitted_tax_amount = $taxrate1 + $taxrate2;
        $vendor_submitted_amount = $vendor_submitted_amount_without_tax + $vendor_submitted_tax_amount;

        $input['invoice_number'] = $invoice_number;
        $input['vendor_invoice_number'] = NULL;
        $input['vendor'] = !empty($expense->vendor) ? $expense->vendor : 0;
        $input['group_pur'] = !empty($group_pur) ? $group_pur : 0;
        $input['description_services'] = !empty($expense_name) ? $expense_name : '';
        $input['invoice_date'] = $expense_date;
        $input['currency'] = 3;
        $input['to_currency'] = 3;
        $input['date_add'] = date('Y-m-d');
        $input['payment_status'] = 0;
        $input['project_id'] = !empty($project_id) ? $project_id : 1;
        $input['vendor_submitted_amount_without_tax'] = $vendor_submitted_amount_without_tax;
        $input['vendor_submitted_tax_amount'] = $vendor_submitted_tax_amount;
        $input['vendor_submitted_amount'] = $vendor_submitted_amount;
        $input['final_certified_amount'] = $vendor_submitted_amount;
        $input['expense_id'] = $id;
        $input['add_from'] = get_staff_user_id();
        $input['pur_order'] = $pur_order;
        $input['wo_order'] = $wo_order;
        $input['order_tracker_id'] = $order_tracker_id;

        $this->db->insert(db_prefix() . 'pur_invoices', $input);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $this->db->where('option_name', 'next_inv_number');
            $this->db->update(db_prefix() . 'purchase_option', ['option_val' =>  $next_number + 1]);
            update_pur_invoices_last_action($insert_id);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'expenses', ['vbt_id' => $insert_id]);
        $this->expenses_model->copy_expense_files_to_vbt($id, $insert_id);
        $this->expenses_model->add_expense_to_vbt_activity_log($id);
        add_vbt_activity_log($insert_id, ' by expense');

        if ($rtype == 0) {
            set_alert('success', _l('purchase_invoice') . ' ' . _l('added_successfully'));
            redirect(admin_url('purchase/pur_invoice/' . $insert_id));
        } else {
            return true;
        }
    }

    public function bulk_convert_expenses_to_vbt()
    {
        $response = array();
        $data = $this->input->post();
        unset(
            $data['bulk_active_tab'],
            $data['convert_expense_name'],
            $data['convert_category'],
            $data['convert_date']
        );

        $expense_vbt = $this->expenses_model->check_convert_expenses_to_vbt($data['ids']);

        if (empty($expense_vbt)) {
            $response['success'] = false;
            $response['message'] = 'All the selected expenses have already been converted into vendor bills.';
            set_alert('warning', 'All the selected expenses have already been converted into vendor bills.');
            redirect(admin_url('expenses'));
        } else {
            // Create a mapping of expense IDs to their data from the newitems array
            $expense_data_map = [];
            foreach ($data['newitems'] as $item) {
                // Assuming the IDs are in the same order as newitems
                // You might need to adjust this logic based on your actual data structure
                $expense_data_map[] = $item;
            }

            $expense_ids = explode(',', $data['ids']);

            foreach ($expense_vbt as $key => $value) {
                // Find the corresponding data for this expense ID
                $expense_index = array_search($value['id'], $expense_ids);
                if ($expense_index !== false && isset($expense_data_map[$expense_index])) {
                    $this->convert_pur_invoice_from_expense($value['id'], 1, $expense_data_map[$expense_index]);
                } else {
                    // Fallback to original behavior if data not found
                    $this->convert_pur_invoice_from_expense($value['id'], 1, []);
                }
            }

            $response['success'] = true;
            $response['message'] = 'All the selected expenses are now converted into vendor bills.';
            set_alert('success', 'All the selected expenses are now converted into vendor bills.');
            redirect(admin_url('expenses'));
        }
    }

    public function bulk_convert_expense_to_vbt()
    {
        $response = array();
        $data = $this->input->post();
        $bulk_html = $this->expenses_model->bulk_convert_expense_to_vbt($data);
        echo json_encode(['success' => true, 'bulk_html' => $bulk_html]);
    }

    public function get_order_options()
    {
        $type = $this->input->post('type');
        $options = array();

        switch ($type) {
            case '1': // pur_order
                $this->db->select('id, concat(pur_order_number, " ", pur_order_name) as name');
                $this->db->from('tblpur_orders');
                $orders = $this->db->get()->result_array();
                break;

            case '2': // wo_order
                $this->db->select('id, concat(wo_order_number, " ", wo_order_name) as name');
                $this->db->from('tblwo_orders');
                $orders = $this->db->get()->result_array();
                break;

            case '3': // order_tracker
                $this->db->select('id, pur_order_name as name');
                $this->db->from('tblpur_order_tracker');
                $orders = $this->db->get()->result_array();
                break;

            default:
                $orders = array();
                break;
        }

        echo json_encode(array(
            'success' => true,
            'options' => $orders
        ));
    }
}
