<?php

defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'work_order';
$purchase_request_filter_name = 'purchase_request';
$status_filter_name = 'pur_approval_status';
$vendor_filter_name = 'pur_vendor_filter';
$group_pur_filter_name = 'group_pur';
$project_filter_name = 'project';
$department_filter_name = 'department';
$delivery_status_filter_name = 'delivery_status';
$from_date_filter_name = 'from_date';
$to_date_filter_name = 'to_date';

$custom_fields = get_custom_fields('pur_order', [
    'show_on_table' => 1,
    ]);

$aColumns = [
    'wo_order_number',
    'vendor',
    'wo_order_name',
    'order_date',
    'group_name',
//    'sub_group_name',
    // 'area_name',
    'type',
    'project',
    'department',
    'approve_status',
    // 'expense_convert',
    'subtotal',
    'total_tax',
    'total',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'wo_orders.id and rel_type="pur_order" ORDER by tag_order ASC) as tags',
    'number',
    ];

if(isset($vendor) || isset($project) || isset($purchase_dashboard)) {
    $aColumns = [
        'wo_order_number',
        'total',
        'total_tax',
        'vendor',
        'order_date',
        'approve_status',
    ];
}

$sIndexColumn = 'id';
$sTable       = db_prefix().'wo_orders';
$join         = [
                    'LEFT JOIN '.db_prefix().'pur_vendor ON '.db_prefix().'pur_vendor.userid = '.db_prefix().'wo_orders.vendor',
                    'LEFT JOIN '.db_prefix().'departments ON '.db_prefix().'departments.departmentid = '.db_prefix().'wo_orders.department',
                    'LEFT JOIN '.db_prefix().'projects ON '.db_prefix().'projects.id = '.db_prefix().'wo_orders.project',
                    'LEFT JOIN '.db_prefix().'assets_group ON '.db_prefix().'assets_group.group_id = '.db_prefix().'wo_orders.group_pur',
                    'LEFT JOIN '.db_prefix().'wh_sub_group ON '.db_prefix().'wh_sub_group.id = '.db_prefix().'wo_orders.sub_groups_pur',
                    // 'LEFT JOIN '.db_prefix().'area ON '.db_prefix().'area.id = '.db_prefix().'wo_orders.area_pur',
                ];
$i = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_' . $i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_' . $i;
    }
    array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
    array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $i . ' ON '.db_prefix().'wo_orders.id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}

$where = [];

if(isset($vendor)){
    array_push($where, ' AND '.db_prefix().'wo_orders.vendor = '.$vendor);
}

if(isset($project)){
    array_push($where, ' AND '.db_prefix().'wo_orders.project = '.$project);
}

if(isset($purchase_dashboard)){
    array_push($where, 'AND order_date >= "'.date('Y-m-d', strtotime('-1 month')).'"');
    array_push($where, 'AND order_date <= "'.date('Y-m-d').'"');
}

if ($this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != '') {
    array_push($where, 'AND order_date >= "'.date('Y-m-d', strtotime($this->ci->input->post('from_date'))).'"');
}

if ($this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != '') {
    array_push($where, 'AND order_date <= "'.date('Y-m-d', strtotime($this->ci->input->post('to_date'))).'"');
}


if ($this->ci->input->post('status') && count($this->ci->input->post('status')) > 0) {
    array_push($where, 'AND approve_status IN (' . implode(',', $this->ci->input->post('status')) . ')');
}

if ($this->ci->input->post('vendor')
    && count($this->ci->input->post('vendor')) > 0) {
    array_push($where, 'AND vendor IN (' . implode(',', $this->ci->input->post('vendor')) . ')');
}

if ($this->ci->input->post('group_pur')
    && count($this->ci->input->post('group_pur')) > 0) {
    array_push($where, 'AND group_pur IN (' . implode(',', $this->ci->input->post('group_pur')) . ')');
}

if ($this->ci->input->post('project')
    && count($this->ci->input->post('project')) > 0) {
    array_push($where, 'AND project IN (' . implode(',', $this->ci->input->post('project')) . ')');
}

if ($this->ci->input->post('department')
    && count($this->ci->input->post('department')) > 0) {
    array_push($where, 'AND department IN (' . implode(',', $this->ci->input->post('department')) . ')');
}

if ($this->ci->input->post('purchase_request')
    && count($this->ci->input->post('purchase_request')) > 0) {
    array_push($where, 'AND pur_request IN (' . implode(',', $this->ci->input->post('purchase_request')) . ')');
}

if(!has_permission('purchase_orders', '', 'view')){
   array_push($where, 'AND (' . db_prefix() . 'wo_orders.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'wo_orders.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'wo_orders.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ') OR '.get_staff_user_id().' IN (SELECT staffid FROM ' . db_prefix() . 'pur_approval_details WHERE ' . db_prefix() . 'pur_approval_details.rel_type = "pur_order" AND ' . db_prefix() . 'pur_approval_details.rel_id = '.db_prefix().'wo_orders.id))');
}

$type = $this->ci->input->post('type');
if (isset($type)) {
    $where_type = '';
    foreach ($type as $t) {
        if ($t != '') {
            if ($where_type == '') {
                $where_type .= ' AND (tblwo_orders.type = "' . $t . '"';
            } else {
                $where_type .= ' or tblwo_orders.type = "' . $t . '"';
            }
        }
    }
    if ($where_type != '') {
        $where_type .= ')';
        array_push($where, $where_type);
    }
}

//tags filter
$tags_ft = $this->ci->input->post('item_filter');
if (isset($tags_ft)) {
    $where_tags_ft = '';
    foreach ($tags_ft as $commodity_id) {
        if ($commodity_id != '') {
            if ($where_tags_ft == '') {
                $where_tags_ft .= ' AND (tblwo_orders.id = "' . $commodity_id . '"';
            } else {
                $where_tags_ft .= ' or tblwo_orders.id = "' . $commodity_id . '"';
            }
        }
    }
    if ($where_tags_ft != '') {
        $where_tags_ft .= ')';
        array_push($where, $where_tags_ft);
    }
}

$having = '';
if(!is_admin()) {
    $having = "FIND_IN_SET('".get_staff_user_id()."', member_list) != 0";
}

if(!isset($vendor) && !isset($project) && !isset($purchase_dashboard)) {
    $purchase_request_filter_name_value = !empty($this->ci->input->post('purchase_request')) ? implode(',', $this->ci->input->post('purchase_request')) : NULL;
    update_module_filter($module_name, $purchase_request_filter_name, $purchase_request_filter_name_value);

    $status_filter_name_value = !empty($this->ci->input->post('status')) ? implode(',', $this->ci->input->post('status')) : NULL;
    update_module_filter($module_name, $status_filter_name, $status_filter_name_value);

    $vendor_filter_name_value = !empty($this->ci->input->post('vendor')) ? implode(',', $this->ci->input->post('vendor')) : NULL;
    update_module_filter($module_name, $vendor_filter_name, $vendor_filter_name_value);

    $group_pur_filter_name_value = !empty($this->ci->input->post('group_pur')) ? implode(',', $this->ci->input->post('group_pur')) : NULL;
    update_module_filter($module_name, $group_pur_filter_name, $group_pur_filter_name_value);

    $project_filter_name_value = !empty($this->ci->input->post('project')) ? implode(',', $this->ci->input->post('project')) : NULL;
    update_module_filter($module_name, $project_filter_name, $project_filter_name_value);

    $department_filter_name_value = !empty($this->ci->input->post('department')) ? implode(',', $this->ci->input->post('department')) : NULL;
    update_module_filter($module_name, $department_filter_name, $department_filter_name_value);

    $delivery_status_filter_name_value = !empty($this->ci->input->post('delivery_status')) ? implode(',', $this->ci->input->post('delivery_status')) : NULL;
    update_module_filter($module_name, $delivery_status_filter_name, $delivery_status_filter_name_value);

    $from_date_filter_name_value = !empty($this->ci->input->post('from_date')) ? $this->ci->input->post('from_date') : NULL;
    update_module_filter($module_name, $from_date_filter_name, $from_date_filter_name_value);

    $to_date_filter_name_value = !empty($this->ci->input->post('to_date')) ? $this->ci->input->post('to_date') : NULL;
    update_module_filter($module_name, $to_date_filter_name, $to_date_filter_name_value);
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'wo_orders.id as id','company','wo_order_number','expense_convert',db_prefix().'projects.name as project_name',db_prefix().'departments.name as department_name', 'currency', '(SELECT GROUP_CONCAT(' . db_prefix() . 'project_members.staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE ' . db_prefix() . 'project_members.project_id=' . db_prefix() . 'wo_orders.project) as member_list'], '', [], $having);

$output  = $result['output'];
$rResult = $result['rResult'];
// echo '<pre>';
// print_r($rResult);
// die;

$footer_data = [
    'total_wo_value' => 0,
    'total_tax_value' => 0,
    'total_wo_value_included_tax' => 0,
];

$this->ci->load->model('purchase/purchase_model');
$sr = 1;
foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        $base_currency = get_base_currency_pur();
        if($aRow['currency'] != 0){
            $base_currency = pur_get_currency_by_id($aRow['currency']);
        }

        if($aColumns[$i] == 'total'){
            // $_data = app_format_money($aRow['total'], $base_currency->symbol);
            $_data = 0;
            $wo_total = $aRow['total'];
            $wo_co_sum_values = get_wo_co_sum_values($aRow['id']);
            if(!empty($wo_co_sum_values)) {
                $wo_total = $wo_total + $wo_co_sum_values->co_value;
            }
            $_data = app_format_money($wo_total, $base_currency->symbol);
        }elseif($aColumns[$i] == 'wo_order_number'){

            $numberOutput = '';
            $numberOutput .= '<a href="' . admin_url('purchase/work_order/' . $aRow['id']) . '"  onclick="init_wo_order(' . $aRow['id'] . '); small_table_full_view(); return false;" >'.$aRow['wo_order_number']. '</a>';

            $numberOutput .= '<div class="row-options">';

            if (has_permission('work_orders', '', 'view') || has_permission('work_orders', '', 'view_own')) {
                $numberOutput .= ' <a href="' . admin_url('purchase/work_order/' . $aRow['id']) . '" onclick="init_wo_order(' . $aRow['id'] . '); small_table_full_view(); return false;" >' . _l('view') . '</a>';
            }
            if ((has_permission('work_orders', '', 'edit') || is_admin()) && $aRow['approve_status'] != 2 ) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/wo_order/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('work_orders', '', 'delete') || is_admin()) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/delete_wo_order/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $numberOutput .= '</div>';

            $_data = $numberOutput;

        }elseif($aColumns[$i] == 'vendor'){
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" >' .  $aRow['company'] . '</a>';
        }elseif ($aColumns[$i] == 'order_date') {
            $_data = _d($aRow['order_date']);
        }elseif($aColumns[$i] == 'approve_status'){
            $_data = get_status_approve($aRow['approve_status']);
        }elseif($aColumns[$i] == 'total_tax'){
          $tax = $this->ci->purchase_model->get_html_tax_wo_order($aRow['id']);
          $total_tax = 0;
          foreach($tax['taxes_val'] as $tax_val){
            $total_tax += $tax_val;
          }

          $_data = app_format_money($total_tax, $base_currency->symbol);
        }elseif($aColumns[$i] == 'expense_convert'){
            if($aRow['expense_convert'] == 0){
             $_data = '<a href="javascript:void(0)" onclick="convert_expense_wo('.$aRow['id'].','.$aRow['total'].'); return false;" class="btn btn-warning btn-icon">'._l('convert').'</a>';
            }else{
                $expense_convert_check = get_expense_data($aRow['expense_convert']);
                if(!empty($expense_convert_check)) {
                    $_data = '<a href="'.admin_url('expenses/list_expenses/'.$aRow['expense_convert']).'" class="btn btn-success btn-icon">'._l('view_expense').'</a>';
                } else {
                    $_data = '<a href="javascript:void(0)" onclick="convert_expense_wo('.$aRow['id'].','.$aRow['total'].'); return false;" class="btn btn-warning btn-icon">'._l('convert').'</a>';
                }
            }
        }elseif($aColumns[$i] == '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'wo_orders.id and rel_type="pur_order" ORDER by tag_order ASC) as tags'){

                $_data = render_tags($aRow['tags']);

        }elseif($aColumns[$i] == 'type'){
            $_data = _l($aRow['type']);
        }elseif($aColumns[$i] == 'subtotal'){
            // $_data = app_format_money($aRow['subtotal'],$base_currency->symbol);
            $_data = 0;
            $wo_subtotal = $aRow['subtotal'];
            $wo_co_sum_values = get_wo_co_sum_values($aRow['id']);
            if(!empty($wo_co_sum_values)) {
                $wo_subtotal = $wo_subtotal + $wo_co_sum_values->co_value;
            }
            $_data = app_format_money($wo_subtotal, $base_currency->symbol);
        }elseif($aColumns[$i] == 'project'){
            $_data = $aRow['project_name'];
        }elseif($aColumns[$i] == 'department'){
            $_data = $aRow['department_name'];
        }else if($aColumns[$i] == 'number'){
            $paid = $aRow['total'] - purorder_inv_left_to_pay($aRow['id']);

            $percent = 0;

            if($aRow['total'] > 0){

                $percent = ($paid / $aRow['total'] ) * 100;

            }



            $_data = '<div class="progress">

                          <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="' .round($percent).'"

                          aria-valuemin="0" aria-valuemax="100" style="width:'.round($percent).'%" data-percent="' .round($percent).'">

                           ' .round($percent).' % 

                          </div>

                        </div>';

        }else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;
    }

    $footer_data['total_wo_value'] += $aRow['subtotal'];
    $footer_data['total_tax_value'] += $total_tax;
    $footer_data['total_wo_value_included_tax'] += $aRow['total'];
    $output['aaData'][] = $row;
    $sr++;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, $base_currency->symbol);
}
$output['sums'] = $footer_data;
