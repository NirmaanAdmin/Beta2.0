<?php
defined('BASEPATH') or exit('No direct script access allowed');

$select = [
    'project_name',
    'total_billed',
    'total_paid',
    'status'
];
$where = [];
$this->ci->load->model('purchase/purchase_model');
$custom_date_select = $this->ci->purchase_model->get_where_report_period('last_bill_date');
if ($custom_date_select != '') {
    array_push($where, $custom_date_select);
}

$aColumns     = $select;
$sIndexColumn = 'id';

$result = data_tables_init_for_billing_invoicing_reports($aColumns, $sIndexColumn, '', [], $where, [
    'project_id',
]);

$output  = $result['output'];
$rResult = $result['rResult'];
$base_currency = get_base_currency_pur();

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '" target="_blank">' . $aRow['project_name'] . '</a>';
    $row[] = app_format_money($aRow['total_billed'], $base_currency->symbol);
    $row[] = app_format_money($aRow['total_paid'], $base_currency->symbol);

    switch ($aRow['status']) {
        case 'Unpaid':
            $payment_status = '<span class="inline-block label label-danger">' . _l('Unpaid');
            break;
        case 'Partial':
            $payment_status = '<span class="inline-block label label-warning">' . _l('Partial');
            break;
        case 'Paid':
            $payment_status = '<span class="inline-block label label-success">' . _l('Paid');
            break;
        case 4:
            $payment_status = '<span class="inline-block label label-primary">' . _l('bill_verification_on_hold');
            break;
        default:
            $payment_status = '<span class="inline-block label label-danger">' . _l('Unpaid');
    }

    $row[] = $payment_status;

    $output['aaData'][] = $row;
}

?>