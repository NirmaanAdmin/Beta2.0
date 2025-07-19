<?php
defined('BASEPATH') or exit('No direct script access allowed');

$select = [
    'vendor_name',
    'total_billed',
    'total_paid',
    'total_balance',
    'paid_percentage',
];
$where = [];
$this->ci->load->model('purchase/purchase_model');
$custom_date_select = $this->ci->purchase_model->get_where_report_period('last_bill_date');
if ($custom_date_select != '') {
    array_push($where, $custom_date_select);
}

$aColumns     = $select;
$sIndexColumn = 'id';

$result = data_tables_init_for_billing_summary_reports($aColumns, $sIndexColumn, '', [], $where, [
	'vendor_id',
]);

$output  = $result['output'];
$rResult = $result['rResult'];
$base_currency = get_base_currency_pur();

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';
    $row[] = app_format_money($aRow['total_billed'], $base_currency->symbol);
    $row[] = app_format_money($aRow['total_paid'], $base_currency->symbol);
    $row[] = app_format_money($aRow['total_balance'], $base_currency->symbol);
    $row[] = round($aRow['paid_percentage']).'%';

    $output['aaData'][] = $row;
}

?>