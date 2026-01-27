<?php
defined('BASEPATH') or exit('No direct script access allowed');

$month = $this->ci->input->post('month');

$aColumns = [
    'tblassar_clients.name',
    'tblassar_clients.investment',
    'm.assar_holds',
    'm.client_earnings'
];

$sIndexColumn = 'tblassar_clients.id';
$sTable = db_prefix().'assar_clients';

$join = [
    'LEFT JOIN tbl_assar_main_sheet m
     ON m.client_id = tblassar_clients.id
     AND m.month_year = "'.$month.'"'
];

$where = [];
$having = '';

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    ['tblassar_clients.id'],
    '',
    [],
    $having
);

$output  = $result['output'];
$rResult = $result['rResult'];

$aColumns = array_map(function ($col) {
    if (stripos($col, ' as ') !== false) {
        $parts = preg_split('/\s+as\s+/i', $col);
        return trim($parts[1], '"` ');
    }
    return trim($col, '"` ');
}, $aColumns);

foreach ($rResult as $aRow) {

    $row = [];

    foreach ($aColumns as $col) {

        $_data = $aRow[$col] ?? '';

        if ($col == 'name') {

            $_data = $aRow['name'];

        } elseif ($col == 'investment') {

            $_data = app_format_money($aRow['investment'], '₹');

        } elseif ($col == 'm.assar_holds') {

            $_data = '<input type="number"
                class="form-control assar-input"
                data-client="'.$aRow['id'].'"
                data-first="1"
                value="'.($aRow['assar_holds'] ?? 0).'">';

        } elseif ($col == 'm.client_earnings') {

            $_data = '<span class="earning-text">'
                . app_format_money($aRow['client_earnings'] ?? 0, '₹') .
                '</span>';
        }

        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
