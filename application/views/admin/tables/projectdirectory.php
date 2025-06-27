<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'com_con_name',
    'address',
    'fullname',
    'designation',
    'contact',
    'email_account',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'project_directory';

$join = [];


$where = [];


$project = $this->ci->input->post('projects');  // Changed from 'projects' to 'project'
if (!empty($project)) {
    $where_project = ' AND project_id = "' . $this->ci->db->escape_str($project) . '"';
    array_push($where, $where_project);
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['project_id']);

$output  = $result['output'];
$rResult = $result['rResult'];
$hasPermissionDelete = staff_can('delete',  'projects');
$sr = 1;
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'com_con_name') {
            $name =  $aRow['com_con_name'];
            $name .= '<div class="row-options">';
            if ($hasPermissionDelete) {
                $name .= '<a href="' . admin_url('projects/deleteprojectdirectory/' . $aRow['id'].'/'.$aRow['project_id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $name .= '</div>';
            $_data = $name;
        } elseif ($aColumns[$i] == 'address') {
            $_data = $aRow['address'];
        } elseif ($aColumns[$i] == 'designation') {
            $_data = $aRow['designation'];
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
