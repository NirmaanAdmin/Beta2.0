<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'commodity_group_code',
    'name',
    'project_id',
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'items_groups';
$join         = [];
$where        = [];

if ($this->ci->input->post('project')) {
    $project = $this->ci->input->post('project');
    array_push($where, 'AND (project_id IS NULL OR project_id = ' . $project . ')');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [$sIndexColumn]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $key => $aRow) {
    $row = [];

    $id = $aRow['id'];
    $commodity_group_code = $aRow['commodity_group_code'];
    $name = $aRow['name'];

    $row[] = $key + 1;
    $row[] = $commodity_group_code;
    $row[] = $name;
    $row[] = get_project_name_by_id($aRow['project_id']);

    $options = '';

    if (has_permission('purchase_settings', '', 'edit') || is_admin()) {
        $options .= '<a href="#" onclick="edit_commodity_group_type(this,' . pur_html_entity_decode($id) . '); return false;"';
        $options .= ' data-commodity_group_code="' . pur_html_entity_decode($commodity_group_code) . '"';
        $options .= ' data-name="' . pur_html_entity_decode($name) . '"';
        $options .= ' class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>';
    }

    if (has_permission('purchase_settings', '', 'edit') || is_admin()) {
        $options .= ' <a href="' . admin_url('purchase/delete_commodity_group_type/' . $id) . '" class="btn btn-danger btn-icon delete_commodity_group_type"><i class="fa fa-remove"></i></a>';
    }

    $row[] = $options;

    $output['aaData'][] = $row;
}

?>
