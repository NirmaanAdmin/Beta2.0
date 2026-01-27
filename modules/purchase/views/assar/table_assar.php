<?php

defined('BASEPATH') or exit('No direct script access allowed');




$aColumns = [
    'client_id',
    'name',
    'phone',
    'start_date',
    'investment',
    'status',
    'refferred_by',
    'remarks',
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
foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'client_id') {
            $_data = $aRow['client_id'];
        } elseif ($aColumns[$i] == 'name') {
            $_data = $aRow['name'];

            $numberOutput = '';
            $numberOutput .= $aRow['name'];

            $numberOutput .= '<div class="row-options">';

            $numberOutput .= '<a href="' . admin_url('purchase/add_assar/' . $aRow['id']) . '">' . _l('edit') . '</a>';

            $numberOutput .= ' | <a href="' . admin_url('purchase/delete_assar/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';

            $numberOutput .= '</div>';

            $_data = $numberOutput;
        } elseif ($aColumns[$i] == 'phone') {
            $_data = $aRow['phone'];
        } elseif ($aColumns[$i] == 'start_date') {
            $_data = date('d M, Y', strtotime($aRow['start_date']));
        } elseif ($aColumns[$i] == 'investment') {
            $_data = app_format_money($aRow['investment'], '₹');
        } elseif ($aColumns[$i] == 'status') {
            $status = '';
            if ($aRow['status'] == 1) {
                $status = 'Active';
            } else {
                $status = 'Inactive';
            }
            $_data = $status;
        } elseif ($aColumns[$i] == 'refferred_by') {
            $_data = $aRow['refferred_by'];
        } elseif ($aColumns[$i] == 'remarks') {
            $_data = $aRow['remarks'];
        }
        $row[] = $_data;
    }
    $footer_data['investment'] += $aRow['investment'];
    $output['aaData'][] = $row;
    $sr++;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, '₹');
}
$output['sums'] = $footer_data;