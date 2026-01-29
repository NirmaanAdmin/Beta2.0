<?php

defined('BASEPATH') or exit('No direct script access allowed');

$month = $this->ci->input->post('month');

$aColumns = [
    'client_id',
    'name',
    'investment',
    1,
    2,
    3,
    4,
    5,
    6,
    7,
    8,
    9,
    10,
    11
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'assar_clients';
$join = [];

$where = [];


$having = '';

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    ['id'],
    '',
    [],
    $having
);

$output  = $result['output'];
$rResult = $result['rResult'];
$footer_data = [
    'investment' => 0,
];

$aColumns = array_map(function ($col) {
    $col = trim($col);
    if (stripos($col, ' as ') !== false) {
        $parts = preg_split('/\s+as\s+/i', $col);
        return trim($parts[1], '"` ');
    }
    return trim($col, '"` ');
}, $aColumns);

// Calculate total days of selected month
if (!empty($month)) {

    // If month is like 2026-01
    if (strpos($month, '-') !== false) {
        $total_days = date('t', strtotime($month . '-01'));
    }
    // If only month number (01-12)
    else {
        $year = date('Y');
        $total_days = cal_days_in_month(CAL_GREGORIAN, (int)$month, $year);
    }
} else {
    $total_days = date('t'); // fallback current month
}

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'client_id') {
            $_data = $aRow['client_id'];
        } elseif ($aColumns[$i] == 'name') {
            $_data = $aRow['name'];
        } elseif ($aColumns[$i] == 'investment') {
            $_data = app_format_money($aRow['investment'], '₹');
        } elseif ($aColumns[$i] == 1) {
            $_data = app_format_money($aRow['investment'], '₹');
        } elseif ($aColumns[$i] == 2) {
            $_data = $total_days;
        } elseif ($aColumns[$i] == 3) {
            $_data = 'Total P&L';
        } elseif ($aColumns[$i] == 4) {
            $_data = 'Rolled Over? (Y/N)';
        } elseif ($aColumns[$i] == 5) {
            $_data = 'Commission';
        } elseif ($aColumns[$i] == 6) {
            $_data = 'Payout GROSS';
        } elseif ($aColumns[$i] == 7) {
            $_data = 'TDS';
        } elseif ($aColumns[$i] == 8) {
            $_data = 'Payout Net';
        } elseif ($aColumns[$i] == 9) {
            $_data = 'Payout Date';
        } elseif ($aColumns[$i] == 10) {
            $_data = 'Notes';
        } elseif ($aColumns[$i] == 11) {
            $_data = 'Net Rollover';
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
    $sr++;
}
