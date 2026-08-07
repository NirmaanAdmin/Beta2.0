<?php

defined('BASEPATH') or exit('No direct script access allowed');

$module_name = 'purchase_area';
$project_filter_name = 'project';

$aColumns = [
    'id',
    'id',
    'area_name',
    'project',
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'area';
$join         = [];
$where        = [];

if ($this->ci->input->post('project')) {
    $project = $this->ci->input->post('project');
    array_push($where, 'AND project = ' . $project . '');
}

$project_filter_name_value = !empty($this->ci->input->post('project')) ? $this->ci->input->post('project') : NULL;
update_module_filter($module_name, $project_filter_name, $project_filter_name_value);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [$sIndexColumn]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $key => $aRow) {
    $row = [];

    $id = $aRow['id'];
    $area_name = $aRow['area_name'];
    $project = $aRow['project'];

    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $id . '"><label></label></div>';
    $row[] = $key + 1;
    $row[] = $area_name;
    $row[] = get_project_name_by_id($project);

    $options = '';
    $options .= '<a href="#" onclick="edit_area(this,' . $id . '); return false;"';
    $options .= ' data-name="' . htmlspecialchars($area_name, ENT_QUOTES) . '"';
    $options .= ' data-project="' . htmlspecialchars($project, ENT_QUOTES) . '"';
    $options .= ' class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>';

    $options .= '<a href="' . admin_url('purchase/delete_area/' . $id) . '" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>';

    $row[] = $options;

    $output['aaData'][] = $row;
}

?>