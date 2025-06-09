<?php
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'goods_delivery_code',
    'pr_order_id',
    'date_add',
    'approval',
    'delivery_status',
    'id as pdf' // we’ll handle 'pdf' separately, not as 'id as pdf'
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'goods_delivery';
$join         = [];

$where = [];

if ($this->ci->input->post('day_vouchers')) {
    $day_vouchers = to_sql_date($this->ci->input->post('day_vouchers'));
    if ($day_vouchers) {
        $where[] = 'AND ' . $sTable . '.date_add <= "' . $day_vouchers . '"';
    }
}

if ($this->ci->input->post('invoice_id')) {
    $invoice_id = $this->ci->input->post('invoice_id');
    if ($invoice_id) {
        $where[] = 'AND ' . $sTable . '.invoice_id = "' . $invoice_id . '"';
    }
}

$approval = $this->ci->input->post('approval');
if ($approval == 0) {
    $where[] = 'AND ' . $sTable . '.approval = 0';
} else if ($approval == 1) {
    $where[] = 'AND ' . $sTable . '.approval = 1';
} else if ($approval == -1) {
    $where[] = 'AND ' . $sTable . '.approval = -1';
}

if ($this->ci->input->post('delivery_status')) {
    $delivery_status = $this->ci->input->post('delivery_status');
    if ($delivery_status) {
        $where[] = 'AND ' . $sTable . '.delivery_status = "'.$delivery_status.'"';
    }
}

// Add any extra fields you want to retrieve
$additionalSelect = [
    'id',
    'date_add',
    'date_c',
    'goods_delivery_code',
    'total_money',
    'type_of_delivery'
];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    foreach ($aColumns as $column) {
        $CI = &get_instance();
        $_data = $aRow[$column];

        switch ($column) {
            case 'customer_code':
                $_data = '';
                if (!empty($aRow['customer_code'])) {
                    $CI->db->where(db_prefix() . 'clients.userid', $aRow['customer_code']);
                    $client = $CI->db->get(db_prefix() . 'clients')->row();
                    if ($client) {
                        $_data = $client->company;
                    }
                }
                break;

            case 'invoice_id':
                $_data = '';
                if (!empty($aRow['invoice_id'])) {
                    $type_of_delivery = '';
                    if ($aRow['type_of_delivery'] == 'partial') {
                        $type_of_delivery = ' (<span class="text-danger">' . _l($aRow['type_of_delivery']) . '</span>)';
                    } elseif ($aRow['type_of_delivery'] == 'total') {
                        $type_of_delivery = ' (<span class="text-success">' . _l($aRow['type_of_delivery']) . '</span>)';
                    }
                    $_data = format_invoice_number($aRow['invoice_id']) . get_invoice_company_projecy($aRow['invoice_id']) . $type_of_delivery;
                }
                break;

            case 'date_add':
                $_data = _d($aRow['date_add']);
                break;

            case 'staff_id':
                $_data = '<a href="' . admin_url('staff/profile/' . $aRow['staff_id']) . '">' . staff_profile_image($aRow['staff_id'], ['staff-profile-image-small']) . '</a>';
                $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['staff_id']) . '">' . get_staff_full_name($aRow['staff_id']) . '</a>';
                break;

            case 'goods_delivery_code':
                $name = '<a href="' . admin_url('warehouse/view_delivery/' . $aRow['id']) . '" onclick="init_goods_delivery(' . $aRow['id'] . '); small_table_full_view(); return false;">' . $aRow['goods_delivery_code'] . '</a>';
                $name .= '<div class="row-options">';
                $name .= '<a href="' . admin_url('warehouse/edit_delivery/' . $aRow['id']) . '">' . _l('view') . '</a>';
                if ((has_permission('warehouse', '', 'edit') || is_admin()) && ($aRow['approval'] == 0)) {
                    $name .= ' | <a href="' . admin_url('warehouse/goods_delivery/' . $aRow['id']) . '">' . _l('edit') . '</a>';
                }
                if ((is_admin()) && ($aRow['approval'] == 1)) {
                    $name .= ' | <a href="' . admin_url('warehouse/goods_delivery/' . $aRow['id']) . '/true">' . _l('edit') . '</a>';
                }
                if ((has_permission('warehouse', '', 'delete') || is_admin()) && ($aRow['approval'] == 0)) {
                    $name .= ' | <a href="' . admin_url('warehouse/delete_goods_delivery/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }
                if (get_warehouse_option('revert_goods_receipt_goods_delivery') == 1) {
                    if ((has_permission('warehouse', '', 'delete') || is_admin()) && ($aRow['approval'] == 1)) {
                        $name .= ' | <a href="' . admin_url('warehouse/revert_goods_delivery/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete_after_approval') . '</a>';
                    }
                }
                $name .= '</div>';
                $_data = $name;
                break;

            case 'approval':
                if ($aRow['approval'] == 1) {
                    $_data = '<span class="label label-tag label-tab1"><span class="tag">' . _l('approved') . '</span></span>';
                } elseif ($aRow['approval'] == 0) {
                    $_data = '<span class="label label-tag label-tab2"><span class="tag">' . _l('not_yet_approve') . '</span></span>';
                } elseif ($aRow['approval'] == -1) {
                    $_data = '<span class="label label-tag label-tab3"><span class="tag">' . _l('reject') . '</span></span>';
                }
                break;

            case 'delivery_status':
                $_data = render_delivery_status_html($aRow['id'], 'delivery', $aRow['delivery_status']);
                break;

            case 'pr_order_id':
                $_data = '';
                if (get_status_modules_wh('purchase') && !empty($aRow['pr_order_id'])) {
                    $_data = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pr_order_id']) . '">' . get_pur_order_name($aRow['pr_order_id']) . '</a>';
                }
                break;
            case 'id as pdf':
                $pdf = '<div class="btn-group display-flex" >';
                $pdf .= '<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><span class="caret"></span></a>';
                $pdf .= '<ul class="dropdown-menu dropdown-menu-right">';
                $pdf .= '<li class="hidden-xs"><a href="' . admin_url('warehouse/stock_export_pdf/' . $aRow['id'] . '?output_type=I') . '">' . _l('view_pdf') . '</a></li>';
                $pdf .= '<li class="hidden-xs"><a href="' . admin_url('warehouse/stock_export_pdf/' . $aRow['id'] . '?output_type=I') . '" target="_blank">' . _l('view_pdf_in_new_window') . '</a></li>';
                $pdf .= '<li><a href="' . admin_url('warehouse/stock_export_pdf/' . $aRow['id']) . '">' . _l('download') . '</a></li>';
                $pdf .= '<li><a href="' . admin_url('warehouse/stock_export_pdf/' . $aRow['id'] . '?print=true') . '" target="_blank">' . _l('print') . '</a></li>';
                $pdf .= '</ul>';


                // Add the View/Edit button
                if (has_permission("warehouse", "", "edit")) {
                    $pdf .= '<a href="' . admin_url("warehouse/edit_delivery/" . $aRow['id']) . '" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="' . _l("view") . '" data-placement="bottom"><i class="fa fa-eye"></i></a>';
                }

                $pdf .= '</div>';

                $_data .= $pdf;
                break;
        }

        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
