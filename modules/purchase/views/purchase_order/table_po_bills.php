<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('purchase/purchase_model');
$base_currency = get_base_currency_pur();

$select = [
    'id',
    'bill_number',
    'total',
    'invoice_date',
    'approve_status',
    '(CASE 
        WHEN pc_id IS NOT NULL THEN 2 
        WHEN approve_status = 2 AND pc_id IS NULL THEN 1 
        ELSE 3 
     END) as applied_to_payment_certificate',
    1,
];

$join = [];

$where = [];
$po_id = isset($po_id) ? $po_id : null;
if ($po_id) {
    $where[] = 'AND pb.pur_order = ' . $po_id;
}

$additionalSelect = [];

$sIndexColumn = 'pb.id';
$sTable       = db_prefix() . 'pur_bills pb';

$result  = data_tables_init($select, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $key => $aRow) {
    $row = [];

    $row[] = $key + 1;
    $row[] = $aRow['bill_number'];
    $row[] = app_format_money($aRow['total'], $base_currency->symbol);
    $row[] = date('d-M-Y', strtotime($aRow['invoice_date']));
    $approve_status = '';
    if ($aRow['approve_status'] == 2) {
        $approve_status = '<span class="label label-success">' . _l('approved') . '</span>';
    } else if ($aRow['approve_status'] == 3) {
        $approve_status = '<span class="label label-danger">' . _l('rejected') . '</span>';
    } else {
        $list_approval_details = $this->ci->purchase_model->get_list_pur_bills_approval_details($aRow['id']);
        if (empty($list_approval_details)) {
            $approve_status = '<a data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_bill_bifurcation_approve(' . pur_html_entity_decode($aRow['id']) . '); return false;">' . _l('approval_request_sent') . '</a>';
        } else {
            $approve_status = '<span class="label label-primary">' . _l('approval_request_sent') . '</span>';
        }
    }
    $row[] = $approve_status;
    $applied_to_payment_certificate = '';
    if ($aRow['applied_to_payment_certificate'] == 1) {
        // $applied_to_payment_certificate = '<a href="' . admin_url('purchase/payment_certificate/' . $po_id . '?bill_id=' . $aRow['id']) . '" class="btn btn-info" target="_blank">' . _l('convert_to_payment_certificate') . '</a>';
        $applied_to_payment_certificate = '';
    } else if($aRow['applied_to_payment_certificate'] == 2) {
        $applied_to_payment_certificate = '<span class="btn btn-success">Converted</span>';
    } else if($aRow['applied_to_payment_certificate'] == 3) {
        $applied_to_payment_certificate = '<span class="btn btn-warning">Pending</span>';
    } else {
        $applied_to_payment_certificate = '';
    }
    $row[] = $applied_to_payment_certificate;
    $actions = '';
    if (has_permission('bill_bifurcation', '', 'edit') || is_admin()) {
        $actions .= '<a href="' . admin_url('purchase/edit_pur_bills/' . $aRow['id']) . '" 
            target="_blank" 
            class="btn btn-default btn-icon" 
            data-toggle="tooltip" 
            data-placement="top" 
            title="' . _l('edit') . '">
            <i class="fa fa-pencil-square"></i></a> ';
    }
    if (has_permission('bill_bifurcation', '', 'delete') || is_admin()) {
        $actions .= '<a href="' . admin_url('purchase/delete_bill/' . $aRow['id']) . '/'.$po_id.'" 
            class="btn btn-danger btn-icon _delete" 
            data-toggle="tooltip" 
            data-placement="top" 
            title="' . _l('delete') . '">
            <i class="fa fa-remove"></i></a>';
    }
    $row[] = $actions;

    $output['aaData'][] = $row;
}

?>