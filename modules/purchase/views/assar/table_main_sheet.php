<?php

defined('BASEPATH') or exit('No direct script access allowed');




$aColumns = [
    'name',
    'investment',
    'status',
    1,
    2,
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

        if ($aColumns[$i] == 'name') {
            $_data = $aRow['name'];
        } elseif ($aColumns[$i] == 'investment') {
            $_data = app_format_money($aRow['investment'], '₹');
        } elseif ($aColumns[$i] == 1) {
            $_data = '---';
        } elseif ($aColumns[$i] == 2) {
            $_data = '---';
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
    $sr++;
}
