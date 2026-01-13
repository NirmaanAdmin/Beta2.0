<?php

use app\services\utilities\Arr;

defined('BASEPATH') or exit('No direct script access allowed');

class Misc_model extends App_Model
{
    public $notifications_limit;

    public function __construct()
    {
        parent::__construct();
        $this->notifications_limit = 15;
    }

    public function get_notifications_limit()
    {
        return hooks()->apply_filters('notifications_limit', $this->notifications_limit);
    }

    public function get_taxes_dropdown_template($name, $taxname, $type = '', $item_id = '', $is_edit = false, $manual = false, $disable_dropdown = false)
    {
        // if passed manually - like in proposal convert items or project
        if ($manual == true) {
            // + is no longer used and is here for backward compatibilities
            if (is_array($taxname) || strpos($taxname, '+') !== false) {
                if (!is_array($taxname)) {
                    $__tax = explode('+', $taxname);
                } else {
                    $__tax = $taxname;
                }
                // Multiple taxes found // possible option from default settings when invoicing project
                $taxname = [];
                foreach ($__tax as $t) {
                    $tax_array = explode('|', $t);
                    if (isset($tax_array[0]) && isset($tax_array[1])) {
                        array_push($taxname, $tax_array[0] . '|' . $tax_array[1]);
                    }
                }
            } else {
                $tax_array = explode('|', $taxname);
                // isset tax rate
                if (isset($tax_array[0]) && isset($tax_array[1])) {
                    $tax = get_tax_by_name($tax_array[0]);
                    if ($tax) {
                        $taxname = $tax->name . '|' . $tax->taxrate;
                    }
                }
            }
        }
        // First get all system taxes
        $this->load->model('taxes_model');
        $taxes = $this->taxes_model->get();
        $i     = 0;
        foreach ($taxes as $tax) {
            unset($taxes[$i]['id']);
            $taxes[$i]['name'] = $tax['name'] . '|' . $tax['taxrate'];
            $i++;
        }
        if ($is_edit == true) {

            // Lets check the items taxes in case of changes.
            // Separate functions exists to get item taxes for Invoice, Estimate, Proposal, Credit Note
            $func_taxes = 'get_' . $type . '_item_taxes';
            if (function_exists($func_taxes)) {
                $item_taxes = call_user_func($func_taxes, $item_id);
            }

            foreach ($item_taxes as $item_tax) {
                $new_tax            = [];
                $new_tax['name']    = $item_tax['taxname'];
                $new_tax['taxrate'] = $item_tax['taxrate'];
                $taxes[]            = $new_tax;
            }
        }

        // In case tax is changed and the old tax is still linked to estimate/proposal when converting
        // This will allow the tax that don't exists to be shown on the dropdowns too.
        if (is_array($taxname)) {
            foreach ($taxname as $tax) {
                // Check if tax empty
                if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['taxname'] == '') {
                    continue;
                };
                // Check if really the taxname NAME|RATE don't exists in all taxes
                if (!value_exists_in_array_by_key($taxes, 'name', $tax)) {
                    if (!is_array($tax)) {
                        $tmp_taxname = $tax;
                        $tax_array   = explode('|', $tax);
                    } else {
                        $tax_array   = explode('|', $tax['taxname']);
                        $tmp_taxname = $tax['taxname'];
                        if ($tmp_taxname == '') {
                            continue;
                        }
                    }
                    $taxes[] = ['name' => $tmp_taxname, 'taxrate' => $tax_array[1]];
                }
            }
        }

        // Clear the duplicates
        $taxes = Arr::uniqueByKey($taxes, 'name');
        $disable = $disable_dropdown ? 'disabled' : '';

        $select = '<select class="selectpicker display-block tax" data-width="100%" name="' . $name . '" multiple data-none-selected-text="' . _l('no_tax') . '" ' . $disable . '>';

        foreach ($taxes as $tax) {
            $selected = '';
            if (is_array($taxname)) {
                foreach ($taxname as $_tax) {
                    if (is_array($_tax)) {
                        if ($_tax['taxname'] == $tax['name']) {
                            $selected = 'selected';
                        }
                    } else {
                        if ($_tax == $tax['name']) {
                            $selected = 'selected';
                        }
                    }
                }
            } else {
                if ($taxname == $tax['name']) {
                    $selected = 'selected';
                }
            }

            $select .= '<option value="' . $tax['name'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
        }
        $select .= '</select>';

        return $select;
    }

    public function add_attachment_to_database($rel_id, $rel_type, $attachment, $external = false)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['rel_id']    = $rel_id;
        if (!isset($attachment[0]['staffid'])) {
            $data['staffid'] = get_staff_user_id();
        } else {
            $data['staffid'] = $attachment[0]['staffid'];
        }

        if (isset($attachment[0]['task_comment_id'])) {
            $data['task_comment_id'] = $attachment[0]['task_comment_id'];
        }

        $data['rel_type'] = $rel_type;

        if (isset($attachment[0]['contact_id'])) {
            $data['contact_id']          = $attachment[0]['contact_id'];
            $data['visible_to_customer'] = 1;
            if (isset($data['staffid'])) {
                unset($data['staffid']);
            }
        }

        $data['attachment_key'] = app_generate_hash();

        if ($external == false) {
            $data['file_name'] = $attachment[0]['file_name'];
            $data['filetype']  = $attachment[0]['filetype'];
        } else {
            $path_parts            = pathinfo($attachment[0]['name']);
            $data['file_name']     = $attachment[0]['name'];
            $data['external_link'] = $attachment[0]['link'];
            $data['filetype']      = !isset($attachment[0]['mime']) ? get_mime_by_extension('.' . $path_parts['extension']) : $attachment[0]['mime'];
            $data['external']      = $external;
            if (isset($attachment[0]['thumbnailLink'])) {
                $data['thumbnail_link'] = $attachment[0]['thumbnailLink'];
            }
        }

        $this->db->insert(db_prefix() . 'files', $data);
        $insert_id = $this->db->insert_id();

        if ($data['rel_type'] == 'customer' && isset($data['contact_id'])) {
            if (get_option('only_own_files_contacts') == 1) {
                $this->db->insert(db_prefix() . 'shared_customer_files', [
                    'file_id'    => $insert_id,
                    'contact_id' => $data['contact_id'],
                ]);
            } else {
                $this->db->select('id');
                $this->db->where('userid', $data['rel_id']);
                $contacts = $this->db->get(db_prefix() . 'contacts')->result_array();
                foreach ($contacts as $contact) {
                    $this->db->insert(db_prefix() . 'shared_customer_files', [
                        'file_id'    => $insert_id,
                        'contact_id' => $contact['id'],
                    ]);
                }
            }
        }

        return $insert_id;
    }

    public function get_file($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'files')->row();
    }

    public function get_staff_started_timers()
    {
        $this->db->select(db_prefix() . 'taskstimers.*,' . db_prefix() . 'tasks.name as task_subject');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'taskstimers.staff_id');
        $this->db->join(db_prefix() . 'tasks', db_prefix() . 'tasks.id=' . db_prefix() . 'taskstimers.task_id', 'left');
        $this->db->where('staff_id', get_staff_user_id());
        $this->db->where('end_time IS NULL');

        return $this->db->get(db_prefix() . 'taskstimers')->result_array();
    }

    /**
     * Add reminder
     * @since  Version 1.0.2
     * @param mixed $data All $_POST data for the reminder
     * @param mixed $id   relid id
     * @return boolean
     */
    public function add_reminder($data, $id)
    {
        if (isset($data['notify_by_email'])) {
            $data['notify_by_email'] = 1;
        } //isset($data['notify_by_email'])
        else {
            $data['notify_by_email'] = 0;
        }
        $data['date']        = to_sql_date($data['date'], true);
        $data['description'] = nl2br($data['description']);
        $data['creator']     = get_staff_user_id();
        $this->db->insert(db_prefix() . 'reminders', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if ($data['rel_type'] == 'lead') {
                $this->load->model('leads_model');
                $this->leads_model->log_lead_activity($data['rel_id'], 'not_activity_new_reminder_created', false, serialize([
                    get_staff_full_name($data['staff']),
                    _dt($data['date']),
                ]));
            }
            log_activity('New Reminder Added [' . ucfirst($data['rel_type']) . 'ID: ' . $data['rel_id'] . ' Description: ' . $data['description'] . ']');

            return true;
        } //$insert_id
        return false;
    }

    public function edit_reminder($data, $id)
    {
        if (isset($data['notify_by_email'])) {
            $data['notify_by_email'] = 1;
        } else {
            $data['notify_by_email'] = 0;
        }

        $data['date']        = to_sql_date($data['date'], true);
        $data['description'] = nl2br($data['description']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'reminders', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function get_notes($rel_id, $rel_type)
    {
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'notes.addedfrom');
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->order_by('dateadded', 'desc');

        $notes = $this->db->get(db_prefix() . 'notes')->result_array();

        return hooks()->apply_filters('get_notes', $notes, ['rel_id' => $rel_id, 'rel_type' => $rel_type]);
    }

    public function add_note($data, $rel_type, $rel_id)
    {
        $data['dateadded']   = date('Y-m-d H:i:s');
        $data['addedfrom']   = get_staff_user_id();
        $data['rel_type']    = $rel_type;
        $data['rel_id']      = $rel_id;
        $data['description'] = nl2br($data['description']);

        $data = hooks()->apply_filters('create_note_data', $data, $rel_type, $rel_id);

        $this->db->insert(db_prefix() . 'notes', $data);
        $insert_id = $this->db->insert_id();
        add_order_notes_activity_log($insert_id, true);

        if ($insert_id) {
            hooks()->do_action('note_created', $insert_id, $data);

            return $insert_id;
        }

        return false;
    }

    public function edit_note($data, $id)
    {
        hooks()->do_action('before_update_note', [
            'data' => $data,
            'id'   => $id,
        ]);

        $this->db->where('id', $id);
        $notes = $this->db->get(db_prefix() . 'notes')->row();
        update_order_notes_activity_log($id, $notes->description, $data['description']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'notes', $data = [
            'description' => nl2br($data['description']),
        ]);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('note_updated', $id, $data);

            return true;
        }

        return false;
    }

    public function get_activity_log($limit = 30)
    {
        $this->db->limit($limit);
        $this->db->order_by('date', 'desc');

        return $this->db->get(db_prefix() . 'activity_log')->result_array();
    }

    public function delete_note($note_id)
    {
        add_order_notes_activity_log($note_id, false);
        hooks()->do_action('before_delete_note', $note_id);

        $this->db->where('id', $note_id);
        $note = $this->db->get(db_prefix() . 'notes')->row();

        if ($note->addedfrom != get_staff_user_id() && !is_admin()) {
            return false;
        }

        $this->db->where('id', $note_id);
        $this->db->delete(db_prefix() . 'notes');
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('note_deleted', $note_id, $note);

            return true;
        }

        return false;
    }

    /**
     * Get all reminders or 1 reminder if id is passed
     * @since Version 1.0.2
     * @param  mixed $id reminder id OPTIONAL
     * @return array or object
     */
    public function get_reminders($id = '')
    {
        $this->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'reminders.staff', 'left');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'reminders.id', $id);

            return $this->db->get(db_prefix() . 'reminders')->row();
        } //is_numeric($id)
        $this->db->order_by('date', 'desc');

        return $this->db->get(db_prefix() . 'reminders')->result_array();
    }

    /**
     * Remove client reminder from database
     * @since Version 1.0.2
     * @param  mixed $id reminder id
     * @return boolean
     */
    public function delete_reminder($id)
    {
        $reminder = $this->get_reminders($id);
        if ($reminder->creator == get_staff_user_id() || is_admin()) {
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'reminders');
            if ($this->db->affected_rows() > 0) {
                log_activity('Reminder Deleted [' . ucfirst($reminder->rel_type) . 'ID: ' . $reminder->id . ' Description: ' . $reminder->description . ']');

                return true;
            } //$this->db->affected_rows() > 0
            return false;
        } //$reminder->creator == get_staff_user_id() || is_admin()
        return false;
    }

    public function get_tasks_distinct_assignees()
    {
        return $this->db->query('SELECT DISTINCT(' . db_prefix() . "task_assigned.staffid) as assigneeid, CONCAT(firstname,' ',lastname) as full_name FROM " . db_prefix() . 'task_assigned JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'task_assigned.staffid')->result_array();
    }

    public function get_google_calendar_ids()
    {
        $is_admin = is_admin();
        $this->load->model('departments_model');
        $departments       = $this->departments_model->get();
        $staff_departments = $this->departments_model->get_staff_departments(false, true);
        $ids               = [];

        // Check departments google calendar ids
        foreach ($departments as $department) {
            if ($department['calendar_id'] == '') {
                continue;
            }
            if ($is_admin) {
                $ids[] = $department['calendar_id'];
            } else {
                if (in_array($department['departmentid'], $staff_departments)) {
                    $ids[] = $department['calendar_id'];
                }
            }
        }

        // Ok now check if main calendar is setup
        $main_id_calendar = get_option('google_calendar_main_calendar');
        if ($main_id_calendar != '') {
            $ids[] = $main_id_calendar;
        }

        return array_unique($ids);
    }

    /**
     * Get current user notifications
     * @param  boolean $read include and readed notifications
     * @return array
     */
    public function get_user_notifications($read = false)
    {
        $read     = $read == false ? 0 : 1;
        $total    = $this->notifications_limit;
        $staff_id = get_staff_user_id();

        $sql = 'SELECT COUNT(*) as total FROM ' . db_prefix() . 'notifications WHERE isread=' . $read . ' AND touserid=' . $staff_id;
        $sql .= ' UNION ALL ';
        $sql .= 'SELECT COUNT(*) as total FROM ' . db_prefix() . 'notifications WHERE isread_inline=' . $read . ' AND touserid=' . $staff_id;

        $res = $this->db->query($sql)->result();

        $total_unread        = $res[0]->total;
        $total_unread_inline = $res[1]->total;

        if ($total_unread > $total) {
            $total = ($total_unread - $total) + $total;
        } elseif ($total_unread_inline > $total) {
            $total = ($total_unread_inline - $total) + $total;
        }

        // In case user is not marking the notifications are read this process may be long because the script will always fetch the total from the not read notifications.
        // In this case we are limiting to 30
        $total = $total > 30 ? 30 : $total;

        $this->db->where('touserid', $staff_id);
        $this->db->limit($total);
        $this->db->order_by('date', 'desc');

        return $this->db->get(db_prefix() . 'notifications')->result_array();
    }

    /**
     * Set notification read when user open notification dropdown
     * @return boolean
     */
    public function set_notifications_read()
    {
        $this->db->where('touserid', get_staff_user_id());
        $this->db->update(db_prefix() . 'notifications', [
            'isread' => 1,
        ]);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function set_notification_read_inline($id)
    {
        $this->db->where('touserid', get_staff_user_id());
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'notifications', [
            'isread_inline' => 1,
        ]);
    }

    public function set_desktop_notification_read($id)
    {
        $this->db->where('touserid', get_staff_user_id());
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'notifications', [
            'isread'        => 1,
            'isread_inline' => 1,
        ]);
    }

    public function mark_all_notifications_as_read_inline()
    {
        $this->db->where('touserid', get_staff_user_id());
        $this->db->update(db_prefix() . 'notifications', [
            'isread_inline' => 1,
            'isread'        => 1,
        ]);
    }

    /**
     * Dismiss announcement
     * @param  array  $data  announcement data
     * @param  boolean $staff is staff or client
     * @return boolean
     */
    public function dismiss_announcement($id, $staff = true)
    {
        if ($staff == false) {
            $userid = get_contact_user_id();
        } //$staff == false
        else {
            $userid = get_staff_user_id();
        }
        $data['announcementid'] = $id;
        $data['userid']         = $userid;
        $data['staff']          = $staff;
        $this->db->insert(db_prefix() . 'dismissed_announcements', $data);

        return true;
    }

    /**
     * Perform search on top header
     * @since  Version 1.0.1
     * @param  string $q search
     * @return array    search results
     */
    public function perform_search($q)
    {
        $q = trim($q);
        $this->load->model('staff_model');
        $is_admin                       = is_admin();
        $result                         = [];
        $limit                          = get_option('limit_top_search_bar_results_to');
        $have_assigned_customers        = have_assigned_customers();
        $have_permission_customers_view = staff_can('view',  'customers');

        $staff_search = $this->_search_staff($q, $limit);
        if (count($staff_search['result']) > 0) {
            $result[] = $staff_search;
        }

        $clients_search = $this->_search_clients($q, $limit);
        if (count($clients_search['result']) > 0) {
            $result[] = $clients_search;
        }

        $where_contacts = '';
        if ($have_assigned_customers && !$have_permission_customers_view) {
            $where_contacts = db_prefix() . 'contacts.userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
        }

        $contacts_search = $this->_search_contacts($q, $limit, $where_contacts);
        if (count($contacts_search['result']) > 0) {
            $result[] = $contacts_search;
        }

        $projects_search = $this->_search_projects($q, $limit);
        if (count($projects_search['result']) > 0) {
            $result[] = $projects_search;
        }

        $estimates_search = $this->_search_estimates($q, $limit);
        if (count($estimates_search['result']) > 0) {
            $result[] = $estimates_search;
        }

        $estimate_items_search = $this->_search_estimate_items($q, $limit);
        if (count($estimate_items_search['result']) > 0) {
            $result[] = $estimate_items_search;
        }

        $estimate_commodity_groups_search = $this->_search_estimate_commodity_groups($q, $limit);
        if (count($estimate_commodity_groups_search['result']) > 0) {
            $result[] = $estimate_commodity_groups_search;
        }

        $estimate_sub_groups_search = $this->_search_estimate_sub_groups($q, $limit);
        if (count($estimate_sub_groups_search['result']) > 0) {
            $result[] = $estimate_sub_groups_search;
        }

        $estimate_master_areas_search = $this->_search_estimate_master_areas($q, $limit);
        if (count($estimate_master_areas_search['result']) > 0) {
            $result[] = $estimate_master_areas_search;
        }

        $estimate_functionality_areas_search = $this->_search_estimate_functionality_areas($q, $limit);
        if (count($estimate_functionality_areas_search['result']) > 0) {
            $result[] = $estimate_functionality_areas_search;
        }

        $items_search = $this->_search_items($q, $limit);
        if (count($items_search['result']) > 0) {
            $result[] = $items_search;
        }

        $vendors_search = $this->_search_vendors($q, $limit);
        if (count($vendors_search['result']) > 0) {
            $result[] = $vendors_search;
        }

        $vendor_contacts_search = $this->_search_vendor_contacts($q, $limit);
        if (count($vendor_contacts_search['result']) > 0) {
            $result[] = $vendor_contacts_search;
        }

        $unawarded_trackers_search = $this->_search_unawarded_trackers($q, $limit);
        if (count($unawarded_trackers_search['result']) > 0) {
            $result[] = $unawarded_trackers_search;
        }

        $purchase_requests_search = $this->_search_purchase_requests($q, $limit);
        if (count($purchase_requests_search['result']) > 0) {
            $result[] = $purchase_requests_search;
        }

        $purchase_request_items_search = $this->_search_purchase_request_items($q, $limit);
        if (count($purchase_request_items_search['result']) > 0) {
            $result[] = $purchase_request_items_search;
        }

        $quotations_search = $this->_search_quotations($q, $limit);
        if (count($quotations_search['result']) > 0) {
            $result[] = $quotations_search;
        }

        $purchase_orders_search = $this->_search_purchase_orders($q, $limit);
        if (count($purchase_orders_search['result']) > 0) {
            $result[] = $purchase_orders_search;
        }

        $purchase_order_items_search = $this->_search_purchase_order_items($q, $limit);
        if (count($purchase_order_items_search['result']) > 0) {
            $result[] = $purchase_order_items_search;
        }

        $work_orders_search = $this->_search_work_orders($q, $limit);
        if (count($work_orders_search['result']) > 0) {
            $result[] = $work_orders_search;
        }

        $work_order_items_search = $this->_search_work_order_items($q, $limit);
        if (count($work_order_items_search['result']) > 0) {
            $result[] = $work_order_items_search;
        }

        $payment_certificate_search = $this->_search_payment_certificate($q, $limit);
        if (count($payment_certificate_search['result']) > 0) {
            $result[] = $payment_certificate_search;
        }

        $pur_bills_search = $this->_search_pur_bills($q, $limit);
        if (count($pur_bills_search['result']) > 0) {
            $result[] = $pur_bills_search;
        }

        $pur_bill_items_search = $this->_search_pur_bill_items($q, $limit);
        if (count($pur_bill_items_search['result']) > 0) {
            $result[] = $pur_bill_items_search;
        }

        $order_tracker_search = $this->_search_order_tracker($q, $limit);
        if (count($order_tracker_search['result']) > 0) {
            $result[] = $order_tracker_search;
        }

        $purchase_tracker_search = $this->_search_purchase_tracker($q, $limit);
        if (count($purchase_tracker_search['result']) > 0) {
            $result[] = $purchase_tracker_search;
        }

        $invoices_search = $this->_search_invoices($q, $limit);
        if (count($invoices_search['result']) > 0) {
            $result[] = $invoices_search;
        }

        $invoice_items_search = $this->_search_invoice_items($q, $limit);
        if (count($invoice_items_search['result']) > 0) {
            $result[] = $invoice_items_search;
        }

        $payments_search = $this->_search_payments($q, $limit);
        if (count($payments_search['result']) > 0) {
            $result[] = $payments_search;
        }

        $pur_invoices_search = $this->_search_pur_invoices($q, $limit);
        if (count($pur_invoices_search['result']) > 0) {
            $result[] = $pur_invoices_search;
        }

        $pur_invoice_payments_search = $this->_search_pur_invoice_payments($q, $limit);
        if (count($pur_invoice_payments_search['result']) > 0) {
            $result[] = $pur_invoice_payments_search;
        }

        $debit_notes_search = $this->_search_debit_notes($q, $limit);
        if (count($debit_notes_search['result']) > 0) {
            $result[] = $debit_notes_search;
        }

        $debit_note_items_search = $this->_search_debit_note_items($q, $limit);
        if (count($debit_note_items_search['result']) > 0) {
            $result[] = $debit_note_items_search;
        }

        $credit_notes_search = $this->_search_credit_notes($q, $limit);
        if (count($credit_notes_search['result']) > 0) {
            $result[] = $credit_notes_search;
        }

        $credit_note_items_search = $this->_search_credit_note_items($q, $limit);
        if (count($credit_note_items_search['result']) > 0) {
            $result[] = $credit_note_items_search;
        }

        $change_orders_search = $this->_search_change_orders($q, $limit);
        if (count($change_orders_search['result']) > 0) {
            $result[] = $change_orders_search;
        }

        $change_order_items_search = $this->_search_change_order_items($q, $limit);
        if (count($change_order_items_search['result']) > 0) {
            $result[] = $change_order_items_search;
        }

        $stock_import_search = $this->_search_stock_import($q, $limit);
        if (count($stock_import_search['result']) > 0) {
            $result[] = $stock_import_search;
        }

        $stock_import_items_search = $this->_search_stock_import_items($q, $limit);
        if (count($stock_import_items_search['result']) > 0) {
            $result[] = $stock_import_items_search;
        }

        $stock_export_search = $this->_search_stock_export($q, $limit);
        if (count($stock_export_search['result']) > 0) {
            $result[] = $stock_export_search;
        }

        $stock_export_items_search = $this->_search_stock_export_items($q, $limit);
        if (count($stock_export_items_search['result']) > 0) {
            $result[] = $stock_export_items_search;
        }

        $internal_delivery_note_search = $this->_search_internal_delivery_note($q, $limit);
        if (count($internal_delivery_note_search['result']) > 0) {
            $result[] = $internal_delivery_note_search;
        }

        $loss_adjustment_search = $this->_search_loss_adjustment($q, $limit);
        if (count($loss_adjustment_search['result']) > 0) {
            $result[] = $loss_adjustment_search;
        }

        $expenses_search = $this->_search_expenses($q, $limit);
        if (count($expenses_search['result']) > 0) {
            $result[] = $expenses_search;
        }

        $tasks_search = $this->_search_tasks($q, $limit);
        if (count($tasks_search['result']) > 0) {
            $result[] = $tasks_search;
        }

        $tickets_search = $this->_search_tickets($q, $limit);
        if (count($tickets_search['result']) > 0) {
            $result[] = $tickets_search;
        }

        $contracts_search = $this->_search_contracts($q, $limit);
        if (count($contracts_search['result']) > 0) {
            $result[] = $contracts_search;
        }

        $custom_fields_search = $this->_search_custom_fields($q, $limit);
        if (count($custom_fields_search['result']) > 0) {
            $result[] = $custom_fields_search;
        }

        $leads_search = $this->_search_leads($q, $limit);
        if (count($leads_search['result']) > 0) {
            $result[] = $leads_search;
        }

        $proposals_search = $this->_search_proposals($q, $limit);
        if (count($proposals_search['result']) > 0) {
            $result[] = $proposals_search;
        }

        $knowledge_base_search = $this->_search_knowledge_base($q, $limit);
        if (count($knowledge_base_search['result']) > 0) {
            $result[] = $knowledge_base_search;
        }

        $result = hooks()->apply_filters('global_search_result_query', $result, $q, $limit);

        return $result;
    }

    public function _search_staff($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'staff',
            'search_heading' => _l('staff_members'),
        ];
        if (staff_can('view',  'staff')) {
            $this->db->select();
            $this->db->from(db_prefix() . 'staff');
            $this->db->like('firstname', $q);
            $this->db->or_like('lastname', $q);
            $this->db->or_like("CONCAT(firstname, ' ', lastname)", $q, false);
            $this->db->or_like("CONCAT(lastname, ' ', firstname)", $q, false);
            $this->db->or_like('facebook', $q);
            $this->db->or_like('linkedin', $q);
            $this->db->or_like('phonenumber', $q);
            $this->db->or_like('email', $q);
            $this->db->or_like('skype', $q);
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('firstname', 'ASC');
            $this->db->group_by('staffid');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_clients($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'clients',
            'search_heading' => _l('clients'),
        ];
        $have_assigned_customers        = have_assigned_customers();
        $have_permission_customers_view = staff_can('view',  'customers');
        if ($have_assigned_customers || $have_permission_customers_view) {
            $this->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'clients')) . ',' . get_sql_select_client_company());
            $this->db->join(db_prefix() . 'countries', db_prefix() . 'countries.country_id = ' . db_prefix() . 'clients.country', 'left');
            $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid AND is_primary = 1', 'left');
            $this->db->from(db_prefix() . 'clients');
            if ($have_assigned_customers && !$have_permission_customers_view) {
                $this->db->where(db_prefix() . 'clients.userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')');
            }
            $this->db->where('(company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'clients.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'contacts.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR CONCAT(firstname, \' \', lastname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR CONCAT(lastname, \' \', firstname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'countries.short_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'countries.long_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'countries.numcode LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            )');
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->group_by(db_prefix() . 'clients.userid');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_contacts($q, $limit = 0, $where = '')
    {
        $result = [
            'result'         => [],
            'type'           => 'contacts',
            'search_heading' => _l('customer_contacts'),
        ];
        $have_assigned_customers        = have_assigned_customers();
        $have_permission_customers_view = staff_can('view',  'customers');
        $tickets_contacts = $this->input->post('tickets_contacts') && get_option('staff_members_open_tickets_to_all_contacts') == 1;
        if ($have_assigned_customers || $have_permission_customers_view || $tickets_contacts) {
            $this->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'contacts')) . ',company');
            $this->db->from(db_prefix() . 'contacts');
            $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.userid=' . db_prefix() . 'contacts.userid', 'left');
            $this->db->where('(firstname LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR lastname LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR CONCAT(firstname, \' \', lastname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR CONCAT(lastname, \' \', firstname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'contacts.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'contacts.title LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            )');
            if ($where != '') {
                $this->db->where($where);
            }
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('firstname', 'ASC');
            $this->db->group_by(db_prefix() . 'contacts.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_projects($q, $limit = 0, $where = false)
    {
        $result = [
            'result'         => [],
            'type'           => 'projects',
            'search_heading' => _l('projects'),
        ];
        $projects = staff_can('view',  'projects');
        $this->db->select();
        $this->db->from(db_prefix() . 'projects');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid');
        if (!$projects) {
            $this->db->where(db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
        }
        if ($where != false) {
            $this->db->where($where);
        }
        if (!startsWith($q, '#')) {
            $this->db->where('(company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            )');
        } else {
            $this->db->where('id IN
                (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . $this->db->escape_str(strafter($q, '#')) . '")
                AND ' . db_prefix() . 'taggables.rel_type=\'project\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
            ');
        }
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $this->db->order_by('name', 'ASC');
        $this->db->group_by(db_prefix() . 'projects.id');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_estimates($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'estimates',
            'search_heading' => _l('project_budget'),
        ];
        $has_permission_view_estimates     = staff_can('view',  'estimates');
        $has_permission_view_estimates_own = staff_can('view_own',  'estimates');
        if ($has_permission_view_estimates || $has_permission_view_estimates_own || get_option('allow_staff_view_estimates_assigned') == '1') {
            if (is_numeric($q)) {
                $q = trim($q);
                $q = ltrim($q, '0');
            } elseif (startsWith($q, get_option('estimate_prefix'))) {
                $q = strafter($q, get_option('estimate_prefix'));
                $q = trim($q);
                $q = ltrim($q, '0');
            }
            $estimates_fields  = prefixed_table_fields_array(db_prefix() . 'estimates');
            $clients_fields    = prefixed_table_fields_array(db_prefix() . 'clients');
            $noPermissionQuery = get_estimates_where_sql_for_staff(get_staff_user_id());
            $default_project = get_default_project();
            $this->db->select(implode(',', $estimates_fields) . ',' . implode(',', $clients_fields) . ',' . db_prefix() . 'estimates.id as estimateid,' . get_sql_select_client_company());
            $this->db->from(db_prefix() . 'estimates');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'estimates.clientid', 'left');
            $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'estimates.currency');
            $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid AND is_primary = 1', 'left');
            if (!$has_permission_view_estimates) {
                $this->db->where($noPermissionQuery);
            }
            $this->db->where(db_prefix() . 'estimates.active', 1);
            $this->db->where(db_prefix() . 'estimates.project_id', $default_project);
            $this->db->where('(
                ' . db_prefix() . 'estimates.number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.clientnote LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.total LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.adminnote LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.billing_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.billing_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.billing_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.billing_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.shipping_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.shipping_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.shipping_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'estimates.shipping_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            )');
            $this->db->order_by('number,YEAR(date)', 'desc');
            $this->db->group_by('estimates.id');
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_estimate_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'estimate_items',
            'search_heading' => _l('estimate_items'),
        ];
        $has_permission_view_estimates       = staff_can('view',  'estimates');
        $has_permission_view_estimates_own   = staff_can('view_own',  'estimates');
        $allow_staff_view_estimates_assigned = get_option('allow_staff_view_estimates_assigned');
        $default_project = get_default_project();
        if ($has_permission_view_estimates || $has_permission_view_estimates_own || $allow_staff_view_estimates_assigned) {
            $noPermissionQuery = get_estimates_where_sql_for_staff(get_staff_user_id());
            $this->db->select(db_prefix() . 'itemable.rel_id', db_prefix() . 'itemable.description');
            $this->db->from(db_prefix() . 'itemable');
            $this->db->join(db_prefix() . 'estimates', db_prefix() . 'estimates.id = ' . db_prefix() . 'itemable.rel_id', 'left');
            $this->db->where(db_prefix() . 'itemable.rel_type', 'estimate');
            if (!$has_permission_view_estimates) {
                $this->db->where(db_prefix() . 'itemable.rel_id IN (select id from ' . db_prefix() . 'estimates where ' . $noPermissionQuery . ')');
            }
            $this->db->where(db_prefix() . 'estimates.project_id', $default_project);
            $this->db->where('(' . db_prefix() . 'itemable.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' OR ' . db_prefix() . 'itemable.long_description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\')');
            $this->db->order_by(db_prefix() . 'itemable.description', 'ASC');
            $this->db->group_by(db_prefix() . 'estimates.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_estimate_commodity_groups($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'estimate_commodity_groups',
            'search_heading' => _l('project_budget').' > '._l('Budget head'),
        ];
        $this->db->select('name')->from(db_prefix() . 'items_groups')->like('name', $q);
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $this->db->group_by(db_prefix() . 'items_groups.id');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_estimate_sub_groups($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'estimate_sub_groups',
            'search_heading' => _l('project_budget').' > '._l('Budget sub head'),
        ];
        $this->db->select('sub_group_name')->from(db_prefix() . 'wh_sub_group')->like('sub_group_name', $q);
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $this->db->group_by(db_prefix() . 'wh_sub_group.id');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_estimate_master_areas($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'estimate_master_areas',
            'search_heading' => _l('project_budget').' > '._l('Master area'),
        ];
        $this->db->select('category_name')->from(db_prefix() . 'master_area')->like('category_name', $q)->or_like('description', $q);
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $this->db->order_by('category_name', 'ASC');
        $this->db->group_by(db_prefix() . 'master_area.id');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_estimate_functionality_areas($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'estimate_functionality_areas',
            'search_heading' => _l('project_budget').' > '._l('Functionality area'),
        ];
        $this->db->select('category_name')->from(db_prefix() . 'functionality_area')->like('category_name', $q)->or_like('description', $q);
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $this->db->order_by('category_name', 'ASC');
        $this->db->group_by(db_prefix() . 'functionality_area.id');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'items',
            'search_heading' => _l('items'),
        ];
        $this->db->select('it.id, it.commodity_code, it.description');
        $this->db->from(db_prefix() . 'items AS it');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = it.group_id', 'left');
        $this->db->join(db_prefix() . 'wh_sub_group AS sg', 'sg.id = it.sub_group', 'left');
        $this->db->where('(
            it.commodity_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            sg.sub_group_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.long_description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.commodity_barcode LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.sku_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.sku_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('it.commodity_code', 'DESC');
        $this->db->group_by('it.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_vendors($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'vendors',
            'search_heading' => _l('vendor'),
        ];
        $this->db->select('pv.userid, pv.company');
        $this->db->from(db_prefix() . 'pur_vendor AS pv');
        $this->db->where('(
            pv.vendor_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.com_email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.pan_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.website LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.bank_detail LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.preferred_location LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pv.company', 'ASC');
        $this->db->group_by('pv.userid');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_vendor_contacts($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'vendor_contacts',
            'search_heading' => _l('Vendor Contacts'),
        ];
        $this->db->select('pc.userid, pv.company, pc.firstname, pc.lastname');
        $this->db->from(db_prefix() . 'pur_contacts AS pc');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = pc.userid', 'left');
        $this->db->where('(
            pc.firstname LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pc.lastname LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pc.title LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pc.email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pc.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pc.firstname', 'ASC');
        $this->db->group_by('pc.userid');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;       
    }

    public function _search_unawarded_trackers($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'unawarded_trackers',
            'search_heading' => _l('unawarded_tracker'),
        ];
        $default_project = get_default_project();
        $this->db->select('epi.id, epi.package_name');
        $this->db->from(db_prefix() . 'estimate_package_info AS epi');
        $this->db->join(db_prefix() . 'estimates AS est', 'est.id = epi.estimate_id');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = epi.budget_head');
        $this->db->where('est.project_id', $default_project);
        $this->db->where('(
            epi.package_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            epi.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('epi.package_name', 'ASC');
        $this->db->group_by('epi.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_purchase_requests($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'purchase_requests',
            'search_heading' => _l('purchase_request'),
        ];
        $default_project = get_default_project();
        $this->db->select('pr.id, pr.pur_rq_name, pr.pur_rq_code');
        $this->db->from(db_prefix() . 'pur_request AS pr');
        $this->db->join(db_prefix() . 'departments AS de', 'de.departmentid = pr.department', 'left');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = pr.group_pur', 'left');
        $this->db->join(db_prefix() . 'wh_sub_group AS sg', 'sg.id = pr.sub_groups_pur', 'left');
        $this->db->where('pr.project', $default_project);
        $this->db->where('(
            pr.pur_rq_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pr.pur_rq_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            de.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            sg.sub_group_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pr.pur_rq_name', 'ASC');
        $this->db->group_by('pr.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_purchase_request_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'purchase_request_items',
            'search_heading' => _l('purchase_request_items'),
        ];
        $default_project = get_default_project();
        $this->db->select('pr.id, pr.pur_rq_name, pr.pur_rq_code');
        $this->db->from(db_prefix() . 'pur_request_detail AS prd');
        $this->db->join(db_prefix() . 'pur_request AS pr', 'pr.id = prd.pur_request', 'left');
        $this->db->where('pr.project', $default_project);
        $this->db->where('(
            prd.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pr.pur_rq_name', 'ASC');
        $this->db->group_by('pr.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_quotations($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'quotations',
            'search_heading' => _l('quotations'),
        ];
        $default_project = get_default_project();
        $this->db->select('pe.id');
        $this->db->from(db_prefix() . 'pur_estimates AS pe');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = pe.vendor', 'left');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = pe.group_pur', 'left');
        $this->db->join(db_prefix() . 'wh_sub_group AS sg', 'sg.id = pe.sub_groups_pur', 'left');
        $this->db->where('pe.project', $default_project);
        $this->db->where('(
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            sg.sub_group_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pe.id', 'ASC');
        $this->db->group_by('pe.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_purchase_orders($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'purchase_orders',
            'search_heading' => _l('purchase_orders'),
        ];
        $default_project = get_default_project();
        $this->db->select('po.id, po.pur_order_name, po.pur_order_number');
        $this->db->from(db_prefix() . 'pur_orders AS po');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = po.vendor', 'left');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = po.group_pur', 'left');
        $this->db->join(db_prefix() . 'departments AS de', 'de.departmentid = po.department', 'left');
        $this->db->where('po.project', $default_project);
        $this->db->where('(
            po.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            de.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('po.pur_order_name', 'ASC');
        $this->db->group_by('po.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_purchase_order_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'purchase_order_items',
            'search_heading' => _l('purchase_order_items'),
        ];
        $default_project = get_default_project();
        $this->db->select('po.id, po.pur_order_name, po.pur_order_number');
        $this->db->from(db_prefix() . 'pur_order_detail AS pod');
        $this->db->join(db_prefix() . 'pur_orders AS po', 'po.id = pod.pur_order', 'left');
        $this->db->where('po.project', $default_project);
        $this->db->where('(
            pod.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('po.pur_order_name', 'ASC');
        $this->db->group_by('po.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_work_orders($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'work_orders',
            'search_heading' => _l('work_order'),
        ];
        $default_project = get_default_project();
        $this->db->select('wo.id, wo.wo_order_name, wo.wo_order_number');
        $this->db->from(db_prefix() . 'wo_orders AS wo');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = wo.vendor', 'left');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = wo.group_pur', 'left');
        $this->db->join(db_prefix() . 'departments AS de', 'de.departmentid = wo.department', 'left');
        $this->db->where('wo.project', $default_project);
        $this->db->where('(
            wo.wo_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            de.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('wo.wo_order_name', 'ASC');
        $this->db->group_by('wo.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_work_order_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'work_order_items',
            'search_heading' => _l('work_order_items'),
        ];
        $default_project = get_default_project();
        $this->db->select('wo.id, wo.wo_order_name, wo.wo_order_number');
        $this->db->from(db_prefix() . 'wo_order_detail AS wod');
        $this->db->join(db_prefix() . 'wo_orders AS wo', 'wo.id = wod.wo_order', 'left');
        $this->db->where('wo.project', $default_project);
        $this->db->where('(
            wod.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('wo.wo_order_name', 'ASC');
        $this->db->group_by('wo.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_payment_certificate($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'payment_certificate',
            'search_heading' => _l('payment_certificate'),
        ];
        $default_project = get_default_project();
        $this->db->select('pc.id, pc.po_id, pc.wo_id, pc.ot_id, pc.pc_number');
        $this->db->from(db_prefix() . 'payment_certificate AS pc');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = pc.vendor', 'left');
        $this->db->join(db_prefix() . 'pur_orders AS po', 'po.id = pc.po_id', 'left');
        $this->db->join(db_prefix() . 'wo_orders AS wo', 'wo.id = pc.wo_id', 'left');
        $this->db->join(db_prefix() . 'pur_order_tracker AS pot', 'pot.id = pc.ot_id', 'left');
        $this->db->where('(
            pc.po_id IS NOT NULL AND po.project = "'.$default_project.'"
            OR
            pc.wo_id IS NOT NULL AND wo.project = "'.$default_project.'"
            OR
            pc.ot_id IS NOT NULL AND pot.project = "'.$default_project.'"
        )');
        $this->db->where('(
            pc.serial_no LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pc.pc_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pot.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pc.location LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pc.invoice_ref LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pc.id', 'ASC');
        $this->db->group_by('pc.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_pur_bills($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'pur_bills',
            'search_heading' => _l('bill_bifurcation'),
        ];
        $default_project = get_default_project();
        $this->db->select('pb.id, pb.bill_code');
        $this->db->from(db_prefix() . 'pur_bills AS pb');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = pb.vendor', 'left');
        $this->db->join(db_prefix() . 'pur_orders AS po', 'po.id = pb.pur_order', 'left');
        $this->db->join(db_prefix() . 'wo_orders AS wo', 'wo.id = pb.wo_order', 'left');
        $this->db->join(db_prefix() . 'pur_order_tracker AS pot', 'pot.id = pb.order_tracker_id', 'left');
        $this->db->where('pb.project_id', $default_project);
        $this->db->where('(
            pb.bill_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pb.bill_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pot.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pb.transactionid LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pb.id', 'ASC');
        $this->db->group_by('pb.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_pur_bill_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'pur_bill_items',
            'search_heading' => _l('Bill Bifurcation Items'),
        ];
        $default_project = get_default_project();
        $this->db->select('pb.id, pb.bill_code');
        $this->db->from(db_prefix() . 'pur_bill_details AS pbd');
        $this->db->join(db_prefix() . 'pur_bills AS pb', 'pb.id = pbd.pur_bill', 'left');
        $this->db->where('pb.project_id', $default_project);
        $this->db->where('(
            pbd.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pb.id', 'ASC');
        $this->db->group_by('pb.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_order_tracker($q, $limit = 0)
    {
        $order_tracker_result = [];
        $result = [
            'result'         => [],
            'type'           => 'order_tracker',
            'search_heading' => _l('order_tracker'),
        ];
        $default_project = get_default_project();

        $this->db->select("CONCAT(po.pur_order_number, ' - ', po.pur_order_name) AS order_name");
        $this->db->from(db_prefix() . 'pur_orders AS po');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = po.vendor', 'left');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = po.group_pur', 'left');
        $this->db->where('po.approve_status', 2);
        $this->db->where('po.project', $default_project);
        $this->db->where('(
            po.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('po.id', 'ASC');
        $this->db->group_by('po.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $pur_orders = $this->db->get()->result_array();

        $this->db->select("CONCAT(wo.wo_order_number, ' - ', wo.wo_order_name) AS order_name");
        $this->db->from(db_prefix() . 'wo_orders AS wo');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = wo.vendor', 'left');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = wo.group_pur', 'left');
        $this->db->where('wo.approve_status', 2);
        $this->db->where('wo.project', $default_project);
        $this->db->where('(
            wo.wo_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('wo.id', 'ASC');
        $this->db->group_by('wo.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $wo_orders = $this->db->get()->result_array();

        $this->db->select("t.pur_order_name AS order_name");
        $this->db->from(db_prefix() . 'pur_order_tracker AS t');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = t.vendor', 'left');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = t.group_pur', 'left');
        $this->db->where('t.project', $default_project);
        $this->db->where('(
            t.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            t.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            t.remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('t.id', 'ASC');
        $this->db->group_by('t.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $pur_order_trackers = $this->db->get()->result_array();

        $order_tracker_result = array_merge($pur_order_trackers, $pur_orders, $wo_orders);

        $result['result'] = $order_tracker_result;
        return $result;    
    }

    public function _search_purchase_tracker($q, $limit = 0)
    {
        $purchase_tracker_result = [];
        $result = [
            'result'         => [],
            'type'           => 'purchase_tracker',
            'search_heading' => _l('purchase_tracker'),
        ];
        $default_project = get_default_project();

        $this->db->select('gr.id, gr.goods_receipt_code AS order_name, 1 AS type');
        $this->db->from(db_prefix() . 'goods_receipt_detail AS grd');
        $this->db->join(db_prefix() . 'goods_receipt AS gr', 'gr.id = grd.goods_receipt_id', 'left');
        $this->db->join(db_prefix() . 'pur_orders AS po', 'po.id = gr.pr_order_id', 'left');
        $this->db->join(db_prefix() . 'wo_orders AS wo', 'wo.id = gr.wo_order_id', 'left');
        $this->db->join(db_prefix() . 'items AS it', 'it.id = grd.commodity_code', 'left');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = gr.supplier_code', 'left');
        $this->db->where('gr.project', $default_project);
        $this->db->where('(
            gr.goods_receipt_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.commodity_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            grd.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            gr.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            grd.remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            grd.actual_remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('gr.id', 'ASC');
        $this->db->group_by('gr.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $goods_receipts = $this->db->get()->result_array();

        $this->db->select("po.id, CONCAT(po.pur_order_number, ' - ', po.pur_order_name) AS order_name, 2 AS type");
        $this->db->from(db_prefix() . 'pur_order_detail AS pod');
        $this->db->join(db_prefix() . 'pur_orders AS po', 'po.id = pod.pur_order', 'left');
        $this->db->join(db_prefix() . 'items AS it', 'it.id = pod.item_code', 'left');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = po.vendor', 'left');
        $this->db->where('po.goods_id', 0);
        $this->db->where('po.project', $default_project);
        $this->db->where('(
            po.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.commodity_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pod.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pod.remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pod.actual_remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('po.id', 'ASC');
        $this->db->group_by('po.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $pur_orders = $this->db->get()->result_array();

        $this->db->select("wo.id, CONCAT(wo.wo_order_number, ' - ', wo.wo_order_name) AS order_name, 3 AS type");
        $this->db->from(db_prefix() . 'wo_order_detail AS wod');
        $this->db->join(db_prefix() . 'wo_orders AS wo', 'wo.id = wod.wo_order', 'left');
        $this->db->join(db_prefix() . 'items AS it', 'it.id = wod.item_code', 'left');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = wo.vendor', 'left');
        $this->db->where('wo.goods_id', 0);
        $this->db->where('wo.project', $default_project);
        $this->db->where('(
            wo.wo_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.commodity_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            it.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wod.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wod.remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wod.actual_remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('wo.id', 'ASC');
        $this->db->group_by('wo.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $wo_orders = $this->db->get()->result_array();

        $purchase_tracker_result = array_merge($goods_receipts, $pur_orders, $wo_orders);

        $result['result'] = $purchase_tracker_result;
        return $result;      
    }

    public function _search_invoices($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'invoices',
            'search_heading' => _l('invoices'),
        ];
        $has_permission_view_invoices     = staff_can('view',  'invoices');
        $has_permission_view_invoices_own = staff_can('view_own',  'invoices');
        if ($has_permission_view_invoices || $has_permission_view_invoices_own || get_option('allow_staff_view_invoices_assigned') == '1') {
            if (is_numeric($q)) {
                $q = trim($q);
                $q = ltrim($q, '0');
            } elseif (startsWith($q, get_option('invoice_prefix'))) {
                $q = strafter($q, get_option('invoice_prefix'));
                $q = trim($q);
                $q = ltrim($q, '0');
            }
            $invoice_fields    = prefixed_table_fields_array(db_prefix() . 'invoices');
            $clients_fields    = prefixed_table_fields_array(db_prefix() . 'clients');
            $noPermissionQuery = get_invoices_where_sql_for_staff(get_staff_user_id());
            $default_project = get_default_project();
            $this->db->select(implode(',', $invoice_fields) . ',' . implode(',', $clients_fields) . ',' . db_prefix() . 'invoices.id as invoiceid,' . get_sql_select_client_company());
            $this->db->from(db_prefix() . 'invoices');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid', 'left');
            $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency');
            $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid AND is_primary = 1', 'left');
            if (!$has_permission_view_invoices) {
                $this->db->where($noPermissionQuery);
            }
            $this->db->where(db_prefix() . 'invoices.project_id', $default_project);
            if (!startsWith($q, '#')) {
                $this->db->where('(
                    ' . db_prefix() . 'invoices.number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.title LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.clientnote LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.adminnote LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    CONCAT(firstname,\' \',lastname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    CONCAT(lastname,\' \',firstname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.billing_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.billing_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.billing_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.billing_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.shipping_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.shipping_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.shipping_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'invoices.shipping_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.billing_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.billing_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.billing_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.billing_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.shipping_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.shipping_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.shipping_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR
                    ' . db_prefix() . 'clients.shipping_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                )');
            } else {
                $this->db->where(db_prefix() . 'invoices.id IN
                (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . $this->db->escape_str(strafter($q, '#')) . '")
                AND ' . db_prefix() . 'taggables.rel_type=\'invoice\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
            }
            $this->db->order_by('number,YEAR(date)', 'desc');
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->group_by('invoices.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_invoice_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'invoice_items',
            'search_heading' => _l('invoice_items'),
        ];
        $has_permission_view_invoices       = staff_can('view',  'invoices');
        $has_permission_view_invoices_own   = staff_can('view_own',  'invoices');
        $allow_staff_view_invoices_assigned = get_option('allow_staff_view_invoices_assigned');
        $default_project = get_default_project();
        if ($has_permission_view_invoices || $has_permission_view_invoices_own || $allow_staff_view_invoices_assigned == '1') {
            $noPermissionQuery = get_invoices_where_sql_for_staff(get_staff_user_id());
            $this->db->select(db_prefix() . 'itemable.rel_id', db_prefix() . 'itemable.description');
            $this->db->from(db_prefix() . 'itemable');
            $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'itemable.rel_id', 'left');
            $this->db->where(db_prefix() . 'itemable.rel_type', 'invoice');
            $this->db->where(db_prefix() . 'invoices.project_id', $default_project);
            $this->db->where('(' . db_prefix() . 'itemable.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' OR ' . db_prefix() . 'itemable.long_description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\')');
            if (!$has_permission_view_invoices) {
                $this->db->where(db_prefix() . 'itemable.rel_id IN (select id from ' . db_prefix() . 'invoices where ' . $noPermissionQuery . ')');
            }
            $this->db->order_by(db_prefix() . 'itemable.description', 'ASC');
            $this->db->group_by(db_prefix() . 'invoices.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_payments($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'invoice_payment_records',
            'search_heading' => _l('payments'),
        ];
        $has_permission_view_payments     = staff_can('view',  'payments');
        $has_permission_view_invoices_own = staff_can('view_own',  'invoices');
        if (staff_can('view',  'payments') || $has_permission_view_invoices_own || get_option('allow_staff_view_invoices_assigned') == '1') {
            if (is_numeric($q)) {
                $q = trim($q);
                $q = ltrim($q, '0');
            } elseif (startsWith($q, get_option('invoice_prefix'))) {
                $q = strafter($q, get_option('invoice_prefix'));
                $q = trim($q);
                $q = ltrim($q, '0');
            }
            $noPermissionQuery = get_invoices_where_sql_for_staff(get_staff_user_id());
            $this->db->select(db_prefix() . 'invoicepaymentrecords.date,' . db_prefix() . 'invoicepaymentrecords.id as paymentid');
            $this->db->from(db_prefix() . 'invoicepaymentrecords');
            $this->db->join(db_prefix() . 'payment_modes', '' . db_prefix() . 'invoicepaymentrecords.paymentmode = ' . db_prefix() . 'payment_modes.id', 'LEFT');
            $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
            if (!$has_permission_view_payments) {
                $this->db->where('invoiceid IN (select id from ' . db_prefix() . 'invoices where ' . $noPermissionQuery . ')');
            }
            $this->db->where('(' . db_prefix() . 'invoicepaymentrecords.id LIKE "' . $this->db->escape_like_str($q) . '"
                OR ' . db_prefix() . 'payment_modes.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'invoicepaymentrecords.note LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'invoicepaymentrecords.transactionid LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            )');
            $this->db->order_by(db_prefix() . 'invoicepaymentrecords.date', 'ASC');
            $this->db->group_by(db_prefix() . 'invoicepaymentrecords.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_pur_invoices($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'pur_invoices',
            'search_heading' => _l('Vendor Billing Tracker'),
        ];
        $default_project = get_default_project();
        $this->db->select('pi.id, pi.invoice_number');
        $this->db->from(db_prefix() . 'pur_invoices AS pi');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = pi.vendor', 'left');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = pi.group_pur', 'left');
        $this->db->join(db_prefix() . 'pur_orders AS po', 'po.id = pi.pur_order', 'left');
        $this->db->join(db_prefix() . 'wo_orders AS wo', 'wo.id = pi.wo_order', 'left');
        $this->db->join(db_prefix() . 'pur_order_tracker AS pot', 'pot.id = pi.order_tracker_id', 'left');
        $this->db->where('pi.project_id', $default_project);
        $this->db->where('(
            pi.invoice_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pi.vendor_invoice_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pi.description_services LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pi.vendor_submitted_amount_without_tax LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pi.vendor_submitted_tax_amount LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pi.final_certified_amount LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pot.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pi.adminnote LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pi.id', 'ASC');
        $this->db->group_by('pi.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_pur_invoice_payments($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'pur_invoice_payments',
            'search_heading' => _l('Vendor Payment Tracker'),
        ];
        $default_project = get_default_project();
        $this->db->select('pi.id, pi.invoice_number');
        $this->db->from(db_prefix() . 'pur_invoices AS pi');
        $this->db->where('pi.project_id', $default_project);
        $this->db->where('(
            pi.payment_remarks LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pi.id', 'ASC');
        $this->db->group_by('pi.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_debit_notes($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'debit_note',
            'search_heading' => _l('pur_debit_note'),
        ];
        $this->db->select('pdn.id, pdn.date');
        $this->db->from(db_prefix() . 'pur_debit_notes AS pdn');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = pdn.vendorid', 'left');
        $this->db->where('(
            pdn.number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pdn.reference_no LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('pdn.id', 'ASC');
        $this->db->group_by('pdn.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_debit_note_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'debit_note_items',
            'search_heading' => _l('Debit Notes Tracker Items'),
        ];
        $this->db->select(db_prefix() . 'itemable.rel_id', db_prefix() . 'itemable.description');
        $this->db->from(db_prefix() . 'itemable');
        $this->db->join(db_prefix() . 'pur_debit_notes', db_prefix() . 'pur_debit_notes.id = ' . db_prefix() . 'itemable.rel_id', 'left');
        $this->db->where(db_prefix() . 'itemable.rel_type', 'debit_note');
        $this->db->where('(' . db_prefix() . 'itemable.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' OR ' . db_prefix() . 'itemable.long_description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\')');
        $this->db->order_by(db_prefix() . 'itemable.description', 'ASC');
        $this->db->group_by(db_prefix() . 'pur_debit_notes.id');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_credit_notes($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'credit_note',
            'search_heading' => _l('credit_notes'),
        ];
        $has_permission_view_credit_notes     = staff_can('view',  'credit_notes');
        $has_permission_view_credit_notes_own = staff_can('view_own',  'credit_notes');
        if ($has_permission_view_credit_notes || $has_permission_view_credit_notes_own) {
            if (is_numeric($q)) {
                $q = trim($q);
                $q = ltrim($q, '0');
            } elseif (startsWith($q, get_option('credit_note_prefix'))) {
                $q = strafter($q, get_option('credit_note_prefix'));
                $q = trim($q);
                $q = ltrim($q, '0');
            }
            $credit_note_fields = prefixed_table_fields_array(db_prefix() . 'creditnotes');
            $clients_fields     = prefixed_table_fields_array(db_prefix() . 'clients');
            $default_project = get_default_project();
            $this->db->select(implode(',', $credit_note_fields) . ',' . implode(',', $clients_fields) . ',' . db_prefix() . 'creditnotes.id as credit_note_id,' . get_sql_select_client_company());
            $this->db->from(db_prefix() . 'creditnotes');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'creditnotes.clientid', 'left');
            $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'creditnotes.currency');
            $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid AND is_primary = 1', 'left');
            if (!$has_permission_view_credit_notes) {
                $this->db->where(db_prefix() . 'creditnotes.addedfrom', get_staff_user_id());
            }
            $this->db->where(db_prefix() . 'creditnotes.project_id', $default_project);
            $this->db->where('(
                ' . db_prefix() . 'creditnotes.number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.reference_no LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.clientnote LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.adminnote LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                CONCAT(firstname,\' \',lastname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                CONCAT(lastname,\' \',firstname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.billing_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.billing_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.billing_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.billing_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.shipping_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.shipping_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.shipping_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'creditnotes.shipping_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            )');
            $this->db->order_by('number', 'desc');
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->group_by(db_prefix() . 'creditnotes.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_credit_note_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'credit_note_items',
            'search_heading' => _l('Credit Notes Tracker Items'),
        ];
        $default_project = get_default_project();
        $this->db->select(db_prefix() . 'itemable.rel_id', db_prefix() . 'itemable.description');
        $this->db->from(db_prefix() . 'itemable');
        $this->db->join(db_prefix() . 'creditnotes', db_prefix() . 'creditnotes.id = ' . db_prefix() . 'itemable.rel_id', 'left');
        $this->db->where(db_prefix() . 'itemable.rel_type', 'credit_note');
        $this->db->where(db_prefix() . 'creditnotes.project_id', $default_project);
        $this->db->where('(' . db_prefix() . 'itemable.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' OR ' . db_prefix() . 'itemable.long_description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\')');
        $this->db->order_by(db_prefix() . 'itemable.description', 'ASC');
        $this->db->group_by(db_prefix() . 'creditnotes.id');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_change_orders($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'change_orders',
            'search_heading' => _l('Change Orders'),
        ];
        $default_project = get_default_project();
        $this->db->select('co.id, co.pur_order_name, co.pur_order_number');
        $this->db->from(db_prefix() . 'co_orders AS co');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = co.vendor', 'left');
        $this->db->join(db_prefix() . 'items_groups AS ig', 'ig.id = co.group_pur', 'left');
        $this->db->join(db_prefix() . 'wh_sub_group AS sg', 'sg.id = co.sub_groups_pur', 'left');
        $this->db->join(db_prefix() . 'departments AS de', 'de.departmentid = co.department', 'left');
        $this->db->where('co.project', $default_project);
        $this->db->where('(
            co.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            co.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            ig.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            sg.sub_group_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            co.type LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            de.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('co.pur_order_name', 'ASC');
        $this->db->group_by('co.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_change_order_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'change_order_items',
            'search_heading' => _l('Change Order Items'),
        ];
        $default_project = get_default_project();
        $this->db->select('co.id, co.pur_order_name, co.pur_order_number');
        $this->db->from(db_prefix() . 'co_order_detail AS cod');
        $this->db->join(db_prefix() . 'co_orders AS co', 'co.id = cod.pur_order', 'left');
        $this->db->where('co.project', $default_project);
        $this->db->where('(
            cod.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('co.pur_order_name', 'ASC');
        $this->db->group_by('co.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_stock_import($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'stock_import',
            'search_heading' => _l('stock_import'),
        ];
        $default_project = get_default_project();
        $this->db->select('gr.id, gr.goods_receipt_code');
        $this->db->from(db_prefix() . 'goods_receipt AS gr');
        $this->db->join(db_prefix() . 'pur_vendor AS pv', 'pv.userid = gr.supplier_code', 'left');
        $this->db->join(db_prefix() . 'pur_orders AS po', 'po.id = gr.pr_order_id', 'left');
        $this->db->join(db_prefix() . 'wo_orders AS wo', 'wo.id = gr.wo_order_id', 'left');
        $this->db->where('gr.project', $default_project);
        $this->db->where('(
            gr.goods_receipt_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            pv.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            gr.kind LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('gr.goods_receipt_code', 'ASC');
        $this->db->group_by('gr.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_stock_import_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'stock_import_items',
            'search_heading' => _l('Stock Received Items'),
        ];
        $default_project = get_default_project();
        $this->db->select('gr.id, gr.goods_receipt_code');
        $this->db->from(db_prefix() . 'goods_receipt_detail AS grd');
        $this->db->join(db_prefix() . 'goods_receipt AS gr', 'gr.id = grd.goods_receipt_id', 'left');
        $this->db->where('gr.project', $default_project);
        $this->db->where('(
            grd.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('gr.goods_receipt_code', 'ASC');
        $this->db->group_by('gr.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_stock_export($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'stock_export',
            'search_heading' => _l('stock_export'),
        ];
        $default_project = get_default_project();
        $this->db->select('gd.id, gd.goods_delivery_code');
        $this->db->from(db_prefix() . 'goods_delivery AS gd');
        $this->db->join(db_prefix() . 'pur_orders AS po', 'po.id = gd.pr_order_id', 'left');
        $this->db->join(db_prefix() . 'wo_orders AS wo', 'wo.id = gd.wo_order_id', 'left');
        $this->db->where('gd.project', $default_project);
        $this->db->where('(
            gd.goods_delivery_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            po.pur_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wo.wo_order_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('gd.goods_delivery_code', 'ASC');
        $this->db->group_by('gd.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_stock_export_items($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'stock_export_items',
            'search_heading' => _l('Stock Issued Items'),
        ];
        $default_project = get_default_project();
        $this->db->select('gd.id, gd.goods_delivery_code');
        $this->db->from(db_prefix() . 'goods_delivery_detail AS gdd');
        $this->db->join(db_prefix() . 'goods_delivery AS gd', 'gd.id = gdd.goods_delivery_id', 'left');
        $this->db->where('gd.project', $default_project);
        $this->db->where('(
            gdd.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('gd.goods_delivery_code', 'ASC');
        $this->db->group_by('gd.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_internal_delivery_note($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'internal_delivery_note',
            'search_heading' => _l('internal_delivery_note'),
        ];
        $default_project = get_default_project();
        $this->db->select('idn.id, idn.internal_delivery_code, idn.internal_delivery_name');
        $this->db->from(db_prefix() . 'internal_delivery_note AS idn');
        $this->db->where('idn.project', $default_project);
        $this->db->where('(
            idn.internal_delivery_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            idn.description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            idn.internal_delivery_code LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            idn.total_amount LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('idn.id', 'ASC');
        $this->db->group_by('idn.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_loss_adjustment($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'loss_adjustment',
            'search_heading' => _l('loss_adjustment'),
        ];
        $default_project = get_default_project();
        $this->db->select('wha.id, wha.type, wha.time');
        $this->db->from(db_prefix() . 'wh_loss_adjustment AS wha');
        $this->db->where('wha.project', $default_project);
        $this->db->where('(
            wha.type LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            OR
            wha.reason LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
        )');
        $this->db->order_by('wha.id', 'ASC');
        $this->db->group_by('wha.id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_expenses($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'expenses',
            'search_heading' => _l('expenses'),
        ];
        $has_permission_expenses_view     = staff_can('view',  'expenses');
        $has_permission_expenses_view_own = staff_can('view_own',  'expenses');
        if ($has_permission_expenses_view || $has_permission_expenses_view_own) {
            $this->db->select('*,' . db_prefix() . 'expenses.amount as amount,' . db_prefix() . 'expenses_categories.name as category_name,' . db_prefix() . 'payment_modes.name as payment_mode_name,' . db_prefix() . 'taxes.name as tax_name, ' . db_prefix() . 'expenses.id as expenseid,' . db_prefix() . 'currencies.name as currency_name');
            $this->db->from(db_prefix() . 'expenses');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'expenses.clientid', 'left');
            $this->db->join(db_prefix() . 'payment_modes', db_prefix() . 'payment_modes.id = ' . db_prefix() . 'expenses.paymentmode', 'left');
            $this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
            $this->db->join(db_prefix() . 'expenses_categories', db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category');
            $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'expenses.currency', 'left');
            if (!$has_permission_expenses_view) {
                $this->db->where(db_prefix() . 'expenses.addedfrom', get_staff_user_id());
            }
            $this->db->where('(company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR paymentmode LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'payment_modes.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'expenses_categories.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'expenses.note LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'expenses.expense_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            )');
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('date', 'DESC');
            $this->db->group_by(db_prefix() . 'expenses.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_tasks($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'tasks',
            'search_heading' => _l('tasks'),
        ];
        $is_admin = is_admin();
        $tasks = staff_can('view',  'tasks');
        $this->db->select('id, name');
        $this->db->from(db_prefix() . 'tasks');
        if (!$is_admin) {
            if (!$tasks) {
                $where = '(id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ') OR id IN (SELECT taskid FROM ' . db_prefix() . 'task_followers WHERE staffid = ' . get_staff_user_id() . ') OR (addedfrom=' . get_staff_user_id() . ' AND is_added_from_contact=0) ';
                if (get_option('show_all_tasks_for_project_member') == 1) {
                    $where .= ' OR (rel_type="project" AND rel_id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . '))';
                }
                $where .= ' OR is_public = 1)';
                $this->db->where($where);
            }
        }
        if (!startsWith($q, '#')) {
            $this->db->where('(name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' OR description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\')');
        } else {
            $this->db->where('id IN
                (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . $this->db->escape_str(strafter($q, '#')) . '")
                AND ' . db_prefix() . 'taggables.rel_type=\'task\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
            ');
        }
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $this->db->order_by('name', 'ASC');
        $this->db->group_by(db_prefix() . 'tasks.id');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_tickets($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'tickets',
            'search_heading' => _l('support_tickets'),
        ];
        if (is_staff_member() || (!is_staff_member() && get_option('access_tickets_to_none_staff_members') == 1)) {
            $is_admin = is_admin();
            $where = '';
            if (!$is_admin && get_option('staff_access_only_assigned_departments') == 1) {
                $this->load->model('departments_model');
                $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                $departments_ids      = [];
                if (count($staff_deparments_ids) == 0) {
                    $departments = $this->departments_model->get();
                    foreach ($departments as $department) {
                        array_push($departments_ids, $department['departmentid']);
                    }
                } else {
                    $departments_ids = $staff_deparments_ids;
                }
                if (count($departments_ids) > 0) {
                    $where = 'department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")';
                }
            }
            $this->db->select();
            $this->db->from(db_prefix() . 'tickets');
            $this->db->join(db_prefix() . 'departments', db_prefix() . 'departments.departmentid = ' . db_prefix() . 'tickets.department');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'tickets.userid', 'left');
            $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.id = ' . db_prefix() . 'tickets.contactid', 'left');
            if (!startsWith($q, '#')) {
                $this->db->where('(
                    ticketid LIKE "' . $q . '%"
                    OR subject LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR message LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'contacts.email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR CONCAT(firstname, \' \', lastname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR CONCAT(lastname, \' \', firstname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'contacts.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'clients.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR ' . db_prefix() . 'departments.name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                )');
                if ($where != '') {
                    $this->db->where($where);
                }
            } else {
                $this->db->where('ticketid IN
                    (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                    (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . $this->db->escape_str(strafter($q, '#')) . '")
                    AND ' . db_prefix() . 'taggables.rel_type=\'ticket\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
            }
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('ticketid', 'DESC');
            $this->db->group_by(db_prefix() . 'tickets.ticketid');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_contracts($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'contracts',
            'search_heading' => _l('contracts'),
        ];
        $has_permission_view_contracts = staff_can('view',  'contracts');
        if ($has_permission_view_contracts || staff_can('view_own',  'contracts')) {
            $this->db->select();
            $this->db->from(db_prefix() . 'contracts');
            if (!$has_permission_view_contracts) {
                $this->db->where(db_prefix() . 'contracts.addedfrom', get_staff_user_id());
            }
            $this->db->where('(description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' OR subject LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\')');
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('subject', 'ASC');
            $this->db->group_by(db_prefix() . 'contracts.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_custom_fields($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'custom_fields',
            'search_heading' => _l('custom_fields'),
        ];
        $is_admin = is_admin();
        if ($is_admin) {
            $this->db->select()->from(db_prefix() . 'customfieldsvalues')->like('value', $q);
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->group_by(db_prefix() . 'customfieldsvalues.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_leads($q, $limit = 0, $where = [])
    {
        $result = [
            'result'         => [],
            'type'           => 'leads',
            'search_heading' => _l('leads'),
        ];
        $has_permission_view = staff_can('view',  'leads');
        if (is_staff_member()) {
            $this->db->select();
            $this->db->from(db_prefix() . 'leads');
            if (!$has_permission_view) {
                $this->db->where('(assigned = ' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR is_public=1)');
            }
            if (!startsWith($q, '#')) {
                $this->db->where('(name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR title LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                    OR phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                )');
            } else {
                $this->db->where('id IN
                    (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                    (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . $this->db->escape_str(strafter($q, '#')) . '")
                    AND ' . db_prefix() . 'taggables.rel_type=\'lead\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
            }
            $this->db->where($where);
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('name', 'ASC');
            $this->db->group_by(db_prefix() . 'leads.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_proposals($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'proposals',
            'search_heading' => _l('proposals'),
        ];
        $has_permission_view_proposals     = staff_can('view',  'proposals');
        $has_permission_view_proposals_own = staff_can('view_own',  'proposals');
        if ($has_permission_view_proposals || $has_permission_view_proposals_own || get_option('allow_staff_view_proposals_assigned') == '1') {
            if (is_numeric($q)) {
                $q = trim($q);
                $q = ltrim($q, '0');
            } elseif (startsWith($q, get_option('proposal_number_prefix'))) {
                $q = strafter($q, get_option('proposal_number_prefix'));
                $q = trim($q);
                $q = ltrim($q, '0');
            }
            $noPermissionQuery = get_proposals_sql_where_staff(get_staff_user_id());
            $this->db->select('*,' . db_prefix() . 'proposals.id as id');
            $this->db->from(db_prefix() . 'proposals');
            $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'proposals.currency');
            if (!$has_permission_view_proposals) {
                $this->db->where($noPermissionQuery);
            }
            $this->db->where('(
                ' . db_prefix() . 'proposals.id LIKE "' . $q . '%"
                OR ' . db_prefix() . 'proposals.subject LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'proposals.content LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'proposals.proposal_to LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'proposals.zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'proposals.state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'proposals.city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'proposals.address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'proposals.email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'proposals.phone LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
            )');
            $this->db->order_by(db_prefix() . 'proposals.id', 'desc');
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->group_by(db_prefix() . 'proposals.id');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_knowledge_base($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'knowledge_base_articles',
            'search_heading' => _l('kb_string'),
        ];
        if (staff_can('view',  'knowledge_base')) {
            $this->db->select('staff_article, slug, subject')->from(db_prefix() . 'knowledge_base')->like('subject', $q)->or_like('description', $q)->or_like('slug', $q);
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('subject', 'ASC');
            $this->db->group_by(db_prefix() . 'knowledge_base.articleid');
            $result['result'] = $this->db->get()->result_array();
        }
        return $result;
    }

    public function _search_drawings($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'drawing',
            'search_heading' => _l('Drawing Name'),
        ];
        $this->db->select();
        $this->db->from(db_prefix() . 'dms_items');

        $this->db->where('(name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\')');
        $this->db->where('filetype !=', 'folder');
        if ($limit != 0) {
            $this->db->limit($limit);
        }

        $this->db->order_by('name', 'ASC');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_minutes($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'minutes',
            'search_heading' => _l('meeting_minutes'),
        ];
        $this->db->select();
        $this->db->from(db_prefix() . 'meeting_management');

        $this->db->where('(meeting_title LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\')');
        if ($limit != 0) {
            $this->db->limit($limit);
        }

        $this->db->order_by('meeting_title', 'ASC');
        $result['result'] = $this->db->get()->result_array();
        return $result;
    }

    public function _search_pur_contracts($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'contract',
            'search_heading' => _l('contracts'),
        ];

        // Contracts
        $this->db->select();
        $this->db->from(db_prefix() . 'pur_contracts'); // Table name for contracts

        // Search in the specified columns
        $this->db->where('(contract_number LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' 
        OR contract_name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' 
        OR vendor LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\')');

        if ($limit != 0) {
            $this->db->limit($limit);
        }

        $this->db->order_by('contract_name', 'ASC');
        $result['result'] = $this->db->get()->result_array();

        return $result;
    }
}
