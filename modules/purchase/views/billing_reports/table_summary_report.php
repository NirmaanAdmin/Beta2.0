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
    $custom_date_select = trim($custom_date_select);
    if (!startsWith($custom_date_select, 'AND')) {
        $custom_date_select = 'AND ' . $custom_date_select;
    }
    $where[] = $custom_date_select;
}

if ($this->ci->input->post('summary_vendor') && count($this->ci->input->post('summary_vendor')) > 0
) {
    array_push($where, 'AND vendor_id IN (' . implode(',', $this->ci->input->post('summary_vendor')) . ')');
}

if ($this->ci->input->post('summary_status') && count($this->ci->input->post('summary_status')) > 0) {
    $status_conditions = [];
    foreach ($this->ci->input->post('summary_status') as $status) {
        switch ($status) {
            case '1':
                $status_conditions[] = 'paid_percentage = 0';
                break;
            case '2':
                $status_conditions[] = 'paid_percentage > 0 AND paid_percentage < 100';
                break;
            case '3':
                $status_conditions[] = 'paid_percentage = 100';
                break;
        }
    }
    if (!empty($status_conditions)) {
        $where[] = 'AND (' . implode(' OR ', $status_conditions) . ')';
    }
}

$aColumns     = $select;
$sIndexColumn = 'id';

$result = data_tables_init_for_billing_summary_reports($aColumns, $sIndexColumn, '', [], $where, [
	'vendor_id',
]);

$output  = $result['output'];
$rResult = $result['rResult'];
$base_currency = get_base_currency_pur();

$footer_data = [
    'total_billed' => 0,
    'total_paid' => 0,
    'total_balance' => 0,
];
foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';
    $row[] = app_format_money($aRow['total_billed'], $base_currency->symbol);
    $row[] = app_format_money($aRow['total_paid'], $base_currency->symbol);
    $row[] = app_format_money($aRow['total_balance'], $base_currency->symbol);
    $row[] = round($aRow['paid_percentage']).'%';

    $footer_data['total_billed'] += $aRow['total_billed'];
    $footer_data['total_paid'] += $aRow['total_paid'];
    $footer_data['total_balance'] += $aRow['total_balance'];

    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, '₹');
}

$output['sums'] = $footer_data;

?>