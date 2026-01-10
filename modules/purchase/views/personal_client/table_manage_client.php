<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('purchase/purchase_model');


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
            $_data = app_format_money($aRow['investment'],'₹');
        } elseif ($aColumns[$i] == 'frequency') {
            $_data = $aRow['frequency'];
        } elseif ($aColumns[$i] == 'august_2025') {
            $_data = app_format_money($aRow['august_2025'],'₹');
        } elseif ($aColumns[$i] == 'september_2025') {
            $_data = app_format_money($aRow['september_2025'],'₹');
        } elseif ($aColumns[$i] == 'october_2025') {
            $_data = app_format_money($aRow['october_2025'],'₹');
        } elseif ($aColumns[$i] == 'november_2025') {
            $_data = app_format_money($aRow['november_2025'],'₹');
        } elseif ($aColumns[$i] == 'december_2025') {
            $_data = app_format_money($aRow['december_2025'],'₹');
        } elseif ($aColumns[$i] == 'earned_to_date') {
            $_data = app_format_money($aRow['earned_to_date'],'₹');
        } elseif ($aColumns[$i] == 'percent_profits') {
            $_data = $aRow['percent_profits'];
        } 

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
    $sr++;
}
