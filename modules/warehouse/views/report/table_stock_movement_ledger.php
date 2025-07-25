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
    'grd.commodity_name',
    'grd.description',
    'CAST(grd.quantities AS DECIMAL(10,2))',
    'CAST(grd.quantities AS DECIMAL(10,2))',
    'CASE 
        WHEN gdd.quantities IS NOT NULL THEN CAST(gdd.quantities AS DECIMAL(10,2)) 
        ELSE 0 
    END',
    1,
    'grd.quantities - 
    CASE 
        WHEN gdd.quantities IS NOT NULL THEN gdd.quantities 
        ELSE 0 
    END',
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'goods_receipt gr ON gr.id = grd.goods_receipt_id',
    'LEFT JOIN ' . db_prefix() . 'pur_orders po ON po.id = gr.pr_order_id',
    'LEFT JOIN ' . db_prefix() . 'wo_orders wo ON wo.id = gr.wo_order_id',
    'LEFT JOIN ' . db_prefix() . 'goods_delivery_detail gdd 
        ON gdd.commodity_code = grd.commodity_code 
        AND REPLACE(REPLACE(REPLACE(REPLACE(
            gdd.description, "\r", ""), "\n", ""), "<br />", ""), "<br/>", "") =
            REPLACE(REPLACE(REPLACE(REPLACE(
            grd.description, "\r", ""), "\n", ""), "<br />", ""), "<br/>", "")'
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
    'CAST(grd.quantities AS DECIMAL(10,2)) AS opening_qty',
    'CAST(grd.quantities AS DECIMAL(10,2)) AS inward',
    'CASE 
        WHEN gdd.quantities IS NOT NULL THEN CAST(gdd.quantities AS DECIMAL(10,2)) 
        ELSE 0 
    END AS outward',
    'grd.quantities - 
    CASE 
        WHEN gdd.quantities IS NOT NULL THEN gdd.quantities 
        ELSE 0 
    END AS closing_qty',
];

$sIndexColumn = 'grd.id';
$sTable       = db_prefix() . 'goods_receipt_detail grd';

$result  = data_tables_init($select, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    if($aRow['source_table'] == "po") {
        $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['order_id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
    } else {
        $row[] = '<a href="' . admin_url('purchase/work_order/' . $aRow['order_id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
    }
    $row[] = $aRow['commodity_name'];
    $row[] = $aRow['description'];
    $row[] = $aRow['opening_qty'];
    $row[] = $aRow['inward'];
    $row[] = $aRow['outward'];
    $row[] = 0;
    $row[] = number_format($aRow['closing_qty'], 2, '.', '');

    $output['aaData'][] = $row;
}

?>
