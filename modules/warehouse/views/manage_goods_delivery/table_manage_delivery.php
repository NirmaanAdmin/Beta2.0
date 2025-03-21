<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'goods_delivery_code',
    'pr_order_id',
    'date_add',
    // 'invoice_id',
    // 'staff_id',
    'approval',
    'delivery_status',
    'id as pdf'
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'goods_delivery';
$join         = [ ];

$where = [];

if($this->ci->input->post('day_vouchers')){
    $day_vouchers = to_sql_date($this->ci->input->post('day_vouchers'));
}

if (isset($day_vouchers)) {

    $where[] = ' AND tblgoods_delivery.date_add <= "' . $day_vouchers . '"';

}



if($this->ci->input->post('invoice_id')){
    $invoice_id = $this->ci->input->post('invoice_id');

    $where_invoice_id = '';

    $where_invoice_id .= ' where invoice_id = "'.$invoice_id. '"';

    array_push($where, $where_invoice_id);


}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id','date_add','date_c','goods_delivery_code','total_money', 'type_of_delivery']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
   for ($i = 0; $i < count($aColumns); $i++) {
    $CI           = & get_instance();

        $_data = $aRow[$aColumns[$i]];

        if($aColumns[$i] == 'customer_code'){
            $_data = '';
            if($aRow['customer_code']){
                $CI->db->where(db_prefix() . 'clients.userid', $aRow['customer_code']);
                $client = $CI->db->get(db_prefix() . 'clients')->row();
                if($client){
                    $_data = $client->company;
                }

            }


        }elseif($aColumns[$i] == 'invoice_id'){
            $_data = '';
            if($aRow['invoice_id']){

                $type_of_delivery='';
                if($aRow['type_of_delivery'] == 'partial'){
                    $type_of_delivery .= '( <span class="text-danger">'._l($aRow['type_of_delivery']).'</span> )';
                }elseif($aRow['type_of_delivery'] == 'total'){
                    $type_of_delivery .= '( <span class="text-success">'._l($aRow['type_of_delivery']).'</span> )';
                }

               $_data = format_invoice_number($aRow['invoice_id']).get_invoice_company_projecy($aRow['invoice_id']).$type_of_delivery;

            }


        }elseif($aColumns[$i] == 'date_add'){

            $_data = _d($aRow['date_add']);

        }elseif($aColumns[$i] == 'staff_id'){
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['staff_id']) . '">' . staff_profile_image($aRow['staff_id'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['staff_id']) . '">' . get_staff_full_name($aRow['staff_id']) . '</a>';
        }elseif($aColumns[$i] == 'department'){
            $_data = $aRow['name'];
        }elseif($aColumns[$i] == 'goods_delivery_code'){
            $name = '<a href="' . admin_url('warehouse/view_delivery/' . $aRow['id'] ).'" onclick="init_goods_delivery('.$aRow['id'].'); return false;">' . $aRow['goods_delivery_code'] . '</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="' . admin_url('warehouse/edit_delivery/' . $aRow['id'] ).'" >' . _l('view') . '</a>';

            if((has_permission('warehouse', '', 'edit') || is_admin()) && ($aRow['approval'] == 0)){
                $name .= ' | <a href="' . admin_url('warehouse/goods_delivery/' . $aRow['id'] ).'" >' . _l('edit') . '</a>';
            }

            if((is_admin()) && ($aRow['approval'] == 1)){
                $name .= ' | <a href="' . admin_url('warehouse/goods_delivery/' . $aRow['id'] ).'/true" >' . _l('edit') . '</a>';
            }


            if ((has_permission('warehouse', '', 'delete') || is_admin()) && ($aRow['approval'] == 0)) {
                $name .= ' | <a href="' . admin_url('warehouse/delete_goods_delivery/' . $aRow['id'] ).'" class="text-danger _delete" >' . _l('delete') . '</a>';
            }
            if(get_warehouse_option('revert_goods_receipt_goods_delivery') == 1 ){
                if ((has_permission('warehouse', '', 'delete') || is_admin()) && ($aRow['approval'] == 1)) {
                    $name .= ' | <a href="' . admin_url('warehouse/revert_goods_delivery/' . $aRow['id'] ).'" class="text-danger _delete" >' . _l('delete_after_approval') . '</a>';
                }
            }


            $name .= '</div>';

            $_data = $name;
        }elseif ($aColumns[$i] == 'custumer_name') {
            $_data =$aRow['custumer_name'];
        }elseif ($aColumns[$i] == 'to_') {
            $_data =    $aRow['to_'];
        }elseif($aColumns[$i] == 'address') {
            $_data = $aRow['address'];
        }elseif($aColumns[$i] == 'approval') {

             if($aRow['approval'] == 1){
                $_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
             }elseif($aRow['approval'] == 0){
                $_data = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
             }elseif($aRow['approval'] == -1){
                $_data = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
             } 
        }elseif($aColumns[$i] == 'delivery_status'){
            $_data = render_delivery_status_html($aRow['id'], 'delivery', $aRow['delivery_status']);
        }elseif ($aColumns[$i] == 'id as pdf') {
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
        }elseif ($aColumns[$i] == 'pr_order_id') {
            $get_pur_order_name = '';
            if (get_status_modules_wh('purchase')) {
                if (($aRow['pr_order_id'] != '') && ($aRow['pr_order_id'] != 0)) {
                    $get_pur_order_name .= '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pr_order_id']) . '" >' . get_pur_order_name($aRow['pr_order_id']) . '</a>';
                }
            }

            $_data = $get_pur_order_name;
        }



        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
