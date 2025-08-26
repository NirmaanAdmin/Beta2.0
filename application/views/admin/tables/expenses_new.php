<?php

defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'expenses';

$expense_category_name = 'expense_category';
$payment_mode_name = 'payment_mode';
$vendor_name = 'vendor';

$CI = &get_instance();
$CI->load->model('expenses_model');
$CI->load->model('payment_modes_model');

$hasPermissionEdit = staff_can('edit', 'expenses');
$hasPermissionDelete = staff_can('delete', 'expenses');

$aColumns = [
    '1',
    db_prefix() . 'expenses.id as id',
    db_prefix() . 'expenses_categories.name as category_name',
    'amount',
    'expense_name',
    'file_name',
    'date',
    db_prefix() . 'projects.name as project_name',
    get_sql_select_client_company(),
    'invoiceid',
    'reference_no',
    'paymentmode',
    'vendor',
    1,
];

$custom_fields = get_table_custom_fields('expenses');
$customFieldsColumns = [];
foreach ($custom_fields as $key => $field) {
    $selectAs = is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key;
    $customFieldsColumns[] = $selectAs;
    $aColumns[] = '(SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE relid=' . db_prefix() . 'expenses.id AND fieldid=' . $field['id'] . ' AND fieldto="' . $field['fieldto'] . '" LIMIT 1) as ' . $selectAs;
}

$aColumns = hooks()->apply_filters('expenses_table_sql_columns', $aColumns);

if (count($custom_fields) > 4) {
    @$CI->db->query('SET SQL_BIG_SELECTS=1');
}

$where = [];
$join = [
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'expenses.clientid',
    'JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'expenses.project_id',
    'LEFT JOIN ' . db_prefix() . 'files ON ' . db_prefix() . 'files.rel_id = ' . db_prefix() . 'expenses.id AND rel_type="expense"',
    'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'expenses.currency',
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'expenses.vendor',
];

if ($CI->input->post('filters')) {
    $filters = $CI->input->post('filters');
    if (isset($filters['rules']) && is_array($filters['rules'])) {
        $where[] = $this->getWhereFromRules($filters['rules']);
    }
}

if (staff_cant('view', 'expenses')) {
    $where[] = 'AND ' . db_prefix() . 'expenses.addedfrom=' . get_staff_user_id();
}

array_push($where, 'AND invoiceid IS NULL');

if (get_default_project()) {
    $where[] = 'AND ' . db_prefix() . 'expenses.project_id = ' . get_default_project();
}

if ($this->ci->input->post('expense_category') && count($this->ci->input->post('expense_category')) > 0) {
    array_push($where, 'AND ' . db_prefix() . 'expenses.category IN (' . implode(',', $this->ci->input->post('expense_category')) . ')');
}

if ($this->ci->input->post('payment_mode') && count($this->ci->input->post('payment_mode')) > 0) {
    array_push($where, 'AND paymentmode IN (' . implode(',', $this->ci->input->post('payment_mode')) . ')');
}

if ($this->ci->input->post('vendor') && count($this->ci->input->post('vendor')) > 0) {
    array_push($where, 'AND vendor IN (' . implode(',', $this->ci->input->post('vendor')) . ')');
}

$expense_category_name_value = !empty($this->ci->input->post('expense_category')) ? implode(',', $this->ci->input->post('expense_category')) : NULL;
update_module_filter($module_name, $expense_category_name, $expense_category_name_value);

$payment_mode_name_value = !empty($this->ci->input->post('payment_mode')) ? implode(',', $this->ci->input->post('payment_mode')) : NULL;
update_module_filter($module_name, $payment_mode_name, $payment_mode_name_value);

$vendor_name_value = !empty($this->ci->input->post('vendor')) ? implode(',', $this->ci->input->post('vendor')) : NULL;
update_module_filter($module_name, $vendor_name, $vendor_name_value);

$sIndexColumn = 'id';
$sTable = db_prefix() . 'expenses';

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    [
        'billable',
        db_prefix() . 'currencies.name as currency_name',
        db_prefix() . 'expenses.clientid',
        'tax',
        'tax2',
        'project_id',
        'recurring',
        'vbt_id',
        db_prefix() . 'pur_vendor.company as vendor_name',
    ]
);

$output = $result['output'];
$rResult = $result['rResult'];
$sr = 1;

$footer_data = [
    'total_expense_amount' => 0,
];

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    $row[] = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" onclick="init_expense(' . $aRow['id'] . ');return false;">' . $sr++ . '</a>';

    $categoryOutput = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" onclick="init_expense(' . $aRow['id'] . ');return false;">' . e($aRow['category_name']) . '</a>';

    if ($aRow['billable'] == 1) {
        $categoryOutput .= $aRow['invoiceid'] == null ? '<p class="text-danger">' . _l('expense_list_unbilled') . '</p>' : '<p class="text-success">' . _l('expense_list_invoice') . '</p>';
    }
    if ($aRow['recurring'] == 1) {
        $categoryOutput .= '<span class="label label-primary"> ' . _l('expense_recurring_indicator') . '</span>';
    }

    $categoryOutput .= '<div class="row-options">';
    $categoryOutput .= '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" onclick="init_expense(' . $aRow['id'] . ');return false;">' . _l('view') . '</a>';
    if ($hasPermissionEdit && empty($aRow['invoiceid'])) {
        $categoryOutput .= ' | <a href="' . admin_url('expenses/expense/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }
    if ($hasPermissionDelete) {
        $categoryOutput .= ' | <a href="' . admin_url('expenses/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }
    $categoryOutput .= '</div>';

    $row[] = $categoryOutput;

    $total = $aRow['amount'];
    $tmpTotal = $total;
    if ($aRow['tax'] != 0) {
        $tax = get_tax_by_id($aRow['tax']);
        $total += ($total / 100 * $tax->taxrate);
    }
    if ($aRow['tax2'] != 0) {
        $tax = get_tax_by_id($aRow['tax2']);
        $total += ($tmpTotal / 100 * $tax->taxrate);
    }
    $row[] = app_format_money($total, $aRow['currency_name']);
    $row[] = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" onclick="init_expense(' . $aRow['id'] . ');return false;">' . e($aRow['expense_name']) . '</a>';
    $row[] = !empty($aRow['file_name']) ? '<a href="' . site_url('download/file/expense/' . $aRow['id']) . '">' . e($aRow['file_name']) . '</a>' : '';
    $row[] = date('d M, Y', strtotime($aRow['date']));
    $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . e($aRow['project_name']) . '</a>';
    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . e($aRow['company']) . '</a>';

    if ($aRow['vbt_id']) {
        $pur_invoices = get_pur_invoices($aRow['vbt_id']);
        if(!empty($pur_invoices)) {
            $row[] = '<a href="' . admin_url('purchase/purchase_invoice/' . $aRow['vbt_id']) . '" target="_blank">' .$pur_invoices->invoice_number . '</a>';
        } else {
          $row[] = '';  
        }
    } else {
        $row[] = '';
    }

    $row[] = e($aRow['reference_no']);

    $paymentModeOutput = '';
    if (!empty($aRow['paymentmode']) && $aRow['paymentmode'] != '0') {
        $payment_mode = $CI->payment_modes_model->get($aRow['paymentmode'], [], false, true);
        if ($payment_mode) {
            $paymentModeOutput = e($payment_mode->name);
        }
    }
    $row[] = $paymentModeOutput;

    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '">' . e($aRow['vendor_name']) . '</a>';

    $pdf = '';
    $pdf = '<div class="btn-group display-flex">';
    $pdf .= '<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><span class="caret"></span></a>';
    $pdf .= '<ul class="dropdown-menu dropdown-menu-right">';
    $pdf .= '<li class="hidden-xs"><a href="' . admin_url('expenses/pdf/' . $aRow['id'] . '?output_type=I') . '">' . _l('view_pdf') . '</a></li>';
    $pdf .= '<li class="hidden-xs"><a href="' . admin_url('expenses/pdf/' . $aRow['id'] . '?output_type=I') . '" target="_blank">' . _l('view_pdf_in_new_window') . '</a></li>';
    $pdf .= '<li><a href="' . admin_url('expenses/pdf/' . $aRow['id']) . '">' . _l('download') . '</a></li>';
    $pdf .= '<li><a href="' . admin_url('expenses/pdf/' . $aRow['id'] . '?print=true') . '" target="_blank">' . _l('print') . '</a></li>';
    $pdf .= '</ul>';
    $pdf .= '</div>';
    $row[] = $pdf;

    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn];
    }

    $row['DT_RowClass'] = 'has-row-options';
    $footer_data['total_expense_amount'] += $total;
    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $exp_total) {
    $footer_data[$key] = app_format_money($exp_total, $aRow['currency_name']);
}
$output['sums'] = $footer_data;
