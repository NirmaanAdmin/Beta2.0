<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('purchase/purchase_model');
$base_currency = get_base_currency_pur();

$select = [
    'pv.company as vendor_name',
    'pi.vendor_invoice_number as invoice_no',
    'pi.invoice_date as invoice_date',
    'pi.final_certified_amount as invoice_amount',
    'DATEDIFF(CURRENT_DATE, pi.invoice_date) as days_since_invoice',
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
$where[] = 'AND (inv.id IS NULL OR pi.payment_status IN (0, 2, 3, 4))';

if ($this->ci->input->post('aging_project')) {
    array_push($where, 'AND pi.project_id = '.$this->ci->input->post('aging_project').'');
}

if ($this->ci->input->post('aging_vendor') && count($this->ci->input->post('aging_vendor')) > 0
) {
    array_push($where, 'AND pv.userid IN (' . implode(',', $this->ci->input->post('aging_vendor')) . ')');
}

if ($this->ci->input->post('aging_status') && count($this->ci->input->post('aging_status')) > 0) {
    array_push($where, 'AND pi.payment_status IN (' . implode(',', $this->ci->input->post('aging_status')) . ')');
}

$additionalSelect = [
    'pv.userid as vendor_id'
];

$sIndexColumn = 'pi.id';
$sTable       = db_prefix() . 'pur_invoices pi';

$result  = data_tables_init($select, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

$footer_data = [
    'total_amount' => 0,
];
foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';
    $row[] = $aRow['invoice_no'];
    $row[] = date('d-M-Y', strtotime($aRow['invoice_date']));
    $row[] = app_format_money($aRow['invoice_amount'], $base_currency->symbol);
    $row[] = $aRow['days_since_invoice'];

    switch ($aRow['billing_status']) {
        case 0:
            $payment_status = '<span class="inline-block label label-danger">' . _l('unpaid');
            break;
        case 2:
            $payment_status = '<span class="inline-block label label-info">' . _l('recevied_with_comments');
            break;
        case 3:
            $payment_status = '<span class="inline-block label label-warning">' . _l('bill_verification_in_process');
            break;
        case 4:
            $payment_status = '<span class="inline-block label label-primary">' . _l('bill_verification_on_hold');
            break;
        default:
            $payment_status = '<span class="inline-block label label-danger">' . _l('Pending');
    }

    $row[] = $payment_status;

    $footer_data['total_amount'] += $aRow['invoice_amount'];

    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, '₹');
}

$output['sums'] = $footer_data;

?>