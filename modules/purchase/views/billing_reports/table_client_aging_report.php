<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('purchase/purchase_model');
$base_currency = get_base_currency_pur();

$select = [
    'inv.id as invoice_id',
    'pr.name as project_name',
    'inv.date as invoice_date',
    '(
        ROUND(
            inv.total
            - IFNULL((SELECT SUM(p.amount) FROM ' . db_prefix() . 'invoicepaymentrecords p WHERE p.invoiceid = inv.id), 0)
            - IFNULL((SELECT SUM(c.amount) FROM ' . db_prefix() . 'credits c WHERE c.invoice_id = inv.id), 0),
        2)
    ) AS total_left_to_pay',
    'DATEDIFF(CURDATE(), inv.date) AS days_outstanding',
    'inv.status'
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'projects pr ON pr.id = inv.project_id'
];

$where = [];
$custom_date_select = $this->ci->purchase_model->get_where_report_period('inv.date');
if ($custom_date_select != '') {
    $custom_date_select = trim($custom_date_select);
    if (!startsWith($custom_date_select, 'AND')) {
        $custom_date_select = 'AND ' . $custom_date_select;
    }
    $where[] = $custom_date_select;
}
$where[] = 'AND inv.project_id = '.get_default_project().'';

if ($this->ci->input->post('client_aging_project')) {
    array_push($where, 'AND inv.project_id = '.$this->ci->input->post('client_aging_project').'');
}

if ($this->ci->input->post('client_aging_status') && count($this->ci->input->post('client_aging_status')) > 0) {
    array_push($where, 'AND inv.status IN (' . implode(',', $this->ci->input->post('client_aging_status')) . ')');
}

$additionalSelect = [
    'pr.id as project_id',
];

$sIndexColumn = 'inv.id';
$sTable       = db_prefix() . 'invoices inv';

$result  = data_tables_init($select, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

$footer_data = [
    'total_amount_due' => 0,
];
foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoice_id']) . '" target="_blank">' . e(format_invoice_number($aRow['invoice_id'])) . '</a>';
    $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '" target="_blank">' . $aRow['project_name'] . '</a>';
    $row[] = date('d-M-Y', strtotime($aRow['invoice_date']));
    $row[] = app_format_money($aRow['total_left_to_pay'], $base_currency->symbol);
    $row[] = $aRow['days_outstanding'];
    $row[] = format_invoice_status($aRow['status']);

    $footer_data['total_amount_due'] += $aRow['total_left_to_pay'];

    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, '₹');
}

$output['sums'] = $footer_data;

?>
