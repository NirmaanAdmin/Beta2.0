<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('gdpr_model');

$consentContacts = get_option('gdpr_enable_consent_for_contacts');
$aColumns        = [ 'firstname', 'lastname'];
if (is_gdpr() && $consentContacts == '1') {
    array_push($aColumns, '1');
}

$aColumns = array_merge($aColumns, [
    'email',
    'company',
    db_prefix() . 'pur_contacts.phonenumber as phonenumber',
    'title',

    db_prefix() . 'pur_contacts.active as active',
]);

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'pur_contacts';
$join         = ['JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid=' . db_prefix() . 'pur_contacts.userid'];

$custom_fields = get_table_custom_fields('contacts');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'pur_contacts.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$where = [];

if (!has_permission('changee_vendors', '', 'view')) {
    array_push($where, 'AND ' . db_prefix() . 'pur_contacts.userid IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ')');
}

if ($this->ci->input->post('custom_view')) {
    $filter = $this->ci->input->post('custom_view');
    if (startsWith($filter, 'consent_')) {
        array_push($where, 'AND ' . db_prefix() . 'pur_contacts.id IN (SELECT contact_id FROM ' . db_prefix() . 'consents WHERE purpose_id=' . strafter($filter, 'consent_') . ' and action="opt-in" AND date IN (SELECT MAX(date) FROM ' . db_prefix() . 'consents WHERE purpose_id=' . strafter($filter, 'consent_') . ' AND contact_id=' . db_prefix() . 'pur_contacts.id))');
    }
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'pur_contacts.id as id', db_prefix() . 'pur_contacts.userid as userid', 'is_primary', '(SELECT count(*) FROM ' . db_prefix() . 'pur_contacts c WHERE c.userid=' . db_prefix() . 'pur_contacts.userid) as total_contacts', db_prefix() . 'pur_vendor.registration_confirmed as registration_confirmed']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $rowName = '<a href="#" onclick="vendor_contact(' . $aRow['userid'] . ',' . $aRow['id'] . ');return false;">' . $aRow['firstname'] . '</a>';

    $rowName .= '<div class="row-options">';

    $rowName .= '<a href="#" onclick="vendor_contact(' . $aRow['userid'] . ',' . $aRow['id'] . ');return false;">' . _l('edit') . '</a>';

    if (has_permission('changee_vendors', '', 'delete') || changee_is_vendor_admin($aRow['userid']) || is_admin()) {
        if ($aRow['is_primary'] == 0 || ($aRow['is_primary'] == 1 && $aRow['total_contacts'] == 1)) {
            $rowName .= ' | <a href="' . admin_url('changee/delete_vendor_contact/' . $aRow['userid'] . '/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
        }
    }

    $rowName .= '</div>';

    $row[] = $rowName;

    $row[] = $aRow['lastname'];

    if (is_gdpr() && $consentContacts == '1') {
        $consentHTML = '<p class="bold"><a href="#" onclick="view_contact_consent(' . $aRow['id'] . '); return false;">' . _l('view_consent') . '</a></p>';
        $consents    = $this->ci->gdpr_model->get_consent_purposes($aRow['id'], 'contact');
        foreach ($consents as $consent) {
            $consentHTML .= '<p class="m-bot-0">' . $consent['name'] . (!empty($consent['consent_given']) ? '<i class="fa fa-check text-success pull-right"></i>' : '<i class="fa fa-remove text-danger pull-right"></i>') . '</p>';
        }
        $row[] = $consentHTML;
    }

    $row[] = '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>';

    if (!empty($aRow['company'])) {
        $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';
    } else {
        $row[] = '';
    }

    $row[] = '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>';

    $row[] = $aRow['title'];

    $row[] = (!empty($aRow['last_login']) ? '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _dt($aRow['last_login']) . '">' . time_ago($aRow['last_login']) . '</span>' : '');

    $outputActive = '<div class="onoffswitch">
                <input type="checkbox"' . ($aRow['registration_confirmed'] == 0 ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'clients/change_contact_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '"' . ($aRow['active'] == 1 ? ' checked': '') . '>
                <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
            </div>';
    // For exporting
    $outputActive .= '<span class="hide">' . ($aRow['active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    $row[] = $outputActive;
    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $row['DT_RowClass'] = 'has-row-options';

    if ($aRow['registration_confirmed'] == 0) {
        $row['DT_RowClass'] .= ' alert-info requires-confirmation';
        $row['Data_Title']  = _l('customer_requires_registration_confirmation');
        $row['Data_Toggle'] = 'tooltip';
    }
    $output['aaData'][] = $row;
}
