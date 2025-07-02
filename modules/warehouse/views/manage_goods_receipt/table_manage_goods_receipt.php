<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'goods_receipt_code',
    'supplier_name',
    'buyer_id',
    'kind',
    1,
    'date_add',
    // 'total_tax_money', 
    // 'total_goods_money',
    // 'value_of_inventory',
    // 'total_money',
    'approval',
    'id as pdf_id',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'goods_receipt';
$join         = [];
$where = [];



if ($this->ci->input->post('day_vouchers')) {
    $day_vouchers = to_sql_date($this->ci->input->post('day_vouchers'));
}

if ($this->ci->input->post('kind')) {
    $kind = $this->ci->input->post('kind');
}

if($this->ci->input->post('vendor')){
    $vendor = $this->ci->input->post('vendor');
}

if($this->ci->input->post('status')){
    $status = $this->ci->input->post('status');
}

if (isset($day_vouchers)) {
    $where[] = 'AND tblgoods_receipt.date_add <= "' . $day_vouchers . '"';
}

if (isset($kind)) {
    $where[] = 'AND tblgoods_receipt.kind = "' . $kind . '"';
}

if(!empty($vendor)){
    $where[] = 'AND tblgoods_receipt.supplier_code IN(' . implode(',', $vendor) . ')';
}
// $status = $this->ci->input->post('status');
if (isset($status)) {
    if($status == 'approved'){
        $where[] = 'AND tblgoods_receipt.approval = 1';
    }elseif ($status == 'not_yet_approve') {
        $where[] = 'AND tblgoods_receipt.approval = 0';
    }
}

if(get_default_project()) {
    $where[] = 'AND ' . db_prefix() . 'goods_receipt.project = '.get_default_project().'';
}

$this->ci->load->model('purchase/purchase_model');
$custom_date_select = $this->ci->purchase_model->get_where_report_period(db_prefix() . 'goods_receipt.date_add');
if ($custom_date_select != '') {
    array_push($where, $custom_date_select);
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'date_add', 'date_c', 'goods_receipt_code', 'supplier_code', 'pr_order_id', 'wo_order_id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'supplier_name') {

            if (get_status_modules_wh('purchase') && ($aRow['supplier_code'] != '') && ($aRow['supplier_code'] != 0)) {
                $_data = wh_get_vendor_company_name($aRow['supplier_code']);
            } else {
                $_data = $aRow['supplier_name'];
            }
        } elseif ($aColumns[$i] == 'buyer_id') {
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['buyer_id']) . '">' . staff_profile_image($aRow['buyer_id'], [
                'staff-profile-image-small',
            ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['buyer_id']) . '">' . get_staff_full_name($aRow['buyer_id']) . '</a>';
        } elseif ($aColumns[$i] == 'date_add') {
            $_data = _d($aRow['date_add']);
        } elseif ($aColumns[$i] == 'total_tax_money') {
            // $_data = app_format_money((float)$aRow['total_tax_money'],'');
        } elseif ($aColumns[$i] == 'goods_receipt_code') {
            $name = '<a href="' . admin_url('warehouse/view_purchase/' . $aRow['id']) . '" onclick="init_goods_receipt(' . $aRow['id'] . ');small_table_full_view(); return false;">' . $aRow['goods_receipt_code'] . '</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="' . admin_url('warehouse/edit_purchase/' . $aRow['id']) . '" >' . _l('view') . '</a>';

            if ((has_permission('warehouse', '', 'edit') || is_admin()) && ($aRow['approval'] == 0)) {
                $name .= ' | <a href="' . admin_url('warehouse/manage_goods_receipt/' . $aRow['id']) . '" >' . _l('edit') . '</a>';
            }

            if ((has_permission('warehouse', '', 'delete') || is_admin()) && ($aRow['approval'] == 0)) {
                $name .= ' | <a href="' . admin_url('warehouse/delete_goods_receipt/' . $aRow['id']) . '" class="text-danger _delete" >' . _l('delete') . '</a>';
            }

            if (get_warehouse_option('revert_goods_receipt_goods_delivery') == 1) {
                if ((has_permission('warehouse', '', 'delete') || is_admin()) && ($aRow['approval'] == 1)) {
                    $name .= ' | <a href="' . admin_url('warehouse/revert_goods_receipt/' . $aRow['id']) . '" class="text-danger _delete" >' . _l('delete_after_approval') . '</a>';
                }
            }

            $name .= '</div>';

            $_data = $name;
        } elseif ($aColumns[$i] == 'total_goods_money') {
            // $_data = app_format_money((float)$aRow['total_goods_money'],'');
        } elseif ($aColumns[$i] == 'total_money') {
            // $_data = app_format_money((float)$aRow['total_money'],'');
        } elseif ($aColumns[$i] == 'value_of_inventory') {
            // $_data = app_format_money((float)$aRow['value_of_inventory'],'');
        } elseif ($aColumns[$i] == 'approval') {

            if ($aRow['approval'] == 1) {
                $_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">' . _l('approved') . '</span><span class="hide">, </span></span>&nbsp';
            } elseif ($aRow['approval'] == 0) {
                $_data = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">' . _l('not_yet_approve') . '</span><span class="hide">, </span></span>&nbsp';
            } elseif ($aRow['approval'] == -1) {
                $_data = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">' . _l('reject') . '</span><span class="hide">, </span></span>&nbsp';
            }
        } elseif ($aColumns[$i] == 'id as pdf_id') {
            $pdf = '<div class="btn-group display-flex" >';
            $pdf .= '<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><span class="caret"></span></a>';
            $pdf .= '<ul class="dropdown-menu dropdown-menu-right">';
            $pdf .= '<li class="hidden-xs"><a href="' . admin_url('warehouse/stock_import_pdf/' . $aRow['id'] . '?output_type=I') . '">' . _l('view_pdf') . '</a></li>';
            $pdf .= '<li class="hidden-xs"><a href="' . admin_url('warehouse/stock_import_pdf/' . $aRow['id'] . '?output_type=I') . '" target="_blank">' . _l('view_pdf_in_new_window') . '</a></li>';
            $pdf .= '<li><a href="' . admin_url('warehouse/stock_import_pdf/' . $aRow['id']) . '">' . _l('download') . '</a></li>';
            $pdf .= '<li><a href="' . admin_url('warehouse/stock_import_pdf/' . $aRow['id'] . '?print=true') . '" target="_blank">' . _l('print') . '</a></li>';
            $pdf .= '</ul>';


            // Add the View/Edit button
            if (has_permission("warehouse", "", "edit")) {
                $pdf .= '<a href="' . admin_url("warehouse/edit_purchase/" . $aRow['id']) . '" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="' . _l("view") . '" data-placement="bottom"><i class="fa fa-eye"></i></a>';
            }

            $pdf .= '</div>';

            $_data .= $pdf;
        } elseif ($aColumns[$i] == 1) {
            $get_order_name = '';
            if (get_status_modules_wh('purchase')) {
                if (($aRow['pr_order_id'] != '') && ($aRow['pr_order_id'] != 0)) {
                    $get_order_name .= '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pr_order_id']) . '" >' . get_pur_order_name($aRow['pr_order_id']) . '</a>';
                }
            }
            if (get_status_modules_wh('purchase')) {
                if (($aRow['wo_order_id'] != '') && ($aRow['wo_order_id'] != 0)) {
                    $get_order_name .= '<a href="' . admin_url('purchase/work_order/' . $aRow['wo_order_id']) . '" >' . get_wo_order_name($aRow['wo_order_id']) . '</a>';
                }
            }

            $_data = $get_order_name;
        }



        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
