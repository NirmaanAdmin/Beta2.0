<?php

defined('BASEPATH') or exit('No direct script access allowed');

$module_name = 'vendor_billing_payments';
$from_date_filter_name = 'from_date';
$to_date_filter_name = 'to_date';
$vendors_filter_name = 'vendors';
$budget_head_filter_name = 'budget_head';
$billing_invoices_filter_name = 'billing_invoices';
$bil_payment_status_filter_name = 'bil_payment_status';

$aColumns = [
    1,
    'invoice_number',
    'vendor_invoice_number',
    db_prefix() . 'pur_invoices.vendor',
    db_prefix() . 'items_groups.name',
    'invoice_date',
    'vendor_submitted_amount_without_tax',
    'vendor_submitted_tax_amount',
    'final_certified_amount',
    2,
    3,
    4,
    'bil_total',
    5,
    'ril_previous',
    db_prefix() . 'invoicepaymentrecords.amount as ril_this_bill',
    db_prefix() . 'invoicepaymentrecords.date as ril_date',
    'ril_amount',
    'payment_remarks',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'pur_invoices';
$join         = [
    'LEFT JOIN ' . db_prefix() . 'invoicepaymentrecords ON ' . db_prefix() . 'invoicepaymentrecords.pur_invoice = ' . db_prefix() . 'pur_invoices.id',
    'LEFT JOIN ' . db_prefix() . 'pur_contracts ON ' . db_prefix() . 'pur_contracts.id = ' . db_prefix() . 'pur_invoices.contract',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'pur_invoices.project_id = ' . db_prefix() . 'projects.id',
    'LEFT JOIN ' . db_prefix() . 'items_groups ON ' . db_prefix() . 'pur_invoices.group_pur = ' . db_prefix() . 'items_groups.id',
    'LEFT JOIN ' . db_prefix() . 'itemable AS itm ON itm.vbt_id = ' . db_prefix() . 'pur_invoices.id AND itm.rel_type = "invoice"',
    'LEFT JOIN ' . db_prefix() . 'invoices AS ril ON ril.id = itm.rel_id'
];

$where = [];

if (
    $this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != ''
) {
    array_push($where, 'AND invoice_date >= "' . to_sql_date($this->ci->input->post('from_date')) . '"');
}

if (
    $this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != ''
) {
    array_push($where, 'AND invoice_date <= "' . to_sql_date($this->ci->input->post('to_date')) . '"');
}

if ($this->ci->input->post('billing_invoices') && $this->ci->input->post('billing_invoices') != '') {
    if ($this->ci->input->post('billing_invoices') == "to_be_converted") {
        array_push($where, 'AND (ril.id IS NULL)');
    } else {
        array_push($where, 'AND (ril.id IS NOT NULL)');
    }
}

if ($this->ci->input->post('bil_payment_status') && $this->ci->input->post('bil_payment_status') != '') {
    if ($this->ci->input->post('bil_payment_status') == "paid") {
        array_push($where, 'AND (payment_status = "paid")');
    } else if ($this->ci->input->post('bil_payment_status') == "partially_paid") {
        array_push($where, 'AND (payment_status = "partially_paid")');
    } else {
        array_push($where, 'AND (payment_status != "partially_paid" AND payment_status != "paid")');
    }
}

if (!has_permission('purchase_invoices', '', 'view')) {
    array_push($where, 'AND (' . db_prefix() . 'pur_invoices.add_from = ' . get_staff_user_id() . ' OR ' . db_prefix() . 'pur_invoices.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
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

if(get_default_project()) {
    array_push($where, 'AND ' . db_prefix() . 'pur_invoices.project_id = '.get_default_project().'');
}

$from_date_filter_value = !empty($this->ci->input->post('from_date')) ? $this->ci->input->post('from_date') : NULL;
update_module_filter($module_name, $from_date_filter_name, $from_date_filter_value);

$to_date_filter_value = !empty($this->ci->input->post('to_date')) ? $this->ci->input->post('to_date') : NULL;
update_module_filter($module_name, $to_date_filter_name, $to_date_filter_value);

$vendors_filter_value = !empty($this->ci->input->post('vendors')) ? implode(',', $this->ci->input->post('vendors')) : NULL;
update_module_filter($module_name, $vendors_filter_name, $vendors_filter_value);

$budget_head_filter_name_value = !empty($this->ci->input->post('budget_head')) ? $this->ci->input->post('budget_head') : NULL;
update_module_filter($module_name, $budget_head_filter_name, $budget_head_filter_name_value);

$billing_invoices_filter_name_value = !empty($this->ci->input->post('billing_invoices')) ? $this->ci->input->post('billing_invoices') : NULL;
update_module_filter($module_name, $billing_invoices_filter_name, $billing_invoices_filter_name_value);

$bil_payment_status_filter_name_value = !empty($this->ci->input->post('bil_payment_status')) ? $this->ci->input->post('bil_payment_status') : NULL;
update_module_filter($module_name, $bil_payment_status_filter_name, $bil_payment_status_filter_name_value);

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'pur_invoices.id as id',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'pur_invoices.id and rel_type="pur_invoice" ORDER by tag_order ASC) as tags',
    'contract_number',
    'invoice_number',
    db_prefix() . 'pur_invoices.currency',
    'expense_convert',
    db_prefix() . 'pur_invoices.wo_order',
    db_prefix() . 'items_groups.name',
    db_prefix() . 'pur_invoices.description_services',
    'ril.id as ril_invoice_id',
    'ril.title as ril_invoice_title',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

$all_bil_tds = 0;
$all_payment_made = 0;
$footer_data = [
    'total_vendor_submitted_amount_without_tax' => 0,
    'total_vendor_submitted_tax_amount' => 0,
    'total_final_certified_amount' => 0,
    'total_payment_made' => 0,
    'total_bil_tds' => 0,
    'total_bil_total' => 0,
    'total_ril_previous' => 0,
    'total_ril_this_bill' => 0,
    'total_ril_amount' => 0,
];

$this->ci->load->model('purchase/purchase_model');
$sr = 1 + $this->ci->input->post('start');
foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {

        $base_currency = get_base_currency_pur();
        if ($aRow['currency'] != 0) {
            $base_currency = pur_get_currency_by_id($aRow['currency']);
        }

        $ril_invoice_link = '';
        // $ril_invoice_item = get_ril_invoice_item($aRow['id']);
        // if(!empty($ril_invoice_item)) {
        //     $invoice_data = get_invoice_data($ril_invoice_item->rel_id);
        //     $ril_invoice_link = '<a href="' . admin_url('invoices/list_invoices/' . $invoice_data->id) . '">' . $invoice_data->title . '</a>';
        // }
        if(!empty($aRow['ril_invoice_id'])) {
            $ril_invoice_link = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['ril_invoice_id']) . '">' . $aRow['ril_invoice_title'] . '</a>';
        }

        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if($aColumns[$i] == 1){
            $_data = $sr++;
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
            $numberOutput .= '</div>';

            $_data = $numberOutput;
        } else if ($aColumns[$i] == db_prefix() . 'items_groups.name') {
            $budget_head = '';
            $budget_head .= '<span class="inline-block label label-info" id="budget_span_' . $aRow['id'] . '">' . $aRow['name'];
            $budget_head .= '</span>';
            $_data = $budget_head;
        } else if ($aColumns[$i] == 'vendor_invoice_number') {
            if ($aRow['vendor_invoice_number'] != '') {
                $_data = $aRow['vendor_invoice_number'];
            } else {
                $_data = $aRow['invoice_number'];
            }
        } elseif ($aColumns[$i] == 'invoice_date') {
            $_data = date('d-m-Y', strtotime($aRow['invoice_date']));
        } elseif ($aColumns[$i] == 'vendor_submitted_amount_without_tax') {
            $_data = app_format_money($aRow['vendor_submitted_amount_without_tax'], $base_currency->symbol);
        } elseif ($aColumns[$i] == 'vendor_submitted_tax_amount') {
            $_data = app_format_money($aRow['vendor_submitted_tax_amount'], $base_currency->symbol);
        } elseif ($aColumns[$i] == 'final_certified_amount') {
            $_data = app_format_money($aRow['final_certified_amount'], $base_currency->symbol);
        } elseif ($aColumns[$i] == db_prefix() . 'pur_invoices.vendor') {
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow[db_prefix() . 'pur_invoices.vendor']) . '" target="_blank">' .  get_vendor_company_name($aRow[db_prefix() . 'pur_invoices.vendor']) . '</a>';
        } elseif ($aColumns[$i] == 'bil_tds') {
            $_data = '<span class="bil-tds-display" data-id="' . $aRow['id'] . '">' .app_format_money($aRow['bil_tds'], $base_currency->symbol) .'</span>';
        } elseif ($aColumns[$i] == 'bil_total') {
            $_data = app_format_money($aRow['bil_total'], $base_currency->symbol);
        } elseif ($aColumns[$i] == 'ril_previous') {
            if(!empty($ril_invoice_link)) {
                $_data = '<span class="ril-previous-display" data-id="' . $aRow['id'] . '">' .app_format_money($aRow['ril_previous'], $base_currency->symbol) .'</span>';
            } else {
                $_data = '';
            }
        } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.amount as ril_this_bill') {
            if(!empty($ril_invoice_link)) {
                $_data = '<span class="ril-this-bill-display" data-id="' . $aRow['id'] . '">' .app_format_money($aRow['ril_this_bill'], $base_currency->symbol) .'</span>';
            } else {
                $_data = '';
            }
        } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.date as ril_date') {
            if(!empty($ril_invoice_link)) {
                $_data = '<input type="date" class="form-control ril-date-input" value="' . $aRow['ril_date'] . '" data-id="' . $aRow['id'] . '" style="width: 138px">';
            } else {
                $_data = '';
            }
        } elseif ($aColumns[$i] == 'ril_amount') {
            if(!empty($ril_invoice_link)) {
                $_data = app_format_money($aRow['ril_amount'], $base_currency->symbol);
            } else {
                $_data = '';
            }
        } elseif ($aColumns[$i] == 2) {
            $bil_payment_details = get_bil_payment_details($aRow['id']);
            if(!empty($bil_payment_details)) {
                $_data = '';
                foreach ($bil_payment_details as $bkey => $bvalue) {
                    $_data .= '<div class="input-group date all_payment_date" data-id="' . $aRow['id'] . '">
                        <input type="date" class="form-control payment-date-input" data-payment-id="' . $bvalue['id'] . '" data-id="' . $aRow['id'] . '" value="' . $bvalue['date'] . '" style="width: 138px">
                        <div class="input-group-addon">
                            <i class="fa fa-plus add_new_payment_date" data-id="' . $aRow['id'] . '" style="cursor: pointer;"></i>
                        </div>
                    </div>';
                }
            } else {
                $_data = '<div class="input-group date all_payment_date" data-id="' . $aRow['id'] . '">
                    <input type="date" class="form-control payment-date-input" data-payment-id="0" data-id="' . $aRow['id'] . '" style="width: 138px">
                    <div class="input-group-addon">
                        <i class="fa fa-plus add_new_payment_date" data-id="' . $aRow['id'] . '" style="cursor: pointer;"></i>
                    </div>
                </div>';
            }
        } elseif ($aColumns[$i] == 3) {
            $bil_payment_details = get_bil_payment_details($aRow['id']);
            if(!empty($bil_payment_details)) {
                $_data = '';
                foreach ($bil_payment_details as $bkey => $bvalue) {
                    $all_payment_made = $all_payment_made + $bvalue['amount'];
                    $_data .= '<div class="input-group all_payment_made" data-id="' . $aRow['id'] . '">
                        <input type="number" class="form-control payment-made-input" data-payment-id="' . $bvalue['id'] . '" data-id="' . $aRow['id'] . '" value="' . $bvalue['amount'] . '" style="width: 138px">
                        <div class="input-group-addon">
                            <i class="fa fa-plus add_new_payment_made" data-id="' . $aRow['id'] . '" style="cursor: pointer;"></i>
                        </div>
                    </div>';
                }
            } else {
                $_data = '<div class="input-group all_payment_made" data-id="' . $aRow['id'] . '">
                    <input type="number" class="form-control payment-made-input" data-payment-id="0" data-id="' . $aRow['id'] . '" style="width: 138px">
                    <div class="input-group-addon">
                        <i class="fa fa-plus add_new_payment_made" data-id="' . $aRow['id'] . '" style="cursor: pointer;"></i>
                    </div>
                </div>';
            }
        } elseif ($aColumns[$i] == 4) {
            $bil_payment_details = get_bil_payment_details($aRow['id']);
            if(!empty($bil_payment_details)) {
                $_data = '';
                foreach ($bil_payment_details as $bkey => $bvalue) {
                    $all_bil_tds = $all_bil_tds + $bvalue['tds'];
                    $_data .= '<div class="input-group all_payment_tds" data-id="' . $aRow['id'] . '">
                        <input type="number" class="form-control payment-tds-input" data-payment-id="' . $bvalue['id'] . '" data-id="' . $aRow['id'] . '" value="' . $bvalue['tds'] . '" style="width: 138px">
                        <div class="input-group-addon">
                            <i class="fa fa-plus add_new_payment_tds" data-id="' . $aRow['id'] . '" style="cursor: pointer;"></i>
                        </div>
                    </div>';
                }
            } else {
                $_data = '<div class="input-group all_payment_tds" data-id="' . $aRow['id'] . '">
                    <input type="number" class="form-control payment-tds-input" data-payment-id="0" data-id="' . $aRow['id'] . '" style="width: 138px">
                    <div class="input-group-addon">
                        <i class="fa fa-plus add_new_payment_tds" data-id="' . $aRow['id'] . '" style="cursor: pointer;"></i>
                    </div>
                </div>';
            }
        } elseif ($aColumns[$i] == 5) {
            if(!empty($ril_invoice_link)) {
                $_data = $ril_invoice_link;
            } else {
                $_data = '';
            }
        } elseif ($aColumns[$i] == 'payment_remarks') {
            $order_name = '<textarea class="form-control payment-remarks-input"  data-id="' . $aRow['id'] . '" rows="3" style="width: 150px">' . $aRow['payment_remarks'] . '</textarea>';
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
    $footer_data['total_final_certified_amount'] += $aRow['final_certified_amount'];
    $footer_data['total_payment_made'] = $all_payment_made;
    $footer_data['total_bil_tds'] = $all_bil_tds;
    $footer_data['total_bil_total'] += $aRow['bil_total'];
    $footer_data['total_ril_previous'] += $aRow['ril_previous'];
    $footer_data['total_ril_this_bill'] += $aRow['ril_this_bill'];
    $footer_data['total_ril_amount'] += $aRow['ril_amount'];
    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, $base_currency->symbol);
}

$output['sums'] = $footer_data;
