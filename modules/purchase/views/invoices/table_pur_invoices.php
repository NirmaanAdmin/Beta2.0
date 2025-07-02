<?php

defined('BASEPATH') or exit('No direct script access allowed');

$custom_fields = get_custom_fields('pur_invoice', [
    'show_on_table' => 1,
]);

$module_name = 'vendor_billing_tracker';
$from_date_filter_name = 'from_date';
$to_date_filter_name = 'to_date';
$vendors_filter_name = 'vendors';
$billing_invoices_filter_name = 'billing_invoices';
$budget_head_filter_name = 'budget_head';
$billing_status_filter_name = 'billing_status';


$aColumns = [
    0,
    1,
    'invoice_number',
    'vendor_invoice_number',
    db_prefix() . 'pur_vendor.company',
    'invoice_date',
    db_prefix() . 'items_groups.name',
    db_prefix() . 'pur_invoices.pur_order',
    'expense_convert',
    'vendor_submitted_amount_without_tax',
    'vendor_submitted_tax_amount',
    'final_certified_amount',
    'payment_status',
    3,
    4,
    'vendor_note',
    db_prefix() . 'pur_invoices.id as inv_id',
    'adminnote',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'pur_invoices';
$join         = [
    'LEFT JOIN ' . db_prefix() . 'pur_contracts ON ' . db_prefix() . 'pur_contracts.id = ' . db_prefix() . 'pur_invoices.contract',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'pur_invoices.project_id = ' . db_prefix() . 'projects.id',
    'LEFT JOIN ' . db_prefix() . 'items_groups ON ' . db_prefix() . 'pur_invoices.group_pur = ' . db_prefix() . 'items_groups.id',
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_invoices.vendor',
];

$i = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_' . $i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_' . $i;
    }
    array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $i . ' ON ' . db_prefix() . 'pur_invoices.id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}


$where = [];


if (
    $this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != ''
) {
    array_push($where, 'AND invoice_date >= "' . to_sql_date($this->ci->input->post('from_date')) . '"');
}

if (isset($vendor)) {
    array_push($where, ' AND ' . db_prefix() . 'pur_invoices.vendor = ' . $vendor);
}


if (
    $this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != ''
) {
    array_push($where, 'AND invoice_date <= "' . to_sql_date($this->ci->input->post('to_date')) . '"');
}

if (!has_permission('purchase_invoices', '', 'view')) {
    array_push($where, 'AND (' . db_prefix() . 'pur_invoices.add_from = ' . get_staff_user_id() . ' OR ' . db_prefix() . 'pur_invoices.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
}

$contract = $this->ci->input->post('contract');
if (isset($contract)) {
    $where_contract = '';
    foreach ($contract as $t) {
        if ($t != '') {
            if ($where_contract == '') {
                $where_contract .= ' AND (' . db_prefix() . 'pur_invoices.contract = "' . $t . '"';
            } else {
                $where_contract .= ' or ' . db_prefix() . 'pur_invoices.contract = "' . $t . '"';
            }
        }
    }
    if ($where_contract != '') {
        $where_contract .= ')';
        array_push($where, $where_contract);
    }
}

$pur_orders = $this->ci->input->post('pur_orders');
if (isset($pur_orders)) {
    $where_pur_orders = '';
    foreach ($pur_orders as $t) {
        if ($t != '') {
            if ($where_pur_orders == '') {
                $where_pur_orders .= ' AND (' . db_prefix() . 'pur_invoices.pur_order = "' . $t . '"';
            } else {
                $where_pur_orders .= ' or ' . db_prefix() . 'pur_invoices.pur_order = "' . $t . '"';
            }
        }
    }
    if ($where_pur_orders != '') {
        $where_pur_orders .= ')';
        array_push($where, $where_pur_orders);
    }
}
$wo_orders = $this->ci->input->post('wo_orders');
if (isset($wo_orders)) {
    $where_wo_orders = '';
    foreach ($wo_orders as $t) {
        if ($t != '') {
            if ($where_wo_orders == '') {
                $where_wo_orders .= ' AND (' . db_prefix() . 'pur_invoices.wo_order = "' . $t . '"';
            } else {
                $where_wo_orders .= ' or ' . db_prefix() . 'pur_invoices.wo_order = "' . $t . '"';
            }
        }
    }
    if ($where_wo_orders != '') {
        $where_wo_orders .= ')';
        array_push($where, $where_wo_orders);
    }
}

$vendors = $this->ci->input->post('vendors');
if (isset($vendors)) {
    $where_vendors = '';
    foreach ($vendors as $t) {
        if ($t != '') {
            if ($where_vendors == '') {
                $where_vendors .= ' AND (' . db_prefix() . 'pur_invoices.vendor = "' . $t . '"';
            } else {
                $where_vendors .= ' or ' . db_prefix() . 'pur_invoices.vendor = "' . $t . '"';
            }
        }
    }
    if ($where_vendors != '') {
        $where_vendors .= ')';
        array_push($where, $where_vendors);
    }
}

$billing_invoices = $this->ci->input->post('billing_invoices');
if (isset($billing_invoices) && !empty($billing_invoices)) {
    $where_billing_invoices = '';
    if ($billing_invoices == "None") {
        $where_billing_invoices .= ' AND (' . db_prefix() . 'pur_invoices.expense_convert = 0';
    } else {
        $billing_invoice_array = get_expenses_data_by_pur_invoices($billing_invoices);
        if (!empty($billing_invoice_array)) {
            $billing_invoice_array = explode(",", $billing_invoice_array->vbt_ids);
            $where_billing_invoices .= ' AND (' . db_prefix() . 'pur_invoices.id IN (' . implode(',', $billing_invoice_array) . ')';
        }
    }

    if ($where_billing_invoices != '') {
        $where_billing_invoices .= ')';
        array_push($where, $where_billing_invoices);
    }
}
$budget_head = $this->ci->input->post('budget_head');
if (isset($budget_head)) {

    $where_budget_head = '';


    if ($budget_head != '') {
        if ($where_budget_head == '') {
            $where_budget_head .= ' AND (' . db_prefix() . 'pur_invoices.group_pur = "' . $budget_head . '"';
        } else {
            $where_budget_head .= ' or ' . db_prefix() . 'pur_invoices.group_pur = "' . $budget_head . '"';
        }
    }
    if ($where_budget_head != '') {
        $where_budget_head .= ')';
        array_push($where, $where_budget_head);
    }
}
$billing_status = $this->ci->input->post('billing_status');
if (isset($billing_status)) {

    $where_billing_status = '';
    if ($billing_status == 8) {
        $billing_status = 0;
    }

    if ($billing_status != '') {
        if ($where_billing_status == '') {
            $where_billing_status .= ' AND (' . db_prefix() . 'pur_invoices.payment_status = "' . $billing_status . '"';
        } else {
            $where_billing_status .= ' or ' . db_prefix() . 'pur_invoices.payment_status = "' . $billing_status . '"';
        }
    }
    if ($where_billing_status != '') {
        $where_billing_status .= ')';
        array_push($where, $where_billing_status);
    }
}

if(get_default_project()) {
    array_push($where, 'AND ' . db_prefix() . 'pur_invoices.project_id = '.get_default_project().'');
}

$from_date_filter_value = !empty($this->ci->input->post('from_date')) ? $this->ci->input->post('from_date') : NULL;
update_module_filter($module_name, $from_date_filter_name, $from_date_filter_value);

$to_date_filter_value = !empty($this->ci->input->post('to_date')) ? $this->ci->input->post('to_date') : NULL;
update_module_filter($module_name, $to_date_filter_name, $to_date_filter_value);

$vendors_filter_value = !empty($this->ci->input->post('vendors')) ? implode(',', $this->ci->input->post('vendors')) : NULL;
update_module_filter($module_name, $vendors_filter_name, $vendors_filter_value);

$billing_invoices_filter_value = !empty($this->ci->input->post('billing_invoices')) ? $this->ci->input->post('billing_invoices') : NULL;
update_module_filter($module_name, $billing_invoices_filter_name, $billing_invoices_filter_value);

$budget_head_filter_name_value = !empty($this->ci->input->post('budget_head')) ? $this->ci->input->post('budget_head') : NULL;
update_module_filter($module_name, $budget_head_filter_name, $budget_head_filter_name_value);

$billing_status_filter_name_value = !empty($this->ci->input->post('billing_status')) ? $this->ci->input->post('billing_status') : NULL;
update_module_filter($module_name, $billing_status_filter_name, $billing_status_filter_name_value);




$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'pur_invoices.id as id',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'pur_invoices.id and rel_type="pur_invoice" ORDER by tag_order ASC) as tags',
    'contract_number',
    'invoice_number',
    'currency',
    'expense_convert',
    db_prefix() . 'pur_invoices.wo_order',
    db_prefix() . 'items_groups.name',
    db_prefix() . 'pur_invoices.description_services',
    db_prefix() . 'pur_invoices.vendor as vendor_id',
    db_prefix() . 'pur_invoices.pur_order',
    db_prefix() . 'pur_invoices.order_tracker_id',
], '', [], '', 'vendor_billing_tracker');

$output  = $result['output'];
$rResult = $result['rResult'];

$footer_data = [
    'total_vendor_submitted_amount_without_tax' => 0,
    'total_vendor_submitted_tax_amount' => 0,
    'total_vendor_submitted_amount' => 0,
    'total_final_certified_amount' => 0,
    'total_invoice_amount' => 0,
];
$invoice_ids = '';

$this->ci->load->model('purchase/purchase_model');
$sr = 1 + $this->ci->input->post('start');
foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {


        $base_currency = get_base_currency_pur();
        if ($aRow['currency'] != 0) {
            $base_currency = pur_get_currency_by_id($aRow['currency']);
        }

        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 1) {
            $_data = $sr++;
        } elseif ($aColumns[$i] == '0') {
            $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
        } else if ($aColumns[$i] == 'invoice_number') {
            $numberOutput = '';

            $numberOutput = '<a href="' . admin_url('purchase/purchase_invoice/' . $aRow['id']) . '" target="_blank"  >' . $aRow['invoice_number'] . '</a>';

            $numberOutput .= '<div class="row-options">';

            if (has_permission('purchase_invoices', '', 'view') || has_permission('purchase_invoices', '', 'view_own')) {
                $numberOutput .= ' <a href="' . admin_url('purchase/purchase_invoice/' . $aRow['id']) . '" target="_blank">' . _l('view') . '</a>';
            }
            if ((has_permission('purchase_invoices', '', 'edit') || is_admin())) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/pur_invoice/' . $aRow['id']) . '" target="_blank">' . _l('edit') . '</a>';
            }
            if (has_permission('purchase_invoices', '', 'delete') || is_admin()) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/delete_pur_invoice/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $numberOutput .= '</div>';

            $_data = $numberOutput;
        } else if ($aColumns[$i] == db_prefix() . 'items_groups.name') {
            $budget_head = '';
            $budget_head .= '<span class="inline-block label label-info" id="budget_span_' . $aRow['id'] . '">' . $aRow['name'];
            $budget_head .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $budget_head .= '<a href="#" class="dropdown-toggle text-dark" id="tableChangeBudget-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $budget_head .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $budget_head .= '</a>';
            $budget_head .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableChangeBudget-' . $aRow['id'] . '">';
            $group_name_item = get_group_name_item();
            foreach ($group_name_item as $gkey => $gvalue) {
                $budget_head .= '<li>
                    <a href="#" onclick="change_budget_head( ' . $gvalue['id'] . ',' . $aRow['id'] . '); return false;">
                    ' . $gvalue['name'] . '
                    </a>
                </li>';
            }
            $budget_head .= '</ul>';
            $budget_head .= '</div>';
            $budget_head .= '</span>';
            $_data = $budget_head;
        } else if ($aColumns[$i] == 'vendor_invoice_number') {
            if ($aRow['vendor_invoice_number'] != '') {
                $_data = '<input type="text" class="form-control vin-input" placeholder="Enter invoice number" data-id="' . $aRow['id'] . '" value="' . $aRow['vendor_invoice_number'] . '" size="10">';
            } else {
                $_data = $aRow['invoice_number'];
            }
        } elseif ($aColumns[$i] == db_prefix() . 'pur_invoices.id as inv_id') {

            $this->ci->load->model('purchase/purchase_model');
            $attachments = $this->ci->purchase_model->get_purchase_invoice_attachments($aRow['id']);
            $file_html = '';

            if (!empty($attachments)) {

                // URL for downloading the attachments as a ZIP file.
                // Ensure that you have a controller method at purchase/download_invoice_attachments/ that creates the ZIP.
                $zip_download_url = admin_url('purchase/download_invoice_attachments/' . $aRow['id']);

                $file_html .= '<a href="' . $zip_download_url . '" class="btn btn-primary" download>' . _l('Zip') . '</a>';
                $file_html .= '<hr>';
            }

            $_data = $file_html;
        } elseif ($aColumns[$i] == 3) {
            $order_data = '';
            if (!empty($aRow['order_tracker_id'])) {
                $order_tracker_detail = get_order_tracker_main_detail($aRow['order_tracker_id']);
                $order_data = $order_tracker_detail->pur_order_name;
            } else if (!empty($aRow['pur_order'])) {
                $pur_order_detail = get_pur_order_main_detail($aRow['pur_order']);
                $order_data =  '<span><a href="' . admin_url('purchase/pur_order/' . $pur_order_detail->id) . '" target="_blank">' . $pur_order_detail->pur_order_number . ' - ' . $pur_order_detail->pur_order_name . '</a></span>';
            } else if (!empty($aRow['wo_order'])) {
                $wo_order_detail = get_wo_order_main_detail($aRow['wo_order']);
                $order_data = '<span><a href="' . admin_url('purchase/wo_order/' . $wo_order_detail->id) . '" target="_blank">' . $wo_order_detail->wo_order_number . ' - ' . $wo_order_detail->wo_order_name . '</a><span>';
            } else {
                $order_data = '';
            }
            $_data = '<div style="width: 200px;">' . $order_data . '</div>';
        } elseif ($aColumns[$i] == 4) {
            $order_data = '';
            if (!empty($aRow['order_tracker_id'])) {
                $order_tracker_detail = get_order_tracker_main_detail($aRow['order_tracker_id']);
                $order_data = get_group_name_item($order_tracker_detail->group_pur)->name;
            } else if (!empty($aRow['pur_order'])) {
                $pur_order_detail = get_pur_order_main_detail($aRow['pur_order']);
                $order_data = get_group_name_item($pur_order_detail->group_pur)->name;
            } else if (!empty($aRow['wo_order'])) {
                $wo_order_detail = get_wo_order_main_detail($aRow['wo_order']);
                $order_data = get_group_name_item($wo_order_detail->group_pur)->name;
            } else {
                $order_data = '';
            }
            $_data = $order_data;
        } elseif ($aColumns[$i] == 'vendor_note') {
            $_data = render_tags($aRow['tags']);
        } elseif ($aColumns[$i] == 'invoice_date') {
            $_data = '<input type="date" class="form-control invoice-date-input" value="' . $aRow['invoice_date'] . '" data-id="' . $aRow['id'] . '">';
        } elseif ($aColumns[$i] == 'vendor_submitted_amount_without_tax') {

            // $_data = '<input type="text" class="form-control vsawt-input"  data-id="' . $aRow['id'] . '" value="' . app_format_money($aRow['vendor_submitted_amount_without_tax'], $base_currency->symbol) . '" >';
            // $_data = app_format_money($aRow['vendor_submitted_amount_without_tax'], $base_currency->symbol);

            // Check if budget exists in the database
            if (!empty($aRow['vendor_submitted_amount_without_tax'])) {
                // Display as plain text
                $_data = '<span class="vsawt-display" data-id="' . $aRow['id'] . '">' .
                    app_format_money($aRow['vendor_submitted_amount_without_tax'], $base_currency->symbol) .
                    '</span>';
            } else {
                // Render as an editable input if no budget exists
                $_data = '<input type="number" class="form-control vsawt-input" 
                         placeholder="Enter Certified Amount w/o Tax ( ₹ )" 
                         data-id="' . $aRow['id'] . '">';
            }
        } elseif ($aColumns[$i] == 'vendor_submitted_tax_amount') {
            // $tax = $this->ci->purchase_model->get_html_tax_pur_invoice($aRow['id']);
            // $total_tax = 0;
            // foreach ($tax['taxes_val'] as $tax_val) {
            //     $total_tax += $tax_val;
            // }

            $_data = app_format_money($aRow['vendor_submitted_tax_amount'], $base_currency->symbol);

            if (!empty($aRow['vendor_submitted_tax_amount'])) {
                // Display as plain text
                $_data = '<span class="vsta-display" data-id="' . $aRow['id'] . '">' .
                    app_format_money($aRow['vendor_submitted_tax_amount'], $base_currency->symbol) .
                    '</span>';
            } else {
                // Render as an editable input if no budget exists
                $_data = '<input type="number" class="form-control budget-input" 
                         placeholder="Enter Certified Amount w/o Tax ( ₹ )" 
                         data-id="' . $aRow['id'] . '">';
            }
        } elseif ($aColumns[$i] == 'final_certified_amount') {
            $_data = app_format_money($aRow['final_certified_amount'], $base_currency->symbol);
        }
        // elseif ($aColumns[$i] == 'vendor_submitted_amount') {
        //     $_data = app_format_money($aRow['vendor_submitted_amount'], $base_currency->symbol);
        // } 
        elseif ($aColumns[$i] == 'payment_status') {
            // $class = ''; 
            // if($aRow['payment_status'] == 'unpaid'){
            //     $class = 'danger';
            // }elseif($aRow['payment_status'] == 'paid'){
            //     $class = 'success';
            // }elseif ($aRow['payment_status'] == 'partially_paid') {
            //     $class = 'warning';
            // }

            // $_data = '<span class="label label-'.$class.' s-status invoice-status-3">'._l($aRow['payment_status']).'</span>';

            $delivery_status = '';

            if ($aRow['payment_status'] == 1) {
                $delivery_status = '<span class="inline-block label label-danger" id="status_span_' . $aRow['id'] . '" task-status-table="rejected">' . _l('rejected');
            } else if ($aRow['payment_status'] == 2) {
                $delivery_status = '<span class="inline-block label label-info" id="status_span_' . $aRow['id'] . '" task-status-table="recevied_with_comments">' . _l('recevied_with_comments');
            } else if ($aRow['payment_status'] == 3) {
                $delivery_status = '<span class="inline-block label label-warning" id="status_span_' . $aRow['id'] . '" task-status-table="bill_verification_in_process">' . _l('bill_verification_in_process');
            } else if ($aRow['payment_status'] == 4) {
                $delivery_status = '<span class="inline-block label label-primary" id="status_span_' . $aRow['id'] . '" task-status-table="bill_verification_on_hold">' . _l('bill_verification_on_hold');
            } else if ($aRow['payment_status'] == 5) {
                $delivery_status = '<span class="inline-block label label-success" id="status_span_' . $aRow['id'] . '" task-status-table="bill_verified_by_ril">' . _l('bill_verified_by_ril');
            } else if ($aRow['payment_status'] == 6) {
                $delivery_status = '<span class="inline-block label label-success" id="status_span_' . $aRow['id'] . '" task-status-table="payment_certifiate_issued">' . _l('payment_certifiate_issued');
            } else if ($aRow['payment_status'] == 7) {
                $delivery_status = '<span class="inline-block label label-success" id="status_span_' . $aRow['id'] . '" task-status-table="payment_processed">' . _l('payment_processed');
            } else if ($aRow['payment_status'] == 0) {
                $delivery_status = '<span class="inline-block label label-danger" id="status_span_' . $aRow['id'] . '" task-status-table="unpaid">' . _l('unpaid');
            }
            if (has_permission('purchase_invoices', '', 'edit') || is_admin()) {
                $delivery_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $delivery_status .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $delivery_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                $delivery_status .= '</a>';

                $delivery_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['id'] . '">';

                $delivery_status .= '<li>
                            <a href="#" onclick="change_payment_status( 0,' . $aRow['id'] . '); return false;">
                            ' . _l('unpaid') . '
                            </a>
                        </li>';
                $delivery_status .= '<li>
                              <a href="#" onclick="change_payment_status( 1,' . $aRow['id'] . '); return false;">
                                 ' . _l('rejected') . '
                              </a>
                           </li>';
                $delivery_status .= '<li>
                              <a href="#" onclick="change_payment_status( 2,' . $aRow['id'] . '); return false;">
                                 ' . _l('recevied_with_comments') . '
                              </a>
                           </li>';
                $delivery_status .= '<li>
                              <a href="#" onclick="change_payment_status( 3,' . $aRow['id'] . '); return false;">
                                 ' . _l('bill_verification_in_process') . '
                              </a>
                           </li>';
                $delivery_status .= '<li>
                           <a href="#" onclick="change_payment_status( 4,' . $aRow['id'] . '); return false;">
                              ' . _l('bill_verification_on_hold') . '
                           </a>
                        </li>';
                $delivery_status .= '<li>
                           <a href="#" onclick="change_payment_status( 5,' . $aRow['id'] . '); return false;">
                              ' . _l('bill_verified_by_ril') . '
                           </a>
                        </li>';
                $delivery_status .= '<li>
                        <a href="#" onclick="change_payment_status( 6,' . $aRow['id'] . '); return false;">
                           ' . _l('payment_certifiate_issued') . '
                        </a>
                     </li>';
                $delivery_status .= '<li>
                        <a href="#" onclick="change_payment_status( 7,' . $aRow['id'] . '); return false;">
                           ' . _l('payment_processed') . '
                        </a>
                     </li>';


                $delivery_status .= '</ul>';
                $delivery_status .= '</div>';
            }
            $delivery_status .= '</span>';
            $_data = $delivery_status;
        } elseif ($aColumns[$i] == 'contract') {
            $_data = '<a href="' . admin_url('purchase/contract/' . $aRow['contract']) . '" target="_blank">' . $aRow['contract_number'] . '</a>';
        } elseif ($aColumns[$i] == 'payment_request_status') {
            $_data = get_payment_request_status_by_inv($aRow['id']);
        } elseif ($aColumns[$i] == db_prefix() . 'pur_invoices.pur_order') {
            // $order_name = $aRow['description_services'];



            $order_name = '<textarea class="form-control description-services-input"  data-id="' . $aRow['id'] . '">' . $aRow['description_services'] . '</textarea>';
            // $order_name .= '<a href="' . admin_url('purchase/purchase_order/' . $aRow[db_prefix() . 'pur_invoices.pur_order']) . '">' . get_pur_order_subject($aRow[db_prefix() . 'pur_invoices.pur_order']) . '</a>';
            // $order_name .= '<a href="' . admin_url('purchase/work_order/' . $aRow[db_prefix() . 'pur_invoices.wo_order']) . '">' . get_wo_order_subject($aRow[db_prefix() . 'pur_invoices.wo_order']) . '</a>';
            $_data = $order_name;
        }
        // elseif ($aColumns[$i] == db_prefix() . 'pur_invoices.wo_order') {
        //     $_data = '<a href="' . admin_url('purchase/work_order/' . $aRow[db_prefix() . 'pur_invoices.wo_order']) . '">' . get_wo_order_subject($aRow[db_prefix() . 'pur_invoices.wo_order']) . '</a>';
        // } 
        elseif ($aColumns[$i] == db_prefix() . 'pur_vendor.company') {
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' .  $aRow[db_prefix() . 'pur_vendor.company'] . '</a>';
        } elseif ($aColumns[$i] == 'expense_convert') {
            $expense_convert = '';
            if ($aRow['expense_convert'] == 0) {
                $expense_convert = '<a href="javascript:void(0)" onclick="convert_expense(' . $aRow['id'] . ',' . $aRow['final_certified_amount'] . '); return false;" class="btn btn-warning btn-icon">' . _l('convert') . '</a>';
            } else {
                $expense_convert_check = get_expense_data($aRow['expense_convert']);
                if (!empty($expense_convert_check)) {
                    if (!empty($expense_convert_check->invoiceid)) {
                        $invoice_data = get_invoice_data($expense_convert_check->invoiceid);
                        if (!empty($invoice_data)) {

                            $expense_convert = '<a href="' . admin_url('invoices/list_invoices/' . $invoice_data->id) . '">' . $invoice_data->title . '</a>';

                            $invoice_ids .= $invoice_data->id . ",";
                        }
                    }
                } else {
                    $expense_convert = '<a href="javascript:void(0)" onclick="convert_expense(' . $aRow['id'] . ',' . $aRow['final_certified_amount'] . '); return false;" class="btn btn-warning btn-icon">' . _l('convert') . '</a>';
                }
            }
            $_data = $expense_convert;
        } elseif ($aColumns[$i] == 'adminnote') {
            // $_data = '<input type="date" class="form-control invoice-date-input" value="' . $aRow['invoice_date'] . '" data-id="' . $aRow['id'] . '">';

            $_data = '<textarea class="form-control adminnote-input"  data-id="' . $aRow['id'] . '">' . $aRow['adminnote'] . '</textarea>';
        } elseif ($aColumns[$i] == 'billing_remarks') {
            $order_name = '<textarea class="form-control billing-remarks-input"  data-id="' . $aRow['id'] . '" rows="3" style="width: 150px">' . $aRow['billing_remarks'] . '</textarea>';
            $_data = $order_name;
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;
    }

    $footer_data['total_vendor_submitted_amount_without_tax'] += $aRow['vendor_submitted_amount_without_tax'];
    $footer_data['total_vendor_submitted_tax_amount'] += $aRow['vendor_submitted_tax_amount'];
    $footer_data['total_vendor_submitted_amount'] += $aRow['vendor_submitted_amount'];
    $footer_data['total_final_certified_amount'] += $aRow['final_certified_amount'];
    $footer_data['total_invoice_amount'] = 0;
    $output['aaData'][] = $row;
}

if (!empty($invoice_ids)) {
    $invoice_ids = rtrim($invoice_ids, ",");
    $invoice_amount = get_pur_invoice_subtotal($invoice_ids);
    $footer_data['total_invoice_amount'] = $invoice_amount;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, $base_currency->symbol);
}

$output['sums'] = $footer_data;
