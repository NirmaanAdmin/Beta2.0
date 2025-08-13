<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('purchase/purchase_model');
$base_currency = get_base_currency_pur();

$select = [
    'pi.vendor_invoice_number as invoice_no',
    'pv.company as vendor_name',
    'inv.title as invoice_title',
    'pi.final_certified_amount as invoice_amount',
    'pi.payment_status as billing_status'
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'pur_vendor pv ON pv.userid = pi.vendor',
    'LEFT JOIN ' . db_prefix() . 'itemable it ON it.vbt_id = pi.id AND it.rel_type = "invoice"',
    'LEFT JOIN ' . db_prefix() . 'invoices inv ON inv.id = it.rel_id',
];

$where = [];
$custom_date_select = $this->ci->purchase_model->get_where_report_period('invoice_date');
if ($custom_date_select != '') {
    $custom_date_select = trim($custom_date_select);
    if (!startsWith($custom_date_select, 'AND')) {
        $custom_date_select = 'AND ' . $custom_date_select;
    }
    $where[] = $custom_date_select;
}
$where[] = 'AND (inv.id IS NOT NULL)';

if ($this->ci->input->post('mapping_project')) {
    array_push($where, 'AND pi.project_id = '.$this->ci->input->post('mapping_project').'');
}

if ($this->ci->input->post('mapping_vendor') && count($this->ci->input->post('mapping_vendor')) > 0
) {
    array_push($where, 'AND pv.userid IN (' . implode(',', $this->ci->input->post('mapping_vendor')) . ')');
}

if ($this->ci->input->post('mapping_status') && count($this->ci->input->post('mapping_status')) > 0) {
    array_push($where, 'AND pi.payment_status IN (' . implode(',', $this->ci->input->post('mapping_status')) . ')');
}

$additionalSelect = [
    'pv.userid as vendor_id',
    'inv.id as client_invoice_id'
];

$sIndexColumn = 'pi.id';
$sTable       = db_prefix() . 'pur_invoices pi';

$result  = data_tables_init($select, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

$footer_data = [
    'total_vendor_amount' => 0,
];
foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['invoice_no'];
    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';
    $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['client_invoice_id']) . '" target="_blank">' . $aRow['invoice_title'] . '</a>';
    $row[] = app_format_money($aRow['invoice_amount'], $base_currency->symbol);

    switch ($aRow['billing_status']) {
        case 5:
            $payment_status = '<span class="inline-block label label-success">' . _l('bill_verified_by_ril');
            break;
        case 7:
            $payment_status = '<span class="inline-block label label-success">' . _l('payment_processed');
            break;
        default:
            $payment_status = '<span class="inline-block label label-success">' . _l('Paid');
    }
    $row[] = $payment_status;

    $footer_data['total_vendor_amount'] += $aRow['invoice_amount'];

    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, '₹');
}

$output['sums'] = $footer_data;

?>