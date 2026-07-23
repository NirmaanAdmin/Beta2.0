<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'module_activity_log' . '.description as description',
    db_prefix() . 'module_activity_log' . '.date as date',
    db_prefix() . 'module_activity_log' . '.staffid as staffid',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'module_activity_log';
$join = [];

$where = [];
array_push($where, ' AND '.db_prefix().'module_activity_log.staffid != 1');
if ($this->ci->input->get('module_name')) {
    $module_name = $this->ci->input->get('module_name');
    array_push($where, ' AND '.db_prefix().'module_activity_log.module_name = "'.$module_name.'"');
}
if ($this->ci->input->post('rel_id')) {
    $rel_id = $this->ci->input->post('rel_id');
    array_push($where, ' AND '.db_prefix().'module_activity_log.rel_id = "'.$rel_id.'"');
}

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
            $_data = date('d M, Y h:i A',strtotime($aRow['date']));
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

?>