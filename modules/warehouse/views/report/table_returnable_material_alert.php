<?php
defined('BASEPATH') or exit('No direct script access allowed');

$base_currency = get_base_currency_pur();

$select = [
    'sr.pr_order_id as order_id',
    'srd.commodity_name',
    'srd.description',
    'gdd.issued_date',
    'srd.returnable_date',
    1,
    "CASE 
        WHEN sr.approval = 0 AND sr.date_add < CURDATE() 
        THEN DATEDIFF(CURDATE(), sr.date_add) 
        ELSE 0 
     END AS days_overdue",
    'sr.delivery_status',
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'stock_reconciliation sr ON sr.id = srd.goods_delivery_id',
    'LEFT JOIN ' . db_prefix() . 'pur_orders po ON po.id = sr.pr_order_id',
    'LEFT JOIN ' . db_prefix() . 'goods_delivery gd ON gd.pr_order_id = sr.pr_order_id',
    'LEFT JOIN ' . db_prefix() . 'goods_delivery_detail gdd ON 
        gdd.goods_delivery_id = gd.id 
        AND gdd.commodity_code = srd.commodity_code 
        AND REPLACE(REPLACE(REPLACE(REPLACE(gdd.description, "\r", ""), "\n", ""), "<br />", ""), "<br/>", "") = 
            REPLACE(REPLACE(REPLACE(REPLACE(srd.description, "\r", ""), "\n", ""), "<br />", ""), "<br/>", "")'
];

$where = [];
$where[] = 'AND (sr.goods_delivery_code IS NOT NULL)';

$additionalSelect = [
    'sr.id as stock_reconciliation_id',
    'gd.id as goods_delivery_id',
    'po.pur_order_number as order_number',
];

$sIndexColumn = 'srd.id';
$sTable       = db_prefix() . 'stock_reconciliation_detail srd';

$result  = data_tables_init($select, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['order_id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
    $row[] = $aRow['commodity_name'];
    $row[] = $aRow['description'];
    $issued_date = "";
    if(!empty($aRow['issued_date'])) {
        $issued_date = json_decode($aRow['issued_date'], true);
        $issued_date = array_values(array_filter($issued_date, function($value) {
            return !empty($value);
        }));
        if(!empty($issued_date)) {
            $issued_date = implode('<br>', $issued_date);
        }
    }
    $row[] = $issued_date;
    $returnable_date = "";
    if(!empty($aRow['returnable_date'])) {
        $returnable_date = json_decode($aRow['returnable_date'], true);
        $returnable_date = array_values(array_filter($returnable_date, function($value) {
            return !empty($value);
        }));
        if(!empty($returnable_date)) {
            $returnable_date = implode('<br>', $returnable_date);
        }
    }
    $row[] = $returnable_date;
    $row[] = 'Yes';
    $row[] = $aRow['days_overdue'];
    $delivery_status = render_delivery_status_html($aRow['stock_reconciliation_id'], 'reconciliation', $aRow['delivery_status']);
    $row[] = $delivery_status;
    
    $output['aaData'][] = $row;
}

?>
