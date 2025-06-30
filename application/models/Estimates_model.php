<?php

use app\services\AbstractKanban;
use app\services\estimates\EstimatesPipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Estimates_model extends App_Model
{
    private $statuses;

    private $shipping_fields = ['shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];

    public function __construct()
    {
        parent::__construct();

        $this->statuses = hooks()->apply_filters('before_set_estimate_statuses', [
            1,
            2,
            5,
            3,
            4,
        ]);
    }

    /**
     * Get unique sale agent for estimates / Used for filters
     * @return array
     */
    public function get_sale_agents()
    {
        return $this->db->query("SELECT DISTINCT(sale_agent) as sale_agent, CONCAT(firstname, ' ', lastname) as full_name FROM " . db_prefix() . 'estimates JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'estimates.sale_agent WHERE sale_agent != 0')->result_array();
    }

    /**
     * Get estimate/s
     * @param mixed $id estimate id
     * @param array $where perform where
     * @return mixed
     */
    public function get($id = '', $where = [])
    {
        $this->db->select('*,' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'estimates.id as id, ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->from(db_prefix() . 'estimates');
        $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'estimates.currency', 'left');
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'estimates.id', $id);
            $estimate = $this->db->get()->row();
            if ($estimate) {
                $estimate->attachments                           = $this->get_attachments($id);
                $estimate->visible_attachments_to_customer_found = false;

                foreach ($estimate->attachments as $attachment) {
                    if ($attachment['visible_to_customer'] == 1) {
                        $estimate->visible_attachments_to_customer_found = true;

                        break;
                    }
                }

                $estimate->items = get_items_by_type('estimate', $id);

                if ($estimate->project_id) {
                    $this->load->model('projects_model');
                    $estimate->project_data = $this->projects_model->get($estimate->project_id);
                }

                $estimate->client = $this->clients_model->get($estimate->clientid);

                if (!$estimate->client) {
                    $estimate->client          = new stdClass();
                    $estimate->client->company = $estimate->deleted_customer_name;
                }

                $this->load->model('email_schedule_model');
                $estimate->scheduled_email = $this->email_schedule_model->get($id, 'estimate');
            }

            return $estimate;
        }
        $this->db->order_by('number,YEAR(date)', 'desc');

        return $this->db->get()->result_array();
    }

    /**
     * Get estimate statuses
     * @return array
     */
    public function get_statuses()
    {
        return $this->statuses;
    }

    public function clear_signature($id)
    {
        $this->db->select('signature');
        $this->db->where('id', $id);
        $estimate = $this->db->get(db_prefix() . 'estimates')->row();

        if ($estimate) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'estimates', ['signature' => null]);

            if (!empty($estimate->signature)) {
                unlink(get_upload_path_by_type('estimate') . $id . '/' . $estimate->signature);
            }

            return true;
        }

        return false;
    }

    /**
     * Convert estimate to invoice
     * @param mixed $id estimate id
     * @return mixed     New invoice ID
     */
    public function convert_to_invoice($id, $client = false, $draft_invoice = false)
    {
        // Recurring invoice date is okey lets convert it to new invoice
        $_estimate = $this->get($id);

        $new_invoice_data = [];
        if ($draft_invoice == true) {
            $new_invoice_data['save_as_draft'] = true;
        }
        $new_invoice_data['clientid']   = $_estimate->clientid;
        $new_invoice_data['project_id'] = $_estimate->project_id;
        $new_invoice_data['number']     = get_option('next_invoice_number');
        $new_invoice_data['date']       = _d(date('Y-m-d'));
        $new_invoice_data['duedate']    = _d(date('Y-m-d'));
        if (get_option('invoice_due_after') != 0) {
            $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        }
        $new_invoice_data['show_quantity_as'] = $_estimate->show_quantity_as;
        $new_invoice_data['currency']         = $_estimate->currency;
        $new_invoice_data['subtotal']         = $_estimate->subtotal;
        $new_invoice_data['total']            = $_estimate->total;
        $new_invoice_data['adjustment']       = $_estimate->adjustment;
        $new_invoice_data['discount_percent'] = $_estimate->discount_percent;
        $new_invoice_data['discount_total']   = $_estimate->discount_total;
        $new_invoice_data['discount_type']    = $_estimate->discount_type;
        $new_invoice_data['sale_agent']       = $_estimate->sale_agent;
        // Since version 1.0.6
        $new_invoice_data['billing_street']   = clear_textarea_breaks($_estimate->billing_street);
        $new_invoice_data['billing_city']     = $_estimate->billing_city;
        $new_invoice_data['billing_state']    = $_estimate->billing_state;
        $new_invoice_data['billing_zip']      = $_estimate->billing_zip;
        $new_invoice_data['billing_country']  = $_estimate->billing_country;
        $new_invoice_data['shipping_street']  = clear_textarea_breaks($_estimate->shipping_street);
        $new_invoice_data['shipping_city']    = $_estimate->shipping_city;
        $new_invoice_data['shipping_state']   = $_estimate->shipping_state;
        $new_invoice_data['shipping_zip']     = $_estimate->shipping_zip;
        $new_invoice_data['shipping_country'] = $_estimate->shipping_country;

        if ($_estimate->include_shipping == 1) {
            $new_invoice_data['include_shipping'] = 1;
        }

        $new_invoice_data['show_shipping_on_invoice'] = $_estimate->show_shipping_on_estimate;
        $new_invoice_data['terms']                    = get_option('predefined_terms_invoice');
        $new_invoice_data['clientnote']               = get_option('predefined_clientnote_invoice');
        // Set to unpaid status automatically
        $new_invoice_data['status']    = 1;
        $new_invoice_data['adminnote'] = '';

        $this->load->model('payment_modes_model');
        $modes = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $temp_modes = [];
        foreach ($modes as $mode) {
            if ($mode['selected_by_default'] == 0) {
                continue;
            }
            $temp_modes[] = $mode['id'];
        }
        $new_invoice_data['allowed_payment_modes'] = $temp_modes;
        $new_invoice_data['newitems']              = [];
        $custom_fields_items                       = get_custom_fields('items');
        $key                                       = 1;
        foreach ($_estimate->items as $item) {
            $new_invoice_data['newitems'][$key]['description']      = $item['description'];
            $new_invoice_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
            $new_invoice_data['newitems'][$key]['unit']             = $item['unit'];
            $new_invoice_data['newitems'][$key]['taxname']          = [];
            $taxes                                                  = get_estimate_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_invoice_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_invoice_data['newitems'][$key]['rate']  = $item['rate'];
            $new_invoice_data['newitems'][$key]['order'] = $item['item_order'];
            foreach ($custom_fields_items as $cf) {
                $new_invoice_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                    define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                }
            }
            $key++;
        }
        $this->load->model('invoices_model');
        $id = $this->invoices_model->add($new_invoice_data);
        if ($id) {
            // Customer accepted the estimate and is auto converted to invoice
            if (!is_staff_logged_in()) {
                $this->db->where('rel_type', 'invoice');
                $this->db->where('rel_id', $id);
                $this->db->delete(db_prefix() . 'sales_activity');
                $this->invoices_model->log_invoice_activity($id, 'invoice_activity_auto_converted_from_estimate', true, serialize([
                    '<a href="' . admin_url('estimates/list_estimates/' . $_estimate->id) . '">' . format_estimate_number($_estimate->id) . '</a>',
                ]));
            }
            // For all cases update addefrom and sale agent from the invoice
            // May happen staff is not logged in and these values to be 0
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', [
                'addedfrom'  => $_estimate->addedfrom,
                'sale_agent' => $_estimate->sale_agent,
            ]);

            // Update estimate with the new invoice data and set to status accepted
            $this->db->where('id', $_estimate->id);
            $this->db->update(db_prefix() . 'estimates', [
                'invoiced_date' => date('Y-m-d H:i:s'),
                'invoiceid'     => $id,
                'status'        => 4,
            ]);


            if (is_custom_fields_smart_transfer_enabled()) {
                $this->db->where('fieldto', 'estimate');
                $this->db->where('active', 1);
                $cfEstimates = $this->db->get(db_prefix() . 'customfields')->result_array();
                foreach ($cfEstimates as $field) {
                    $tmpSlug = explode('_', $field['slug'], 2);
                    if (isset($tmpSlug[1])) {
                        $this->db->where('fieldto', 'invoice');

                        $this->db->group_start();
                        $this->db->like('slug', 'invoice_' . $tmpSlug[1], 'after');
                        $this->db->where('type', $field['type']);
                        $this->db->where('options', $field['options']);
                        $this->db->where('active', 1);
                        $this->db->group_end();

                        // $this->db->where('slug LIKE "invoice_' . $tmpSlug[1] . '%" AND type="' . $field['type'] . '" AND options="' . $field['options'] . '" AND active=1');
                        $cfTransfer = $this->db->get(db_prefix() . 'customfields')->result_array();

                        // Don't make mistakes
                        // Only valid if 1 result returned
                        // + if field names similarity is equal or more then CUSTOM_FIELD_TRANSFER_SIMILARITY%
                        if (count($cfTransfer) == 1 && ((similarity($field['name'], $cfTransfer[0]['name']) * 100) >= CUSTOM_FIELD_TRANSFER_SIMILARITY)) {
                            $value = get_custom_field_value($_estimate->id, $field['id'], 'estimate', false);

                            if ($value == '') {
                                continue;
                            }

                            $this->db->insert(db_prefix() . 'customfieldsvalues', [
                                'relid'   => $id,
                                'fieldid' => $cfTransfer[0]['id'],
                                'fieldto' => 'invoice',
                                'value'   => $value,
                            ]);
                        }
                    }
                }
            }

            if ($client == false) {
                $this->log_estimate_activity($_estimate->id, 'estimate_activity_converted', false, serialize([
                    '<a href="' . admin_url('invoices/list_invoices/' . $id) . '">' . format_invoice_number($id) . '</a>',
                ]));
            }

            hooks()->do_action('estimate_converted_to_invoice', ['invoice_id' => $id, 'estimate_id' => $_estimate->id]);
        }

        return $id;
    }

    /**
     * Copy estimate
     * @param mixed $id estimate id to copy
     * @return mixed
     */
    public function copy($id)
    {
        $_estimate                       = $this->get($id);
        $new_estimate_data               = [];
        $new_estimate_data['clientid']   = $_estimate->clientid;
        $new_estimate_data['project_id'] = $_estimate->project_id;
        $new_estimate_data['number']     = get_option('next_estimate_number');
        $new_estimate_data['date']       = _d(date('Y-m-d'));
        $new_estimate_data['expirydate'] = null;

        if ($_estimate->expirydate && get_option('estimate_due_after') != 0) {
            $new_estimate_data['expirydate'] = _d(date('Y-m-d', strtotime('+' . get_option('estimate_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        }

        $new_estimate_data['show_quantity_as'] = $_estimate->show_quantity_as;
        $new_estimate_data['currency']         = $_estimate->currency;
        $new_estimate_data['subtotal']         = $_estimate->subtotal;
        $new_estimate_data['total']            = $_estimate->total;
        $new_estimate_data['adminnote']        = $_estimate->adminnote;
        $new_estimate_data['adjustment']       = $_estimate->adjustment;
        $new_estimate_data['discount_percent'] = $_estimate->discount_percent;
        $new_estimate_data['discount_total']   = $_estimate->discount_total;
        $new_estimate_data['discount_type']    = $_estimate->discount_type;
        $new_estimate_data['terms']            = $_estimate->terms;
        $new_estimate_data['sale_agent']       = $_estimate->sale_agent;
        $new_estimate_data['reference_no']     = $_estimate->reference_no;
        // Since version 1.0.6
        $new_estimate_data['billing_street']   = clear_textarea_breaks($_estimate->billing_street);
        $new_estimate_data['billing_city']     = $_estimate->billing_city;
        $new_estimate_data['billing_state']    = $_estimate->billing_state;
        $new_estimate_data['billing_zip']      = $_estimate->billing_zip;
        $new_estimate_data['billing_country']  = $_estimate->billing_country;
        $new_estimate_data['shipping_street']  = clear_textarea_breaks($_estimate->shipping_street);
        $new_estimate_data['shipping_city']    = $_estimate->shipping_city;
        $new_estimate_data['shipping_state']   = $_estimate->shipping_state;
        $new_estimate_data['shipping_zip']     = $_estimate->shipping_zip;
        $new_estimate_data['shipping_country'] = $_estimate->shipping_country;
        if ($_estimate->include_shipping == 1) {
            $new_estimate_data['include_shipping'] = $_estimate->include_shipping;
        }
        $new_estimate_data['show_shipping_on_estimate'] = $_estimate->show_shipping_on_estimate;
        // Set to unpaid status automatically
        $new_estimate_data['status']     = 1;
        $new_estimate_data['clientnote'] = $_estimate->clientnote;
        $new_estimate_data['adminnote']  = '';
        $new_estimate_data['newitems']   = [];
        $custom_fields_items             = get_custom_fields('items');
        $key                             = 1;
        foreach ($_estimate->items as $item) {
            $new_estimate_data['newitems'][$key]['description']      = $item['description'];
            $new_estimate_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_estimate_data['newitems'][$key]['qty']              = $item['qty'];
            $new_estimate_data['newitems'][$key]['unit']             = $item['unit'];
            $new_estimate_data['newitems'][$key]['taxname']          = [];
            $taxes                                                   = get_estimate_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_estimate_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_estimate_data['newitems'][$key]['rate']  = $item['rate'];
            $new_estimate_data['newitems'][$key]['order'] = $item['item_order'];
            foreach ($custom_fields_items as $cf) {
                $new_estimate_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                    define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                }
            }
            $key++;
        }
        $id = $this->add($new_estimate_data);
        if ($id) {
            $custom_fields = get_custom_fields('estimate');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($_estimate->id, $field['id'], 'estimate', false);
                if ($value == '') {
                    continue;
                }

                $this->db->insert(db_prefix() . 'customfieldsvalues', [
                    'relid'   => $id,
                    'fieldid' => $field['id'],
                    'fieldto' => 'estimate',
                    'value'   => $value,
                ]);
            }

            $tags = get_tags_in($_estimate->id, 'estimate');
            handle_tags_save($tags, $id, 'estimate');

            log_activity('Copied Estimate ' . format_estimate_number($_estimate->id));

            return $id;
        }

        return false;
    }

    /**
     * Performs estimates totals status
     * @param array $data
     * @return array
     */
    public function get_estimates_total($data)
    {
        $statuses            = $this->get_statuses();
        $has_permission_view = staff_can('view',  'estimates');
        $this->load->model('currencies_model');
        if (isset($data['currency'])) {
            $currencyid = $data['currency'];
        } elseif (isset($data['customer_id']) && $data['customer_id'] != '') {
            $currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);
            if ($currencyid == 0) {
                $currencyid = $this->currencies_model->get_base_currency()->id;
            }
        } elseif (isset($data['project_id']) && $data['project_id'] != '') {
            $this->load->model('projects_model');
            $currencyid = $this->projects_model->get_currency($data['project_id'])->id;
        } else {
            $currencyid = $this->currencies_model->get_base_currency()->id;
        }

        $currency = get_currency($currencyid);
        $where    = '';
        if (isset($data['customer_id']) && $data['customer_id'] != '') {
            $where = ' AND clientid=' . $data['customer_id'];
        }

        if (isset($data['project_id']) && $data['project_id'] != '') {
            $where .= ' AND project_id=' . $data['project_id'];
        }

        if (!$has_permission_view) {
            $where .= ' AND ' . get_estimates_where_sql_for_staff(get_staff_user_id());
        }

        $sql = 'SELECT';
        foreach ($statuses as $estimate_status) {
            $sql .= '(SELECT SUM(total) FROM ' . db_prefix() . 'estimates WHERE status=' . $estimate_status;
            $sql .= ' AND currency =' . $this->db->escape_str($currencyid);
            if (isset($data['years']) && count($data['years']) > 0) {
                $sql .= ' AND YEAR(date) IN (' . implode(', ', array_map(function ($year) {
                    return get_instance()->db->escape_str($year);
                }, $data['years'])) . ')';
            } else {
                $sql .= ' AND YEAR(date) = ' . date('Y');
            }
            $sql .= $where;
            $sql .= ') as "' . $estimate_status . '",';
        }

        $sql     = substr($sql, 0, -1);
        $result  = $this->db->query($sql)->result_array();
        $_result = [];
        $i       = 1;
        foreach ($result as $key => $val) {
            foreach ($val as $status => $total) {
                $_result[$i]['total']         = $total;
                $_result[$i]['symbol']        = $currency->symbol;
                $_result[$i]['currency_name'] = $currency->name;
                $_result[$i]['status']        = $status;
                $i++;
            }
        }
        $_result['currencyid'] = $currencyid;

        return $_result;
    }

    /**
     * Insert new estimate to database
     * @param array $data invoiec data
     * @return mixed - false if not insert, estimate ID if succes
     */
    public function add($data)
    {
        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        $data['prefix'] = get_option('estimate_prefix');

        $data['number_format'] = get_option('estimate_number_format');

        $save_and_send = isset($data['save_and_send']);

        $estimateRequestID = false;
        if (isset($data['estimate_request_id'])) {
            $estimateRequestID = $data['estimate_request_id'];
            unset($data['estimate_request_id']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['hash'] = app_generate_hash();
        $tags         = isset($data['tags']) ? $data['tags'] : '';

        $items = [];
        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }

        $data = $this->map_shipping_columns($data);

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        $hook = hooks()->apply_filters('before_estimate_added', [
            'data'  => $data,
            'items' => $items,
        ]);

        $data  = $hook['data'];
        $items = $hook['items'];

        if (isset($data['remarks'])) {
            unset($data['remarks']);
        }
        unset($data['unit_id']);

        $detailed_costing = [];
        if (isset($data['detailed_costing'])) {
            $detailed_costing = $data['detailed_costing'];
            unset($data['detailed_costing']);
        }

        $budget_summary_remarks = [];
        if (isset($data['budget_summary_remarks'])) {
            $budget_summary_remarks = $data['budget_summary_remarks'];
            unset($data['budget_summary_remarks']);
        }

        $overall_budget_area = [];
        if (isset($data['overall_budget_area'])) {
            $overall_budget_area = $data['overall_budget_area'];
            unset($data['overall_budget_area']);
        }

        if (isset($data['file_csv'])) {
            unset($data['file_csv']);
        }

        if (isset($data['floor'])) {
            unset($data['floor']);
        }
        if (isset($data['area'])) {
            unset($data['area']);
        }
        if (isset($data['master_area'])) {
            unset($data['master_area']);
        }
        $newareasummaryitems = [];
        if (isset($data['newareasummaryitems'])) {
            $newareasummaryitems = $data['newareasummaryitems'];
            unset($data['newareasummaryitems']);
        }

        if (isset($data['item_name'])) {
            unset($data['item_name']);
        }

        if (isset($data['item_code'])) {
            unset($data['item_code']);
        }

        if (isset($data['sub_head'])) {
            unset($data['sub_head']);
        }

        if (isset($data['area_working_file_csv'])) {
            unset($data['area_working_file_csv']);
        }

        if (isset($data['cost_sub_head'])) {
            unset($data['cost_sub_head']);
        }

        $this->db->insert(db_prefix() . 'estimates', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Update next estimate number in settings
            $this->db->where('name', 'next_estimate_number');
            $this->db->set('value', 'value+1', false);
            $this->db->update(db_prefix() . 'options');

            if ($estimateRequestID !== false && $estimateRequestID != '') {
                $this->load->model('estimate_request_model');
                $completedStatus = $this->estimate_request_model->get_status_by_flag('completed');
                $this->estimate_request_model->update_request_status([
                    'requestid' => $estimateRequestID,
                    'status'    => $completedStatus->id,
                ]);
            }

            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            handle_tags_save($tags, $insert_id, 'estimate');

            foreach ($items as $key => $item) {
                if ($itemid = add_new_sales_item_post($item, $insert_id, 'estimate')) {
                    _maybe_insert_post_item_tax($itemid, $item, $insert_id, 'estimate');
                }
            }

            if(!empty($newareasummaryitems)) {
                foreach ($newareasummaryitems as $akey => $aitem) {
                    $this->add_new_area_summary_item_post($aitem, $insert_id);
                }
            }

            update_sales_total_tax_column($insert_id, 'estimate', db_prefix() . 'estimates');
            $this->update_estimate_budget_info($insert_id, $detailed_costing, 'detailed_costing');
            $this->update_estimate_budget_info($insert_id, $budget_summary_remarks, 'budget_summary_remarks');
            $this->update_estimate_budget_info($insert_id, $overall_budget_area, 'overall_budget_area');
            $this->update_basic_estimate_details($insert_id);
            $this->log_estimate_activity($insert_id, 'estimate_activity_created');

            hooks()->do_action('after_estimate_added', $insert_id);

            if ($save_and_send === true) {
                $this->send_estimate_to_client($insert_id, '', true, '', true);
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * Get item by id
     * @param mixed $id item id
     * @return object
     */
    public function get_estimate_item($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'itemable')->row();
    }

    /**
     * Update estimate data
     * @param array $data estimate data
     * @param mixed $id estimateid
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;

        $data['number'] = trim($data['number']);

        $original_estimate = $this->get($id);

        $original_status = $original_estimate->status;

        $original_number = $original_estimate->number;

        $original_number_formatted = format_estimate_number($id);

        $save_and_send = isset($data['save_and_send']);

        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        $newitems = [];
        if (isset($data['newitems'])) {
            $newitems = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'estimate')) {
                $affectedRows++;
            }
        }

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        $data['shipping_street'] = trim($data['shipping_street']);
        $data['shipping_street'] = nl2br($data['shipping_street']);

        $data = $this->map_shipping_columns($data);

        $hook = hooks()->apply_filters('before_estimate_updated', [
            'data'          => $data,
            'items'         => $items,
            'newitems'      => $newitems,
            'removed_items' => isset($data['removed_items']) ? $data['removed_items'] : [],
        ], $id);

        $data                  = $hook['data'];
        $items                 = $hook['items'];
        $newitems              = $hook['newitems'];
        $data['removed_items'] = $hook['removed_items'];

        // Delete items checked to be removed from database
        foreach ($data['removed_items'] as $remove_item_id) {
            $original_item = $this->get_estimate_item($remove_item_id);
            if (handle_removed_sales_item_post($remove_item_id, 'estimate')) {
                $affectedRows++;
                $this->log_estimate_activity($id, 'invoice_estimate_activity_removed_item', false, serialize([
                    $original_item->description,
                ]));
            }
        }

        if(!empty($data['removed_area_working_items'])) {
            foreach ($data['removed_area_working_items'] as $remove_item_id) {
                $this->delete_area_working_item($remove_item_id);
            }
        }
        unset($data['removed_area_working_items']);

        if(!empty($data['removed_area_summary_items'])) {
            foreach ($data['removed_area_summary_items'] as $remove_item_id) {
                $this->delete_area_summary_item($remove_item_id);
            }
        }
        unset($data['removed_area_summary_items']);

        unset($data['removed_items']);

        if (isset($data['remarks'])) {
            unset($data['remarks']);
        }
        unset($data['unit_id']);

        unset($data['area_description']);
        unset($data['area_length']);
        unset($data['area_width']);
        $newareaworkingitems = [];
        if (isset($data['newareaworkingitems'])) {
            $newareaworkingitems = $data['newareaworkingitems'];
            unset($data['newareaworkingitems']);
        }
        $areaworkingitems = [];
        if (isset($data['areaworkingitems'])) {
            $areaworkingitems = $data['areaworkingitems'];
            unset($data['areaworkingitems']);
        }

        $detailed_costing = [];
        if (isset($data['detailed_costing'])) {
            $detailed_costing = $data['detailed_costing'];
            unset($data['detailed_costing']);
        }

        $budget_summary_remarks = [];
        if (isset($data['budget_summary_remarks'])) {
            $budget_summary_remarks = $data['budget_summary_remarks'];
            unset($data['budget_summary_remarks']);
        }

        $overall_budget_area = [];
        if (isset($data['overall_budget_area'])) {
            $overall_budget_area = $data['overall_budget_area'];
            unset($data['overall_budget_area']);
        }

        if (isset($data['file_csv'])) {
            unset($data['file_csv']);
        }

        unset($data['floor']);
        unset($data['area']);
        unset($data['master_area']);
        $newareasummaryitems = [];
        if (isset($data['newareasummaryitems'])) {
            $newareasummaryitems = $data['newareasummaryitems'];
            unset($data['newareasummaryitems']);
        }
        $areasummaryitems = [];
        if (isset($data['areasummaryitems'])) {
            $areasummaryitems = $data['areasummaryitems'];
            unset($data['areasummaryitems']);
        }

        if (isset($data['item_name'])) {
            unset($data['item_name']);
        }

        if (isset($data['item_code'])) {
            unset($data['item_code']);
        }

        if (isset($data['sub_head'])) {
            unset($data['sub_head']);
        }

        if (isset($data['area_working_file_csv'])) {
            unset($data['area_working_file_csv']);
        }

        if (isset($data['cost_sub_head'])) {
            unset($data['cost_sub_head']);
        }

        $next_revision = false;
        if (isset($data['next_revision'])) {
            $next_revision = true;
            unset($data['next_revision']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'estimates', $data);

        if ($this->db->affected_rows() > 0) {
            // Check for status change
            if ($original_status != $data['status']) {
                $this->log_estimate_activity($original_estimate->id, 'not_estimate_status_updated', false, serialize([
                    '<original_status>' . $original_status . '</original_status>',
                    '<new_status>' . $data['status'] . '</new_status>',
                ]));
                if ($data['status'] == 2) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'estimates', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);
                }
            }
            if ($original_number != $data['number']) {
                $this->log_estimate_activity($original_estimate->id, 'estimate_activity_number_changed', false, serialize([
                    $original_number_formatted,
                    format_estimate_number($original_estimate->id),
                ]));
            }
            $affectedRows++;
        }

        foreach ($items as $key => $item) {
            $original_item = $this->get_estimate_item($item['itemid']);

            if (update_sales_item_post($item['itemid'], $item, 'item_order')) {
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'sub_head')) {
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'unit')) {
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'unit_id')) {
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'rate')) {
                $this->log_estimate_activity($id, 'invoice_estimate_activity_updated_item_rate', false, serialize([
                    $original_item->rate,
                    $item['rate'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'qty')) {
                $this->log_estimate_activity($id, 'invoice_estimate_activity_updated_qty_item', false, serialize([
                    $item['description'],
                    $original_item->qty,
                    $item['qty'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'description')) {
                $this->log_estimate_activity($id, 'invoice_estimate_activity_updated_item_short_description', false, serialize([
                    $original_item->description,
                    $item['description'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'long_description')) {
                $this->log_estimate_activity($id, 'invoice_estimate_activity_updated_item_long_description', false, serialize([
                    $original_item->long_description,
                    $item['long_description'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'detailed_costing_item_code')) {
                $affectedRows++;
            }

            if (isset($item['custom_fields'])) {
                if (handle_custom_fields_post($item['itemid'], $item['custom_fields'])) {
                    $affectedRows++;
                }
            }

            if (!isset($item['taxname']) || (isset($item['taxname']) && count($item['taxname']) == 0)) {
                if (delete_taxes_from_item($item['itemid'], 'estimate')) {
                    $affectedRows++;
                }
            } else {
                $item_taxes        = get_estimate_item_taxes($item['itemid']);
                $_item_taxes_names = [];
                foreach ($item_taxes as $_item_tax) {
                    array_push($_item_taxes_names, $_item_tax['taxname']);
                }

                $i = 0;
                foreach ($_item_taxes_names as $_item_tax) {
                    if (!in_array($_item_tax, $item['taxname'])) {
                        $this->db->where('id', $item_taxes[$i]['id'])
                            ->delete(db_prefix() . 'item_tax');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                    $i++;
                }
                if (_maybe_insert_post_item_tax($item['itemid'], $item, $id, 'estimate')) {
                    $affectedRows++;
                }
            }
        }

        foreach ($newitems as $key => $item) {
            if ($new_item_added = add_new_sales_item_post($item, $id, 'estimate')) {
                _maybe_insert_post_item_tax($new_item_added, $item, $id, 'estimate');
                $this->log_estimate_activity($id, 'invoice_estimate_activity_added_item', false, serialize([
                    $item['description'],
                ]));
                $affectedRows++;
            }
        }

        if(!empty($newareaworkingitems)) {
            foreach ($newareaworkingitems as $awkey => $awitem) {
                $this->add_new_area_working_item_post($awitem, $id);
            }
        }

        if(!empty($areaworkingitems)) {
            foreach ($areaworkingitems as $awkey => $awitem) {
                $awid = $awitem['itemid'];
                unset($awitem['itemid']);
                $this->update_area_working_item_post($awitem, $awid);
            }
        }

        if(!empty($newareasummaryitems)) {
            foreach ($newareasummaryitems as $askey => $asitem) {
                $this->add_new_area_summary_item_post($asitem, $id);
            }
        }

        if(!empty($areasummaryitems)) {
            foreach ($areasummaryitems as $askey => $asitem) {
                $asid = $asitem['itemid'];
                unset($asitem['itemid']);
                $this->update_area_summary_item_post($asitem, $asid);
            }
        }

        if ($affectedRows > 0) {
            update_sales_total_tax_column($id, 'estimate', db_prefix() . 'estimates');
        }

        if ($save_and_send === true) {
            $this->send_estimate_to_client($id, '', true, '', true);
        }

        $this->update_estimate_budget_info($id, $detailed_costing, 'detailed_costing');
        $this->update_estimate_budget_info($id, $budget_summary_remarks, 'budget_summary_remarks');
        $this->update_estimate_budget_info($id, $overall_budget_area, 'overall_budget_area');
        $this->update_basic_estimate_details($id);

        if ($affectedRows > 0) {
            hooks()->do_action('after_estimate_updated', $id);

            if($next_revision) {
                $this->create_new_revision(['id' => $id]);
            }

            return true;
        }

        return false;
    }

    public function mark_action_status($action, $id, $client = false)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'status' => $action,
        ]);

        $notifiedUsers = [];

        if ($this->db->affected_rows() > 0) {
            $estimate = $this->get($id);
            if ($client == true) {
                $this->db->where('staffid', $estimate->addedfrom);
                $this->db->or_where('staffid', $estimate->sale_agent);
                $staff_estimate = $this->db->get(db_prefix() . 'staff')->result_array();

                $invoiceid = false;
                $invoiced  = false;

                $contact_id = !is_client_logged_in()
                    ? get_primary_contact_user_id($estimate->clientid)
                    : get_contact_user_id();

                if ($action == 4) {
                    if (get_option('estimate_auto_convert_to_invoice_on_client_accept') == 1) {
                        $invoiceid = $this->convert_to_invoice($id, true);
                        $this->load->model('invoices_model');
                        if ($invoiceid) {
                            $invoiced = true;
                            $invoice  = $this->invoices_model->get($invoiceid);
                            $this->log_estimate_activity($id, 'estimate_activity_client_accepted_and_converted', true, serialize([
                                '<a href="' . admin_url('invoices/list_invoices/' . $invoiceid) . '">' . format_invoice_number($invoice->id) . '</a>',
                            ]));
                        }
                    } else {
                        $this->log_estimate_activity($id, 'estimate_activity_client_accepted', true);
                    }

                    // Send thank you email to all contacts with permission estimates
                    $contacts = $this->clients_model->get_contacts($estimate->clientid, ['active' => 1, 'estimate_emails' => 1]);

                    foreach ($contacts as $contact) {
                        send_mail_template('estimate_accepted_to_customer', $estimate, $contact);
                    }

                    foreach ($staff_estimate as $member) {
                        $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => 'not_estimate_customer_accepted',
                            'link'            => 'estimates/list_estimates/' . $id,
                            'additional_data' => serialize([
                                format_estimate_number($estimate->id),
                            ]),
                        ]);

                        if ($notified) {
                            array_push($notifiedUsers, $member['staffid']);
                        }

                        send_mail_template('estimate_accepted_to_staff', $estimate, $member['email'], $contact_id);
                    }

                    pusher_trigger_notification($notifiedUsers);
                    hooks()->do_action('estimate_accepted', $id);

                    return [
                        'invoiced'  => $invoiced,
                        'invoiceid' => $invoiceid,
                    ];
                } elseif ($action == 3) {
                    foreach ($staff_estimate as $member) {
                        $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => 'not_estimate_customer_declined',
                            'link'            => 'estimates/list_estimates/' . $id,
                            'additional_data' => serialize([
                                format_estimate_number($estimate->id),
                            ]),
                        ]);

                        if ($notified) {
                            array_push($notifiedUsers, $member['staffid']);
                        }
                        // Send staff email notification that customer declined estimate
                        send_mail_template('estimate_declined_to_staff', $estimate, $member['email'], $contact_id);
                    }

                    pusher_trigger_notification($notifiedUsers);
                    $this->log_estimate_activity($id, 'estimate_activity_client_declined', true);
                    hooks()->do_action('estimate_declined', $id);

                    return [
                        'invoiced'  => $invoiced,
                        'invoiceid' => $invoiceid,
                    ];
                }
            } else {
                if ($action == 2) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'estimates', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);
                }
                // Admin marked estimate
                $this->log_estimate_activity($id, 'estimate_activity_marked', false, serialize([
                    '<status>' . $action . '</status>',
                ]));

                return true;
            }
        }

        return false;
    }

    /**
     * Get estimate attachments
     * @param mixed $estimate_id
     * @param string $id attachment id
     * @return mixed
     */
    public function get_attachments($estimate_id, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $estimate_id);
        }
        $this->db->where('rel_type', 'estimate');
        $result = $this->db->get(db_prefix() . 'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     *  Delete estimate attachment
     * @param mixed $id attachmentid
     * @return  boolean
     */
    public function delete_attachment($id)
    {
        $attachment = $this->get_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('estimate') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Estimate Attachment Deleted [EstimateID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('estimate') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('estimate') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('estimate') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Delete estimate items and all connections
     * @param mixed $id estimateid
     * @return boolean
     */
    public function delete($id, $simpleDelete = false)
    {
        if (get_option('delete_only_on_last_estimate') == 1 && $simpleDelete == false) {
            if (!is_last_estimate($id)) {
                return false;
            }
        }
        $estimate = $this->get($id);
        if (!is_null($estimate->invoiceid) && $simpleDelete == false) {
            return [
                'is_invoiced_estimate_delete_error' => true,
            ];
        }
        hooks()->do_action('before_estimate_deleted', $id);

        $number = format_estimate_number($id);

        $this->clear_signature($id);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'estimates');

        if ($this->db->affected_rows() > 0) {
            if (!is_null($estimate->short_link)) {
                app_archive_short_link($estimate->short_link);
            }

            if (get_option('estimate_number_decrement_on_delete') == 1 && $simpleDelete == false) {
                $current_next_estimate_number = get_option('next_estimate_number');
                if ($current_next_estimate_number > 1) {
                    // Decrement next estimate number to
                    $this->db->where('name', 'next_estimate_number');
                    $this->db->set('value', 'value-1', false);
                    $this->db->update(db_prefix() . 'options');
                }
            }

            if (total_rows(db_prefix() . 'proposals', [
                    'estimate_id' => $id,
                ]) > 0) {
                $this->db->where('estimate_id', $id);
                $estimate = $this->db->get(db_prefix() . 'proposals')->row();
                $this->db->where('id', $estimate->id);
                $this->db->update(db_prefix() . 'proposals', [
                    'estimate_id'    => null,
                    'date_converted' => null,
                ]);
            }

            delete_tracked_emails($id, 'estimate');

            $this->db->where('relid IN (SELECT id from ' . db_prefix() . 'itemable WHERE rel_type="estimate" AND rel_id="' . $this->db->escape_str($id) . '")');
            $this->db->where('fieldto', 'items');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'estimate');
            $this->db->delete(db_prefix() . 'notes');

            $this->db->where('rel_type', 'estimate');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'views_tracking');

            $this->db->where('rel_type', 'estimate');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'taggables');

            $this->db->where('rel_type', 'estimate');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'reminders');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'estimate');
            $this->db->delete(db_prefix() . 'itemable');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'estimate');
            $this->db->delete(db_prefix() . 'item_tax');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'estimate');
            $this->db->delete(db_prefix() . 'sales_activity');

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'estimate');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $attachments = $this->get_attachments($id);
            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'estimate');
            $this->db->delete('scheduled_emails');

            // Get related tasks
            $this->db->where('rel_type', 'estimate');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
            if ($simpleDelete == false) {
                log_activity('Estimates Deleted [Number: ' . $number . ']');
            }

            hooks()->do_action('after_estimate_deleted', $id);

            return true;
        }

        return false;
    }

    /**
     * Set estimate to sent when email is successfuly sended to client
     * @param mixed $id estimateid
     */
    public function set_estimate_sent($id, $emails_sent = [])
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'sent'     => 1,
            'datesend' => date('Y-m-d H:i:s'),
        ]);

        $this->log_estimate_activity($id, 'invoice_estimate_activity_sent_to_client', false, serialize([
            '<custom_data>' . implode(', ', $emails_sent) . '</custom_data>',
        ]));

        // Update estimate status to sent
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'status' => 2,
        ]);

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'estimate');
        $this->db->delete('scheduled_emails');
    }

    /**
     * Send expiration reminder to customer
     * @param mixed $id estimate id
     * @return boolean
     */
    public function send_expiry_reminder($id)
    {
        $estimate        = $this->get($id);
        $estimate_number = format_estimate_number($estimate->id);
        set_mailing_constant();
        $pdf              = estimate_pdf($estimate);
        $attach           = $pdf->Output($estimate_number . '.pdf', 'S');
        $emails_sent      = [];
        $sms_sent         = false;
        $sms_reminder_log = [];

        // For all cases update this to prevent sending multiple reminders eq on fail
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'is_expiry_notified' => 1,
        ]);

        $contacts = $this->clients_model->get_contacts($estimate->clientid, ['active' => 1, 'estimate_emails' => 1]);

        foreach ($contacts as $contact) {
            $template = mail_template('estimate_expiration_reminder', $estimate, $contact);

            $merge_fields = $template->get_merge_fields();

            $template->add_attachment([
                'attachment' => $attach,
                'filename'   => str_replace('/', '-', $estimate_number . '.pdf'),
                'type'       => 'application/pdf',
            ]);

            if ($template->send()) {
                array_push($emails_sent, $contact['email']);
            }

            if (can_send_sms_based_on_creation_date($estimate->datecreated)
                && $this->app_sms->trigger(SMS_TRIGGER_ESTIMATE_EXP_REMINDER, $contact['phonenumber'], $merge_fields)) {
                $sms_sent = true;
                array_push($sms_reminder_log, $contact['firstname'] . ' (' . $contact['phonenumber'] . ')');
            }
        }

        if (count($emails_sent) > 0 || $sms_sent) {
            if (count($emails_sent) > 0) {
                $this->log_estimate_activity($id, 'not_expiry_reminder_sent', false, serialize([
                    '<custom_data>' . implode(', ', $emails_sent) . '</custom_data>',
                ]));
            }

            if ($sms_sent) {
                $this->log_estimate_activity($id, 'sms_reminder_sent_to', false, serialize([
                    implode(', ', $sms_reminder_log),
                ]));
            }

            return true;
        }

        return false;
    }

    /**
     * Send estimate to client
     * @param mixed $id estimateid
     * @param string $template email template to sent
     * @param boolean $attachpdf attach estimate pdf or not
     * @return boolean
     */
    public function send_estimate_to_client($id, $template_name = '', $attachpdf = true, $cc = '', $manually = false)
    {
        $estimate = $this->get($id);

        if ($template_name == '') {
            $template_name = $estimate->sent == 0 ?
                'estimate_send_to_customer' :
                'estimate_send_to_customer_already_sent';
        }

        $estimate_number = format_estimate_number($estimate->id);

        $emails_sent = [];
        $send_to     = [];

        // Manually is used when sending the estimate via add/edit area button Save & Send
        if (!DEFINED('CRON') && $manually === false) {
            $send_to = $this->input->post('sent_to');
        } elseif (isset($GLOBALS['scheduled_email_contacts'])) {
            $send_to = $GLOBALS['scheduled_email_contacts'];
        } else {
            $contacts = $this->clients_model->get_contacts(
                $estimate->clientid,
                ['active' => 1, 'estimate_emails' => 1]
            );

            foreach ($contacts as $contact) {
                array_push($send_to, $contact['id']);
            }
        }

        $status_auto_updated = false;
        $status_now          = $estimate->status;

        if (is_array($send_to) && count($send_to) > 0) {
            $i = 0;

            // Auto update status to sent in case when user sends the estimate is with status draft
            if ($status_now == 1) {
                $this->db->where('id', $estimate->id);
                $this->db->update(db_prefix() . 'estimates', [
                    'status' => 2,
                ]);
                $status_auto_updated = true;
            }

            if ($attachpdf) {
                $_pdf_estimate = $this->get($estimate->id);
                set_mailing_constant();
                $pdf = estimate_pdf($_pdf_estimate);

                $attach = $pdf->Output($estimate_number . '.pdf', 'S');
            }

            foreach ($send_to as $contact_id) {
                if ($contact_id != '') {
                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
                    }

                    $contact = $this->clients_model->get_contact($contact_id);

                    if (!$contact) {
                        continue;
                    }

                    $template = mail_template($template_name, $estimate, $contact, $cc);

                    if ($attachpdf) {
                        $hook = hooks()->apply_filters('send_estimate_to_customer_file_name', [
                            'file_name' => str_replace('/', '-', $estimate_number . '.pdf'),
                            'estimate'  => $_pdf_estimate,
                        ]);

                        $template->add_attachment([
                            'attachment' => $attach,
                            'filename'   => $hook['file_name'],
                            'type'       => 'application/pdf',
                        ]);
                    }

                    if ($template->send()) {
                        array_push($emails_sent, $contact->email);
                    }
                }
                $i++;
            }
        } else {
            return false;
        }

        if (count($emails_sent) > 0) {
            $this->set_estimate_sent($id, $emails_sent);
            hooks()->do_action('estimate_sent', $id);

            return true;
        }

        if ($status_auto_updated) {
            // Estimate not send to customer but the status was previously updated to sent now we need to revert back to draft
            $this->db->where('id', $estimate->id);
            $this->db->update(db_prefix() . 'estimates', [
                'status' => 1,
            ]);
        }

        return false;
    }

    /**
     * All estimate activity
     * @param mixed $id estimateid
     * @return array
     */
    public function get_estimate_activity($id)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'estimate');
        $this->db->order_by('date', 'asc');

        return $this->db->get(db_prefix() . 'sales_activity')->result_array();
    }

    /**
     * Log estimate activity to database
     * @param mixed $id estimateid
     * @param string $description activity description
     */
    public function log_estimate_activity($id, $description = '', $client = false, $additional_data = '')
    {
        $staffid   = get_staff_user_id();
        $full_name = get_staff_full_name(get_staff_user_id());
        if (DEFINED('CRON')) {
            $staffid   = '[CRON]';
            $full_name = '[CRON]';
        } elseif ($client == true) {
            $staffid   = null;
            $full_name = '';
        }

        $this->db->insert(db_prefix() . 'sales_activity', [
            'description'     => $description,
            'date'            => date('Y-m-d H:i:s'),
            'rel_id'          => $id,
            'rel_type'        => 'estimate',
            'staffid'         => $staffid,
            'full_name'       => $full_name,
            'additional_data' => $additional_data,
        ]);
    }

    /**
     * Updates pipeline order when drag and drop
     * @param mixe $data $_POST data
     * @return void
     */
    public function update_pipeline($data)
    {
        $this->mark_action_status($data['status'], $data['estimateid']);
        AbstractKanban::updateOrder($data['order'], 'pipeline_order', 'estimates', $data['status']);
    }

    /**
     * Get estimate unique year for filtering
     * @return array
     */
    public function get_estimates_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM ' . db_prefix() . 'estimates ORDER BY year DESC')->result_array();
    }

    private function map_shipping_columns($data)
    {
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = null;
                }
            }
            $data['show_shipping_on_estimate'] = 1;
            $data['include_shipping']          = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_estimate']) && ($data['show_shipping_on_estimate'] == 1 || $data['show_shipping_on_estimate'] == 'on')) {
                $data['show_shipping_on_estimate'] = 1;
            } else {
                $data['show_shipping_on_estimate'] = 0;
            }
        }

        return $data;
    }

    public function do_kanban_query($status, $search = '', $page = 1, $sort = [], $count = false)
    {
        _deprecated_function('Estimates_model::do_kanban_query', '2.9.2', 'EstimatesPipeline class');

        $kanBan = (new EstimatesPipeline($status))
            ->search($search)
            ->page($page)
            ->sortBy($sort['sort'] ?? null, $sort['sort_by'] ?? null);

        if ($count) {
            return $kanBan->countAll();
        }

        return $kanBan->get();
    }

    public function get_co_total_for_estimate($id)
    {
        $co_total = $this->db->query('SELECT SUM(total) as co_total FROM ' . db_prefix() . 'co_orders WHERE estimate = '.$id.' AND approve_status = 2')->result_array();
        return !empty($co_total) ? $co_total[0]['co_total'] : 0;
    }

    public function get_annexure_estimate_details($estimateid, $ignore_management_fess = false)
    {
        $this->db->where('id', $estimateid);
        $estimate = $this->db->get(db_prefix() . 'estimates')->row();
        $items = get_items_by_type('estimate', $estimateid, $ignore_management_fess);

        $total_built_up_area = 1;
        $summary = array();
        $final_estimate = array();

        $all_area_summary = $this->get_area_summary($estimateid);
        if(!empty($all_area_summary)) {
            $total_built_up_area = array_sum(array_column(array_filter($all_area_summary, fn($item) => $item['area_id'] == 2), 'area'));
            $total_built_up_area = $total_built_up_area == 0 ? 1 : $total_built_up_area;
        }

        foreach ($items as $key => $value) {
            $annexure = $value['annexure'];
            $items_group = $this->get_items_groups($annexure);
            $booked_amount = $this->get_estimate_booked_amount($estimateid, $annexure);
            $summary[$annexure]['name'] = $items_group->name;
            $summary[$annexure]['description'] = '';
            $summary[$annexure]['qty'] += $value['qty'];
            $summary[$annexure]['rate'] += $value['rate'];
            $summary[$annexure]['subtotal'] += $value['qty'] * $value['rate'];
            $summary[$annexure]['tax'] = 0;
            $summary[$annexure]['amount'] = $summary[$annexure]['subtotal'] + $summary[$annexure]['tax'];
            $summary[$annexure]['annexure'] = $annexure;
            $summary[$annexure]['total_bua'] = $summary[$annexure]['amount'] / $total_built_up_area;
            $summary[$annexure]['booked_amount'] = $booked_amount;
            $summary[$annexure]['pending_amount'] = $summary[$annexure]['amount'] - $booked_amount;
        }
        $summary = !empty($summary) ? array_values($summary) : array();
        if(!empty($summary)) {
            usort($summary, function($a, $b) {
                return $a['annexure'] <=> $b['annexure'];
            });
            $summary = array_values($summary);
        }

        $summary = !empty($summary) ? array_values($summary) : array();
        if(!empty($summary)) {
            usort($summary, function($a, $b) {
                return $a['annexure'] <=> $b['annexure'];
            });
            $summary = array_values($summary);
        }

        foreach ($summary as $key => $value) {
            $final_estimate['name'] = _l('final_estimate_by_all_annexures');
            $final_estimate['description'] = '';
            $final_estimate['qty'] = 1;
            $final_estimate['subtotal'] += $value['subtotal'];
            $final_estimate['tax'] += $value['tax'];
            $final_estimate['amount'] += $value['amount'];
        }
    
        $response = array();
        $response['summary'] = $summary;
        $response['final_estimate'] = $final_estimate;

        return $response;
    }

    public function get_items_groups($id)
    {
        $all_annexures = get_all_annexures();
        $result = array_filter($all_annexures, function ($item) use ($id) {
            return $item['id'] == $id;
        });
        $result = array_values($result);
        if(!empty($result)) {
            $result = $result[0];
        }
        return (object) $result;
    }

    public function update_basic_estimate_details($estimate_id)
    {
        $annexure_estimate = $this->get_annexure_estimate_details($estimate_id);
        $update_estimate = array();
        $update_estimate['subtotal'] = $annexure_estimate['final_estimate']['subtotal'];
        $update_estimate['total_tax'] = $annexure_estimate['final_estimate']['tax'];
        $update_estimate['total'] = $annexure_estimate['final_estimate']['amount'];
        $this->db->where('id', $estimate_id);
        $this->db->update(db_prefix() . 'estimates', $update_estimate);
        return true;
    }

    public function add_new_area_working_item_post($data, $id)
    {
        $data['estimate_id'] = $id;
        $this->db->insert(db_prefix() . 'costarea_working', $data);
        return true;
    }

    public function get_area_working($id)
    {
        $this->db->where('estimate_id', $id);
        return $this->db->get(db_prefix() . 'costarea_working')->result_array();
    }

    public function update_area_working_item_post($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'costarea_working', $data);
        return true;
    }

    public function delete_area_working_item($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'costarea_working');
        return true;
    }

    public function update_area_statement_tabs($data)
    {
        if(!empty($data)) {
            if(!empty($data['id']) && !empty($data['name'])) {
               $this->db->where('id', $data['id']);
               $this->db->update(db_prefix() . 'area_statement_tabs', ['name' => $data['name']]); 
               if ($this->db->affected_rows() > 0) {
                    return $data['id'];
               }
            }
        }
        return null; 
    }

    public function add_area_statement_tabs($data)
    {
        if(!empty($data)) {
            if(!empty($data['name']) && !empty($data['estimate_id'])) {
                $this->db->insert(db_prefix() . 'area_statement_tabs', $data);
                return $this->db->insert_id();
            }
        }
        return null;
    }

    public function delete_area_statement_tabs($data)
    {
        if(!empty($data)) {
            if(!empty($data['id'])) {
                $this->db->where('id', $data['id']);
                $this->db->delete(db_prefix() . 'area_statement_tabs');
                $this->db->where('area_id', $data['id']);
                $this->db->delete(db_prefix() . 'costarea_working');
                return $data['id'];
            }
        }
        return null;
    }

    public function get_area_summary($id)
    {
        $this->db->where('estimate_id', $id);
        return $this->db->get(db_prefix() . 'costarea_summary')->result_array();
    }

    public function add_new_area_summary_item_post($data, $id)
    {
        $data['estimate_id'] = $id;
        $this->db->insert(db_prefix() . 'costarea_summary', $data);
        return true;
    }

    public function update_area_summary_item_post($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'costarea_summary', $data);
        return true;
    }

    public function delete_area_summary_item($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'costarea_summary');
        return true;
    }

    public function update_estimate_budget_info($id, $estimate_budget_info, $type)
    {
        if(!empty($estimate_budget_info)) {
            foreach ($estimate_budget_info as $ckey => $cvalue) {
                $this->db->where('estimate_id', $id);
                $this->db->where('budget_id', $ckey);
                $estimate_budget_info = $this->db->get(db_prefix() . 'estimate_budget_info')->row();
                if(empty($estimate_budget_info) && !empty($cvalue)) {
                    $this->db->insert(db_prefix() . 'estimate_budget_info', [
                        'estimate_id' => $id,
                        'budget_id' => $ckey,
                        $type => $cvalue,
                    ]);
                } else {
                    $this->db->where('estimate_id', $id);
                    $this->db->where('budget_id', $ckey);
                    $this->db->update(db_prefix() . 'estimate_budget_info', [$type => $cvalue]);
                }
            }
        }
        return true;
    }

    public function get_estimate_budget_info($id)
    {
        $this->db->where('estimate_id', $id);
        return $this->db->get(db_prefix() . 'estimate_budget_info')->result_array();
    }

    public function get_area_summary_tabs()
    {
        return $this->db->get(db_prefix() . 'area_summary_tabs')->result_array();
    }

    public function get_area_statement_tabs($id)
    {
        $this->db->where('estimate_id', $id);
        return $this->db->get(db_prefix() . 'area_statement_tabs')->result_array();
    }

    public function get_cost_planning_details($id)
    {
        $final_result = array();
        $annexure_estimate = array();
        $total_built_up_area = 1;

        $this->db->where('id', $id);
        $estimate = $this->db->get(db_prefix() . 'estimates')->result_array();
        $items = get_items_by_type('estimate', $id);
        $all_area_summary = $this->get_area_summary($id);
        $budget_info = $this->get_estimate_budget_info($id);
        $area_summary_tabs = $this->get_area_summary_tabs();
        $area_statement_tabs = $this->get_area_statement_tabs($id);
        $area_working = $this->get_area_working($id);

        if(!empty($all_area_summary)) {
            $total_built_up_area = array_sum(array_column(array_filter($all_area_summary, fn($item) => $item['area_id'] == 2), 'area'));
            $total_built_up_area = $total_built_up_area == 0 ? 1 : $total_built_up_area;
        }

        foreach ($items as $key => $value) {
            $annexure = $value['annexure'];
            $items_group = $this->get_items_groups($annexure);
            $booked_amount = $this->get_estimate_booked_amount($id, $annexure);
            $annexure_estimate[$annexure]['name'] = $items_group->name;
            $annexure_estimate[$annexure]['description'] = '';
            $annexure_estimate[$annexure]['qty'] += $value['qty'];
            $annexure_estimate[$annexure]['rate'] += $value['rate'];
            $annexure_estimate[$annexure]['subtotal'] += $value['qty'] * $value['rate'];
            $annexure_estimate[$annexure]['tax'] = 0;
            $annexure_estimate[$annexure]['amount'] = $annexure_estimate[$annexure]['subtotal'];
            $annexure_estimate[$annexure]['annexure'] = $annexure;
            $annexure_estimate[$annexure]['total_bua'] = $annexure_estimate[$annexure]['amount'] / $total_built_up_area;
            $annexure_estimate[$annexure]['booked_amount'] = $booked_amount;
            $annexure_estimate[$annexure]['pending_amount'] = $annexure_estimate[$annexure]['amount'] - $booked_amount;
        }
        $annexure_estimate = !empty($annexure_estimate) ? array_values($annexure_estimate) : array();
        if(!empty($annexure_estimate)) {
            usort($annexure_estimate, function($a, $b) {
                return $a['annexure'] <=> $b['annexure'];
            });
            $annexure_estimate = array_values($annexure_estimate);
        }

        $annexure_estimate = !empty($annexure_estimate) ? array_values($annexure_estimate) : array();
        if(!empty($annexure_estimate)) {
            usort($annexure_estimate, function($a, $b) {
                return $a['annexure'] <=> $b['annexure'];
            });
            $annexure_estimate = array_values($annexure_estimate);
        }

        $final_result['estimate_detail'] = $estimate[0];
        $final_result['budget_info'] = $budget_info;
        $final_result['annexure_estimate'] = $annexure_estimate;
        $final_result['area_summary_tabs'] = $area_summary_tabs;
        $final_result['all_area_summary'] = $all_area_summary;
        $final_result['area_statement_tabs'] = $area_statement_tabs;
        $final_result['area_working'] = $area_working;
        $final_result['estimate_items'] = $items;
        
        return $final_result;
    }

    public function get_overall_budget_area($budgetData, $budget_id, $amount) {
        foreach ($budgetData as $entry) {
            if ($entry['budget_id'] == $budget_id) {
                if(!empty($entry['overall_budget_area'])) {
                    return $amount / $entry['overall_budget_area'];
                }
            }
        }
        return 0;
    }

    public function create_new_revision($data) 
    {
        $new_estimate_id = '';
        $id = $data['id'];

        $this->db->where('id', $id);
        $estimates = $this->db->get(db_prefix() . 'estimates')->row();
        if(!empty($estimates)) {
            $estimates = json_decode(json_encode($estimates), true);
            unset($estimates['id']);
            $this->db->insert(db_prefix() . 'estimates', $estimates);
            $new_estimate_id = $this->db->insert_id();

            $area_summary = $this->get_area_summary($id);
            if(!empty($area_summary)) {
                foreach ($area_summary as $key => $value) {
                    unset($value['id']);
                    unset($value['estimate_id']);
                    $value['estimate_id'] = $new_estimate_id;
                    $this->db->insert(db_prefix() . 'costarea_summary', $value);
                }
            }

            $area_statement = $this->get_area_statement_tabs($id);
            if(!empty($area_statement)) {
                foreach ($area_statement as $key => $value) {
                    if(isset($value['id'])) {
                        $old_area_statement_tab_id = $value['id'];
                        unset($value['id']);
                    }
                    unset($value['estimate_id']);
                    $value['estimate_id'] = $new_estimate_id;
                    $this->db->insert(db_prefix() . 'area_statement_tabs', $value);
                    $new_area_statement_tab_id = $this->db->insert_id();

                    $this->db->where('estimate_id', $id);
                    $this->db->where('area_id', $old_area_statement_tab_id);
                    $costarea_working = $this->db->get(db_prefix() . 'costarea_working')->result_array();
                    if(!empty($costarea_working)) {
                        foreach ($costarea_working as $ckey => $cvalue) {
                            unset($cvalue['id']);
                            unset($cvalue['estimate_id']);
                            unset($cvalue['area_id']);
                            $cvalue['estimate_id'] = $new_estimate_id;
                            $cvalue['area_id'] = $new_area_statement_tab_id;
                            $this->db->insert(db_prefix() . 'costarea_working', $cvalue);
                        }
                    }
                }
            }

            $estimate_budget_info = $this->get_estimate_budget_info($id);
            if(!empty($estimate_budget_info)) {
                foreach ($estimate_budget_info as $key => $value) {
                    unset($value['id']);
                    unset($value['estimate_id']);
                    $value['estimate_id'] = $new_estimate_id;
                    $this->db->insert(db_prefix() . 'estimate_budget_info', $value);
                }
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'estimate');
            $itemable = $this->db->get(db_prefix() . 'itemable')->result_array();
            if(!empty($itemable)) {
                foreach ($itemable as $key => $value) {
                    unset($value['id']);
                    unset($value['rel_id']);
                    $value['rel_id'] = $new_estimate_id;
                    $this->db->insert(db_prefix() . 'itemable', $value);
                }
            }

            $this->db->where('estimate_id', $id);
            $unawarded_budget_info = $this->db->get(db_prefix() . 'unawarded_budget_info')->result_array();
            if(!empty($unawarded_budget_info)) {
                foreach ($unawarded_budget_info as $key => $value) {
                    unset($value['id']);
                    unset($value['estimate_id']);
                    $value['estimate_id'] = $new_estimate_id;
                    $this->db->insert(db_prefix() . 'unawarded_budget_info', $value);
                }
            }

            $this->db->where('estimate_id', $id);
            $estimate_package_info = $this->db->get(db_prefix() . 'estimate_package_info')->result_array();
            if(!empty($estimate_package_info)) {
                foreach ($estimate_package_info as $key => $value) {
                    if(isset($value['id'])) {
                        $old_package_id = $value['id'];
                        unset($value['id']);
                    }
                    unset($value['estimate_id']);
                    $value['estimate_id'] = $new_estimate_id;
                    $this->db->insert(db_prefix() . 'estimate_package_info', $value);
                    $new_package_id = $this->db->insert_id();

                    $this->db->where('package_id', $old_package_id);
                    $estimate_package_items_info = $this->db->get(db_prefix() . 'estimate_package_items_info')->result_array();
                    if(!empty($estimate_package_items_info)) {
                        foreach ($estimate_package_items_info as $ckey => $cvalue) {
                            unset($cvalue['id']);
                            unset($cvalue['package_id']);
                            $cvalue['package_id'] = $new_package_id;
                            $this->db->insert(db_prefix() . 'estimate_package_items_info', $cvalue);
                        }
                    }
                }
            }

            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'estimates', ['active' => 0]);

            $this->db->where('id', $new_estimate_id);
            $this->db->update(db_prefix() . 'estimates', ['parent_id' => $id]);
        }

        return $new_estimate_id;
    }

    public function find_awarded_capex($group_pur, $project_id)
    {
    $sql = "SELECT combined_orders.total_rev_contract_value, combined_orders.project_id, combined_orders.group_pur FROM (
            SELECT (po.subtotal + IFNULL(co_sum.co_total, 0)) AS total_rev_contract_value, pr.id AS project_id, po.group_pur 
            FROM tblpur_orders po 
            LEFT JOIN (
                SELECT po_order_id, SUM(co_value) AS co_total
                FROM tblco_orders
                WHERE po_order_id IS NOT NULL
                GROUP BY po_order_id
            ) AS co_sum ON co_sum.po_order_id = po.id
            LEFT JOIN tblprojects pr ON pr.id = po.project
            UNION ALL
            SELECT (wo.subtotal + IFNULL(co_sum.co_total, 0)) AS total_rev_contract_value, pr.id AS project_id, wo.group_pur 
            FROM tblwo_orders wo 
            LEFT JOIN (
                SELECT wo_order_id, SUM(co_value) AS co_total
                FROM tblco_orders
                WHERE wo_order_id IS NOT NULL
                GROUP BY wo_order_id
            ) AS co_sum ON co_sum.wo_order_id = wo.id
            LEFT JOIN tblprojects pr ON pr.id = wo.project
            UNION ALL
            SELECT (t.total + IFNULL(t.co_total, 0)) AS total_rev_contract_value, pr.id AS project_id, t.group_pur 
            FROM tblpur_order_tracker t 
            LEFT JOIN tblprojects pr ON pr.id = t.project
        ) AS combined_orders";
        $conditions = [];
        if (!empty($group_pur)) {
            $conditions[] = "combined_orders.group_pur = '" . $group_pur . "'";
        }
        if (!empty($project_id)) {
            $conditions[] = "combined_orders.project_id = '" . $project_id . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $query = $this->db->query($sql);
        $result = $query->result_array();
        $awarded_capex = 0;
        if(!empty($result)) {
            $awarded_capex = array_sum(array_column($result, 'total_rev_contract_value'));
        }
        return $awarded_capex;
    }

    public function assign_unawarded_capex($data)
    {
        $this->load->model('currencies_model');
        $response = array();
        $budgetsummary = '';
        $itemhtml = '';
        $estimate_id = $data['id'];
        $unawarded_budget = isset($data['unawarded_budget']) ? $data['unawarded_budget'] : '';
        $base_currency = $this->currencies_model->get_base_currency();
        $this->db->where('id', $estimate_id);
        $estimates = $this->db->get(db_prefix() . 'estimates')->row();
        $unawarded_budget_head = $this->get_estimate_budget_listing($estimates->id);
        if(!empty($unawarded_budget_head)) {
            if(empty($unawarded_budget)) {
                $unawarded_budget = isset($unawarded_budget_head[0]) ? $unawarded_budget_head[0]['annexure'] : '';
                $budgetsummaryhtml = '<div class="form-group">';
                $budgetsummaryhtml .= '<label for="unawarded_budget_head" class="control-label">' . _l('Budget Head') . '</label>';
                $budgetsummaryhtml .= '<select name="unawarded_budget_head" class="selectpicker" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '" data-live-search="true">';
                foreach ($unawarded_budget_head as $item) {
                    $selected = ($unawarded_budget == $item['annexure']) ? 'selected' : '';
                    $budgetsummaryhtml .= '<option value="' . $item['annexure'] . '" data-estimateid="' . $estimates->id . '" ' . $selected . '>' . $item['budget_head'] . '</option>';
                }
                $budgetsummaryhtml .= '</select>';
                $budgetsummaryhtml .= '</div>';

            }

            $this->db->select(
                db_prefix() . 'itemable.*,' .
                db_prefix() . 'unawarded_budget_info.unawarded_qty,' .
                db_prefix() . 'unawarded_budget_info.unawarded_rate'
            );
            $this->db->from(db_prefix() . 'itemable');
            $this->db->join(db_prefix() . 'unawarded_budget_info', db_prefix() . 'unawarded_budget_info.item_id = ' . db_prefix() . 'itemable.id', 'left');
            $this->db->where('rel_id', $estimates->id);
            $this->db->where('rel_type', 'estimate');
            $this->db->where('annexure', $unawarded_budget);
            $this->db->group_by(db_prefix() . 'itemable.id');
            $unawarded_budget_itemable = $this->db->get()->result_array();

            if (!empty($unawarded_budget_itemable)) {
                $itemhtml .= '<div class="table-responsive s_table">';
                $itemhtml .= '<table class="table items">';
                $itemhtml .= '<thead>
                    <tr>
                        <th align="left">' . _l('estimate_table_item_heading') . '</th>
                        <th align="left">' . _l('estimate_table_item_description') . '</th>
                        <th align="left">' . _l('sub_groups_pur') . '</th>
                        <th align="left">' . _l('budgeted_qty') . '</th>
                        <th align="left">Remaining Quantity In Budget</th>
                        <th align="left">' . _l('budgeted_rate') . '</th>
                        <th align="left">' . _l('budgeted_amount') . '</th>
                        <th align="left">Unawarded Quantity</th>
                        <th align="left">Unawarded Rate</th>
                        <th align="left">Unawarded Capex</th>
                        <th align="left">Remaining Capex</th>
                    </tr>
                </thead>';
                $itemhtml .= '<tbody style="border: 1px solid #ddd;">';
                $itemhtml .= form_hidden('estimate_id', $estimates->id);
                foreach ($unawarded_budget_itemable as $key => $item) {
                    $non_break_description = strip_tags(str_replace(["\r", "\n", "<br />", "<br/>"], '', $item['long_description']));
                    $this->db->select(db_prefix() . 'pur_order_detail.id as id, ' . db_prefix() . 'pur_order_detail.quantity as quantity, ' . db_prefix() . 'pur_order_detail.total as total');
                    $this->db->select("
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(" . db_prefix() . "pur_order_detail.description, '\r', ''),
                                '\n', ''),
                            '<br />', ''),
                        '<br/>', '') AS non_break_description
                    ");
                    $this->db->from(db_prefix() . 'pur_order_detail');
                    $this->db->join(db_prefix() . 'pur_orders', db_prefix() . 'pur_orders.id = ' . db_prefix() . 'pur_order_detail.pur_order', 'left');
                    $this->db->where(db_prefix() . 'pur_order_detail.item_code', $item['item_code']);
                    $this->db->where(db_prefix() . 'pur_orders.estimate', $estimates->id);
                    $this->db->where(db_prefix() . 'pur_orders.group_pur', $unawarded_budget);
                    $this->db->where(db_prefix() . 'pur_orders.approve_status', 2);
                    $this->db->where(db_prefix() . 'pur_order_detail.quantity' . ' >', 0, false);
                    $this->db->where(db_prefix() . 'pur_order_detail.total' . ' >', 0, false);
                    $this->db->group_by(db_prefix() . 'pur_order_detail.id');
                    $this->db->having('non_break_description', $non_break_description);
                    $pur_order_detail = $this->db->get()->result_array();

                    $budgeted_qty = number_format($item['qty'], 2, '.', '');
                    $budgeted_rate = number_format($item['rate'], 2, '.', '');
                    $budgeted_amount = number_format($budgeted_qty * $budgeted_rate, 2, '.', '');
                    $unawarded_qty = !empty($item['unawarded_qty']) ? $item['unawarded_qty'] : 0.00;
                    $unawarded_qty = number_format($unawarded_qty, 2, '.', '');
                    $unawarded_rate = !empty($item['unawarded_rate']) ? $item['unawarded_rate'] : 0.00;
                    $unawarded_rate = number_format($unawarded_rate, 2, '.', '');
                    $unawarded_amount = number_format($unawarded_qty * $unawarded_rate, 2, '.', '');
                    $remaining_qty_budget = $budgeted_qty;
                    $remaining_capex = 0;
                    if(!empty($pur_order_detail)) {
                        $pur_detail_quantity = 0;
                        $pur_detail_amount = 0;
                        foreach ($pur_order_detail as $srow) {
                            $pur_detail_quantity += (float)$srow['quantity'];
                            $pur_detail_amount += (float)$srow['total'];
                        }
                        $remaining_qty_budget = $budgeted_qty - $pur_detail_quantity;
                        $remaining_qty_budget = number_format($remaining_qty_budget, 2, '.', '');
                    }
                    $remaining_capex = $budgeted_amount - $unawarded_amount;
                    $remaining_capex = number_format($remaining_capex, 2, '.', '');
                    $item_id_name_attr = "newitems[$key][item_id]";
                    $budgeted_qty_name_attr = "newitems[$key][budgeted_qty]";
                    $remaining_qty_budget_name_attr = "newitems[$key][remaining_qty_budget]";
                    $budgeted_unit_name_attr = "newitems[$key][budgeted_unit]";
                    $budgeted_rate_name_attr = "newitems[$key][budgeted_rate]";
                    $budgeted_amount_name_attr = "newitems[$key][budgeted_amount]";
                    $unawarded_qty_name_attr = "newitems[$key][unawarded_qty]";
                    $unawarded_unit_name_attr = "newitems[$key][unawarded_unit]";
                    $unawarded_rate_name_attr = "newitems[$key][unawarded_rate]";
                    $unawarded_amount_name_attr = "newitems[$key][unawarded_amount]";
                    $remaining_capex_name_attr = "newitems[$key][remaining_capex]";

                    $itemhtml .= form_hidden($item_id_name_attr, $item['id']);
                    $itemhtml .= '<tr>';
                    $itemhtml .= '<td align="left">' . get_purchase_items($item['item_code']) . '</td>';
                    $itemhtml .= '<td align="left">' . clear_textarea_breaks($item['long_description']) . '</td>';
                    $itemhtml .= '<td align="left">' . get_sub_head_name_by_id($item['sub_head']) . '</td>';
                    $itemhtml .= '<td class="all_budgeted_qty" style="text-align: left;">
                        <input type="number" 
                           id="' . $budgeted_qty_name_attr . '" 
                           name="' . $budgeted_qty_name_attr . '" 
                           value="' . $budgeted_qty . '" 
                           class="form-control" 
                           readonly>
                        <span style="text-align: left; display: block;">' . 
                            (!empty($item['unit_id']) ? pur_get_unit_name($item['unit_id']) : '') . 
                        '</span>
                    </td>';
                    $itemhtml .= '<td align="left" class="all_remaining_qty_budget">' . render_input($remaining_qty_budget_name_attr, '', $remaining_qty_budget, 'number', ['readonly' => true]) . '</td>';
                    $itemhtml .= '<td align="left" class="all_budgeted_rate">' . render_input($budgeted_rate_name_attr, '', $budgeted_rate, 'number', ['readonly' => true]) . '</td>';
                    $itemhtml .= '<td align="left" class="all_budgeted_amount">' . render_input($budgeted_amount_name_attr, '', $budgeted_amount, 'number', ['readonly' => true]) . '</td>';
                    $itemhtml .= '<td align="left" class="all_unawarded_qty">
                        <input type="number" 
                           id="' . $unawarded_qty_name_attr . '" 
                           name="' . $unawarded_qty_name_attr . '" 
                           value="' . $unawarded_qty . '" 
                           class="form-control" 
                           onchange="calculate_unawarded_capex()" 
                           step="any">
                        <span style="text-align: left; display: block;">' . 
                            (!empty($item['unit_id']) ? pur_get_unit_name($item['unit_id']) : '') . 
                        '</span>
                    </td>';
                    $itemhtml .= '<td align="left" class="all_unawarded_rate">' . render_input($unawarded_rate_name_attr, '', $unawarded_rate, 'number', ['onchange' => 'calculate_unawarded_capex()', 'step' => 'any']) . '</td>';
                    $itemhtml .= '<td align="left" class="all_unawarded_amount">' . render_input($unawarded_amount_name_attr, '', $unawarded_amount, 'number', ['readonly' => true]) . '</td>';
                    $itemhtml .= '<td align="left" class="all_remaining_capex">' . render_input($remaining_capex_name_attr, '', $remaining_capex, 'number', ['readonly' => true]) . '</td>';
                    $itemhtml .= '</tr>';
                }
                $itemhtml .= '</tbody>';
                $itemhtml .= '</table>';
                $itemhtml .= '</div>';
            }
        }

        $itemhtml .= '<div class="col-md-8 col-md-offset-4">
            <table class="table text-right">
                <tbody>
                    <tr>
                        <td><span class="bold tw-text-neutral-700">Total Budgeted Amount :</span>
                        </td>
                        <td class="total_budgeted_amount"></td>
                    </tr>
                    <tr>
                        <td><span class="bold tw-text-neutral-700">Total Unawarded Capex :</span>
                        </td>
                        <td class="total_unawarded_amount"></td>
                    </tr>
                    <tr>
                        <td><span class="bold tw-text-neutral-700">Total Remaining Capex :</span>
                        </td>
                        <td class="total_remaining_capex"></td>
                    </tr>
                </tbody>
            </table>
        </div>';

        $response = ['budgetsummaryhtml' => $budgetsummaryhtml, 'itemhtml' => $itemhtml];
        return $response;
    }

    public function add_assign_unawarded_capex($data)
    {
        $newitems = isset($data['newitems']) ? $data['newitems'] : array();
        $estimate_id = isset($data['estimate_id']) ? $data['estimate_id'] : 0;
        $budget_head = isset($data['unawarded_budget_head']) ? $data['unawarded_budget_head'] : NULL;
        if(!empty($newitems)) {
            foreach ($newitems as $key => $value) {
                $this->db->where('estimate_id', $estimate_id);
                $this->db->where('budget_head', $budget_head);
                $this->db->where('item_id', $value['item_id']);
                $unawarded_budget_info = $this->db->get(db_prefix() . 'unawarded_budget_info')->row();
                if(!empty($unawarded_budget_info)) {
                    $this->db->where('estimate_id', $estimate_id);
                    $this->db->where('budget_head', $budget_head);
                    $this->db->where('item_id', $value['item_id']);
                    $this->db->update(db_prefix() . 'unawarded_budget_info', [
                        'unawarded_qty' => $value['unawarded_qty'],
                        'unawarded_rate' => $value['unawarded_rate'],
                    ]);
                } else {
                    $this->db->insert(db_prefix() . 'unawarded_budget_info', [
                        'estimate_id' => $estimate_id,
                        'budget_head' => $budget_head,
                        'item_id' => $value['item_id'],
                        'unawarded_qty' => $value['unawarded_qty'],
                        'unawarded_rate' => $value['unawarded_rate'],
                    ]);
                }
            }
        }

        return true;
    }

    public function view_package($data)
    {
        $this->load->model('currencies_model');
        $response = array();
        $package_info = array();
        $budgetsummary = '';
        $itemhtml = '';
        $estimate_id = $data['id'];
        $package_budget = isset($data['package_budget']) ? $data['package_budget'] : '';
        $package_id = isset($data['package_id']) ? $data['package_id'] : '';
        $base_currency = $this->currencies_model->get_base_currency();
        if(!empty($package_id)) {
            $this->db->where('id', $package_id);
            $package_info = $this->db->get(db_prefix() . 'estimate_package_info')->row();
        }
        $this->db->where('id', $estimate_id);
        $estimates = $this->db->get(db_prefix() . 'estimates')->row();
        $package_budget_head = $this->get_estimate_budget_listing($estimates->id);
        if(!empty($package_budget_head)) {
            if(empty($package_budget)) {
                $package_budget = isset($package_budget_head[0]) ? $package_budget_head[0]['annexure'] : '';
                if(!empty($package_info)) {
                    $package_budget = $package_info->budget_head;
                }
                $budgetsummaryhtml .= '<div class="row">';
                $budgetsummaryhtml .= '<div class="col-md-12" style="padding-left:0px;">';
                $budgetsummaryhtml .= '<div class="col-md-2 form-group">';
                $budgetsummaryhtml .= '<label for="package_budget_head" class="control-label">' . _l('Budget Head') . '</label>';
                $budgetsummaryhtml .= '<select name="package_budget_head" class="selectpicker" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '" data-live-search="true">';
                foreach ($package_budget_head as $item) {
                    $selected = ($package_budget == $item['annexure']) ? 'selected' : '';
                    $budgetsummaryhtml .= '<option value="' . $item['annexure'] . '" data-estimateid="' . $estimates->id . '" ' . $selected . '>' . $item['budget_head'] . '</option>';
                }
                $budgetsummaryhtml .= '</select>';
                $budgetsummaryhtml .= '</div>';
                $budgetsummaryhtml .= '<div class="col-md-2 form-group">';
                $budgetsummaryhtml .= render_date_input('project_awarded_date', 'Project Awarded Date', !empty($package_info) ? $package_info->project_awarded_date : '');
                $budgetsummaryhtml .= '</div>';
                $budgetsummaryhtml .= '<div class="col-md-2 form-group">';
                $budgetsummaryhtml .= render_input('package_name', 'Package Name', !empty($package_info) ? $package_info->package_name : '');
                $budgetsummaryhtml .= '</div>';
                $budgetsummaryhtml .= '<div class="col-md-2 form-group">';
                $options = [
                    ['id' => 'Client Supply', 'name' => _l('client_supply')],
                    ['id' => 'Bought out items', 'name' => _l('bought_out_items')]
                ];
                $budgetsummaryhtml .= render_select('kind', $options, ['id', 'name'], 'cat', !empty($package_info) ? $package_info->kind : '', ['data-live-search' => 'true', 'data-width' => '100%', 'data-none-selected-text' => _l('ticket_settings_none_assigned')], [], 'selectpicker');
                $budgetsummaryhtml .= '</div>';
                $budgetsummaryhtml .= '<div class="col-md-2 form-group">';
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
                $rli_filter_options = [];
                foreach ($status_labels as $key => $status) {
                    $rli_filter_options[] = ['id' => $key, 'name' => $status['text']];
                }
                $budgetsummaryhtml .= render_select('rli_filter', $rli_filter_options, ['id', 'name'], 'rli_filter', !empty($package_info) ? $package_info->rli_filter : '', ['data-width' => '100%', 'data-none-selected-text' => _l('ticket_settings_none_assigned')], [], 'selectpicker');
                $budgetsummaryhtml .= '</div>';
                $budgetsummaryhtml .= '</div>';
                $budgetsummaryhtml .= '</div>';
            }

            $this->db->select(
                db_prefix() . 'itemable.*,' .
                db_prefix() . 'unawarded_budget_info.unawarded_qty,' .
                db_prefix() . 'unawarded_budget_info.unawarded_rate'
            );
            $this->db->from(db_prefix() . 'itemable');
            $this->db->join(db_prefix() . 'unawarded_budget_info', db_prefix() . 'unawarded_budget_info.item_id = ' . db_prefix() . 'itemable.id', 'left');
            $this->db->where('rel_id', $estimates->id);
            $this->db->where('rel_type', 'estimate');
            $this->db->where('annexure', $package_budget);
            $this->db->group_by(db_prefix() . 'itemable.id');
            $unawarded_budget_itemable = $this->db->get()->result_array();
            $total_budgeted_amount = 0;
            if(!empty($unawarded_budget_itemable)) {
                $total_budgeted_amount = array_reduce($unawarded_budget_itemable, function ($carry, $item) {
                    return $carry + ($item['qty'] * $item['rate']);
                }, 0);
            }

            if (!empty($unawarded_budget_itemable)) {
                $itemhtml .= '<div class="table-responsive s_table">';
                $itemhtml .= '<table class="table items">';
                $itemhtml .= '<thead>
                    <tr>
                        <th width="11%" align="left">' . _l('estimate_table_item_heading') . '</th>
                        <th width="14%" align="left">' . _l('estimate_table_item_description') . '</th>
                        <th width="9%" align="left">' . _l('sub_groups_pur') . '</th>
                        <th width="9%" align="right">Unawarded Quantity</th>
                        <th width="9%" align="right">Unawarded Rate</th>
                        <th width="9%" align="right">Unawarded Amount</th>
                        <th width="9%" align="right">Package Quantity</th>
                        <th width="9%" align="right">Package Rate</th>
                        <th width="9%" align="right">Package Amount</th>
                        <th width="9%" align="right">Remarks</th>
                        <th align="center"><i class="fa fa-cog"></i></th>
                    </tr>
                </thead>';
                $itemhtml .= '<tbody style="border: 1px solid #ddd;">';
                $itemhtml .= form_hidden('estimate_id', $estimates->id);
                $itemhtml .= form_hidden('package_id', $package_id);
                $itemhtml .= '<tr>';
                $itemhtml .= '<td align="left">
                    <select id="item_name" name="item_name" data-selected-id="" class="form-control selectpicker item-select" data-live-search="true" >
                        <option value="">Type at least 3 letters...</option>
                    </select>
                </td>';
                $itemhtml .= '<td class="hide commodity_code">
                    <div class="form-group" app-field-wrapper="item_code">
                        <input type="text" id="item_code" name="item_code" class="form-control" placeholder="Commodity Code" value="">
                    </div>
                </td>';
                $itemhtml .= '<td align="left">
                    <textarea name="long_description" rows="2" class="form-control" placeholder="'._l('item_long_description_placeholder').'">
                    </textarea>
                </td>';
                $itemhtml .= '<td align="left"></td>';
                $itemhtml .= '<td align="right">' . render_input('unawarded_qty', '', 0.00, 'number', ['readonly' => true]) . '</td>';
                $itemhtml .= '<td align="right">' . render_input('unawarded_rate', '', 0.00, 'number', ['readonly' => true]) . '</td>';
                $itemhtml .= '<td align="right">' . render_input('unawarded_amount', '', 0.00, 'number', ['readonly' => true]) . '</td>';
                $itemhtml .= '<td align="right">' . render_input('package_qty', '', 0.00, 'number') . '</td>';
                $itemhtml .= '<td align="right">' . render_input('package_rate', '', 0.00, 'number') . '</td>';
                $itemhtml .= '<td align="right">' . render_input('package_amount', '', 0.00, 'number', ['readonly' => true]) . '</td>';
                $itemhtml .= '<td align="right">' . render_textarea('remarks', '', '', ['rows' => 2]) . '</td>';
                $itemhtml .= '<td align="center">
                    <button type="button" onclick="add_package_item_to_table(\'undefined\',\'undefined\'); return false;"
                        class="btn pull-right btn-primary"><i class="fa fa-check"></i>
                    </button>
                </td>';
                $itemhtml .= '</tr>';
                foreach ($unawarded_budget_itemable as $key => $item) {
                    $package_items_info = array();
                    if(!empty($package_id)) {
                        $this->db->where('package_id', $package_id);
                        $this->db->where('item_id', $item['id']);
                        $package_items_info = $this->db->get(db_prefix() . 'estimate_package_items_info')->row();
                    }
                    $unawarded_qty = !empty($item['unawarded_qty']) ? number_format($item['unawarded_qty'], 2, '.', '') : 0.00;
                    $unawarded_rate = !empty($item['unawarded_rate']) ? number_format($item['unawarded_rate'], 2, '.', '') : 0.00;
                    $unawarded_amount = number_format($unawarded_qty * $unawarded_rate, 2, '.', '');
                    $item_id_name_attr = "items[$key][item_id]";
                    $unawarded_qty_name_attr = "items[$key][unawarded_qty]";
                    $unawarded_unit_name_attr = "items[$key][unawarded_unit]";
                    $unawarded_rate_name_attr = "items[$key][unawarded_rate]";
                    $unawarded_amount_name_attr = "items[$key][unawarded_amount]";
                    $package_qty_name_attr = "items[$key][package_qty]";
                    $package_unit_name_attr = "items[$key][package_unit]";
                    $package_rate_name_attr = "items[$key][package_rate]";
                    $package_amount_name_attr = "items[$key][package_amount]";
                    $package_remarks_name_attr = "items[$key][remarks]";

                    $itemhtml .= form_hidden($item_id_name_attr, $item['id']);
                    $itemhtml .= '<tr class="items">';
                    $itemhtml .= '<td align="left">' . get_purchase_items($item['item_code']) . '</td>';
                    $itemhtml .= '<td align="left">' . clear_textarea_breaks($item['long_description']) . '</td>';
                    $itemhtml .= '<td align="left">' . get_sub_head_name_by_id($item['sub_head']) . '</td>';
                    $itemhtml .= '<td class="all_unawarded_qty" style="text-align: left;">
                        <input type="number" 
                           id="' . $unawarded_qty_name_attr . '" 
                           name="' . $unawarded_qty_name_attr . '" 
                           value="' . $unawarded_qty . '" 
                           class="form-control" 
                           readonly>
                        <span style="text-align: left; display: block;">' . 
                            (!empty($item['unit_id']) ? pur_get_unit_name($item['unit_id']) : '') . 
                        '</span>
                    </td>';
                    $itemhtml .= '<td align="right" class="all_unawarded_rate">' . render_input($unawarded_rate_name_attr, '', $unawarded_rate, 'number', ['readonly' => true]) . '</td>';
                    $itemhtml .= '<td align="right" class="all_unawarded_amount">' . render_input($unawarded_amount_name_attr, '', $unawarded_amount, 'number', ['readonly' => true]) . '</td>';
                    $itemhtml .= '<td align="right" class="all_package_qty">
                        <input type="number" id="' . $package_qty_name_attr . '" 
                           name="' . $package_qty_name_attr . '" 
                           value="' . (!empty($package_items_info) ? $package_items_info->package_qty : '0.00') . '" 
                           class="form-control" 
                           onchange="calculate_package()" 
                           step="any">
                        <span style="text-align: left; display: block;">' . 
                            (!empty($item['unit_id']) ? pur_get_unit_name($item['unit_id']) : '') . 
                        '</span>
                    </td>';
                    $itemhtml .= '<td align="right" class="all_package_rate">' . render_input($package_rate_name_attr, '', !empty($package_items_info) ? $package_items_info->package_rate : 0.00, 'number', ['onchange' => 'calculate_package()', 'step' => 'any']) . '</td>';
                    $itemhtml .= '<td align="right" class="all_package_amount">' . render_input($package_amount_name_attr, '', 0.00, 'number', ['readonly' => true]) . '</td>';
                    $itemhtml .= '<td align="right">' . render_textarea($package_remarks_name_attr, '', !empty($package_items_info) ? $package_items_info->remarks : '', ['rows' => 2]) . '</td>';
                    $itemhtml .= '<td align="center"></td>';
                    $itemhtml .= '</tr>';
                }
                $itemhtml .= '</tbody>';
                $itemhtml .= '</table>';
                $itemhtml .= '</div>';
            }
        }

        $itemhtml .= '<div class="col-md-8 col-md-offset-4">
            <table class="table text-right">
                <tbody>
                    <tr>
                        <td><span class="bold tw-text-neutral-700">Total Budgeted Amount :</span>
                        </td>
                        <td>'.app_format_money($total_budgeted_amount, $base_currency).'</td>
                    </tr>
                    <tr>
                        <td width="75%"><span class="bold tw-text-neutral-700">Total Unawarded Amount :</span>
                        </td>
                        <td width="25%" class="total_unawarded_amount"></td>
                    </tr>
                    <tr>
                        <td width="75%"><span class="bold tw-text-neutral-700">Secured Deposit :</span>
                        </td>
                        <td width="25%">
                            <div class="input-group date">
                                <input type="number" id="sdeposit_percent" name="sdeposit_percent" class="form-control" value="' . (!empty($package_info) ? $package_info->sdeposit_percent : 0) . '" autocomplete="off" min="0" max="100" onchange="calculate_package()">
                                <div class="input-group-addon">
                                    %
                                </div>
                            </div>
                            <div class="sdeposit_value"></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="75%"><span class="bold tw-text-neutral-700">Total Package Amount :</span>
                        </td>
                        <td width="25%" class="total_package"></td>
                    </tr>
                    <tr>
                        <td width="75%"><span class="bold tw-text-neutral-700">Percentage of Capex Used :</span>
                        </td>
                        <td width="25%" class="percentage_of_capex_used"></td>
                    </tr>
                </tbody>
            </table>
        </div>';

        $response = ['budgetsummaryhtml' => $budgetsummaryhtml, 'itemhtml' => $itemhtml];
        return $response;
    }

    public function save_package($data)
    {
        $items = isset($data['items']) ? $data['items'] : array();
        $package_id = isset($data['package_id']) ? $data['package_id'] : NULL;
        $estimate_id = isset($data['estimate_id']) ? $data['estimate_id'] : NULL;
        $budget_head = isset($data['package_budget_head']) ? $data['package_budget_head'] : NULL;
        $project_awarded_date = isset($data['project_awarded_date']) ? $data['project_awarded_date'] : NULL;
        $package_name = isset($data['package_name']) ? $data['package_name'] : NULL;
        $sdeposit_percent = isset($data['sdeposit_percent']) ? $data['sdeposit_percent'] : NULL;
        $sdeposit_value = isset($data['sdeposit_value']) ? $data['sdeposit_value'] : NULL;
        $total_package = isset($data['total_package']) ? $data['total_package'] : NULL;
        $newpackageitems = isset($data['newpackageitems']) ? $data['newpackageitems'] : array();
        $kind = isset($data['kind']) ? $data['kind'] : NULL;
        $rli_filter = isset($data['rli_filter']) ? $data['rli_filter'] : NULL;

        if(!empty($package_id)) {
            $this->db->where('id', $package_id);
            $this->db->update(db_prefix() . 'estimate_package_info', [
                'estimate_id' => $estimate_id,
                'budget_head' => $budget_head,
                'project_awarded_date' => date('Y-m-d', strtotime($project_awarded_date)),
                'package_name' => $package_name,
                'sdeposit_percent' => $sdeposit_percent,
                'sdeposit_value' => $sdeposit_value,
                'total_package' => $total_package,
                'kind' => $kind,
                'rli_filter' => $rli_filter,
            ]);
            if(!empty($items)) {
                foreach ($items as $key => $value) {
                    $this->db->where('package_id', $package_id);
                    $this->db->where('item_id', $value['item_id']);
                    $this->db->update(db_prefix() . 'estimate_package_items_info', [
                        'package_qty' => $value['package_qty'],
                        'package_rate' => $value['package_rate'],
                        'remarks' => $value['remarks'],
                    ]);
                }
            }
        } else {
            $this->db->insert(db_prefix() . 'estimate_package_info', [
                'estimate_id' => $estimate_id,
                'budget_head' => $budget_head,
                'project_awarded_date' => date('Y-m-d', strtotime($project_awarded_date)),
                'package_name' => $package_name,
                'sdeposit_percent' => $sdeposit_percent,
                'sdeposit_value' => $sdeposit_value,
                'total_package' => $total_package,
                'kind' => $kind,
                'rli_filter' => $rli_filter,
            ]);
            $package_id = $this->db->insert_id();
            if(!empty($items)) {
                foreach ($items as $key => $value) {
                    $this->db->insert(db_prefix() . 'estimate_package_items_info', [
                        'package_id' => $package_id,
                        'item_id' => $value['item_id'],
                        'package_qty' => $value['package_qty'],
                        'package_rate' => $value['package_rate'],
                        'remarks' => $value['remarks'],
                    ]);
                }
            }
        }

        if(!empty($newpackageitems)) {
            foreach ($newpackageitems as $key => $value) {
                $this->db->insert(db_prefix() . 'itemable', [
                    'rel_id' => $estimate_id,
                    'rel_type' => 'estimate',
                    'long_description' => $value['long_description'],
                    'annexure' => $budget_head,
                    'item_code' => $value['item_name'],
                ]);
                $itemable_id = $this->db->insert_id();
                $this->db->insert(db_prefix() . 'unawarded_budget_info', [
                    'estimate_id' => $estimate_id,
                    'budget_head' => $budget_head,
                    'item_id' => $itemable_id,
                ]);
                $this->db->insert(db_prefix() . 'estimate_package_items_info', [
                    'package_id' => $package_id,
                    'item_id' => $itemable_id,
                    'package_qty' => $value['package_qty'],
                    'package_rate' => $value['package_rate'],
                    'remarks' => $value['remarks'],
                ]);
            }
        }

        return true;
    }

    public function delete_package($id)
    {
        $affectedRows = 0;
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'estimate_package_info');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('package_id', $id);
        $this->db->delete(db_prefix() . 'estimate_package_items_info');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            return true;
        }
        return false;
    }

    public function get_estimate_budget_listing($estimate_id)
    {
        $budget_listing = array();
        $budget_listing = $this->db->select(
            db_prefix() . 'itemable.id as id, ' .
            db_prefix() . 'itemable.annexure as annexure, ' .
            db_prefix() . 'items_groups.name as budget_head'
        )
        ->from(db_prefix() . 'itemable')
        ->join(
            db_prefix() . 'items_groups', 
            db_prefix() . 'items_groups.id = ' . db_prefix() . 'itemable.annexure', 
            'left'
        )
        ->where('rel_id', $estimate_id)
        ->where('rel_type', 'estimate')
        ->group_by(db_prefix() . 'itemable.annexure')
        ->get()
        ->result_array();
        if(!empty($budget_listing)) {
            usort($budget_listing, function($a, $b) {
                return $a['annexure'] <=> $b['annexure'];
            });
        }
        return $budget_listing;
    }

    public function get_units($id = '')
    {
        if ($id != '') {
            $this->db->where('unit_type_id', $id);
            return $this->db->get(db_prefix() . 'ware_unit_type')->row();
        } else {
            return $this->db->get(db_prefix() . 'ware_unit_type')->result_array();
        }
    }

    public function update_lock_budget($data)
    {
        $id = $data['id'];
        $lock_budget = $data['lock_budget'];
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'lock_budget' => $lock_budget,
        ]);

        return $id;
    }

    public function get_estimate_booked_amount($estimate_id, $budget_head)
    {
        $booked_amount = 0;
        $this->db->select('SUM(' . db_prefix() . 'pur_order_detail.total) as total');
        $this->db->from(db_prefix() . 'pur_order_detail');
        $this->db->join(
            db_prefix() . 'pur_orders',
            db_prefix() . 'pur_orders.id = ' . db_prefix() . 'pur_order_detail.pur_order',
            'left'
        );
        $this->db->where(db_prefix() . 'pur_orders.estimate', $estimate_id);
        $this->db->where(db_prefix() . 'pur_orders.group_pur', $budget_head);
        $this->db->where(db_prefix() . 'pur_orders.approve_status', 2);
        $this->db->where(db_prefix() . 'pur_order_detail.quantity >', 0, false);
        $this->db->where(db_prefix() . 'pur_order_detail.total >', 0, false);
        $result = $this->db->get()->row();
        $booked_amount = isset($result->total) ? $result->total : 0;
        return $booked_amount;
    }

}
