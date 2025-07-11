<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'sub_group_code',
    'sub_group_name',
    'group_id',
    'project_id',
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'wh_sub_group';
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
    $sub_group_code = $aRow['sub_group_code'];
    $sub_group_name = $aRow['sub_group_name'];
    $group_id = $aRow['group_id'];

    $group_name = '';
    if ($aRow['group_id']) {
        $group = get_group_name_pur($aRow['group_id']);
        if ($group) {
            $group_name = $group->name;
        }
    }

    $row[] = $key + 1;
    $row[] = $sub_group_code;
    $row[] = $sub_group_name;
    $row[] = $group_name;
    $row[] = get_project_name_by_id($aRow['project_id']);

    $options = '';

    if (has_permission('purchase_settings', '', 'edit') || is_admin()) {
        $options .= '<a href="#" onclick="edit_sub_group_type(this,' . pur_html_entity_decode($id) . '); return false;"';
        $options .= ' data-sub_group_code="' . pur_html_entity_decode($sub_group_code) . '"';
        $options .= ' data-sub_group_name="' . pur_html_entity_decode($sub_group_name) . '"';
        $options .= ' data-group_id="' . pur_html_entity_decode($group_id) . '"';
        $options .= ' class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>';
    }

    if (has_permission('purchase_settings', '', 'edit') || is_admin()) {
        $options .= ' <a href="' . admin_url('purchase/delete_sub_group/' . $id) . '" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>';
    }

    $row[] = $options;

    $output['aaData'][] = $row;
}

?>
