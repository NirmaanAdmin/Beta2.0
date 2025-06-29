<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoices extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
        $this->load->model('credit_notes_model');
    }

    /* Get all invoices in case user go on index page */
    public function index($id = '')
    {
        $this->list_invoices($id);
    }

    /* List all invoices datatables */
    public function list_invoices($id = '')
    {
        if (staff_cant('view', 'invoices')
            && staff_cant('view_own', 'invoices')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('invoices');
        }

        close_setup_menu();

        $this->load->model('payment_modes_model');
        $data['payment_modes']        = $this->payment_modes_model->get('', [], true);
        $data['invoiceid']            = $id;
        $data['title']                = _l('invoices');
        $data['invoices_years']       = $this->invoices_model->get_invoices_years();
        $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
        $data['invoices_statuses']    = $this->invoices_model->get_statuses();
        $data['invoices_table'] = App_table::find('invoices');
        $data['bodyclass']            = 'invoices-total-manual';
        $this->load->view('admin/invoices/manage', $data);
    }

    /* List all recurring invoices */
    public function recurring($id = '')
    {
        if (staff_cant('view', 'invoices')
            && staff_cant('view_own', 'invoices')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('invoices');
        }

        close_setup_menu();

        $data['invoiceid']            = $id;
        $data['title']                = _l('invoices_list_recurring');
        $data['invoices_years']       = $this->invoices_model->get_invoices_years();
        $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
        $this->load->view('admin/invoices/recurring/list', $data);
    }

    public function table($clientid = '')
    {
        if (staff_cant('view', 'invoices')
            && staff_cant('view_own', 'invoices')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            ajax_access_denied();
        }
        
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

        if($this->input->get('recurring')) {
            $this->app->get_table_data('recurring_invoices', [
                'data'     => $data,
            ]);
        } else {
            App_table::find('invoices')->output([
                'clientid' => $clientid,
                'data'     => $data,
            ]);
        }
    }

    public function client_change_data($customer_id, $current_invoice = '')
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('projects_model');
            $data                     = [];
            $data['billing_shipping'] = $this->clients_model->get_customer_billing_and_shipping_details($customer_id);
            $data['client_currency']  = $this->clients_model->get_customer_default_currency($customer_id);

            $data['customer_has_projects'] = customer_has_projects($customer_id);
            $data['billable_tasks']        = $this->tasks_model->get_billable_tasks($customer_id);

            if ($current_invoice != '') {
                $this->db->select('status');
                $this->db->where('id', $current_invoice);
                $current_invoice_status = $this->db->get(db_prefix() . 'invoices')->row()->status;
            }

            $_data['invoices_to_merge'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != Invoices_model::STATUS_CANCELLED) ? $this->invoices_model->check_for_merge_invoice($customer_id, $current_invoice) : [];

            $data['merge_info'] = $this->load->view('admin/invoices/merge_invoice', $_data, true);

            $this->load->model('currencies_model');

            $__data['expenses_to_bill'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != Invoices_model::STATUS_CANCELLED) ? $this->invoices_model->get_expenses_to_bill($customer_id) : [];

            $data['expenses_bill_info'] = $this->load->view('admin/invoices/bill_expenses', $__data, true);
            echo json_encode($data);
        }
    }

    public function update_number_settings($id)
    {
        $response = [
            'success' => false,
            'message' => '',
        ];
        if (staff_can('edit',  'invoices')) {
            $affected_rows = 0;

            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', [
                'prefix' => $this->input->post('prefix'),
            ]);
            if ($this->db->affected_rows() > 0) {
                $affected_rows++;
            }

            if ($affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = _l('updated_successfully', _l('invoice'));
            }
        }
        echo json_encode($response);
        die;
    }

    public function validate_invoice_number()
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

        if (total_rows('invoices', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
            'status !=' => Invoices_model::STATUS_DRAFT,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_invoice($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'invoice', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_invoice($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'invoice');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function pause_overdue_reminders($id)
    {
        if (staff_can('edit',  'invoices')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 1]);
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function resume_overdue_reminders($id)
    {
        if (staff_can('edit',  'invoices')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 0]);
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function mark_as_cancelled($id)
    {
        if (staff_cant('edit', 'invoices') && staff_cant('create', 'invoices')) {
            access_denied('invoices');
        }

        $success = $this->invoices_model->mark_as_cancelled($id);

        if ($success) {
            set_alert('success', _l('invoice_marked_as_cancelled_successfully'));
        }

        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function unmark_as_cancelled($id)
    {
        if (staff_cant('edit', 'invoices') && staff_cant('create', 'invoices')) {
            access_denied('invoices');
        }
        $success = $this->invoices_model->unmark_as_cancelled($id);
        if ($success) {
            set_alert('success', _l('invoice_unmarked_as_cancelled'));
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function copy($id)
    {
        if (!$id) {
            redirect(admin_url('invoices'));
        }
        if (staff_cant('create', 'invoices')) {
            access_denied('invoices');
        }
        $new_id = $this->invoices_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('invoice_copy_success'));
            redirect(admin_url('invoices/invoice/' . $new_id));
        } else {
            set_alert('success', _l('invoice_copy_fail'));
        }
        redirect(admin_url('invoices/invoice/' . $id));
    }

    public function get_merge_data($id)
    {
        $invoice = $this->invoices_model->get($id);
        $cf      = get_custom_fields('items');

        $i = 0;

        foreach ($invoice->items as $item) {
            $invoice->items[$i]['taxname']          = get_invoice_item_taxes($item['id']);
            $invoice->items[$i]['long_description'] = clear_textarea_breaks($item['long_description']);
            $this->db->where('item_id', $item['id']);
            $rel              = $this->db->get(db_prefix() . 'related_items')->result_array();
            $item_related_val = '';
            $rel_type         = '';
            foreach ($rel as $item_related) {
                $rel_type = $item_related['rel_type'];
                $item_related_val .= $item_related['rel_id'] . ',';
            }
            if ($item_related_val != '') {
                $item_related_val = substr($item_related_val, 0, -1);
            }
            $invoice->items[$i]['item_related_formatted_for_input'] = $item_related_val;
            $invoice->items[$i]['rel_type']                         = $rel_type;

            $invoice->items[$i]['custom_fields'] = [];

            foreach ($cf as $custom_field) {
                $custom_field['value']                 = get_custom_field_value($item['id'], $custom_field['id'], 'items');
                $invoice->items[$i]['custom_fields'][] = $custom_field;
            }
            $i++;
        }
        echo json_encode($invoice);
    }

    public function get_bill_expense_data($id)
    {
        $this->load->model('expenses_model');
        $expense = $this->expenses_model->get($id);

        $expense->qty              = 1;
        $expense->long_description = clear_textarea_breaks($expense->description);
        $expense->description      = $expense->name;
        $expense->rate             = $expense->amount;
        if ($expense->tax != 0) {
            $expense->taxname = [];
            array_push($expense->taxname, $expense->tax_name . '|' . $expense->taxrate);
        }
        if ($expense->tax2 != 0) {
            array_push($expense->taxname, $expense->tax_name2 . '|' . $expense->taxrate2);
        }
        echo json_encode($expense);
    }

    /* Add new invoice or update existing */
    public function invoice($id = '')
    {
        if ($this->input->post()) {
            $invoice_data = $this->input->post();
            if ($id == '') {
                if (staff_cant('create', 'invoices')) {
                    access_denied('invoices');
                }

                if (hooks()->apply_filters('validate_invoice_number', true)) {
                    $number = ltrim($invoice_data['number'], '0');
                    if (total_rows('invoices', [
                        'YEAR(date)' => (int) date('Y', strtotime(to_sql_date($invoice_data['date']))),
                        'number'     => $number,
                        'status !='  => Invoices_model::STATUS_DRAFT,
                    ])) {
                        set_alert('warning', _l('invoice_number_exists'));

                        redirect(admin_url('invoices/invoice'));
                    }
                }

                $id = $this->invoices_model->add($invoice_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('invoice')));
                    $redUrl = admin_url('invoices/list_invoices/' . $id);

                    if (isset($invoice_data['save_and_record_payment'])) {
                        $this->session->set_userdata('record_payment', true);
                    } elseif (isset($invoice_data['save_and_send_later'])) {
                        $this->session->set_userdata('send_later', true);
                    }

                    redirect($redUrl);
                }
            } else {
                if (staff_cant('edit', 'invoices')) {
                    access_denied('invoices');
                }

                // If number not set, is draft
                if (hooks()->apply_filters('validate_invoice_number', true) && isset($invoice_data['number'])) {
                    $number = trim(ltrim($invoice_data['number'], '0'));
                    if (total_rows('invoices', [
                        'YEAR(date)' => (int) date('Y', strtotime(to_sql_date($invoice_data['date']))),
                        'number'     => $number,
                        'status !='  => Invoices_model::STATUS_DRAFT,
                        'id !='      => $id,
                    ])) {
                        set_alert('warning', _l('invoice_number_exists'));

                        redirect(admin_url('invoices/invoice/' . $id));
                    }
                }
                $success = $this->invoices_model->update($invoice_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('invoice')));
                }

                redirect(admin_url('invoices/list_invoices/' . $id));
            }
        }
        if ($id == '') {
            $title                  = _l('create_new_invoice');
            $data['billable_tasks'] = [];
            $data['final_invoice'] = [];
        } else {
            $invoice = $this->invoices_model->get($id);

            if (!$invoice || !user_can_view_invoice($id)) {
                blank_page(_l('invoice_not_found'));
            }

            $data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $invoice->id);
            $data['expenses_to_bill']  = $this->invoices_model->get_expenses_to_bill($invoice->clientid);

            $data['invoice']        = $invoice;
            $data['edit']           = true;
            $data['billable_tasks'] = $this->tasks_model->get_billable_tasks($invoice->clientid, !empty($invoice->project_id) ? $invoice->project_id : '');
            $data['annexure_invoice'] = $this->invoices_model->get_annexure_invoice_details($id);

            $title = _l('edit', _l('invoice_lowercase')) . ' - ' . format_invoice_number($invoice->id);
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['staff']     = $this->staff_model->get('', ['active' => 1]);
        $data['commodity_groups_pur'] = $this->invoices_model->get_commodity_group_add_commodity();
        $data['get_hsn_sac_code'] = $this->invoices_model->get_hsn_sac_code();
        $data['estimates'] = $this->invoices_model->get_all_estimates();
        $data['title']     = $title;
        $data['bodyclass'] = 'invoice';
        
        $this->load->view('admin/invoices/invoice', $data);
    }

    /* Get all invoice data used when user click on invoiec number in a datatable left side*/
    public function get_invoice_data_ajax($id)
    {
        if (staff_cant('view', 'invoices')
            && staff_cant('view_own', 'invoices')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die(_l('invoice_not_found'));
        }

        $invoice = $this->invoices_model->get($id);

        if (!$invoice || !user_can_view_invoice($id)) {
            echo _l('invoice_not_found');
            die;
        }

        $template_name = 'invoice_send_to_customer';

        if ($invoice->sent == 1) {
            $template_name = 'invoice_send_to_customer_already_sent';
        }

        $data = prepare_mail_preview_data($template_name, $invoice->clientid);

        // Check for recorded payments
        $this->load->model('payments_model');
        $data['invoices_to_merge']          = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $id);
        $data['members']                    = $this->staff_model->get('', ['active' => 1]);
        $data['payments']                   = $this->payments_model->get_invoice_payments($id);
        $data['activity']                   = $this->invoices_model->get_invoice_activity($id);
        $data['totalNotes']                 = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'invoice']);
        $data['invoice_recurring_invoices'] = $this->invoices_model->get_invoice_recurring_invoices($id);

        $data['applied_credits'] = $this->credit_notes_model->get_applied_invoice_credits($id);
        // This data is used only when credit can be applied to invoice
        if (credits_can_be_applied_to_invoice($invoice->status)) {
            $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($invoice->clientid);

            if ($data['credits_available'] > 0) {
                $data['open_credits'] = $this->credit_notes_model->get_open_credits($invoice->clientid);
            }

            $customer_currency = $this->clients_model->get_customer_default_currency($invoice->clientid);
            $this->load->model('currencies_model');

            if ($customer_currency != 0) {
                $data['customer_currency'] = $this->currencies_model->get($customer_currency);
            } else {
                $data['customer_currency'] = $this->currencies_model->get_base_currency();
            }
        }

        $data['invoice'] = $invoice;

        $data['record_payment'] = false;
        $data['send_later']     = false;

        if ($this->session->has_userdata('record_payment')) {
            $data['record_payment'] = true;
            $this->session->unset_userdata('record_payment');
        } elseif ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }
        $data['annexure_invoice'] = $this->invoices_model->get_annexure_invoice_details($id, false, true);

        $this->load->view('admin/invoices/invoice_preview_template', $data);
    }

    public function apply_credits($invoice_id)
    {
        $total_credits_applied = 0;
        foreach ($this->input->post('amount') as $credit_id => $amount) {
            $success = $this->credit_notes_model->apply_credits($credit_id, [
            'invoice_id' => $invoice_id,
            'amount'     => $amount,
        ]);
            if ($success) {
                $total_credits_applied++;
            }
        }

        if ($total_credits_applied > 0) {
            update_invoice_status($invoice_id, true);
            set_alert('success', _l('invoice_credits_applied'));
        }
        redirect(admin_url('invoices/list_invoices/' . $invoice_id));
    }

    public function get_invoices_total()
    {
        if ($this->input->post()) {
            load_invoices_total_template();
        }
    }

    /* Record new inoice payment view */
    public function record_invoice_payment_ajax($id)
    {
        $this->load->model('payment_modes_model');
        $this->load->model('payments_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $data['invoice']  = $this->invoices_model->get($id);
        $data['payments'] = $this->payments_model->get_invoice_payments($id);
        $this->load->view('admin/invoices/record_payment_template', $data);
    }

    /* This is where invoice payment record $_POST data is send */
    public function record_payment()
    {
        if (staff_cant('create', 'payments')) {
            access_denied('Record Payment');
        }
        if ($this->input->post()) {
            $this->load->model('payments_model');
            $id = $this->payments_model->process_payment($this->input->post(), '');
            if ($id) {
                set_alert('success', _l('invoice_payment_recorded'));
                redirect(admin_url('payments/payment/' . $id));
            } else {
                set_alert('danger', _l('invoice_payment_record_failed'));
            }
            redirect(admin_url('invoices/list_invoices/' . $this->input->post('invoiceid')));
        }
    }

    /* Send invoice to email */
    public function send_to_email($id)
    {
        $canView = user_can_view_invoice($id);
        if (!$canView) {
            access_denied('Invoices');
        } else {
            if (staff_cant('view', 'invoices') && staff_cant('view_own', 'invoices') && $canView == false) {
                access_denied('Invoices');
            }
        }

        try {
            $statementData = [];
            if ($this->input->post('attach_statement')) {
                $statementData['attach'] = true;
                $statementData['from']   = to_sql_date($this->input->post('statement_from'));
                $statementData['to']     = to_sql_date($this->input->post('statement_to'));
            }

            $success = $this->invoices_model->send_invoice_to_client(
                $id,
                '',
                $this->input->post('attach_pdf'),
                $this->input->post('cc'),
                false,
                $statementData
            );
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
            set_alert('success', _l('invoice_sent_to_client_success'));
        } else {
            set_alert('danger', _l('invoice_sent_to_client_fail'));
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    /* Delete invoice payment*/
    public function delete_payment($id, $invoiceid)
    {
        if (staff_cant('delete', 'payments')) {
            access_denied('payments');
        }
        $this->load->model('payments_model');
        if (!$id) {
            redirect(admin_url('payments'));
        }
        $response = $this->payments_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
        }
        redirect(admin_url('invoices/list_invoices/' . $invoiceid));
    }

    /* Delete invoice */
    public function delete($id)
    {
        if (staff_cant('delete', 'invoices')) {
            access_denied('invoices');
        }
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }
        $success = $this->invoices_model->delete($id);

        if ($success) {
            set_alert('success', _l('deleted', _l('invoice')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_lowercase')));
        }
        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->invoices_model->delete_attachment($id);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Will send overdue notice to client */
    public function send_overdue_notice($id)
    {
        $canView = user_can_view_invoice($id);
        if (!$canView) {
            access_denied('Invoices');
        } else {
            if (staff_cant('view', 'invoices') && staff_cant('view_own', 'invoices') && $canView == false) {
                access_denied('Invoices');
            }
        }

        $send = $this->invoices_model->send_invoice_overdue_notice($id);
        if ($send) {
            set_alert('success', _l('invoice_overdue_reminder_sent'));
        } else {
            set_alert('warning', _l('invoice_reminder_send_problem'));
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    /* Generates invoice PDF and senting to email of $send_to_email = true is passed */
    public function pdf($id)
    {
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }

        $canView = user_can_view_invoice($id);
        if (!$canView) {
            access_denied('Invoices');
        } else {
            if (staff_cant('view', 'invoices') && staff_cant('view_own', 'invoices') && $canView == false) {
                access_denied('Invoices');
            }
        }

        $invoice        = $this->invoices_model->get($id);
        $invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);
        $invoice_number = format_invoice_number($invoice->id);
       
        try {
            $pdf = invoice_pdf($invoice);
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

        $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
    }

    public function mark_as_sent($id)
    {
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }
        if (!user_can_view_invoice($id)) {
            access_denied('Invoice Mark As Sent');
        }

        $success = $this->invoices_model->set_invoice_sent($id, true);

        if ($success) {
            set_alert('success', _l('invoice_marked_as_sent'));
        } else {
            set_alert('warning', _l('invoice_marked_as_sent_failed'));
        }

        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date    = $this->input->post('date');
            $duedate = '';
            if (get_option('invoice_due_after') != 0) {
                $date    = to_sql_date($date);
                $d       = date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }

    public function get_payment_modes_by_project($project_id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->invoices_model->get_payment_modes_by_project($project_id));
        }
    }

    public function import_invoices()
    {
        echo "Start";
        die();
        ini_set('max_execution_time', 0);
        $this->load->model('expenses_model');
        $file = fopen(FCPATH . 'uploads/Invoice_9B_Pro.csv', 'r');
        while (($line = fgetcsv($file)) !== FALSE) {
            $vbt = array();
            $prefix = get_purchase_option('pur_inv_prefix');
            $next_number = get_purchase_option('next_inv_number');
            $invoice_number = $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);
            $vendor_invoice_number = $line[0];
            if(empty($vendor_invoice_number)) {
                $vendor_invoice_number = $invoice_number;
            }
            $vendor = 0;
            if(!empty($line[1])) {
                $vendor = $this->get_vendor_id($line[1]);
            }
            $group_pur = 0;
            if(!empty($line[2])) {
                $group_pur = $this->get_budget_head_id($line[2]);
            }

            $vbt['invoice_number'] = $invoice_number;
            $vbt['vendor_invoice_number'] = $vendor_invoice_number;
            $vbt['vendor'] = $vendor;
            $vbt['group_pur'] = $group_pur;
            $vbt['description_services'] = $line[3];
            $vbt['invoice_date'] = !empty($line[5]) ? date('Y-m-d', strtotime($line[5])) : '';
            $vbt['currency'] = 3;
            $vbt['to_currency'] = 3;
            $vbt['date_add'] = date('Y-m-d');
            $vbt['payment_status'] = 5;
            $vbt['project_id'] = 1;
            $vbt['payment_date'] = date('Y-m-d');
            $vbt['duedate'] = date('Y-m-d');
            $vbt['payment_date_basilius'] = date('Y-m-d');
            $vbt['bill_accept_date'] = date('Y-m-d');
            $vbt['certified_bill_date'] = date('Y-m-d');
            $vbt['vendor_submitted_amount_without_tax'] = !empty($line[7]) ? $line[7] : 0;
            $vbt['vendor_submitted_tax_amount'] = !empty($line[8]) ? $line[8] : 0;
            $vbt['vendor_submitted_amount'] = !empty($line[9]) ? $line[9] : 0;
            $vbt['final_certified_amount'] = !empty($line[10]) ? $line[10] : 0;

            $this->db->insert(db_prefix() . 'pur_invoices', $vbt);
            $pur_invoice_id = $this->db->insert_id();
            if($pur_invoice_id) {
                $this->db->where('option_name', 'next_inv_number');
                $this->db->update(db_prefix() . 'purchase_option', ['option_val' =>  $next_number + 1]);
            }

            $expense = array();
            $category = $this->find_budget_head_value($group_pur);
            $expense['vendor'] = $vendor;
            $expense['expense_name'] = $line[3];
            $expense['note'] = '';
            $expense['clientid'] = 3;
            $expense['project_id'] = 1;
            $expense['category'] = $category;
            $expense['date'] = date('d-m-Y');
            $expense['amount'] = $vbt['final_certified_amount'];
            $expense['tax'] = NULL;
            $expense['currency'] = 3;
            $expense['billable'] = 'on';
            $expense['reference_no'] = NULL;
            $expense['paymentmode'] = NULL;
            $expense['vbt_id'] = $pur_invoice_id;

            $expense_id = $this->expenses_model->add($expense);
            $this->mark_converted_pur_invoice($pur_invoice_id, $expense_id);

            $invoice = array();
            $invoice['invoice_id'] = 21;
            $invoice['expense_id'] = $expense_id;
            $this->expenses_model->applied_to_invoice($invoice);
        }
        fclose($file);  
        echo "Success";
        die();
    }

    public function get_vendor_id($vendor) 
    {
        $this->db->like('company', $vendor);
        $result = $this->db->get(db_prefix() . 'pur_vendor')->row();
        if(!empty($result)) {
            return $result->userid;
        }
        return 0;
    }

    public function get_budget_head_id($budget_head) 
    {
        $this->db->like('name', $budget_head);
        $result = $this->db->get(db_prefix() . 'items_groups')->row();
        if(!empty($result)) {
            return $result->id;
        }
        return 0;
    }

    public function find_budget_head_value($group_pur)
    {
        $this->db->where('id', $group_pur);
        $budget_head = $this->db->get('tblitems_groups')->row();

        $this->db->select('id');
        $this->db->where('name', $budget_head->name);
        $expenses_categories = $this->db->get('tblexpenses_categories')->row();
        if (!empty($expenses_categories)) {
            return $expenses_categories->id;
        }
        return '';
    }

    public function mark_converted_pur_invoice($pur_invoice, $expense)
    {
        $this->db->where('id', $pur_invoice);
        $this->db->update(db_prefix() . 'pur_invoices', ['expense_convert' => $expense]);
        return true;
    }

    public function update_total_previous_billing_summary()
    {
        $invoiceid = $this->input->post('invoiceid');
        $annexure = $this->input->post('annexure');
        $amount = $this->input->post('amount');

        if (!$invoiceid || !$annexure || !$amount) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        $this->db->where('invoiceid', $invoiceid);
        $this->db->where('annexure', $annexure);
        $success = $this->db->update('tblinvoice_budget_summary', ['total_previous_billing' => $amount]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Total Previous Billing is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    // public function update_budgeted_amount_summary()
    // {
    //     $invoiceid = $this->input->post('invoiceid');
    //     $annexure = $this->input->post('annexure');
    //     $amount = $this->input->post('amount');

    //     if (!$invoiceid || !$annexure || !$amount) {
    //         echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
    //         return;
    //     }

    //     $this->db->where('invoiceid', $invoiceid);
    //     $this->db->where('annexure', $annexure);
    //     $success = $this->db->update('tblinvoice_budget_summary', ['budgeted_amount' => $amount]);

    //     if ($success) {
    //         echo json_encode(['success' => true, 'message' => 'Budgeted Amount is updated']);
    //     } else {
    //         echo json_encode(['success' => false, 'message' => _l('update_failed')]);
    //     }
    // }

    public function export_to_xlsx($invoiceid)
    {
        $sheets = $this->invoices_model->get_invoice_export_data($invoiceid);
        $invoice_number = format_invoice_number($invoiceid);
        $file_name = mb_strtoupper(slug_it($invoice_number));
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=".$file_name.".xls");
        header("Cache-Control: max-age=0");

        echo '<?xml version="1.0"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
        xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:x="urn:schemas-microsoft-com:office:excel"
        xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
        xmlns:html="http://www.w3.org/TR/REC-html40">';

        // Define styles
        echo '<Styles>
            <Style ss:ID="HeaderStyle">
                <Font ss:Bold="1" ss:Color="#FFFFFF"/>
                <Interior ss:Color="#323a45" ss:Pattern="Solid"/>
                <Alignment ss:Horizontal="Center"/>
            </Style>
            <Style ss:ID="DataStyle">
                <Font ss:Color="#000000"/>
            </Style>
            <Style ss:ID="PriceStyle">
                <NumberFormat ss:Format="Currency"/>
            </Style>
        </Styles>';

        foreach ($sheets as $sheetName => $htmlTable) 
        {
            echo '<Worksheet ss:Name="' . htmlspecialchars($sheetName) . '">
            <Table ss:DefaultColumnWidth="120">';

            $dom = new DOMDocument();
            @$dom->loadHTML($htmlTable);
            $rows = $dom->getElementsByTagName('tr');

            $isHeader = true;

            foreach ($rows as $row) {
                echo '<Row>';
                $cols = $row->getElementsByTagName('td');
                $headers = $row->getElementsByTagName('th');
                if ($headers->length > 0) {
                    foreach ($headers as $header) {
                        echo '<Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">' . htmlspecialchars($header->nodeValue) . '</Data></Cell>';
                    }
                } else {
                    $colIndex = 0;
                    foreach ($cols as $col) {
                        $styleID = ($colIndex == 1) ? 'PriceStyle' : 'DataStyle'; // Apply currency format to price column
                        echo '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . htmlspecialchars($col->nodeValue) . '</Data></Cell>';
                        $colIndex++;
                    }
                }
                echo '</Row>';
            }

            echo '</Table></Worksheet>';
        }
        echo '</Workbook>';
        exit;
    }

    public function get_client_invoices_dashboard()
    {
        $data = $this->input->post();
        $result = $this->invoices_model->get_client_invoices_dashboard($data);
        echo json_encode($result);
        die;
    }
}
