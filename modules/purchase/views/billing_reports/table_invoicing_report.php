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
    $custom_date_select = trim($custom_date_select);
    if (!startsWith($custom_date_select, 'AND')) {
        $custom_date_select = 'AND ' . $custom_date_select;
    }
    $where[] = $custom_date_select;
}

if ($this->ci->input->post('invoicing_status') && count($this->ci->input->post('invoicing_status')) > 0) {
    $statuses = array_map(function($status) {
        return "'" . trim($status) . "'";
    }, $this->ci->input->post('invoicing_status'));
    $where[] = 'AND status IN (' . implode(',', $statuses) . ')';
}

$aColumns     = $select;
$sIndexColumn = 'id';

$result = data_tables_init_for_billing_invoicing_reports($aColumns, $sIndexColumn, '', [], $where, [
    'project_id',
]);

$output  = $result['output'];
$rResult = $result['rResult'];
$base_currency = get_base_currency_pur();

$footer_data = [
    'total_amount' => 0,
    'total_paid' => 0,
];
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

    $footer_data['total_amount'] += $aRow['total_billed'];
    $footer_data['total_paid'] += $aRow['total_paid'];

    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, '₹');
}

$output['sums'] = $footer_data;

?>