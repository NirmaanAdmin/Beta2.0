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
    1,
];

$join = [];

$where = [];
$rel_type = '';
if ($this->ci->input->post('po_id')) {
    $where[] = 'AND pb.pur_order = ' . $this->ci->input->post('po_id');
    $rel_type = "po_bill_bifurcation";
}
if ($this->ci->input->post('wo_id')) {
    $where[] = 'AND pb.wo_order = ' . $this->ci->input->post('wo_id');
    $rel_type = "wo_bill_bifurcation";
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
            $approve_status = '<a data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_bill_bifurcation_approve(' . pur_html_entity_decode($aRow['id']) . ', \'' . $rel_type . '\'); return false;">' . _l('approval_request_sent') . '</a>';
        } else {
            $approve_status = '<span class="label label-primary">' . _l('approval_request_sent') . '</span>';
        }
    }
    $row[] = $approve_status;
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
        $actions .= '<a href="' . admin_url('purchase/delete_bill/' . $aRow['id']) . '" 
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