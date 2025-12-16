<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get Estimate short_url
 * @since  Version 2.7.3
 * @param  object $estimate
 * @return string Url
 */
function get_estimate_shortlink($estimate)
{
    $long_url = site_url("estimate/{$estimate->id}/{$estimate->hash}");
    if (!get_option('bitly_access_token')) {
        return $long_url;
    }

    // Check if estimate has short link, if yes return short link
    if (!empty($estimate->short_link)) {
        return $estimate->short_link;
    }

    // Create short link and return the newly created short link
    $short_link = app_generate_short_link([
        'long_url' => $long_url,
        'title'    => format_estimate_number($estimate->id),
    ]);

    if ($short_link) {
        $CI = &get_instance();
        $CI->db->where('id', $estimate->id);
        $CI->db->update(db_prefix() . 'estimates', [
            'short_link' => $short_link,
        ]);

        return $short_link;
    }

    return $long_url;
}

/**
 * Check estimate restrictions - hash, clientid
 * @param  mixed $id   estimate id
 * @param  string $hash estimate hash
 */
function check_estimate_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('estimates_model');
    if (!$hash || !$id) {
        show_404();
    }
    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_estimate_only_logged_in') == 1) {
            redirect_after_login_to_current_url();
            redirect(site_url('authentication/login'));
        }
    }
    $estimate = $CI->estimates_model->get($id);
    if (!$estimate || ($estimate->hash != $hash)) {
        show_404();
    }
    // Do one more check
    if (!is_staff_logged_in()) {
        if (get_option('view_estimate_only_logged_in') == 1) {
            if ($estimate->clientid != get_client_user_id()) {
                show_404();
            }
        }
    }
}

/**
 * Check if estimate email template for expiry reminders is enabled
 * @return boolean
 */
function is_estimates_email_expiry_reminder_enabled()
{
    return total_rows(db_prefix() . 'emailtemplates', ['slug' => 'estimate-expiry-reminder', 'active' => 1]) > 0;
}

/**
 * Check if there are sources for sending estimate expiry reminders
 * Will be either email or SMS
 * @return boolean
 */
function is_estimates_expiry_reminders_enabled()
{
    return is_estimates_email_expiry_reminder_enabled() || is_sms_trigger_active(SMS_TRIGGER_ESTIMATE_EXP_REMINDER);
}

/**
 * Return RGBa estimate status color for PDF documents
 * @param  mixed $status_id current estimate status
 * @return string
 */
function estimate_status_color_pdf($status_id)
{
    if ($status_id == 1) {
        $statusColor = '119, 119, 119';
    } elseif ($status_id == 2) {
        // Sent
        $statusColor = '3, 169, 244';
    } elseif ($status_id == 3) {
        //Declines
        $statusColor = '252, 45, 66';
    } elseif ($status_id == 4) {
        //Accepted
        $statusColor = '0, 191, 54';
    } else {
        // Expired
        $statusColor = '255, 111, 0';
    }

    return hooks()->apply_filters('estimate_status_pdf_color', $statusColor, $status_id);
}

/**
 * Format estimate status
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_estimate_status($status, $classes = '', $label = true)
{
    $id          = $status;
    $label_class = estimate_status_color_class($status);
    $status      = estimate_status_by_id($status);
    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status estimate-status-' . $id . ' estimate-status-' . $label_class . '">' . $status . '</span>';
    }

    return $status;
}

/**
 * Return estimate status translated by passed status id
 * @param  mixed $id estimate status id
 * @return string
 */
function estimate_status_by_id($id)
{
    $status = '';
    if ($id == 1) {
        $status = _l('estimate_status_draft');
    } elseif ($id == 2) {
        $status = _l('estimate_status_sent');
    } elseif ($id == 3) {
        $status = _l('estimate_status_declined');
    } elseif ($id == 4) {
        $status = _l('estimate_status_accepted');
    } elseif ($id == 5) {
        // status 5
        $status = _l('estimate_status_expired');
    } else {
        if (!is_numeric($id)) {
            if ($id == 'not_sent') {
                $status = _l('not_sent_indicator');
            }
        }
    }

    return hooks()->apply_filters('estimate_status_label', $status, $id);
}

/**
 * Return estimate status color class based on twitter bootstrap
 * @param  mixed  $id
 * @param  boolean $replace_default_by_muted
 * @return string
 */
function estimate_status_color_class($id, $replace_default_by_muted = false)
{
    $class = '';
    if ($id == 1) {
        $class = 'default';
        if ($replace_default_by_muted == true) {
            $class = 'muted';
        }
    } elseif ($id == 2) {
        $class = 'info';
    } elseif ($id == 3) {
        $class = 'danger';
    } elseif ($id == 4) {
        $class = 'success';
    } elseif ($id == 5) {
        // status 5
        $class = 'warning';
    } else {
        if (!is_numeric($id)) {
            if ($id == 'not_sent') {
                $class = 'default';
                if ($replace_default_by_muted == true) {
                    $class = 'muted';
                }
            }
        }
    }

    return hooks()->apply_filters('estimate_status_color_class', $class, $id);
}

/**
 * Check if the estimate id is last invoice
 * @param  mixed  $id estimateid
 * @return boolean
 */
function is_last_estimate($id)
{
    $CI = &get_instance();
    $CI->db->select('id')->from(db_prefix() . 'estimates')->order_by('id', 'desc')->limit(1);
    $query            = $CI->db->get();
    $last_estimate_id = $query->row()->id;
    if ($last_estimate_id == $id) {
        return true;
    }

    return false;
}

/**
 * Format estimate number based on description
 * @param  mixed $id
 * @return string
 */
function format_estimate_number($id)
{
    $CI = &get_instance();

    if (! is_object($id)) {
        $CI->db->select('date,number,prefix,number_format')->from(db_prefix() . 'estimates')->where('id', $id);
        $estimate = $CI->db->get()->row();
    } else {
        $estimate = $id;
        $id       = $estimate->id;
    }

    if (!$estimate) {
        return '';
    }

    $number = sales_number_format($estimate->number, $estimate->number_format, $estimate->prefix, $estimate->date);

    return hooks()->apply_filters('format_estimate_number', $number, [
        'id'       => $id,
        'estimate' => $estimate,
    ]);
}


/**
 * Function that return estimate item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function get_estimate_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'estimate');
    $taxes = $CI->db->get(db_prefix() . 'item_tax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }

    return $taxes;
}

/**
 * Calculate estimates percent by status
 * @param  mixed $status          estimate status
 * @return array
 */
function get_estimates_percent_by_status($status, $project_id = null)
{
    $has_permission_view = staff_can('view',  'estimates');
    $where               = '';

    if (isset($project_id)) {
        $where .= 'project_id=' . get_instance()->db->escape_str($project_id) . ' AND ';
    }
    if (!$has_permission_view) {
        $where .= get_estimates_where_sql_for_staff(get_staff_user_id());
    }

    $where = trim($where);

    if (endsWith($where, ' AND')) {
        $where = substr_replace($where, '', -3);
    }

    $total_estimates = total_rows(db_prefix() . 'estimates', $where);

    $data            = [];
    $total_by_status = 0;

    if (!is_numeric($status)) {
        if ($status == 'not_sent') {
            $total_by_status = total_rows(db_prefix() . 'estimates', 'sent=0 AND status NOT IN(2,3,4)' . ($where != '' ? ' AND (' . $where . ')' : ''));
        }
    } else {
        $whereByStatus = 'status=' . $status;
        if ($where != '') {
            $whereByStatus .= ' AND (' . $where . ')';
        }
        $total_by_status = total_rows(db_prefix() . 'estimates', $whereByStatus);
    }

    $percent                 = ($total_estimates > 0 ? number_format(($total_by_status * 100) / $total_estimates, 2) : 0);
    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_estimates;

    return $data;
}

function get_estimates_where_sql_for_staff($staff_id)
{
    $CI                                  = &get_instance();
    $has_permission_view_own             = staff_can('view_own',  'estimates');
    $allow_staff_view_estimates_assigned = get_option('allow_staff_view_estimates_assigned');
    $whereUser                           = '';
    if ($has_permission_view_own) {
        $whereUser = '((' . db_prefix() . 'estimates.addedfrom=' . $CI->db->escape_str($staff_id) . ' AND ' . db_prefix() . 'estimates.addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature = "estimates" AND capability="view_own"))';
        if ($allow_staff_view_estimates_assigned == 1) {
            $whereUser .= ' OR sale_agent=' . $CI->db->escape_str($staff_id);
        }
        $whereUser .= ')';
    } else {
        $whereUser .= 'sale_agent=' . $CI->db->escape_str($staff_id);
    }

    return $whereUser;
}
/**
 * Check if staff member have assigned estimates / added as sale agent
 * @param  mixed $staff_id staff id to check
 * @return boolean
 */
function staff_has_assigned_estimates($staff_id = '')
{
    $CI       = &get_instance();
    $staff_id = is_numeric($staff_id) ? $staff_id : get_staff_user_id();
    $cache    = $CI->app_object_cache->get('staff-total-assigned-estimates-' . $staff_id);

    if (is_numeric($cache)) {
        $result = $cache;
    } else {
        $result = total_rows(db_prefix() . 'estimates', ['sale_agent' => $staff_id]);
        $CI->app_object_cache->add('staff-total-assigned-estimates-' . $staff_id, $result);
    }

    return $result > 0 ? true : false;
}
/**
 * Check if staff member can view estimate
 * @param  mixed $id estimate id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_estimate($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    if (has_permission('estimates', $staff_id, 'view')) {
        return true;
    }

    $CI->db->select('id, addedfrom, sale_agent');
    $CI->db->from(db_prefix() . 'estimates');
    $CI->db->where('id', $id);
    $estimate = $CI->db->get()->row();

    if ((has_permission('estimates', $staff_id, 'view_own') && $estimate->addedfrom == $staff_id)
        || ($estimate->sale_agent == $staff_id && get_option('allow_staff_view_estimates_assigned') == '1')
    ) {
        return true;
    }

    return false;
}

function get_master_area($id)
{
    if(!empty($id)) {
        $CI = &get_instance();
        $CI->db->where('id', $id);
        $master_area = $CI->db->get(db_prefix() . 'master_area')->row();
        if(!empty($master_area)) {
            return $master_area->category_name;
        }
    }
    return '';
}

function get_functionality_area($id)
{
    if(!empty($id)) {
        $CI = &get_instance();
        $CI->db->where('id', $id);
        $functionality_area = $CI->db->get(db_prefix() . 'functionality_area')->row();
        if(!empty($functionality_area)) {
            return $functionality_area->category_name;
        }
    }
    return '';
}

function get_purchase_items($id)
{
    if(!empty($id)) {
        $CI = &get_instance();
        $CI->db->where('id', $id);
        $items = $CI->db->get(db_prefix() . 'items')->row();
        if(!empty($items)) {
            return $items->commodity_code.' '.$items->description;
        }
    }
    return '';
}

function get_purchase_unit($id)
{
    if(!empty($id)) {
        $CI = &get_instance();
        $CI->db->where('unit_type_id', $id);
        $ware_unit_type = $CI->db->get(db_prefix() . 'ware_unit_type')->row();
        if(!empty($ware_unit_type)) {
            return $ware_unit_type->unit_name;
        }
    }
    return '';
}

function get_estimate_revision_no($estimate_id, $count = 0, $use_count_only = 0)
{
    $CI =& get_instance();
    $CI->db->select('parent_id');
    $CI->db->from(db_prefix() . 'estimates');
    $CI->db->where('id', $estimate_id);
    $parent = $CI->db->get()->row();
    if ($parent && $parent->parent_id != 0) {
        return get_estimate_revision_no($parent->parent_id, $count + 1, $use_count_only);
    }
    return $use_count_only == 0 ? ' - Revision ' . $count : $count;
}

function get_estimate_revision_chain($estimate_id, $chain = [])
{
    $CI =& get_instance();
    $CI->db->select('parent_id');
    $CI->db->from(db_prefix() . 'estimates');
    $CI->db->where('id', $estimate_id);
    $parent = $CI->db->get()->row();
    if ($parent && $parent->parent_id != 0) {
        $chain = get_estimate_revision_chain($parent->parent_id, $chain);
        $chain[] = $parent->parent_id;
    }
    return $chain;
}

function get_root_estimate_id($estimate_id)
{
    $CI =& get_instance();
    $CI->db->select('parent_id');
    $CI->db->from(db_prefix() . 'estimates');
    $CI->db->where('id', $estimate_id);
    $parent = $CI->db->get()->row();
    if ($parent && $parent->parent_id != 0) {
        return get_root_estimate_id($parent->parent_id);
    }
    return $estimate_id;
}


function render_estimate_revision_template($id)
{
    $CI =& get_instance();
    $CI->load->model('estimates_model');
    $data = $CI->estimates_model->get_cost_planning_details($id);
    $estimate = $CI->estimates_model->get($id);
    $root_estimate = get_root_estimate_id($id);
    $root_estimate_data = $CI->estimates_model->get_cost_planning_details($root_estimate);
    return $CI->load->view('admin/estimates/estimate_revision_template', ['cost_planning_details' => $data, 'estimate' => $estimate, 'unique_id' => $id, 'root_estimate_data' => $root_estimate_data], true);
}

function find_estimate_revision_bold($val1, $val2) {
    if(empty($val1)) {
        return '';
    } else if(empty($val2)) {
        return '';
    } else if($val1 != $val2) {
        return ' class="revision_bold"';
    } else {
        return '';
    }
}

function get_estimate_all_revision_chain($estimate_id, $chain = [])
{
    $CI =& get_instance();
    $CI->db->select('id, parent_id');
    $CI->db->from(db_prefix() . 'estimates');
    $CI->db->where('id', $estimate_id);
    $CI->db->order_by('id', 'asc');
    $parent = $CI->db->get()->row();
    if (!empty($parent)) {
        $chain = get_estimate_all_revision_chain($parent->parent_id, $chain);
        $chain[] = $parent->id;
    }
    return $chain;
}

function get_sub_head($id)
{
    if(!empty($id)) {
        $CI = &get_instance();
        $CI->db->where('id', $id);
        $sub_head = $CI->db->get(db_prefix() . 'wh_sub_group')->row();
        if(!empty($sub_head)) {
            return $sub_head->sub_group_name;
        }
    }
    return '';
}

function get_estimate_data($est_id)
{
    $CI = &get_instance();
    $CI->db->select('*');
    $CI->db->from(db_prefix() . 'estimates');
    $CI->db->where('id', $est_id);
    $estimate = $CI->db->get()->row();
    if ($estimate) {
        return $estimate;
    }
    return null;

}

function get_package_budget_head_dropdown($id, $name, $value)
{
    $CI = &get_instance();
    $CI->load->model('estimates_model');
    $select  = '<select class="selectpicker" data-width="100%" name="'.$name.'" data-none-selected-text="' . _l('None') . '" data-live-search="true">';
    $package_budget_head = $CI->estimates_model->get_estimate_budget_listing($id);
    foreach ($package_budget_head as $item) {
        $selected = ($item['annexure'] == $value) ? ' selected' : '';
        $select .= '<option value="' . $item['annexure'] . '"' . $selected . '>' . $item['budget_head'] . '</option>';
    }
    $select .= '</select>';
    return $select;
}

function get_package_kind_dropdown($name, $value)
{
    $select  = '<select class="selectpicker" data-width="100%" name="'.$name.'" data-none-selected-text="' . _l('None') . '" data-live-search="true">';
    $kind_options = [
        ['id' => _l('client_supply'), 'name' => _l('client_supply')],
        ['id' => _l('bought_out_items'), 'name' => _l('bought_out_items')]
    ];
    $select .= '<option value=""></option>';
    foreach ($kind_options as $item) {
        $selected = ($item['id'] == $value) ? ' selected' : '';
        $select .= '<option value="' . $item['id'] . '"' . $selected . '>' . $item['name'] . '</option>';
    }
    $select .= '</select>';
    return $select;
}

function get_package_rli_filter_dropdown($name, $value)
{
    $select  = '<select class="selectpicker" data-width="100%" name="'.$name.'" data-none-selected-text="' . _l('None') . '" data-live-search="true">';
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
    $select .= '<option value=""></option>';
    foreach ($status_labels as $key => $status) {
        $selected = ($key == $value) ? ' selected' : '';
        $select .= '<option value="' . $key . '"' . $selected . '>' . $status['text'] . '</option>';
    }
    $select .= '</select>';
    return $select;
}

function add_budget_activity_log($id, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $estimates = $CI->db->get(db_prefix() . 'estimates')->row();
        if(!empty($estimates)) {
            $is_create_value = $is_create ? 'created' : 'deleted';
            $description = "Budget <b>".format_estimate_number($id)."</b> has been ".$is_create_value.".";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'bud',
                'rel_id' => $id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function update_all_budget_fields_activity_log($id, $new_data)
{
    $CI = &get_instance();
    $CI->load->model('staff_model');
    $CI->load->model('invoices_model');
    if (empty($id)) {
        return false;
    }
    $estimates = $CI->db->where('id', $id)
        ->get(db_prefix() . 'estimates')
        ->row();
    if (!$estimates) {
        return false;
    }
    $old_data = (array)$estimates;
    $normalize = function ($value) {
        $value = trim((string)$value);
        if (in_array(strtolower($value), ['null', 'none', 'nil', 'n/a', '-', '--'])) {
            return '';
        }
        if ($value === '0000-00-00') {
            return '';
        }
        if (is_numeric($value)) {
            $num = (float)$value;
            return ($num == 0.0) ? '' : $num;
        }
        return strtolower($value);
    };
    $norm_old = array_map($normalize, $old_data);
    $norm_new = array_map($normalize, $new_data);
    $changes = array_diff_assoc($norm_new, $norm_old);
    if (empty($changes)) {
        return true;
    }
    $field_map = [
        'budget_description' => _l('budget_description'),
        'clientid' => _l('estimate_select_customer'),
        'project_id' => _l('project'),
        'status' => _l('estimate_status'),
        'billing_street' => _l('billing_street'),
        'billing_city' => _l('billing_city'),
        'billing_state' => _l('billing_state'),
        'billing_zip' => _l('billing_zip'),
        'billing_country' => _l('billing_country'),
        'shipping_street' => _l('shipping_street'),
        'shipping_city' => _l('shipping_city'),
        'shipping_state' => _l('shipping_state'),
        'shipping_zip' => _l('shipping_zip'),
        'shipping_country' => _l('shipping_country'),
        'reference_no' => _l('reference_no'),
        'sale_agent' => _l('sale_agent_string'),
        'discount_type' => _l('discount_type'),
        'number' => _l('Budget Number'),
        'adminnote' => _l('estimate_add_edit_admin_note'),
        'date' => _l('Budget Date'),
        'expirydate' => _l('estimate_add_edit_expirydate'),
        'hsn_sac' => _l('hsn_sac'),
        'project_brief' => _l('project_brief'),
        'cost_plan_summary' => _l('Cost Plan Summary'),
        'project_timelines' => _l('project_timelines'),
    ];
    foreach ($changes as $field => $dummy) {
        if (!isset($field_map[$field])) {
            continue;
        }
        $old_value = $old_data[$field] ?? '';
        $new_value = $new_data[$field] ?? '';
        if ($field === 'clientid') {
            $old_value = !empty($old_value) ? get_company_name($old_value) : '';
            $new_value = !empty($new_value) ? get_company_name($new_value) : '';
        }
        if ($field === 'project_id') {
            $old_value = !empty($old_value) ? get_project_name_by_id($old_value) : '';
            $new_value = !empty($new_value) ? get_project_name_by_id($new_value) : '';
        }
        if ($field === 'status') {
            $old_value = !empty($old_value) ? estimate_status_by_id($old_value) : '';
            $new_value = !empty($new_value) ? estimate_status_by_id($new_value) : '';
        }
        if ($field === 'billing_country' || $field === 'shipping_country') {
            $countries = get_all_countries();
            $opts = array_column($countries, 'short_name', 'country_id');
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        if ($field === 'sale_agent') {
            $staff_list = $CI->staff_model->get('', ['active' => 1]);
            $opts = array_combine(
                array_column($staff_list, 'staffid'),
                array_map(fn($a) => $a['firstname'] . ' ' . $a['lastname'], $staff_list)
            );
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        if ($field === 'discount_type') {
            $opts = [
                '' => _l('no_discount'),
                'before_tax' => _l('discount_type_before_tax'),
                'after_tax' => _l('discount_type_after_tax'),
            ];
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        if ($field === 'hsn_sac') {
            $hsn_sac_code = $CI->invoices_model->get_hsn_sac_code();
            $opts = array_column($hsn_sac_code, 'name', 'id');
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        update_budget_activity_log($id, $field_map[$field], $old_value, $new_value);
    }
    return true;
}

function update_budget_activity_log($id, $field, $old_value, $new_value)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $estimates = $CI->db->get(db_prefix() . 'estimates')->row();
        if(!empty($estimates)) {
            $old_value = !empty($old_value) ? $old_value : 'None';
            $new_value = !empty($new_value) ? $new_value : 'None';
            $description = "".$field." field has been updated from <b>".$old_value."</b> to <b>".$new_value."</b> in budget <b>".format_estimate_number($id)."</b>.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'bud',
                'rel_id' => $id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_area_summary_activity_log($id, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $costarea_summary = $CI->db->get(db_prefix() . 'costarea_summary')->row();
        if(!empty($costarea_summary)) {
            $is_create_value = $is_create ? 'added' : 'deleted';
            $category_name = '';
            $area_type = '';
            $CI->db->where('id', $costarea_summary->area_id);
            $area_summary_tabs = $CI->db->get(db_prefix() . 'area_summary_tabs')->row();
            if($costarea_summary->area_id == 1 || $costarea_summary->area_id == 2 || $costarea_summary->area_id == 4) {
                $CI->db->where('id', $costarea_summary->master_area);
                $master_area = $CI->db->get(db_prefix() . 'master_area')->row();
                $category_name = $master_area->category_name;
                $area_type = 'master area';
            }
            if($costarea_summary->area_id == 3) {
                $CI->db->where('id', $costarea_summary->master_area);
                $functionality_area = $CI->db->get(db_prefix() . 'functionality_area')->row();
                $category_name = $functionality_area->category_name;
                $area_type = 'functionality area';
            }
            $description = "The ".$area_type." <b>".$category_name."</b> has been ".$is_create_value." under the <b>".$area_summary_tabs->name."</b> section within the Area Summary tab in budget <b>".format_estimate_number($costarea_summary->estimate_id)."</b>.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'bud',
                'rel_id' => $costarea_summary->estimate_id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_area_statement_tabs_activity_log($id, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $area_statement_tabs = $CI->db->get(db_prefix() . 'area_statement_tabs')->row();
        if(!empty($area_statement_tabs)) {
            $is_create_value = $is_create ? 'created' : 'deleted';
            $description = "The area statement tab <b>".$area_statement_tabs->name."</b> has been ".$is_create_value." in budget <b>".format_estimate_number($area_statement_tabs->estimate_id)."</b>.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'bud',
                'rel_id' => $area_statement_tabs->estimate_id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_area_statement_activity_log($id, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $costarea_working = $CI->db->get(db_prefix() . 'costarea_working')->row();
        if(!empty($costarea_working)) {
            $is_create_value = $is_create ? 'added' : 'deleted';
            $CI->db->where('id', $costarea_working->area_id);
            $area_statement_tabs = $CI->db->get(db_prefix() . 'area_statement_tabs')->row();
            $description = "The Room/Spaces <b>".$costarea_working->area_description."</b> has been ".$is_create_value." under the <b>".$area_statement_tabs->name."</b> section within the Area Statement tab in budget <b>".format_estimate_number($costarea_working->estimate_id)."</b>.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'bud',
                'rel_id' => $costarea_working->estimate_id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_lock_budget_activity_log($id, $lock_budget)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $estimates = $CI->db->get(db_prefix() . 'estimates')->row();
        if(!empty($estimates)) {
            $lock_budget_value = $lock_budget == 1 ? 'Lock' : 'Unlock';
            $description = "Budget <b>".format_estimate_number($id)."</b> has been <b>".$lock_budget_value."</b>.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'bud',
                'rel_id' => $id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_budget_revision_activity_log($id)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $estimates = $CI->db->get(db_prefix() . 'estimates')->row();
        if(!empty($estimates)) {
            $description = "A new revision has been created for budget <b>".format_estimate_number($id)."</b>.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'bud',
                'rel_id' => $id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_budget_attachment_activity_log($id, $file_name, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $files = $CI->db->get(db_prefix() . 'files')->row();
        if(!empty($files)) {
            if($files->rel_type == 'estimate') {
                $is_create_value = $is_create ? 'added' : 'removed';
                $description = "Attachment <b>".$file_name."</b> has been ".$is_create_value." for budget <b>".format_estimate_number($files->rel_id)."</b>.";
                $CI->db->insert(db_prefix() . 'module_activity_log', [
                    'module_name' => 'bud',
                    'rel_id' => $files->rel_id,
                    'description' => $description,
                    'date' => date('Y-m-d H:i:s'),
                    'staffid' => get_staff_user_id(),
                    'project_id' => $default_project
                ]);
            }
        }
    }
    return true;
}

function add_budget_package_activity_log($id, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $estimate_package_info = $CI->db->get(db_prefix() . 'estimate_package_info')->row();
        if(!empty($estimate_package_info)) {
            $is_create_value = $is_create ? 'added' : 'removed';
            $description = "Package <b>".$estimate_package_info->package_name."</b> has been ".$is_create_value." for budget <b>".format_estimate_number($estimate_package_info->estimate_id)."</b>.";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'bud',
                'rel_id' => $estimate_package_info->estimate_id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function add_assign_unawarded_capex_activity_log($id)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $unawarded_budget_info = $CI->db->get(db_prefix() . 'unawarded_budget_info')->row();
        if(!empty($unawarded_budget_info)) {
            $CI->db->select(
                db_prefix() . 'items.commodity_code,' .
                db_prefix() . 'items.description'
            );
            $CI->db->from(db_prefix() . 'itemable');
            $CI->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'itemable.item_code', 'left');
            $CI->db->where(db_prefix() . 'itemable.id', $unawarded_budget_info->item_id);
            $CI->db->group_by(db_prefix() . 'itemable.id');
            $items = $CI->db->get()->row();
            if(!empty($unawarded_budget_info->packages)) {
                $packages = $CI->db->select('package_name')
                ->where_in('id', explode(",", $unawarded_budget_info->packages))
                ->from(db_prefix() . 'estimate_package_info')
                ->get()
                ->result_array();
                $all_packages = !empty($packages) ? implode(', ', array_column($packages, 'package_name')) : '';
                $description = "Packages <b>".$all_packages."</b> have been assigned to item <b>".$items->commodity_code." ".$items->description."</b> under budget head <b>".get_group_name_by_id($unawarded_budget_info->budget_head)."</b> and budget <b>".format_estimate_number($unawarded_budget_info->estimate_id)."</b>.";
                $CI->db->insert(db_prefix() . 'module_activity_log', [
                    'module_name' => 'bud',
                    'rel_id' => $unawarded_budget_info->estimate_id,
                    'description' => $description,
                    'date' => date('Y-m-d H:i:s'),
                    'staffid' => get_staff_user_id(),
                    'project_id' => $default_project
                ]);
            }
        }
    }
    return true;
}

function update_estimate_budget_info_activity_log($id, $type)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $CI->db->where('id', $id);
        $estimate_budget_info = $CI->db->get(db_prefix() . 'estimate_budget_info')->row();
        if(!empty($estimate_budget_info)) {
            if($type == 'detailed_costing') {
                $description = "The detailed costing summary has been updated to <b>".$estimate_budget_info->detailed_costing."</b> under budget head <b>".get_group_name_by_id($estimate_budget_info->budget_id)."</b> and budget <b>".format_estimate_number($estimate_budget_info->estimate_id)."</b>.";
            } else if($type == 'budget_summary_remarks') {
                $description = "The remarks has been updated to <b>".$estimate_budget_info->budget_summary_remarks."</b> under budget head <b>".get_group_name_by_id($estimate_budget_info->budget_id)."</b> and budget <b>".format_estimate_number($estimate_budget_info->estimate_id)."</b>.";
            } else if($type == 'overall_budget_area') {
                $description = "The overall area has been updated to <b>".$estimate_budget_info->overall_budget_area."</b> under budget head <b>".get_group_name_by_id($estimate_budget_info->budget_id)."</b> and budget <b>".format_estimate_number($estimate_budget_info->estimate_id)."</b>.";
            } else {
                $description = '';
            }
            if(!empty($description)) {
                $CI->db->insert(db_prefix() . 'module_activity_log', [
                    'module_name' => 'bud',
                    'rel_id' => $estimate_budget_info->estimate_id,
                    'description' => $description,
                    'date' => date('Y-m-d H:i:s'),
                    'staffid' => get_staff_user_id(),
                    'project_id' => $default_project
                ]);
            }
        }
    }
    return true;
}

function add_budget_item_activity_log($id, $is_create = true)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if(!empty($id)) {
        $is_create_value = $is_create ? 'added' : 'removed';
        $CI->db->select(
            db_prefix() . 'itemable.rel_id,' .
            db_prefix() . 'itemable.rel_type,' .
            db_prefix() . 'itemable.annexure,' .
            db_prefix() . 'items.commodity_code,' .
            db_prefix() . 'items.description'
        );
        $CI->db->from(db_prefix() . 'itemable');
        $CI->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'itemable.item_code', 'left');
        $CI->db->where(db_prefix() . 'itemable.id', $id);
        $CI->db->group_by(db_prefix() . 'itemable.id');
        $items = $CI->db->get()->row();
        if(!empty($items)) {
            if($items->rel_type == 'estimate') {
                $description = "Item <b>".$items->commodity_code." ".$items->description."</b> has been ".$is_create_value." under budget head <b>".get_group_name_by_id($items->annexure)."</b> and budget <b>".format_estimate_number($items->rel_id)."</b>.";
                $CI->db->insert(db_prefix() . 'module_activity_log', [
                    'module_name' => 'bud',
                    'rel_id' => $items->rel_id,
                    'description' => $description,
                    'date' => date('Y-m-d H:i:s'),
                    'staffid' => get_staff_user_id(),
                    'project_id' => $default_project
                ]);
            }
        }
    }
    return true;
}

function update_budget_item_activity_log($new_data)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if (empty($new_data['itemid'])) {
        return false;
    }
    $itemable = $CI->db->where('id', $new_data['itemid'])
        ->get(db_prefix() . 'itemable')
        ->row();
    if (!$itemable) {
        return false;
    }
    $old_data = (array)$itemable;
    if (isset($old_data['item_code'])) {
        $old_data['item_name'] = $old_data['item_code'];
    }
    if (isset($old_data['area'])) {
        $areaArray = is_array($old_data['area']) ? $old_data['area'] : explode(',', $old_data['area']);
        $areaArray = array_map('trim', $areaArray);
        $areaArray = array_filter($areaArray, fn($v) => $v !== '');
        sort($areaArray, SORT_NUMERIC);
        $old_data['area'] = implode(',', $areaArray);
    }
    if (isset($new_data['area']) && is_array($new_data['area'])) {
        $areaArray = array_map('trim', $new_data['area']);
        $areaArray = array_filter($areaArray, fn($v) => $v !== '');
        sort($areaArray, SORT_NUMERIC);
        $new_data['area'] = implode(',', $areaArray);
    }
    $normalize = function ($value) {
        $value = trim((string)$value);
        if (in_array(strtolower($value), ['null', 'none', 'nil', 'n/a', '-', '--'])) {
            return '';
        }
        if ($value === '0000-00-00') {
            return '';
        }
        if (is_numeric($value)) {
            $num = (float)$value;
            return ($num == 0.0) ? '' : $num;
        }
        return strtolower($value);
    };
    $norm_old = array_map($normalize, $old_data);
    $norm_new = array_map($normalize, $new_data);
    $changes = array_diff_assoc($norm_new, $norm_old);
    if (empty($changes)) {
        return true;
    }
    $field_map = [
        'item_name' => _l('estimate_table_item_heading'),
        'long_description' => _l('estimate_table_item_description'),
        'sub_head' => _l('sub_head'),
        'area' => _l('area'),
        'qty' => _l('estimate_table_quantity_heading'),
        'unit_id' => _l('Unit'),
        'rate' => _l('estimate_table_rate_heading'),
        'remarks' => _l('remarks'),
    ];
    foreach ($changes as $field => $dummy) {
        if (!isset($field_map[$field])) {
            continue;
        }
        $old_value = $old_data[$field] ?? '';
        $new_value = $new_data[$field] ?? '';
        if ($field === 'item_name') {
            $old_value = !empty($old_value) ? pur_get_item_variatiom($old_value) : '';
            $new_value = !empty($new_value) ? pur_get_item_variatiom($new_value) : '';
        }
        if ($field === 'sub_head') {
            $old_value = !empty($old_value) ? get_sub_head_name_by_id($old_value) : '';
            $new_value = !empty($new_value) ? get_sub_head_name_by_id($new_value) : '';
        }
        if ($field === 'area') {
            $old_value = !empty($old_value) ? get_area_name_by_id($old_value) : '';
            $new_value = !empty($new_value) ? get_area_name_by_id($new_value) : '';
        }
        if ($field === 'unit_id') {
            $old_value = !empty($old_value) ? pur_get_unit_name($old_value) : '';
            $new_value = !empty($new_value) ? pur_get_unit_name($new_value) : '';
        }

        $CI->db->select(
            db_prefix() . 'itemable.rel_id,' .
            db_prefix() . 'itemable.rel_type,' .
            db_prefix() . 'itemable.annexure,' .
            db_prefix() . 'items.commodity_code,' .
            db_prefix() . 'items.description'
        );
        $CI->db->from(db_prefix() . 'itemable');
        $CI->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'itemable.item_code', 'left');
        $CI->db->where(db_prefix() . 'itemable.id', $new_data['itemid']);
        $CI->db->group_by(db_prefix() . 'itemable.id');
        $items = $CI->db->get()->row();
        $old_value = !empty($old_value) ? $old_value : 'None';
        $new_value = !empty($new_value) ? $new_value : 'None';
        $description = "".$field_map[$field]." field is updated from <b>".$old_value."</b> to <b>".$new_value."</b> for item <b>".$items->commodity_code." ".$items->description."</b> under budget head <b>".get_group_name_by_id($items->annexure)."</b> and budget <b>".format_estimate_number($items->rel_id)."</b>.";
        $module_name = 'bud';
        $rel_id = $old_data['rel_id'];
        if(!empty($description)) {
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => $module_name,
                'rel_id' => $rel_id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function update_budget_package_activity_log($id, $new_data)
{
    $CI = &get_instance();
    $default_project = get_default_project();
    if (empty($id)) {
        return false;
    }
    $estimate_package_info = $CI->db->where('id', $id)
        ->get(db_prefix() . 'estimate_package_info')
        ->row();
    if (!$estimate_package_info) {
        return false;
    }
    $old_data = (array)$estimate_package_info;
    $normalize = function ($value) {
        $value = trim((string)$value);
        if (in_array(strtolower($value), ['null', 'none', 'nil', 'n/a', '-', '--'])) {
            return '';
        }
        if ($value === '0000-00-00') {
            return '';
        }
        if (is_numeric($value)) {
            $num = (float)$value;
            return ($num == 0.0) ? '' : $num;
        }
        return strtolower($value);
    };
    $norm_old = array_map($normalize, $old_data);
    $norm_new = array_map($normalize, $new_data);
    $changes = array_diff_assoc($norm_new, $norm_old);
    if (empty($changes)) {
        return true;
    }
    $field_map = [
        'project_awarded_date' => _l('Project Awarded Date'),
        'package_name' => _l('Package Name'),
        'kind' => _l('cat'),
        'rli_filter' => _l('rli_filter'),
        'sdeposit_percent' => _l('Secured Deposit'),
    ];
    foreach ($changes as $field => $dummy) {
        if (!isset($field_map[$field])) {
            continue;
        }
        $old_value = $old_data[$field] ?? '';
        $new_value = $new_data[$field] ?? '';
        if ($field === 'kind') {
            $opts = [
                'Client Supply' => _l('client_supply'),
                'Bought out items' => _l('bought_out_items'),
            ];
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        if ($field === 'rli_filter') {
            $opts = [
                0 => _l('provided_by_ril'),
                1 => _l('new_item_service_been_addded_as_per_instruction'),
                2 => _l('due_to_spec_change_then_original_cost'),
                3 => _l('deal_slip'),
                4 => _l('to_be_provided_by_ril_but_managed_by_bil'),
                5 => _l('due_to_additional_item_as_per_apex_instrution'),
                6 => _l('event_expense'),
                7 => _l('pending_procurements'),
                8 => _l('common_services_in_ghj_scope'),
                9 => _l('common_services_in_ril_scope'),
                10 => _l('due_to_site_specfic_constraint'),
            ];
            $old_value = $opts[$old_value] ?? '';
            $new_value = $opts[$new_value] ?? '';
        }
        $old_value = !empty($old_value) ? $old_value : 'None';
        $new_value = !empty($new_value) ? $new_value : 'None';
        $description = "".$field_map[$field]." field is updated from <b>".$old_value."</b> to <b>".$new_value."</b> for package <b>".$estimate_package_info->package_name."</b> under budget head <b>".get_group_name_by_id($estimate_package_info->budget_head)."</b> and budget <b>".format_estimate_number($estimate_package_info->estimate_id)."</b>.";
        $module_name = 'bud';
        $rel_id = $estimate_package_info->estimate_id;
        if(!empty($description)) {
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => $module_name,
                'rel_id' => $rel_id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'project_id' => $default_project
            ]);
        }
    }
    return true;
}

function get_total_budgeted_amount($estimate_id, $budget_head = null)
{
    if (empty($estimate_id)) {
        return 0;
    }
    $CI = &get_instance();
    $CI->db->select('SUM(qty * rate) AS total');
    $CI->db->from(db_prefix() . 'itemable');
    $CI->db->where('rel_id', $estimate_id);
    $CI->db->where('rel_type', 'estimate');
    if (!empty($budget_head)) {
        $CI->db->where('annexure', $budget_head);
    }
    $row = $CI->db->get()->row();
    return ($row && $row->total !== null) ? $row->total : 0;
}

