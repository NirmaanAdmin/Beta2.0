<?php
defined('BASEPATH') or exit('No direct script access allowed');

$select = [
    'vendor_name',
    'invoice_no',
    'invoice_date',
    'invoice_amount',
    'days_since_invoice',
    'billing_status',
];
$where = [];
$this->ci->load->model('purchase/purchase_model');
$custom_date_select = $this->ci->purchase_model->get_where_report_period('invoice_date');
if ($custom_date_select != '') {
    array_push($where, $custom_date_select);
}

$aColumns     = $select;
$sIndexColumn = 'id';

$result = data_tables_init_for_billing_aging_reports($aColumns, $sIndexColumn, '', [], $where, [
	'vendor_id',
]);

$output  = $result['output'];
$rResult = $result['rResult'];
$base_currency = get_base_currency_pur();

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';
    $row[] = $aRow['invoice_no'];
    $row[] = date('d-M-Y', strtotime($aRow['invoice_date']));
    $row[] = app_format_money($aRow['invoice_amount'], $base_currency->symbol);
    $row[] = $aRow['days_since_invoice'];
    $payment_status = '';
    if($aRow['payment_status'] == 0) {
        $payment_status = _l('unpaid');
    } else if ($aRow['payment_status'] == 2) {
        $payment_status = _l('recevied_with_comments');
    } else if ($aRow['payment_status'] == 3) {
        $payment_status = _l('bill_verification_in_process');
    } else if ($aRow['payment_status'] == 4) {
        $payment_status = _l('bill_verification_on_hold');
    } else {
        $payment_status = _l('Pending');
    }
    $row[] = $payment_status;

    $output['aaData'][] = $row;
}

?>