<?php
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'pur_order_number',
    'pur_order_name',
    'vendor',
    'DATE_ADD(order_date, INTERVAL lead_time_days DAY) as est_delivery_date',
    'gr.delivery_date as delivery_date',
];

if(isset($po_critical_deliver)) {
    $aColumns[] = 'DATEDIFF(gr.delivery_date, 
        DATE_ADD(' . db_prefix() . 'pur_orders.order_date, 
        INTERVAL ' . db_prefix() . 'pur_orders.lead_time_days DAY)
    ) as delivery_delay_days';
}

$sIndexColumn = 'id';
$sTable       = db_prefix().'pur_orders';
$join         = [
    'LEFT JOIN '.db_prefix().'pur_vendor ON '.db_prefix().'pur_vendor.userid = '.db_prefix().'pur_orders.vendor',
    'LEFT JOIN '.db_prefix().'projects ON '.db_prefix().'projects.id = '.db_prefix().'pur_orders.project',
    'LEFT JOIN (SELECT pr_order_id, MAX(date_add) as delivery_date FROM ' . db_prefix() . 'goods_receipt WHERE pr_order_id IS NOT NULL GROUP BY pr_order_id) as gr ON gr.pr_order_id = ' . db_prefix() . 'pur_orders.id',
];

$where = [];

if ($this->ci->input->post('vendor')
    && $this->ci->input->post('vendor') != '') {
    array_push($where, 'AND vendor = "'.$this->ci->input->post('vendor').'"');
}

if ($this->ci->input->post('project')
    && $this->ci->input->post('project') != '') {
    array_push($where, 'AND project = "'.$this->ci->input->post('project').'"');
}

if ($this->ci->input->post('group_pur')
    && $this->ci->input->post('group_pur') != '') {
    array_push($where, 'AND group_pur = "'.$this->ci->input->post('group_pur').'"');
}

if ($this->ci->input->post('kind')
    && $this->ci->input->post('kind') != '') {
    array_push($where, 'AND kind = "'.$this->ci->input->post('kind').'"');
}

if ($this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != '') {
    array_push($where, 'AND order_date >= "'.date('Y-m-d', strtotime($this->ci->input->post('from_date'))).'"');
}

if ($this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != '') {
    array_push($where, 'AND order_date <= "'.date('Y-m-d', strtotime($this->ci->input->post('to_date'))).'"');
}

array_push($where, 'AND gr.delivery_date IS NOT NULL');
if(isset($po_upcoming_deliver)) {
    array_push($where, 'AND (
    DATE_ADD(' . db_prefix() . 'pur_orders.order_date, 
        INTERVAL ' . db_prefix() . 'pur_orders.lead_time_days DAY) >= CURDATE()
        OR
        gr.delivery_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
    )');
}

$having = '';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, 
    [db_prefix().'pur_orders.id as id', 'company'], '', [], $having);

$output  = $result['output'];
$rResult = $result['rResult'];

$aColumns = array_map(function ($col) {
    $col = trim($col);
    if (stripos($col, ' as ') !== false) {
        $parts = preg_split('/\s+as\s+/i', $col);
        return trim(end($parts), '"` ');
    }
    return trim($col, '"` ');
}, $aColumns);

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        if($aColumns[$i] == 'pur_order_number') {
            $numberOutput = '';
            $numberOutput .= '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" target="_blank">'.$aRow['pur_order_number']. '</a>';
            $_data = $numberOutput;
        } elseif($aColumns[$i] == 'pur_order_name'){
            $_data = $aRow['pur_order_name'];
        } elseif($aColumns[$i] == 'vendor'){
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" target="_blank">' .  $aRow['company'] . '</a>';
        } elseif($aColumns[$i] == 'est_delivery_date'){
            $_data = _d($aRow['est_delivery_date']);
        } elseif($aColumns[$i] == 'delivery_date'){
            $_data = _d($aRow['delivery_date']);
        } elseif($aColumns[$i] == 'delivery_delay_days'){
            $delay = (int)$aRow['delivery_delay_days'];
            if ($delay > 15) {
                $_data = '<span class="label label-danger">Critical</span>';
            } elseif ($delay >= 0) {
                $_data = '<span class="label label-success">Not Critical</span>';
            } else {
                $_data = '<span class="label label-default">Early</span>';
            }
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}

?>