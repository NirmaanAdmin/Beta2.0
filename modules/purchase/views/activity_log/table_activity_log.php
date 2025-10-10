<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('purchase/purchase_model');
$module_name = 'module_activity_log';
$module_name_filter_name = 'module_name';
$staff_filter_name = 'staff';

$aColumns = [
    db_prefix() . 'module_activity_log' . '.description as description',
    db_prefix() . 'module_activity_log' . '.date as date',
    db_prefix() . 'module_activity_log' . '.staffid as staffid',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'module_activity_log';
$join = [];

$where = [];
$this->ci->load->model('projects_model');
$get_project_id = get_default_project();


if (isset($get_project_id)) {
    array_push($where, ' AND '.db_prefix().'module_activity_log.project_id = '.$get_project_id);
}

if ($this->ci->input->post('module_name') && count($this->ci->input->post('module_name')) > 0) {
    $modules = array_map(function ($m) {
        return "'" . $m . "'";
    }, $this->ci->input->post('module_name'));
    array_push(
        $where,
        'AND ' . db_prefix() . 'module_activity_log.module_name IN (' . implode(',', $modules) . ')'
    );
}

if ($this->ci->input->post('staff') && count($this->ci->input->post('staff')) > 0) {
    $staffs = $this->ci->input->post('staff');
    $conditions = [];
    foreach ($staffs as $p) {
        $conditions[] = "FIND_IN_SET(" . (int)$p . ", " . db_prefix() . "module_activity_log.staffid)";
    }
    $where[] = "AND (" . implode(' OR ', $conditions) . ")";
}

$custom_date_select = $this->ci->purchase_model->get_where_report_period(db_prefix().'module_activity_log.date');
if ($custom_date_select != '') {
    $custom_date_select = trim($custom_date_select);
    if (!startsWith($custom_date_select, 'AND')) {
        $custom_date_select = 'AND ' . $custom_date_select;
    }
    $where[] = $custom_date_select;
}

$module_name_filter_name_value = !empty($this->ci->input->post('module_name')) ? implode(',', $this->ci->input->post('module_name')) : NULL;
update_module_filter($module_name, $module_name_filter_name, $module_name_filter_name_value);

$staff_filter_name_value = !empty($this->ci->input->post('staff')) ? implode(',', $this->ci->input->post('staff')) : NULL;
update_module_filter($module_name, $staff_filter_name, $staff_filter_name_value);

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

        if ($aColumns[$i] == 'description') {
            $_data = html_entity_decode($aRow['description']);
        } elseif ($aColumns[$i] == 'date') {
            $_data = _dt($aRow['date']);
        } elseif ($aColumns[$i] == 'staffid') {
            $_data = get_last_action_full_name($aRow['staffid']);
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
    $sr++;
}
