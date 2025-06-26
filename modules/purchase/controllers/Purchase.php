<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This class describes a purchase.
 */
class purchase extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_model');
        hooks()->do_action('purchase_init');
    }

    public function index()
    {
        if (is_staff_logged_in()) {
            redirect(admin_url('purchase/reports'));
        }

        if (is_vendor_logged_in()) {

            redirect(site_url('purchase/authentication_vendor'));
        }
    }

    /**
     * { vendors }
     */
    public function vendors()
    {

        $data['title']          = _l('vendor');
        $data['vendor_categorys'] = $this->purchase_model->get_vendor_category();
        $this->load->view('vendors/manage', $data);
    }

    /**
     * { table vendor }
     */
    public function table_vendor()
    {

        $this->app->get_table_data(module_views_path('purchase', 'vendors/table_vendor'));
    }

    /**
     * { vendor }
     *
     * @param      string  $id     The vendor
     * @return      view
     */
    public function vendor($id = '')
    {

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {


                $data = $this->input->post();

                $save_and_add_contact = false;
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                $id = $this->purchase_model->add_vendor($data);
                if (!has_permission('purchase_vendors', '', 'view')) {
                    $assign['customer_admins']   = [];
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->purchase_model->assign_vendor_admins($assign, $id);
                }
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('vendor')));
                    if ($save_and_add_contact == false) {
                        redirect(admin_url('purchase/vendor/' . $id));
                    } else {
                        redirect(admin_url('purchase/vendor/' . $id . '?group=contacts&new_contact=true'));
                    }
                }
            } else {

                if (has_permission('purchase_vendors', '', 'view_own') && !is_admin()) {
                    if (!is_vendor_admin($id, get_staff_user_id())) {
                        access_denied('purchase');
                    }
                }
                $data = $this->input->post();
                $data['rating_date'] = date('Y-m-d H:i:s');
                $data['rated_by'] = get_staff_user_id();
                unset($data['rating_id']);
                if ($data['quality_rating'] > 0 || $data['delivery_rating'] > 0 || $data['service_rating'] > 0 || $data['pricing_rating'] > 0 || $data['compliance_rating'] > 0) {
                    $success1 = $this->purchase_model->save_rating($data);
                }


                $success = $this->purchase_model->update_vendor($this->input->post(), $id);
                if ($success == true || $success1 == true) {
                    set_alert('success', _l('updated_successfully', _l('vendor')));
                }
                redirect(admin_url('purchase/vendor/' . $id));
            }
        }

        $group         = !$this->input->get('group') ? 'profile' : $this->input->get('group');
        $data['group'] = $group;

        if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {
            redirect(admin_url('purchase/vendor/' . $id . '?group=contacts&contactid=' . $contact_id));
        }




        if ($id == '') {
            $title = _l('add_new', _l('vendor_lowercase'));
            $is_edit = false;
        } else {
            if (has_permission('purchase_vendors', '', 'view_own') && !is_admin()) {
                if (!is_vendor_admin($id, get_staff_user_id())) {
                    access_denied('purchase');
                }
            }
            $is_edit = true;
            $client                = $this->purchase_model->get_vendor($id);
            $data['customer_tabs'] = get_customer_profile_tabs();

            if (!$client) {
                show_404();
            }

            $data['contacts'] = $this->purchase_model->get_contacts($id);

            $data['payments'] = $this->purchase_model->get_payment_invoices_by_vendor($id);

            $data['group'] = $this->input->get('group');

            $data['vendor_contacts'] = $this->purchase_model->get_contacts($id);

            $data['title']                 = _l('setting');
            $data['tab'][] = ['name' => 'profile', 'icon' => '<i class="fa fa-user-circle menu-icon"></i>'];
            $data['tab'][] = ['name' => 'contacts', 'icon' => '<i class="fa fa-users menu-icon"></i>'];
            $data['tab'][] = ['name' => 'quotations', 'icon' => '<i class="fa fa-file-powerpoint menu-icon"></i>'];
            // $data['tab'][] = ['name' => 'contracts', 'icon' => '<i class="fa fa-file-text menu-icon"></i>'];
            $data['tab'][] = ['name' => 'purchase_order', 'icon' => '<i class="fa fa-cart-plus menu-icon"></i>'];
            $data['tab'][] = ['name' => 'work_order', 'icon' => '<i class="fa fa-check menu-icon"></i>'];
            $data['tab'][] = ['name' => 'purchase_invoice', 'icon' => '<i class="fa fa-clipboard menu-icon"></i>'];
            $data['tab'][] = ['name' => 'debit_notes', 'icon' => '<i class="fa fa-credit-card menu-icon"></i>'];
            $data['tab'][] = ['name' => 'purchase_statement', 'icon' => '<i class="fa fa-building menu-icon"></i>'];
            $data['tab'][] = ['name' => 'payments', 'icon' => '<i class="fa fa-usd menu-icon"></i>'];
            $data['tab'][] = ['name' => 'expenses', 'icon' => '<i class="fa fa-tasks menu-icon"></i>'];
            $data['tab'][] = ['name' => 'notes', 'icon' => '<i class="fa fa-sticky-note menu-icon"></i>'];
            $data['tab'][] = ['name' => 'attachments', 'icon' => '<i class="fa fa-paperclip menu-icon"></i>'];

            if ($data['group'] == '') {
                $data['group'] = 'profile';
            }
            $data['tabs']['view'] = 'vendors/groups/' . $data['group'];
            // Fetch data based on groups
            if ($data['group'] == 'profile') {
                $data['customer_admins'] = $this->purchase_model->get_vendor_admins($id);
                $data['vendor_work_completed'] = $this->purchase_model->get_vendor_work_completed($id);
                $data['vendor_work_progress'] = $this->purchase_model->get_vendor_work_progress($id);
            } elseif ($group == 'estimates') {
                $this->load->model('estimates_model');
                $data['estimate_statuses'] = $this->estimates_model->get_statuses();
            } elseif ($group == 'notes') {

                $data['user_notes'] = $this->misc_model->get_notes($id, 'pur_vendor');
            } elseif ($group == 'payments') {
                $this->load->model('payment_modes_model');
                $data['payment_modes'] = $this->payment_modes_model->get();
            } elseif ($group == 'attachments') {
                $data['attachments'] = get_all_pur_vendor_attachments($id);
            } elseif ($group == 'expenses') {
                $this->load->model('expenses_model');
                $data['expenses'] = $this->expenses_model->get('', ['vendor' =>  $id]);
            }

            $data['staff'] = $this->staff_model->get('', ['active' => 1]);

            $data['client'] = $client;
            $title          = $client->company;

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_vendor_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }
            $data['vendor_id'] = $id;
            $data['ratings'] = $this->purchase_model->get_ratings($id);
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        if ($id != '') {
            $customer_currency = $data['client']->default_currency;

            foreach ($data['currencies'] as $currency) {
                if ($customer_currency != 0) {
                    if ($currency['id'] == $customer_currency) {
                        $customer_currency = $currency;

                        break;
                    }
                } else {
                    if ($currency['isdefault'] == 1) {
                        $customer_currency = $currency;

                        break;
                    }
                }
            }

            if (is_array($customer_currency)) {
                $customer_currency = (object) $customer_currency;
            }

            $data['customer_currency'] = $customer_currency;
        }

        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['vendor_categories'] = $this->purchase_model->get_vendor_category();
        $data['title']     = $title;
        $data['is_edit']   = $is_edit;
        $this->load->view('vendors/vendor', $data);
    }

    /**
     * { setting }
     */
    public function setting()
    {
        if (!is_admin() && !has_permission('purchase_settings', '', 'edit')) {
            access_denied('purchase');
        }
        $data['group'] = $this->input->get('group');
        $data['unit_tab'] = $this->input->get('tab');

        $data['title'] = _l('setting');

        $this->db->where('module_name', 'warehouse');
        $module = $this->db->get(db_prefix() . 'modules')->row();
        $data['tab'][] = 'purchase_order_setting';
        $data['tab'][] = 'purchase_options';
        $data['tab'][] = 'units';
        $data['tab'][] = 'approval';
        $data['tab'][] = 'commodity_group';
        $data['tab'][] = 'sub_group';
        $data['tab'][] = 'area';
        $data['tab'][] = 'vendor_category';
        $data['tab'][] = 'permissions';
        $data['tab'][] = 'order_return';
        $data['tab'][] = 'currency_rates';

        if ($data['group'] == '') {
            $data['group'] = 'purchase_order_setting';
        } else if ($data['group'] == 'units') {
            $data['unit_types'] = $this->purchase_model->get_unit_type();
        }

        if ($data['group'] == 'currency_rates') {
            $this->load->model('currencies_model');
            $this->purchase_model->check_auto_create_currency_rate();

            $data['currencies'] = $this->currencies_model->get();
            if ($data['unit_tab'] == '') {
                $data['unit_tab'] = 'general';
            }
        }


        $data['tabs']['view'] = 'includes/' . $data['group'];
        $data['commodity_group_types'] = $this->purchase_model->get_commodity_group_type();
        $data['sub_groups'] = $this->purchase_model->get_sub_group();
        $data['item_group'] = $this->purchase_model->get_item_group();
        $data['approval_setting'] = $this->purchase_model->get_approval_setting();
        $data['vendor_categories'] = $this->purchase_model->get_vendor_category();
        $data['staffs'] = $this->staff_model->get();
        $data['projects'] = $this->projects_model->get_items();
        $data['area'] = $this->purchase_model->get_area();

        $this->load->view('manage_setting', $data);
    }

    /**
     * { assign vendor admins }
     *
     * @param      string  $id     The identifier
     * @return      redirect
     */
    public function assign_vendor_admins($id)
    {
        if (!has_permission('purchase_vendors', '', 'create') && !has_permission('purchase_vendors', '', 'edit')) {
            access_denied('vendors');
        }
        $success = $this->purchase_model->assign_vendor_admins($this->input->post(), $id);
        if ($success == true) {
            set_alert('success', _l('updated_successfully', _l('vendor')));
        }

        redirect(admin_url('purchase/vendor/' . $id . '?tab=vendor_admins'));
    }

    /**
     * { delete vendor }
     *
     * @param      <type>  $id     The identifier
     * @return      redirect
     */
    public function delete_vendor($id)
    {
        if (!has_permission('purchase_vendors', '', 'delete')) {
            access_denied('vendors');
        }
        if (!$id) {
            redirect(admin_url('purchase/vendors'));
        }
        $response = $this->purchase_model->delete_vendor($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('customer_delete_transactions_warning', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('credit_notes')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('client')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('client_lowercase')));
        }
        redirect(admin_url('purchase/vendors'));
    }

    /**
     * { form contact }
     *
     * @param      <type>  $customer_id  The customer identifier
     * @param      string  $contact_id   The contact identifier
     */
    public function form_contact($customer_id, $contact_id = '')
    {
        if (!has_permission('purchase_vendors', '', 'view') && !has_permission('purchase_vendors', '', 'view_own')) {
            if (!is_vendor_admin($customer_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data             = $this->input->post();
            $data['password'] = $this->input->post('password', false);

            unset($data['contactid']);
            if ($contact_id == '') {
                if (!has_permission('purchase_vendors', '', 'create')) {
                    if (!is_vendor_admin($customer_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode([
                            'success' => false,
                            'message' => _l('access_denied'),
                        ]);
                        die;
                    }
                }
                $id      = $this->purchase_model->add_contact($data, $customer_id);
                $message = '';
                $success = false;
                if ($id) {

                    $success = true;
                    $message = _l('added_successfully', _l('contact'));
                }
                echo json_encode([
                    'success'             => $success,
                    'message'             => $message,
                    'has_primary_contact' => (total_rows(db_prefix() . 'pur_contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
                    'is_individual'       => is_empty_vendor_company($customer_id) && total_rows(db_prefix() . 'pur_contacts', ['userid' => $customer_id]) == 1,
                ]);
                die;
            }
            if (!has_permission('purchase_vendors', '', 'edit')) {
                if (!is_vendor_admin($customer_id)) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                        'success' => false,
                        'message' => _l('access_denied'),
                    ]);
                    die;
                }
            }
            $original_contact = $this->purchase_model->get_contact($contact_id);
            $success          = $this->purchase_model->update_contact($data, $contact_id);
            $message          = '';
            $proposal_warning = false;
            $original_email   = '';
            $updated          = false;
            if (is_array($success)) {
                if (isset($success['set_password_email_sent'])) {
                    $message = _l('set_password_email_sent_to_client');
                } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {
                    $updated = true;
                    $message = _l('set_password_email_sent_to_client_and_profile_updated');
                }
            } else {
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('contact'));
                }
            }

            echo json_encode([
                'success'             => $success,
                'proposal_warning'    => $proposal_warning,
                'message'             => $message,
                'original_email'      => $original_email,
                'has_primary_contact' => (total_rows(db_prefix() . 'pur_contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
            ]);
            die;
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('contact_lowercase'));
        } else {
            $data['contact'] = $this->purchase_model->get_contact($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode([
                    'success' => false,
                    'message' => 'Contact Not Found',
                ]);
                die;
            }
            $title = $data['contact']->firstname . ' ' . $data['contact']->lastname;
        }


        $data['title']                = $title;
        $this->load->view('vendors/modals/contact', $data);
    }

    /**
     * { vendor contacts }
     *
     * @param      <type>  $client_id  The client identifier
     */
    public function vendor_contacts($client_id)
    {
        $this->app->get_table_data(module_views_path('purchase', 'vendors/table_contacts'), [
            'client_id' => $client_id,
        ]);
    }

    /**
     * Determines if contact email exists.
     */
    public function contact_email_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $userid = $this->input->post('userid');
                if ($userid != '') {
                    $this->db->where('id', $userid);
                    $_current_email = $this->db->get(db_prefix() . 'pur_contacts')->row();
                    if ($_current_email->email == $this->input->post('email')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('email', $this->input->post('email'));
                $total_rows = $this->db->count_all_results(db_prefix() . 'pur_contacts');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /**
     * { delete vendor contact }
     *
     * @param      string  $customer_id  The customer identifier
     * @param      <type>  $id           The identifier
     * @return     redirect
     */
    public function delete_vendor_contact($customer_id, $id)
    {
        if (!has_permission('purchase_vendors', '', 'delete')) {
            if (!is_customer_admin($customer_id)) {
                access_denied('vendors');
            }
        }

        $this->purchase_model->delete_contact($id);

        redirect(admin_url('purchase/vendor/' . $customer_id . '?group=contacts'));
    }


    /**
     * { all contacts }
     * @return     view
     */
    public function all_contacts()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('purchase', 'vendors/table_all_contacts'));
        }

        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }

        $data['title'] = _l('customer_contacts');
        $this->load->view('vendors/all_contacts', $data);
    }

    /**
     * { purchase request }
     * @return     view
     */
    public function purchase_request()
    {
        $this->load->model('departments_model');
        $this->load->model('projects_model');

        $data['title'] = _l('purchase_request');
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['departments'] = $this->departments_model->get();
        $data['vendor_contacts'] = $this->purchase_model->get_contacts();
        $data['projects'] = $this->projects_model->get();
        $data['item_group'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['item_sub_group'] = $this->purchase_model->get_sub_group();
        $data['requester'] = $this->staff_model->get('', ['active' => 1]);

        $this->load->view('purchase_request/manage', $data);
    }

    /**
     * { add update purchase request }
     *
     * @param      string  $id     The identifier
     * @return    redirect, view
     */
    public function pur_request($id = '')
    {
        $this->load->model('departments_model');
        $this->load->model('staff_model');
        $this->load->model('projects_model');
        $this->load->model('currencies_model');
        if ($id == '') {

            if ($this->input->post()) {
                $add_data = $this->input->post();
                $id = $this->purchase_model->add_pur_request($add_data);
                if ($id) {
                    set_alert('success', _l('added_pur_request'));
                }
                redirect(admin_url('purchase/purchase_request'));
            }

            $data['title'] = _l('add_new');
            $is_edit = false;
        } else {
            if ($this->input->post()) {
                $edit_data = $this->input->post();
                $success = $this->purchase_model->update_pur_request($edit_data, $id);
                if ($success == true) {
                    set_alert('success', _l('updated_pur_request'));
                }
                redirect(admin_url('purchase/purchase_request'));
            }

            $data['pur_request_detail'] = json_encode($this->purchase_model->get_pur_request_detail($id));
            $data['pur_request'] = $this->purchase_model->get_purchase_request($id);
            $data['taxes_data'] = $this->purchase_model->get_html_tax_pur_request($id);
            $data['attachments'] = $this->purchase_model->get_purchase_attachments('pur_request', $id);
            $data['title'] = _l('edit');
            $is_edit = true;
        }
        $data['commodity_groups_pur_request'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups_pur_request'] = $this->purchase_model->get_sub_group();
        $data['area_pur_request'] = $this->purchase_model->get_area();
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $purchase_request_row_template = $this->purchase_model->create_purchase_request_row_template();

        if ($id != '') {
            $data['pur_request_detail'] = $this->purchase_model->get_pur_request_detail($id);
            $currency_rate = 1;
            if ($data['pur_request']->currency != 0 && $data['pur_request']->currency_rate != null) {
                $currency_rate = $data['pur_request']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if ($data['pur_request']->currency != 0 && $data['pur_request']->to_currency != null) {
                $to_currency = $data['pur_request']->to_currency;
            }

            if (count($data['pur_request_detail']) > 0) {
                $index_request = 0;
                foreach ($data['pur_request_detail'] as $request_detail) {
                    $index_request++;
                    $unit_name = $request_detail['unit_id'];
                    $taxname = '';
                    $item_text = $request_detail['item_text'];

                    if (strlen($item_text) == 0) {
                        $item_text = pur_get_item_variatiom($request_detail['item_code']);
                    }

                    $purchase_request_row_template .= $this->purchase_model->create_purchase_request_row_template('items[' . $index_request . ']', $request_detail['item_code'], $item_text, $request_detail['description'], $request_detail['area'], $request_detail['image'], $request_detail['unit_price'], $request_detail['quantity'], $unit_name, $request_detail['unit_id'], $request_detail['into_money'], $request_detail['prd_id'], $request_detail['tax_value'], $request_detail['total'], $request_detail['tax_name'], $request_detail['tax_rate'], $request_detail['tax'], true, $currency_rate, $to_currency, $request_detail);
                }
            }
        }

        $data['currencies'] = $this->currencies_model->get();
        $data['is_edit'] = $is_edit;
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['purchase_request_row_template'] = $purchase_request_row_template;
        $data['invoices'] = $this->purchase_model->get_invoice_for_pr();
        $data['salse_estimates'] = $this->purchase_model->get_sale_estimate_for_pr();

        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['projects'] = $this->projects_model->get_items();
        $data['staffs'] = $this->staff_model->get();
        $data['departments'] = $this->departments_model->get();
        $data['units'] = $this->purchase_model->get_units();

        // Old script  $data['items'] = $this->purchase_model->get_items();
        $data['ajaxItems'] = false;

        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $this->load->view('purchase_request/pur_request', $data);
    }

    /**
     * { view pur request }
     *
     * @param      <type>  $id     The identifier
     * @return view
     */
    public function view_pur_request($id)
    {
        if (!has_permission('purchase_request', '', 'view') && !has_permission('purchase_request', '', 'view_own')) {
            access_denied('purchase');
        }

        $this->load->model('departments_model');
        $this->load->model('currencies_model');

        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if ((isset($send_mail_approve)) && $send_mail_approve != '') {
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        $data['pur_request'] = $this->purchase_model->get_purchase_request($id);

        if (has_permission('purchase_request', '', 'view_own') && !is_admin()) {
            $staffid = get_staff_user_id();
            $in_vendor = false;

            if ($data['pur_request']->send_to_vendors != null &&  $data['pur_request']->send_to_vendors != '') {
                $send_to_vendors_ids = explode(',', $data['pur_request']->send_to_vendors);

                $list_vendor = get_vendor_admin_list($staffid);
                foreach ($list_vendor as $vendor_id) {
                    if (in_array($vendor_id, $send_to_vendors_ids)) {
                        $in_vendor = true;
                    }
                }
            }

            $approve_access = total_rows(db_prefix() . 'pur_approval_details', ['staffid' => $staffid, 'rel_type' => 'pur_request', 'rel_id' => $id]);

            if ($data['pur_request']->requester != $staffid && $in_vendor == false && $approve_access == 0) {
                access_denied('purchase');
            }
        }

        if (!$data['pur_request']) {
            show_404();
        }

        $data['pur_request_detail'] = $this->purchase_model->get_pur_request_detail($id);
        $data['title'] = $data['pur_request']->pur_rq_name;
        $data['departments'] = $this->departments_model->get();
        $data['units'] = $this->purchase_model->get_units();
        $data['items'] = $this->purchase_model->get_items();
        $data['taxes_data'] = $this->purchase_model->get_html_tax_pur_request($id);
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['check_appr'] = $this->purchase_model->get_approve_setting('pur_request');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'pur_request');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'pur_request');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'pur_request');
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['pur_request_attachments'] = $this->purchase_model->get_purchase_request_attachments($id);
        $data['check_approval_setting'] = $this->purchase_model->check_approval_setting($data['pur_request']->project, 'pur_request', 0);
        $data['attachments'] = $this->purchase_model->get_purchase_attachments('pur_request', $id);
        $data['pur_request'] = $this->purchase_model->get_purchase_request($id);
        $data['commodity_groups_request'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups_request'] = $this->purchase_model->get_sub_group();
        $data['area_request'] = $this->purchase_model->get_area();
        $data['activity'] = $this->purchase_model->get_pr_activity($id);
        $this->load->view('purchase_request/view_pur_request', $data);
    }

    /**
     * { approval setting }
     * @return redirect
     */
    public function approval_setting()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['approval_setting_id'] == '') {
                $message = '';
                $success = $this->purchase_model->add_approval_setting($data);
                if ($success) {
                    $message = _l('added_successfully', _l('approval_setting'));
                }
                set_alert('success', $message);
                redirect(admin_url('purchase/setting?group=approval'));
            } else {
                $message = '';
                $id = $data['approval_setting_id'];
                $success = $this->purchase_model->edit_approval_setting($id, $data);
                if ($success) {
                    $message = _l('updated_successfully', _l('approval_setting'));
                }
                set_alert('success', $message);
                redirect(admin_url('purchase/setting?group=approval'));
            }
        }
    }

    /**
     * { delete approval setting }
     *
     * @param      <type>  $id     The identifier
     * @return redirect
     */
    public function delete_approval_setting($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=approval'));
        }
        $response = $this->purchase_model->delete_approval_setting($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('approval_setting')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('approval_setting')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('approval_setting')));
        }
        redirect(admin_url('purchase/setting?group=approval'));
    }

    /**
     * { items change event}
     *
     * @param      <type>  $val    The value
     * @return      json
     */
    public function items_change($val)
    {

        $value = $this->purchase_model->items_change($val);

        echo json_encode([
            'value' => $value
        ]);
        die;
    }

    /**
     * { table pur request }
     */
    public function table_pur_request()
    {
        $this->app->get_table_data(module_views_path('purchase', 'purchase_request/table_pur_request'));
    }

    /**
     * { delete pur request }
     *
     * @param      <type>  $id     The identifier
     * @return     redirect
     */
    public function delete_pur_request($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }
        $response = $this->purchase_model->delete_pur_request($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('purchase_request')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('purchase_request')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('purchase_request')));
        }
        redirect(admin_url('purchase/purchase_request'));
    }

    /**
     * { change status pur request }
     *
     * @param      <type>  $status  The status
     * @param      <type>  $id      The identifier
     * @return     json
     */
    public function change_status_pur_request($status, $id)
    {
        $change = $this->purchase_model->change_status_pur_request($status, $id);
        if ($change == true) {

            $message = _l('change_status_pur_request') . ' ' . _l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        } else {
            $message = _l('change_status_pur_request') . ' ' . _l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }

    /**
     * { quotations }
     *
     * @param      string  $id     The identifier
     * @return     view
     */
    public function quotations($id = '')
    {
        if (!has_permission('purchase_quotations', '', 'view') && !is_admin() && !has_permission('purchase_quotations', '', 'view_own')) {
            access_denied('quotations');
        }

        // Pipeline was initiated but user click from home page and need to show table only to filter
        if ($this->input->get('status') || $this->input->get('filter') && $isPipeline) {
            $this->pipeline(0, true);
        }
        $this->load->model('projects_model');

        $data['estimateid']            = $id;
        $data['pur_request'] = $this->purchase_model->get_purchase_request();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['projects'] = $this->projects_model->get();
        $data['title']                 = _l('estimates');
        $data['bodyclass']             = 'estimates-total-manual';
        $data['item_group'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['item_sub_group'] = $this->purchase_model->get_sub_group();
        $this->load->view('quotations/manage', $data);
    }

    /**
     * { function_description }
     *
     * @param      string  $id     The identifier
     * @return     redirect
     */
    public function estimate($id = '')
    {
        $this->load->model('currencies_model');
        if ($this->input->post()) {
            $estimate_data = $this->input->post();
            $estimate_data['terms'] = $this->input->post('terms', false);
            if ($id == '') {
                if (!has_permission('purchase_quotations', '', 'create')) {
                    access_denied('quotations');
                }

                $id = $this->purchase_model->add_estimate($estimate_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('estimate')));

                    redirect(admin_url('purchase/quotations/' . $id));
                }
            } else {
                if (!has_permission('purchase_quotations', '', 'edit')) {
                    access_denied('quotations');
                }

                $success = $this->purchase_model->update_estimate($estimate_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('estimate')));
                }
                redirect(admin_url('purchase/quotations/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('create_new_estimate');
        } else {
            $estimate = $this->purchase_model->get_estimate($id);
            $data['attachments'] = $this->purchase_model->get_purchase_attachments('pur_quotation', $id);

            $data['tax_data'] = $this->purchase_model->get_html_tax_pur_estimate($id);

            $data['estimate'] = $estimate;
            $data['edit']     = true;
            $title            = _l('edit', _l('estimate_lowercase'));
        }
        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $pur_quotation_row_template = $this->purchase_model->create_quotation_row_template();

        if ($id != '') {
            $data['estimate_detail'] = $this->purchase_model->get_pur_estimate_detail($id);
            $currency_rate = 1;
            if ($data['estimate']->currency != 0 && $data['estimate']->currency_rate != null) {
                $currency_rate = $data['estimate']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if ($data['estimate']->currency != 0 && $data['estimate']->to_currency != null) {
                $to_currency = $data['estimate']->to_currency;
            }


            if (count($data['estimate_detail']) > 0) {
                $index_quote = 0;
                foreach ($data['estimate_detail'] as $quote_detail) {
                    $index_quote++;
                    $unit_name = pur_get_unit_name($quote_detail['unit_id']);
                    $taxname = $quote_detail['tax_name'];
                    $item_name = $quote_detail['item_name'];

                    if (strlen($item_name) == 0) {
                        $item_name = pur_get_item_variatiom($quote_detail['item_code']);
                    }

                    $pur_quotation_row_template .= $this->purchase_model->create_quotation_row_template('items[' . $index_quote . ']',  $item_name, $quote_detail['area'], $quote_detail['image'], $quote_detail['quantity'], $unit_name, $quote_detail['unit_price'], $taxname, $quote_detail['item_code'], $quote_detail['unit_id'], $quote_detail['tax_rate'],  $quote_detail['total_money'], $quote_detail['discount_%'], $quote_detail['discount_money'], $quote_detail['total'], $quote_detail['into_money'], $quote_detail['tax'], $quote_detail['tax_value'], $quote_detail['id'], true, $currency_rate, $to_currency, $quote_detail);
                }
            }
        }

        $data['pur_quotation_row_template'] = $pur_quotation_row_template;

        $this->load->model('taxes_model');
        $data['taxes'] = $this->purchase_model->get_taxes();

        $data['currencies'] = $this->currencies_model->get();

        $this->load->model('invoice_items_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['pur_request'] = $this->purchase_model->get_pur_request_by_status(2);
        $data['units'] = $this->purchase_model->get_units();
        $data['projects'] = $this->projects_model->get_items();
        $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups_pur'] = $this->purchase_model->get_sub_group();
        $data['area_pur'] = $this->purchase_model->get_area();
        $data['title']             = $title;
        $this->load->view('quotations/estimate', $data);
    }

    /** 
     * { validate estimate number }
     */
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

        if (total_rows(db_prefix() . 'pur_estimates', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * { table estimates }
     */
    public function table_estimates()
    {
        $this->app->get_table_data(module_views_path('purchase', 'quotations/table_estimates'));
    }

    /**
     * Gets the estimate data ajax.
     *
     * @param      <type>   $id         The identifier
     * @param      boolean  $to_return  To return
     *
     * @return     <type>   view.
     */
    public function get_estimate_data_ajax($id, $to_return = false)
    {
        if (!has_permission('purchase_quotations', '', 'view') && !is_admin() && !has_permission('purchase_quotations', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No estimate found');
        }

        $estimate = $this->purchase_model->get_estimate($id);
        // echo '<pre>';
        // print_r($estimate);
        // die;    
        if (has_permission('purchase_quotations', '', 'view_own') && !is_admin()) {
            $staffid = get_staff_user_id();

            $approve_access = total_rows(db_prefix() . 'pur_approval_details', ['staffid' => $staffid, 'rel_type' => 'pur_quotation', 'rel_id' => $id]);

            if ($estimate->buyer != $staffid && $estimate->addedfrom != $staffid && !is_vendor_admin($estimate->vendor->userid) && $approve_access == 0) {
                echo _l('access_denied');
                die;
            }
        }

        $estimate->date       = _d($estimate->date);
        $estimate->expirydate = _d($estimate->expirydate);


        if ($estimate->sent == 0) {
            $template_name = 'estimate_send_to_customer';
        } else {
            $template_name = 'estimate_send_to_customer_already_sent';
        }

        $data['pur_estimate_attachments'] = $this->purchase_model->get_purchase_estimate_attachments($id);
        $data['estimate_detail'] = $this->purchase_model->get_pur_estimate_detail($id);
        $data['estimate']          = $estimate;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['vendor_contacts'] = $this->purchase_model->get_contacts($estimate->vendor->userid);
        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if ((isset($send_mail_approve)) && $send_mail_approve != '') {
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        $data['check_appr'] = $this->purchase_model->get_approve_setting('pur_quotation');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'pur_quotation');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'pur_quotation');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'pur_quotation');
        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_estimate($id);
        $data['check_approval_setting'] = $this->purchase_model->check_approval_setting($estimate->project, 'pur_quotation', 0);
        $data['attachments'] = $this->purchase_model->get_purchase_attachments('pur_quotation', $id);
        $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups_pur'] = $this->purchase_model->get_sub_group();
        $data['area_pur'] = $this->purchase_model->get_area();
        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        if ($to_return == false) {
            $this->load->view('quotations/estimate_preview_template', $data);
        } else {
            return $this->load->view('quotations/estimate_preview_template', $data, true);
        }
    }

    /**
     * { delete estimate }
     *
     * @param      <type>  $id     The identifier
     * @return     redirect
     */
    public function delete_estimate($id)
    {
        if (!has_permission('purchase_quotations', '', 'delete')) {
            access_denied('estimates');
        }
        if (!$id) {
            redirect(admin_url('purchase/quotations'));
        }
        $success = $this->purchase_model->delete_estimate($id);
        if (is_array($success)) {
            set_alert('warning', _l('is_invoiced_estimate_delete_error'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('estimate')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('estimate_lowercase')));
        }
        redirect(admin_url('purchase/quotations'));
    }

    /**
     * { tax change event }
     *
     * @param      <type>  $tax    The tax
     * @return   json
     */
    public function tax_change($tax)
    {
        $this->load->model('taxes_model');

        $taxes = explode('%7C', $tax);
        $total_tax = $this->purchase_model->get_total_tax($taxes);
        $tax_arr = [];
        foreach ($taxes as $t) {

            $tax_if = $this->taxes_model->get($t);
            if ($tax_if) {
                $tax_arr[$tax_if->id] = $tax_if->taxrate;
            }
        }

        echo json_encode([
            'total_tax' => $total_tax,
            'taxes' => $tax_arr
        ]);
    }

    /**
     * { coppy pur request }
     *
     * @param      <type>  $pur_request  The purchase request id
     * @return json
     */
    public function coppy_pur_request($pur_request)
    {
        $this->load->model('currencies_model');

        $pur_request_detail = $this->purchase_model->get_pur_request_detail_in_estimate($pur_request);
        $purchase_request = $this->purchase_model->get_purchase_request($pur_request);

        $base_currency = $this->currencies_model->get_base_currency();
        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';

        if (count($pur_request_detail) > 0) {
            foreach ($pur_request_detail as $key => $item) {
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $list_item = $this->purchase_model->create_quotation_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if ($purchase_request->currency != 0 && $purchase_request->currency_rate != null) {
            $currency_rate = $purchase_request->currency_rate;
            $to_currency = $purchase_request->currency;
        }

        if (count($pur_request_detail) > 0) {
            $index_quote = 0;
            foreach ($pur_request_detail as $key => $item) {
                $index_quote++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_text'];

                if (strlen($item_name) == 0) {
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->purchase_model->create_quotation_row_template('newitems[' . $index_quote . ']',  $item_name, $item['area'], '', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total'], '', '', $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index_quote, true, $currency_rate, $to_currency);
            }
        }


        $taxes_data = $this->purchase_model->get_html_tax_pur_request($pur_request);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_request_detail,
            'subtotal' => app_format_money(round($subtotal, 2), ''),
            'total' => app_format_money(round($total, 2), ''),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
    }

    /**
     * { coppy pur request }
     *
     * @param      <type>  $pur_request  The purchase request id
     * @return json
     */
    public function coppy_pur_request_for_po($pur_request, $vendor = '')
    {

        $this->load->model('currencies_model');

        $pur_request_detail = $this->purchase_model->get_pur_request_detail_in_po($pur_request);
        $purchase_request = $this->purchase_model->get_purchase_request($pur_request);

        $base_currency = $this->currencies_model->get_base_currency();
        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';
        $estimate_html = '';

        $estimate_html .= $this->purchase_model->get_estimate_html_by_pr_vendor($pur_request, $vendor);

        if (count($pur_request_detail) > 0) {
            foreach ($pur_request_detail as $key => $item) {
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $list_item = $this->purchase_model->create_purchase_order_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if ($purchase_request->currency != 0 && $purchase_request->currency_rate != null) {
            $currency_rate = $purchase_request->currency_rate;
            $to_currency = $purchase_request->currency;
        }


        if (count($pur_request_detail) > 0) {
            $index_quote = 0;
            foreach ($pur_request_detail as $key => $item) {
                $index_quote++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_text'];

                if (strlen($item_name) == 0) {
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->purchase_model->create_purchase_order_row_template('newitems[' . $index_quote . ']',  $item_name, $item['description'], $item['area'], '', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total'], '', '', $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index_quote, true, $currency_rate, $to_currency, [], false);
            }
        }

        $taxes_data = $this->purchase_model->get_html_tax_pur_request($pur_request);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_request_detail,
            'subtotal' => app_format_money(round($subtotal, 2), ''),
            'total' => app_format_money(round($total, 2), ''),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'estimate_html' => $estimate_html,
        ]);
    }
    public function coppy_pur_request_for_wo($pur_request, $vendor = '')
    {

        $this->load->model('currencies_model');

        $pur_request_detail = $this->purchase_model->get_pur_request_detail_in_po($pur_request);
        $purchase_request = $this->purchase_model->get_purchase_request($pur_request);

        $base_currency = $this->currencies_model->get_base_currency();
        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';
        $estimate_html = '';

        $estimate_html .= $this->purchase_model->get_estimate_html_by_pr_vendor($pur_request, $vendor);

        if (count($pur_request_detail) > 0) {
            foreach ($pur_request_detail as $key => $item) {
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $list_item = $this->purchase_model->create_purchase_order_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if ($purchase_request->currency != 0 && $purchase_request->currency_rate != null) {
            $currency_rate = $purchase_request->currency_rate;
            $to_currency = $purchase_request->currency;
        }


        if (count($pur_request_detail) > 0) {
            $index_quote = 0;
            foreach ($pur_request_detail as $key => $item) {
                $index_quote++;
                $unit_name = $item['unit_id'];
                $taxname = $item['tax_name'];
                $item_name = $item['item_text'];

                if (strlen($item_name) == 0) {
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->purchase_model->create_wo_order_row_template('newitems[' . $index_quote . ']',  $item_name, $item['description'], $item['area'], '', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total'], '', '', $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index_quote, true, $currency_rate, $to_currency);
            }
        }

        $taxes_data = $this->purchase_model->get_html_tax_pur_request($pur_request);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_request_detail,
            'subtotal' => app_format_money(round($subtotal, 2), ''),
            'total' => app_format_money(round($total, 2), ''),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'estimate_html' => $estimate_html,
        ]);
    }

    /**
     * { coppy pur estimate }
     *
     * @param      <type>  $pur_estimate  The purchase estimate id
     * @return  json
     */
    public function coppy_pur_estimate($pur_estimate_id)
    {
        $this->load->model('currencies_model');
        $pur_estimate_detail = $this->purchase_model->get_pur_estimate_detail_in_order($pur_estimate_id);
        $pur_estimate = $this->purchase_model->get_estimate($pur_estimate_id);

        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';

        if (count($pur_estimate_detail) > 0) {
            foreach ($pur_estimate_detail as $key => $item) {
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $base_currency = $this->currencies_model->get_base_currency();
        $list_item = $this->purchase_model->create_purchase_order_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if ($pur_estimate->currency != 0 && $pur_estimate->currency_rate != null) {
            $currency_rate = $pur_estimate->currency_rate;
            $to_currency = $pur_estimate->currency;
        }

        if (count($pur_estimate_detail) > 0) {
            $index = 0;
            foreach ($pur_estimate_detail as $key => $item) {
                $index++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_name'];
                if (strlen($item_name) == 0) {
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->purchase_model->create_purchase_order_row_template('newitems[' . $index . ']',  $item_name, '', $item['area'], '', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total_money'], $item['discount_%'], $item['discount_money'], $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index, true, $currency_rate, $to_currency, [], false);
            }
        }

        $taxes_data = $this->purchase_model->get_html_tax_pur_estimate($pur_estimate_id);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_estimate_detail,
            'dc_percent' => $pur_estimate->discount_percent,
            'dc_total' => $pur_estimate->discount_total,
            'discount_type' => $pur_estimate->discount_type,
            'subtotal' => app_format_money(round($subtotal, 2), ''),
            'total' => app_format_money(round($total, 2), ''),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'shipping_fee' => $pur_estimate->shipping_fee
        ]);
    }

    /**
     * { view purchase order }
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return json
     */
    public function view_pur_order($pur_order)
    {
        $pur_order_detail = $this->purchase_model->get_pur_order_detail($pur_order);
        $pur_order = $this->purchase_model->get_pur_order($pur_order);
        $base_currency = get_base_currency_pur();


        $total = $pur_order->total;
        $rate = 1;
        if ($base_currency->id != $pur_order->currency && $pur_order->currency != 0) {
            $po_currency = pur_get_currency_by_id($pur_order->currency);

            $rate = pur_get_currency_rate($po_currency->name, $base_currency->name);
        }

        $total = $total * $rate;

        echo json_encode([
            'total' => app_format_money($total, ''),
            'vendor' => $pur_order->vendor,
            'buyer' => $pur_order->buyer,
            'project' => $pur_order->project,
            'department' => $pur_order->department
        ]);
    }

    /**
     * { change status pur estimate }
     *
     * @param      <type>  $status  The status
     * @param      <type>  $id      The identifier
     * @return json
     */
    public function change_status_pur_estimate($status, $id)
    {
        $change = $this->purchase_model->change_status_pur_estimate($status, $id);
        if ($change == true) {

            $message = _l('change_status_pur_estimate') . ' ' . _l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        } else {
            $message = _l('change_status_pur_estimate') . ' ' . _l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }

    /**
     * { change status pur order }
     *
     * @param      <type>  $status  The status
     * @param      <type>  $id      The identifier
     * @return json
     */
    public function change_status_pur_order($status, $id)
    {
        $change = $this->purchase_model->change_status_pur_order($status, $id);
        if ($change == true) {

            $message = _l('change_status_pur_order') . ' ' . _l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        } else {
            $message = _l('change_status_pur_order') . ' ' . _l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }

    /**
     * { purchase order }
     *
     * @param      string  $id     The identifier
     * @return view
     */
    public function purchase_order($id = '')
    {
        if (!has_permission('purchase_orders', '', 'view') && !is_admin() && !has_permission('purchase_orders', '', 'view_own')) {
            access_denied('purchase');
        }
        $this->load->model('expenses_model');
        $this->load->model('payment_modes_model');
        $this->load->model('taxes_model');
        $this->load->model('currencies_model');
        $this->load->model('departments_model');
        $this->load->model('projects_model');
        $this->load->model('clients_model');

        $data['pur_orderid']            = $id;
        $data['title'] = _l('purchase_order');

        $data['departments'] = $this->departments_model->get();
        $data['projects'] = $this->projects_model->get();
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);
        $data['currencies']         = $this->currencies_model->get();
        $data['taxes']              = $this->taxes_model->get();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['expense_categories'] = $this->expenses_model->get_category();
        $data['item_tags'] = $this->purchase_model->get_item_tag_filter();
        $data['customers'] = $this->clients_model->get();
        $data['pur_request'] = $this->purchase_model->get_pur_request_by_status(2);

        $data['projects'] = $this->projects_model->get();
        $data['item_group'] = $this->purchase_model->get_commodity_group_add_commodity();

        $this->load->view('purchase_order/manage', $data);
    }

    /**
     * Gets the pur order data ajax.
     *
     * @param      <type>   $id         The identifier
     * @param      boolean  $to_return  To return
     *
     * @return     view.
     */
    public function get_pur_order_data_ajax($id, $to_return = false)
    {
        if (!has_permission('purchase_orders', '', 'view') && !has_permission('purchase_orders', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No purchase order found');
        }

        $estimate = $this->purchase_model->get_pur_order($id);

        if (has_permission('purchase_orders', '', 'view_own') && !is_admin()) {
            $staffid = get_staff_user_id();

            $approve_access = total_rows(db_prefix() . 'pur_approval_details', ['staffid' => $staffid, 'rel_type' => 'pur_order', 'rel_id' => $id]);

            if ($estimate->buyer != $staffid && $estimate->addedfrom != $staffid && !is_vendor_admin($estimate->vendor) && $approve_access == 0) {
                echo _l('access_denied');
                die;
            }
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $data['payment'] = $this->purchase_model->get_inv_payment_purchase_order($id);
        $data['pur_order_attachments'] = $this->purchase_model->get_purchase_order_attachments($id);
        $data['estimate_detail'] = $this->purchase_model->get_pur_order_detail($id);
        if (!empty($data['estimate_detail'])) {
            $data['estimate_detail'] = $this->purchase_model->get_changee_pur_order_detail($data['estimate_detail'], $id);
        }
        $data['estimate']          = $estimate;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['vendor_contacts'] = $this->purchase_model->get_contacts($estimate->vendor);
        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if ((isset($send_mail_approve)) && $send_mail_approve != '') {
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        $data['invoices'] = $this->purchase_model->get_invoices_by_po($id);
        $data['check_appr'] = $this->purchase_model->get_approve_setting('pur_order');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'pur_order');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'pur_order');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'pur_order');
        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_order($id);
        $data['check_approval_setting'] = $this->purchase_model->check_approval_setting($estimate->project, 'pur_order', 0);
        $data['attachments'] = $this->purchase_model->get_purchase_attachments('pur_order', $id);
        $data['pur_order'] = $this->purchase_model->get_pur_order($id);
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups'] = $this->purchase_model->get_sub_group();
        $data['area'] = $this->purchase_model->get_area();
        $data['activity'] = $this->purchase_model->get_po_activity($id);
        $data['changes'] = $this->purchase_model->get_po_changes($id);
        $data['payment_certificate'] = $this->purchase_model->get_all_po_payment_certificate($id);
        $data['bills_data'] = $this->purchase_model->get_bills_data($id);
        if ($to_return == false) {
            $this->load->view('purchase_order/pur_order_preview', $data);
        } else {
            return $this->load->view('purchase_order/pur_order_preview', $data, true);
        }
    }

    /**
     * { purchase order form }
     *
     * @param      string  $id     The identifier
     * @return redirect, view
     */
    public function pur_order($id = '')
    {
        if ($this->input->post()) {
            $pur_order_data = $this->input->post();
            $pur_order_data['terms'] = $this->input->post('terms', false);
            $pur_order_data['vendornote'] = $this->input->post('vendornote', false);
            $pur_order_data['order_summary'] = $this->input->post('order_summary', false);

            if ($id == '') {
                if (!has_permission('purchase_orders', '', 'create')) {
                    access_denied('purchase_order');
                }

                $id = $this->purchase_model->add_pur_order($pur_order_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('pur_order')));

                    redirect(admin_url('purchase/purchase_order/' . $id));
                }
            } else {
                if (!has_permission('purchase_orders', '', 'edit')) {
                    access_denied('purchase_order');
                }
                $success = $this->purchase_model->update_pur_order($pur_order_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('pur_order')));
                }
                redirect(admin_url('purchase/purchase_order/' . $id));
            }
        }

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $pur_order_row_template = $this->purchase_model->create_purchase_order_row_template();

        if ($id == '') {
            $title = _l('create_new_pur_order');
            $is_edit = false;
        } else {
            $data['pur_order_detail'] = $this->purchase_model->get_pur_order_detail($id);
            $data['pur_order'] = $this->purchase_model->get_pur_order($id);
            $data['attachments'] = $this->purchase_model->get_purchase_attachments('pur_order', $id);

            $currency_rate = 1;
            if ($data['pur_order']->currency != 0 && $data['pur_order']->currency_rate != null) {
                $currency_rate = $data['pur_order']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if ($data['pur_order']->currency != 0 && $data['pur_order']->to_currency != null) {
                $to_currency = $data['pur_order']->to_currency;
            }


            $data['tax_data'] = $this->purchase_model->get_html_tax_pur_order($id);
            $title = _l('pur_order_detail');

            if (count($data['pur_order_detail']) > 0) {
                $index_order = 0;
                foreach ($data['pur_order_detail'] as $order_detail) {
                    $index_order++;
                    $unit_name = $order_detail['unit_id'];
                    $taxname = $order_detail['tax_name'];
                    $item_name = $order_detail['item_name'];

                    if (strlen($item_name) == 0) {
                        $item_name = pur_get_item_variatiom($order_detail['item_code']);
                    }

                    $pur_order_row_template .= $this->purchase_model->create_purchase_order_row_template('items[' . $index_order . ']',  $item_name, $order_detail['description'], $order_detail['area'], $order_detail['image'], $order_detail['quantity'], $unit_name, $order_detail['unit_price'], $taxname, $order_detail['item_code'], $order_detail['unit_id'], $order_detail['tax_rate'],  $order_detail['total_money'], $order_detail['discount_%'], $order_detail['discount_money'], $order_detail['total'], $order_detail['into_money'], $order_detail['tax'], $order_detail['tax_value'], $order_detail['id'], true, $currency_rate, $to_currency, $order_detail, false, $order_detail['sub_groups_pur'], $order_detail['serial_no'], $order_detail['non_budget_item']);
                }
            }
            $is_edit = true;
        }
        $data['is_edit'] = $is_edit;
        $data['pur_order_row_template'] = $pur_order_row_template;
        $data['currencies'] = $this->currencies_model->get();
        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        $data['invoices'] = $this->purchase_model->get_invoice_for_pr();
        $data['pur_request'] = $this->purchase_model->get_pur_request_by_status(2);
        $data['projects'] = $this->projects_model->get_items();
        $data['ven'] = $this->input->get('vendor');
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['estimates'] = $this->purchase_model->get_estimates_by_status(2);
        $data['units'] = $this->purchase_model->get_units();
        $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups_pur'] = $this->purchase_model->get_sub_group();
        $data['area_pur'] = $this->purchase_model->get_area();
        $this->load->model('invoices_model');
        $data['get_hsn_sac_code'] = $this->invoices_model->get_hsn_sac_code();
        $data['budgets'] = $this->purchase_model->get_all_estimates();
        $data['ajaxItems'] = false;

        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $data['convert_po'] = false;
        $pr = $this->input->get('pr', TRUE);
        if (!empty($pr)) {
            $purchase_request = $this->purchase_model->get_purchase_request($pr);
            if (!empty($purchase_request)) {
                $data['convert_po'] = true;
                $data['selected_pr'] = $purchase_request->id;
                $data['selected_project'] = $purchase_request->project;
                $data['selected_head'] = $purchase_request->group_pur;
                $data['selected_sub_head'] = $purchase_request->sub_groups_pur;
                $data['selected_area'] = $purchase_request->area_pur;
            }
        }

        $data['book_order'] = false;
        $package = $this->input->get('package', TRUE);
        if (!empty($package)) {
            $cost_package_detail = $this->purchase_model->get_cost_package_detail($package);
            if (!empty($cost_package_detail)) {
                $data['book_order'] = true;
                $data['cost_package_detail'] = $cost_package_detail;
                $package_items_info = $this->purchase_model->get_package_items_info($package);
                if (!empty($package_items_info)) {
                    $index_order = 0;
                    $pur_order_row_template = '';
                    $pur_order_row_template .= $this->purchase_model->create_purchase_order_row_template();
                    foreach ($package_items_info as $order_detail) {
                        $index_order++;
                        $package_item_total = $order_detail['package_qty'] * $order_detail['package_rate'];
                        $pur_order_row_template .= $this->purchase_model->create_purchase_order_row_template('items[' . $index_order . ']',  $order_detail['item_code'], $order_detail['long_description'], '', '', $order_detail['package_qty'], $order_detail['unit_id'], $order_detail['package_rate'], '', $order_detail['item_code'], $order_detail['unit_id'], '',  $package_item_total, '', '', $package_item_total, $package_item_total, '', '', '', false, 1, $data['base_currency']->name, array(), false, $order_detail['sub_head'], '', 0);
                    }
                    $data['pur_order_row_template'] = $pur_order_row_template;
                }
            }
        }

        $data['title'] = $title;

        $this->load->view('purchase_order/pur_order', $data);
    }

    /**
     * { delete pur order }
     *
     * @param      <type>  $id     The identifier
     * @return redirect
     */
    public function delete_pur_order($id)
    {
        if (!has_permission('purchase_orders', '', 'delete')) {
            access_denied('purchase_order');
        }
        if (!$id) {
            redirect(admin_url('purchase/purchase_order'));
        }
        $success = $this->purchase_model->delete_pur_order($id);
        if (is_array($success)) {
            set_alert('warning', _l('purchase_order'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('purchase_order')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('purchase_order')));
        }
        redirect(admin_url('purchase/purchase_order'));
    }

    /**
     * { estimate by vendor }
     *
     * @param      <type>  $vendor  The vendor
     * @return json
     */
    public function estimate_by_vendor($vendor)
    {
        $estimate = $this->purchase_model->estimate_by_vendor($vendor);
        $ven = $this->purchase_model->get_vendor($vendor);

        $currency = get_base_currency_pur();
        $currency_id = $currency->id;
        if ($ven->default_currency != 0) {
            $currency_id = $ven->default_currency;
        }

        $vendor_data = '';
        $html = '<option value=""></option>';
        $company = '';
        foreach ($estimate as $es) {
            $html .= '<option value="' . $es['id'] . '">' . format_pur_estimate_number($es['id']) . '</option>';
        }

        $option_html = '';

        if (total_rows(db_prefix() . 'pur_vendor_items', ['vendor' => $vendor]) <= ajax_on_total_items()) {
            $items = $this->purchase_model->get_items_by_vendor_variation($vendor);
            $option_html .= '<option value=""></option>';
            foreach ($items as $item) {
                $option_html .= '<option value="' . $item['id'] . '" >' . $item['label'] . '</option>';
            }
        }


        if ($ven) {
            $vendor_data .= '<div class="col-md-6">';
            $vendor_data .= '<p class="bold p_style">' . _l('vendor_detail') . '</p>
                            <hr class="hr_style"/>';
            $vendor_data .= '<table class="table table-striped table-bordered"><tbody>';
            $vendor_data .= '<tr><td>' . _l('company') . '</td><td>' . $ven->company . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('client_vat_number') . '</td><td>' . $ven->vat . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('client_phonenumber') . '</td><td>' . $ven->phonenumber . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('website') . '</td><td>' . $ven->website . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('vendor_category') . '</td><td>' . get_vendor_category_html($ven->category) . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('client_address') . '</td><td>' . $ven->address . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('client_city') . '</td><td>' . $ven->city . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('client_state') . '</td><td>' . $ven->state . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('client_postal_code') . '</td><td>' . $ven->zip . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('clients_country') . '</td><td>' . get_country_short_name($ven->country) . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('bank_detail') . '</td><td>' . $ven->bank_detail . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('payment_terms') . '</td><td>' . $ven->payment_terms . '</td></tr>';
            $vendor_data .= '</tbody></table>';
            $vendor_data .= '</div>';

            $vendor_data .= '<div class="col-md-6">';
            $vendor_data .= '<p class="bold p_style">' . _l('billing_address') . '</p>
                            <hr class="hr_style"/>';
            $vendor_data .= '<table class="table table-striped table-bordered"><tbody>';
            $vendor_data .= '<tr><td>' . _l('billing_street') . '</td><td>' . $ven->billing_street . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('billing_city') . '</td><td>' . $ven->billing_city . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('billing_state') . '</td><td>' . $ven->billing_state . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('billing_zip') . '</td><td>' . $ven->billing_zip . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('billing_country') . '</td><td>' . get_country_short_name($ven->billing_country) . '</td></tr>';
            $vendor_data .= '</tbody></table>';
            $vendor_data .= '<p class="bold p_style">' . _l('shipping_address') . '</p>
                            <hr class="hr_style"/>';
            $vendor_data .= '<table class="table table-striped table-bordered"><tbody>';
            $vendor_data .= '<tr><td>' . _l('shipping_street') . '</td><td>' . $ven->shipping_street . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('shipping_city') . '</td><td>' . $ven->shipping_city . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('shipping_state') . '</td><td>' . $ven->shipping_state . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('shipping_zip') . '</td><td>' . $ven->shipping_zip . '</td></tr>';
            $vendor_data .= '<tr><td>' . _l('shipping_country') . '</td><td>' . get_country_short_name($ven->shipping_country) . '</td></tr>';
            $vendor_data .= '</tbody></table>';
            $vendor_data .= '</div>';

            if ($ven->vendor_code != '') {
                $company = $ven->vendor_code;
            }
        }

        echo json_encode([
            'result' => $html,
            'ven_html' => $vendor_data,
            'company' => $company,
            'option_html' => $option_html,
            'currency_id' => $currency_id
        ]);
    }

    /**
     * { table pur order }
     */
    public function table_pur_order()
    {
        $this->app->get_table_data(module_views_path('purchase', 'purchase_order/table_pur_order'));
    }
    public function table_order_tracker()
    {
        $this->app->get_table_data(module_views_path('purchase', 'order_tracker/table_order_tracker'));
    }
    /**
     * { contracts }
     * @return  view
     */
    public function contracts()
    {
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        $this->load->model('projects_model');
        $data['projects'] = $this->projects_model->get();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['title'] = _l('contracts');
        $this->load->view('contracts/manage', $data);
    }

    /**
     * { contract }
     *
     * @param      string  $id     The identifier
     * @return redirect , view
     */
    public function contract($id = '')
    {
        if ($this->input->post()) {
            $contract_data = $this->input->post();
            $contract_data['currency'] = get_base_currency()->id;
            if ($id == '') {

                $id = $this->purchase_model->add_contract($contract_data);
                if ($id) {
                    handle_pur_contract_file($id);
                    set_alert('success', _l('added_successfully', _l('contract')));

                    redirect(admin_url('purchase/contracts'));
                }
            } else {
                handle_pur_contract_file($id);
                $success = $this->purchase_model->update_contract($contract_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('pur_order')));
                }
                redirect(admin_url('purchase/contract/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('create_new_contract');
        } else {
            $data['contract'] = $this->purchase_model->get_contract($id);

            if (!has_permission('purchase_contracts', '', 'view') && !has_permission('purchase_contracts', '', 'view_own')) {
                access_denied('purchase');
            }

            if (has_permission('purchase_contracts', '', 'view_own') && !is_admin()) {
                if ($data['contract']->add_from != get_staff_user_id() && !in_array($data['contract']->vendor, get_vendor_admin_list(get_staff_user_id()))) {
                    access_denied('purchase');
                }
            }


            $data['vendor_contacts'] = $this->purchase_model->get_contacts($data['contract']->vendor);
            $data['attachments'] = $this->purchase_model->get_pur_contract_attachment($id);
            $data['payment'] = $this->purchase_model->get_payment_by_contract($id);
            $title = _l('contract_detail');
        }
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        $this->load->model('projects_model');
        $data['projects'] = $this->projects_model->get();
        $data['ven'] = $this->input->get('vendor');
        $data['pur_orders'] = $this->purchase_model->get_pur_order_approved();
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['members']             = $this->staff_model->get('', ['active' => 1]);
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['units'] = $this->purchase_model->get_units();
        $data['items'] = $this->purchase_model->get_items();
        $data['title'] = $title;

        $this->load->view('contracts/contract', $data);
    }

    /**
     * { delete contract }
     *
     * @param      <type>  $id     The identifier
     * @return redirect
     */
    public function delete_contract($id)
    {
        if (!has_permission('purchase_contracts', '', 'delete')) {
            access_denied('contracts');
        }
        if (!$id) {
            redirect(admin_url('purchase/contracts'));
        }
        $success = $this->purchase_model->delete_contract($id);
        if (is_array($success)) {
            set_alert('warning', _l('contracts'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('contracts')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contracts')));
        }
        redirect(admin_url('purchase/contracts'));
    }

    /**
     * Determines if contract number exists.
     */
    public function contract_number_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $contract = $this->input->post('contract');
                if ($contract != '') {
                    $this->db->where('id', $contract);
                    $cd = $this->db->get('tblpur_contracts')->row();
                    if ($cd->contract_number == $this->input->post('contract_number')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('contract_number', $this->input->post('contract_number'));
                $total_rows = $this->db->count_all_results('tblpur_contracts');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /**
     * { table contracts }
     */
    public function table_contracts()
    {
        $this->app->get_table_data(module_views_path('purchase', 'contracts/table_contracts'));
    }

    /**
     * Saves a contract data.
     * @return  json
     */
    public function save_contract_data()
    {
        if (!has_permission('purchase_contracts', '', 'edit') && !has_permission('purchase_contracts', '', 'create')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die;
        }

        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('contract_id'));
        $this->db->update(db_prefix() . 'pur_contracts', [
            'content' => $this->input->post('content', false),
        ]);

        $success = $this->db->affected_rows() > 0;
        $message = _l('updated_successfully', _l('contract'));

        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    /**
     * { pdf contract }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function pdf_contract($id)
    {
        if (!has_permission('purchase_contracts', '', 'view') && !has_permission('purchase_contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        if (!$id) {
            redirect(admin_url('purchase/contracts'));
        }

        $contract = $this->purchase_model->get_contract($id);

        try {
            $pdf = pur_contract_pdf($contract);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it($contract->contract_number) . '.pdf', $type);
    }

    /**
     * { sign contract }
     *
     * @param      <type>  $contract  The contract
     * @return json
     */
    public function sign_contract($contract)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->purchase_model->sign_contract($contract, $data['status']);
            $message = '';
            if ($success == true) {
                process_digital_signature_image($data['signature'], PURCHASE_MODULE_UPLOAD_FOLDER . '/contract_sign/' . $contract);
                $message = _l('sign_successfully');
            }

            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
        }
    }

    /**
     * Sends a request approve.
     * @return  json
     */
    public function send_request_approve()
    {
        $data = $this->input->post();
        $message = 'Send request approval fail';
        $success = $this->purchase_model->send_request_approve($data);
        if ($success === true) {
            $message = 'Send request approval success';
            $data_new = [];
            $data_new['send_mail_approve'] = $data;
            $this->session->set_userdata($data_new);
        } elseif ($success === false) {
            $message = _l('no_matching_process_found');
            $success = false;
        } else {
            $message = _l('could_not_find_approver_with', _l($success));
            $success = false;
        }
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        die;
    }

    /**
     * Sends a mail.
     * @return json
     */
    public function send_mail()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ((isset($data)) && $data != '') {
                $this->purchase_model->send_mail($data);

                $success = 'success';
                echo json_encode([
                    'success' => $success,
                ]);
            }
        }
    }

    /**
     * { approve request }
     * @return json
     */
    public function approve_request()
    {
        $data = $this->input->post();
        $data['staff_approve'] = get_staff_user_id();
        $success = false;
        $code = '';
        $signature = '';

        if (isset($data['signature'])) {
            $signature = $data['signature'];
            unset($data['signature']);
        }
        $status_string = 'status_' . $data['approve'];
        $check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'], $data['rel_type']);

        if (isset($data['approve']) && in_array(get_staff_user_id(), $check_approve_status['staffid'])) {

            $success = $this->purchase_model->update_approval_details($check_approve_status['id'], $data);

            $message = _l('approved_successfully');

            if ($success) {
                if ($data['approve'] == 2) {
                    $message = _l('approved_successfully');
                    $data_log = [];

                    if ($signature != '') {
                        $data_log['note'] = "signed_request";
                    } else {
                        $data_log['note'] = "approve_request";
                    }
                    if ($signature != '') {
                        switch ($data['rel_type']) {
                            case 'payment_request':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/payment_invoice/signature/' . $data['rel_id'];
                                break;
                            case 'pur_order':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/signature/' . $data['rel_id'];
                                break;
                            case 'pur_request':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_request/signature/' . $data['rel_id'];
                                break;
                            case 'pur_quotation':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/signature/' . $data['rel_id'];
                                break;
                            case 'order_return':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/order_return/signature/' . $data['rel_id'];
                                break;
                            default:
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER;
                                break;
                        }
                        purchase_process_digital_signature_image($signature, $path, 'signature_' . $check_approve_status['id']);
                        $message = _l('sign_successfully');
                    }



                    $check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'], $data['rel_type']);
                    if ($check_approve_status === true) {
                        $this->purchase_model->update_approve_request($data['rel_id'], $data['rel_type'], 2);
                    }
                } else {
                    $message = _l('rejected_successfully');

                    $this->purchase_model->update_approve_request($data['rel_id'], $data['rel_type'], '3');
                }
            }
        }

        $data_new = [];
        $data_new['send_mail_approve'] = $data;
        $this->session->set_userdata($data_new);
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        die();
    }

    /**
     * approve request
     * @param  integer $id
     * @return json
     */
    public function approve_request_for_order_return()
    {
        $data = $this->input->post();

        $data['staff_approve'] = get_staff_user_id();
        $success = false;
        $code = '';
        $signature = '';

        if (isset($data['signature'])) {
            $signature = $data['signature'];
            unset($data['signature']);
        }
        $status_string = 'status_' . $data['approve'];
        $check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'], $data['rel_type']);


        if (isset($data['approve']) && in_array(get_staff_user_id(), $check_approve_status['staffid'])) {

            $success = $this->purchase_model->update_approval_details($check_approve_status['id'], $data);

            $message = _l('approved_successfully');

            if ($success) {
                if ($data['approve'] == 1) {
                    $message = _l('approved_successfully');
                    $data_log = [];

                    if ($signature != '') {
                        $data_log['note'] = "signed_request";
                    } else {
                        $data_log['note'] = "approve_request";
                    }
                    if ($signature != '') {
                        switch ($data['rel_type']) {


                            case 'order_return':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/order_return/signature/' . $data['rel_id'];
                                break;



                            default:
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER;
                                break;
                        }
                        purchase_process_digital_signature_image($signature, $path, 'signature_' . $check_approve_status['id']);
                        $message = _l('sign_successfully');
                    }
                    $data_log['rel_id'] = $data['rel_id'];
                    $data_log['rel_type'] = $data['rel_type'];
                    $data_log['staffid'] = get_staff_user_id();
                    $data_log['date'] = date('Y-m-d H:i:s');

                    $this->purchase_model->add_activity_log($data_log);

                    $check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'], $data['rel_type']);

                    if ($check_approve_status === true) {
                        $this->purchase_model->update_approve_request($data['rel_id'], $data['rel_type'], 1);
                    }
                } else {
                    $message = _l('rejected_successfully');
                    $data_log = [];
                    $data_log['rel_id'] = $data['rel_id'];
                    $data_log['rel_type'] = $data['rel_type'];
                    $data_log['staffid'] = get_staff_user_id();
                    $data_log['date'] = date('Y-m-d H:i:s');
                    $data_log['note'] = "rejected_request";
                    $this->purchase_model->add_activity_log($data_log);
                    $this->purchase_model->update_approve_request($data['rel_id'], $data['rel_type'], '-1');
                }
            }
        }

        $data_new = [];
        $data_new['send_mail_approve'] = $data;
        $this->session->set_userdata($data_new);
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        die();
    }

    /**
     * Sends a request quotation.
     * @return redirect
     */
    public function send_request_quotation()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_pr($data);
            if ($send) {
                set_alert('success', _l('send_pr_successfully'));
            } else {
                set_alert('warning', _l('send_pr_fail'));
            }
            redirect(admin_url('purchase/purchase_request'));
        }
    }

    /**
     * { purchase request pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function pur_request_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }

        $pur_request = $this->purchase_model->get_pur_request_pdf_html($id);

        try {
            $pdf = $this->purchase_model->pur_request_pdf($pur_request);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('purchase_request.pdf', $type);
    }

    /**
     * { request quotation pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function request_quotation_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }

        $pur_request = $this->purchase_model->get_request_quotation_pdf_html($id);

        try {
            $pdf = $this->purchase_model->request_quotation_pdf($pur_request);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('request_quotation.pdf', $type);
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function purchase_order_setting()
    {
        $data = $this->input->post();
        if ($data != 'null') {
            $value = $this->purchase_model->update_purchase_setting($data);
            if ($value) {
                $success = true;
                $message = _l('updated_successfully');
            } else {
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function item_by_vendor()
    {
        $data = $this->input->post();
        if ($data != 'null') {
            $value = $this->purchase_model->update_purchase_setting($data);
            if ($value) {
                $success = true;
                $message = _l('updated_successfully');
            } else {
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function show_item_cf_on_pdf()
    {
        $data = $this->input->post();
        if ($data != 'null') {
            $value = $this->purchase_model->update_pc_options_setting($data);
            if ($value) {
                $success = true;
                $message = _l('updated_successfully');
            } else {
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function show_tax_column()
    {
        $data = $this->input->post();
        if ($data != 'null') {
            $value = $this->purchase_model->update_purchase_setting($data);
            if ($value) {
                $success = true;
                $message = _l('updated_successfully');
            } else {
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function send_email_welcome_for_new_contact()
    {
        $data = $this->input->post();
        if ($data != 'null') {
            $value = $this->purchase_model->update_purchase_setting($data);
            if ($value) {
                $success = true;
                $message = _l('updated_successfully');
            } else {
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function reset_purchase_order_number_every_month()
    {
        $data = $this->input->post();
        if ($data != 'null') {
            $value = $this->purchase_model->update_purchase_setting($data);
            if ($value) {
                $success = true;
                $message = _l('updated_successfully');
            } else {
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function po_only_prefix_and_number()
    {
        $data = $this->input->post();
        if ($data != 'null') {
            $value = $this->purchase_model->update_purchase_setting($data);
            if ($value) {
                $success = true;
                $message = _l('updated_successfully');
            } else {
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * Gets the notes.
     *
     * @param      <type>  $id     The id of purchase order
     */
    public function get_notes($id)
    {
        $data['notes'] = $this->misc_model->get_notes($id, 'purchase_order');
        $this->load->view('admin/includes/sales_notes_template', $data);
    }
    public function get_notes_wo($id)
    {
        $data['notes'] = $this->misc_model->get_notes($id, 'wo_order');
        $this->load->view('admin/includes/sales_notes_template', $data);
    }
    /**
     * Gets the purchase contract notes.
     *
     * @param      <type>  $id     The id of purchase order
     */
    public function get_notes_pur_contract($id)
    {
        $data['notes'] = $this->misc_model->get_notes($id, 'pur_contract');
        $this->load->view('admin/includes/sales_notes_template', $data);
    }


    /**
     * Gets the purchase invoice notes.
     *
     * @param      <type>  $id     The id of purchase order
     */
    public function get_notes_pur_invoice($id)
    {
        $data['notes'] = $this->misc_model->get_notes($id, 'pur_invoice');
        $this->load->view('admin/includes/sales_notes_template', $data);
    }

    /**
     * Adds a note.
     *
     * @param        $rel_id  The purchase contract id
     */
    public function add_pur_contract_note($rel_id)
    {
        if ($this->input->post()) {
            $this->misc_model->add_note($this->input->post(), 'pur_contract', $rel_id);
            echo pur_html_entity_decode($rel_id);
        }
    }

    /**
     * Adds a note.
     *
     * @param        $rel_id  The purchase contract id
     */
    public function add_pur_invoice_note($rel_id)
    {
        if ($this->input->post()) {
            $this->misc_model->add_note($this->input->post(), 'pur_invoice', $rel_id);
            echo pur_html_entity_decode($rel_id);
        }
    }

    /**
     * Adds a note.
     *
     * @param      <type>  $rel_id  The purchase order id
     */
    public function add_note($rel_id)
    {
        if ($this->input->post()) {
            $this->misc_model->add_note($this->input->post(), 'purchase_order', $rel_id);
            echo pur_html_entity_decode($rel_id);
        }
    }
    /**
     * Adds a note.
     *
     * @param      <type>  $rel_id  The work order id
     */
    public function add_wo_note($rel_id)
    {
        if ($this->input->post()) {
            $this->misc_model->add_note($this->input->post(), 'wo_order', $rel_id);
            echo wo_html_entity_decode($rel_id);
        }
    }
    /**
     * Uploads a purchase order attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_order_attachment($id)
    {

        handle_purchase_order_file($id);

        redirect(admin_url('purchase/purchase_order/' . $id));
    }

    /**
     * Uploads a purchase order attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_request_attachment($id)
    {

        handle_purchase_request_file($id);

        redirect(admin_url('purchase/view_pur_request/' . $id . '?tab=attachment'));
    }


    /**
     * { preview purchase order file }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     * @return  view
     */
    public function file_purorder($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('purchase_order/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purorder_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_purorder_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * Adds a payment.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_payment($pur_order)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->purchase_model->add_payment($data, $pur_order);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(admin_url('purchase/purchase_order/' . $pur_order));
        }
    }

    /**
     * Adds a payment on PO.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_payment_on_po($pur_order)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->purchase_model->add_payment_on_po($data, $pur_order);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(admin_url('purchase/purchase_order/' . $pur_order));
        }
    }
    public function add_payment_on_wo($wo_order)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->purchase_model->add_payment_on_wo($data, $wo_order);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(admin_url('purchase/work_order/' . $wo_order));
        }
    }

    /**
     * { delete payment }
     *
     * @param      <type>  $id         The identifier
     * @param      <type>  $pur_order  The pur order
     * @return  redirect
     */
    public function delete_payment($id, $pur_order)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_order/' . $pur_order));
        }
        $response = $this->purchase_model->delete_payment($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('payment')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment')));
        }
        redirect(admin_url('purchase/purchase_order/' . $pur_order));
    }

    /**
     * { purchase order pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function purorder_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }

        $pur_request = $this->purchase_model->get_purorder_pdf_html($id);

        try {
            $pdf = $this->purchase_model->purorder_pdf($pur_request, $id);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pur_order = $this->purchase_model->get_pur_order($id);
        $vendor_name = get_vendor_name_by_id($pur_order->vendor);
        $pdf_name = $pur_order->pur_order_number . '-' . $vendor_name . '-' . $pur_order->pur_order_name . '.pdf';
        $pdf->Output($pdf_name, $type);
    }

    /**
     * { clear signature }
     *
     * @param      <type>  $id     The identifier
     */
    public function clear_signature($id)
    {
        if (has_permission('purchase_contracts', '', 'delete')) {
            $this->purchase_model->clear_signature($id);
        }

        redirect(admin_url('purchase/contract/' . $id));
    }

    /**
     * { Purchase reports }
     * 
     * @return view
     */
    public function reports()
    {
        if (!is_admin() && !has_permission('purchase_reports', '', 'view')) {
            access_denied('purchase');
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['departments'] = $this->departments_model->get();
        $data['title'] = _l('purchase_reports');
        $this->load->view('reports/manage_report', $data);
    }

    /**
     *  import goods report
     *  
     *  @return json
     */
    public function import_goods_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'tblitems.commodity_code as item_code',
                'tblitems.description as item_name',
                '(select pur_order_name from ' . db_prefix() . 'pur_orders where ' . db_prefix() . 'pur_orders.id = pur_order) as po_name',
                'total_money',
            ];
            $where = [];
            $custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_orders.order_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $currency = $this->currencies_model->get_base_currency();

            if ($this->input->post('report_currency')) {
                $report_currency = $this->input->post('report_currency');
                $base_currency = get_base_currency_pur();

                if ($report_currency == $base_currency->id) {
                    array_push($where, 'AND ' . db_prefix() . 'pur_orders.currency IN (0, ' . $report_currency . ')');
                } else {
                    array_push($where, 'AND ' . db_prefix() . 'pur_orders.currency = ' . $report_currency);
                }

                $currency = pur_get_currency_by_id($report_currency);
            }

            if ($this->input->post('products_services')) {
                $products_services  = $this->input->post('products_services');
                $_products_services = [];
                if (is_array($products_services)) {
                    foreach ($products_services as $product) {
                        if ($product != '') {
                            array_push($_products_services, $product);
                        }
                    }
                }
                if (count($_products_services) > 0) {
                    array_push($where, 'AND tblitems.id IN (' . implode(', ', $_products_services) . ')');
                }
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_order_detail';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'pur_order_detail.item_code',
                'LEFT JOIN ' . db_prefix() . 'pur_orders ON ' . db_prefix() . 'pur_orders.id = ' . db_prefix() . 'pur_order_detail.pur_order',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix() . 'items.id as item_id',
                db_prefix() . 'pur_order_detail.pur_order as po_id'
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('purchase/items/' . $aRow['item_id']) . '" target="_blank">' . $aRow['item_code'] . '</a>';

                $row[] = $aRow['item_name'];

                $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['po_id']) . '" target="_blank">' . $aRow['po_name'] . '</a>';




                $row[] = app_format_money($aRow['total_money'], $currency->name);
                $footer_data['total'] += $aRow['total_money'];

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    /**
     * Gets the where report period.
     *
     * @param      string  $field  The field
     *
     * @return     string  The where report period.
     */
    public function get_where_report_period($field = 'date')
    {
        return $this->purchase_model->get_where_report_period($field);
    }

    /**
     * get data Purchase statistics by number of purchase orders
     * 
     * @return     json
     */
    public function number_of_purchase_orders_analysis()
    {
        $year_report      = $this->input->post('year');
        echo json_encode($this->purchase_model->number_of_purchase_orders_analysis($year_report));
        die();
    }

    /**
     * get data Purchase statistics by cost
     * 
     * @return     json
     */
    public function cost_of_purchase_orders_analysis()
    {
        $this->load->model('currencies_model');
        $year_report      = $this->input->post('year');
        $report_currency = $this->input->post('report_currency');
        $currency = pur_get_currency_by_id($report_currency);

        $currency_name = '';
        $currency_unit = '';
        if ($currency) {
            $currency_name = $currency->name;
            $currency_unit = $currency->symbol;
        }
        echo json_encode([
            'data' => $this->purchase_model->cost_of_purchase_orders_analysis($year_report, $report_currency),
            'unit' => $currency_unit,
            'name' => $currency_name,
        ]);
        die();
    }

    /**
     * { table vendor contracts }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_contracts($vendor)
    {
        $this->app->get_table_data(module_views_path('purchase', 'contracts/table_contracts'), ['vendor' => $vendor]);
    }

    /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_pur_order($vendor)
    {
        $this->app->get_table_data(module_views_path('purchase', 'purchase_order/table_pur_order'), ['vendor' => $vendor]);
    }



    public function table_vendor_wo_order($vendor)
    {
        $this->app->get_table_data(module_views_path('purchase', 'work_order/table_wo_order'), ['vendor' => $vendor]);
    }
    /**
     * { delete vendor admin }
     *
     * @param      <type>  $customer_id  The customer identifier
     * @param      <type>  $staff_id     The staff identifier
     */
    public function delete_vendor_admin($customer_id, $staff_id)
    {
        if (!has_permission('purchase_vendors', '', 'create') && !has_permission('purchase_vendors', '', 'edit')) {
            access_denied('vendors');
        }

        $this->db->where('vendor_id', $customer_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete(db_prefix() . 'pur_vendor_admin');
        redirect(admin_url('purchase/vendor/' . $customer_id) . '?tab=vendor_admins');
    }

    /**
     * table commodity list
     * 
     * @return array
     */
    public function table_item_list()
    {
        $this->app->get_table_data(module_views_path('purchase', 'items/table_item_list'));
    }

    /**
     * item list
     * @param  integer $id 
     * @return load view
     */
    public function items($id = '')
    {
        $this->load->model('departments_model');
        $this->load->model('staff_model');


        $data['units'] = $this->purchase_model->get_unit_add_item();
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups'] = $this->purchase_model->get_sub_group();
        $data['area'] = $this->purchase_model->get_area();
        $data['specification'] = $this->purchase_model->get_specification();
        $data['title'] = _l('item_list');

        $data['item_id'] = $id;

        $this->load->view('items/item_list', $data);
    }

    /**
     * get item data ajax
     * @param  integer $id 
     * @return view
     */
    public function get_item_data_ajax($id)
    {

        $data['id'] = $id;
        $data['item'] = $this->purchase_model->get_item($id);
        $data['item_file'] = $this->purchase_model->get_item_attachments($id);
        if (is_numeric($data['item']->from_vendor_item)) {
            $data['vendor_image'] = $this->purchase_model->get_vendor_item_file($data['item']->from_vendor_item);
        }

        $this->load->view('items/item_detail', $data);
    }

    /**
     * add item list
     * @param  integer $id 
     * @return redirect
     */
    public function add_item_list($id = '')
    {
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();

            if (!$this->input->post('id')) {

                $mess = $this->purchase_model->add_item($data);
                if ($mess) {
                    set_alert('success', _l('added_successfully') . _l('item_list'));
                } else {
                    set_alert('warning', _l('Add_item_list_false'));
                }
                redirect(admin_url('purchase/item_list'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->add_purchase($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully') . _l('item_list'));
                } else {
                    set_alert('warning', _l('updated_item_list_false'));
                }

                redirect(admin_url('purchase/item_list'));
            }
        }
    }

    /**
     * delete item
     * @param  integer $id 
     * @return redirect
     */
    public function delete_item($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/item_list'));
        }
        $response = $this->purchase_model->delete_item($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('item_list')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('item_list')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('item_list')));
        }
        redirect(admin_url('purchase/item_list'));
    }

    /**
     * Gets the commodity barcode.
     */
    public function get_commodity_barcode()
    {
        $commodity_barcode = $this->purchase_model->generate_commodity_barcode();

        echo json_encode([
            $commodity_barcode
        ]);
        die();
    }

    /**
     * commodity list add edit
     * @param  integer $id
     * @return json
     */
    public function commodity_list_add_edit($id = '')
    {
        $data = $this->input->post();
        if ($data) {
            if (!isset($data['id'])) {
                $ids = $this->purchase_model->add_commodity_one_item($data);
                if ($ids) {

                    $this->export_tblitems_to_json();

                    // handle commodity list add edit file
                    $success = true;
                    $message = _l('added_successfully');
                    set_alert('success', $message);
                    /*upload multifile*/
                    echo json_encode([
                        'url'       => admin_url('purchase/items/' . $ids),
                        'commodityid' => $ids,
                    ]);
                    die;
                }
                echo json_encode([
                    'url' => admin_url('purchase/items'),
                ]);
                die;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->update_commodity_one_item($data, $id);

                /*update file*/

                if ($success == true) {
                    $this->export_tblitems_to_json();
                    $message = _l('updated_successfully');
                    set_alert('success', $message);
                }

                echo json_encode([
                    'url'       => admin_url('purchase/items/' . $id),
                    'commodityid' => $id,
                ]);
                die;
            }
        }
    }

    /**
     * add commodity attachment
     * @param  integer $id
     * @return json
     */
    public function add_commodity_attachment($id)
    {

        handle_item_attachments($id);
        echo json_encode([
            'url' => admin_url('purchase/items'),
        ]);
    }

    /**
     * get commodity file url 
     * @param  integer $commodity_id
     * @return json
     */
    public function get_commodity_file_url($commodity_id)
    {
        $arr_commodity_file = $this->purchase_model->get_item_attachments($commodity_id);
        /*get images old*/
        $images_old_value = '';

        $custom_fields_html = render_custom_fields('items', $commodity_id, [], ['items_pr' => true]);

        $list = $this->purchase_model->get_item_longdescriptions($commodity_id);

        if (isset($list)) {
            $long_descriptions = strip_tags($list->long_description);
            $description = $list->long_description;
        } else {
            $long_descriptions = '';
            $description = '';
        }


        if (count($arr_commodity_file) > 0) {
            foreach ($arr_commodity_file as $key => $value) {
                $images_old_value .= '<div class="dz-preview dz-image-preview image_old' . $value["id"] . '">';

                $images_old_value .= '<div class="dz-image">';
                if (file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $value["rel_id"] . '/' . $value["file_name"])) {
                    $images_old_value .= '<img class="image-w-h" data-dz-thumbnail alt="' . $value["file_name"] . '" src="' . site_url('modules/purchase/uploads/item_img/' . $value["rel_id"] . '/' . $value["file_name"]) . '">';
                } else {
                    $images_old_value .= '<img class="image-w-h" data-dz-thumbnail alt="' . $value["file_name"] . '" src="' . site_url('modules/warehouse/uploads/item_img/' . $value["rel_id"] . '/' . $value["file_name"]) . '">';
                }
                $images_old_value .= '</div>';

                $images_old_value .= '<div class="dz-error-mark">';
                $images_old_value .= '<a class="dz-remove" data-dz-remove>Remove file';
                $images_old_value .= '</a>';
                $images_old_value .= '</div>';

                $images_old_value .= '<div class="remove_file">';
                $images_old_value .= '<a href="#" class="text-danger" onclick="delete_contract_attachment(this,' . $value["id"] . '); return false;"><i class="fa fa fa-times"></i></a>';
                $images_old_value .= '</div>';

                $images_old_value .= '</div>';
            }
        }

        if (isset($list->from_vendor_item) && is_numeric($list->from_vendor_item)) {
            $vendor_image = $this->purchase_model->get_vendor_item_file($list->from_vendor_item);
            if (count($vendor_image) > 0) {
                foreach ($vendor_image as $key => $value) {
                    $images_old_value .= '<div class="dz-preview dz-image-preview image_old' . $value["id"] . '">';

                    $images_old_value .= '<div class="dz-image">';
                    if (file_exists(PURCHASE_PATH . 'vendor_items/' . $list->from_vendor_item . '/' . $value['file_name'])) {
                        $images_old_value .= '<img class="image-w-h" data-dz-thumbnail alt="' . $value["file_name"] . '" src="' . site_url('modules/purchase/uploads/vendor_items/' . $value['rel_id'] . '/' . $value['file_name']) . '">';
                    }
                    $images_old_value .= '</div>';

                    $images_old_value .= '<div class="dz-error-mark">';
                    $images_old_value .= '<a class="dz-remove" data-dz-remove>Remove file';
                    $images_old_value .= '</a>';
                    $images_old_value .= '</div>';



                    $images_old_value .= '</div>';
                }
            }
        }



        echo json_encode([
            'arr_images' => $images_old_value,
            'custom_fields_html' => $custom_fields_html,
            'description' => $description,
            'long_descriptions' => $long_descriptions,
        ]);
        die();
    }

    /**
     * delete commodity file
     * @param  integer $attachment_id
     * @return json
     */
    public function delete_commodity_file($attachment_id)
    {
        if (!has_permission('purchase_items', '', 'delete') && !is_admin()) {
            access_denied('purchase');
        }

        $file = $this->misc_model->get_file($attachment_id);
        echo json_encode([
            'success' => $this->purchase_model->delete_commodity_file($attachment_id),
        ]);
    }

    /**
     * unit type 
     * @param  integer $id 
     * @return redirect    
     */
    public function unit_type($id = '')
    {
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();

            if (!$this->input->post('id')) {
                $mess = $this->purchase_model->add_unit_type($data);
                if ($mess) {
                    set_alert('success', _l('added_successfully') . ' ' . _l('unit_type'));
                } else {
                    set_alert('warning', _l('Add_unit_type_false'));
                }
                redirect(admin_url('purchase/setting?group=units'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->add_unit_type($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully') . ' ' . _l('unit_type'));
                } else {
                    set_alert('warning', _l('updated_unit_type_false'));
                }

                redirect(admin_url('purchase/setting?group=units'));
            }
        }
    }


    /**
     * delete unit type 
     * @param  integer $id
     * @return redirect
     */
    public function delete_unit_type($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=units'));
        }
        $response = $this->purchase_model->delete_unit_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('unit_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('unit_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('unit_type')));
        }
        redirect(admin_url('purchase/setting?group=units'));
    }

    /**
     * delete commodity
     * @param  integer $id 
     * @return redirect
     */
    public function delete_commodity($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/items'));
        }
        $response = $this->purchase_model->delete_commodity($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('commodity_list')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('commodity_list')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('commodity_list')));
        }
        redirect(admin_url('purchase/items'));
    }

    /**
     * Adds an expense.
     */
    public function add_invoice_expense()
    {
        if ($this->input->post()) {
            $this->load->model('expenses_model');
            $data = $this->input->post();

            if (isset($data['pur_invoice'])) {
                $pur_invoice = $data['pur_invoice'];
                $select_invoice = $data['select_invoice'];
                $applied_to_invoice = $data['applied_to_invoice'];
                $data['vbt_id'] = $pur_invoice;
                unset($data['pur_invoice']);
                unset($data['select_invoice']);
                unset($data['applied_to_invoice']);
            }

            $id = $this->expenses_model->add($data);

            if ($id) {

                $this->purchase_model->mark_converted_pur_invoice($pur_invoice, $id);

                if ($select_invoice == "create_invoice") {
                    $invoiceid = $this->expenses_model->convert_to_invoice($id);
                    if ($invoiceid) {
                        set_alert('success', _l('expense_converted_to_invoice'));
                        echo json_encode([
                            'url' => admin_url('invoices/invoice/' . $invoiceid),
                            'expenseid' => $id,
                        ]);
                    }
                } else {
                    $input = array();
                    $input['invoice_id'] = $applied_to_invoice;
                    $input['expense_id'] = $id;
                    $invoiceid = $this->expenses_model->applied_to_invoice($input);
                    if ($invoiceid) {
                        set_alert('success', _l('expense_applied_to_invoice'));
                        echo json_encode([
                            'url' => admin_url('invoices/invoice/' . $invoiceid),
                            'expenseid' => $id,
                        ]);
                    }
                }

                // set_alert('success', _l('converted', _l('expense')));
                // echo json_encode([
                //     'url'       => admin_url('expenses/list_expenses/' . $id),
                //     'expenseid' => $id,
                // ]);
                die;
            }
        }
    }

    /**
     * Adds an expense.
     */
    public function add_expense()
    {
        if ($this->input->post()) {
            $this->load->model('expenses_model');
            $data = $this->input->post();

            if (isset($data['pur_order'])) {
                $pur_order = $data['pur_order'];
                $data['po_id'] = $pur_order;
                unset($data['pur_order']);
            }

            $id = $this->expenses_model->add($data);

            if ($id) {

                $this->purchase_model->mark_converted_pur_order($pur_order, $id);

                set_alert('success', _l('converted', _l('expense')));
                echo json_encode([
                    'url'       => admin_url('expenses/list_expenses/' . $id),
                    'expenseid' => $id,
                ]);
                die;
            }
        }
    }
    public function add_expense_wo()
    {
        if ($this->input->post()) {
            $this->load->model('expenses_model');
            $data = $this->input->post();

            if (isset($data['wo_order'])) {
                $wo_order = $data['wo_order'];
                $data['wo_id'] = $wo_order;
                unset($data['wo_order']);
            }

            $id = $this->expenses_model->add($data);

            if ($id) {

                $this->purchase_model->mark_converted_wo_order($wo_order, $id);

                set_alert('success', _l('converted', _l('expense')));
                echo json_encode([
                    'url'       => admin_url('expenses/list_expenses/' . $id),
                    'expenseid' => $id,
                ]);
                die;
            }
        }
    }


    /**
     * Uploads an attachment.
     *
     * @param      <type>  $id     The identifier
     */
    public function upload_attachment($id)
    {
        handle_pur_vendor_attachments_upload($id);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     */
    public function file_pur_vendor($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('vendors/_file', $data);
    }

    /**
     * { delete ic attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_ic_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_ic_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Change client status / active / inactive */
    public function change_contact_status($id, $status)
    {
        if (has_permission('purchase_vendors', '', 'edit') || is_vendor_admin(get_user_id_by_contact_id_pur($id)) || is_admin()) {
            if ($this->input->is_ajax_request()) {
                $this->purchase_model->change_contact_status($id, $status);
            }
        }
    }

    /**
     * { vendor items }
     */
    public function vendor_items()
    {
        if (!has_permission('purchase_vendor_items', '', 'view') && !is_admin() && !has_permission('purchase_vendor_items', '', 'view_own')) {
            access_denied('vendor_items');
        }

        $data['title'] = _l('vendor_items');
        $data['vendors'] = $this->purchase_model->get_vendor();

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        //$data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();
        $this->load->view('vendor_items/manage', $data);
    }

    /**
     *  vendor item table
     *  
     *  @return json
     */
    public function vendor_items_table()
    {
        if ($this->input->is_ajax_request()) {

            $select = [
                db_prefix() . 'pur_vendor_items.id as vendor_items_id',
                db_prefix() . 'pur_vendor_items.items as items',
                db_prefix() . 'pur_vendor.company as company',
                db_prefix() . 'pur_vendor_items.add_from as pur_vendor_items_addedfrom',

            ];
            $where = [];


            if ($this->input->post('vendor_filter')) {
                $vendor_filter  = $this->input->post('vendor_filter');
                array_push($where, 'AND vendor IN (' . implode(',', $vendor_filter) . ')');
            }

            if ($this->input->post('group_items_filter')) {
                $group_items_filter  = $this->input->post('group_items_filter');
                array_push($where, 'AND group_items IN (' . implode(',', $group_items_filter) . ')');
            }

            if ($this->input->post('items_filter')) {
                $items_filter  = $this->input->post('items_filter');
                array_push($where, 'AND items = ' . $items_filter);
            }

            if (!has_permission('purchase_vendor_items', '', 'view')) {
                array_push($where, 'AND ' . db_prefix() . 'pur_vendor_items.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ')');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_vendor_items';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_vendor_items.vendor',
                'LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'pur_vendor_items.items'
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'pur_vendor.userid as userid', 'datecreate', 'description', 'commodity_code']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total' => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['vendor_items_id'] . '"><label></label></div>';

                $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';

                $row[] = '<a href="' . admin_url('purchase/items/' . $aRow['items']) . '" >' . $aRow['commodity_code'] . ' - ' . $aRow['description'] . '</a>';

                $row[] = _d($aRow['datecreate']);

                $options = icon_btn('purchase/delete_vendor_items/' . $aRow['vendor_items_id'], 'remove', 'btn-danger', ['title' => _l('delete')]);

                $row[] =  $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * new vendor items
     */
    public function new_vendor_items()
    {
        if (!has_permission('purchase_vendor_items', '', 'create') && !is_admin()) {
            access_denied('vendor_items');
        }
        $this->load->model('staff_model');

        if ($this->input->post()) {
            $data                = $this->input->post();
            if (!has_permission('purchase_vendor_items', '', 'create')) {
                access_denied('vendor_items');
            }
            $success = $this->purchase_model->add_vendor_items($data);
            if ($success) {
                set_alert('success', _l('added_successfully', _l('vendor_items')));
            }
            redirect(admin_url('purchase/vendor_items'));
        }
        $data['title'] = _l('vendor_items');

        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();

        $this->load->view('vendor_items/vendor_items', $data);
    }

    /**
     * { group item change }
     */
    public function group_it_change($group = '')
    {
        if ($group != '') {
            $html = '';
            if (total_rows(db_prefix() . 'items', ['group_id' => $group]) <= ajax_on_total_items()) {
                $list_items = $this->purchase_model->get_item_by_group($group);
                if (count($list_items) > 0) {
                    foreach ($list_items as $item) {
                        $html .= '<option value="' . $item['id'] . '" selected>' . $item['commodity_code'] . ' - ' . $item['description'] . '</option>';
                    }
                }
            }

            echo json_encode([
                'html' => $html,
            ]);
        } else {

            $html = '';
            if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
                $items = $this->purchase_model->get_item();
                if (count($items) > 0) {
                    foreach ($items as $it) {
                        $html .= '<option value="' . $it['id'] . '">' . $it['commodity_code'] . ' - ' . $it['description'] . '</option>';
                    }
                }
            }

            echo json_encode([
                'html' => $html,
            ]);
        }
    }

    /**
     * { delete vendor item  }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_vendor_items($id)
    {
        if (!has_permission('purchase_vendor_items', '', 'delete') && !is_admin()) {
            access_denied('vendor_items');
        }
        if (!$id) {
            redirect(admin_url('purchase/vendor_items'));
        }

        $success = $this->purchase_model->delete_vendor_items($id);
        if ($success == true) {
            set_alert('success', _l('deleted', _l('vendor_items')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('vendor_items')));
        }
        redirect(admin_url('purchase/vendor_items'));
    }

    /**
     * purchase delete bulk action
     * @return
     */
    public function purchase_delete_bulk_action()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $total_deleted = 0;
        $total_updated = 0;
        $total_cloned = 0;
        if ($this->input->post()) {

            $ids                   = $this->input->post('ids');
            $rel_type                   = $this->input->post('rel_type');
            /*check permission*/
            switch ($rel_type) {
                case 'commodity_list':
                    if (!has_permission('purchase_items', '', 'delete') && !is_admin()) {
                        access_denied('commodity_list');
                    }
                    break;

                case 'vendors':
                    if (!has_permission('purchase_vendors', '', 'delete') && !is_admin()) {
                        access_denied('vendors');
                    }
                    break;

                case 'vendor_items':
                    if (!has_permission('purchase_vendor_items', '', 'delete') && !is_admin()) {
                        access_denied('vendor_items');
                    }
                    break;

                case 'change_item_selling_price':
                    if (!has_permission('purchase_items', '', 'edit') && !is_admin()) {
                        access_denied('commodity_list');
                    }
                    break;

                case 'change_item_purchase_price':
                    if (!has_permission('purchase_items', '', 'edit') && !is_admin()) {
                        access_denied('commodity_list');
                    }
                    break;

                default:
                    break;
            }

            /*delete data*/
            if ($this->input->post('mass_delete') && $this->input->post('mass_delete') == 'true') {
                if (is_array($ids)) {
                    foreach ($ids as $id) {

                        switch ($rel_type) {
                            case 'commodity_list':
                                if ($this->purchase_model->delete_commodity($id)) {
                                    $total_deleted++;
                                    break;
                                } else {
                                    break;
                                }

                            case 'vendors':
                                if ($this->purchase_model->delete_vendor($id)) {
                                    $total_deleted++;
                                    break;
                                } else {
                                    break;
                                }

                            case 'vendor_items':
                                if ($this->purchase_model->delete_vendor_items($id)) {
                                    $total_deleted++;
                                    break;
                                } else {
                                    break;
                                }

                            default:

                                break;
                        }
                    }
                }
                /*return result*/
                switch ($rel_type) {
                    case 'commodity_list':
                        set_alert('success', _l('total_commodity_list') . ": " . $total_deleted);
                        break;

                    case 'vendors':
                        set_alert('success', _l('total_vendors_list') . ": " . $total_deleted);
                        break;

                    case 'vendor_items':
                        set_alert('success', _l('total_vendor_items_list') . ": " . $total_deleted);
                        break;

                    default:
                        break;
                }
            }

            // Clone items
            if ($this->input->post('clone_items') && $this->input->post('clone_items') == 'true') {
                if (is_array($ids)) {
                    foreach ($ids as $id) {

                        switch ($rel_type) {
                            case 'commodity_list':
                                if ($this->purchase_model->clone_item($id)) {
                                    $total_cloned++;
                                    break;
                                } else {
                                    break;
                                }

                            default:

                                break;
                        }
                    }
                }
                /*return result*/
                switch ($rel_type) {
                    case 'commodity_list':
                        set_alert('success', _l('total_commodity_list') . ": " . $total_cloned);
                        break;

                    default:
                        break;
                }
            }

            // update selling price, purchase price
            if (($this->input->post('change_item_selling_price')) || ($this->input->post('change_item_purchase_price'))) {

                if (is_array($ids)) {
                    foreach ($ids as $id) {

                        switch ($rel_type) {
                            case 'change_item_selling_price':
                                if ($this->purchase_model->commodity_udpate_profit_rate($id, $this->input->post('selling_price'), 'selling_percent')) {
                                    $total_updated++;
                                    break;
                                } else {
                                    break;
                                }

                            case 'change_item_purchase_price':
                                if ($this->purchase_model->commodity_udpate_profit_rate($id, $this->input->post('purchase_price'), 'purchase_percent')) {
                                    $total_updated++;
                                    break;
                                } else {
                                    break;
                                }


                            default:

                                break;
                        }
                    }
                }

                /*return result*/
                switch ($rel_type) {
                    case 'change_item_selling_price':
                        set_alert('success', _l('total_commodity_list') . ": " . $total_updated);
                        break;

                    case 'change_item_purchase_price':
                        set_alert('success', _l('total_commodity_list') . ": " . $total_updated);
                        break;


                    default:
                        break;
                }
            }
        }
    }

    /**
     * { pur order setting }
     * @return redirect
     */
    public function pur_order_setting()
    {
        if (!is_admin() && !has_permission('purchase_settings', '', 'edit')) {
            access_denied('purchase');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $data['terms_and_conditions'] = $this->input->post('terms_and_conditions', false);
            $update = $this->purchase_model->update_po_number_setting($data);

            if ($update == true) {
                set_alert('success', _l('updated_successfully'));
            } else {
                set_alert('warning', _l('updated_fail'));
            }

            redirect(admin_url('purchase/setting'));
        }
    }

    /**
     * { pur order setting }
     * @return redirect
     */
    public function update_order_return_setting()
    {
        if (!is_admin() && !has_permission('purchase_settings', '', 'edit')) {
            access_denied('purchase');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $update = $this->purchase_model->update_order_return_setting($data);

            if ($update == true) {
                set_alert('success', _l('updated_successfully'));
            } else {
                set_alert('warning', _l('updated_fail'));
            }

            redirect(admin_url('purchase/setting?group=order_return'));
        }
    }


    public function get_html_approval_setting($id = '')
    {
        $html = '';
        $staffs = $this->staff_model->get();
        $approver = [
            0 => ['id' => 'direct_manager', 'name' => _l('direct_manager')],
            1 => ['id' => 'head_of_department', 'name' => _l('department_manager')],
            2 => ['id' => 'staff', 'name' => _l('staff')]
        ];
        $action = [
            1 => ['id' => 'approve', 'name' => _l('approve')],
            0 => ['id' => 'sign', 'name' => _l('sign')],
        ];

        $hr_record_status = 0;
        if (get_status_modules_pur('hr_profile') == true) {
            $hr_record_status = 1;
        }
        if (is_numeric($id)) {
            $approval_setting = $this->purchase_model->get_approval_setting($id);

            $setting = json_decode($approval_setting->setting);

            $approver_md = '1';
            $hide_class = 'hide';
            $staff_md = '8';
            $approver_default = 'staff';
            $staff_hide = '';
            if ($hr_record_status == 1) {
                $approver_md = '4';
                $staff_md = '4';
                $hide_class = '';
                $approver_default = '';
                $staff_hide = 'hide';
            }

            foreach ($setting as $key => $value) {

                if ($value->approver == 'staff') {
                    $staff_hide = '';
                } else {
                    $staff_hide = 'hide';
                }
                if ($key == 0) {

                    $html .= '<div id="item_approve">
                                    <div class="col-md-11">
                                    <div class="col-md-' . $approver_md . ' ' . $hide_class . '"> ' .
                        render_select('approver[' . $key . ']', $approver, array('id', 'name'), 'approver', $value->approver, array('data-id' => '0', 'required' => 'true'), [], '', 'approver_class') . '
                                    </div>
                                    <div class="col-md-' . $staff_md . ' ' . $staff_hide . '" id="is_staff_0">
                                    ' . render_select('staff[' . $key . ']', $staffs, array('staffid', 'full_name'), 'staff', $value->staff) . '
                                    </div>
                                    <div class="col-md-4">
                                        ' . render_select('action[' . $key . ']', $action, array('id', 'name'), 'action', $value->action) . ' 
                                    </div>
                                    </div>
                                    <div class="col-md-1 btn_apr">
                                    <span class="pull-bot">
                                        <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                        </span>
                                  </div>
                                </div>';
                } else {
                    $html .= '<div id="item_approve">
                                    <div class="col-md-11">
                                    <div class="col-md-' . $approver_md . ' ' . $hide_class . '"">
                                        ' .
                        render_select('approver[' . $key . ']', $approver, array('id', 'name'), 'approver', $value->approver, array('data-id' => '0', 'required' => 'true'), [], '', 'approver_class') . ' 
                                    </div>
                                    <div class="col-md-' . $staff_md . ' ' . $staff_hide . '" id="is_staff_' . $key . '">
                                        ' . render_select('staff[' . $key . ']', $staffs, array('staffid', 'full_name'), 'staff', $value->staff) . ' 
                                    </div>
                                    <div class="col-md-4">
                                        ' . render_select('action[' . $key . ']', $action, array('id', 'name'), 'action', $value->action) . ' 
                                    </div>
                                    </div>
                                    <div class="col-md-1 btn_apr">
                                    <span class="pull-bot">
                                        <button name="add" class="btn remove_vendor_requests btn-danger" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                        </span>
                                  </div>
                                </div>';
                }
            }
        } else {

            $approver_md = '1';
            $hide_class = 'hide';
            $staff_md = '8';
            $approver_default = 'staff';
            $staff_hide = '';
            if ($hr_record_status == 1) {
                $approver_md = '4';
                $staff_md = '4';
                $hide_class = '';
                $approver_default = '';
                $staff_hide = 'hide';
            }
            $html .= '<div id="item_approve">
                        <div class="col-md-11">
                        <div class="col-md-' . $approver_md . ' ' . $hide_class . ' "> ' .
                render_select('approver[0]', $approver, array('id', 'name'), 'approver', $approver_default, array('data-id' => '0', 'required' => 'true'), [], '', 'approver_class') . '
                        </div>
                        <div class="col-md-' . $staff_md . ' ' . $staff_hide . '" id="is_staff_0">
                        ' . render_select('staff[0]', $staffs, array('staffid', 'full_name'), 'staff') . '
                        </div>
                        <div class="col-md-4">
                            ' . render_select('action[0]', $action, array('id', 'name'), 'action', 'approve') . ' 
                        </div>
                        </div>
                        <div class="col-md-1 btn_apr">
                        <span class="pull-bot">
                            <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                            </span>
                      </div>
                    </div>';
        }

        echo json_encode([
            $html
        ]);
    }

    /**
     * commodty group type
     * @param  integer $id
     * @return redirect
     */
    public function commodity_group_type($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();

            if (!$this->input->post('id')) {

                $mess = $this->purchase_model->add_commodity_group_type($data);
                if ($mess) {
                    set_alert('success', _l('added_successfully') . _l('commodity_group_type'));
                } else {
                    set_alert('warning', _l('Add_commodity_group_type_false'));
                }
                redirect(admin_url('purchase/setting?group=commodity_group'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->add_commodity_group_type($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully') . _l('commodity_group_type'));
                } else {
                    set_alert('warning', _l('updated_commodity_group_type_false'));
                }

                redirect(admin_url('purchase/setting?group=commodity_group'));
            }
        }
    }
    public function area($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();

            if (!$this->input->post('area_id')) {

                $mess = $this->purchase_model->add_area($data);
                if ($mess) {
                    set_alert('success', _l('added_successfully'));
                } else {
                    set_alert('warning', _l('add_area_false'));
                }
                redirect(admin_url('purchase/setting?group=area'));
            } else {
                $success = $this->purchase_model->update_area($data);
                if ($success) {
                    set_alert('success', _l('updated_successfully'));
                } else {
                    set_alert('warning', _l('update_area_false'));
                }

                redirect(admin_url('purchase/setting?group=area'));
            }
        }
    }

    /**
     * delete commodity group type
     * @param  integer $id
     * @return redirect
     */
    public function delete_commodity_group_type($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=commodity_group'));
        }
        $response = $this->purchase_model->delete_commodity_group_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('commodity_group_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('commodity_group_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('commodity_group_type')));
        }
        redirect(admin_url('purchase/setting?group=commodity_group'));
    }
    public function delete_area($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=area'));
        }
        $response = $this->purchase_model->delete_area($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('area')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('area')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('area')));
        }
        redirect(admin_url('purchase/setting?group=area'));
    }
    /**
     * sub group
     * @param  integer $id
     * @return redirect
     */
    public function sub_group($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();

            if (!$this->input->post('id')) {

                $mess = $this->purchase_model->add_sub_group($data);
                if ($mess) {
                    set_alert('success', _l('added_successfully') . ' ' . _l('sub_group'));
                } else {
                    set_alert('warning', _l('Add_sub_group_false'));
                }
                redirect(admin_url('purchase/setting?group=sub_group'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->add_sub_group($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully') . ' ' . _l('sub_group'));
                } else {
                    set_alert('warning', _l('updated_sub_group_false'));
                }

                redirect(admin_url('purchase/setting?group=sub_group'));
            }
        }
    }

    /**
     * delete sub group
     * @param  integer $id
     * @return redirect
     */
    public function delete_sub_group($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=sub_group'));
        }
        $response = $this->purchase_model->delete_sub_group($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('sub_group')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('sub_group')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('sub_group')));
        }
        redirect(admin_url('purchase/setting?group=sub_group'));
    }

    /**
     * get subgroup fill data
     * @return html 
     */
    public function get_subgroup_fill_data()
    {
        $data = $this->input->post();

        $subgroup = $this->purchase_model->list_subgroup_by_group($data['group_id']);

        echo json_encode([
            'subgroup' => $subgroup
        ]);
    }

    /**
     * { copy public link }
     *
     * @param      string  $id     The identifier
     */
    public function copy_public_link($id)
    {
        $pur_order = $this->purchase_model->get_pur_order($id);
        $copylink = '';
        if ($pur_order) {
            if ($pur_order->hash != '' && $pur_order->hash != null) {
                $copylink = site_url('purchase/vendors_portal/pur_order/' . $id . '/' . $pur_order->hash);
            } else {
                $hash = app_generate_hash();
                $copylink = site_url('purchase/vendors_portal/pur_order/' . $id . '/' . $hash);
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'pur_orders', ['hash' => $hash,]);
            }
        }

        echo json_encode([
            'copylink' => $copylink,
        ]);
    }

    /**
     * { copy public link pur request }
     *
     * @param      string  $id     The identifier
     */
    public function copy_public_link_pur_request($id)
    {
        $pur_request = $this->purchase_model->get_purchase_request($id);
        $copylink = '';
        if ($pur_request) {
            if ($pur_request->hash != '' && $pur_request->hash != null) {
                $copylink = site_url('purchase/vendors_portal/pur_request/' . $id . '/' . $pur_request->hash);
            } else {
                $hash = app_generate_hash();
                $copylink = site_url('purchase/vendors_portal/pur_request/' . $id . '/' . $hash);
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'pur_request', ['hash' => $hash,]);
            }
        }

        echo json_encode([
            'copylink' => $copylink,
        ]);
    }

    /**
     * { file pur vendor }
     *
     * @param       $id      The identifier
     * @param       $rel_id  The relative identifier
     */
    public function file_pur_contract($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('contracts/_file', $data);
    }

    /**
     * { delete purchase contract attachment }
     *
     * @param        $id     The identifier
     */
    public function delete_pur_contract_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_pur_contract_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * { vendor category form }
     * @return redirect
     */
    public function vendor_cate()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $id = $this->purchase_model->add_vendor_category($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('vendor_category'));
                    set_alert('success', $message);
                }
                redirect(admin_url('purchase/setting?group=vendor_category'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->update_vendor_category($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('vendor_category'));
                    set_alert('success', $message);
                }
                redirect(admin_url('purchase/setting?group=vendor_category'));
            }
            die;
        }
    }

    /**
     * delete job_position
     * @param  integer $id
     * @return redirect
     */
    public function delete_vendor_category($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=vendor_category'));
        }
        $response = $this->purchase_model->delete_vendor_category($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('vendor_category')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('vendor_category')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('vendor_category')));
        }
        redirect(admin_url('purchase/setting?group=vendor_category'));
    }

    /**
     * Uploads a purchase estimate attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_estimate_attachment($id)
    {

        handle_purchase_estimate_file($id);

        redirect(admin_url('purchase/quotations/' . $id));
    }

    /**
     * { preview purchase estimate file }
     *
     * @param        $id      The identifier
     * @param        $rel_id  The relative identifier
     * @return  view
     */
    public function file_pur_estimate($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('quotations/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_estimate_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_estimate_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * Determines if vendor code exists.
     */
    public function vendor_code_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $id = $this->input->post('userid');
                if ($id != '') {
                    $this->db->where('userid', $id);
                    $pur_vendor = $this->db->get(db_prefix() . 'pur_vendor')->row();
                    if ($pur_vendor->vendor_code == $this->input->post('vendor_code')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('vendor_code', $this->input->post('vendor_code'));
                $total_rows = $this->db->count_all_results(db_prefix() . 'pur_vendor    ');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /**
     * { dpm name in pur request number }
     *
     * @param        $dpm    The dpm
     */
    public function dpm_name_in_pur_request_number($dpm)
    {
        $this->load->model('departments_model');
        $department = $this->departments_model->get($dpm);
        $name_rs = '';
        if ($department) {
            $name_repl = str_replace(' ', '', $department->name);
            $name_rs = strtoupper($name_repl);
        }

        echo json_encode([
            'rs' => $name_rs,
        ]);
    }

    /**
     * { update customfield po }
     *
     * @param        $id     The identifier
     */
    public function update_customfield_po($id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->purchase_model->update_customfield_po($id, $data);
            if ($success) {
                $message = _l('updated_successfully', _l('vendor_category'));
                set_alert('success', $message);
            }
            redirect(admin_url('purchase/purchase_order/' . $id));
        }
    }

    /**
     * { po voucher }
     */
    public function po_voucher()
    {

        $po_voucher = $this->purchase_model->get_po_voucher_html();

        try {
            $pdf = $this->purchase_model->povoucher_pdf($po_voucher);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('PO_voucher.pdf', $type);
    }


    /**
     *  po voucher report
     *  
     *  @return json
     */
    public function po_voucher_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'pur_order_number',
                'order_date',
                'type',
                'project',
                'department',
                'vendor',
                'approve_status',
                'delivery_status',
            ];
            $where = [];
            $custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_orders.order_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }



            $currency = $this->currencies_model->get_base_currency();
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_orders';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'pur_orders.department',
                'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'pur_orders.project',
                'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_orders.vendor',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix() . 'pur_orders.id as id',
                db_prefix() . 'departments.name as department_name',
                db_prefix() . 'projects.name as project_name',
                db_prefix() . 'pur_vendor.company as vendor_name',
                'total',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" target="_blank">' . $aRow['pur_order_number'] . '</a>';

                $row[] = _d($aRow['order_date']);

                $row[] = _l($aRow['type']);

                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project']) . '" target="_blank">' . $aRow['project_name'] . '</a>';

                $row[] = $aRow['department_name'];

                $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';

                $row[] = get_status_approve($aRow['approve_status']);

                $delivery_status = '';
                if ($aRow['delivery_status'] == 0) {
                    $delivery_status = '<span class="label label-danger">' . _l('undelivered') . '</span>';
                } elseif ($aRow['delivery_status'] == 1) {
                    $delivery_status = '<span class="label label-success">' . _l('delivered') . '</span>';
                }
                $row[] = $delivery_status;

                $paid = $aRow['total'] - purorder_inv_left_to_pay($aRow['id']);

                $percent = 0;

                if ($aRow['total'] > 0) {

                    $percent = ($paid / $aRow['total']) * 100;
                }



                $row[] = '<div class="progress">

                              <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"

                              aria-valuemin="0" aria-valuemax="100" style="width:' . round($percent) . '%">

                               ' . round($percent) . ' % 

                              </div>

                            </div>';

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     *  po voucher report
     *  
     *  @return json
     */
    public function po_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'pur_order_number',
                'order_date',
                'department',
                'vendor',
                'approve_status',
                'subtotal',
                'total_tax',
                'total',
            ];
            $where = [];
            $custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_orders.order_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if (
                $this->input->post('pur_vendor')
                && count($this->input->post('pur_vendor')) > 0
            ) {
                array_push($where, 'AND vendor IN (' . implode(',', $this->input->post('pur_vendor')) . ')');
            }

            if ($this->input->post('pur_status') && count($this->input->post('pur_status')) > 0) {
                array_push($where, 'AND approve_status IN (' . implode(',', $this->input->post('pur_status')) . ')');
            }

            if ($this->input->post('department') && count($this->input->post('department')) > 0) {
                array_push($where, 'AND department IN (' . implode(',', $this->input->post('department')) . ')');
            }

            $currency = $this->currencies_model->get_base_currency();

            if ($this->input->post('report_currency')) {
                $report_currency = $this->input->post('report_currency');
                $base_currency = get_base_currency_pur();

                if ($report_currency == $base_currency->id) {
                    array_push($where, 'AND ' . db_prefix() . 'pur_orders.currency IN (0, ' . $report_currency . ')');
                } else {
                    array_push($where, 'AND ' . db_prefix() . 'pur_orders.currency = ' . $report_currency);
                }

                $currency = pur_get_currency_by_id($report_currency);
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_orders';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'pur_orders.department',
                'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'pur_orders.project',
                'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_orders.vendor',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix() . 'pur_orders.id as id',
                db_prefix() . 'departments.name as department_name',
                db_prefix() . 'projects.name as project_name',
                db_prefix() . 'pur_vendor.company as vendor_name',
                'total',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'total_tax'       => 0,
                'total_value'     => 0,
            ];
            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" target="_blank">' . $aRow['pur_order_number'] . '</a>';

                $row[] = _d($aRow['order_date']);

                $row[] = $aRow['department_name'];

                $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';

                $row[] = get_status_approve($aRow['approve_status']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);

                $row[] = app_format_money($aRow['total_tax'], $currency->name);

                $row[] = app_format_money($aRow['total'], $currency->name);

                $footer_data['total'] += $aRow['total'];
                $footer_data['total_tax'] += $aRow['total_tax'];
                $footer_data['total_value'] += $aRow['subtotal'];

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    /**
     *  wo report
     *  
     *  @return json
     */
    public function wo_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'wo_order_number',
                'order_date',
                'department',
                'vendor',
                'approve_status',
                'subtotal',
                'total_tax',
                'total',
            ];
            $where = [];
            $custom_date_select = $this->get_where_report_period(db_prefix() . 'wo_orders.order_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if (
                $this->input->post('wo_vendor')
                && count($this->input->post('wo_vendor')) > 0
            ) {
                array_push($where, 'AND vendor IN (' . implode(',', $this->input->post('wo_vendor')) . ')');
            }

            if ($this->input->post('wo_status') && count($this->input->post('wo_status')) > 0) {
                array_push($where, 'AND approve_status IN (' . implode(',', $this->input->post('wo_status')) . ')');
            }

            if ($this->input->post('wo_department') && count($this->input->post('wo_department')) > 0) {
                array_push($where, 'AND department IN (' . implode(',', $this->input->post('wo_department')) . ')');
            }

            $currency = $this->currencies_model->get_base_currency();

            if ($this->input->post('report_currency')) {
                $report_currency = $this->input->post('report_currency');
                $base_currency = get_base_currency_pur();

                if ($report_currency == $base_currency->id) {
                    array_push($where, 'AND ' . db_prefix() . 'wo_orders.currency IN (0, ' . $report_currency . ')');
                } else {
                    array_push($where, 'AND ' . db_prefix() . 'wo_orders.currency = ' . $report_currency);
                }

                $currency = pur_get_currency_by_id($report_currency);
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'wo_orders';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'wo_orders.department',
                'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'wo_orders.project',
                'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'wo_orders.vendor',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix() . 'wo_orders.id as id',
                db_prefix() . 'departments.name as department_name',
                db_prefix() . 'projects.name as project_name',
                db_prefix() . 'pur_vendor.company as vendor_name',
                'total',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'total_tax'       => 0,
                'total_value'     => 0,
            ];
            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('purchase/work_order/' . $aRow['id']) . '" target="_blank">' . $aRow['wo_order_number'] . '</a>';

                $row[] = _d($aRow['order_date']);

                $row[] = $aRow['department_name'];

                $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';

                $row[] = get_status_approve($aRow['approve_status']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);

                $row[] = app_format_money($aRow['total_tax'], $currency->name);

                $row[] = app_format_money($aRow['total'], $currency->name);

                $footer_data['total'] += $aRow['total'];
                $footer_data['total_tax'] += $aRow['total_tax'];
                $footer_data['total_value'] += $aRow['subtotal'];

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }
    /**
     *  purchase inv report
     *  
     *  @return json
     */
    public function purchase_inv_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'invoice_number',
                'contract',
                db_prefix() . 'pur_invoices.pur_order',
                'invoice_date',
                'payment_status',
                'subtotal',
                'tax',
                'total',
            ];
            $where = [];
            $custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_invoices.invoice_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }



            $currency = $this->currencies_model->get_base_currency();

            if ($this->input->post('report_currency')) {
                $report_currency = $this->input->post('report_currency');
                $base_currency = get_base_currency_pur();

                if ($report_currency == $base_currency->id) {
                    array_push($where, 'AND ' . db_prefix() . 'pur_invoices.currency IN (0, ' . $report_currency . ')');
                } else {
                    array_push($where, 'AND ' . db_prefix() . 'pur_invoices.currency = ' . $report_currency);
                }

                $currency = pur_get_currency_by_id($report_currency);
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_invoices';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'pur_contracts ON ' . db_prefix() . 'pur_contracts.id = ' . db_prefix() . 'pur_invoices.contract'
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix() . 'pur_invoices.id as id',
                db_prefix() . 'pur_contracts.contract_number as contract_number',

            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'total_tax'       => 0,
                'total_value'     => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('purchase/purchase_invoice/' . $aRow['id']) . '" target="_blank">' . $aRow['invoice_number'] . '</a>';

                $row[] = '<a href="' . admin_url('purchase/contract/' . $aRow['contract']) . '" target="_blank">' . $aRow['contract_number'] . '</a>';

                $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow[db_prefix() . 'pur_invoices.pur_order']) . '" target="_blank">' . get_pur_order_subject($aRow[db_prefix() . 'pur_invoices.pur_order']) . '</a>';

                $row[] = _d($aRow['invoice_date']);

                $class = '';
                if ($aRow['payment_status'] == 'unpaid') {
                    $class = 'danger';
                } elseif ($aRow['payment_status'] == 'paid') {
                    $class = 'success';
                } elseif ($aRow['payment_status'] == 'partially_paid') {
                    $class = 'warning';
                }

                $row[] = '<span class="label label-' . $class . ' s-status invoice-status-3">' . _l($aRow['payment_status']) . '</span>';

                $row[] = app_format_money($aRow['subtotal'], $currency->name);

                $row[] = app_format_money($aRow['tax'], $currency->name);

                $row[] = app_format_money($aRow['total'], $currency->name);

                $footer_data['total'] += $aRow['total'];
                $footer_data['total_tax'] += $aRow['tax'];
                $footer_data['total_value'] += $aRow['subtotal'];

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    /**
     * { invoices }
     * @return view
     */
    public function invoices()
    {
        $this->load->model('taxes_model');
        $this->load->model('currencies_model');

        $data['title'] = _l('vendor_billing_tracker');
        $data['contracts'] = $this->purchase_model->get_contract();
        $data['pur_orders'] = $this->purchase_model->get_list_pur_orders();
        $data['wo_orders'] = $this->purchase_model->get_list_wo_orders();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['customers'] = $this->clients_model->get();
        $data['projects'] = $this->projects_model->get();
        $data['expense_categories'] = $this->expenses_model->get_category();
        $data['taxes'] = $this->taxes_model->get();
        $data['currencies'] = $this->currencies_model->get();
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);
        $data['billing_invoices'] = $this->purchase_model->get_billing_invoices();
        $data['budget_head'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['invoices'] = get_all_applied_invoices();
        $this->load->view('invoices/manage', $data);
    }

    /**
     * { table purchase invoices }
     */
    public function table_pur_invoices()
    {
        $this->app->get_table_data(module_views_path('purchase', 'invoices/table_pur_invoices'));
    }

    /**
     * { purchase invoice }
     *
     * @param      string  $id     The identifier
     */
    public function pur_invoice($id = '')
    {
        if ($id == '') {
            $data['title'] = _l('add_invoice');
        } else {
            $data['title'] = _l('edit_invoice');
        }
        $data['contracts'] = $this->purchase_model->get_contract();
        $data['taxes'] = $this->purchase_model->get_taxes();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['projects'] = $this->projects_model->get_items();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $pur_invoice_row_template = $this->purchase_model->create_purchase_invoice_row_template();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        if ($id != '') {
            $data['pur_orders'] = $this->purchase_model->get_pur_order_approved();
            $data['wo_orders'] = $this->purchase_model->get_wo_order_approved();
            $data['pur_invoice'] = $this->purchase_model->get_pur_invoice($id);
            $data['pur_invoice_detail'] = $this->purchase_model->get_pur_invoice_detail($id);

            $currency_rate = 1;
            if ($data['pur_invoice']->currency != 0 && $data['pur_invoice']->currency_rate != null) {
                $currency_rate = $data['pur_invoice']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if ($data['pur_invoice']->currency != 0 && $data['pur_invoice']->to_currency != null) {
                $to_currency = $data['pur_invoice']->to_currency;
            }

            if (count($data['pur_invoice_detail']) > 0) {
                $index_order = 0;
                foreach ($data['pur_invoice_detail'] as $inv_detail) {
                    $index_order++;
                    $unit_name = pur_get_unit_name($inv_detail['unit_id']);
                    $taxname = $inv_detail['tax_name'];
                    $item_name = $inv_detail['item_name'];

                    if (strlen($item_name) == 0) {
                        $item_name = pur_get_item_variatiom($inv_detail['item_code']);
                    }

                    $pur_invoice_row_template .= $this->purchase_model->create_purchase_invoice_row_template('items[' . $index_order . ']',  $item_name, $inv_detail['description'], $inv_detail['quantity'], $unit_name, $inv_detail['unit_price'], $taxname, $inv_detail['item_code'], $inv_detail['unit_id'], $inv_detail['tax_rate'],  $inv_detail['total_money'], $inv_detail['discount_percent'], $inv_detail['discount_money'], $inv_detail['total'], $inv_detail['into_money'], $inv_detail['tax'], $inv_detail['tax_value'], $inv_detail['id'], true, $currency_rate, $to_currency);
                }
            } else {
                $item_name = $data['pur_invoice']->invoice_number;
                $description = $data['pur_invoice']->adminnote;
                $quantity = 1;
                $taxname = '';
                $tax_rate = 0;
                $tax = get_tax_rate_item($id);
                if ($tax && !is_array($tax)) {
                    $taxname = $tax->name;
                    $tax_rate = $tax->taxrate;
                }

                $total = $data['pur_invoice']->subtotal + $data['pur_invoice']->tax;
                $index = 0;

                $pur_invoice_row_template .= $this->purchase_model->create_purchase_invoice_row_template('newitems[' . $index . ']',  $item_name, $description, $quantity, '', $data['pur_invoice']->subtotal, $taxname, null, null, $tax_rate,  $data['pur_invoice']->total, 0, 0, $total, $data['pur_invoice']->subtotal, $data['pur_invoice']->tax_rate, $data['pur_invoice']->tax, '', true);
            }
        } else {
            $data['pur_orders'] = $this->purchase_model->get_pur_order_approved_for_inv();
            $data['wo_orders'] = $this->purchase_model->get_wo_order_approved_for_inv();
        }

        $data['pur_invoice_row_template'] = $pur_invoice_row_template;
        // $data['pur_invoice_row_template'] = '';
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        $this->load->view('invoices/pur_invoice', $data);
    }

    /**
     * { vendors change }
     */
    public function pur_vendors_change($vendor)
    {
        $currency_id = get_vendor_currency($vendor);
        if ($currency_id == 0 || $currency_id == '') {
            $currency_id = get_base_currency()->id;
        }

        $option_po = '<option value=""></option>';
        $option_ct = '<option value=""></option>';

        $pur_orders = $this->purchase_model->get_pur_order_approved_for_inv_by_vendor($vendor);
        foreach ($pur_orders as $po) {
            $option_po .= '<option value="' . $po['id'] . '">' . $po['pur_order_number'] . '</option>';
        }

        $contracts = $this->purchase_model->get_contracts_by_vendor($vendor);
        foreach ($contracts as $ct) {
            $option_ct .= '<option value="' . $ct['id'] . '">' . $ct['contract_number'] . '</option>';
        }


        $option_html = '';

        if (total_rows(db_prefix() . 'pur_vendor_items', ['vendor' => $vendor]) <= ajax_on_total_items()) {
            $items = $this->purchase_model->get_items_by_vendor_variation($vendor);
            $option_html .= '<option value=""></option>';
            foreach ($items as $item) {
                $option_html .= '<option value="' . $item['id'] . '" >' . $item['label'] . '</option>';
            }
        }

        echo json_encode([
            'type' => get_purchase_option('create_invoice_by'),
            'html' => $option_ct,
            'po_html' => $option_po,
            'option_html' => $option_html,
            'currency_id' => $currency_id,
        ]);
    }

    /**
     * { pur invoice form }
     * @return redirect
     */
    public function pur_invoice_form()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                unset($data['id']);
                $mess = $this->purchase_model->add_pur_invoice($data);
                if ($mess) {
                    handle_pur_invoice_file($mess);
                    set_alert('success', _l('added_successfully') . ' ' . _l('purchase_invoice'));
                } else {
                    set_alert('warning', _l('add_purchase_invoice_fail'));
                }
                redirect(admin_url('purchase/invoices'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                handle_pur_invoice_file($id);
                $success = $this->purchase_model->update_pur_invoice($id, $data);
                if ($success) {
                    set_alert('success', _l('updated_successfully') . ' ' . _l('purchase_invoice'));
                } else {
                    set_alert('warning', _l('update_purchase_invoice_fail'));
                }
                redirect(admin_url('purchase/invoices'));
            }
        }
    }

    public function delete_pur_invoice($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/invoices'));
        }
        $response = $this->purchase_model->delete_pur_invoice($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('purchase_invoice')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('purchase_invoice')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('purchase_invoice')));
        }
        redirect(admin_url('purchase/invoices'));
    }

    /**
     * { contract change }
     *
     * @param      <type>  $ct    
     */
    public function contract_change($ct)
    {
        $contract = $this->purchase_model->get_contract($ct);
        $value = 0;
        if ($contract) {
            $value = $contract->contract_value;
        }

        echo json_encode([
            'value' => $value,
            'purchase_order' => $contract->pur_order,
        ]);
    }

    /**
     * { purchase order change }
     *
     * @param      <type>  $ct    
     */
    public function pur_order_change($ct)
    {
        $pur_order = $this->purchase_model->get_pur_order($ct);
        $pur_order_detail = $this->purchase_model->get_pur_order_detail($ct);

        $list_item = $this->purchase_model->create_purchase_order_row_template();
        $discount_percent = 0;

        $base_currency = get_base_currency();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if ($pur_order->currency != 0 && $pur_order->currency_rate != null) {
            $currency_rate = $pur_order->currency_rate;
            $to_currency = $pur_order->currency;
        }

        if (count($pur_order_detail) > 0) {
            $index = 0;
            foreach ($pur_order_detail as $key => $item) {
                $index++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_name'];
                if (strlen($item_name) == 0) {
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->purchase_model->create_purchase_invoice_row_template('newitems[' . $index . ']',  $item_name, '', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total_money'], $item['discount_%'], $item['discount_money'], $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index, true, $currency_rate, $to_currency);
            }
        }

        $discount_type = 'after_tax';
        if ($pur_order) {
            $discount_percent = $pur_order->discount_percent;
            $discount_type = $pur_order->discount_type;
        }

        echo json_encode([
            'discount_type' => $discount_type,
            'list_item' => $list_item,
            'discount_percent' => $discount_percent,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'shipping_fee' => $pur_order->shipping_fee,
            'order_discount' => $pur_order->discount_total,
        ]);
    }

    /**
     * { tax rate change }
     *
     * @param        $tax    The tax
     */
    public function tax_rate_change($tax)
    {
        $this->load->model('taxes_model');
        $tax = $this->taxes_model->get($tax);
        $rate = 0;
        if ($tax) {
            $rate = $tax->taxrate;
        }

        echo  json_encode([
            'rate' => $rate,
        ]);
    }

    /**
     * { purchase invoice }
     *
     * @param       $id     The identifier
     */
    public function purchase_invoice($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/invoices'));
        }
        $data['pur_invoice'] = $this->purchase_model->get_pur_invoice($id);

        if (!has_permission('purchase_invoices', '', 'view') && !has_permission('purchase_invoices', '', 'view_own')) {
            access_denied('purchase');
        }

        if (has_permission('purchase_invoices', '', 'view_own') && !is_admin()) {
            if ($data['pur_invoice']->add_from != get_staff_user_id() && !in_array($data['pur_invoice']->vendor, get_vendor_admin_list(get_staff_user_id()))) {
                access_denied('purchase');
            }
        }


        $this->load->model('staff_model');
        $this->load->model('currencies_model');

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $data['applied_debits'] = $this->purchase_model->get_applied_invoice_debits($id);
        $data['debits_available'] = $this->purchase_model->total_remaining_debits_by_vendor($data['pur_invoice']->vendor, $data['pur_invoice']->currency);

        if ($data['debits_available'] > 0) {
            $data['open_debits'] = $this->purchase_model->get_open_debits($data['pur_invoice']->vendor);
        }
        $vendor_currency_id = get_vendor_currency($data['pur_invoice']->vendor);
        $data['vendor_currency'] = $this->currencies_model->get_base_currency();
        if ($vendor_currency_id != 0) {
            $data['vendor_currency'] = pur_get_currency_by_id($vendor_currency_id);
        }

        $data['invoice_detail'] = $this->purchase_model->get_pur_invoice_detail($id);

        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_invoice($id);

        $data['title'] = $data['pur_invoice']->invoice_number;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['payment'] = $this->purchase_model->get_payment_invoice($id);
        $data['pur_invoice_attachments'] = $this->purchase_model->get_purchase_invoice_attachments($id);
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();
        $this->load->view('invoices/pur_invoice_preview', $data);
    }

    /**
     * Adds a payment for invoice.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_invoice_payment($invoice)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->purchase_model->add_invoice_payment($data, $invoice);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(admin_url('purchase/purchase_invoice/' . $invoice));
        }
    }

    /**
     * { delete payment }
     *
     * @param      <type>  $id         The identifier
     * @param      <type>  $pur_order  The pur order
     * @return  redirect
     */
    public function delete_payment_pur_invoice($id, $inv)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_invoice/' . $inv));
        }
        $response = $this->purchase_model->delete_payment_pur_invoice($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('payment')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment')));
        }
        redirect(admin_url('purchase/purchase_invoice/' . $inv));
    }

    /**
     * { payment invoice }
     *
     * @param       $id     The identifier
     * @return view
     */
    public function payment_invoice($id)
    {
        $this->load->model('currencies_model');

        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if ((isset($send_mail_approve)) && $send_mail_approve != '') {
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }

        $data['check_appr'] = $this->purchase_model->get_approve_setting('payment_request');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'payment_request');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'payment_request');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'payment_request');


        $data['payment_invoice'] = $this->purchase_model->get_payment_pur_invoice($id);
        $data['title'] = _l('payment_for') . ' ' . get_pur_invoice_number($data['payment_invoice']->pur_invoice);

        $data['invoice'] = $this->purchase_model->get_pur_invoice($data['payment_invoice']->pur_invoice);

        $data['base_currency'] = $this->currencies_model->get_base_currency();
        if ($data['invoice']->currency != 0) {
            $data['base_currency'] = pur_get_currency_by_id($data['invoice']->currency);
        }

        $this->load->view('invoices/payment_invoice', $data);
    }

    /**
     * { purchase invoice attachment }
     */
    public function purchase_invoice_attachment($id)
    {
        handle_pur_invoice_file($id);
        redirect(admin_url('purchase/purchase_invoice/' . $id));
    }

    /**
     * { preview purchase invoice file }
     *
     * @param        $id      The identifier
     * @param        $rel_id  The relative identifier
     * @return  view
     */
    public function file_purinv($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('invoices/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purinv_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_purinv_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * { purchase estimate pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function purestimate_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/quotations'));
        }

        $pur_estimate = $this->purchase_model->get_purestimate_pdf_html($id);

        try {
            $pdf = $this->purchase_model->purestimate_pdf($pur_estimate, $id);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(format_pur_estimate_number($id) . '.pdf', $type);
    }

    /**
     * Sends a request quotation.
     * @return redirect
     */
    public function send_quotation()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_quotation($data);
            if ($send) {
                set_alert('success', _l('send_quotation_successfully'));
            } else {
                set_alert('warning', _l('send_quotation_fail'));
            }
            redirect(admin_url('purchase/quotations/' . $data['pur_estimate_id']));
        }
    }

    /**
     * Sends a purchase order.
     * @return redirect
     */
    public function send_po()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_po($data);
            if ($send) {
                set_alert('success', _l('send_po_successfully'));
            } else {
                set_alert('warning', _l('send_po_fail'));
            }
            redirect(admin_url('purchase/purchase_order/' . $data['po_id']));
        }
    }

    /**
     * import xlsx commodity
     * @param  integer $id
     * @return view
     */
    public function import_xlsx_commodity()
    {
        if (!is_admin() && !has_permission('purchase_items', '', 'create')) {
            access_denied('purchase');
        }
        $this->load->model('staff_model');
        $data_staff = $this->staff_model->get(get_staff_user_id());

        /*get language active*/
        if ($data_staff) {
            if ($data_staff->default_language != '') {
                $data['active_language'] = $data_staff->default_language;
            } else {

                $data['active_language'] = get_option('active_language');
            }
        } else {
            $data['active_language'] = get_option('active_language');
        }
        $data['title'] = _l('import_excel');

        $this->load->view('items/import_excel', $data);
    }

    /**
     * import file xlsx commodity
     * @return json
     */
    public function import_file_xlsx_commodity()
    {
        if (!is_admin() && !has_permission('purchase_items', '', 'create')) {
            access_denied(_l('purchase'));
        }

        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if ($this->input->post()) {

            /*delete file old before export file*/
            $path_before = COMMODITY_ERROR_PUR . 'FILE_ERROR_COMMODITY' . get_staff_user_id() . '.xlsx';
            if (file_exists($path_before)) {
                unlink(COMMODITY_ERROR_PUR . 'FILE_ERROR_COMMODITY' . get_staff_user_id() . '.xlsx');
            }

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                //do_action('before_import_leads');

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        //Writer file
                        $writer_header = array(
                            _l('commodity_code')          => 'string',
                            _l('commodity_name')          => 'string',
                            _l('commodity_barcode')          => 'string',
                            _l('sku_code')               => 'string',
                            _l('sku_name')       => 'string',
                            _l('description')             => 'string',
                            _l('unit_id')                      => 'string',
                            _l('commodity_group')                     => 'string',
                            _l('sub_group')                     => 'string',
                            _l('purchase_price')                     => 'string',
                            _l('rate')                     => 'string',
                            _l('tax_1')                     => 'string',
                            _l('tax_2')                     => 'string',
                            _l('error')                     => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr]);

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $total_rows = 0;
                        $total_row_false    = 0;

                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error = '';

                            $flag_id_unit_id;
                            $flag_id_commodity_group;
                            $flag_id_sub_group;
                            $flag_id_tax;
                            $flag_id_tax2;

                            $value_commodity_code    = isset($data[$row][0]) ? $data[$row][0] : '';
                            $value_commodity_name    = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_commodity_barcode    = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_sku_code   = isset($data[$row][3]) ? $data[$row][3] : '';
                            $value_sku_name      = isset($data[$row][4]) ? $data[$row][4] : '';
                            $value_description       = isset($data[$row][5]) ? $data[$row][5] : '';
                            $value_unit_id            = isset($data[$row][6]) ? $data[$row][6] : '';
                            $value_commodity_group            = isset($data[$row][7]) ? $data[$row][7] : '';
                            $value_sub_group            = isset($data[$row][8]) ? $data[$row][8] : '';
                            $value_purchase_price            = isset($data[$row][9]) ? $data[$row][9] : '';
                            $value_rate            = isset($data[$row][10]) ? $data[$row][10] : '';
                            $value_tax            = isset($data[$row][11]) ? $data[$row][11] : '';
                            $value_tax2            = isset($data[$row][12]) ? $data[$row][12] : '';

                            if (is_null($value_commodity_code) == true || $value_commodity_code == '') {
                                $string_error .= _l('commodity_code') . _l('not_yet_entered');
                                $flag = 1;
                            } else {
                                $this->db->where('commodity_code', $value_commodity_code);
                                $total_rows_check = $this->db->count_all_results(db_prefix() . 'items');
                                if ($total_rows_check > 0) {
                                    $string_error .= _l('commodity_code') . _l('already_exist');
                                    $flag = 1;
                                }
                            }

                            if (is_null($value_commodity_name) == true || $value_commodity_name == '') {
                                $string_error .= _l('commodity_name') . _l('not_yet_entered');
                                $flag = 1;
                            }

                            if (empty($value_unit_id)) {
                                $value_unit_id = 1;
                            }

                            //check unit_code exist  (input: id or name contract)
                            if (is_null($value_unit_id) != true && ($value_unit_id != '0') && $value_unit_id != '') {
                                /*case input id*/
                                if (is_numeric($value_unit_id)) {
                                    $this->db->where('unit_type_id', $value_unit_id);
                                    $unit_id_value = $this->db->count_all_results(db_prefix() . 'ware_unit_type');
                                    if ($unit_id_value == 0) {
                                        $string_error .= _l('unit_id') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id unit_id*/
                                        $flag_id_unit_id = $value_unit_id;
                                    }
                                } else {
                                    /*case input name*/
                                    $this->db->like(db_prefix() . 'ware_unit_type.unit_name', $value_unit_id);
                                    $unit_id_value = $this->db->get(db_prefix() . 'ware_unit_type')->result_array();
                                    if (count($unit_id_value) == 0) {
                                        $string_error .= _l('unit_id') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get unit_id*/
                                        $flag_id_unit_id = $unit_id_value[0]['unit_type_id'];
                                    }
                                }
                            }

                            //check commodity_group exist  (input: id or name contract)
                            if (is_null($value_commodity_group) != true && ($value_commodity_group != '0') && $value_commodity_group != '') {
                                /*case input id*/
                                if (is_numeric($value_commodity_group)) {
                                    $this->db->where('id', $value_commodity_group);
                                    $commodity_group_value = $this->db->count_all_results(db_prefix() . 'items_groups');
                                    if ($commodity_group_value == 0) {
                                        $string_error .= _l('commodity_group') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id commodity_group*/
                                        $flag_id_commodity_group = $value_commodity_group;
                                    }
                                } else {
                                    /*case input name*/
                                    $this->db->like(db_prefix() . 'items_groups.name', $value_commodity_group);
                                    $commodity_group_value = $this->db->get(db_prefix() . 'items_groups')->result_array();
                                    if (count($commodity_group_value) == 0) {
                                        $string_error .= _l('commodity_group') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id commodity_group*/
                                        $flag_id_commodity_group = $commodity_group_value[0]['id'];
                                    }
                                }
                            }

                            //check taxes exist  (input: id or name contract)
                            if (is_null($value_tax) != true && ($value_tax != '0') && $value_tax != '') {
                                /*case input id*/
                                if (is_numeric($value_tax)) {
                                    $this->db->where('id', $value_tax);
                                    $cell_tax_value = $this->db->count_all_results(db_prefix() . 'taxes');
                                    if ($cell_tax_value == 0) {
                                        $string_error .= _l('tax') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id cell_tax*/
                                        $flag_id_tax = $value_tax;
                                    }
                                } else {
                                    /*case input name*/
                                    $this->db->like(db_prefix() . 'taxes.name', $value_tax);
                                    $cell_tax_value = $this->db->get(db_prefix() . 'taxes')->result_array();
                                    if (count($cell_tax_value) == 0) {
                                        $string_error .= _l('tax') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id warehouse_id*/
                                        $flag_id_tax = $cell_tax_value[0]['id'];
                                    }
                                }
                            }

                            //check taxes exist  (input: id or name contract)
                            if (is_null($value_tax2) != true && ($value_tax2 != '0') && $value_tax2 != '') {
                                /*case input id*/
                                if (is_numeric($value_tax2)) {
                                    $this->db->where('id', $value_tax2);
                                    $cell_tax_value2 = $this->db->count_all_results(db_prefix() . 'taxes');
                                    if ($cell_tax_value2 == 0) {
                                        $string_error .= _l('tax') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id cell_tax*/
                                        $flag_id_tax2 = $value_tax2;
                                    }
                                } else {
                                    /*case input name*/
                                    $this->db->like(db_prefix() . 'taxes.name', $value_tax2);
                                    $cell_tax_value2 = $this->db->get(db_prefix() . 'taxes')->result_array();
                                    if (count($cell_tax_value2) == 0) {
                                        $string_error .= _l('tax') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id warehouse_id*/
                                        $flag_id_tax2 = $cell_tax_value2[0]['id'];
                                    }
                                }
                            }

                            //check commodity_group exist  (input: id or name contract)
                            if (is_null($value_sub_group) != true && $value_sub_group != '') {
                                /*case input id*/
                                if (is_numeric($value_sub_group)) {
                                    $this->db->where('id', $value_sub_group);
                                    $sub_group_value = $this->db->count_all_results(db_prefix() . 'wh_sub_group');
                                    if ($sub_group_value == 0) {
                                        $string_error .= _l('sub_group') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id sub_group*/
                                        $flag_id_sub_group = $value_sub_group;
                                    }
                                } else {
                                    /*case input  name*/
                                    $this->db->like(db_prefix() . 'wh_sub_group.sub_group_name', $value_sub_group);
                                    $sub_group_value = $this->db->get(db_prefix() . 'wh_sub_group')->result_array();
                                    if (count($sub_group_value) == 0) {
                                        $string_error .= _l('sub_group') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id sub_group*/
                                        $flag_id_sub_group = $sub_group_value[0]['id'];
                                    }
                                }
                            }

                            //check value_rate input
                            if (is_null($value_rate) != true && $value_rate != '') {
                                if (!check_valid_number_with_setting($value_rate)) {
                                    $string_error .= _l('cell_rate') . _l('_check_invalid');
                                    $flag = 1;
                                }
                            }
                            //check value_purchase_price input
                            if (is_null($value_purchase_price) != true && $value_purchase_price != '') {
                                if (!check_valid_number_with_setting($value_purchase_price)) {
                                    $string_error .= _l('purchase_price') . _l('_check_invalid');
                                    $flag = 1;
                                }
                            }

                            if (($flag == 1) || $flag2 == 1) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_commodity_code,
                                    $value_commodity_name,
                                    $value_commodity_barcode,
                                    $value_sku_code,
                                    $value_sku_name,
                                    $value_description,
                                    $value_unit_id,
                                    $value_commodity_group,
                                    $value_sub_group,
                                    $value_purchase_price,
                                    $value_rate,
                                    $value_tax,
                                    $value_tax2,
                                    $string_error,
                                ]);

                                // $numRow++;
                                $total_row_false++;
                            }

                            if ($flag == 0 && $flag2 == 0) {
                                $rd['commodity_code']                = $value_commodity_code;
                                $rd['description']                = $value_commodity_name;
                                $rd['commodity_barcode']                     = $value_commodity_barcode;
                                $rd['sku_code']     = $value_sku_code;
                                $rd['sku_name']                = $value_sku_name;
                                $rd['long_description']                         = $value_description;
                                $rd['unit_id']                         = isset($flag_id_unit_id) ? $flag_id_unit_id : '';
                                $rd['group_id']                         = isset($flag_id_commodity_group) ? $flag_id_commodity_group : '';
                                $rd['sub_group']                         = isset($flag_id_sub_group) ? $flag_id_sub_group : '';
                                $rd['tax']                         = isset($flag_id_tax) ? $flag_id_tax : '';
                                $rd['tax2']                         = isset($flag_id_tax2) ? $flag_id_tax2 : '';
                                $rd['rate']                         = reformat_currency_pur($value_rate);
                                $rd['purchase_price']                         = reformat_currency_pur($value_purchase_price);

                                $rows[] = $rd;
                                $response = $this->purchase_model->import_xlsx_commodity($rd);
                            }
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        // $dataerror = $dataError;
                        $dataerror = '';
                        $message = 'Not enought rows for importing';

                        if ($total_row_false != 0) {
                            $filename = 'Import_item_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_IMPORT_ITEM_ERROR . $filename, $filename));
                        }
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }
        echo json_encode([
            'message'           => $message,
            'total_row_success' => $total_row_success,
            'total_row_false'   => $total_row_false,
            'total_rows'        => $total_rows,
            'site_url'          => site_url(),
            'staff_id'          => get_staff_user_id(),
            'filename'          => PURCHASE_IMPORT_ITEM_ERROR . $filename,

        ]);
    }

    /**
     * { import vendor }
     */
    public function vendor_import()
    {
        if (!has_permission('purchase_vendors', '', 'create')) {
            access_denied('purchase');
        }

        $this->load->model('staff_model');
        $data_staff = $this->staff_model->get(get_staff_user_id());

        /*get language active*/
        if ($data_staff) {
            if ($data_staff->default_language != '') {
                $data['active_language'] = $data_staff->default_language;
            } else {

                $data['active_language'] = get_option('active_language');
            }
        } else {
            $data['active_language'] = get_option('active_language');
        }
        $data['title'] = _l('import_excel');

        $this->load->view('vendors/import_excel', $data);
    }

    /**
     * { reset data }
     */
    public function reset_data()
    {

        if (!is_admin()) {
            access_denied('purchase');
        }

        //delete purchase request
        $this->db->truncate(db_prefix() . 'pur_request');
        //delete purchase request detail
        $this->db->truncate(db_prefix() . 'pur_request_detail');
        //delete purchase order
        $this->db->truncate(db_prefix() . 'pur_orders');
        //delete purchase order detail
        $this->db->truncate(db_prefix() . 'pur_order_detail');
        //delete purchase order payment
        $this->db->truncate(db_prefix() . 'pur_order_payment');
        //delete purchase invoice
        $this->db->truncate(db_prefix() . 'pur_invoices');
        //delete purchase invoice payment
        $this->db->truncate(db_prefix() . 'pur_invoice_payment');
        //delete purchase estimate
        $this->db->truncate(db_prefix() . 'pur_estimates');
        //delete pur_estimate_detail
        $this->db->truncate(db_prefix() . 'pur_estimate_detail');
        //delete pur_contracts
        $this->db->truncate(db_prefix() . 'pur_contracts');
        //delete tblpur_approval_details
        $this->db->truncate(db_prefix() . 'pur_approval_details');

        //delete create task rel_type: "pur_contract", "pur_contract".
        $this->db->where('rel_type', 'pur_contract');
        $this->db->or_where('rel_type', 'pur_order');
        $this->db->or_where('rel_type', 'pur_quotation');
        $this->db->or_where('rel_type', 'pur_invoice');
        $this->db->delete(db_prefix() . 'tasks');


        $this->db->where('rel_type', 'pur_contract');
        $this->db->or_where('rel_type', 'pur_order');
        $this->db->or_where('rel_type', 'pur_estimate');
        $this->db->or_where('rel_type', 'pur_invoice');
        $this->db->delete(db_prefix() . 'files');

        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/signature/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_invoice/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/signature/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/payment_invoice/signature/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/payment_request/signature/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/request_quotation/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/send_po/');
        delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/send_quotation/');

        $this->db->where('rel_type', 'pur_contract');
        $this->db->or_where('rel_type', 'purchase_order');
        $this->db->or_where('rel_type', 'pur_invoice');
        $this->db->delete(db_prefix() . 'notes');

        $this->db->where('rel_type', 'pur_contract');
        $this->db->or_where('rel_type', 'purchase_order');
        $this->db->or_where('rel_type', 'pur_invoice');
        $this->db->delete(db_prefix() . 'reminders');

        $this->db->where('fieldto', 'pur_order');
        $this->db->delete(db_prefix() . 'customfieldsvalues');

        $this->db->where('rel_type', 'pur_invoice');
        $this->db->or_where('rel_type', 'pur_order');
        $this->db->delete(db_prefix() . 'taggables');

        set_alert('success', _l('reset_data_successful'));

        redirect(admin_url('purchase/setting'));
    }

    /**
     * Removes a po logo.
     */
    public function remove_po_logo()
    {
        if (!is_admin()) {
            access_denied('purchase');
        }

        $success = $this->purchase_model->remove_po_logo();
        if ($success) {
            set_alert('success', _l('deleted', _l('po_logo')));
        }
        redirect(admin_url('purchase/setting'));
    }

    /**
     * delete_error file day before
     * @return [type] 
     */
    public function delete_error_file_day_before()
    {
        //Delete old file before 7 day
        $date = date_create(date('Y-m-d H:i:s'));
        date_sub($date, date_interval_create_from_date_string("7 days"));
        $before_7_day = strtotime(date_format($date, "Y-m-d H:i:s"));

        foreach (glob(PURCHASE_IMPORT_VENDOR_ERROR . '*') as $file) {

            $file_arr = explode("/", $file);
            $filename = array_pop($file_arr);

            if (file_exists($file)) {
                $file_name_arr = explode("_", $filename);
                $date_create_file = array_pop($file_name_arr);
                $date_create_file =  str_replace('.xlsx', '', $date_create_file);

                if ((float)$date_create_file <= (float)$before_7_day) {
                    unlink(PURCHASE_IMPORT_VENDOR_ERROR . $filename);
                }
            }
        }
        return true;
    }

    /**
     * { import job position excel }
     */
    public function import_file_xlsx_vendor()
    {
        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');


        $filename = '';
        if ($this->input->post()) {
            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

                $this->delete_error_file_day_before();

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        //Writer file
                        $writer_header = array(
                            _l('vendor_code')          => 'string',
                            _l('first_name')          => 'string',
                            _l('last_name')          => 'string',
                            _l('email')               => 'string',
                            _l('contact_phonenumber')       => 'string',
                            _l('position')             => 'string',
                            _l('company')                      => 'string',
                            _l('vat')                     => 'string',
                            _l('phonenumber')                     => 'string',
                            _l('country')                     => 'string',
                            _l('city')                     => 'string',
                            _l('zip')                     => 'string',
                            _l('state')                     => 'string',
                            _l('address')                     => 'string',
                            _l('website')                     => 'string',
                            _l('bank_detail')                     => 'string',
                            _l('payment_terms')                     => 'string',
                            _l('pur_billing_street')                     => 'string',
                            _l('pur_billing_city')                     => 'string',
                            _l('pur_billing_state')                     => 'string',
                            _l('pur_billing_zip')                     => 'string',
                            _l('pur_billing_country')                     => 'string',
                            _l('pur_shipping_street')                     => 'string',
                            _l('pur_shipping_city')                     => 'string',
                            _l('pur_shipping_state')                     => 'string',
                            _l('pur_shipping_zip')                     => 'string',
                            _l('pur_shipping_country')                     => 'string',
                            _l('error')                     => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr]);

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $total_rows = 0;
                        $total_row_false    = 0;

                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error = '';

                            $value_vendor_code    = isset($data[$row][0]) ? $data[$row][0] : '';
                            $value_fist_name    = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_last_name    = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_email   = isset($data[$row][3]) ? $data[$row][3] : '';
                            $value_contact_phonenumber      = isset($data[$row][4]) ? $data[$row][4] : '';
                            $value_position       = isset($data[$row][5]) ? $data[$row][5] : '';
                            $value_company            = isset($data[$row][6]) ? $data[$row][6] : '';
                            $value_vat            = isset($data[$row][7]) ? $data[$row][7] : '';
                            $value_phonenumber            = isset($data[$row][8]) ? $data[$row][8] : '';
                            $value_country            = isset($data[$row][9]) ? $data[$row][9] : '';
                            $value_city            = isset($data[$row][10]) ? $data[$row][10] : '';
                            $value_zip            = isset($data[$row][11]) ? $data[$row][11] : '';
                            $value_state            = isset($data[$row][12]) ? $data[$row][12] : '';
                            $value_address            = isset($data[$row][13]) ? $data[$row][13] : '';
                            $value_website            = isset($data[$row][14]) ? $data[$row][14] : '';
                            $value_bank_detail            = isset($data[$row][15]) ? $data[$row][15] : '';
                            $value_payment_terms            = isset($data[$row][16]) ? $data[$row][16] : '';
                            $value_pur_billing_street            = isset($data[$row][17]) ? $data[$row][17] : '';
                            $value_pur_billing_city            = isset($data[$row][18]) ? $data[$row][18] : '';
                            $value_pur_billing_state            = isset($data[$row][19]) ? $data[$row][19] : '';
                            $value_pur_billing_zip            = isset($data[$row][20]) ? $data[$row][20] : '';
                            $value_pur_billing_country            = isset($data[$row][21]) ? $data[$row][21] : '';
                            $value_pur_shipping_street            = isset($data[$row][22]) ? $data[$row][22] : '';
                            $value_pur_shipping_city            = isset($data[$row][23]) ? $data[$row][23] : '';
                            $value_pur_shipping_state            = isset($data[$row][24]) ? $data[$row][24] : '';
                            $value_pur_shipping_zip            = isset($data[$row][25]) ? $data[$row][25] : '';
                            $value_pur_shipping_country            = isset($data[$row][26]) ? $data[$row][26] : '';

                            if (is_null($value_vendor_code) == true || $value_vendor_code == '') {
                                $string_error .= _l('vendor_code') . _l('not_yet_entered');
                                $flag = 1;
                            } else {
                                $this->db->where('vendor_code', $value_vendor_code);
                                $total_rows_check = $this->db->count_all_results(db_prefix() . 'pur_vendor');
                                if ($total_rows_check > 0) {
                                    $string_error .= _l('vendor_code') . _l('already_exist');
                                    $flag = 1;
                                }
                            }

                            if (is_null($value_fist_name) == true || $value_fist_name == '') {
                                $string_error .= _l('fist_name') . _l('not_yet_entered');
                                $flag = 1;
                            }

                            if (is_null($value_last_name) == true || $value_last_name == '') {
                                $string_error .= _l('last_name') . _l('not_yet_entered');
                                $flag = 1;
                            }

                            if (is_null($value_email) == true || $value_email == '') {
                                $string_error .= _l('email') . _l('not_yet_entered');
                                $flag = 1;
                            } else {
                                $this->db->where('email', $value_email);
                                $total_rows_check_email = $this->db->count_all_results(db_prefix() . 'pur_contacts');
                                if ($total_rows_check_email > 0) {
                                    $string_error .= _l('email') . _l('already_exist');
                                    $flag = 1;
                                }
                            }

                            if (is_null($value_company) == true || $value_company == '') {
                                $string_error .= _l('company') . _l('not_yet_entered');
                                $flag = 1;
                            }

                            if (($flag == 1) || $flag2 == 1) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_vendor_code,
                                    $value_fist_name,
                                    $value_last_name,
                                    $value_email,
                                    $value_contact_phonenumber,
                                    $value_position,
                                    $value_company,
                                    $value_vat,
                                    $value_phonenumber,
                                    $value_country,
                                    $value_city,
                                    $value_zip,
                                    $value_state,
                                    $value_address,
                                    $value_website,
                                    $value_bank_detail,
                                    $value_payment_terms,
                                    $value_pur_billing_street,
                                    $value_pur_billing_city,
                                    $value_pur_billing_state,
                                    $value_pur_billing_zip,
                                    $value_pur_billing_country,
                                    $value_pur_shipping_street,
                                    $value_pur_shipping_city,
                                    $value_pur_shipping_state,
                                    $value_pur_shipping_zip,
                                    $value_pur_shipping_country,
                                    $string_error,
                                ]);

                                // $numRow++;
                                $total_row_false++;
                            }

                            if ($flag == 0 && $flag2 == 0) {
                                $rd['vendor_code']                = $value_vendor_code;
                                $rd['firstname']                = $value_fist_name;
                                $rd['lastname']                     = $value_last_name;
                                $rd['email']     = $value_email;
                                $rd['contact_phonenumber']                = $value_contact_phonenumber;
                                $rd['title']                         = $value_position;
                                $rd['company']                         = $value_company;
                                $rd['vat']                         = $value_vat;
                                $rd['phonenumber']                         = $value_phonenumber;
                                $rd['country']                         = $value_country;
                                $rd['city']                         = $value_city;
                                $rd['zip']                         = $value_zip;
                                $rd['state']                         = $value_state;
                                $rd['address']                         = $value_address;
                                $rd['website']                         = $value_website;
                                $rd['bank_detail']                         = $value_bank_detail;
                                $rd['payment_terms']                         = $value_payment_terms;
                                $rd['billing_street']                         = $value_pur_billing_street;
                                $rd['billing_city']                         = $value_pur_billing_city;
                                $rd['billing_state']                         = $value_pur_billing_state;
                                $rd['billing_zip']                         = $value_pur_billing_zip;
                                $rd['billing_country']                         = $value_pur_billing_country;
                                $rd['shipping_street']                         = $value_pur_shipping_street;
                                $rd['shipping_city']                         = $value_pur_shipping_city;
                                $rd['shipping_state']                         = $value_pur_shipping_state;
                                $rd['shipping_zip']                         = $value_pur_shipping_zip;
                                $rd['shipping_country']                         = $value_pur_shipping_country;

                                $rows[] = $rd;
                                $response = $this->purchase_model->add_vendor($rd, null, true);
                            }
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        // $dataerror = $dataError;
                        $dataerror = '';
                        $message = 'Not enought rows for importing';

                        if ($total_row_false != 0) {
                            $filename = 'Import_vendor_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_IMPORT_VENDOR_ERROR . $filename, $filename));
                        }
                    }
                }
            }
        }


        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }

        echo json_encode([
            'message'           => $message,
            'total_row_success' => $total_row_success,
            'total_row_false'   => $total_row_false,
            'total_rows'        => $total_rows,
            'site_url'          => site_url(),
            'staff_id'          => get_staff_user_id(),
            'filename'          => PURCHASE_IMPORT_VENDOR_ERROR . $filename,
        ]);
    }

    /**
     * { change delivery status }
     *
     * @param      integer  $status     The status
     * @param         $pur_order  The pur order
     * @return     json
     */
    public function change_delivery_status($status, $pur_order)
    {
        $success = $this->purchase_model->change_delivery_status($status, $pur_order);
        $message = '';
        $html = '';
        $status_str = '';
        $class = '';
        if ($success == true) {
            $message = _l('change_delivery_status_successfully');
        } else {
            $message = _l('change_delivery_status_fail');
        }

        if (has_permission('purchase_orders', '', 'edit') || is_admin()) {
            $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $pur_order . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $html .= '</a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $pur_order . '">';

            if ($status == 0) {
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 1 ,' . $pur_order . '); return false;">
                             ' . _l('completely_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 2 ,' . $pur_order . '); return false;">
                             ' . _l('pending_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 3 ,' . $pur_order . '); return false;">
                             ' . _l('partially_delivered') . '
                          </a>
                       </li>';

                $status_str = _l('undelivered');
                $class = 'label-danger';
            } else if ($status == 1) {
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 0 ,' . $pur_order . '); return false;">
                             ' . _l('undelivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 2 ,' . $pur_order . '); return false;">
                             ' . _l('pending_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 3 ,' . $pur_order . '); return false;">
                             ' . _l('partially_delivered') . '
                          </a>
                       </li>';
                $status_str = _l('completely_delivered');
                $class = 'label-success';
            } else if ($status == 2) {
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 0 ,' . $pur_order . '); return false;">
                             ' . _l('undelivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 1 ,' . $pur_order . '); return false;">
                             ' . _l('completely_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 3 ,' . $pur_order . '); return false;">
                             ' . _l('partially_delivered') . '
                          </a>
                       </li>';
                $status_str = _l('pending_delivered');
                $class = 'label-info';
            } else if ($status == 3) {
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 0 ,' . $pur_order . '); return false;">
                             ' . _l('undelivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 1 ,' . $pur_order . '); return false;">
                             ' . _l('completely_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 2 ,' . $pur_order . '); return false;">
                             ' . _l('pending_delivered') . '
                          </a>
                       </li>';
                $status_str = _l('partially_delivered');
                $class = 'label-warning';
            }


            $html .= '</ul>';
            $html .= '</div>';
        }

        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }
    public function change_rli_filter($status, $id, $table_name)
    {

        // Define an array of statuses with their corresponding labels and texts
        $status_labels = [
            0 => ['label' => 'label-danger', 'table' => 'provided_by_ril', 'text' => _l('provided_by_ril')],
            1 => ['label' => 'label-success', 'table' => 'new_item_service_been_addded_as_per_instruction', 'text' => _l('new_item_service_been_addded_as_per_instruction')],
            2 => ['label' => 'label-info', 'table' => 'due_to_spec_change_then_original_cost', 'text' => _l('due_to_spec_change_then_original_cost')],
            3 => ['label' => 'label-warning', 'table' => 'deal_slip', 'text' => _l('deal_slip')],
            4 => ['label' => 'label-primary', 'table' => 'to_be_provided_by_ril_but_managed_by_bil', 'text' => _l('to_be_provided_by_ril_but_managed_by_bil')],
            5 => ['label' => 'label-secondary', 'table' => 'due_to_additional_item_as_per_apex_instrution', 'text' => _l('due_to_additional_item_as_per_apex_instrution')],
            6 => ['label' => 'label-purple', 'table' => 'event_expense', 'text' => _l('event_expense')],
            7 => ['label' => 'label-teal', 'table' => 'pending_procurements', 'text' => _l('pending_procurements')],
            8 => ['label' => 'label-orange', 'table' => 'common_services_in_ghj_scope', 'text' => _l('common_services_in_ghj_scope')],
            9 => ['label' => 'label-green', 'table' => 'common_services_in_ghj_scope', 'text' => _l('common_services_in_ril_scope')],
            10 => ['label' => 'label-default', 'table' => 'due_to_site_specfic_constraint', 'text' => _l('due_to_site_specfic_constraint')],
        ];
        $success = $this->purchase_model->change_rli_filter($status, $id, $table_name);
        $message = $success ? _l('change_rli_filter_successfully') : _l('change_rli_filter_fail');

        $html = '';
        $status_str = $status_labels[$status]['text'] ?? '';
        $class = $status_labels[$status]['label'] ?? '';

        if (has_permission('order_tracker', '', 'edit') || is_admin()) {
            $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $html .= '</a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $id . '">';

            // Generate the dropdown menu options dynamically
            foreach ($status_labels as $key => $label) {
                if ($key != $status) {
                    $html .= '<li>
                    <a href="#" onclick="change_rli_filter(' . $key . ', ' . $id . ', \'' . htmlspecialchars($table_name, ENT_QUOTES) . '\'); return false;">
                        ' . $label['text'] . '
                    </a>
                </li>';
                }
            }

            $html .= '</ul>';
            $html .= '</div>';
        }

        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }

    public function change_aw_unw_order_status($status, $id, $table_name)
    {

        // Define an array of statuses with their corresponding labels and texts
        $status_labels_aw_uw = [
            1 => ['label' => 'label-success', 'table' => 'awarded', 'text' => _l('Awarded')],
            2 => ['label' => 'label-default', 'table' => 'unawarded', 'text' => _l('Unawarded')],
            3 => ['label' => 'label-warning', 'table' => 'awarded_by_ril', 'text' => _l('Awarded by RIL')],
        ];
        $success = $this->purchase_model->change_aw_unw_order_status($status, $id, $table_name);
        $message = $success ? _l('change_aw_unw_order_status_successfully') : _l('changeaw_unw_order_status_fail');

        $html = '';
        $status_str = $status_labels_aw_uw[$status]['text'] ?? '';
        $class = $status_labels_aw_uw[$status]['label'] ?? '';

        if (has_permission('order_tracker', '', 'edit') || is_admin()) {
            $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $html .= '</a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $id . '">';

            // Generate the dropdown menu options dynamically
            foreach ($status_labels_aw_uw as $key => $label) {
                if ($key != $status) {
                    $html .= '<li>
                    <a href="#" onclick="change_aw_unw_order_status(' . $key . ', ' . $id . ', \'' . htmlspecialchars($table_name, ENT_QUOTES) . '\'); return false;">
                        ' . $label['text'] . '
                    </a>
                </li>';
                }
            }

            $html .= '</ul>';
            $html .= '</div>';
        }

        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }
    /**
     * { change payment status }
     *
     * @param      integer  $status     The status
     * @param         $invoice_id  The invoice id
     * @return     json
     */

    public function change_payment_status($status, $invoice_id)
    {
        $success = $this->purchase_model->change_payment_status($status, $invoice_id);
        $message = '';
        $html = '';
        $status_str = '';
        $class = '';
        if ($success == true) {
            $message = _l('change_payment_status_successfully');
        } else {
            $message = _l('change_payment_status_fail');
        }
        if (has_permission('purchase_invoices', '', 'edit') || is_admin()) {
            $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $invoice_id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $html .= '</a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $invoice_id . '">';

            if ($status == 1) {
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 0 ,' . $invoice_id . '); return false;">
                            ' . _l('unpaid') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 2 ,' . $invoice_id . '); return false;">
                            ' . _l('recevied_with_comments') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 3 ,' . $invoice_id . '); return false;">
                            ' . _l('bill_verification_in_process') . '
                            </a>
                        </li>';

                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 4 ,' . $invoice_id . '); return false;">
                                ' . _l('bill_verification_on_hold') . '
                                </a>
                            </li>';
                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 5 ,' . $invoice_id . '); return false;">
                                    ' . _l('bill_verified_by_ril') . '
                                </a>
                            </li>';
                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 7 ,' . $invoice_id . '); return false;">
                                ' . _l('payment_processed') . '
                                </a>
                            </li>';
                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 6 ,' . $invoice_id . '); return false;">
                                    ' . _l('payment_certifiate_issued') . '
                                </a>
                            </li>';
                $status_str = _l('rejected');
                $class = 'label-danger';
            } elseif ($status == 2) {
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 0 ,' . $invoice_id . '); return false;">
                            ' . _l('unpaid') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 1 ,' . $invoice_id . '); return false;">
                            ' . _l('rejected') . '
                            </a>
                        </li>';

                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 3 ,' . $invoice_id . '); return false;">
                            ' . _l('bill_verification_in_process') . '
                            </a>
                        </li>';

                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 4 ,' . $invoice_id . '); return false;">
                            ' . _l('bill_verification_on_hold') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 5 ,' . $invoice_id . '); return false;">
                                ' . _l('bill_verified_by_ril') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 7 ,' . $invoice_id . '); return false;">
                            ' . _l('payment_processed') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 6 ,' . $invoice_id . '); return false;">
                                ' . _l('payment_certifiate_issued') . '
                            </a>
                        </li>';
                $status_str = _l('recevied_with_comments');
                $class = 'label-info';
            } elseif ($status == 3) {
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 0 ,' . $invoice_id . '); return false;">
                            ' . _l('unpaid') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 1 ,' . $invoice_id . '); return false;">
                            ' . _l('rejected') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 2 ,' . $invoice_id . '); return false;">
                            ' . _l('recevied_with_comments') . '
                            </a>
                        </li>';

                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 4 ,' . $invoice_id . '); return false;">
                            ' . _l('bill_verification_on_hold') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 5 ,' . $invoice_id . '); return false;">
                                ' . _l('bill_verified_by_ril') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 6 ,' . $invoice_id . '); return false;">
                                ' . _l('payment_certifiate_issued') . '
                            </a>
                        </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 7 ,' . $invoice_id . '); return false;">
                            ' . _l('payment_processed') . '
                            </a>
                        </li>';

                $status_str = _l('bill_verification_in_process');
                $class = 'label-warning';
            } elseif ($status == 4) {
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 0 ,' . $invoice_id . '); return false;">
                ' . _l('unpaid') . '
                </a>
            </li>';
                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 1 ,' . $invoice_id . '); return false;">
                                ' . _l('rejected') . '
                                </a>
                            </li>';
                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 2 ,' . $invoice_id . '); return false;">
                                ' . _l('recevied_with_comments') . '
                                </a>
                            </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 3 ,' . $invoice_id . '); return false;">
                            ' . _l('bill_verification_in_process') . '
                            </a>
                        </li>';


                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 5 ,' . $invoice_id . '); return false;">
                                    ' . _l('bill_verified_by_ril') . '
                                </a>
                            </li>';

                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 6 ,' . $invoice_id . '); return false;">
                                    ' . _l('payment_certifiate_issued') . '
                                </a>
                            </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 7 ,' . $invoice_id . '); return false;">
                            ' . _l('payment_processed') . '
                            </a>
                        </li>';
                $status_str = _l('bill_verification_on_hold');
                $class = 'label-primary';
            } elseif ($status == 5) {
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 0 ,' . $invoice_id . '); return false;">
                ' . _l('unpaid') . '
                </a>
            </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 1 ,' . $invoice_id . '); return false;">
                            ' . _l('rejected') . '
                            </a>
                        </li>';
                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 2 ,' . $invoice_id . '); return false;">
                                ' . _l('recevied_with_comments') . '
                                </a>
                            </li>';
                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 3 ,' . $invoice_id . '); return false;">
                                ' . _l('bill_verification_in_process') . '
                                </a>
                            </li>';

                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 4 ,' . $invoice_id . '); return false;">
                            ' . _l('bill_verification_on_hold') . '
                            </a>
                        </li>';
                $html .= '<li>
                        <a href="#" onclick="change_payment_status( 6 ,' . $invoice_id . '); return false;">
                            ' . _l('payment_certifiate_issued') . '
                        </a>
                    </li>';
                $html .= '<li>
                                <a href="#" onclick="change_payment_status( 7 ,' . $invoice_id . '); return false;">
                                    ' . _l('payment_processed') . '
                                    </a>
                                </li>';

                $status_str = _l('bill_verified_by_ril');
                $class = 'label-success';
            } elseif ($status == 6) {
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 0 ,' . $invoice_id . '); return false;">
                ' . _l('unpaid') . '
                </a>
            </li>';
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 1 ,' . $invoice_id . '); return false;">
                   ' . _l('rejected') . '
                </a>
             </li>';
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 2 ,' . $invoice_id . '); return false;">
                   ' . _l('recevied_with_comments') . '
                </a>
             </li>';
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 3 ,' . $invoice_id . '); return false;">
                   ' . _l('bill_verification_in_process') . '
                </a>
             </li>';

                $html .= '<li>
                <a href="#" onclick="change_payment_status( 4 ,' . $invoice_id . '); return false;">
                   ' . _l('bill_verification_on_hold') . '
                </a>
             </li>';
                $html .= '<li>
                            <a href="#" onclick="change_payment_status( 5 ,' . $invoice_id . '); return false;">
                                ' . _l('bill_verified_by_ril') . '
                            </a>
                        </li>';
                $html .= '<li>
                        <a href="#" onclick="change_payment_status( 7 ,' . $invoice_id . '); return false;">
                        ' . _l('payment_processed') . '
                        </a>
                    </li>';

                $status_str = _l('payment_certifiate_issued');
                $class = 'label-success';
            } elseif ($status == 7) {
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 0 ,' . $invoice_id . '); return false;">
                ' . _l('unpaid') . '
                </a>
            </li>';
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 1 ,' . $invoice_id . '); return false;">
                   ' . _l('rejected') . '
                </a>
             </li>';
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 2 ,' . $invoice_id . '); return false;">
                   ' . _l('recevied_with_comments') . '
                </a>
             </li>';
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 3 ,' . $invoice_id . '); return false;">
                   ' . _l('bill_verification_in_process') . '
                </a>
             </li>';
                $html .= '<li>
                        <a href="#" onclick="change_payment_status( 4 ,' . $invoice_id . '); return false;">
                        ' . _l('bill_verification_on_hold') . '
                        </a>
                    </li>';
                $html .= '<li>
                        <a href="#" onclick="change_payment_status( 5 ,' . $invoice_id . '); return false;">
                            ' . _l('bill_verified_by_ril') . '
                        </a>
                    </li>';

                $html .= '<li>
                        <a href="#" onclick="change_payment_status( 6 ,' . $invoice_id . '); return false;">
                            ' . _l('payment_certifiate_issued') . '
                        </a>
                    </li>';
                $status_str = _l('payment_processed');
                $class = 'label-success';
            } elseif ($status == 0) {
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 1 ,' . $invoice_id . '); return false;">
                   ' . _l('rejected') . '
                </a>
             </li>';
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 2 ,' . $invoice_id . '); return false;">
                   ' . _l('recevied_with_comments') . '
                </a>
             </li>';
                $html .= '<li>
                <a href="#" onclick="change_payment_status( 3 ,' . $invoice_id . '); return false;">
                   ' . _l('bill_verification_in_process') . '
                </a>
             </li>';

                $html .= '<li>
                        <a href="#" onclick="change_payment_status( 4 ,' . $invoice_id . '); return false;">
                        ' . _l('bill_verification_on_hold') . '
                        </a>
                    </li>';
                $html .= '<li>
                        <a href="#" onclick="change_payment_status( 5 ,' . $invoice_id . '); return false;">
                            ' . _l('bill_verified_by_ril') . '
                        </a>
                    </li>';

                $html .= '<li>
                        <a href="#" onclick="change_payment_status( 6 ,' . $invoice_id . '); return false;">
                            ' . _l('payment_certifiate_issued') . '
                        </a>
                    </li>';
                $html .= '<li>
                    <a href="#" onclick="change_payment_status( 7 ,' . $invoice_id . '); return false;">
                    ' . _l('payment_processed') . '
                    </a>
                </li>';
                $status_str = _l('unpaid');
                $class = 'label-danger';
            }

            $html .= '</ul>';
            $html .= '</div>';
        }

        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }
    /**
     * { convert po payment }
     */
    public function convert_po_payment($pur_order)
    {
        $success = $this->purchase_model->convert_po_payment($pur_order);
        $mess = '';
        if ($success == true) {
            $mess = _l('converted_succesfully');
        } else {
            $mess = _l('no_payments_are_converted');
        }

        echo json_encode([
            'mess' => $mess,
            'success' => $success,
        ]);
    }

    /**
     * Gets the comments.
     *
     * @param        $id     The identifier
     */
    public function get_comments($id, $type)
    {
        $data['comments'] = $this->purchase_model->get_comments($id, $type);
        $this->load->view('comments_template', $data);
    }
    public function get_comments_wo($id, $type)
    {
        $data['comments'] = $this->purchase_model->get_comments($id, $type);
        $this->load->view('comments_template', $data);
    }
    /**
     * Adds a comment.
     */
    public function add_comment()
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->purchase_model->add_comment($this->input->post()),
            ]);
        }
    }
    public function add_commen_wo()
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->purchase_model->add_comment($this->input->post()),
            ]);
        }
    }

    /**
     * { edit comment }
     *
     * @param        $id     The identifier
     */
    public function edit_comment($id)
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->purchase_model->edit_comment($this->input->post(), $id),
                'message' => _l('comment_updated_successfully'),
            ]);
        }
    }

    /**
     * Removes a comment.
     *
     * @param        $id     The identifier
     */
    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'pur_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            echo json_encode([
                'success' => $this->purchase_model->remove_comment($id),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    /**
     * { coppy sale invoice }
     *
     * @param        $invoice  The invoice
     */
    public function coppy_sale_invoice($invoice)
    {
        $this->load->model('currencies_model');
        $this->load->model('invoices_model');

        $inv = $this->invoices_model->get($invoice);
        $base_currency = $this->currencies_model->get_base_currency();

        $list_item = $this->purchase_model->create_purchase_request_row_template();
        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if ($inv->currency != 0 && $inv->currency != $base_currency->id) {
            $inv_currency = pur_get_currency_by_id($inv->currency);
            $currency_rate = pur_get_currency_rate($base_currency->name, $inv_currency->name);;
            $to_currency = $inv->currency;
        }

        if ($inv && isset($inv->items)) {
            if (count($inv->items) > 0) {

                $index_request = 0;
                foreach ($inv->items as $key => $item) {
                    $index_request++;

                    $item_taxes = get_invoice_item_taxes($item['id']);

                    $tax = '';
                    $tax_value = 0;
                    $tax_name = [];
                    $tax_name[0] = '';
                    $tax_rate = '';

                    if (count($item_taxes) > 0) {
                        foreach ($item_taxes as $key => $_tax) {
                            if (($key + 1) < count($item_taxes)) {
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']) . '|';

                                $tax_rate .= $_tax['taxrate'] . '|';
                            } else {
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']);

                                $tax_rate .= $_tax['taxrate'];
                            }
                            $tax_name[] = $_tax['taxname'];

                            $tax_value += $item['qty'] * $item['rate'] * $_tax['taxrate'] / 100;
                        }
                    }



                    $item_code = get_item_id_by_des($item['description']);
                    $item_text = $item['description'];
                    $unit_price = $item['rate'];
                    $unit_name = $item['unit'];
                    $into_money = (float) ($item['rate'] * $item['qty']);
                    $total = $tax_value + $into_money;

                    $list_item .= $this->purchase_model->create_purchase_request_row_template('newitems[' . $index_request . ']', $item_code, $item_text, $item['long_description'], '', '', $unit_price, $item['qty'], $unit_name, '', $into_money, $index_request, $tax_value, $total, $tax_name, $tax_rate, $tax, false, $currency_rate, $to_currency);
                }
            }
        }

        echo json_encode([
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
    }

    /**
     * { coppy sale invoice }
     *
     * @param        $invoice  The invoice
     */
    public function coppy_sale_estimate($estimate_id)
    {
        $this->load->model('currencies_model');
        $this->load->model('estimates_model');

        $estimate = $this->estimates_model->get($estimate_id);
        $base_currency = $this->currencies_model->get_base_currency();

        $list_item = $this->purchase_model->create_purchase_request_row_template();
        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if ($estimate->currency != 0 && $estimate->currency != $base_currency->id) {
            $es_currency = pur_get_currency_by_id($estimate->currency);
            $currency_rate = pur_get_currency_rate($base_currency->name, $es_currency->name);;
            $to_currency = $estimate->currency;
        }

        if ($estimate && isset($estimate->items)) {
            if (count($estimate->items) > 0) {

                $index_request = 0;
                foreach ($estimate->items as $key => $item) {
                    $index_request++;

                    $item_taxes = get_invoice_item_taxes($item['id']);

                    $tax = '';
                    $tax_value = 0;
                    $tax_name = [];
                    $tax_name[0] = '';
                    $tax_rate = '';

                    if (count($item_taxes) > 0) {
                        foreach ($item_taxes as $key => $_tax) {
                            if (($key + 1) < count($item_taxes)) {
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']) . '|';

                                $tax_rate .= $_tax['taxrate'] . '|';
                            } else {
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']);

                                $tax_rate .= $_tax['taxrate'];
                            }
                            $tax_name[] = $_tax['taxname'];

                            $tax_value += $item['qty'] * $item['rate'] * $_tax['taxrate'] / 100;
                        }
                    }



                    $item_code = get_item_id_by_des($item['description']);
                    $item_text = $item['description'];
                    $unit_price = $item['rate'];
                    $unit_name = $item['unit'];
                    $into_money = (float) ($item['rate'] * $item['qty']);
                    $total = $tax_value + $into_money;

                    $list_item .= $this->purchase_model->create_purchase_request_row_template('newitems[' . $index_request . ']', $item_code, $item_text, $item['long_description'], '', '', $unit_price, $item['qty'], $unit_name, '', $into_money, $index_request, $tax_value, $total, $tax_name, $tax_rate, $tax, false, $currency_rate, $to_currency);
                }
            }
        }

        echo json_encode([
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
    }

    /**
     * { inv by client }
     */
    public function inv_by_client()
    {
        $data_rs = [];
        $html = '';
        if ($this->input->post()) {
            $clients = $this->input->post('client');
            foreach ($clients as $cli) {
                $list_inv = $this->purchase_model->get_inv_by_client_for_po($cli);
                if (count($list_inv) > 0) {
                    foreach ($list_inv as $inv) {
                        if (total_rows(db_prefix() . 'pur_orders', ['sale_invoice' => $inv['id']]) <= 0) {
                            $data_rs[] = $inv;
                        }
                    }
                }
            }
        } else {
            $data_rs = $this->purchase_model->get_invoice_for_pr();
        }

        $html .= '<option value=""></option>';
        foreach ($data_rs as $rs) {
            $html .= '<option value="' . $rs['id'] . '">' . format_invoice_number($rs['id']) . '</option>';
        }

        echo json_encode(['html' => $html]);
    }

    /**
     * { coppy sale invoice }
     *
     * @param        $invoice  The invoice
     */
    public function coppy_sale_invoice_po($invoice)
    {
        $this->load->model('currencies_model');
        $this->load->model('invoices_model');

        $inv = $this->invoices_model->get($invoice);
        $base_currency = $this->currencies_model->get_base_currency();

        $list_item = $this->purchase_model->create_purchase_order_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if ($inv->currency != 0 && $inv->currency != $base_currency->id) {
            $inv_currency = pur_get_currency_by_id($inv->currency);
            $currency_rate = pur_get_currency_rate($base_currency->name, $inv_currency->name);;
            $to_currency = $inv->currency;
        }

        $discount_type = 'after_tax';
        $discount_total = 0;
        if ($inv) {
            $discount_type = $inv->discount_type;
            $discount_total = $inv->discount_total;
        }

        if ($inv && isset($inv->items)) {
            if (count($inv->items) > 0) {
                $index_request = 0;
                foreach ($inv->items as $key => $item) {
                    $index_request++;

                    $item_taxes = get_invoice_item_taxes($item['id']);

                    $tax = '';
                    $tax_value = 0;
                    $tax_name = [];

                    $tax_rate = '';

                    if (count($item_taxes) > 0) {
                        foreach ($item_taxes as $key => $_tax) {
                            if (($key + 1) < count($item_taxes)) {
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']) . '|';

                                $tax_rate .= $_tax['taxrate'] . '|';
                            } else {
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']);

                                $tax_rate .= $_tax['taxrate'];
                            }
                            $tax_name[] = $_tax['taxname'];

                            $tax_value += $item['qty'] * $item['rate'] * $_tax['taxrate'] / 100;
                        }
                    }

                    $item_code = get_item_id_by_des($item['description']);
                    $item_text = $item['description'];
                    $unit_price = $item['rate'];
                    $unit_name = $item['unit'];
                    $into_money = (float) ($item['rate'] * $item['qty']);
                    $total = $tax_value + $into_money;

                    $list_item .= $this->purchase_model->create_purchase_order_row_template('newitems[' . $index_request . ']', $item_text, $item['long_description'], '', '', $item['qty'], $unit_name, $unit_price, $tax_name, $item_code, '', $tax_rate, $total, '', '', $total, $into_money, $tax, $tax_value, $index_request, false, $currency_rate, $to_currency);
                }
            }
        }


        echo json_encode([
            'discount_type' => $discount_type,
            'discount_total' => $discount_total,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
    }

    /**
     * { table vendor }
     */
    public function dashboard_po_table()
    {
        $this->app->get_table_data(module_views_path('purchase', 'dashboard_po_table'));
    }

    /**
     * Compares the quote pur request.
     *
     * @param        $pur_request  The pur request
     */
    public function compare_quote_pur_request($pur_request)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->purchase_model->update_compare_quote($pur_request, $data);
            if ($success) {
                set_alert('success', _l('updated_successfully'));
            }
            redirect(admin_url('purchase/view_pur_request/' . $pur_request));
        }
    }

    /**
     * { debit notes }
     *
     * @param      string  $id     The identifier
     */
    public function debit_notes($id = '')
    {
        if (!has_permission('purchase_debit_notes', '', 'view') && !is_admin() && !has_permission('purchase_debit_notes', '', 'view_own')) {
            access_denied('debit_notes');
        }

        close_setup_menu();

        $data['years']          = $this->purchase_model->get_debits_years();
        $data['statuses']       = $this->purchase_model->get_debit_note_statuses();
        $data['debit_note_id'] = $id;

        $data['title']          = _l('pur_debit_note');

        $this->load->view('debit_notes/manage', $data);
    }

    /**
     * { debit notes table }
     */
    public function debit_notes_table()
    {
        $this->app->get_table_data(module_views_path('purchase', 'debit_notes/table_debit_notes'));
    }


    /**
     * { debit note }
     *
     * @param      string  $id     The identifier
     */
    public function debit_note($id = '')
    {
        if (!has_permission('purchase_debit_notes', '', 'view') && !is_admin() && !has_permission('purchase_debit_notes', '', 'view_own')) {
            access_denied('debit_notes');
        }
        if ($this->input->post()) {
            $debit_note_data = $this->input->post();
            $debit_note_data['terms'] = $this->input->post('terms', false);
            if ($id == '') {
                if (!has_permission('purchase_debit_notes', '', 'create')) {
                    access_denied('debit_notes');
                }
                $id = $this->purchase_model->add_debit_note($debit_note_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('debit_note')));
                    redirect(admin_url('purchase/debit_notes/' . $id));
                }
            } else {
                if (!has_permission('purchase_debit_notes', '', 'edit')) {
                    access_denied('debit_notes');
                }
                $success = $this->purchase_model->update_debit_note($debit_note_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('debit_note')));
                }
                redirect(admin_url('purchase/debit_notes/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('debit_note'));
            $data['isedit'] = false;
        } else {
            $debit_note = $this->purchase_model->get_debit_note($id);

            if (!has_permission('purchase_debit_notes', '', 'edit')) {
                access_denied('debit_notes');
            }

            if (!$debit_note || (!has_permission('purchase_debit_notes', '', 'view') && !has_permission('purchase_debit_notes', '', 'view_own') && $debit_note->addedfrom != get_staff_user_id() && !in_array($debit_note->vendorid, get_vendor_admin_list()))) {
                blank_page(_l('credit_note_not_found'), 'danger');
            }

            $data['debit_note'] = $debit_note;
            $data['edit']        = true;
            $title               = _l('edit', _l('debit_note')) . ' - ' . format_debit_note_number($debit_note->id);
            $data['isedit'] = true;
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');


        $data['vendors'] = $this->purchase_model->get_vendor();


        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['pur_orders'] = $this->purchase_model->get_pur_order_approved();
        $data['wo_orders']  =  $this->purchase_model->get_wo_order_approved();
        $data['title']      = $title;
        $data['bodyclass']  = 'credit-note';
        $this->load->view('debit_notes/debit_note', $data);
    }

    /**
     * { validate number }
     */
    public function validate_debit_note_number()
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
        if (total_rows(db_prefix() . 'pur_debit_notes', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * { vendor change data }
     *
     * @param        $vendor  The vendor
     */
    public function vendor_change_data($vendor)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $data                     = [];
            $data['billing_shipping'] = $this->purchase_model->get_vendor_billing_and_shipping_details($vendor);
            $data['vendor_currency']  = get_vendor_currency($vendor);

            $option_html = '';

            if (total_rows(db_prefix() . 'pur_vendor_items', ['vendor' => $vendor]) <= ajax_on_total_items()) {
                $items = $this->purchase_model->get_items_by_vendor_variation($vendor);
                $option_html .= '<option value=""></option>';
                foreach ($items as $item) {
                    $option_html .= '<option value="' . $item['id'] . '" >' . $item['label'] . '</option>';
                }
            }

            $data['option_html'] = $option_html;

            echo json_encode($data);
        }
    }

    /**
     * Gets the debit note data ajax.
     *
     * @param        $id     The identifier
     */
    public function get_debit_note_data_ajax($id)
    {
        if (!has_permission('purchase_debit_notes', '', 'view') && !is_admin() && !has_permission('purchase_debit_notes', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die(_l('debit_note_not_found'));
        }

        $debit_note = $this->purchase_model->get_debit_note($id);

        if (!$debit_note || (!has_permission('purchase_debit_notes', '', 'view') && !has_permission('purchase_debit_notes', '', 'view_own'))) {
            echo _l('debit_note_not_found');
            die;
        }

        if (has_permission('purchase_debit_notes', '', 'view_own') && !is_admin()) {
            if ($debit_note->addedfrom != get_staff_user_id() && !in_array($debit_note->vendorid, get_vendor_admin_list(get_staff_user_id()))) {
                echo _l('access_denied');
                die;
            }
        }

        $data['vendor_contacts'] = $this->purchase_model->get_contacts($debit_note->vendorid);
        $data['debit_note']                   = $debit_note;
        $data['members']                       = $this->staff_model->get('', ['active' => 1]);
        $data['available_debitable_invoices'] = $this->purchase_model->get_available_debitable_invoices($id);
        $data['pur_order_name'] = $this->purchase_model->get_pur_order($debit_note->pur_order);
        $data['wo_order_name']  = $this->purchase_model->get_wo_order($debit_note->wo_order);
        $this->load->view('debit_notes/debit_note_preview_template', $data);
    }

    /**
     * { delete debit note }
     */
    public function delete_debit_note($id)
    {
        if (!has_permission('purchase_debit_notes', '', 'delete')) {
            access_denied('debit_notes');
        }

        if (!$id) {
            redirect(admin_url('debit_notes'));
        }

        $debit_note = $this->purchase_model->get_debit_note($id);

        if ($debit_note->debit_used || $debit_note->status == 2) {
            $success = false;
        } else {
            $success = $this->purchase_model->delete_debit_note($id);
        }

        if ($success) {
            set_alert('success', _l('deleted', _l('debit_note')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('debit_note_lowercase')));
        }

        redirect(admin_url('purchase/debit_notes'));
    }

    /**
     * { apply debits to invoices }
     *
     * @param        $debit_note_id  The debit note identifier
     */
    public function apply_debits_to_invoices($debit_note_id)
    {
        $debitApplied = false;
        if ($this->input->post()) {
            foreach ($this->input->post('amount') as $invoice_id => $amount) {
                if ($this->purchase_model->apply_debits($debit_note_id, ['amount' => $amount, 'invoice_id' => $invoice_id])) {
                    $this->purchase_model->update_pur_invoice_status($invoice_id);
                    $debitApplied = true;
                }
            }
        }
        if ($debitApplied) {
            set_alert('success', _l('debits_successfully_applied_to_invoices'));
        }
        redirect(admin_url('purchase/debit_notes/' . $credit_note_id));
    }

    /**
     * { delete debit note applied debit }
     *
     * @param        $id          The identifier
     * @param        $debit_id   The debit identifier
     * @param        $invoice_id  The invoice identifier
     */
    public function delete_debit_note_applied_debit($id, $debit_id, $invoice_id)
    {
        if (has_permission('purchase_debit_notes', '', 'delete')) {
            $this->purchase_model->delete_applied_debit($id, $debit_id, $invoice_id);
        }
        redirect(admin_url('purchase/debit_notes/' . $debit_id));
    }

    /**
     * { mark debit note open }
     *
     * @param        $id     The identifier
     */
    public function mark_debit_note_open($id)
    {
        if (total_rows(db_prefix() . 'pur_debit_notes', ['status' => 3, 'id' => $id]) > 0 && has_permission('purchase_debit_notes', '', 'edit')) {
            $this->purchase_model->mark_debit_note($id, 1);
        }

        redirect(admin_url('purchase/debit_notes/' . $id));
    }

    /**
     * { mark debit note void }
     *
     * @param        $id     The identifier
     */
    public function mark_debit_note_void($id)
    {
        $debit_note = $this->purchase_model->get_debit_note($id);
        if ($debit_note->status != 2 && $debit_note->status != 3 && !$debit_note->debits_used && has_permission('purchase_debit_notes', '', 'edit')) {
            $this->purchase_model->mark_debit_note($id, 3);
        }
        redirect(admin_url('purchase/debit_notes/' . $id));
    }

    /**
     * { refund }
     *
     * @param        $id         The identifier
     * @param        $refund_id  The refund identifier
     */
    public function refund_debit_note($id, $refund_id = null)
    {
        if (has_permission('purchase_debit_notes', '', 'edit')) {
            $this->load->model('payment_modes_model');
            if (!$refund_id) {
                $data['payment_modes'] = $this->payment_modes_model->get('', [
                    'expenses_only !=' => 1,
                ]);
            } else {
                $data['refund']        = $this->purchase_model->get_refund($refund_id);
                $data['payment_modes'] = $this->payment_modes_model->get('', [], true, true);
                $i                     = 0;
                foreach ($data['payment_modes'] as $mode) {
                    if ($mode['active'] == 0 && $data['refund']->payment_mode != $mode['id']) {
                        unset($data['payment_modes'][$i]);
                    }
                    $i++;
                }
            }

            $data['debit_note'] = $this->purchase_model->get_debit_note($id);
            $this->load->view('debit_notes/refund', $data);
        }
    }

    /**
     * Creates a refund.
     *
     * @param        $debit_note_id  The debit note identifier
     */
    public function create_refund($debit_note_id)
    {
        if (has_permission('purchase_debit_notes', '', 'edit')) {
            $data                = $this->input->post();
            $data['refunded_on'] = to_sql_date($data['refunded_on']);
            $data['staff_id']    = get_staff_user_id();
            $success             = $this->purchase_model->create_refund($debit_note_id, $data);

            if ($success) {
                set_alert('success', _l('added_successfully', _l('refund')));
            }
        }

        redirect(admin_url('purchase/debit_notes/' . $debit_note_id));
    }

    /**
     * { edit refund }
     *
     * @param        $refund_id      The refund identifier
     * @param        $debit_note_id  The debit note identifier
     */
    public function edit_refund($refund_id, $debit_note_id)
    {
        if (has_permission('purchase_debit_notes', '', 'edit')) {
            $data                = $this->input->post();
            $data['refunded_on'] = to_sql_date($data['refunded_on']);
            $success             = $this->purchase_model->edit_refund($refund_id, $data);

            if ($success) {
                set_alert('success', _l('updated_successfully', _l('refund')));
            }
        }

        redirect(admin_url('purchase/debit_notes/' . $debit_note_id));
    }

    /**
     * { delete refund }
     *
     * @param        $refund_id       The refund identifier
     * @param        $credit_note_id  The credit note identifier
     */
    public function delete_debit_refund($refund_id, $debit_note_id)
    {
        if (has_permission('purchase_debit_notes', '', 'delete')) {
            $success = $this->purchase_model->delete_refund($refund_id, $debit_note_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('refund')));
            }
        }
        redirect(admin_url('purchase/debit_notes/' . $debit_note_id));
    }

    /**
     * { delete attachment }
     *
     * @param        $id     The identifier
     */
    public function delete_debit_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->purchase_model->delete_attachment($id);
        } else {
            ajax_access_denied();
        }
    }

    /* Generates credit note PDF and send to email */
    public function debit_note_pdf($id)
    {

        if (!$id) {
            redirect(admin_url('purchase/debit_notes'));
        }
        $debit_note        = $this->purchase_model->get_debit_note($id);
        $debit_note_number = format_debit_note_number($debit_note->id);
        // Fetch order names
        $pur_order_name = $this->purchase_model->get_pur_order($debit_note->pur_order);
        $wo_order_name  = $this->purchase_model->get_wo_order($debit_note->wo_order);

        // Attach to credit_note object
        $debit_note->pur_order_name = $pur_order_name;
        $debit_note->wo_order_name  = $wo_order_name;
        try {
            $pdf = debit_note_pdf($debit_note);
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

        $pdf->Output(mb_strtoupper(slug_it($debit_note_number)) . '.pdf', $type);
    }

    /**
     * Sends a purchase order.
     * @return redirect
     */
    public function send_debit_note()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_debit_note($data);
            if ($send) {
                set_alert('success', _l('send_debit_note_successfully'));
            } else {
                set_alert('warning', _l('send_debit_note_fail'));
            }
            redirect(admin_url('purchase/debit_notes/' . $data['debit_note_id']));
        }
    }

    /**
     * { apply debits }
     *
     * @param        $invoice_id  The invoice identifier
     */
    public function apply_debits($invoice_id)
    {
        $total_debits_applied = 0;
        foreach ($this->input->post('amount') as $debit_id => $amount) {
            $success = $this->purchase_model->apply_debits($debit_id, [
                'invoice_id' => $invoice_id,
                'amount'     => $amount,
            ]);
            if ($success) {
                $total_debits_applied++;
            }
        }

        if ($total_debits_applied > 0) {
            $this->purchase_model->update_pur_invoice_status($invoice_id);
            set_alert('success', _l('invoice_credits_applied'));
        }
        redirect(admin_url('purchase/purchase_invoice/' . $invoice_id));
    }

    /**
     * { delete invoice applied debit }
     *
     * @param        $id          The identifier
     * @param        $debit_id    The debit identifier
     * @param        $invoice_id  The invoice identifier
     */
    public function delete_invoice_applied_debit($id, $debit_id, $invoice_id)
    {
        if (has_permission('purchase_debit_notes', '', 'delete')) {
            $this->purchase_model->delete_applied_debit($id, $debit_id, $invoice_id);
        }
        redirect(admin_url('purchase/purchase_invoice/' . $invoice_id));
    }

    /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_pur_invoices($vendor)
    {
        $this->app->get_table_data(module_views_path('purchase', 'invoices/table_pur_invoices'), ['vendor' => $vendor]);
    }

    /**
     * { table vendor debit notes }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_debit_notes($vendor)
    {
        $this->app->get_table_data(module_views_path('purchase', 'debit_notes/table_debit_notes'), ['vendor' => $vendor]);
    }

    /**
     * { statement }
     */
    public function statement()
    {
        if (!has_permission('purchase_vendors', '', 'view') && !is_admin()) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');
            echo _l('access_denied');
            die;
        }

        $vendor_id = $this->input->get('vendor_id');
        $from        = $this->input->get('from');
        $to          = $this->input->get('to');

        $data['statement'] = $this->purchase_model->get_statement($vendor_id, to_sql_date($from), to_sql_date($to));

        $data['from'] = $from;
        $data['to']   = $to;

        $viewData['html'] = $this->load->view('vendors/groups/_statement', $data, true);

        echo json_encode($viewData);
    }

    /**
     * { statement pdf }
     */
    public function statement_pdf()
    {
        $vendor_id = $this->input->get('vendor_id');

        if (!has_permission('purchase_vendors', '', 'view') && !is_admin()) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('purchase/vendor/' . $vendor_id));
        }

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $data['statement'] = $this->purchase_model->get_statement($vendor_id, to_sql_date($from), to_sql_date($to));

        try {
            $pdf = purchase_statement_pdf($data['statement']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it(_l('vendor_statement') . '-' . $data['statement']['client']->company) . '.pdf', $type);
    }

    /**
     * Sends a purchase statment.
     * @return redirect
     */
    public function send_statement()
    {
        $vendor_id = $this->input->get('vendor_id');

        if (!has_permission('purchase_vendors', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('purchase/vendor/' . $vendor_id));
        }

        $data = $this->input->post();

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $data['from'] = $from;
        $data['to'] = $to;

        $data['content'] = $this->input->post('content', false);
        $data['vendor_id'] = $vendor_id;
        $success = $this->purchase_model->send_statement_to_email($data);

        if ($success) {
            set_alert('success', _l('statement_sent_to_vendor_success'));
        } else {
            set_alert('danger', _l('statement_sent_to_vendor_fail'));
        }

        redirect(admin_url('purchase/vendor/' . $vendor_id . '?group=purchase_statement'));
    }

    /**
     * permission modal
     * @return [type] 
     */
    public function permission_modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model('staff_model');

        if ($this->input->post('slug') === 'update') {
            $staff_id = $this->input->post('staff_id');
            $role_id = $this->input->post('role_id');

            $data = ['funcData' => ['staff_id' => isset($staff_id) ? $staff_id : null]];

            if (isset($staff_id)) {
                $data['member']  = $this->staff_model->get($staff_id);
            }

            $data['roles_value']         = $this->roles_model->get();
            $data['staffs']  = purchase_get_staff_id_dont_permissions();
            $add_new = $this->input->post('add_new');

            if ($add_new == ' hide') {
                $data['add_new']        = ' hide';
                $data['display_staff']  = '';
            } else {
                $data['add_new'] = '';
                $data['display_staff']  = ' hide';
            }


            $this->load->view('includes/permissions_modal', $data);
        }
    }

    public function permission_table()
    {
        if ($this->input->is_ajax_request()) {

            $select = [
                'staffid',
                'CONCAT(firstname," ",lastname) as full_name',
                'firstname', //for role name
                'email',
                'phonenumber',
            ];
            $where = [];
            $where[] = 'AND ' . db_prefix() . 'staff.admin != 1';

            $arr_staff_id = purchase_get_staff_id_permissions();

            if (count($arr_staff_id) > 0) {
                $where[] = 'AND ' . db_prefix() . 'staff.staffid IN (' . implode(', ', $arr_staff_id) . ')';
            } else {
                $where[] = 'AND ' . db_prefix() . 'staff.staffid IN ("")';
            }

            $aColumns     = $select;
            $sIndexColumn = 'staffid';
            $sTable       = db_prefix() . 'staff';
            $join         = ['LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'roles.name as role_name', db_prefix() . 'staff.role']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $not_hide = '';

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['full_name']  . '</a>';

                $row[] = $aRow['role_name'];
                $row[] = $aRow['email'];
                $row[] = $aRow['phonenumber'];

                $options = '';

                if (is_admin() || has_permission('purchase_settings', '', 'edit')) {
                    $options = icon_btn('#', 'fa fa-pencil-square', 'btn-default', [
                        'title'   => _l('edit'),
                        'onclick' => 'permissions_update(' . $aRow['staffid'] . ', ' . $aRow['role'] . ', ' . $not_hide . '); return false;',
                    ]);
                }

                if (is_admin() || has_permission('purchase_settings', '', 'edit')) {
                    $options .= icon_btn('purchase/delete_purchase_permission/' . $aRow['staffid'], 'fa fa-remove', 'btn-danger _delete', ['title' => _l('delete')]);
                }

                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * staff id changed
     * @param  [type] $staff_id 
     * @return [type]           
     */
    public function staff_id_changed($staff_id)
    {
        $role_id = '';
        $status = 'false';
        $r_permission = [];

        $staff  = $this->staff_model->get($staff_id);

        if ($staff) {
            if (count($staff->permissions) > 0) {
                foreach ($staff->permissions as $permission) {
                    $r_permission[$permission['feature']][] = $permission['capability'];
                }
            }

            $role_id = $staff->role;
            $status = 'true';
        }

        if (count($r_permission) > 0) {
            $data = ['role_id'   => $role_id, 'status'    => $status, 'permission' => 'true', 'r_permission' => $r_permission];
        } else {
            $data = ['role_id'   => $role_id, 'status'    => $status, 'permission' => 'false', 'r_permission' => $r_permission];
        }

        echo json_encode($data);
        die;
    }


    /**
     * purchase update permissions
     * @param  string $id 
     * @return [type]     
     */
    public function purchase_update_permissions($id = '')
    {
        if (!is_admin() && !has_permission('purchase_settings', '', 'edit')) {
            access_denied('purchase');
        }
        $data = $this->input->post();

        if (!isset($id) || $id == '') {
            $id   = $data['staff_id'];
        }


        if (isset($id) && $id != '') {

            $data = hooks()->apply_filters('before_update_staff_member', $data, $id);

            if (is_admin()) {
                if (isset($data['administrator'])) {
                    $data['admin'] = 1;
                    unset($data['administrator']);
                } else {
                    if ($id != get_staff_user_id()) {
                        if ($id == 1) {
                            return [
                                'cant_remove_main_admin' => true,
                            ];
                        }
                    } else {
                        return [
                            'cant_remove_yourself_from_admin' => true,
                        ];
                    }
                    $data['admin'] = 0;
                }
            }

            $this->db->where('staffid', $id);
            $this->db->update(db_prefix() . 'staff', [
                'role'  => $data['role']
            ]);

            $response = $this->staff_model->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $data['permissions']), $id);
        } else {
            $this->load->model('roles_model');

            $role_id = $data['role'];
            unset($data['role']);
            unset($data['staff_id']);

            $data['update_staff_permissions'] = true;

            $response = $this->roles_model->update($data, $role_id);
        }

        if (is_array($response)) {
            if (isset($response['cant_remove_main_admin'])) {
                set_alert('warning', _l('staff_cant_remove_main_admin'));
            } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
            }
        } elseif ($response == true) {
            set_alert('success', _l('updated_successfully', _l('staff_member')));
        }
        redirect(admin_url('purchase/setting?group=permissions'));
    }


    /**
     * delete purchase permission
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_purchase_permission($id)
    {
        if (!is_admin() && !has_permission('purchase_settings', '', 'edit')) {
            access_denied('purchase');
        }

        $response = $this->purchase_model->delete_hr_profile_permission($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('pur_is_referenced', _l('permissions')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('permissions')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('permissions')));
        }
        redirect(admin_url('purchase/setting?group=permissions'));
    }

    /**
     * { update customfield invoice }
     *
     * @param        $id     The identifier
     */
    public function update_customfield_invoice($id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->purchase_model->update_customfield_invoice($id, $data);
            if ($success) {
                $message = _l('updated_successfully');
                set_alert('success', $message);
            }
            redirect(admin_url('purchase/purchase_invoice/' . $id));
        }
    }

    /**
     * { refresh order value }
     */
    public function refresh_order_value($po_id)
    {
        $success = false;
        if ($po_id != '') {
            $success = $this->purchase_model->refresh_order_value($po_id);
        }

        echo json_encode([
            'success' => $success,
        ]);
    }

    /**
     * Gets the purchase request row template.
     */
    public function get_purchase_request_row_template()
    {
        $name = $this->input->post('name');
        $item_text = $this->input->post('item_text');
        $item_description = $this->input->post('item_description');
        $area = $this->input->post('area');
        $image = $this->input->post('image');
        $unit_price = $this->input->post('unit_price');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_id = $this->input->post('unit_id');
        $into_money = $this->input->post('into_money');
        $item_key = $this->input->post('item_key');
        $tax_value = $this->input->post('tax_value');
        $tax_name = $this->input->post('taxname');
        $total = $this->input->post('total');
        $item_code = $this->input->post('item_code');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');

        echo $this->purchase_model->create_purchase_request_row_template($name, $item_code, $item_text, $item_description, $area, $image, $unit_price, $quantity, $unit_name, $unit_id, $into_money, $item_key, $tax_value, $total, $tax_name, '', '', false, $currency_rate, $to_currency);
    }

    /**
     * wh commodity code search
     * @return [type] 
     */
    public function pur_commodity_code_search($type = 'purchase_price', $can_be = 'can_be_purchased', $vendor = '')
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->purchase_model->pur_commodity_code_search($this->input->post('q'), $type, $can_be, false, $vendor));
        }
    }

    /**
     * wh commodity code search
     * @return [type] 
     */
    public function pur_commodity_code_search_vendor_item($type = 'purchase_price', $can_be = 'can_be_purchased', $group = '')
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->purchase_model->pur_commodity_code_search($this->input->post('q'), $type, $can_be, false, '', $group));
        }
    }

    /**
     * Gets the item by identifier.
     *
     * @param          $id             The identifier
     * @param      bool|int  $get_warehouse  The get warehouse
     * @param      bool      $warehouse_id   The warehouse identifier
     */
    public function get_item_by_id($id, $currency_rate = 1)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->purchase_model->get_item_v2($id);
            $item->long_description   = nl2br($item->long_description ?? '');

            if ($currency_rate != 1) {
                $item->purchase_price = round(($item->purchase_price * $currency_rate), 2);
            }

            $html = '<option value=""></option>';

            $item->warehouses_html = $html;

            echo json_encode($item);
        }
    }

    /**
     * Gets the quotation row template.
     */
    public function get_quotation_row_template()
    {
        $name = $this->input->post('name');
        $item_name = $this->input->post('item_name');
        $area = $this->input->post('area');
        $image = $this->input->post('image');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $item_code = $this->input->post('item_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $item_key = $this->input->post('item_key');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');

        echo $this->purchase_model->create_quotation_row_template($name, $item_name, $area, $image, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency);
    }

    /**
     * Gets the purchase order row template.
     */
    public function get_purchase_order_row_template()
    {
        $name = $this->input->post('name');
        $item_name = $this->input->post('item_name');
        $item_description = $this->input->post('item_description');
        $area = $this->input->post('area');
        $image = $this->input->post('image');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $item_code = $this->input->post('item_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $item_key = $this->input->post('item_key');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');
        $sub_groups_pur = $this->input->post('sub_groups_pur');
        $non_budget_item = $this->input->post('non_budget_item');
        echo $this->purchase_model->create_purchase_order_row_template($name, $item_name, $item_description, $area, $image, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency, [], false, $sub_groups_pur, '', $non_budget_item);
    }
    /**
     * Gets the work order row template.
     */
    public function get_wo_order_row_template()
    {
        $name = $this->input->post('name');
        $item_name = $this->input->post('item_name');
        $item_description = $this->input->post('item_description');
        $area = $this->input->post('area');
        $image = $this->input->post('image');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $item_code = $this->input->post('item_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $item_key = $this->input->post('item_key');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');
        $sub_groups_pur = $this->input->post('sub_groups_pur');
        echo $this->purchase_model->create_wo_order_row_template($name, $item_name, $item_description, $area, $image, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency, [], false, $sub_groups_pur);
    }
    /**
     * currency rate table
     * @return [type] 
     */
    public function currency_rate_table()
    {
        $this->app->get_table_data(module_views_path('purchase', 'includes/currencies/currency_rate_table'));
    }

    /**
     * update automatic conversion
     */
    public function update_setting_currency_rate()
    {
        $data = $this->input->post();
        $success = $this->purchase_model->update_setting_currency_rate($data);
        if ($success == true) {
            $message = _l('updated_successfully', _l('setting'));
            set_alert('success', $message);
        }
        redirect(admin_url('purchase/setting?group=currency_rates'));
    }

    /**
     * Gets all currency rate online.
     */
    public function get_all_currency_rate_online()
    {
        $result = $this->purchase_model->get_all_currency_rate_online();
        if ($result) {
            set_alert('success', _l('updated_successfully', _l('pur_currency_rates')));
        } else {
            set_alert('warning', _l('no_data_changes', _l('pur_currency_rates')));
        }

        redirect(admin_url('purchase/setting?group=currency_rates'));
    }

    /**
     * update currency rate
     * @return [type] 
     */
    public function update_currency_rate($id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            $result =  $this->purchase_model->update_currency_rate($data, $id);
            if ($result) {
                set_alert('success', _l('updated_successfully', _l('pur_currency_rates')));
            } else {
                set_alert('warning', _l('no_data_changes', _l('pur_currency_rates')));
            }
        }

        redirect(admin_url('purchase/setting?group=currency_rates'));
    }

    /**
     * Gets the currency rate online.
     *
     * @param        $id     The identifier
     */
    public function get_currency_rate_online($id)
    {
        $result =  $this->purchase_model->get_currency_rate_online($id);
        echo json_encode(['value' => $result]);
        die;
    }


    /**
     * delete currency
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_currency_rate($id)
    {
        if ($id != '') {
            $result =  $this->purchase_model->delete_currency_rate($id);
            if ($result) {
                set_alert('success', _l('deleted_successfully', _l('pur_currency_rates')));
            } else {
                set_alert('danger', _l('deleted_failure', _l('pur_currency_rates')));
            }
        }
        redirect(admin_url('purchase/setting?group=currency_rates'));
    }

    /**
     * currency rate modal
     * @return [type] 
     */
    public function currency_rate_modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');

        $data = [];
        $data['currency_rate'] = $this->purchase_model->get_currency_rate($id);

        $this->load->view('includes/currencies/currency_rate_modal', $data);
    }

    /**
     * currency rate table
     * @return [type] 
     */
    public function currency_rate_logs_table()
    {
        $this->app->get_table_data(module_views_path('purchase', 'includes/currencies/currency_rate_logs_table'));
    }

    /**
     * { pur inv payment change }
     *
     * @param        $invoice_id  The invoice identifier
     */
    public function pur_inv_payment_change($invoice_id)
    {
        $amount = purinvoice_left_to_pay($invoice_id);

        echo json_encode([
            'amount' => $amount,
        ]);
    }

    /**
     * Adds a payment on PO.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_payment_on_po_with_inv($pur_order)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->purchase_model->add_payment_on_po_with_inv($data);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(admin_url('purchase/purchase_order/' . $pur_order));
        }
    }


    /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_project_pur_order($project_id)
    {
        $this->app->get_table_data(module_views_path('purchase', 'purchase_order/table_pur_order'), ['project' => $project_id]);
    }

    /**
     * Gets the project information.
     */
    public function get_project_info($pur_order, $module_type = 0)
    {
        $budget_head = '';
        if ($module_type == 1) {
            $po = $this->purchase_model->get_pur_invoice($pur_order);
            if (!empty($po->group_pur)) {
                $budget_head = $this->purchase_model->find_budget_head_value($po->group_pur);
            }
        } else {
            $po = $this->purchase_model->get_pur_order($pur_order);
            if (!empty($po->group_pur)) {
                $budget_head = $this->purchase_model->find_budget_head_value($po->group_pur);
            }
        }

        $this->load->model('projects_model');

        $base_currency = get_base_currency();

        $currency = $base_currency->id;

        $project_id = '';
        $customer = '';
        if ($po->project != 0) {
            $project = $this->projects_model->get($po->project);

            if ($project) {
                $project_id = $po->project;
                $customer = $project->clientid;
            }
        }

        if ($po->project_id != 0) {
            $project = $this->projects_model->get($po->project_id);

            if ($project) {
                $project_id = $po->project_id;
                $customer = $project->clientid;
            }
        }

        if ($po->currency != 0) {
            $currency = $po->currency;
        }



        echo json_encode([
            'budget_head' => $budget_head,
            'project_id' => $project_id,
            'customer' => $customer,
            'currency' => $currency,
            'vendor' => $po->vendor,
            'category' => $po->group_pur,
            'description_services' => $po->description_services
        ]);
    }
    /**
     * Gets the project information.
     */
    public function get_project_info_wo($wo_order)
    {
        $budget_head = '';
        $wo = $this->purchase_model->get_wo_order($wo_order);
        if (!empty($wo->group_pur)) {
            $budget_head = $this->purchase_model->find_budget_head_value($wo->group_pur);
        }

        $this->load->model('projects_model');

        $base_currency = get_base_currency();

        $currency = $base_currency->id;

        $project_id = '';
        $customer = '';
        if ($wo->project != 0) {
            $project = $this->projects_model->get($wo->project);

            if ($project) {
                $project_id = $wo->project;
                $customer = $project->clientid;
            }
        }

        if ($wo->currency != 0) {
            $currency = $wo->currency;
        }

        echo json_encode([
            'budget_head' => $budget_head,
            'project_id' => $project_id,
            'customer' => $customer,
            'currency' => $currency,
            'vendor' => $wo->vendor
        ]);
    }

    /**
     * Returns orders.
     */
    public function order_returns($id = '')
    {
        $data['title'] = _l('pur_return_orders');

        $data['delivery_id'] = $id;

        $data['from_date'] = _d(date('Y-m-d', strtotime(date('Y-m-d') . "-15 day")));
        $data['to_date'] = _d(date('Y-m-d'));

        $data['vendors'] = $this->purchase_model->get_vendor();

        //display packing list not yet approval
        $data['rel_type'] = 'purchase_return_order';

        $this->load->view('return_orders/manage', $data);
    }

    /**
     * table manage packing list
     * @return [type] 
     */
    public function table_manage_order_return()
    {
        $this->app->get_table_data(module_views_path('purchase', 'return_orders/table_order_return'));
    }

    /**
     * save and send request send mail
     * @return [type] 
     */
    public function save_and_send_request_send_mail($data = '')
    {
        if ((isset($data)) && $data != '') {
            $this->purchase_model->send_mail($data);

            $success = 'success';
            echo json_encode([
                'success' => $success,
            ]);
        }
    }

    /**
     * { order return }
     */
    public function order_return($order_return_type = 'purchasing_return_order', $id = '')
    {
        $this->load->model('clients_model');

        if ($id == '') {
            $data['title'] = _l('add_new_order_return');
        } else {
            $data['title'] = _l('update_order_return');
        }

        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();

            if (!$this->input->post('id')) {

                $mess = $this->purchase_model->add_order_return($data, $data['rel_type']);

                if ($mess) {
                    if ($data['save_and_send_request'] == 'true') {
                        $this->save_and_send_request_send_mail(['rel_id' => $mess, 'rel_type' => 'order_return', 'addedfrom' => get_staff_user_id()]);
                    }
                    set_alert('success', _l('added_successfully'));
                } else {
                    set_alert('warning', _l('pur_add_order_return_failed'));
                }

                redirect(admin_url('purchase/order_returns/' . $mess));
            } else {
                $id = $this->input->post('id');

                $mess = $this->purchase_model->update_order_return($data, $data['rel_type'], $id);

                if ($data['save_and_send_request'] == 'true') {
                    $this->save_and_send_request_send_mail(['rel_id' => $id, 'rel_type' => 'order_return', 'addedfrom' => get_staff_user_id()]);
                }

                if ($mess) {
                    set_alert('success', _l('updated_successfully'));
                } else {
                    set_alert('warning', _l('pur_update_order_return_failed'));
                }
                redirect(admin_url('purchase/order_returns/' . $id));
            }
        }

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['order_return_name_ex'] = 'ORDER_RETURN' . date('YmdHi');
        $data['clients'] = $this->clients_model->get();

        $order_return_row_template = $this->purchase_model->create_order_return_row_template('purchasing_return_order');

        if ($id != '') {

            $order_return = $this->purchase_model->get_order_return($id);
            if (!$order_return) {
                blank_page('Order Return Not Found', 'danger');
            }
            $data['order_return_detail'] = $this->purchase_model->get_order_return_detail($id);
            $data['order_return'] = $order_return;

            if (count($data['order_return_detail']) > 0) {
                $index_receipt = 0;
                foreach ($data['order_return_detail'] as $order_return_detail) {
                    $index_receipt++;
                    $unit_name = pur_get_unit_name($order_return_detail['unit_id']);
                    $taxname = '';
                    $expiry_date = null;
                    $lot_number = null;
                    $commodity_name = $order_return_detail['commodity_name'];

                    if (strlen($commodity_name) == 0) {
                        $commodity_name = pur_get_item_variatiom($order_return_detail['commodity_code']);
                    }

                    $order_return_row_template .= $this->purchase_model->create_order_return_row_template($order_return->rel_type, $order_return_detail['rel_type_detail_id'], 'items[' . $index_receipt . ']', $commodity_name, $order_return_detail['quantity'], $unit_name, $order_return_detail['unit_price'], $taxname, $order_return_detail['commodity_code'], $order_return_detail['unit_id'], $order_return_detail['tax_rate'], $order_return_detail['total_amount'], $order_return_detail['discount'], $order_return_detail['discount_total'], $order_return_detail['total_after_discount'], $order_return_detail['reason_return'], $order_return_detail['sub_total'], $order_return_detail['tax_name'], $order_return_detail['tax_id'], $order_return_detail['id'], true);
                }
            }
        }

        $data['order_return_row_template'] = $order_return_row_template;

        $this->load->view('return_orders/order_return', $data);
    }

    /**
     * order return get item data
     * @param  string $delivery_id 
     * @return [type]              
     */
    public function order_return_get_item_data($rel_id = 0, $rel_type = '', $return_type = 'fully')
    {
        if ($this->input->is_ajax_request()) {
            $data = [];
            if ($rel_type == 'purchasing_return_order') {
                $data = $this->purchase_model->pur_order_detail_order_return($rel_id, $return_type);
            }
            if ($data) {
                $po = $this->purchase_model->get_pur_order($rel_id);
                $base_currency = get_base_currency();
                if ($po->currency != 0) {
                    $base_currency = pur_get_currency_by_id($po->currency);
                }

                $discount_total = 0;
                if ($po) {
                    $discount_total = $po->discount_total;
                }

                $result = [
                    'discount_total' => $discount_total,
                    'discount_type' => $po->discount_type,
                    'company_id' => $data['company_id'] ? $data['company_id'] : '',
                    'email' => $data['email'] ? $data['email'] : '',
                    'phonenumber' => $data['phonenumber'] ? $data['phonenumber'] : '',
                    'order_number' => $data['order_number'] ? $data['order_number'] : '',
                    'order_date' => $data['order_date'] ? $data['order_date'] : '',
                    'number_of_item' => $data['number_of_item'] ? $data['number_of_item'] : '',
                    'order_total' => $data['order_total'] ? $data['order_total'] : '',
                    'datecreated' => $data['datecreated'] ? $data['datecreated'] : '',
                    'additional_discount' => $data['additional_discount'] ? $data['additional_discount'] : '',
                    'main_additional_discount' => $data['main_additional_discount'] ? $data['main_additional_discount'] : '',
                    'result' => $data['result'] ? $data['result'] : '',
                    'currency' => $base_currency->id,
                    'return_type' => $return_type,
                ];
                echo json_encode($result);
                die;
            }
        }
    }

    /**
     * wh client data
     * @param  [type] $customer_id 
     * @return [type]              
     */
    public function pur_client_data($customer_id, $rel_type)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('clients_model');

            $phonenumber = '';
            $email = '';
            $currency = '';
            $fee_return_order = 0;
            $return_policies_information = '';
            if ($rel_type == 'purchasing_return_order') {
                $this->load->model('purchase/purchase_model');
                $vendor = $this->purchase_model->get_vendor($customer_id);
                if ($vendor) {
                    $phonenumber = $vendor->phonenumber;
                    $contacts = $this->purchase_model->get_contacts($customer_id);
                    if (count($contacts) > 0) {
                        $email = $contacts[0]['email'];
                    }
                    $currency = $vendor->default_currency;
                    $fee_return_order = $vendor->return_order_fee;
                    $return_policies_information = $vendor->return_policies;
                }
            }

            echo json_encode([
                'fee_return_order' => $fee_return_order,
                'phonenumber' => $phonenumber,
                'email' => $email,
                'currency' => $currency,
                'return_policies_information' => $return_policies_information,
            ]);
        }
    }

    /**
     * view order return
     * @param  [type] $id 
     * @return [type]     
     */
    public function view_order_return($id)
    {
        //approval
        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if ((isset($send_mail_approve)) && $send_mail_approve != '') {
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        $this->load->model('clients_model');

        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'order_return');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'order_return');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'order_return');


        //get vaule render dropdown select

        $data['units_code_name'] = $this->purchase_model->get_units_code_name();

        $data['order_return_detail'] = $this->purchase_model->get_order_return_detail($id);
        $data['order_return'] = $this->purchase_model->get_order_return($id);
        $data['order_return_refunds'] = $this->purchase_model->get_order_return_refunds($id);

        $data['activity_log'] = $this->purchase_model->pur_get_activity_log($id, 'order_return');

        $data['title'] = _l('pur_order_return');
        $check_appr = $this->purchase_model->get_approve_setting('order_return');
        $data['check_appr'] = $check_appr;
        $data['tax_data'] = $this->purchase_model->get_html_tax_order_return($id);
        $this->load->model('currencies_model');

        $base_currency = $this->currencies_model->get_base_currency();
        if ($data['order_return']->currency != 0) {
            $base_currency = pur_get_currency_by_id($data['order_return']->currency);
        }

        $data['base_currency'] = $base_currency;

        $this->load->view('return_orders/view_order_return', $data);
    }

    /**
     * order return check before approval
     * @return [type] 
     */
    public function order_return_check_before_approval()
    {
        $data = $this->input->post();
        // packing list
        //check before send request approval
        if ($data['order_rel_type'] == 'manual') {
            echo json_encode([
                'success' => true,
                'message' => '',
            ]);
            die;
        }
    }

    /**
     * add activity
     */
    public function pur_add_activity()
    {
        $goods_delivery_id = $this->input->post('goods_delivery_id');


        if ($this->input->post()) {
            $description = $this->input->post('activity');
            $rel_type = $this->input->post('rel_type');
            $aId     = $this->purchase_model->log_pur_activity($goods_delivery_id, $rel_type, $description);

            if ($aId) {
                $status = true;
                $message = _l('added_successfully');
            } else {
                $status = false;
                $message = _l('added_failed');
            }

            echo json_encode([
                'status' => $status,
                'message' => $message,
            ]);
        }
    }

    /**
     * delete activitylog
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_activitylog($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $delete = $this->purchase_model->delete_activitylog($id);
        if ($delete) {
            $status = true;
        } else {
            $status = false;
        }

        echo json_encode([
            'success' => $status,
        ]);
    }

    /**
     * delete order return
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_order_return($id)
    {

        if (!has_permission('purchase_order_return', '', 'delete')  &&  !is_admin()) {
            access_denied('purchase]');
        }

        $response = $this->purchase_model->delete_order_return($id);
        if ($response == true) {
            set_alert('success', _l('deleted'));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }
        redirect(admin_url('purchase/order_returns'));
    }

    /**
     * order return pdf
     * @param  [type] $id 
     * @return [type]     
     */
    public function order_return_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/order_returns'));
        }
        $this->load->model('clients_model');
        $this->load->model('currencies_model');

        $order_return_number = '';
        $order_return = $this->purchase_model->get_order_return($id);
        $order_return->client = $this->clients_model->get($order_return->company_id);
        $order_return->order_return_detail = $this->purchase_model->get_order_return_detail($id);

        $order_return->base_currency = $this->currencies_model->get_base_currency();
        if ($order_return->currency != 0) {
            $order_return->base_currency = pur_get_currency_by_id($order_return->currency);
        }

        $order_return->tax_data = $this->purchase_model->get_html_tax_order_return($id);
        $order_return->clientid = $order_return->company_id;


        if ($order_return) {
            $order_return_number .= $order_return->order_return_number . ' - ' . $order_return->order_return_name;
        }
        try {
            $pdf = $this->purchase_model->order_return_pdf($order_return);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';
        ob_end_clean();

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(mb_strtoupper(slug_it($order_return_number)) . '.pdf', $type);
    }

    /**
     * open warehouse modal
     * @return [type] 
     */
    public function open_warehouse_modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $id = $this->input->post('order_return_id');

        $data = [];
        $data['title'] = _l('select_warehouse_to_create_inventory_delivery');
        $data['id'] = $id;
        $data['warehouses'] = [];
        if (get_status_modules_pur('warehouse')) {
            $this->load->model('warehouse/warehouse_model');
            $data['warehouses'] = $this->warehouse_model->get_warehouse();
        }

        $this->load->view('return_orders/select_warehouse_modal', $data);
    }
    /**
     * Gets the vendor shared.
     *
     * @param        $pr_id  The pr identifier
     */
    public function get_vendor_shared($pr_id)
    {
        $purchase_request = $this->purchase_model->get_purchase_request($pr_id);
        $vendor_str = $purchase_request->send_to_vendors;
        $vendor_arr = [];

        if ($vendor_str != '') {
            $vendor_arr = explode(',', $vendor_str);
        }

        echo json_encode([
            'shared_vendor' => $vendor_str,
        ]);
    }

    public function share_request()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->purchase_model->share_request_to_vendor($data);

            if ($success) {
                set_alert('success', _l('share_request_successfully'));
            }

            redirect(admin_url('purchase/purchase_request'));
        }
    }

    /**
     * { preview purchase order file }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     * @return  view
     */
    public function file_purrequest($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('purchase_request/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purrequest_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode(['success' => $this->purchase_model->delete_purrequest_attachment($id)]);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }


    /**
     * { change delivery status }
     *
     * @param      integer  $status     The status
     * @param         $pur_order  The pur order
     * @return     json
     */
    public function change_pr_approve_status($status, $pur_request)
    {
        $success = $this->purchase_model->change_pr_approve_status($status, $pur_request);
        $message = '';
        $html = '';
        $status_str = '';
        $class = '';
        if ($success == true) {
            $message = _l('change_approval_status_successfully');
        } else {
            $message = _l('change_approval_status_fail');
        }

        if (has_permission('purchase_orders', '', 'edit') || is_admin()) {
            $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $pur_request . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $html .= '</a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $pur_request . '">';

            if ($status == 1) {
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 2 ,' . $pur_request . '); return false;">
                             ' . _l('purchase_approved') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 3 ,' . $pur_request . '); return false;">
                             ' . _l('purchase_reject') . '
                          </a>
                       </li>';

                $status_str = _l('purchase_draft');
                $class = 'label-primary';
            } else if ($status == 2) {
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 1 ,' . $pur_request . '); return false;">
                             ' . _l('purchase_not_yet_approve') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 3 ,' . $pur_request . '); return false;">
                             ' . _l('purchase_reject') . '
                          </a>
                       </li>';
                $status_str = _l('purchase_approved');
                $class = 'label-success';
            } else if ($status == 3) {
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 1 ,' . $pur_request . '); return false;">
                             ' . _l('purchase_draft') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 2 ,' . $pur_request . '); return false;">
                             ' . _l('purchase_approved') . '
                          </a>
                       </li>';

                $status_str = _l('purchase_reject');
                $class = 'label-warning';
            }


            $html .= '</ul>';
            $html .= '</div>';
        }

        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }

    /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_project_pur_contract($project_id)
    {
        $this->app->get_table_data(module_views_path('purchase', 'contracts/table_contracts'), ['project' => $project_id]);
    }

    /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_project_pur_request($project_id)
    {
        $this->app->get_table_data(module_views_path('purchase', 'purchase_request/table_pur_request'), ['project' => $project_id]);
    }


    /**
     * Gets the purchase order row template.
     */
    public function get_purchase_invoice_row_template()
    {
        $name = $this->input->post('name');
        $item_name = $this->input->post('item_name');
        $item_description = $this->input->post('item_description');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $item_code = $this->input->post('item_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $item_key = $this->input->post('item_key');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');

        echo $this->purchase_model->create_purchase_invoice_row_template($name, $item_name, $item_description, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency);
    }

    /**
     * { mark return order as }
     *
     * @param        $status  The status
     * @param        $id      The identifier
     */
    public function mark_return_order_as($status, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'wh_order_returns', ['status' => $status]);
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_pur_return_order_status_changed', ['id' => $id, 'status' => $status]);

            set_alert('success', _l('change_order_return_status_successfully'));
        }

        redirect(admin_url('purchase/order_returns/' . $id));
    }

    /**
     * { refund }
     *
     * @param        $id         The identifier
     * @param        $refund_id  The refund identifier
     */
    public function refund_order_return($id, $refund_id = null)
    {
        if (has_permission('purchase_order_return', '', 'edit')) {
            $this->load->model('payment_modes_model');
            if (!$refund_id) {
                $data['payment_modes'] = $this->payment_modes_model->get('', [
                    'expenses_only !=' => 1,
                ]);
            } else {
                $data['refund']        = $this->purchase_model->get_order_return_refund($refund_id);
                $data['payment_modes'] = $this->payment_modes_model->get('', [], true, true);
                $i                     = 0;
                foreach ($data['payment_modes'] as $mode) {
                    if ($mode['active'] == 0 && $data['refund']->payment_mode != $mode['id']) {
                        unset($data['payment_modes'][$i]);
                    }
                    $i++;
                }
            }

            $data['order_return'] = $this->purchase_model->get_order_return($id);
            $this->load->view('return_orders/refund', $data);
        }
    }

    /**
     * Creates a refund.
     *
     * @param        $order_return_id  The debit note identifier
     */
    public function create_order_return_refund($order_return_id)
    {
        if (has_permission('purchase_order_return', '', 'edit')) {
            $data                = $this->input->post();
            $data['refunded_on'] = to_sql_date($data['refunded_on']);
            $data['staff_id']    = get_staff_user_id();
            $success             = $this->purchase_model->create_order_return_refund($order_return_id, $data);

            if ($success) {
                set_alert('success', _l('added_successfully', _l('refund')));
            }
        }

        redirect(admin_url('purchase/order_returns/' . $order_return_id));
    }

    /**
     * { edit refund }
     *
     * @param        $refund_id      The refund identifier
     * @param        $order_return_id  The debit note identifier
     */
    public function edit_order_return_refund($refund_id, $order_return_id)
    {
        if (has_permission('purchase_order_return', '', 'edit')) {
            $data                = $this->input->post();
            $data['refunded_on'] = to_sql_date($data['refunded_on']);
            $success             = $this->purchase_model->edit_order_return_refund($refund_id, $data);

            if ($success) {
                set_alert('success', _l('updated_successfully', _l('refund')));
            }
        }

        redirect(admin_url('purchase/order_returns/' . $order_return_id));
    }


    /**
     * { delete refund }
     *
     * @param        $refund_id       The refund identifier
     * @param        $credit_note_id  The credit note identifier
     */
    public function delete_order_return_refund($refund_id, $order_return_id)
    {
        if (has_permission('purchase_debit_notes', '', 'delete')) {
            $success = $this->purchase_model->delete_order_return_refund($refund_id, $order_return_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('refund')));
            }
        }
        redirect(admin_url('purchase/order_returns/' . $order_return_id));
    }

    /**
     * { update delivery status }
     *
     * @param      <type>  $pur_order  The pur order
     * @param      <type>  $status     The status
     */
    public function mark_pur_order_as($status, $pur_order)
    {

        $this->db->where('id', $pur_order);
        $this->db->update(db_prefix() . 'pur_orders', ['order_status' => $status]);
        if ($this->db->affected_rows() > 0) {
            if ($status == 'delivered') {
                $this->db->where('id', $pur_order);
                $this->db->update(db_prefix() . 'pur_orders', ['delivery_status' => 1, 'delivery_date' => date('Y-m-d')]);
            } else {
                $this->db->where('id', $pur_order);
                $this->db->update(db_prefix() . 'pur_orders', ['delivery_status' => 0]);
            }

            set_alert('success', _l('updated_successfully', _l('order_status')));
        }

        redirect(admin_url('purchase/purchase_order/' . $pur_order));
    }

    /**
     * { table vendor debit notes }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_quoations($vendor)
    {
        $this->app->get_table_data(module_views_path('purchase', 'quotations/table_estimates'), ['vendor' => $vendor]);
    }

    /**
     * Gets the currency rate.
     *
     * @param        $currency_id  The currency identifier
     */
    public function get_currency_rate($currency_id)
    {
        $base_currency = get_base_currency();

        $pr_currency = pur_get_currency_by_id($currency_id);

        $currency_rate = 1;
        $convert_str = ' (' . $base_currency->name . ' => ' . $base_currency->name . ')';
        $currency_name = '(' . $base_currency->name . ')';
        if ($base_currency->id != $pr_currency->id) {
            $currency_rate = pur_get_currency_rate($base_currency->name, $pr_currency->name);
            $convert_str = ' (' . $base_currency->name . ' => ' . $pr_currency->name . ')';
            $currency_name = '(' . $pr_currency->name . ')';
        }

        echo json_encode([
            'currency_rate' => $currency_rate,
            'convert_str' => $convert_str,
            'currency_name' => $currency_name,
        ]);
    }

    /**
     * { vendor contract change }
     *
     * @param        $vendor  The vendor
     */
    public function vendor_contract_change($vendor)
    {
        $pur_orders = $this->purchase_model->get_pur_order_approved_by_vendor($vendor);

        $html = '<option value=""></option>';
        foreach ($pur_orders as $po) {
            $html .= '<option value="' . $po['id'] . '">' . $po['pur_order_number'] . '</option>';
        }

        echo json_encode(['html' => $html]);
    }



    /* Change client status / active / inactive */
    public function change_vendor_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {

            $this->db->where('userid', $id);
            $this->db->update(db_prefix() . 'pur_vendor', [
                'active' => $status,
            ]);
        }
    }

    /**
     * { confirm registration }
     *
     * @param        $vendor_id  The client identifier
     */
    public function confirm_registration($vendor_id)
    {
        if (!is_admin() && !has_permission('purchase_settings', '', 'edit')) {
            access_denied('Vendor Confirm Registration, ID: ' . $vendor_id);
        }
        $success = $this->purchase_model->confirm_registration($vendor_id);
        if ($success) {
            set_alert('success', _l('vendor_registration_successfully_confirmed'));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function allow_vendors_to_register()
    {
        $data = $this->input->post();
        if ($data != 'null') {
            $value = $this->purchase_model->update_pc_options_setting($data);
            if ($value) {
                $success = true;
                $message = _l('updated_successfully');
            } else {
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * Sends a purchase order.
     * @return redirect
     */
    public function send_contract()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_contract($data);
            if ($send) {
                set_alert('success', _l('send_contract_successfully'));
            } else {
                set_alert('warning', _l('send_contract_fail'));
            }
            redirect(admin_url('purchase/contract/' . $data['contract_id']));
        }
    }

    /**
     * Get the template modal content
     *
     * @return string
     */
    public function modal_template()
    {
        $this->load->model('templates_model');
        $data['rel_type'] = $this->input->post('rel_type');

        // When modal is submitted, it returns to the proposal/contract that was being edited.
        $data['rel_id'] = $this->input->post('rel_id');

        if ($this->input->post('slug') == 'new') {
            $data['title'] = _l('add_template');
        } elseif ($this->input->post('slug') == 'edit') {
            $data['title'] = _l('edit_template');
            $data['id']    = $this->input->post('id');
            $this->authorize($data['id']);
            $data['template'] = $this->templates_model->get($data['id']);
        }

        $this->load->view('templates/template', $data);
    }

    /**
     * Manage template
     *
     * @param  int|null $id
     *
     */
    public function template($id = null)
    {
        $this->load->model('templates_model');
        $content = $this->input->post('content', false);
        $content = html_purify($content);

        $data['name']      = $this->input->post('name');
        $data['content']   = $content;
        $data['addedfrom'] = get_staff_user_id();
        $data['type']      = $this->input->post('rel_type');

        // so when modal is submitted, it returns to the proposal/contract that was being edited.
        $rel_id = $this->input->post('rel_id');

        if (is_numeric($id)) {
            $this->authorize($id);
            $success = $this->templates_model->update($id, $data);
            $message = _l('template_updated');
        } else {
            $success = $this->templates_model->create($data);
            $message = _l('template_added');
        }

        if ($success) {
            set_alert('success', $message);
        }

        redirect(admin_url('purchase/contract/' . $rel_id));
    }

    /**
     * { request quotation pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function compare_quotation_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }

        $pur_request = $this->purchase_model->get_compare_quotation_pdf_html($id);

        try {
            $pdf = $this->purchase_model->compare_quotation_pdf($pur_request);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('compare_quotation.pdf', $type);
    }

    /**
     * { purchase_invoice_pdf }
     */
    public function purchase_invoice_pdf($invoice_id)
    {
        if (!$invoice_id) {
            redirect(admin_url('purchase/invoices'));
        }

        $purchase_invoice = $this->purchase_model->get_purchase_invoice_pdf_html($invoice_id);

        try {
            $pdf = $this->purchase_model->purchase_invoice_pdf($purchase_invoice);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('purchase_invoice.pdf', $type);
    }

    public function find_project_members()
    {
        $response = array();
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!empty($data['project_id'])) {
                $response = $this->staff_model->find_project_members($data['project_id']);
            }
        }
        echo json_encode($response);
    }

    public function find_approval_setting()
    {
        $response['success'] = false;
        if ($this->input->post()) {
            $response = $this->purchase_model->find_approval_setting($this->input->post());
        }
        echo json_encode($response);
    }

    public function delete_attachment($id)
    {
        $this->purchase_model->delete_purchase_attachment($id);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function cron_emails()
    {
        $cron_emails = $this->purchase_model->check_cron_emails();
        if (!empty($cron_emails)) {
            foreach ($cron_emails as $key => $value) {
                if ($value['type'] == "purchase" && !empty($value['options'])) {
                    $options = json_decode($value['options'], true);
                    $rel_name = $options['rel_name'];
                    $insert_id = $options['insert_id'];
                    $user_id = $options['user_id'];
                    $status = $options['status'];
                    if (isset($options['approver'])) {
                        $rel_type = $options['rel_type'];
                        $project = $options['project'];
                        $requester = $options['requester'];
                        $vendors = isset($options['vendors']) ? $options['vendors'] : '';

                        $this->purchase_model->send_mail_to_approver($rel_type, $rel_name, $insert_id, $user_id, $status, $project, $requester, $vendors);
                    }
                    if (isset($options['sender'])) {
                        if ($status == 2 || $status == 3) {
                            $this->purchase_model->send_mail_to_sender($rel_name, $status, $insert_id, $user_id);
                        }
                    }
                    $this->purchase_model->delete_cron_email_option($value['id']);
                }
            }
        }
        return true;
    }
    public function work_order($id = '')
    {
        // if (!has_permission('work_orders', '', 'view') && !is_admin() && !has_permission('work_orders', '', 'view_own')) {
        //     access_denied('work_order');
        // }
        $this->load->model('expenses_model');
        $this->load->model('payment_modes_model');
        $this->load->model('taxes_model');
        $this->load->model('currencies_model');
        $this->load->model('departments_model');
        $this->load->model('projects_model');
        $this->load->model('clients_model');

        $data['work_orderid']  = $id;
        $data['title'] = _l('work_order');
        $data['departments'] = $this->departments_model->get();
        $data['projects'] = $this->projects_model->get();
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);
        $data['currencies']         = $this->currencies_model->get();
        $data['taxes']              = $this->taxes_model->get();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['expense_categories'] = $this->expenses_model->get_category();
        $data['item_tags'] = $this->purchase_model->get_item_tag_filter();
        $data['customers'] = $this->clients_model->get();
        $data['pur_request'] = $this->purchase_model->get_pur_request_by_status(2);

        $data['projects'] = $this->projects_model->get();
        $data['item_group'] = $this->purchase_model->get_commodity_group_add_commodity();
        $this->load->view('work_order/manage', $data);
    }
    public function table_wo_order()
    {
        $this->app->get_table_data(module_views_path('purchase', 'work_order/table_wo_order'));
    }
    public function change_status_wo_order($status, $id)
    {
        $change = $this->purchase_model->change_status_wo_order($status, $id);
        if ($change == true) {

            $message = _l('change_status_wo_order') . ' ' . _l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        } else {
            $message = _l('change_status_wo_order') . ' ' . _l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }
    public function get_wo_order_data_ajax($id, $to_return = false)
    {
        if (!has_permission('work_orders', '', 'view') && !has_permission('work_orders', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No purchase order found');
        }

        $estimate = $this->purchase_model->get_wo_order($id);

        if (has_permission('work_orders', '', 'view_own') && !is_admin()) {
            $staffid = get_staff_user_id();

            $approve_access = total_rows(db_prefix() . 'pur_approval_details', ['staffid' => $staffid, 'rel_type' => 'pur_order', 'rel_id' => $id]);

            if ($estimate->buyer != $staffid && $estimate->addedfrom != $staffid && !is_vendor_admin($estimate->vendor) && $approve_access == 0) {
                echo _l('access_denied');
                die;
            }
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $data['payment'] = $this->purchase_model->get_inv_payment_purchase_order($id);
        // $data['pur_order_attachments'] = $this->purchase_model->get_purchase_order_attachments($id);
        $data['estimate_detail'] = $this->purchase_model->get_wo_order_detail($id);
        if (!empty($data['estimate_detail'])) {
            $data['estimate_detail'] = $this->purchase_model->get_changee_wo_order_detail($data['estimate_detail'], $id);
        }
        $data['estimate']          = $estimate;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['vendor_contacts'] = $this->purchase_model->get_contacts($estimate->vendor);
        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if ((isset($send_mail_approve)) && $send_mail_approve != '') {
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        $data['invoices'] = $this->purchase_model->get_invoices_by_po($id);
        $data['check_appr'] = $this->purchase_model->get_approve_setting('wo_order');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'wo_order');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'wo_order');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'wo_order');
        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_order($id);
        $data['check_approval_setting'] = $this->purchase_model->check_approval_setting($estimate->project, 'wo_order', 0);
        $data['attachments'] = $this->purchase_model->get_work_order_attachments('wo_order', $id);
        $data['pur_order'] = $this->purchase_model->get_wo_order($id);
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups'] = $this->purchase_model->get_sub_group();
        $data['area'] = $this->purchase_model->get_area();
        $data['activity'] = $this->purchase_model->get_wo_activity($id);
        $data['changes'] = $this->purchase_model->get_change_wo_order($id);
        $data['payment_certificate'] = $this->purchase_model->get_all_wo_payment_certificate($id);
        if ($to_return == false) {
            $this->load->view('work_order/wo_order_preview', $data);
        } else {
            return $this->load->view('work_order/wo_order_preview', $data, true);
        }
    }
    public function wo_order($id = '')
    {
        if ($this->input->post()) {
            $wo_order_data = $this->input->post();

            $wo_order_data['terms'] = $this->input->post('terms', false);
            $wo_order_data['vendornote'] = $this->input->post('vendornote', false);
            $wo_order_data['order_summary'] = $this->input->post('order_summary', false);

            if ($id == '') {
                if (!has_permission('work_orders', '', 'create')) {
                    access_denied('work_order');
                }
                $id = $this->purchase_model->add_wo_order($wo_order_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('wo_order')));

                    redirect(admin_url('purchase/work_order/' . $id));
                }
            } else {
                if (!has_permission('work_orders', '', 'edit')) {
                    access_denied('work_order');
                }
                $success = $this->purchase_model->update_wo_order($wo_order_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('wo_order')));
                }
                redirect(admin_url('purchase/work_order/' . $id));
            }
        }

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $wo_order_row_template = $this->purchase_model->create_wo_order_row_template();

        if ($id == '') {
            $title = _l('create_new_wo_order');
            $is_edit = false;
        } else {

            $data['wo_order_detail'] = $this->purchase_model->get_wo_order_detail($id);

            $data['wo_order'] = $this->purchase_model->get_wo_order($id);
            $data['attachments'] = $this->purchase_model->get_work_order_attachments('wo_order', $id);

            $currency_rate = 1;
            if ($data['wo_order']->currency != 0 && $data['wo_order']->currency_rate != null) {
                $currency_rate = $data['wo_order']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if ($data['wo_order']->currency != 0 && $data['wo_order']->to_currency != null) {
                $to_currency = $data['wo_order']->to_currency;
            }


            $data['tax_data'] = $this->purchase_model->get_html_tax_pur_order($id);
            $title = _l('wo_order_detail');

            if (count($data['wo_order_detail']) > 0) {
                $index_order = 0;
                foreach ($data['wo_order_detail'] as $order_detail) {
                    $index_order++;
                    $unit_name = $order_detail['unit_id'];
                    $taxname = $order_detail['tax_name'];
                    $item_name = $order_detail['item_name'];

                    if (strlen($item_name) == 0) {
                        $item_name = pur_get_item_variatiom($order_detail['item_code']);
                    }

                    $wo_order_row_template .= $this->purchase_model->create_wo_order_row_template('items[' . $index_order . ']',  $item_name, $order_detail['description'], $order_detail['area'], $order_detail['image'], $order_detail['quantity'], $unit_name, $order_detail['unit_price'], $taxname, $order_detail['item_code'], $order_detail['unit_id'], $order_detail['tax_rate'],  $order_detail['total_money'], $order_detail['discount_%'], $order_detail['discount_money'], $order_detail['total'], $order_detail['into_money'], $order_detail['tax'], $order_detail['tax_value'], $order_detail['id'], true, $currency_rate, $to_currency, $order_detail, false, $order_detail['sub_groups_pur'], $order_detail['serial_no']);
                }
            }
            $is_edit = true;
        }
        $data['is_edit'] = $is_edit;
        $data['wo_order_row_template'] = $wo_order_row_template;
        $data['currencies'] = $this->currencies_model->get();
        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        $data['invoices'] = $this->purchase_model->get_invoice_for_pr();
        $data['pur_request'] = $this->purchase_model->get_pur_request_by_status(2);
        $data['projects'] = $this->projects_model->get_items();
        $data['ven'] = $this->input->get('vendor');
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['estimates'] = $this->purchase_model->get_estimates_by_status(2);
        $data['units'] = $this->purchase_model->get_units();
        $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups_pur'] = $this->purchase_model->get_sub_group();
        $data['area_pur'] = $this->purchase_model->get_area();
        $this->load->model('invoices_model');
        $data['get_hsn_sac_code'] = $this->invoices_model->get_hsn_sac_code();
        $data['ajaxItems'] = false;

        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $data['convert_po'] = false;
        $pr = $this->input->get('pr', TRUE);
        if (!empty($pr)) {
            $purchase_request = $this->purchase_model->get_purchase_request($pr);
            if (!empty($purchase_request)) {
                $data['convert_po'] = true;
                $data['selected_pr'] = $purchase_request->id;
                $data['selected_project'] = $purchase_request->project;
                $data['selected_head'] = $purchase_request->group_pur;
                $data['selected_sub_head'] = $purchase_request->sub_groups_pur;
                $data['selected_area'] = $purchase_request->area_pur;
            }
        }

        $data['title'] = $title;

        $this->load->view('work_order/wo_order', $data);
    }
    public function delete_wo_order($id)
    {
        if (!has_permission('work_orders', '', 'delete')) {
            access_denied('work_order');
        }
        if (!$id) {
            redirect(admin_url('purchase/work_order'));
        }
        $success = $this->purchase_model->delete_wo_order($id);
        if (is_array($success)) {
            set_alert('warning', _l('work_order'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('work_order')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('work_order')));
        }
        redirect(admin_url('purchase/work_order'));
    }
    public function woorder_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }

        $wo_order = $this->purchase_model->get_woorder_pdf_html($id);

        try {
            $pdf = $this->purchase_model->woorder_pdf($wo_order, $id);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }
        $wo_order = $this->purchase_model->get_wo_order($id);
        $vendor_name = get_vendor_name_by_id($wo_order->vendor);
        $pdf_name = $wo_order->wo_order_number . '-' . $vendor_name . '-' . $wo_order->wo_order_name . '.pdf';
        $pdf->Output($pdf_name, $type);
    }
    public function mark_wo_order_as($status, $wo_order)
    {

        $this->db->where('id', $wo_order);
        $this->db->update(db_prefix() . 'wo_orders', ['order_status' => $status]);
        if ($this->db->affected_rows() > 0) {
            if ($status == 'delivered') {
                $this->db->where('id', $wo_order);
                $this->db->update(db_prefix() . 'wo_orders', ['delivery_status' => 1, 'delivery_date' => date('Y-m-d')]);
            } else {
                $this->db->where('id', $wo_order);
                $this->db->update(db_prefix() . 'wo_orders', ['delivery_status' => 0]);
            }

            set_alert('success', _l('updated_successfully', _l('order_status')));
        }

        redirect(admin_url('purchase/work_order/' . $wo_order));
    }
    public function delete_work_order_attachment($id)
    {
        $this->purchase_model->delete_wo_order_attachment($id);
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function send_wo()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_po($data);
            if ($send) {
                set_alert('success', _l('send_wo_successfully'));
            } else {
                set_alert('warning', _l('send_wo_fail'));
            }
            redirect(admin_url('purchase/work_order/' . $data['wo_id']));
        }
    }
    public function wo_commodity_code_search($type = 'purchase_price', $can_be = 'can_be_purchased', $vendor = '')
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->purchase_model->wo_commodity_code_search($this->input->post('q'), $type, $can_be, false, $vendor));
        }
    }
    /**
     * import file xlsx pur order items
     * @return json 
     */
    public function import_file_xlsx_pur_order_items()
    {

        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if ($this->input->post()) {

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                //do_action('before_import_leads');

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $import_result = true;
                        $rows = [];

                        //Writer file
                        $writer_header = array(
                            "(*)" . _l('item_description') => 'string',
                            _l('quantity') => 'string',
                            _l('unit_price')    => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();

                        $col_style1 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
                        $style1 = ['widths' => $widths_arr, 'fill' => '#ff9800',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

                        $writer->writeSheetHeader_v2('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr, 'fill' => '#f44336',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13], $col_style1, $style1);

                        //init file error end

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        // start row write 2
                        $numRow = 2;
                        $total_rows = 0;

                        $total_rows_actualy = 0;
                        $list_item = $this->purchase_model->create_purchase_order_row_template('', '', '', '', '', '', '', '', '', '', '', '',  '', '', '', '', '', '', '', '', '', '', '', [], true);
                        //get data for compare
                        $index_quote = 0;

                        for ($row = 1; $row < count($data); $row++) {
                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;
                            $flag_mail = 0;
                            $string_error = '';
                            $flag_contract_form = 0;

                            $flag_id_commodity_code;
                            $flag_id_item_description;

                            // $value_cell_commodity_code = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_item_description = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_quantity = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_cell_unit_price = isset($data[$row][2]) ? $data[$row][2] : '';

                            /*check null*/
                            if (is_null($value_cell_item_description) == true) {
                                $string_error .= _l('item_description') . _l('not_yet_entered');
                                $flag = 1;
                            }

                            if (!is_numeric(trim($value_cell_quantity, " "))) {

                                $string_error .= _l('quantity') . _l('_not_a_number');
                                $flag2 = 1;
                            }
                            if (!is_numeric(trim($value_cell_unit_price, " "))) {

                                $string_error .= _l('unit_price') . _l('_not_a_number');
                                $flag2 = 1;
                            }
                            if (($flag == 1) || ($flag2 == 1)) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_cell_item_description,
                                    $value_cell_quantity,
                                    $value_cell_unit_price,
                                    $string_error,
                                ]);

                                $numRow++;
                                $total_rows_data_error++;
                                $message = 'Import Error In Some Item';
                            }
                            if (($flag == 0) && ($flag2 == 0)) {

                                $rows[] = $row;
                                $list_item .= $this->purchase_model->create_purchase_order_row_template('newitems[' . $index_quote . ']', '', $value_cell_item_description, '', '', $value_cell_quantity, '', $value_cell_unit_price, '', $item_value->id, '', '',  '', '', '', '', '', '', '', $index_quote, '', 1, '', [], true, '', '');

                                $index_quote++;
                                $total_rows_data++;
                                $message = 'Import Item successfully';
                            }
                        }
                        // die('sadad');
                        $total_rows = $total_rows;
                        $data['total_rows_post'] = count($rows);
                        $total_row_success = count($rows);
                        // $total_row_false = '';
                        if (($total_rows_data_error > 0)) {

                            $filename = 'FILE_ERROR_IMPORT_ITEMS_PURCHASE_ORDER' . get_staff_user_id() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_ORDER_IMPORT_ITEMS_ERROR . $filename, $filename));

                            $filename = PURCHASE_ORDER_IMPORT_ITEMS_ERROR . $filename;
                        }
                        $list_item = $list_item;

                        @delete_dir($tmpDir);
                    }
                } else {
                    set_alert('warning', 'Import Item failed');
                }
            }
        }

        echo json_encode([
            'message' => $message,
            'total_row_success' => $total_row_success,
            'total_row_false' => $total_rows_data_error,
            'total_rows' => $total_rows_data,
            'site_url' => site_url(),
            'staff_id' => get_staff_user_id(),
            'total_rows_data_error' => $total_rows_data_error,
            'filename' => $filename,
            'list_item' => $list_item
        ]);
    }
    public function import_file_xlsx_pur_request_items()
    {


        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if ($this->input->post()) {

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                //do_action('before_import_leads');

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $import_result = true;
                        $rows = [];

                        //Writer file
                        $writer_header = array(
                            "(*)" . _l('item_description') => 'string',
                            _l('quantity') => 'string',
                            _l('unit_price')    => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();

                        $col_style1 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
                        $style1 = ['widths' => $widths_arr, 'fill' => '#ff9800',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

                        $writer->writeSheetHeader_v2('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr, 'fill' => '#f44336',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13], $col_style1, $style1);

                        //init file error end

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        // start row write 2
                        $numRow = 2;
                        $total_rows = 0;

                        $total_rows_actualy = 0;
                        $list_item = $this->purchase_model->create_purchase_request_row_template();
                        //get data for compare
                        $index_quote = 0;

                        for ($row = 1; $row < count($data); $row++) {
                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;
                            $flag_mail = 0;
                            $string_error = '';
                            $flag_contract_form = 0;

                            $flag_id_commodity_code;
                            $flag_id_item_description;

                            // $value_cell_commodity_code = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_item_description = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_quantity = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_cell_unit_price = isset($data[$row][2]) ? $data[$row][2] : '';

                            /*check null*/
                            if (is_null($value_cell_item_description) == true) {
                                $string_error .= _l('item_description') . _l('not_yet_entered');
                                $flag = 1;
                            }

                            if (!is_numeric(trim($value_cell_quantity, " "))) {

                                $string_error .= _l('quantity') . _l('_not_a_number');
                                $flag2 = 1;
                            }
                            if (!is_numeric(trim($value_cell_unit_price, " "))) {

                                $string_error .= _l('unit_price') . _l('_not_a_number');
                                $flag2 = 1;
                            }
                            if (($flag == 1) || ($flag2 == 1)) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_cell_item_description,
                                    $value_cell_quantity,
                                    $value_cell_unit_price,
                                    $string_error,
                                ]);

                                $numRow++;
                                $total_rows_data_error++;
                                $message = 'Import Error In Some Item';
                            }


                            if (($flag == 0) && ($flag2 == 0)) {

                                $rows[] = $row;
                                $list_item .= $this->purchase_model->create_purchase_request_row_template('newitems[' . $index_quote . ']', '', '', $value_cell_item_description, '', '', $value_cell_unit_price, $value_cell_quantity, '', '', '', $index_quote, '', '', '', '', '', true, 1, '', []);


                                $index_quote++;
                                $total_rows_data++;
                                $message = 'Import Item successfully';
                            }
                        }

                        $total_rows = $total_rows;
                        $data['total_rows_post'] = count($rows);
                        $total_row_success = count($rows);
                        // $total_row_false = $total_rows - (int) count($rows);

                        if (($total_rows_data_error > 0)) {

                            $filename = 'FILE_ERROR_IMPORT_ITEMS_PURCHASE_REQUEST' . get_staff_user_id() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_ORDER_IMPORT_ITEMS_ERROR . $filename, $filename));

                            $filename = PURCHASE_ORDER_IMPORT_ITEMS_ERROR . $filename;
                        }

                        @delete_dir($tmpDir);
                    }
                } else {
                    set_alert('warning', 'Import Item failed');
                }
            }
        }

        echo json_encode([
            'message' => $message,
            'total_row_success' => $total_row_success,
            'total_row_false' => $total_rows_data_error,
            'total_rows' => $total_rows_data,
            'site_url' => site_url(),
            'staff_id' => get_staff_user_id(),
            'total_rows_data_error' => $total_rows_data_error,
            'filename' => $filename,
            'list_item' => $list_item
        ]);
    }

    public function import_file_xlsx_wo_order_items()
    {


        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if ($this->input->post()) {

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                //do_action('before_import_leads');

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $import_result = true;
                        $rows = [];

                        //Writer file
                        $writer_header = array(
                            "(*)" . _l('item_description') => 'string',
                            _l('quantity') => 'string',
                            _l('unit_price')    => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();

                        $col_style1 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
                        $style1 = ['widths' => $widths_arr, 'fill' => '#ff9800',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

                        $writer->writeSheetHeader_v2('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr, 'fill' => '#f44336',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13], $col_style1, $style1);

                        //init file error end

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        // start row write 2
                        $numRow = 2;
                        $total_rows = 0;

                        $total_rows_actualy = 0;
                        // $list_item = $this->purchase_model->create_wo_order_row_template();
                        $list_item = $this->purchase_model->create_wo_order_row_template('', '', '', '', '', '', '', '', '', '', '', '',  '', '', '', '', '', '', '', '', '', '', '', [], true);
                        //get data for compare
                        $index_quote = 0;
                        for ($row = 1; $row < count($data); $row++) {
                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;
                            $flag_mail = 0;
                            $string_error = '';
                            $flag_contract_form = 0;

                            $flag_id_commodity_code;
                            $flag_id_item_description;

                            // $value_cell_commodity_code = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_item_description = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_quantity = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_cell_unit_price = isset($data[$row][2]) ? $data[$row][2] : '';

                            /*check null*/
                            if (is_null($value_cell_item_description) == true) {
                                $string_error .= _l('item_description') . _l('not_yet_entered');
                                $flag = 1;
                            }
                            if (!is_numeric(trim($value_cell_quantity, " "))) {

                                $string_error .= _l('quantity') . _l('_not_a_number');
                                $flag2 = 1;
                            }
                            if (!is_numeric(trim($value_cell_unit_price, " "))) {

                                $string_error .= _l('unit_price') . _l('_not_a_number');
                                $flag2 = 1;
                            }
                            if (($flag == 1) || ($flag2 == 1)) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_cell_item_description,
                                    $value_cell_quantity,
                                    $value_cell_unit_price,
                                    $string_error,
                                ]);

                                $numRow++;
                                $total_rows_data_error++;
                                $message = 'Import Error In Some Item';
                            }
                            if (($flag == 0) && ($flag2 == 0)) {
                                $item_name = $value_cell_commodity_code . ' ' . $item_value->description;

                                // if (is_null($value_cell_commodity_code) != true) {
                                $rows[] = $row;
                                // $list_item .= $this->purchase_model->create_wo_order_row_template('newitems[' . $index_quote . ']',  $item_name, $value_cell_item_description, '', '', $value_cell_quantity, '', $value_cell_unit_price, '', $item_value->id, '', '',  '', '', '', '', '', '', '', '', true, '', '');
                                $list_item .= $this->purchase_model->create_wo_order_row_template('newitems[' . $index_quote . ']', '', $value_cell_item_description, '', '', $value_cell_quantity, '', $value_cell_unit_price, '', $item_value->id, '', '',  '', '', '', '', '', '', '', $index_quote, '', 1, '', [], true);
                                // }
                                $index_quote++;
                                $total_rows_data++;
                                $message = 'Import Item successfully';
                            }
                        }
                        $total_rows = $total_rows;
                        $data['total_rows_post'] = count($rows);
                        $total_row_success = count($rows);
                        $total_row_false = $total_rows - (int) count($rows);
                        if (($total_rows_data_error > 0) || ($total_row_false != 0)) {

                            $filename = 'FILE_ERROR_IMPORT_ITEMS_WORK_ORDER' . get_staff_user_id() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, WORK_ORDER_IMPORT_ITEMS_ERROR . $filename, $filename));

                            $filename = WORK_ORDER_IMPORT_ITEMS_ERROR . $filename;
                        }
                        $list_item = $list_item;
                        @delete_dir($tmpDir);
                    }
                } else {
                    set_alert('warning', 'Import Item failed');
                }
            }
        }
        echo json_encode([
            'message' => $message,
            'total_row_success' => $total_row_success,
            'total_row_false' => $total_rows_data_error,
            'total_rows' => $total_rows_data,
            'site_url' => site_url(),
            'staff_id' => get_staff_user_id(),
            'total_rows_data_error' => $total_rows_data_error,
            'filename' => $filename,
            'list_item' => $list_item
        ]);
    }
    public function import_file_xlsx_order_tracker_items()
    {

        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if ($this->input->post()) {

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                //do_action('before_import_leads');

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $import_result = true;
                        $rows = [];

                        //Writer file
                        $writer_header = array(
                            "(*)" . _l('order_scope') => 'string',
                            _l('order_date') => 'date',
                            _l('completion_date')    => 'date',
                            _l('budget_ro_projection')    => 'numeric',
                            _l('order_value')    => 'numeric',
                            _l('committed_contract_amount')    => 'numeric',
                            _l('change_order_amount')    => 'numeric',
                            _l('anticipate_variation')    => 'numeric',
                            _l('final_certified_amount')    => 'numeric',
                            _l('remarks')    => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();

                        $col_style1 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
                        $style1 = ['widths' => $widths_arr, 'fill' => '#ff9800',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

                        $writer->writeSheetHeader_v2('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr, 'fill' => '#f44336',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13], $col_style1, $style1);

                        //init file error end

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);


                        // start row write 2
                        $numRow = 2;
                        $total_rows = 0;
                        $total_rows_actualy = 0;
                        $list_item = $this->purchase_model->create_order_tracker_row_template();
                        //get data for compare
                        $index_quote = 0;
                        for ($row = 1; $row < count($data); $row++) {

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error = '';
                            $flag_contract_form = 0;

                            $flag_id_commodity_code;
                            $flag_id_order_scope;


                            $value_cell_order_scope = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_order_date = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_cell_completion_date = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_cell_budget = isset($data[$row][3]) ? $data[$row][3] : '';
                            $value_cell_order_value = isset($data[$row][4]) ? $data[$row][4] : '';
                            $value_cell_contract_amount = isset($data[$row][5]) ? $data[$row][5] : '';
                            $value_cell_change_order = isset($data[$row][6]) ? $data[$row][6] : '';
                            $value_cell_anticipate_variation = isset($data[$row][7]) ? $data[$row][7] : '';
                            $value_cell_total_certified_amount = isset($data[$row][8]) ? $data[$row][8] : '';
                            $value_cell_total_remaks = isset($data[$row][9]) ? $data[$row][9] : '';


                            /*check null*/
                            if (is_null($value_cell_order_scope) == true) {
                                $string_error .= _l('order_scope') . ' ' . _l('not_yet_entered');
                                $flag = 1;
                            }
                            $value_cell_budget = trim($value_cell_budget, " ");
                            if ($value_cell_budget !== "" && !is_numeric($value_cell_budget)) {
                                $string_error .= _l('budget_ro_projection') . ' ' . _l('_not_a_number');
                                $flag2 = 1;
                            }

                            $value_cell_order_value = trim($value_cell_order_value, " ");
                            if ($value_cell_order_value !== "" && !is_numeric($value_cell_order_value)) {
                                $string_error .= _l('order_value') . ' ' . _l('_not_a_number');
                                $flag2 = 1;
                            }

                            $value_cell_contract_amount = trim($value_cell_contract_amount, " ");
                            if ($value_cell_contract_amount !== "" && !is_numeric($value_cell_contract_amount)) {
                                $string_error .= _l('committed_contract_amount') . ' ' . _l('_not_a_number');
                                $flag2 = 1;
                            }

                            $value_cell_change_order = trim($value_cell_change_order, " ");
                            if ($value_cell_change_order !== "" && !is_numeric($value_cell_change_order)) {
                                $string_error .= _l('change_order_amount') . ' ' . _l('_not_a_number');
                                $flag2 = 1;
                            }

                            $value_cell_anticipate_variation = trim($value_cell_anticipate_variation, " ");
                            if ($value_cell_anticipate_variation !== "" && !is_numeric($value_cell_anticipate_variation)) {
                                $string_error .= _l('anticipate_variation') . ' ' . _l('_not_a_number');
                                $flag2 = 1;
                            }

                            $value_cell_total_certified_amount = trim($value_cell_total_certified_amount, " ");
                            if ($value_cell_total_certified_amount !== "" && !is_numeric($value_cell_total_certified_amount)) {
                                $string_error .= _l('final_certified_amount') . ' ' . _l('_not_a_number');
                                $flag2 = 1;
                            }

                            if (($flag == 1) || ($flag2 == 1)) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_cell_order_scope,
                                    $value_cell_order_date,
                                    $value_cell_completion_date,
                                    $value_cell_budget,
                                    $value_cell_order_value,
                                    $value_cell_contract_amount,
                                    $value_cell_change_order,
                                    $value_cell_anticipate_variation,
                                    $value_cell_total_certified_amount,
                                    $value_cell_total_remaks,
                                    $string_error,
                                ]);

                                $numRow++;
                                $total_rows_data_error++;
                                $message = 'Import Error In Some Item';
                            }
                            if (($flag == 0) && ($flag2 == 0)) {

                                $rows[] = $row;
                                $list_item .= $this->purchase_model->create_order_tracker_row_template('newitems[' . $index_quote . ']', $value_cell_order_scope, '', $value_cell_order_date, $value_cell_completion_date, $value_cell_budget, $value_cell_contract_amount, $value_cell_change_order, $value_cell_anticipate_variation, $value_cell_total_certified_amount, '', '', $value_cell_total_remaks, $value_cell_order_value, '');

                                $index_quote++;
                                $total_rows_data++;
                                $message = 'Data Import successfully';
                            }
                        }

                        $total_rows = $total_rows;
                        $data['total_rows_post'] = count($rows);
                        $total_row_success = count($rows);
                        $total_row_false = $total_rows - (int) count($rows);

                        if (($total_rows_data_error > 0) || ($total_row_false != 0)) {

                            $filename = 'FILE_ERROR_IMPORT_ORDER_TRACKER' . get_staff_user_id() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_ORDER_IMPORT_ORDER_TRACKER_ERROR . $filename, $filename));

                            $filename = PURCHASE_ORDER_IMPORT_ORDER_TRACKER_ERROR . $filename;
                        }
                        $list_item = $list_item;
                        @delete_dir($tmpDir);
                    }
                } else {
                    set_alert('warning', 'Import Item failed');
                }
            }
        }

        echo json_encode([
            'message' => $message,
            'total_row_success' => $total_row_success,
            'total_row_false' => $total_rows_data_error,
            'total_rows' => $total_rows_data,
            'site_url' => site_url(),
            'staff_id' => get_staff_user_id(),
            'total_rows_data_error' => $total_rows_data_error,
            'filename' => $filename,
            'list_item' => $list_item
        ]);
    }

    public function get_rating($id)
    {
        $rating = $this->purchase_model->get_rating_by_id($id);
        if ($rating) {
            echo json_encode($rating);
        } else {
            echo json_encode(['error' => 'Rating not found']);
        }
    }
    public function delete_rating($id, $vendor_id)
    {
        $success = $this->purchase_model->delete_rating($id);
        if ($success == true) {
            set_alert('success', _l('deleted_successfully', _l('vendor')));
        }
        redirect(admin_url('purchase/vendor/' . $vendor_id));
    }
    public function export_tblitems_to_json()
    {


        // Load the database library
        $this->load->database();

        // Query to fetch data from tblitems
        $query = $this->db->get('tblitems');

        // Check if there are records
        if ($query->num_rows() > 0) {
            $items = $query->result_array();

            // Convert data to JSON format
            $jsonData = json_encode($items, JSON_PRETTY_PRINT);

            // File path to save the JSON file
            // $filePath = './uploads/tblitems.json';
            $base_path = get_upload_path_by_type('item_json');
            $filePath = $base_path . 'tblitems.json';

            // Write JSON data to file
            if (file_put_contents($filePath, $jsonData)) {
                // Set response or flash message
                $this->session->set_flashdata('success', 'JSON file created successfully: ' . $filePath);
            } else {
                // Set response or flash message
                $this->session->set_flashdata('error', 'Error writing JSON file.');
            }
        } else {
            // Set response or flash message
            $this->session->set_flashdata('info', 'No records found in the table.');
        }

        // Redirect to a page (optional)
        // redirect('items');
    }

    public function fetch_items()
    {
        // Get the search term from the request
        $search = $this->input->get('search');
        $items = [];

        // Path to the JSON file
        $base_path = get_upload_path_by_type('item_json');
        $jsonFilePath = $base_path . 'tblitems.json';

        if (file_exists($jsonFilePath)) {
            $jsonData = file_get_contents($jsonFilePath);
            $allItems = json_decode($jsonData, true);

            // Filter items that match the search term in `description` or `commodity_code`
            $items = array_filter($allItems, function ($item) use ($search) {
                return (stripos($item['description'], $search) !== false ||
                    stripos($item['commodity_code'], $search) !== false);
            });
        }
        // Send the filtered items as JSON response
        echo json_encode(array_values($items)); // Reindex array
    }

    public function order_tracker()
    {
        $data['title'] = _l('order_tracker');
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['projects'] = $this->projects_model->get();
        $data['order_tracker_row_template'] = $this->purchase_model->create_order_tracker_row_template();
        $data['budget_head'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['rli_filters'] = $this->purchase_model->get_all_rli_filters();
        $data['sub_groups_pur'] = $this->purchase_model->get_sub_group();
        $this->load->view('order_tracker/manage', $data);
    }
    public function update_completion_date()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $completion_date = $this->input->post('completion_date');

        if (!$id || !$table || !$completion_date) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Determine the table to update
        // $tableName = $table === 'wo_orders' ? 'tblwo_orders' : 'tblpur_orders';
        if ($table === 'pur_orders') {
            $tableName = 'tblpur_orders';
        } elseif ($table === 'wo_orders') {
            $tableName = 'tblwo_orders';
        } elseif ($table === 'order_tracker') {
            $tableName = 'tblpur_order_tracker';
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update($tableName, ['completion_date' => $completion_date]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('completion_date_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }
    public function update_order_date()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $order_date = $this->input->post('orderDate');

        if (!$id || !$table || !$order_date) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Determine the table to update
        // $tableName = $table === 'wo_orders' ? 'tblwo_orders' : 'tblpur_orders';
        if ($table === 'pur_orders') {
            $tableName = 'tblpur_orders';
        } elseif ($table === 'wo_orders') {
            $tableName = 'tblwo_orders';
        } elseif ($table === 'order_tracker') {
            $tableName = 'tblpur_order_tracker';
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update($tableName, ['order_date' => $order_date]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('order_date_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }
    public function update_budget()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $budget = $this->input->post('budget');

        if (!$id || !$table || !$budget) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Determine the table to update
        // $tableName = $table === 'wo_orders' ? 'tblwo_orders' : 'tblpur_orders';
        if ($table === 'pur_orders') {
            $tableName = 'tblpur_orders';
        } elseif ($table === 'wo_orders') {
            $tableName = 'tblwo_orders';
        } elseif ($table === 'order_tracker') {
            $tableName = 'tblpur_order_tracker';
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update($tableName, ['budget' => $budget]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('amount_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_order_tracker_contract_amount()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $total = $this->input->post('total');

        if (!$id || !$table || !$total) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        if ($table === 'order_tracker') {
            $tableName = 'tblpur_order_tracker';
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update($tableName, ['total' => $total]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('amount_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_anticipate_variation()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $anticipate_variation = $this->input->post('anticipate_variation');

        if (!$id || !$table || !$anticipate_variation) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // $tableName = $table === 'wo_orders' ? 'tblwo_orders' : 'tblpur_orders';

        if ($table === 'pur_orders') {
            $tableName = 'tblpur_orders';
        } elseif ($table === 'wo_orders') {
            $tableName = 'tblwo_orders';
        } elseif ($table === 'order_tracker') {
            $tableName = 'tblpur_order_tracker';
        }

        $this->db->where('id', $id);
        $success = $this->db->update($tableName, ['anticipate_variation' => $anticipate_variation]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('anticipate_variation_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_final_certified_amount()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $finalCertifiedAmount = $this->input->post('finalCertifiedAmount');

        if (!$id || !$table || !$finalCertifiedAmount) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }
        if ($table === 'pur_orders') {
            $aColumn_name = 'pur_order';
            $tableName = 'tblpur_invoices';
        } elseif ($table === 'wo_orders') {
            $aColumn_name = 'wo_order';
            $tableName = 'tblpur_invoices';
        } elseif ($table === 'order_tracker') {
            $tableName = 'tblpur_order_tracker';
        }
        if ($table == 'pur_orders' || $table == 'wo_orders') {
            $this->db->where($aColumn_name, $id);
            $success = $this->db->update($tableName, ['final_certified_amount' => $finalCertifiedAmount]);
        } else {
            $this->db->where('id', $id);
            $success = $this->db->update($tableName, ['final_certified_amount' => $finalCertifiedAmount]);
        }
        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('final_certified_amount_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }
    public function update_change_order_amount()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $changeOrderAmount = $this->input->post('changeOrderAmount');

        if (!$id || !$table || !$changeOrderAmount) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }
        if ($table === 'pur_orders') {
            $aColumn_name = 'po_order_id';
            $tableName = 'tblco_request';
        } elseif ($table === 'wo_orders') {
            $aColumn_name = 'wo_order_id';
            $tableName = 'tblco_request';
        } elseif ($table === 'order_tracker') {
            $tableName = 'tblpur_order_tracker';
        }
        if ($table == 'pur_orders' || $table == 'wo_orders') {
            $this->db->where($aColumn_name, $id);
            $success = $this->db->update($tableName, ['total' => $changeOrderAmount]);
        } else {
            $this->db->where('id', $id);
            $success = $this->db->update($tableName, ['co_total' => $changeOrderAmount]);
        }
        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('change_order_amount_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }
    public function update_remarks()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $remarks = $this->input->post('remarks');

        if (!$id || !$table) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // $tableName = $table === 'wo_orders' ? 'tblwo_orders' : 'tblpur_orders';
        if ($table === 'pur_orders') {
            $tableName = 'tblpur_orders';
        } elseif ($table === 'wo_orders') {
            $tableName = 'tblwo_orders';
        } elseif ($table === 'order_tracker') {
            $tableName = 'tblpur_order_tracker';
        }

        $this->db->where('id', $id);
        $success = $this->db->update($tableName, ['remarks' => $remarks]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('remarks_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    /**
     *  item_tracker_report
     *  
     *  @return json
     */
    public function item_tracker_report()
    {
        if ($this->input->is_ajax_request()) {
            $select = [
                db_prefix() . 'goods_receipt_detail.id as id',
                db_prefix() . 'goods_receipt_detail.goods_receipt_id as goods_receipt_id',
                db_prefix() . 'goods_receipt_detail.commodity_name as commodity_name',
                db_prefix() . 'goods_receipt_detail.description as description',
                db_prefix() . 'goods_receipt_detail.quantities as quantities',
                db_prefix() . 'goods_receipt_detail.po_quantities as po_quantities',
                db_prefix() . 'goods_receipt_detail.payment_date as payment_date',
                db_prefix() . 'goods_receipt_detail.est_delivery_date as est_delivery_date',
                db_prefix() . 'goods_receipt_detail.delivery_date as delivery_date',
                db_prefix() . 'goods_receipt_detail.production_status as production_status',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'goods_receipt_detail';
            $join         = [];
            $join         = [
                'INNER JOIN ' . db_prefix() . 'goods_receipt ON ' . db_prefix() . 'goods_receipt.id = ' . db_prefix() . 'goods_receipt_detail.goods_receipt_id',
            ];

            // Initialize filter variables
            $purOrdersVendor1 = [];
            $purOrdersVendor2 = [];
            $purOrdersReturn1 = [];
            $purOrdersReturn0 = [];
            $productionStatusFilters = [];

            if ($this->input->post('vendor')) {
                $vendor_ids_filters = $this->input->post('vendor');

                foreach ($vendor_ids_filters as $vendor_id) {
                    $status = get_vendor_goods_status($vendor_id);

                    if ($status == 1) {
                        $purOrdersVendor1[] = $vendor_id;
                    } elseif ($status == 0) {
                        $purOrdersVendor2[] = $vendor_id;
                    }
                }
            }

            if ($this->input->post('pur_order')) {
                $pur_order_ids_filters = $this->input->post('pur_order');

                foreach ($pur_order_ids_filters as $pur_order_id) {
                    $status = get_pur_order_goods_status($pur_order_id);

                    if ($status == 1) {
                        $purOrdersReturn1[] = $pur_order_id;
                    } elseif ($status == 0) {
                        $purOrdersReturn0[] = $pur_order_id;
                    }
                }
            }

            // Handle production_status filter similar to other filters
            if ($this->input->post('production_status')) {
                $production_status_filters = $this->input->post('production_status');
                if (!empty($production_status_filters)) {
                    $productionStatusFilters = is_array($production_status_filters) ? $production_status_filters : [$production_status_filters];
                }
            }

            // Handle Vendor Filter
            if ($purOrdersVendor1) {
                array_push($where, 'AND ' . db_prefix() . 'goods_receipt.supplier_code IN (' . implode(',', $purOrdersVendor1) . ')');
            }

            if ($purOrdersReturn1) {
                array_push($where, 'AND ' . db_prefix() . 'goods_receipt.pr_order_id IN (' . implode(',', $purOrdersReturn1) . ')');
            }

            // Handle Production Status Filter for first query
            if (!empty($productionStatusFilters)) {
                array_push($where, 'AND ' . db_prefix() . 'goods_receipt_detail.production_status IN (' . implode(',', $productionStatusFilters) . ')');
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);

            $select1 = [
                db_prefix() . 'pur_order_detail.id as id',
                db_prefix() . 'pur_order_detail.pur_order as pur_order',
                db_prefix() . 'pur_order_detail.item_name as commodity_name',
                db_prefix() . 'pur_order_detail.description as description',
                db_prefix() . 'pur_order_detail.quantity as quantities',
                db_prefix() . 'pur_order_detail.po_quantities as po_quantities',
                db_prefix() . 'pur_order_detail.payment_date as payment_date',
                db_prefix() . 'pur_order_detail.est_delivery_date as est_delivery_date',
                db_prefix() . 'pur_order_detail.delivery_date as delivery_date',
                db_prefix() . 'pur_order_detail.production_status as production_status',
            ];
            $where1 = [];

            $aColumns1     = $select1;
            $sIndexColumn1 = 'id';
            $sTable1       = db_prefix() . 'pur_order_detail';
            $join1         = [];
            $join1         = [
                'INNER JOIN ' . db_prefix() . 'pur_orders ON ' . db_prefix() . 'pur_orders.id = ' . db_prefix() . 'pur_order_detail.pur_order',
            ];
            $where1[] = 'AND ' . db_prefix() . 'pur_orders.goods_id = 0';

            if ($purOrdersReturn0) {
                array_push($where1, 'AND ' . db_prefix() . 'pur_orders.id IN (' . implode(',', $purOrdersReturn0) . ')');
            }
            if ($purOrdersVendor2) {
                array_push($where1, 'AND ' . db_prefix() . 'pur_orders.vendor IN (' . implode(',', $purOrdersVendor2) . ')');
            }

            // Handle Production Status Filter for second query
            if (!empty($productionStatusFilters)) {
                array_push($where1, 'AND ' . db_prefix() . 'pur_order_detail.production_status IN (' . implode(',', $productionStatusFilters) . ')');
            }

            $result1 = data_tables_init($aColumns1, $sIndexColumn1, $sTable1, $join1, $where1);

            $output  = $result['output'];

            $rResult0 = isset($result['rResult']) && is_array($result['rResult']) ? $result['rResult'] : [];
            $rResult1 = isset($result1['rResult']) && is_array($result1['rResult']) ? $result1['rResult'] : [];

            if (!empty($purOrdersReturn0) && !empty($purOrdersReturn1)) {
                $rResult = array_merge($rResult0, $rResult1);
            } elseif ($purOrdersReturn1) {
                $rResult = $rResult0;
            } elseif ($purOrdersReturn0) {
                $rResult = $rResult1;
            } else {
                $rResult = array_merge($rResult0, $rResult1);
            }

            $tracker = [];

            foreach ($rResult as $aRow) {
                $row = [];

                $goods_receipt = get_goods_receipt_code($aRow['goods_receipt_id']);
                if ($goods_receipt->pr_order_id > 0) {
                    $row[] = get_pur_order_name($goods_receipt->pr_order_id);
                } else {
                    $row[] = get_pur_order_name($aRow['pur_order']);
                }

                $row[] = $aRow['commodity_name'];
                $row[] = $aRow['description'];

                if ($goods_receipt->pr_order_id > 0) {
                    $row[] = isset($aRow['po_quantities']) && $aRow['po_quantities'] !== null ? app_format_number($aRow['po_quantities']) : '-';
                } else {
                    $row[] = isset($aRow['quantities']) && $aRow['quantities'] !== null ? app_format_number($aRow['quantities']) : '-';
                }

                if ($goods_receipt->pr_order_id > 0) {
                    $row[] = app_format_number($aRow['quantities']);
                } else {
                    $row[] = isset($aRow['po_quantities']) && $aRow['po_quantities'] !== null ? app_format_number($aRow['po_quantities']) : '-';
                }

                if ($goods_receipt->pr_order_id > 0) {
                    $remaining_quantities = $aRow['po_quantities'] - $aRow['quantities'];
                } else {
                    $remaining_quantities = isset($aRow['quantities']) && $aRow['quantities'] !== null ? app_format_number($aRow['quantities']) : '-';
                }

                $row[] = app_format_number($remaining_quantities);

                $production_status = '';
                if ($aRow['production_status'] > 0) {
                    if ($aRow['production_status'] == 1) {
                        $production_status = _l('not_started');
                    } elseif ($aRow['production_status'] == 2) {
                        $production_status = _l('on_going');
                    } elseif ($aRow['production_status'] == 3) {
                        $production_status = _l('approved');
                    } else {
                        $production_status = '';
                    }
                }
                $row[] = $production_status;

                $row[] = !empty($aRow['payment_date']) ? date('d M, Y', strtotime($aRow['payment_date'])) : '-';
                $row[] = !empty($aRow['est_delivery_date']) ? date('d M, Y', strtotime($aRow['est_delivery_date'])) : '-';
                $row[] = !empty($aRow['delivery_date']) ? date('d M, Y', strtotime($aRow['delivery_date'])) : '-';

                $tracker[] = $row;
            }

            // Grouped data array
            $grouped_data = [];
            foreach ($tracker as $row) {
                $group = $row[0];
                if (!isset($grouped_data[$group])) {
                    $grouped_data[$group][] = [
                        "group_name" => '<span class="group-name-cell" style="text-align: center !important; display: block">' . $group . '</span>'
                    ];
                }
                $grouped_data[$group][] = $row;
            }

            // Flatten grouped data for DataTables
            $flattened_data = [];
            foreach ($grouped_data as $group_rows) {
                foreach ($group_rows as $row) {
                    unset($row[0]);
                    $row = array_values($row);
                    if (count($row) === 1) {
                        for ($i = 1; $i <= 8; $i++) {
                            $row[$i] = "";
                        }
                        ksort($row);
                    }
                    $flattened_data[] = $row;
                }
            }

            $output['aaData'] = $flattened_data;

            echo json_encode($output);
            die();
        }
    }

    public function update_vendor_invoice_number()
    {
        $id = $this->input->post('id');
        $vin = $this->input->post('vin');

        if (!$id || !$vin) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_invoices', ['vendor_invoice_number' => $vin]);
        $this->purchase_model->update_vbt_expense_ril_data($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Invoice number is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_invoice_date()
    {
        $id = $this->input->post('id');
        $invoice_date = $this->input->post('invoice_date');

        if (!$id || !$invoice_date) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_invoices', ['invoice_date' => $invoice_date]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Invoice date is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }


    public function update_adminnote()
    {
        $id = $this->input->post('id');
        $admin_note = $this->input->post('admin_note');

        if (!$id || !$admin_note) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_invoices', ['adminnote' => $admin_note]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Admin Note is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }



    public function update_description_services()
    {
        $id = $this->input->post('id');
        $update_description_services = $this->input->post('description_services');

        if (!$id || !$update_description_services) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_invoices', ['description_services' => $update_description_services]);
        $this->purchase_model->update_vbt_expense_ril_data($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Description Services is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function change_budget_head($budgetid, $invoice_id)
    {
        $success = $this->purchase_model->change_budget_head($budgetid, $invoice_id);
        $message = '';
        $html = '';
        $status_str = '';
        $class = '';
        if ($success == true) {
            $this->purchase_model->update_vbt_expense_ril_data($invoice_id);
            $message = _l('change_budget_head_successfully');
        } else {
            $message = "Failed";
        }

        $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $html .= '<a href="#" class="dropdown-toggle text-dark" id="tableChangeBudget-' . $invoice_id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $html .= '</a>';
        $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableChangeBudget-' . $invoice_id . '">';

        $group_name_item = get_group_name_item();
        foreach ($group_name_item as $gkey => $gvalue) {
            $html .= '<li>
                <a href="#" onclick="change_budget_head( ' . $gvalue['id'] . ',' . $invoice_id . '); return false;">
                ' . $gvalue['name'] . '
                </a>
            </li>';
        }
        $selected_budget = get_group_name_item($budgetid);
        $status_str = $selected_budget->name;
        $class = 'label-info';

        $html .= '</ul>';
        $html .= '</div>';

        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }

    /**
     * manage purchase
     * @param  integer $id
     * @return view
     */
    public function manage_purchase($id = '')
    {
        $data['title'] = 'Purchase Tracker';
        $data['purchase_id'] = $id;
        $data['vendors'] = $this->purchase_model->get_vendor();
        $this->load->view('manage_goods_receipt/manage_purchase', $data);
    }

    /**
     * table manage goods receipt
     * @param  integer $id
     * @return array
     */
    public function table_manage_goods_receipt()
    {
        $this->app->get_table_data(module_views_path('purchase', 'manage_goods_receipt/table_manage_goods_receipt'));
    }

    /**
     * view purchase
     * @param  integer $id
     * @return view
     */
    public function view_purchase($id)
    {
        //approval
        $data['purchase_tracker'] = true;
        $this->load->model('warehouse/warehouse_model');
        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if ((isset($send_mail_approve)) && $send_mail_approve != '') {
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }

        $data['get_staff_sign'] = $this->warehouse_model->get_staff_sign($id, 1);

        $data['check_approve_status'] = $this->warehouse_model->check_approval_details($id, 1);
        $data['list_approve_status'] = $this->warehouse_model->get_list_approval_details($id, 1);
        $data['payslip_log'] = $this->warehouse_model->get_activity_log($id, 1);

        //get vaule render dropdown select
        $data['commodity_code_name'] = $this->warehouse_model->get_commodity_code_name();
        $data['units_code_name'] = $this->warehouse_model->get_units_code_name();
        $data['units_warehouse_name'] = $this->warehouse_model->get_warehouse_code_name();

        $data['goods_receipt_detail'] = $this->warehouse_model->get_goods_receipt_detail($id);

        $data['goods_receipt'] = $this->warehouse_model->get_goods_receipt($id);

        $data['tax_data'] = $this->warehouse_model->get_html_tax_receip($id);

        $data['title'] = _l('stock_received_info');
        $check_appr = $this->warehouse_model->get_approve_setting('1');
        $data['check_appr'] = $check_appr;
        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $data['base_currency'] = $base_currency;

        $this->load->view('manage_goods_receipt/view_purchase', $data);
    }

    /**
     * view po tracker
     * @param  integer $id
     * @return view
     */
    public function view_po_tracker($id)
    {
        //approval
        $this->load->model('warehouse/warehouse_model');
        $data['get_staff_sign'] = array();
        $data['check_approve_status'] = false;
        $data['list_approve_status'] = array();
        $data['payslip_log'] = array();
        $data['commodity_code_name'] = $this->warehouse_model->get_commodity_code_name();
        $data['units_code_name'] = $this->warehouse_model->get_units_code_name();
        $data['units_warehouse_name'] = $this->warehouse_model->get_warehouse_code_name();
        $data['tax_data'] = $this->warehouse_model->get_html_tax_receip($id);
        $data['title'] = _l('stock_received_info');
        $check_appr = $this->warehouse_model->get_approve_setting('1');
        $data['check_appr'] = $check_appr;
        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $data['base_currency'] = $base_currency;
        $pur_order = $this->purchase_model->get_pur_order($id);
        $data['pur_order'] = $pur_order;
        $pur_order_details = $this->purchase_model->get_pur_order_detail($id);
        $goods_receipt = array();
        $goods_receipt['id'] = 0;
        $goods_receipt['approval'] = '';
        $goods_receipt['supplier_code'] = $pur_order->vendor;
        $goods_receipt['deliver_name'] = '';
        $goods_receipt['buyer_id'] = $pur_order->buyer;
        $goods_receipt['goods_receipt_code'] = '';
        $goods_receipt['description'] = '';
        $goods_receipt['kind'] = $pur_order->kind;
        $goods_receipt['pr_order_id'] = $pur_order->id;
        $data['goods_receipt'] = (object) $goods_receipt;
        if (!empty($pur_order_details)) {
            foreach ($pur_order_details as $key => $detail) {
                $pur_order_details[$key]['commodity_code'] = $detail['item_code'];
                $pur_order_details[$key]['po_quantities'] = (float) $detail['quantity'];
                $pur_order_details[$key]['quantities'] = 0;
            }
        }
        $data['goods_receipt_detail'] = $pur_order_details;

        $this->load->view('manage_goods_receipt/view_purchase', $data);
    }

    /**
     * import file xlsx vendor billing tracker
     * @return json
     */
    public function import_file_xlsx_vendor_billing_tracker()
    {
        $this->load->model('invoices_model');
        if (!is_admin() && !has_permission('purchase_items', '', 'create')) {
            access_denied(_l('purchase'));
        }

        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

            /*delete file old before export file*/
            $path_before = COMMODITY_ERROR_PUR . 'FILE_ERROR_VENDOR_BILLING_TRACKER' . get_staff_user_id() . '.xlsx';
            if (file_exists($path_before)) {
                unlink(COMMODITY_ERROR_PUR . 'FILE_ERROR_VENDOR_BILLING_TRACKER' . get_staff_user_id() . '.xlsx');
            }

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }
                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $writer_header = array(
                            _l('invoice_code') => 'string',
                            _l('invoice_number') => 'string',
                            _l('pur_vendor') => 'string',
                            _l('group_pur') => 'string',
                            _l('invoice_date') => 'string',
                            _l('project') => 'string',
                            _l('description_of_services') => 'string',
                            _l('amount_without_tax') => 'string',
                            _l('vendor_submitted_tax_amount') => 'string',
                            _l('error') => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr]);

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $total_rows = 0;
                        $total_row_false = 0;

                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error = '';
                            $flag_id_invoice_id = '';

                            $flag_id_vendor_id;
                            $flag_id_budget_head;
                            $flag_id_project;

                            $value_invoice_code = isset($data[$row][0]) ? $data[$row][0] : '';
                            $value_invoice_number = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_vendor = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_budget_head = isset($data[$row][3]) ? $data[$row][3] : '';
                            $value_invoice_date = isset($data[$row][4]) ? $data[$row][4] : '';
                            $value_project = isset($data[$row][5]) ? $data[$row][5] : '';
                            $value_description_of_services = isset($data[$row][6]) ? $data[$row][6] : '';
                            $value_amount_wo_tax = isset($data[$row][7]) ? $data[$row][7] : '';
                            $value_tax_value = isset($data[$row][8]) ? $data[$row][8] : '';
                            $value_total_included_tax = 0.00;
                            $value_certified_amount = 0.00;

                            if (!empty($value_invoice_code)) {
                                $this->db->like('invoice_number', $value_invoice_code);
                                $result = $this->db->get(db_prefix() . 'pur_invoices')->row();
                                if (empty($result)) {
                                    $string_error .= _l('invoice_code') . ' ' . _l('does_not_exist');
                                    $flag2 = 1;
                                } else {
                                    $flag_id_invoice_id = $result->id;
                                }
                            }

                            if (empty($value_vendor)) {
                                $string_error .= _l('pur_vendor') . ' ' . _l('does_not_exist');
                                $flag2 = 1;
                            } else {
                                $this->db->like('company', $value_vendor);
                                $result = $this->db->get(db_prefix() . 'pur_vendor')->row();
                                if (empty($result)) {
                                    $string_error .= _l('pur_vendor') . ' ' . _l('does_not_exist');
                                    $flag2 = 1;
                                } else {
                                    $flag_id_vendor_id = $result->userid;
                                }
                            }

                            if (empty($value_budget_head)) {
                                $string_error .= _l('group_pur') . ' ' . _l('does_not_exist');
                                $flag2 = 1;
                            } else {
                                $this->db->like('name', $value_budget_head);
                                $result = $this->db->get(db_prefix() . 'items_groups')->row();
                                if (empty($result)) {
                                    $string_error .= _l('group_pur') . ' ' . _l('does_not_exist');
                                    $flag2 = 1;
                                } else {
                                    $flag_id_budget_head = $result->id;
                                }
                            }

                            if (empty($value_invoice_date)) {
                                $string_error .= _l('invoice_date') . ' ' . _l('does_not_exist');
                                $flag2 = 1;
                            } else {
                                if (is_numeric($value_invoice_date)) {
                                    $unix_timestamp = ($value_invoice_date - 25569) * 86400;
                                    $value_invoice_date = date('Y-m-d', $unix_timestamp);
                                } else {
                                    $value_invoice_date = date('Y-m-d', strtotime($value_invoice_date));
                                }
                            }

                            if (empty($value_project)) {
                                $string_error .= _l('project') . ' ' . _l('does_not_exist');
                                $flag2 = 1;
                            } else {
                                $this->db->like('name', $value_project);
                                $result = $this->db->get(db_prefix() . 'projects')->row();
                                if (empty($result)) {
                                    $string_error .= _l('project') . ' ' . _l('does_not_exist');
                                    $flag2 = 1;
                                } else {
                                    $flag_id_project = $result->id;
                                }
                            }

                            if (empty($value_description_of_services)) {
                                $string_error .= _l('description_of_services') . ' ' . _l('does_not_exist');
                                $flag2 = 1;
                            }

                            if (empty($value_amount_wo_tax)) {
                                $value_amount_wo_tax = 0.00;
                            } else {
                                $value_amount_wo_tax = str_replace(",", "", $value_amount_wo_tax);
                                $value_amount_wo_tax = floatval($value_amount_wo_tax);
                            }

                            if (empty($value_tax_value)) {
                                $value_tax_value = 0.00;
                            } else {
                                $value_tax_value = str_replace(",", "", $value_tax_value);
                                $value_tax_value = floatval($value_tax_value);
                            }

                            $value_total_included_tax = $value_amount_wo_tax + $value_tax_value;
                            $value_certified_amount = $value_amount_wo_tax + $value_tax_value;

                            if (($flag == 1) || $flag2 == 1) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_invoice_code,
                                    $value_invoice_number,
                                    $value_vendor,
                                    $value_budget_head,
                                    $value_invoice_date,
                                    $value_project,
                                    $value_description_of_services,
                                    $value_amount_wo_tax,
                                    $value_tax_value,
                                    $string_error,
                                ]);
                                $total_row_false++;
                            }

                            if ($flag == 0 && $flag2 == 0) {
                                $prefix = get_purchase_option('pur_inv_prefix');
                                $next_number = get_purchase_option('next_inv_number');
                                $invoice_number = $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);

                                $rd = array();

                                if (!empty($flag_id_invoice_id)) {
                                    $rd['vendor_invoice_number'] = !empty($value_invoice_number) ? $value_invoice_number : $invoice_number;
                                    $rd['vendor'] = isset($flag_id_vendor_id) ? $flag_id_vendor_id : 0;
                                    $rd['group_pur'] = isset($flag_id_budget_head) ? $flag_id_budget_head : 0;
                                    $rd['description_services'] = $value_description_of_services;
                                    $rd['invoice_date'] = $value_invoice_date;
                                    $rd['project_id'] = isset($flag_id_project) ? $flag_id_project : 1;
                                    $rd['vendor_submitted_amount_without_tax'] = $value_amount_wo_tax;
                                    $rd['vendor_submitted_tax_amount'] = $value_tax_value;
                                    $rd['vendor_submitted_amount'] = $value_total_included_tax;
                                    $rd['final_certified_amount'] = $value_certified_amount;

                                    $rows[] = $rd;

                                    // Update Vendor billing tracker
                                    $this->db->where('id', $flag_id_invoice_id);
                                    $this->db->update(db_prefix() . 'pur_invoices', $rd);
                                    $this->purchase_model->update_vbt_expense_ril_data($flag_id_invoice_id);
                                } else {
                                    $rd['invoice_number'] = $invoice_number;
                                    $rd['vendor_invoice_number'] = !empty($value_invoice_number) ? $value_invoice_number : $invoice_number;
                                    $rd['vendor'] = isset($flag_id_vendor_id) ? $flag_id_vendor_id : 0;
                                    $rd['group_pur'] = isset($flag_id_budget_head) ? $flag_id_budget_head : 0;
                                    $rd['description_services'] = $value_description_of_services;
                                    $rd['invoice_date'] = $value_invoice_date;
                                    $rd['currency'] = 3;
                                    $rd['to_currency'] = 3;
                                    $rd['date_add'] = date('Y-m-d');
                                    $rd['payment_status'] = 0;
                                    $rd['project_id'] = isset($flag_id_project) ? $flag_id_project : 1;
                                    $rd['vendor_submitted_amount_without_tax'] = $value_amount_wo_tax;
                                    $rd['vendor_submitted_tax_amount'] = $value_tax_value;
                                    $rd['vendor_submitted_amount'] = $value_total_included_tax;
                                    $rd['final_certified_amount'] = $value_certified_amount;

                                    $rows[] = $rd;

                                    $this->db->insert(db_prefix() . 'pur_invoices', $rd);
                                    $pur_invoice_id = $this->db->insert_id();
                                    if ($pur_invoice_id) {
                                        $this->db->where('option_name', 'next_inv_number');
                                        $this->db->update(db_prefix() . 'purchase_option', ['option_val' =>  $next_number + 1]);
                                    }
                                }
                            }
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        $dataerror = '';
                        $message = 'Not enought rows for importing';

                        if ($total_row_false != 0) {
                            if (!file_exists(FCPATH . PURCHASE_IMPORT_VENDOR_BILLING_TRACKER_ERROR)) {
                                mkdir(FCPATH . PURCHASE_IMPORT_VENDOR_BILLING_TRACKER_ERROR, 0755, true);
                            }
                            $filename = 'Import_item_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_IMPORT_VENDOR_BILLING_TRACKER_ERROR . $filename, $filename));
                        }
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }
        echo json_encode([
            'message'           => $message,
            'total_row_success' => $total_row_success,
            'total_row_false'   => $total_row_false,
            'total_rows'        => $total_rows,
            'site_url'          => site_url(),
            'staff_id'          => get_staff_user_id(),
            'filename'          => PURCHASE_IMPORT_VENDOR_BILLING_TRACKER_ERROR . $filename,

        ]);
    }

    public function update_certified_amount_without_tax()
    {
        $id = $this->input->post('id');
        $amount = $this->input->post('amount');

        if (!$id || !$amount) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_invoices', ['vendor_submitted_amount_without_tax' => $amount]);
        $this->purchase_model->update_vbt_expense_ril_data($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Certified amount without tax is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }


    public function update_vendor_submitted_tax_amount()
    {
        $id = $this->input->post('id');
        $amount = $this->input->post('amount');

        if (!$id || !$amount) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_invoices', ['vendor_submitted_tax_amount' => $amount]);
        $this->purchase_model->update_vbt_expense_ril_data($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Certified Tax amount is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }


    public function update_total_amount()
    {
        $id = $this->input->post('id');
        $amount = $this->input->post('total_amount');

        if (!$id || !$amount) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_invoices', ['final_certified_amount' => $amount]);
        $this->purchase_model->update_vbt_expense_ril_data($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Total Certified amount is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function savePreferences()
    {
        $data = $this->input->post();

        $id = $this->purchase_model->add_update_preferences($data);
        if ($id) {
            set_alert('success', _l('added_successfully', _l('pur_order')));

            redirect(admin_url('purchase/invoices'));
        }
    }

    public function getPreferences()
    {
        $module = $this->input->get('module');
        // Retrieve user preferences using the model
        $preferences = $this->purchase_model->get_datatable_preferences($module);
        // If no preferences exist, return an empty array (or set defaults)
        if (!$preferences) {
            $preferences = array();
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['preferences' => $preferences]));
    }
    public function invoice_payments()
    {
        $this->load->model('taxes_model');
        $this->load->model('currencies_model');
        $data['title'] = _l('vendor_payment_tracker');
        $data['contracts'] = $this->purchase_model->get_contract();
        $data['pur_orders'] = $this->purchase_model->get_list_pur_orders();
        $data['wo_orders'] = $this->purchase_model->get_list_wo_orders();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['customers'] = $this->clients_model->get();
        $data['projects'] = $this->projects_model->get();
        $data['expense_categories'] = $this->expenses_model->get_category();
        $data['taxes'] = $this->taxes_model->get();
        $data['currencies'] = $this->currencies_model->get();
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);
        $data['billing_invoices'] = $this->purchase_model->get_billing_invoices();
        $data['budget_head'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['invoices'] = get_all_applied_invoices();
        $this->load->view('invoice_payments/manage', $data);
    }

    public function table_pur_invoice_payments()
    {
        $this->app->get_table_data(module_views_path('purchase', 'invoice_payments/table_pur_invoice_payments'));
    }

    public function update_ril_previous_amount()
    {
        $id = $this->input->post('id');
        $amount = $this->input->post('amount');

        if (!$id || !$amount) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $success = $this->purchase_model->update_ril_payment_details($id, 'ril_previous', $amount);
        $this->purchase_model->update_final_ril_total($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'RIL upto previous is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_ril_this_bill_amount()
    {
        $id = $this->input->post('id');
        $amount = $this->input->post('amount');

        if (!$id || !$amount) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $success = $this->purchase_model->update_ril_payment_details($id, 'amount', $amount);
        $this->purchase_model->update_final_ril_total($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'RIL this bill is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_ril_date()
    {
        $id = $this->input->post('id');
        $ril_date = $this->input->post('ril_date');

        if (!$id || !$ril_date) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $success = $this->purchase_model->update_ril_payment_details($id, 'date', $ril_date);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'RIL date is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_bil_payment_date()
    {
        $data = $this->input->post();

        if (empty($data)) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $success = $this->purchase_model->update_bil_payment_date($data);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Payment date is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_bil_payment_made()
    {
        $data = $this->input->post();

        if (empty($data)) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $success = $this->purchase_model->update_bil_payment_made($data);
        $this->purchase_model->update_final_bil_total($data['vbt_id']);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Payment made is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_bil_payment_tds()
    {
        $data = $this->input->post();

        if (empty($data)) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $success = $this->purchase_model->update_bil_payment_tds($data);
        $this->purchase_model->update_final_bil_total($data['vbt_id']);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Payment made is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }
    public function add_order()
    {
        $data = $this->input->post();

        if ($data) {
            $id = $this->purchase_model->add_order_tracker($data);
            if ($id) {
                // Set a flash message if needed
                // set_alert('success', _l('added_successfully', _l('order_tracker')));

                // Return a JSON success response
                echo json_encode([
                    'success' => true,
                    'row_template' => $this->purchase_model->create_order_tracker_row_template(),
                    'message' => _l('added_successfully', _l('order_tracker'))
                ]);
            } else {
                // Return a JSON error response if the insert failed
                echo json_encode([
                    'success' => false,
                    'message' => _l('problem_adding_order')
                ]);
            }
        } else {
            // Return a JSON error response if no data was posted
            echo json_encode([
                'success' => false,
                'message' => _l('no_data_received')
            ]);
        }
        exit; // Stop further execution
    }


    public function get_order_tracker_row_template()
    {
        $name = $this->input->post('name');
        $order_scope = $this->input->post('order_scope');
        $vendor = $this->input->post('vendor');
        $order_date = $this->input->post('order_date');
        $completion_date = $this->input->post('completion_date');
        $budget_ro_projection = $this->input->post('budget_ro_projection');
        $committed_contract_amount = $this->input->post('committed_contract_amount');
        $change_order_amount = $this->input->post('change_order_amount');
        $anticipate_variation = $this->input->post('anticipate_variation');
        $final_certified_amount = $this->input->post('final_certified_amount');
        $project = $this->input->post('project');
        $kind = $this->input->post('kind');
        $group_pur = $this->input->post('group_pur');
        $remarks = $this->input->post('remarks');
        $order_value = $this->input->post('order_value');

        echo $this->purchase_model->create_order_tracker_row_template($name, $order_scope, $vendor, $order_date, $completion_date, $budget_ro_projection, $committed_contract_amount, $change_order_amount, $anticipate_variation, $final_certified_amount, $kind, $group_pur, $remarks, $order_value, $project);
    }

    public function update_billing_remarks()
    {
        $id = $this->input->post('id');
        $billing_remarks = $this->input->post('billing_remarks');

        if (!$id || !$billing_remarks) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_invoices', ['billing_remarks' => $billing_remarks]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Remarks is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_payment_remarks()
    {
        $id = $this->input->post('id');
        $payment_remarks = $this->input->post('payment_remarks');

        if (!$id || !$payment_remarks) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_invoices', ['payment_remarks' => $payment_remarks]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Remarks is updated']);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }
    // Example controller method to download invoice attachments as a ZIP file:
    public function download_invoice_attachments($invoice_id)
    {
        $this->load->model('purchase/purchase_model');
        $attachments = $this->purchase_model->get_purchase_invoice_attachments($invoice_id);

        if (empty($attachments)) {
            // No attachments to download.
            echo _l('no_attachments_found');
            return;
        }

        // Create a temporary file for the ZIP archive.
        $tempZipPath = tempnam(sys_get_temp_dir(), 'attachments_') . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($tempZipPath, ZipArchive::CREATE) !== TRUE) {
            echo _l('unable_to_create_zip');
            return;
        }

        // Loop through each attachment and add it to the ZIP.
        foreach ($attachments as $attachment) {
            // Build the file path. Adjust PURCHASE_PATH if necessary.
            $file_path = PURCHASE_PATH . 'pur_invoice/' . $attachment['rel_id'] . '/' . $attachment['file_name'];
            if (file_exists($file_path)) {
                // Add the file with its original file name.
                $zip->addFile($file_path, $attachment['file_name']);
            }
        }
        $zip->close();

        // Set headers to download the ZIP file.
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="invoice_' . $invoice_id . '_attachments.zip"');
        header('Content-Length: ' . filesize($tempZipPath));
        readfile($tempZipPath);
        unlink($tempZipPath); // Remove the temporary file.
        exit;
    }
    public function bulk_convert_ril_bill()
    {
        $response = array();
        $data = $this->input->post();
        $bulk_html = $this->purchase_model->bulk_convert_ril_bill($data);
        if (!empty($bulk_html)) {
            $response['success'] = true;
            $response['bulk_html'] = $bulk_html;
        } else {
            $response['success'] = false;
            $response['message'] = _l('you_have_not_select_the_convert_button_rows');
        }
        echo json_encode($response);
    }

    public function add_bulk_convert_ril_bill()
    {
        $input = $this->input->post();
        if (!empty($input)) {
            unset($input['convert_expense_name']);
            unset($input['convert_category']);
            unset($input['convert_date']);
            unset($input['convert_select_invoice']);
            unset($input['convert_applied_to_invoice']);
            unset($input['bulk_pur_order']);
            unset($input['bulk_wo_order']);
            unset($input['bulk_order_tracker']);
            $neworderitems = array();
            $bulk_active_tab = '';
            if (isset($input['neworderitems'])) {
                $neworderitems = $input['neworderitems'];
                unset($input['neworderitems']);
            }
            if (isset($input['bulk_active_tab'])) {
                $bulk_active_tab = $input['bulk_active_tab'];
                unset($input['bulk_active_tab']);
            }
            if ($bulk_active_tab == 'bulk_action') {
                $this->load->model('expenses_model');
                $input = $input['newitems'];
                foreach ($input as $ikey => $data) {
                    if (isset($data['pur_invoice'])) {
                        $pur_invoice = $data['pur_invoice'];
                        $select_invoice = $data['select_invoice'];
                        $applied_to_invoice = $data['applied_to_invoice'];
                        $data['vbt_id'] = $pur_invoice;
                        unset($data['pur_invoice']);
                        unset($data['select_invoice']);
                        unset($data['applied_to_invoice']);
                    }
                    if ($select_invoice == 'none') {
                        $this->purchase_model->update_bulk_pur_invoices($data);
                        set_alert('success', _l('updated_successfully', _l('vendor_bills')));
                    } else {
                        $id = $this->expenses_model->add($data);
                        if ($id) {
                            if ($select_invoice == "create_invoice") {
                                $this->purchase_model->mark_converted_pur_invoice($pur_invoice, $id);
                                $invoiceid = $this->expenses_model->convert_to_invoice($id); 
                                set_alert('success', _l('vendor_bills_converted_to_ril_invoices'));
                            } elseif ($select_invoice == "applied_invoice") {
                                $this->purchase_model->mark_converted_pur_invoice($pur_invoice, $id);
                                $applied = array();
                                $applied['invoice_id'] = $applied_to_invoice;
                                $applied['expense_id'] = $id;
                                $invoiceid = $this->expenses_model->applied_to_invoice($applied); 
                                set_alert('success', _l('vendor_bills_converted_to_ril_invoices'));
                            }
                        }
                    }
                }

                
            }
            if ($bulk_active_tab == 'bulk_assign') {
                if (!empty($neworderitems)) {
                    foreach ($neworderitems as $key => $value) {
                        $this->purchase_model->update_vbt_bulk_assign_order($value);
                    }
                }
                set_alert('success', _l('vendor_bills_assign_to_order'));
            }
        }
        redirect(admin_url('purchase/invoices'));
    }

    public function update_order_value_amount()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $orderValueAmount = $this->input->post('orderValueAmount');

        if (!$id || !$table || !$orderValueAmount) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }
        if ($table === 'pur_orders') {
            $tableName = 'tblpur_orders';
        } else {
            $tableName = 'tblwo_orders';
        }
        $this->db->where('id', $id);
        $success = $this->db->update($tableName, ['order_value' => $orderValueAmount]);
        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('change_order_amount_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function get_vendor_detail($vendor_id)
    {
        $vendor_detail = $this->purchase_model->get_vendor_detail($vendor_id);
        echo json_encode($vendor_detail);
    }

    public function payment_certificate($po_id, $payment_certificate_id = '', $view = 0)
    {
        if ($this->input->post()) {
            $pur_cert_data = $this->input->post();
            if ($payment_certificate_id == '') {
                $this->purchase_model->add_payment_certificate($pur_cert_data);
                set_alert('success', _l('added_successfully', _l('payment_certificate')));
                redirect(admin_url('purchase/purchase_order/' . $po_id));
            } else {
                $success = $this->purchase_model->update_payment_certificate($pur_cert_data, $payment_certificate_id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('payment_certificate')));
                }
                redirect(admin_url('purchase/purchase_order/' . $po_id));
            }
        }

        if ($payment_certificate_id == '') {
            $title = _l('create_new_payment_certificate');
            $is_edit = false;
        } else {
            $data['payment_certificate'] = $this->purchase_model->get_payment_certificate($payment_certificate_id);
            $title = _l('pur_cert_detail');
            $data['attachments'] = $this->purchase_model->get_payment_certificate_attachments($payment_certificate_id);
            $is_edit = true;
        }

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['po_id'] = $po_id;
        $data['payment_certificate_id'] = $payment_certificate_id;
        $data['pur_order'] = $this->purchase_model->get_pur_order($po_id);
        $data['title'] = $title;
        $data['is_edit'] = $is_edit;
        $data['is_view'] = $view;
        $data['list_approve_status'] = $this->purchase_model->get_list_pay_cert_approval_details($payment_certificate_id, 'po_payment_certificate');
        $data['check_approve_status'] = $this->purchase_model->check_pay_cert_approval_details($payment_certificate_id, 'po_payment_certificate');
        $data['get_staff_sign'] = $this->purchase_model->get_pay_cert_staff_sign($payment_certificate_id, 'po_payment_certificate');

        $data['activity'] = $this->purchase_model->get_pay_cert_activity($payment_certificate_id);
        $this->load->view('payment_certificate/payment_certificate', $data);
    }

    public function get_po_contract_data($po_id, $payment_certificate_id = '')
    {
        $po_contract_data = $this->purchase_model->get_po_contract_data($po_id, $payment_certificate_id);
        echo json_encode($po_contract_data);
    }

    public function delete_payment_certificate($po_id, $id)
    {
        $response = $this->purchase_model->delete_payment_certificate($id);
        redirect(admin_url('purchase/purchase_order/' . $po_id));
    }

    public function payment_certificate_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_order'));
        }

        $payment_certificate = $this->purchase_model->get_paymentcertificate_pdf_html($id);

        try {
            $pdf = $this->purchase_model->paymentcertificate_pdf($payment_certificate, $id);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf_name = _l('payment_certificate') . '.pdf';
        $pdf->Output($pdf_name, $type);
    }

    public function convert_pur_invoice_from_po($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_order'));
        }
        $payment_certificate = $this->purchase_model->get_payment_certificate($id);
        if (empty($payment_certificate)) {
            redirect(admin_url('purchase/purchase_order'));
        }

        if (!empty($payment_certificate->wo_id)) {
            $pur_order = $this->purchase_model->get_wo_order($payment_certificate->wo_id);
        } else {
            $pur_order = $this->purchase_model->get_pur_order($payment_certificate->po_id);
        }
        $pur_invoice = array();
        $input = array();
        $prefix = get_purchase_option('pur_inv_prefix');
        $next_number = get_purchase_option('next_inv_number');
        $invoice_number = $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);
        $payment_certificate_calc = $this->purchase_model->get_payment_certificate_calc($id);
        $value_certified_amount = $payment_certificate_calc['sub_fg_3'] + $payment_certificate_calc['tot_app_tax_3'];
        if (!empty($payment_certificate->pur_invoice_id)) {
            $pur_invoice = $this->purchase_model->get_pur_invoice($payment_certificate->pur_invoice_id);
        }

        if (empty($pur_invoice)) {
            $input['invoice_number'] = $invoice_number;
            $input['vendor_invoice_number'] = $invoice_number;
            $input['vendor'] = isset($pur_order->vendor) ? $pur_order->vendor : 0;
            $input['group_pur'] = isset($pur_order->group_pur) ? $pur_order->group_pur : 0;
            $input['description_services'] = '';
            $input['invoice_date'] = date('Y-m-d');
            $input['currency'] = 3;
            $input['to_currency'] = 3;
            $input['date_add'] = date('Y-m-d');
            $input['payment_status'] = 0;
            $input['pur_order'] = !empty($payment_certificate->po_id) ? $payment_certificate->po_id : NULL;
            $input['wo_order'] = !empty($payment_certificate->wo_id) ? $payment_certificate->wo_id : NULL;
            $input['project_id'] = isset($pur_order->project) ? $pur_order->project : 1;
            $input['vendor_submitted_amount_without_tax'] = $payment_certificate_calc['sub_fg_3'];
            $input['vendor_submitted_tax_amount'] = $payment_certificate_calc['tot_app_tax_3'];
            $input['vendor_submitted_amount'] = $value_certified_amount;
            $input['final_certified_amount'] = $value_certified_amount;
            $this->db->insert(db_prefix() . 'pur_invoices', $input);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                $this->db->where('option_name', 'next_inv_number');
                $this->db->update(db_prefix() . 'purchase_option', ['option_val' =>  $next_number + 1]);
            }
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'payment_certificate', ['pur_invoice_id' => $insert_id]);
            set_alert('success', _l('purchase_invoice') . ' ' . _l('added_successfully'));
            redirect(admin_url('purchase/pur_invoice/' . $insert_id));
        } else {
            $input['pur_order'] = !empty($payment_certificate->po_id) ? $payment_certificate->po_id : NULL;
            $input['wo_order'] = !empty($payment_certificate->wo_id) ? $payment_certificate->wo_id : NULL;
            $input['vendor_invoice_number'] = $invoice_number;
            $input['vendor'] = isset($pur_order->vendor) ? $pur_order->vendor : 0;
            $input['group_pur'] = isset($pur_order->group_pur) ? $pur_order->group_pur : 0;
            $input['project_id'] = isset($pur_order->project) ? $pur_order->project : 1;
            $input['vendor_submitted_amount_without_tax'] = $payment_certificate_calc['sub_fg_3'];
            $input['vendor_submitted_tax_amount'] = $payment_certificate_calc['tot_app_tax_3'];
            $input['vendor_submitted_amount'] = $value_certified_amount;
            $input['final_certified_amount'] = $value_certified_amount;
            // Update Vendor billing tracker
            $this->db->where('id', $pur_invoice->id);
            $this->db->update(db_prefix() . 'pur_invoices', $input);
            $this->purchase_model->update_vbt_expense_ril_data($pur_invoice->id);
            set_alert('success', _l('purchase_invoice') . ' ' . _l('updated_successfully'));
            redirect(admin_url('purchase/pur_invoice/' . $pur_invoice->id));
        }
    }

    public function send_payment_certificate_approve()
    {
        $data = $this->input->post();
        $message = 'Send request approval fail';
        $success = $this->purchase_model->send_payment_certificate_approve($data);
        if ($success === true) {
            $message = 'Send request approval success';
            $data_new = [];
            $data_new['send_mail_approve'] = $data;
            $this->session->set_userdata($data_new);
        } elseif ($success === false) {
            $message = _l('no_matching_process_found');
            $success = false;
        } else {
            $message = _l('could_not_find_approver_with', _l($success));
            $success = false;
        }
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        die;
    }

    public function payment_certificate_request()
    {
        $data = $this->input->post();
        $data['staff_approve'] = get_staff_user_id();
        $success = false;

        $check_approve_status = $this->purchase_model->check_pay_cert_approval_details($data['rel_id'], $data['rel_type']);
        if (isset($check_approve_status)) {
            if (isset($data['approve']) && in_array(get_staff_user_id(), $check_approve_status['staffid'])) {
                $success = $this->purchase_model->update_pay_cert_approval_details($check_approve_status['id'], $data);
                $message = _l('approved_successfully');

                if ($success) {
                    if ($data['approve'] == 2) {
                        $message = _l('approved_successfully');
                        $check_approve_status = $this->purchase_model->check_pay_cert_approval_details($data['rel_id'], $data['rel_type']);
                        if ($check_approve_status === true) {
                            $this->purchase_model->update_pay_cert_approve_request($data['rel_id'], $data['rel_type'], 2);
                        }
                    } else {
                        $message = _l('rejected_successfully');
                        $this->purchase_model->update_pay_cert_approve_request($data['rel_id'], $data['rel_type'], '3');
                    }
                }
            }
        }

        $data_new = [];
        $data_new['send_mail_approve'] = $data;
        $this->session->set_userdata($data_new);
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        die();
    }

    public function change_status_pay_cert($status, $id)
    {
        $change = $this->purchase_model->change_status_pay_cert($status, $id);
        if ($change == true) {
            $message = _l('payment_certificate') . ' ' . _l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        } else {
            $message = _l('payment_certificate') . ' ' . _l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }

    public function wo_payment_certificate($wo_id, $payment_certificate_id = '', $view = 0)
    {
        if ($this->input->post()) {
            $pur_cert_data = $this->input->post();
            if ($payment_certificate_id == '') {
                $this->purchase_model->add_payment_certificate($pur_cert_data);
                set_alert('success', _l('added_successfully', _l('payment_certificate')));
                redirect(admin_url('purchase/work_order/' . $wo_id));
            } else {
                $success = $this->purchase_model->update_payment_certificate($pur_cert_data, $payment_certificate_id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('payment_certificate')));
                }
                redirect(admin_url('purchase/work_order/' . $wo_id));
            }
        }

        if ($payment_certificate_id == '') {
            $title = _l('create_new_payment_certificate');
            $is_edit = false;
        } else {
            $data['payment_certificate'] = $this->purchase_model->get_payment_certificate($payment_certificate_id);
            $title = _l('pur_cert_detail');
            $data['attachments'] = $this->purchase_model->get_payment_certificate_attachments($payment_certificate_id);
            $is_edit = true;
        }

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['wo_id'] = $wo_id;
        $data['payment_certificate_id'] = $payment_certificate_id;
        $data['wo_order'] = $this->purchase_model->get_wo_order($wo_id);
        $data['title'] = $title;
        $data['is_edit'] = $is_edit;
        $data['is_view'] = $view;
        $data['list_approve_status'] = $this->purchase_model->get_list_pay_cert_approval_details($payment_certificate_id, 'wo_payment_certificate');
        $data['check_approve_status'] = $this->purchase_model->check_pay_cert_approval_details($payment_certificate_id, 'wo_payment_certificate');
        $data['get_staff_sign'] = $this->purchase_model->get_pay_cert_staff_sign($payment_certificate_id, 'wo_payment_certificate');
        $data['activity'] = $this->purchase_model->get_pay_cert_activity($payment_certificate_id);
        $this->load->view('payment_certificate/wo_payment_certificate', $data);
    }

    public function get_wo_contract_data($wo_id, $payment_certificate_id = '')
    {
        $wo_contract_data = $this->purchase_model->get_wo_contract_data($wo_id, $payment_certificate_id);
        echo json_encode($wo_contract_data);
    }

    public function delete_order_tracker($id)
    {

        $response = $this->purchase_model->delete_order_tracker($id);

        if ($response == true) {
            set_alert('success', _l('deleted'));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }
        redirect(admin_url('purchase/order_tracker'));
    }

    public function list_payment_certificate()
    {
        $data['title'] = _l('payment_certificate');
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['item_group'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['projects'] = $this->projects_model->get();
        $this->load->view('payment_certificate/list_payment_certificate', $data);
    }

    public function table_payment_certificate()
    {
        $this->app->get_table_data(module_views_path('purchase', 'payment_certificate/table_payment_certificate'));
    }

    public function purchase_dashboard()
    {
        $data['title'] = _l('dashboard');
        $data['group'] = $this->input->get('group');
        $data['tab'][] = 'purchase_order';
        $data['tab'][] = 'work_order';
        $data['tab'][] = 'payment_certificate';
        $data['tab'][] = 'order_tracker';
        $data['tab'][] = 'purchase_tracker';

        if ($data['group'] == 'work_order') {
            $data['title'] = _l('work_order');
            $data['vendors'] = $this->purchase_model->get_vendor();
            $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        } else if ($data['group'] == 'payment_certificate') {
            $data['title'] = _l('payment_certificate');
        } else if ($data['group'] == 'order_tracker') {
            $data['title'] = _l('order_tracker');
        } else if ($data['group'] == 'purchase_tracker') {
            $data['title'] = _l('purchase_tracker');
        } else {
            $data['title'] = _l('purchase_order');
            $data['group'] = 'purchase_order';
            $data['vendors'] = $this->purchase_model->get_vendor();
            $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        }

        $data['tabs']['view'] = 'purchase_dashboard/module/' . $data['group'];

        $this->load->view('purchase_dashboard/purchase_dashboard', $data);
    }

    public function sales_dashboard()
    {
        $data['title'] = _l('dashboard');
        $data['group'] = $this->input->get('group');
        $data['tab'][] = 'ril_invoices';
        $data['tab'][] = 'vendor_bills';
        $data['tab'][] = 'vendor_payment_tracker';

        if ($data['group'] == 'vendor_bills') {
            $data['title'] = _l('vendor_bills');
        } else if ($data['group'] == 'vendor_payment_tracker') {
            $data['title'] = _l('vendor_payment_tracker');
        } else {
            $data['title'] = _l('ril_invoices');
            $data['group'] = 'ril_invoices';
        }

        $data['tabs']['view'] = 'sales_dashboard/report/' . $data['group'];

        $this->load->view('sales_dashboard/sales_dashboard', $data);
    }

    public function get_purchase_order_dashboard()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_purchase_order_dashboard($data);
        echo json_encode($result);
        die;
    }

    public function get_work_order_dashboard()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_work_order_dashboard($data);
        echo json_encode($result);
        die;
    }

    public function file_purchase_preview($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_purchase_attachments_with_id($id);

        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('purchase_order/_file_new', $data);
    }

    public function file_work_preview($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_work_attachments_with_id($id);

        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('work_order/_file_new', $data);
    }

    public function file_estimate_preview($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_estimate_attachments_with_id($id);

        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('quotations/_file_new', $data);
    }

    public function delete_payment_certificate_files($id)
    {
        $this->purchase_model->delete_payment_certificate_files($id);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function get_pur_order($pur_order)
    {
        $result = $this->purchase_model->get_pur_order($pur_order);
        echo json_encode($result);
        die;
    }

    public function get_wo_order($wo_order)
    {
        $result = $this->purchase_model->get_wo_order($wo_order);
        echo json_encode($result);
        die;
    }

    public function order_tracker_id($order_tracker)
    {
        $result = $this->purchase_model->get_order_tracker($order_tracker);
        echo json_encode($result);
        die;
    }

    public function view_paymentcert_file($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_paymentcert_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('payment_certificate/preview_file', $data);
    }

    public function table_pur_area()
    {
        $this->app->get_table_data(module_views_path('purchase', 'includes/table_pur_area'));
    }

    public function get_project_areas()
    {
        if ($this->input->post()) {
            $project = $this->input->post('project');
            $areas = $this->purchase_model->get_areas_by_project($project);
            echo json_encode($areas);
        }
    }

    public function import_file_xlsx_purchase_area()
    {
        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

            /*delete file old before export file*/
            $path_before = COMMODITY_ERROR_PUR . 'FILE_ERROR_PURCHASE_AREA' . get_staff_user_id() . '.xlsx';
            if (file_exists($path_before)) {
                unlink(COMMODITY_ERROR_PUR . 'FILE_ERROR_PURCHASE_AREA' . get_staff_user_id() . '.xlsx');
            }

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }
                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $writer_header = array(
                            _l('area_name') => 'string',
                            _l('error') => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr]);

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $total_rows = 0;
                        $total_row_false = 0;

                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error = '';

                            $value_area_name = isset($data[$row][0]) ? $data[$row][0] : '';

                            if (empty($value_area_name)) {
                                $string_error .= _l('area_name') . ' ' . _l('does_not_exist');
                                $flag2 = 1;
                            }

                            if (($flag == 1) || $flag2 == 1) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_area_name,
                                    $string_error,
                                ]);
                                $total_row_false++;
                            }

                            if ($flag == 0 && $flag2 == 0) {
                                $rd = array();
                                $rd['area_name'] = $value_area_name;
                                $rd['project'] = $this->input->post('project');
                                $rows[] = $rd;
                                $this->db->insert(db_prefix() . 'area', $rd);
                            }
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        $dataerror = '';
                        $message = 'Not enought rows for importing';

                        if ($total_row_false != 0) {
                            if (!file_exists(FCPATH . PURCHASE_IMPORT_PURCHASE_AREA_ERROR)) {
                                mkdir(FCPATH . PURCHASE_IMPORT_PURCHASE_AREA_ERROR, 0755, true);
                            }
                            $filename = 'Import_item_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_IMPORT_PURCHASE_AREA_ERROR . $filename, $filename));
                        }
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }
        echo json_encode([
            'message'           => $message,
            'total_row_success' => $total_row_success,
            'total_row_false'   => $total_row_false,
            'total_rows'        => $total_rows,
            'site_url'          => site_url(),
            'staff_id'          => get_staff_user_id(),
            'filename'          => PURCHASE_IMPORT_PURCHASE_AREA_ERROR . $filename,

        ]);
    }

    /**
     * import file xlsx vendor payment tracker
     * @return json
     */
    public function import_file_xlsx_vendor_payment_tracker()
    {
        $this->load->model('invoices_model');
        if (!is_admin() && !has_permission('purchase_items', '', 'create')) {
            access_denied(_l('purchase'));
        }

        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(PURCHASE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

            /*delete file old before export file*/
            $path_before = COMMODITY_ERROR_PUR . 'FILE_ERROR_VENDOR_PAYMENT_TRACKER' . get_staff_user_id() . '.xlsx';
            if (file_exists($path_before)) {
                unlink(COMMODITY_ERROR_PUR . 'FILE_ERROR_VENDOR_PAYMENT_TRACKER' . get_staff_user_id() . '.xlsx');
            }

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }
                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $writer_header = array(
                            _l('invoice_code') => 'string',
                            _l('bil_payment_date') => 'string',
                            _l('bil_payment_made') => 'string',
                            _l('bil_tds') => 'string',
                            _l('ril_previous') => 'string',
                            _l('ril_this_bill') => 'string',
                            _l('ril_date') => 'string',
                            _l('remarks') => 'string',
                            _l('error') => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr]);

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $total_rows = 0;
                        $total_row_false = 0;

                        for ($row = 1; $row < count($data); $row++) {
                            $total_rows++;
                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;
                            $string_error = '';
                            $flag_id_invoice_id = '';
                            $pur_invoice_payment_id = '';
                            $ril_invoice_id = '';

                            $value_invoice_code = isset($data[$row][0]) ? $data[$row][0] : '';
                            $value_bil_payment_date = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_bil_payment_made = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_bil_tds = isset($data[$row][3]) ? $data[$row][3] : '';
                            $value_ril_upto_previous = isset($data[$row][4]) ? $data[$row][4] : '';
                            $value_ril_this_bill = isset($data[$row][5]) ? $data[$row][5] : '';
                            $value_ril_payment_date = isset($data[$row][6]) ? $data[$row][6] : '';
                            $value_remarks = isset($data[$row][7]) ? $data[$row][7] : '';

                            if (!empty($value_invoice_code)) {
                                $this->db->like('invoice_number', $value_invoice_code);
                                $result = $this->db->get(db_prefix() . 'pur_invoices')->row();
                                if (empty($result)) {
                                    $string_error .= _l('invoice_code') . ' ' . _l('does_not_exist');
                                    $flag2 = 1;
                                } else {
                                    $flag_id_invoice_id = $result->id;
                                }
                            }

                            if (!empty($value_bil_payment_date) && !empty($flag_id_invoice_id)) {
                                $value_bil_payment_date = date('Y-m-d', strtotime($value_bil_payment_date));
                                $bil_payment_date_array = array();
                                $bil_payment_date_array['id'] = !empty($pur_invoice_payment_id) ? $pur_invoice_payment_id : 0;
                                $bil_payment_date_array['vbt_id'] = $flag_id_invoice_id;
                                $bil_payment_date_array['payment_date'] = $value_bil_payment_date;
                                $pur_invoice_payment_id = $this->purchase_model->update_bil_payment_date($bil_payment_date_array);
                            }

                            if (!empty($value_bil_payment_made) && !empty($flag_id_invoice_id)) {
                                $value_bil_payment_made = str_replace(['₹', ','], '', $value_bil_payment_made);
                                $value_bil_payment_made = (float)$value_bil_payment_made;
                                $bil_payment_made_array = array();
                                $bil_payment_made_array['id'] = !empty($pur_invoice_payment_id) ? $pur_invoice_payment_id : 0;
                                $bil_payment_made_array['vbt_id'] = $flag_id_invoice_id;
                                $bil_payment_made_array['payment_made'] = $value_bil_payment_made;
                                $pur_invoice_payment_id = $this->purchase_model->update_bil_payment_made($bil_payment_made_array);
                                $this->purchase_model->update_final_bil_total($flag_id_invoice_id);
                            }

                            if (!empty($value_bil_tds) && !empty($flag_id_invoice_id)) {
                                $value_bil_tds = str_replace(['₹', ','], '', $value_bil_tds);
                                $value_bil_tds = (float)$value_bil_tds;
                                $bil_tds_array = array();
                                $bil_tds_array['id'] = !empty($pur_invoice_payment_id) ? $pur_invoice_payment_id : 0;
                                $bil_tds_array['vbt_id'] = $flag_id_invoice_id;
                                $bil_tds_array['payment_tds'] = $value_bil_tds;
                                $pur_invoice_payment_id = $this->purchase_model->update_bil_payment_tds($bil_tds_array);
                                $this->purchase_model->update_final_bil_total($flag_id_invoice_id);
                            }

                            if (!empty($flag_id_invoice_id)) {
                                $ril_invoice_item = get_ril_invoice_item($flag_id_invoice_id);
                                if (!empty($ril_invoice_item)) {
                                    $invoice_data = get_invoice_data($ril_invoice_item->rel_id);
                                    if (!empty($invoice_data)) {
                                        // Upto previous (RIL)
                                        if (!empty($value_ril_upto_previous)) {
                                            $value_ril_upto_previous = str_replace(['₹', ','], '', $value_ril_upto_previous);
                                            $value_ril_upto_previous = (float)$value_ril_upto_previous;
                                            $this->purchase_model->update_ril_payment_details($flag_id_invoice_id, 'ril_previous', $value_ril_upto_previous);
                                            $this->purchase_model->update_final_ril_total($flag_id_invoice_id);
                                        }

                                        // This bill (RIL)
                                        if (!empty($value_ril_this_bill)) {
                                            $value_ril_this_bill = str_replace(['₹', ','], '', $value_ril_this_bill);
                                            $value_ril_this_bill = (float)$value_ril_this_bill;
                                            $this->purchase_model->update_ril_payment_details($flag_id_invoice_id, 'amount', $value_ril_this_bill);
                                            $this->purchase_model->update_final_ril_total($flag_id_invoice_id);
                                        }

                                        // RIL Payment date
                                        if (!empty($value_ril_payment_date)) {
                                            $value_ril_payment_date = date('Y-m-d', strtotime($value_ril_payment_date));
                                            $this->purchase_model->update_ril_payment_details($flag_id_invoice_id, 'date', $value_ril_payment_date);
                                        }
                                    }
                                }
                            }

                            if (($flag == 1) || $flag2 == 1) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_invoice_code,
                                    $value_bil_payment_date,
                                    $value_bil_payment_made,
                                    $value_bil_tds,
                                    $value_ril_upto_previous,
                                    $value_ril_this_bill,
                                    $value_ril_payment_date,
                                    $value_remarks,
                                    $string_error,
                                ]);
                                $total_row_false++;
                            }

                            if ($flag == 0 && $flag2 == 0) {
                                $rd = array();
                                if (!empty($flag_id_invoice_id)) {
                                    $rd['payment_remarks'] = !empty($value_remarks) ? $value_remarks : '';
                                    $rows[] = $rd;
                                    $this->db->where('id', $flag_id_invoice_id);
                                    $this->db->update(db_prefix() . 'pur_invoices', $rd);
                                }
                            }
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        $dataerror = '';
                        $message = 'Not enought rows for importing';

                        if ($total_row_false != 0) {
                            if (!file_exists(FCPATH . PURCHASE_IMPORT_VENDOR_PAYMENT_TRACKER_ERROR)) {
                                mkdir(FCPATH . PURCHASE_IMPORT_VENDOR_PAYMENT_TRACKER_ERROR, 0755, true);
                            }
                            $filename = 'Import_item_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_IMPORT_VENDOR_PAYMENT_TRACKER_ERROR . $filename, $filename));
                        }
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }
        echo json_encode([
            'message'           => $message,
            'total_row_success' => $total_row_success,
            'total_row_false'   => $total_row_false,
            'total_rows'        => $total_rows,
            'site_url'          => site_url(),
            'staff_id'          => get_staff_user_id(),
            'filename'          => PURCHASE_IMPORT_VENDOR_PAYMENT_TRACKER_ERROR . $filename,

        ]);
    }

    public function change_vendor()
    {
        $id = $this->input->post('id');
        $vendorId = $this->input->post('vendor'); // Expecting single vendor ID

        // Basic validation
        if (!$id || $vendorId === null) {
            echo json_encode([
                'success' => false,
                'message' => _l('invalid_request')
            ]);
            return;
        }

        // Convert to integer (empty string becomes 0)
        $vendorId = (int)trim($vendorId);

        // Update database with single vendor ID (or empty if 0)
        $this->db->where('id', $id);
        $success = $this->db->update(
            db_prefix() . 'pur_order_tracker',
            ['vendor' => $vendorId ? $vendorId : null] // Store NULL if 0
        );

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => _l('vendor_updated_successfully'),
                'vendor' => $vendorId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => _l('update_failed')
            ]);
        }
    }

    public function order_tracker_pdf()
    {
        $order_tracker = $this->purchase_model->get_order_tracker_pdf_html();

        try {
            $pdf = $this->purchase_model->order_tracker_pdf($order_tracker);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf_name = 'order_tracker.pdf';
        $pdf->Output($pdf_name, $type);
    }


    public function order_tracker_excel()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Order_Tracker_Export.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');
        $get_order_tracker = $this->purchase_model->get_order_tracker_pdf();
        // CSV Headers (same as PDF table columns)
        $headers = [
            'Order Status',
            'Order Scope',
            'Contractor',
            'Order Date',
            'Completion Date',
            'Budget RO Projection',
            'Committed Contract Amount',
            'Change Order Amount',
            'Total Rev Contract Value',
            'Anticipate Variation',
            'Cost to Complete',
            'Final Certified Amount',
            'Project',
            'RLI Filter',
            'Category',
            'Group Pur',
            'Remarks'
        ];

        // Write headers to CSV
        fputcsv($output, $headers);

        // Data rows
        $serial_no = 1;
        foreach ($get_order_tracker as $row) {
            // Format dates (same logic as PDF)
            $completion_date = $aw_unw_order_status = $contract_amount = $order_name = '';
            if (!empty($row['completion_date']) && $row['completion_date'] != '0000-00-00') {
                $completion_date = date('d M, Y', strtotime($row['completion_date']));
            }

            $order_date = '';
            if (!empty($row['order_date']) && $row['order_date'] != '0000-00-00') {
                $order_date = date('d M, Y', strtotime($row['order_date']));
            }
            if (!empty($row['aw_unw_order_status'])) {
                if ($row['aw_unw_order_status'] == 1) {
                    $aw_unw_order_status = _l('Awarded');
                } elseif ($row['aw_unw_order_status'] == 2) {
                    $aw_unw_order_status = _l('Unawarded');
                } elseif ($row['aw_unw_order_status'] == 3) {
                    $aw_unw_order_status = _l('Awarded by RIL');
                }
            }
            $status_labels = [
                0 => ['label' => 'danger', 'table' => 'provided_by_ril', 'text' => _l('provided_by_ril')],
                1 => ['label' => 'success', 'table' => 'new_item_service_been_addded_as_per_instruction', 'text' => _l('new_item_service_been_addded_as_per_instruction')],
                2 => ['label' => 'info', 'table' => 'due_to_spec_change_then_original_cost', 'text' => _l('due_to_spec_change_then_original_cost')],
                3 => ['label' => 'warning', 'table' => 'deal_slip', 'text' => _l('deal_slip')],
                4 => ['label' => 'primary', 'table' => 'to_be_provided_by_ril_but_managed_by_bil', 'text' => _l('to_be_provided_by_ril_but_managed_by_bil')],
                5 => ['label' => 'secondary', 'table' => 'due_to_additional_item_as_per_apex_instrution', 'text' => _l('due_to_additional_item_as_per_apex_instrution')],
                6 => ['label' => 'purple', 'table' => 'event_expense', 'text' => _l('event_expense')],
                7 => ['label' => 'teal', 'table' => 'pending_procurements', 'text' => _l('pending_procurements')],
                8 => ['label' => 'orange', 'table' => 'common_services_in_ghj_scope', 'text' => _l('common_services_in_ghj_scope')],
                9 => ['label' => 'green', 'table' => 'common_services_in_ril_scope', 'text' => _l('common_services_in_ril_scope')],
                10 => ['label' => 'default', 'table' => 'due_to_site_specfic_constraint', 'text' => _l('due_to_site_specfic_constraint')],
            ];
            if ($row['source_table'] == "order_tracker") {
                $contract_amount =  app_format_money($row['total'] ?? 0, '');
                $order_name = $row['order_name'] ?? '';
            } else {
                $contract_amount = app_format_money($row['subtotal'] ?? 0, '');
                $order_name = $row['order_number'] . '-' . $row['order_name'] ?? '';
            }
            // Write row data
            fputcsv($output, [
                $aw_unw_order_status,
                $order_name,
                $row['vendor'] ?? '',
                $order_date,
                $completion_date,
                app_format_money($row['budget'] ?? 0, ''),
                $contract_amount,
                app_format_money($row['co_total'] ?? 0, ''),
                app_format_money($row['total_rev_contract_value'] ?? 0, ''),
                app_format_money($row['anticipate_variation'] ?? 0, ''),
                app_format_money($row['cost_to_complete'] ?? 0, ''),
                app_format_money($row['final_certified_amount'] ?? 0, ''),
                $row['project'] ?? '',
                $status_labels[$row['rli_filter']]['text'] ?? '',
                $row['kind'] ?? '',
                get_group_name_by_id($row['group_pur']) ?? '',
                $row['remarks'] ?? ''
            ]);
        }

        // Close output stream
        fclose($output);
        exit;
    }

    public function get_cost_control_sheet()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_cost_control_sheet($data);
        echo json_encode(['result' => $result]);
        exit;
    }

    public function download_revision_historical_data()
    {
        $estimate_id = $this->input->get('estimate_id');
        $budget_head_id = $this->input->get('budget_head_id');
        $this->purchase_model->download_revision_historical_data($estimate_id, $budget_head_id);
    }
    public function update_budget_head($budget_head_id, $id, $table_name)
    {
        // 1) Get all budget heads
        $budget_heads = get_group_name_item();

        // 2) Define your 11-item label palette
        $label_palette = [
            'danger',
            'success',
            'info',
            'warning',
            'primary',
            'secondary',
            'purple',
            'teal',
            'orange',
            'green',
            'default',
        ];

        // 3) Build a map id => label, cycling through the palette
        $status_labels = [];
        $i = 0;
        foreach ($budget_heads as $h) {
            $status_labels[$h['id']] = $label_palette[$i % count($label_palette)];
            $i++;
        }

        // 4) Lookup the chosen head’s name
        $current_budget_head_name = '';
        foreach ($budget_heads as $h) {
            if ($h['id'] == $budget_head_id) {
                $current_budget_head_name = $h['name'];
                break;
            }
        }

        // 5) Do the DB update
        $success = $this->purchase_model->update_budget_head($budget_head_id, $id, $table_name);
        $message = $success ? _l('update_budget_head_successfully') : _l('update_budget_head_fail');

        // 6) Determine this head’s label‐class
        $label_key = isset($status_labels[$budget_head_id]) ? $status_labels[$budget_head_id] : 'default';
        $class = 'label label-' . $label_key;

        // 7) Build the replacement <span> with dropdown
        $span = '<span>'
            .  $current_budget_head_name;

        if (has_permission('order_tracker', '', 'edit') || is_admin()) {
            $span .= '<div class="dropdown inline-block mleft5 table-export-exclude">'
                .  '<a href="#" class="dropdown-toggle text-dark" '
                .     'id="tableBudgetHead-' . $id . '" data-toggle="dropdown">'
                .     '<span data-toggle="tooltip" title="' . _l('change_budget_head') . '">'
                .         '<i class="fa fa-caret-down"></i>'
                .     '</span>'
                .  '</a>'
                .  '<ul class="dropdown-menu dropdown-menu-right" '
                .      'aria-labelledby="tableBudgetHead-' . $id . '">';
            foreach ($budget_heads as $h) {
                if ($h['id'] != $budget_head_id) {
                    $other_label = isset($status_labels[$h['id']])
                        ? $status_labels[$h['id']]
                        : 'default';
                    $span .= '<li>'
                        .   '<a href="javascript:void(0);" '
                        .      'onclick="update_budget_head('
                        .         $h['id'] . ',' . $id . ',\'' .
                        htmlspecialchars($table_name, ENT_QUOTES) .
                        '\');return false;">'
                        .          $h['name']
                        .   '</a>'
                        . '</li>';
                }
            }
            $span .=   '</ul>'
                .   '</div>';
        }

        $span .= '</span>';

        // 8) Return JSON
        echo json_encode([
            'success'    => $success,
            'status_str' => $span,
            'class'      => $class,
            'mess'       => $message,

        ]);
    }

    public function upload_order_tracker_attachments()
    {
        $input = $this->input->post();
        $uploaded_files = $this->purchase_model->upload_order_tracker_attachments($input);
        echo json_encode(['status' => !empty($uploaded_files)]);
        die();
    }

    public function view_order_tracker_attachments()
    {
        $input = $this->input->post();
        $attachments = $this->purchase_model->view_order_tracker_attachments($input);
        echo json_encode(['result' => $attachments]);
        die();
    }

    public function delete_order_tracker_attachment($id)
    {
        $file = $this->purchase_model->get_order_tracker_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_order_tracker_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    public function view_order_tracker_file($id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_order_tracker_file($id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('order_tracker/preview_order_tracker_file', $data);
    }

    public function bulk_assign_ril_bill()
    {
        $response = array();
        $data = $this->input->post();
        $bulk_html = $this->purchase_model->bulk_assign_ril_bill($data);
        echo json_encode(['success' => true, 'bulk_html' => $bulk_html]);
    }

    public function po_charts()
    {
        // 1) Fetch PO rows and compute counts
        $po_data = get_all_po_data();
        $approved_count = 0;
        $draft_count    = 0;
        $rejected_count = 0;
        foreach ($po_data as $row) {
            switch (strtolower($row['approve_status'])) {
                case '2':
                    $approved_count++;
                    break;
                case '1':
                    $draft_count++;
                    break;
                case '3':
                    $rejected_count++;
                    break;
            }
        }

        // 2) BAR CHART: Budget Head vs Actual PO Value
        $budget_head_data_all = get_budget_head_and_total_tax();
        $budget_head_labels   = array_column($budget_head_data_all, 'budget_head');
        $budget_head_data     = array_column($budget_head_data_all, 'total');

        $bar_chart = [
            'type' => 'bar',
            'data' => [
                'labels'   => $budget_head_labels,
                'datasets' => [[
                    'label'           => _l('PO Value'),
                    'data'            => $budget_head_data,
                    'borderColor'     => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ]]
            ],
            'options' => [
                'indexAxis' => 'y',
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text'    => _l('Budget Head vs Actual PO Value'),
                        'font'    => ['size' => 16]
                    ],
                    'legend' => ['display' => false]
                    // Removed tooltip callback strings here
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true
                        // Removed ticks.callback string here
                    ],
                    'y' => [
                        'grid' => ['display' => false]
                    ],
                    'x' => [
                        'grid' => ['display' => false]
                    ]
                ]
            ]
        ];
        $budget_sub_head_data_all = get_budget_sub_head_and_total();
        $budget_sub_head_labels   = array_column($budget_sub_head_data_all, 'budget_sub_head');
        $budget_sub_head_data     = array_column($budget_sub_head_data_all, 'total');
        $stacked_bar_chart = [
            'type' => 'bar',
            'data' => [
                'labels'   => $budget_sub_head_labels,
                'datasets' => [[
                    'label'           => _l('Value'),
                    'data'            => $budget_sub_head_data,
                    'borderColor'     => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ]]
            ],
            'options' => [
                'indexAxis' => 'x',
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text'    => _l('Budget Sub Head vs Actual Order Value'),
                        'font'    => ['size' => 16]
                    ],
                    'legend' => ['display' => false]
                    // Removed tooltip callback strings here
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true,
                        // Removed ticks.callback string here

                    ],
                    'y' => [

                        'grid' => ['display' => false]
                    ],
                    'x' => [
                        'grid' => ['display' => false]
                    ]
                ]
            ]
        ];

        $vendor_bar_data_all = get_vendor_po_volume();
        $vendor_bar_labels   = array_column($vendor_bar_data_all, 'vendor_name');
        $vendor_bar_data     = array_column($vendor_bar_data_all, 'total');
        $vendor_bar_chart = [
            'type' => 'bar',
            'data' => [
                'labels'   => $vendor_bar_labels,
                'datasets' => [[
                    'label'           => _l('Value'),
                    'data'            => $vendor_bar_data,
                    'borderColor'     => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ]]
            ],
            'options' => [
                'indexAxis' => 'y',
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text'    => _l('Vendor-Wise Total Spend'),
                        'font'    => ['size' => 16]
                    ],
                    'legend' => ['display' => false]
                    // Removed tooltip callback strings here
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true,
                        // Removed ticks.callback string here

                    ],
                    'y' => [

                        'grid' => ['display' => false]
                    ],
                    'x' => [
                        'grid' => ['display' => false]
                    ]
                ]
            ]
        ];

        $department_bar_data_all = get_department_and_total_tax();
        $department_bar_labels   = array_column($department_bar_data_all, 'department_name');
        $department_bar_data     = array_column($department_bar_data_all, 'total');
        $department_bar_chart = [
            'type' => 'bar',
            'data' => [
                'labels'   => $department_bar_labels,
                'datasets' => [[
                    'label'           => _l('Value'),
                    'data'            => $department_bar_data,
                    'borderColor'     => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ]]
            ],
            'options' => [
                'indexAxis' => 'x',
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text'    => _l('Departmental Spend Distribution'),
                        'font'    => ['size' => 16]
                    ],
                    'legend' => ['display' => false]
                    // Removed tooltip callback strings here
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true,
                        // Removed ticks.callback string here

                    ],
                    'y' => [

                        'grid' => ['display' => false]
                    ],
                    'x' => [
                        'grid' => ['display' => false]
                    ]
                ]
            ]
        ];

        $expensive_item_bar_data_all = get_expensive_item_wise();
        $expensive_item_bar_labels   = array_column($expensive_item_bar_data_all, 'item_name');
        $expensive_item_bar_data     = array_column($expensive_item_bar_data_all, 'total_cost');
        $expensive_item_bar_chart = [
            'type' => 'bar',
            'data' => [
                'labels'   => $expensive_item_bar_labels,
                'datasets' => [[
                    'label'           => _l('PO Value'),
                    'data'            => $expensive_item_bar_data,
                    'borderColor'     => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ]]
            ],
            'options' => [
                'indexAxis' => 'y',
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text'    => _l('Departmental Spend Distribution'),
                        'font'    => ['size' => 16]
                    ],
                    'legend' => ['display' => false]
                    // Removed tooltip callback strings here
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true,
                        // Removed ticks.callback string here

                    ],
                    'y' => [

                        'grid' => ['display' => false]
                    ],
                    'x' => [
                        'grid' => ['display' => false]
                    ]
                ]
            ]
        ];



        // 3) PIE CHART: PO Approval Status
        $pie_chart = [
            'type' => 'pie',
            'data' => [
                'labels'   => [_l('Approved'), _l('Draft'), _l('Rejected')],
                'datasets' => [[
                    'data'            => [$approved_count, $draft_count, $rejected_count],
                    'backgroundColor' => [
                        'rgba(22, 163, 74, 1)',
                        'rgba(37, 99, 235, 1)',
                        'rgba(202, 138, 4, 1)'
                    ],
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text'    => _l('PO Approval Status'),
                        'font'    => ['size' => 16]
                    ],
                    'legend' => ['position' => 'right']
                    // Removed tooltip callback string here
                ]
            ]
        ];
        $get_item_wise_cost_summary = get_item_wise_cost_summary();
        $tree = [];
        foreach ($get_item_wise_cost_summary as $item) {
            $tree[] = [
                'item_name' => $item['item_name'],
                'total_value'  => round($item['total_cost']),
            ];
        }

        $tree_map_chart = [
            'type' => 'treemap',
            'data' => [
                'datasets' => [[
                    'label'             => 'Total Value',
                    'tree'              => $tree,
                    'key'               => 'total_value',
                    'groups'            => ['item_name'],
                    'spacing'           => 0.5,
                    'borderWidth'       => 1,

                ]]
            ],
            'options' => [
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text'    => 'Item-Wise Cost Summary',
                        'font'    => ['size' => 16]
                    ],
                    'legend' => [
                        'display' => false
                    ]
                ]
            ]
        ];


        // 5) Pass all three charts to the view
        $data['charts']      = [$bar_chart, $pie_chart, $stacked_bar_chart, $vendor_bar_chart, $department_bar_chart, $expensive_item_bar_chart];
        $data['col_classes'] = ['col-md-9', 'col-md-3', 'col-md-9', 'col-md-9', 'col-md-9', 'col-md-9'];

        $this->load->view('admin/chartjs_common_view', $data);
    }

    public function table_unawarded_tracker($estimate_id = 0)
    {
        $this->app->get_table_data(module_views_path('purchase', 'unawarded_tracker/table_unawarded_tracker'), ['estimate_id' => $estimate_id]);
    }


    public function unawarded_tracker()
    {
        $data['title'] = _l('unawarded_tracker');
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['commodity_groups_pur'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['projects'] = $this->projects_model->get();
        $data['order_tracker_row_template'] = $this->purchase_model->create_unawarded_tracker_row_template();
        $data['budget_head'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['rli_filters'] = $this->purchase_model->get_all_rli_filters();

        $this->load->view('unawarded_tracker/manage', $data);
    }

    public function import_file_xlsx_unawared_tracker_items()
    {

        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(WAREHOUSE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename = '';

        if ($this->input->post()) {

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                //do_action('before_import_leads');

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $import_result = true;
                        $rows = [];

                        //Writer file
                        $writer_header = array(
                            "(*)" . _l('order_scope') => 'string',
                            _l('order_date') => 'date',
                            _l('completion_date')    => 'date',
                            _l('budget_ro_projection')    => 'numeric',
                            _l('remarks')    => 'string',
                        );

                        $widths_arr = array();
                        for ($i = 1; $i <= count($writer_header); $i++) {
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();

                        $col_style1 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
                        $style1 = ['widths' => $widths_arr, 'fill' => '#ff9800',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

                        $writer->writeSheetHeader_v2('Sheet1', $writer_header,  $col_options = ['widths' => $widths_arr, 'fill' => '#f44336',  'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13], $col_style1, $style1);

                        //init file error end

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);


                        // start row write 2
                        $numRow = 2;
                        $total_rows = 0;
                        $total_rows_actualy = 0;
                        $list_item = $this->purchase_model->create_unawarded_tracker_row_template();
                        //get data for compare
                        $index_quote = 0;
                        for ($row = 1; $row < count($data); $row++) {

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error = '';
                            $flag_contract_form = 0;

                            $flag_id_commodity_code;
                            $flag_id_order_scope;


                            $value_cell_order_scope = isset($data[$row][0]) ? $data[$row][0] : null;
                            $value_cell_order_date = isset($data[$row][1]) ? $data[$row][1] : '';
                            $value_cell_completion_date = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_cell_budget = isset($data[$row][3]) ? $data[$row][3] : '';
                            $value_cell_total_remaks = isset($data[$row][4]) ? $data[$row][4] : '';


                            /*check null*/
                            if (is_null($value_cell_order_scope) == true) {
                                $string_error .= _l('order_scope') . ' ' . _l('not_yet_entered');
                                $flag = 1;
                            }
                            $value_cell_budget = trim($value_cell_budget, " ");
                            if ($value_cell_budget !== "" && !is_numeric($value_cell_budget)) {
                                $string_error .= _l('budget_ro_projection') . ' ' . _l('_not_a_number');
                                $flag2 = 1;
                            }


                            if (($flag == 1) || ($flag2 == 1)) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_cell_order_scope,
                                    $value_cell_order_date,
                                    $value_cell_completion_date,
                                    $value_cell_budget,
                                    $value_cell_total_remaks,
                                    $string_error,
                                ]);

                                $numRow++;
                                $total_rows_data_error++;
                                $message = 'Import Error In Some Item';
                            }
                            if (($flag == 0) && ($flag2 == 0)) {

                                $rows[] = $row;
                                $list_item .= $this->purchase_model->create_unawarded_tracker_row_template('newitems[' . $index_quote . ']', $value_cell_order_scope, '', $value_cell_order_date, $value_cell_completion_date, $value_cell_budget, '', '', '', '', '', '', $value_cell_total_remaks, '', '');
                                $index_quote++;
                                $total_rows_data++;
                                $message = 'Data Import successfully';
                            }
                        }

                        $total_rows = $total_rows;
                        $data['total_rows_post'] = count($rows);
                        $total_row_success = count($rows);
                        $total_row_false = $total_rows - (int) count($rows);

                        if (($total_rows_data_error > 0) || ($total_row_false > 0)) {

                            $filename = 'FILE_ERROR_IMPORT_ORDER_TRACKER' . get_staff_user_id() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_ORDER_IMPORT_ORDER_TRACKER_ERROR . $filename, $filename));

                            $filename = PURCHASE_ORDER_IMPORT_ORDER_TRACKER_ERROR . $filename;
                        }
                        $list_item = $list_item;

                        @delete_dir($tmpDir);
                    }
                } else {
                    set_alert('warning', 'Import Item failed');
                }
            }
        }

        echo json_encode([
            'message' => $message,
            'total_row_success' => $total_row_success,
            'total_row_false' => $total_rows_data_error,
            'total_rows' => $total_rows_data,
            'site_url' => site_url(),
            'staff_id' => get_staff_user_id(),
            'total_rows_data_error' => $total_rows_data_error,
            'filename' => $filename,
            'list_item' => $list_item
        ]);
    }

    public function add_unawarded_order()
    {
        $data = $this->input->post();

        if ($data) {
            $id = $this->purchase_model->add_unawarded_order_tracker($data);
            if ($id) {
                // Set a flash message if needed
                // set_alert('success', _l('added_successfully', _l('order_tracker')));

                // Return a JSON success response
                echo json_encode([
                    'success' => true,
                    'row_template' => $this->purchase_model->create_unawarded_tracker_row_template(),
                    'message' => _l('added_successfully', _l('order_tracker'))
                ]);
            } else {
                // Return a JSON error response if the insert failed
                echo json_encode([
                    'success' => false,
                    'message' => _l('problem_adding_order')
                ]);
            }
        } else {
            // Return a JSON error response if no data was posted
            echo json_encode([
                'success' => false,
                'message' => _l('no_data_received')
            ]);
        }
        exit; // Stop further execution
    }
    public function unawarded_tracker_pdf()
    {
        $unawarded_tracker = $this->purchase_model->get_unawarded_tracker_pdf_html();
        try {
            $pdf = $this->purchase_model->unawarded_tracker_pdf($unawarded_tracker);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf_name = 'unwarded_tracker.pdf';
        $pdf->Output($pdf_name, $type);
    }

    public function unawarded_tracker_excel()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Unwarded_Tracker_Export.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');
        $get_order_tracker = $this->purchase_model->get_unawarded_tracker_pdf();
        // CSV Headers (same as PDF table columns)
        $headers = [
            'Order Scope',
            'Order Date',
            'Completion Date',
            'Budget RO Projection',
            'Project',
            'RLI Filter',
            'Category',
            'Group Pur',
            'Remarks'
        ];

        // Write headers to CSV
        fputcsv($output, $headers);

        // Data rows
        $serial_no = 1;
        foreach ($get_order_tracker as $row) {
            // Format dates (same logic as PDF)
            $completion_date = $aw_unw_order_status = $contract_amount = $order_name = '';
            if (!empty($row['completion_date']) && $row['completion_date'] != '0000-00-00') {
                $completion_date = date('d M, Y', strtotime($row['completion_date']));
            }

            $order_date = '';
            if (!empty($row['order_date']) && $row['order_date'] != '0000-00-00') {
                $order_date = date('d M, Y', strtotime($row['order_date']));
            }

            $status_labels = [
                0 => ['label' => 'danger', 'table' => 'provided_by_ril', 'text' => _l('provided_by_ril')],
                1 => ['label' => 'success', 'table' => 'new_item_service_been_addded_as_per_instruction', 'text' => _l('new_item_service_been_addded_as_per_instruction')],
                2 => ['label' => 'info', 'table' => 'due_to_spec_change_then_original_cost', 'text' => _l('due_to_spec_change_then_original_cost')],
                3 => ['label' => 'warning', 'table' => 'deal_slip', 'text' => _l('deal_slip')],
                4 => ['label' => 'primary', 'table' => 'to_be_provided_by_ril_but_managed_by_bil', 'text' => _l('to_be_provided_by_ril_but_managed_by_bil')],
                5 => ['label' => 'secondary', 'table' => 'due_to_additional_item_as_per_apex_instrution', 'text' => _l('due_to_additional_item_as_per_apex_instrution')],
                6 => ['label' => 'purple', 'table' => 'event_expense', 'text' => _l('event_expense')],
                7 => ['label' => 'teal', 'table' => 'pending_procurements', 'text' => _l('pending_procurements')],
                8 => ['label' => 'orange', 'table' => 'common_services_in_ghj_scope', 'text' => _l('common_services_in_ghj_scope')],
                9 => ['label' => 'green', 'table' => 'common_services_in_ril_scope', 'text' => _l('common_services_in_ril_scope')],
                10 => ['label' => 'default', 'table' => 'due_to_site_specfic_constraint', 'text' => _l('due_to_site_specfic_constraint')],
            ];
            if ($row['source_table'] == "order_tracker") {
                $contract_amount =  app_format_money($row['total'] ?? 0, '');
                $order_name = $row['order_name'] ?? '';
            } else {
                $contract_amount = app_format_money($row['subtotal'] ?? 0, '');
                $order_name = $row['order_number'] . '-' . $row['order_name'] ?? '';
            }
            // Write row data
            fputcsv($output, [
                $order_name,
                $order_date,
                $completion_date,
                app_format_money($row['budget'] ?? 0, ''),
                $row['project'] ?? '',
                $status_labels[$row['rli_filter']]['text'] ?? '',
                $row['kind'] ?? '',
                get_group_name_by_id($row['group_pur']) ?? '',
                $row['remarks'] ?? ''
            ]);
        }

        // Close output stream
        fclose($output);
        exit;
    }
    public function update_unawarded_date()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $order_date = $this->input->post('orderDate');

        if (!$id || !$table || !$order_date) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_unawarded_tracker', ['order_date' => $order_date]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('order_date_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }
    public function update_unawarded_completion_date()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $completion_date = $this->input->post('completion_date');

        if (!$id || !$table || !$completion_date) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_unawarded_tracker', ['completion_date' => $completion_date]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('completion_date_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function update_unawarded_budget()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $budget = $this->input->post('budget');

        if (!$id || !$table || !$budget) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        // Perform the update
        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_unawarded_tracker', ['budget' => $budget]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('amount_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function change_rli_filter_unawarded($status, $id, $table_name)
    {

        // Define an array of statuses with their corresponding labels and texts
        $status_labels = [
            0 => ['label' => 'label-danger', 'table' => 'provided_by_ril', 'text' => _l('provided_by_ril')],
            1 => ['label' => 'label-success', 'table' => 'new_item_service_been_addded_as_per_instruction', 'text' => _l('new_item_service_been_addded_as_per_instruction')],
            2 => ['label' => 'label-info', 'table' => 'due_to_spec_change_then_original_cost', 'text' => _l('due_to_spec_change_then_original_cost')],
            3 => ['label' => 'label-warning', 'table' => 'deal_slip', 'text' => _l('deal_slip')],
            4 => ['label' => 'label-primary', 'table' => 'to_be_provided_by_ril_but_managed_by_bil', 'text' => _l('to_be_provided_by_ril_but_managed_by_bil')],
            5 => ['label' => 'label-secondary', 'table' => 'due_to_additional_item_as_per_apex_instrution', 'text' => _l('due_to_additional_item_as_per_apex_instrution')],
            6 => ['label' => 'label-purple', 'table' => 'event_expense', 'text' => _l('event_expense')],
            7 => ['label' => 'label-teal', 'table' => 'pending_procurements', 'text' => _l('pending_procurements')],
            8 => ['label' => 'label-orange', 'table' => 'common_services_in_ghj_scope', 'text' => _l('common_services_in_ghj_scope')],
            9 => ['label' => 'label-green', 'table' => 'common_services_in_ghj_scope', 'text' => _l('common_services_in_ril_scope')],
            10 => ['label' => 'label-default', 'table' => 'due_to_site_specfic_constraint', 'text' => _l('due_to_site_specfic_constraint')],
        ];
        $success = $this->purchase_model->change_rli_filter_unawarded($status, $id, $table_name);
        $message = $success ? _l('change_rli_filter_successfully') : _l('change_rli_filter_fail');

        $html = '';
        $status_str = $status_labels[$status]['text'] ?? '';
        $class = $status_labels[$status]['label'] ?? '';

        if (has_permission('order_tracker', '', 'edit') || is_admin()) {
            $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $html .= '</a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $id . '">';

            // Generate the dropdown menu options dynamically
            foreach ($status_labels as $key => $label) {
                if ($key != $status) {
                    $html .= '<li>
                    <a href="#" onclick="change_rli_filter_unawarded(' . $key . ', ' . $id . ', \'' . htmlspecialchars($table_name, ENT_QUOTES) . '\'); return false;">
                        ' . $label['text'] . '
                    </a>
                </li>';
                }
            }

            $html .= '</ul>';
            $html .= '</div>';
        }

        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }
    public function update_budget_head_unawarded($budget_head_id, $id, $table_name)
    {
        // 1) Get all budget heads
        $budget_heads = get_group_name_item();

        // 2) Define your 11-item label palette
        $label_palette = [
            'danger',
            'success',
            'info',
            'warning',
            'primary',
            'secondary',
            'purple',
            'teal',
            'orange',
            'green',
            'default',
        ];

        // 3) Build a map id => label, cycling through the palette
        $status_labels = [];
        $i = 0;
        foreach ($budget_heads as $h) {
            $status_labels[$h['id']] = $label_palette[$i % count($label_palette)];
            $i++;
        }

        // 4) Lookup the chosen head’s name
        $current_budget_head_name = '';
        foreach ($budget_heads as $h) {
            if ($h['id'] == $budget_head_id) {
                $current_budget_head_name = $h['name'];
                break;
            }
        }

        // 5) Do the DB update
        $success = $this->purchase_model->update_budget_head_unawarded($budget_head_id, $id, $table_name);
        $message = $success ? _l('update_budget_head_successfully') : _l('update_budget_head_fail');

        // 6) Determine this head’s label‐class
        $label_key = isset($status_labels[$budget_head_id]) ? $status_labels[$budget_head_id] : 'default';
        $class = 'label label-' . $label_key;

        // 7) Build the replacement <span> with dropdown
        $span = '<span>'
            .  $current_budget_head_name;

        if (has_permission('order_tracker', '', 'edit') || is_admin()) {
            $span .= '<div class="dropdown inline-block mleft5 table-export-exclude">'
                .  '<a href="#" class="dropdown-toggle text-dark" '
                .     'id="tableBudgetHead-' . $id . '" data-toggle="dropdown">'
                .     '<span data-toggle="tooltip" title="' . _l('change_budget_head') . '">'
                .         '<i class="fa fa-caret-down"></i>'
                .     '</span>'
                .  '</a>'
                .  '<ul class="dropdown-menu dropdown-menu-right" '
                .      'aria-labelledby="tableBudgetHead-' . $id . '">';
            foreach ($budget_heads as $h) {
                if ($h['id'] != $budget_head_id) {
                    $other_label = isset($status_labels[$h['id']])
                        ? $status_labels[$h['id']]
                        : 'default';
                    $span .= '<li>'
                        .   '<a href="javascript:void(0);" '
                        .      'onclick="update_budget_head_unawarded('
                        .         $h['id'] . ',' . $id . ',\'' .
                        htmlspecialchars($table_name, ENT_QUOTES) .
                        '\');return false;">'
                        .          $h['name']
                        .   '</a>'
                        . '</li>';
                }
            }
            $span .=   '</ul>'
                .   '</div>';
        }

        $span .= '</span>';

        // 8) Return JSON
        echo json_encode([
            'success'    => $success,
            'status_str' => $span,
            'class'      => $class,
            'mess'       => $message,

        ]);
    }

    public function update_unawarded_remarks()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $remarks = $this->input->post('remarks');

        if (!$id || !$table) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }

        $this->db->where('id', $id);
        $success = $this->db->update('tblpur_unawarded_tracker', ['remarks' => $remarks]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('remarks_updated')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('update_failed')]);
        }
    }

    public function pur_bills($id = '')
    {
        $data['title'] = _l('add_bill');

        $type = $this->input->get('type');


        if ($type === 'po') {
            $pur_order = $this->purchase_model->get_pur_order($id);
            $data['po_id'] = $id;
            $data['vendor_id'] = $pur_order->vendor;
            $data['project_id'] = $pur_order->project;
            $currency = $pur_order->currency;
            $currency_rate = $pur_order->currency_rate;
            $to_currency = $pur_order->to_currency;
        }

        $data['contracts'] = $this->purchase_model->get_contract();
        $data['taxes'] = $this->purchase_model->get_taxes();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['projects'] = $this->projects_model->get_items();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $pur_bill_row_template = '';

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        if ($id != '') {
            $data['pur_orders'] = $this->purchase_model->get_pur_order_approved($id);
            $data['pur_invoice'] = $this->purchase_model->get_pur_invoice($id);
            $data['pur_invoice_detail'] = $this->purchase_model->get_pur_invoice_detail($id);
            $data['pur_order_detail'] = $this->purchase_model->get_pur_order_detail($id);
            $currency_rate = 1;
            if ($currency != 0 && $currency_rate != null) {
                $currency_rate = $currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if ($currency != 0 && $to_currency != null) {
                $to_currency = $to_currency;
            }

            if (count($data['pur_order_detail']) > 0) {
                $index_order = 0;
                foreach ($data['pur_order_detail'] as $inv_detail) {
                    $index_order++;
                    $unit_name = pur_get_unit_name($inv_detail['unit_id']);
                    $taxname = $inv_detail['tax_name'];
                    // $item_name = $inv_detail['item_name'];

                    // if (strlen($item_name) == 0) {
                    $item_name = pur_get_item_variatiom($inv_detail['item_code']);
                    // }

                    $pur_bill_row_template .= $this->purchase_model->create_purchase_bill_row_template('newitems[' . $index_order . ']',  $item_name, $inv_detail['description'], $inv_detail['quantity'], $unit_name, $inv_detail['unit_price'], $taxname, $inv_detail['item_code'], $inv_detail['unit_id'], $inv_detail['tax_rate'],  $inv_detail['total_money'], $inv_detail['discount_percent'], $inv_detail['discount_money'], $inv_detail['total'], $inv_detail['into_money'], $inv_detail['tax'], $inv_detail['tax_value'], $inv_detail['id'], true, $currency_rate, $to_currency, '');
                }
            }
        } else {
            $data['pur_orders'] = $this->purchase_model->get_pur_order_approved_for_inv();
        }

        $data['pur_bill_row_template'] = $pur_bill_row_template;

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $this->load->view('purchase_order/pur_bills', $data);
    }


    public function edit_pur_bills($id = '')
    {
        $data['title'] = _l('edit_bill');

        $type = $this->input->get('type');

        $data['contracts'] = $this->purchase_model->get_contract();
        $data['taxes'] = $this->purchase_model->get_taxes();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['projects'] = $this->projects_model->get_items();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $pur_bill_row_template = '';

        $data['base_currency'] = $this->currencies_model->get_base_currency();


        $data['pur_orders'] = $this->purchase_model->get_pur_order_approved($id);
        $data['pur_bill'] = $this->purchase_model->get_pur_bill($id);
        $data['pur_bill_detail'] = $this->purchase_model->get_pur_bill_detail($id);
        $currency_rate = 1;
        if ($data['pur_bill']->currency != 0 && $data['pur_bill']->currency_rate != null) {
            $currency_rate = $data['pur_bill']->currency_rate;
        }

        $to_currency = $data['base_currency']->name;
        if ($data['pur_bill']->currency != 0 && $data['pur_bill']->to_currency != null) {
            $to_currency = $data['pur_bill']->to_currency;
        }

        if (count($data['pur_bill_detail']) > 0) {
            $index_order = 0;
            foreach ($data['pur_bill_detail'] as $bill_detail) {
                $index_order++;
                $unit_name = pur_get_unit_name($bill_detail['unit_id']);
                $taxname = $bill_detail['tax_name'];
                $item_name = $bill_detail['item_name'];

                if (strlen($item_name) == 0) {
                    $item_name = pur_get_item_variatiom($bill_detail['item_code']);
                }

                $pur_bill_row_template .= $this->purchase_model->create_purchase_bill_row_template('items[' . $index_order . ']',  $item_name, $bill_detail['description'], $bill_detail['quantity'], $unit_name, $bill_detail['unit_price'], $taxname, $bill_detail['item_code'], $bill_detail['unit_id'], $bill_detail['tax_rate'],  $bill_detail['total_money'], $bill_detail['discount_percent'], $bill_detail['discount_money'], $bill_detail['total'], $bill_detail['into_money'], $bill_detail['tax'], $bill_detail['tax_value'], $bill_detail['id'], true, $currency_rate, $to_currency, $bill_detail['billed_quantity']);
            }
        }


        $data['pur_bill_row_template'] = $pur_bill_row_template;

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $this->load->view('purchase_order/edit_pur_bills', $data);
    }
    public function pur_bill_form()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                unset($data['id']);

                $mess = $this->purchase_model->add_pur_bill($data);
                if ($mess) {
                    // handle_pur_invoice_file($mess);
                    set_alert('success', _l('added_successfully') . ' ' . _l('purchase_bill'));
                } else {
                    set_alert('warning', _l('add_purchase_bill_fail'));
                }
                redirect(admin_url('purchase/purchase_order/' . $data['pur_order']));
            } else {
                $id = $data['id'];
                unset($data['id']);
                // handle_pur_invoice_file($id);
                $success = $this->purchase_model->update_pur_bill($id, $data);
                if ($success) {
                    set_alert('success', _l('updated_successfully') . ' ' . _l('purchase_bill'));
                } else {
                    set_alert('warning', _l('update_purchase_bill_fail'));
                }
                redirect(admin_url('purchase/purchase_order/' . $data['pur_order']));
            }
        }
    }

    /**
     * Deletes a bill and redirects to the purchase order page
     * 
     * @param int $id The ID of the bill to delete
     * @param int $pur_id The ID of the related purchase order for redirect
     * @return void
     * @throws Exception If deletion fails or invalid parameters provided
     */
    public function delete_bill($id, $pur_id)
    {
        // Validate input parameters
        if (!is_numeric($id) || !is_numeric($pur_id)) {
            set_alert('warning', _l('invalid_parameters'));
            redirect(admin_url('purchase'));
        }

        try {
            // Attempt to delete the bill
            $result = $this->purchase_model->delete_pur_bill($id);

            if ($result) {
                set_alert('success', _l('bill_deleted_successfully'));
            } else {
                set_alert('warning', _l('bill_deletion_failed'));
            }
        } catch (Exception $e) {
            // Log the error and show message to user
            log_message('error', 'Bill deletion failed: ' . $e->getMessage());
            set_alert('danger', _l('bill_deletion_error'));
        }

        // Redirect to purchase order page
        redirect(admin_url('purchase/purchase_order/' . $pur_id));
    }



    public function get_cost_control_sheet_for_unawarded_tracker()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_cost_control_sheet_for_unawarded_tracker($data);
        echo json_encode(['result' => $result]);
        exit;
    }


    public function item_tracker_report_for_charts()
    {
        if ($this->input->is_ajax_request()) {
            $select = [
                db_prefix() . 'goods_receipt_detail.id as id',
                db_prefix() . 'goods_receipt_detail.goods_receipt_id as goods_receipt_id',
                db_prefix() . 'goods_receipt_detail.commodity_name as commodity_name',
                db_prefix() . 'goods_receipt_detail.description as description',
                db_prefix() . 'goods_receipt_detail.quantities as quantities',
                db_prefix() . 'goods_receipt_detail.po_quantities as po_quantities',
                db_prefix() . 'goods_receipt_detail.payment_date as payment_date',
                db_prefix() . 'goods_receipt_detail.est_delivery_date as est_delivery_date',
                db_prefix() . 'goods_receipt_detail.delivery_date as delivery_date',
                db_prefix() . 'goods_receipt_detail.production_status as production_status',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'goods_receipt_detail';
            $join         = [];
            $join         = [
                'INNER JOIN ' . db_prefix() . 'goods_receipt ON ' . db_prefix() . 'goods_receipt.id = ' . db_prefix() . 'goods_receipt_detail.goods_receipt_id',
            ];

            // Initialize filter variables
            $purOrdersVendor1 = '';
            $purOrdersVendor2 = '';
            $purOrdersReturn1 = [];
            $purOrdersReturn0 = [];
            $productionStatusFilters = [];

            if ($this->input->post('vendors')) {
                $vendor_id = $this->input->post('vendors');


                $status = get_vendor_goods_status($vendor_id);

                if ($status == 1) {
                    $purOrdersVendor1 = $vendor_id;
                } elseif ($status == 0) {
                    $purOrdersVendor2 = $vendor_id;
                }
            }

            if ($this->input->post('pur_order')) {
                $pur_order_ids_filters = $this->input->post('pur_order');

                foreach ($pur_order_ids_filters as $pur_order_id) {
                    $status = get_pur_order_goods_status($pur_order_id);

                    if ($status == 1) {
                        $purOrdersReturn1[] = $pur_order_id;
                    } elseif ($status == 0) {
                        $purOrdersReturn0[] = $pur_order_id;
                    }
                }
            }

            // Handle production_status filter similar to other filters
            if ($this->input->post('production_status')) {
                $production_status_filters = $this->input->post('production_status');
                if (!empty($production_status_filters)) {
                    $productionStatusFilters = is_array($production_status_filters) ? $production_status_filters : [$production_status_filters];
                }
            }

            // Handle Vendor Filter
            if ($purOrdersVendor1) {
                array_push($where, 'AND ' . db_prefix() . 'goods_receipt.supplier_code IN (' .  $purOrdersVendor1 . ')');
            }

            if ($purOrdersReturn1) {
                array_push($where, 'AND ' . db_prefix() . 'goods_receipt.pr_order_id IN (' .  $purOrdersReturn1 . ')');
            }

            // Handle Production Status Filter for first query
            if (!empty($productionStatusFilters)) {
                array_push($where, 'AND ' . db_prefix() . 'goods_receipt_detail.production_status IN (' . implode(',', $productionStatusFilters) . ')');
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);

            $select1 = [
                db_prefix() . 'pur_order_detail.id as id',
                db_prefix() . 'pur_order_detail.pur_order as pur_order',
                db_prefix() . 'pur_order_detail.item_name as commodity_name',
                db_prefix() . 'pur_order_detail.description as description',
                db_prefix() . 'pur_order_detail.quantity as quantities',
                db_prefix() . 'pur_order_detail.po_quantities as po_quantities',
                db_prefix() . 'pur_order_detail.payment_date as payment_date',
                db_prefix() . 'pur_order_detail.est_delivery_date as est_delivery_date',
                db_prefix() . 'pur_order_detail.delivery_date as delivery_date',
                db_prefix() . 'pur_order_detail.production_status as production_status',
            ];
            $where1 = [];

            $aColumns1     = $select1;
            $sIndexColumn1 = 'id';
            $sTable1       = db_prefix() . 'pur_order_detail';
            $join1         = [];
            $join1         = [
                'INNER JOIN ' . db_prefix() . 'pur_orders ON ' . db_prefix() . 'pur_orders.id = ' . db_prefix() . 'pur_order_detail.pur_order',
            ];
            $where1[] = 'AND ' . db_prefix() . 'pur_orders.goods_id = 0';

            if ($purOrdersReturn0) {
                array_push($where1, 'AND ' . db_prefix() . 'pur_orders.id IN (' . implode(',', $purOrdersReturn0) . ')');
            }
            if ($purOrdersVendor2) {
                array_push($where1, 'AND ' . db_prefix() . 'pur_orders.vendor IN (' .  $purOrdersVendor2 . ')');
            }

            // Handle Production Status Filter for second query
            if (!empty($productionStatusFilters)) {
                array_push($where1, 'AND ' . db_prefix() . 'pur_order_detail.production_status IN (' . implode(',', $productionStatusFilters) . ')');
            }

            $result1 = data_tables_init($aColumns1, $sIndexColumn1, $sTable1, $join1, $where1);

            $output  = $result['output'];

            $rResult0 = isset($result['rResult']) && is_array($result['rResult']) ? $result['rResult'] : [];
            $rResult1 = isset($result1['rResult']) && is_array($result1['rResult']) ? $result1['rResult'] : [];

            if (!empty($purOrdersReturn0) && !empty($purOrdersReturn1)) {
                $rResult = array_merge($rResult0, $rResult1);
            } elseif ($purOrdersReturn1) {
                $rResult = $rResult0;
            } elseif ($purOrdersReturn0) {
                $rResult = $rResult1;
            } else {
                $rResult = array_merge($rResult0, $rResult1);
            }

            $tracker = [];

            foreach ($rResult as $aRow) {
                $row = [];

                $goods_receipt = get_goods_receipt_code($aRow['goods_receipt_id']);
                if ($goods_receipt->pr_order_id > 0) {
                    $row[] = get_pur_order_name($goods_receipt->pr_order_id);
                } else {
                    $row[] = get_pur_order_name($aRow['pur_order']);
                }

                $row[] = $aRow['commodity_name'];
                $row[] = $aRow['description'];

                if ($goods_receipt->pr_order_id > 0) {
                    $row[] = isset($aRow['po_quantities']) && $aRow['po_quantities'] !== null ? app_format_number($aRow['po_quantities']) : '-';
                } else {
                    $row[] = isset($aRow['quantities']) && $aRow['quantities'] !== null ? app_format_number($aRow['quantities']) : '-';
                }

                if ($goods_receipt->pr_order_id > 0) {
                    $row[] = app_format_number($aRow['quantities']);
                } else {
                    $row[] = isset($aRow['po_quantities']) && $aRow['po_quantities'] !== null ? app_format_number($aRow['po_quantities']) : '-';
                }

                if ($goods_receipt->pr_order_id > 0) {
                    $remaining_quantities = $aRow['po_quantities'] - $aRow['quantities'];
                } else {
                    $remaining_quantities = isset($aRow['quantities']) && $aRow['quantities'] !== null ? app_format_number($aRow['quantities']) : '-';
                }

                $row[] = app_format_number($remaining_quantities);


                $row[] = !empty($aRow['est_delivery_date']) ? date('d M, Y', strtotime($aRow['est_delivery_date'])) : '-';
                $row[] = !empty($aRow['delivery_date']) ? date('d M, Y', strtotime($aRow['delivery_date'])) : '-';

                $tracker[] = $row;
            }

            // Grouped data array
            $grouped_data = [];
            foreach ($tracker as $row) {
                $group = $row[0];
                if (!isset($grouped_data[$group])) {
                    $grouped_data[$group][] = [
                        "group_name" => '<span class="group-name-cell" style="text-align: center !important; display: block">' . $group . '</span>'
                    ];
                }
                $grouped_data[$group][] = $row;
            }

            // Flatten grouped data for DataTables
            $flattened_data = [];
            foreach ($grouped_data as $group_rows) {
                foreach ($group_rows as $row) {
                    unset($row[0]);
                    $row = array_values($row);
                    if (count($row) === 1) {
                        for ($i = 1; $i <= 8; $i++) {
                            $row[$i] = "";
                        }
                        ksort($row);
                    }
                    $flattened_data[] = $row;
                }
            }

            $output['aaData'] = $flattened_data;

            echo json_encode($output);
            die();
        }
    }

    public function view_purchase_tracker_attachments()
    {
        $input = $this->input->post();
        $attachments = $this->purchase_model->view_purchase_tracker_attachments($input);
        echo json_encode(['result' => $attachments]);
        die();
    }

    public function view_purchase_tracker_file($id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $this->load->model('warehouse/warehouse_model');
        $data['file'] = $this->warehouse_model->get_goods_receipt_file($id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('manage_goods_receipt/preview_purchase_tracker_file', $data);
    }

    public function delete_purchase_tracker_attachment($id)
    {
        $this->load->model('warehouse/warehouse_model');
        $file = $this->warehouse_model->get_goods_receipt_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_purchase_tracker_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    public function cost_fetch_pur_item()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_package_items_info($data['package_id'], $data['itemableid']);
        echo json_encode(['result' => $result]);
        exit;
    }

    public function get_po_charts()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_po_charts($data);
        echo json_encode($result);
        die;
    }

    public function get_wo_charts()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_wo_charts($data);
        echo json_encode($result);
        die;
    }

    public function get_pr_charts()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_pr_charts($data);
        echo json_encode($result);
        die;
    }

    public function get_pc_charts()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_pc_charts($data);
        echo json_encode($result);
        die;
    }

    public function get_vbt_dashboard()
    {
        $data = $this->input->post();
        $result = $this->purchase_model->get_vbt_dashboard($data);
        echo json_encode($result);
        die;
    }
}
