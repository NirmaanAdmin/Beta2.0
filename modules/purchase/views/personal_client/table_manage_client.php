<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('purchase/purchase_model');
$module_name = 'per_client';
$pre_client_name_filter = 'per_client';
$month_filter_name = 'months';
$frequency_filter_name = 'frequency';

$aColumns = [
    'client_id',
    'name',
    'phone',
    'start_date',
    'investment',
    'frequency',
    'august_2025',
    'september_2025',
    'october_2025',
    'november_2025',
    'december_2025',
    'earned_to_date',
    'percent_profits',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . '_per_clients';
$join = [];

$where = [];

if ($this->ci->input->post('per_client') && count($this->ci->input->post('per_client')) > 0) {
    $clients = $this->ci->input->post('per_client');
    $conditions = [];
    foreach ($clients as $client_code) {
        // Sanitize the client code
        $clean_client_code = $this->ci->db->escape($client_code);
        $conditions[] = db_prefix() . "_per_clients.id = " . $clean_client_code;
    }
    $where[] = "AND (" . implode(' OR ', $conditions) . ")";
}
if ($this->ci->input->post('months') && $this->ci->input->post('months') != '') {
    $year = $this->ci->input->post('months');
    $clean_year = (int)$year; // Sanitize as integer
    // Use YEAR() function to extract year from date
    $where[] = " AND YEAR(" . db_prefix() . "_per_clients.start_date) = " . $clean_year;
}

if ($this->ci->input->post('frequency') && $this->ci->input->post('frequency') != '') {
    $frequency = $this->ci->input->post('frequency');
    if ($frequency !== 'all') {
        $where[] = " AND " . db_prefix() . "_per_clients.frequency = '" . $this->ci->db->escape_str($frequency) . "'";
    }
}
$per_client_filter_name_value = !empty($this->ci->input->post('per_client')) ? implode(',', $this->ci->input->post('per_client')) : NULL;
update_module_filter($module_name, $pre_client_name_filter, $per_client_filter_name_value);

$month_filter_name_value = !empty($this->ci->input->post('months')) ? $this->ci->input->post('months') : NULL;
update_module_filter($module_name, $month_filter_name, $month_filter_name_value);

$frequency_filter_name_value = !empty($this->ci->input->post('frequency')) ? $this->ci->input->post('frequency') : NULL;
update_module_filter($module_name, $frequency_filter_name, $frequency_filter_name_value);

$having = '';

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    [],
    '',
    [],
    $having
);

$output  = $result['output'];
$rResult = $result['rResult'];

$aColumns = array_map(function ($col) {
    $col = trim($col);
    if (stripos($col, ' as ') !== false) {
        $parts = preg_split('/\s+as\s+/i', $col);
        return trim($parts[1], '"` ');
    }
    return trim($col, '"` ');
}, $aColumns);
foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'client_id') {
            $_data = $aRow['client_id'];
        } elseif ($aColumns[$i] == 'name') {
            $_data = $aRow['name'];
        } elseif ($aColumns[$i] == 'phone') {
            $_data = $aRow['phone'];
        } elseif ($aColumns[$i] == 'start_date') {
            $_data = date('d M, Y', strtotime($aRow['start_date']));
        } elseif ($aColumns[$i] == 'investment') {
            $_data = app_format_money($aRow['investment'], '₹');
        } elseif ($aColumns[$i] == 'frequency') {
            $_data = $aRow['frequency'];
        } elseif ($aColumns[$i] == 'august_2025') {
            $_data = app_format_money($aRow['august_2025'], '₹');
        } elseif ($aColumns[$i] == 'september_2025') {
            $_data = app_format_money($aRow['september_2025'], '₹');
        } elseif ($aColumns[$i] == 'october_2025') {
            $_data = app_format_money($aRow['october_2025'], '₹');
        } elseif ($aColumns[$i] == 'november_2025') {
            $_data = app_format_money($aRow['november_2025'], '₹');
        } elseif ($aColumns[$i] == 'december_2025') {
            $_data = app_format_money($aRow['december_2025'], '₹');
        } elseif ($aColumns[$i] == 'earned_to_date') {
            $_data = app_format_money($aRow['earned_to_date'], '₹');
        } elseif ($aColumns[$i] == 'percent_profits') {
            $_data = $aRow['percent_profits'];
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
    $sr++;
}
