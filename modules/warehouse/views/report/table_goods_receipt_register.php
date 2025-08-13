<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('purchase/purchase_model');
$base_currency = get_base_currency_pur();

$select = [
    'CASE 
        WHEN gr.pr_order_id IS NOT NULL THEN gr.pr_order_id 
        WHEN gr.wo_order_id IS NOT NULL THEN gr.wo_order_id 
        ELSE NULL 
     END as order_id',
    'gr.goods_receipt_code',
    'grd.commodity_name',
    'grd.description',
    'supplier_code as vendor_id',
    'CAST(grd.quantities AS DECIMAL(10,2))',
    'gr.date_add',
    1,
    2,
    3, 
    4, 
    'gr.approval',
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'goods_receipt gr ON gr.id = grd.goods_receipt_id',
    'LEFT JOIN ' . db_prefix() . 'pur_orders po ON po.id = gr.pr_order_id',
    'LEFT JOIN ' . db_prefix() . 'wo_orders wo ON wo.id = gr.wo_order_id',
];

$where = [];
$custom_date_select = $this->ci->purchase_model->get_where_report_period('gr.date_add');
if ($custom_date_select != '') {
    $custom_date_select = trim($custom_date_select);
    if (!startsWith($custom_date_select, 'AND')) {
        $custom_date_select = 'AND ' . $custom_date_select;
    }
    $where[] = $custom_date_select;
}
$where[] = 'AND (gr.goods_receipt_code IS NOT NULL)';

if ($this->ci->input->post('vendors') && count($this->ci->input->post('vendors')) > 0
) {
    array_push($where, 'AND gr.supplier_code IN (' . implode(',', $this->ci->input->post('vendors')) . ')');
}

if ($this->ci->input->post('status') && count($this->ci->input->post('status')) > 0) {
    $status_conditions = [];
    foreach ($this->ci->input->post('status') as $status) {
        switch ($status) {
            case 'approved':
                $status_conditions[] = 'gr.approval = 1';
                break;
            case 'not_yet_approve':
                $status_conditions[] = 'gr.approval = 0';
                break;
            case 'reject':
                $status_conditions[] = 'gr.approval = -1';
                break;
        }
    }
    if (!empty($status_conditions)) {
        $where[] = 'AND (' . implode(' OR ', $status_conditions) . ')';
    }
}

$additionalSelect = [
    'gr.id as goods_receipt_id',
    'CASE 
        WHEN gr.pr_order_id IS NOT NULL THEN pur_order_number
        WHEN gr.wo_order_id IS NOT NULL THEN wo_order_number
        ELSE NULL 
     END as order_number',
    'CASE 
        WHEN gr.pr_order_id IS NOT NULL THEN "po" 
        WHEN gr.wo_order_id IS NOT NULL THEN "wo"
        ELSE NULL 
     END as source_table',
     'CAST(grd.quantities AS DECIMAL(10,2)) AS quantities',
];

$sIndexColumn = 'grd.id';
$sTable       = db_prefix() . 'goods_receipt_detail grd';

$result  = data_tables_init($select, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

$footer_data = [
    'total_quantity' => 0,
];
foreach ($rResult as $aRow) {
    $row = [];

    if($aRow['source_table'] == "po") {
        $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['order_id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
    } else {
        $row[] = '<a href="' . admin_url('purchase/work_order/' . $aRow['order_id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
    }
    $row[] = '<a href="' . admin_url('warehouse/manage_goods_receipt/' . $aRow['goods_receipt_id']) . '" target="_blank">'.$aRow['goods_receipt_code']. '</a>';
    $row[] = $aRow['commodity_name'];
    $row[] = $aRow['description'];
    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' . wh_get_vendor_company_name($aRow['vendor_id']) . '</a>';
    $row[] = $aRow['quantities'];
    $row[] = _d($aRow['date_add']);
    $row[] = "";
    $row[] = "";
    $row[] = "";
    $row[] = "";
    $approval = "";
    if ($aRow['approval'] == 1) {
        $approval = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">' . _l('approved') . '</span><span class="hide">, </span></span>&nbsp';
    } elseif ($aRow['approval'] == 0) {
        $approval = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">' . _l('not_yet_approve') . '</span><span class="hide">, </span></span>&nbsp';
    } elseif ($aRow['approval'] == -1) {
        $approval = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">' . _l('reject') . '</span><span class="hide">, </span></span>&nbsp';
    } else {
        $approval = "";
    }
    $row[] = $approval;

    $footer_data['total_quantity'] += $aRow['quantities'];

    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total, 2, '.', '');
}

$output['sums'] = $footer_data;

?>
