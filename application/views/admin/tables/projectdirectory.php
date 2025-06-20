<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'postion',
    'staff',
    'vendor',
    'fullname',
    'contact',
    'email_account',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'project_directory';

$join = [];

$where = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

$sr = 1;
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'postion') {
            $_data = $aRow['postion'];
        } elseif ($aColumns[$i] == 'staff') {
            $_data = get_staff_namebyId($aRow['staff']);
        } elseif ($aColumns[$i] == 'vendor') {
            $_data = get_vendor_company_name($aRow['vendor']);
        } elseif ($aColumns[$i] == 'fullname') {
            $_data = $aRow['fullname'];
        } elseif ($aColumns[$i] == 'contact') {
            $_data = $aRow['contact'];
        } elseif ($aColumns[$i] == 'email_account') {
            $_data = $aRow['email_account'];
        } elseif ($aColumns[$i] == 'id') {
            $_data = $sr++;
        }
        $row[] = $_data;
    }


    $output['aaData'][] = $row;
}
