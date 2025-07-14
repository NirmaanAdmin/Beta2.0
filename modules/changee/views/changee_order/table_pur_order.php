<?php

defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'changee_order';

$from_date_filter_name = 'from_date';
$to_date_filter_name = 'to_date';
$changee_request_filter_name = 'changee_request';
$vendor_filter_name = 'vendor';
$status_filter_name = 'status';
$type_filter_name = 'type';
$project_filter_name = 'project';
$department_filter_name = 'department';
$delivery_status_filter_name = 'delivery_status';

$custom_fields = get_custom_fields('pur_order', [
    'show_on_table' => 1,
]);

$aColumns = [
    'pur_order_number',
    'vendor',
    'order_date',
    'group_name',
    'sub_group_name',
    'area_name',
    'type',
    db_prefix() . 'projects.name as project_name',
    'department',
    'pur_order_name',
    'approve_status',
    'subtotal',
    'total_tax',
    'total',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'co_orders.id and rel_type="pur_order" ORDER by tag_order ASC) as tags',
    
    // 'delivery_date',
    // 'delivery_status',
    'number',
    'expense_convert',
];

if (isset($vendor) || isset($project)) {
    $aColumns = [
        'pur_order_number',
        'total',
        'total_tax',
        'vendor',
        'order_date',
        'number',
        'approve_status',

    ];
}

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'co_orders';
$join         = [
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'co_orders.vendor',
    'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'co_orders.department',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'co_orders.project',
    'LEFT JOIN ' . db_prefix() . 'assets_group ON ' . db_prefix() . 'assets_group.group_id = ' . db_prefix() . 'co_orders.group_pur',
    'LEFT JOIN ' . db_prefix() . 'wh_sub_group ON ' . db_prefix() . 'wh_sub_group.id = ' . db_prefix() . 'co_orders.sub_groups_pur',
    'LEFT JOIN ' . db_prefix() . 'area ON ' . db_prefix() . 'area.id = ' . db_prefix() . 'co_orders.area_pur',
];
$i = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_' . $i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_' . $i;
    }
    array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $i . ' ON ' . db_prefix() . 'co_orders.id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}

$where = [];

if (isset($vendor)) {
    array_push($where, ' AND ' . db_prefix() . 'co_orders.vendor = ' . $vendor);
}

if (isset($project)) {
    array_push($where, ' AND ' . db_prefix() . 'co_orders.project = ' . $project);
}

if (
    $this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != ''
) {

    $from_date = date('Y-m-d', strtotime($this->ci->input->post('from_date')));
    array_push($where, 'AND order_date >= "' . $from_date . '"');
}

if (
    $this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != ''
) {
    $to_date = date('Y-m-d', strtotime($this->ci->input->post('to_date')));
    array_push($where, 'AND order_date <= "' .$to_date . '"');
}


if ($this->ci->input->post('status') && count($this->ci->input->post('status')) > 0) {
    array_push($where, 'AND approve_status IN (' . implode(',', $this->ci->input->post('status')) . ')');
}

if (
    $this->ci->input->post('vendor')
    && count($this->ci->input->post('vendor')) > 0
) {
    array_push($where, 'AND vendor IN (' . implode(',', $this->ci->input->post('vendor')) . ')');
}

if (
    $this->ci->input->post('project')
    && count($this->ci->input->post('project')) > 0
) {
    array_push($where, 'AND ' . db_prefix() . 'co_orders.project IN (' . implode(',', $this->ci->input->post('project')) . ')');
}

if (
    $this->ci->input->post('department')
    && count($this->ci->input->post('department')) > 0
) {
    array_push($where, 'AND department IN (' . implode(',', $this->ci->input->post('department')) . ')');
}

if (
    $this->ci->input->post('delivery_status')
    && count($this->ci->input->post('delivery_status')) > 0
) {
    array_push($where, 'AND delivery_status IN (' . implode(',', $this->ci->input->post('delivery_status')) . ')');
}

if (
    $this->ci->input->post('changee_request')
    && count($this->ci->input->post('changee_request')) > 0
) {
    array_push($where, 'AND co_request IN (' . implode(',', $this->ci->input->post('changee_request')) . ')');
}

if (!has_permission('changee_orders', '', 'view')) {
    array_push($where, 'AND (' . db_prefix() . 'co_orders.addedfrom = ' . get_staff_user_id() . ' OR ' . db_prefix() . 'co_orders.buyer = ' . get_staff_user_id() . ' OR ' . db_prefix() . 'co_orders.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ') OR ' . get_staff_user_id() . ' IN (SELECT staffid FROM ' . db_prefix() . 'co_approval_details WHERE ' . db_prefix() . 'co_approval_details.rel_type = "pur_order" AND ' . db_prefix() . 'co_approval_details.rel_id = ' . db_prefix() . 'co_orders.id))');
}

$type = $this->ci->input->post('type');
if (isset($type)) {
    $where_type = '';
    foreach ($type as $t) {
        if ($t != '') {
            if ($where_type == '') {
                $where_type .= ' AND (tblco_orders.type = "' . $t . '"';
            } else {
                $where_type .= ' or tblco_orders.type = "' . $t . '"';
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
                $where_tags_ft .= ' AND (tblco_orders.id = "' . $commodity_id . '"';
            } else {
                $where_tags_ft .= ' or tblco_orders.id = "' . $commodity_id . '"';
            }
        }
    }
    if ($where_tags_ft != '') {
        $where_tags_ft .= ')';
        array_push($where, $where_tags_ft);
    }
}

$having = '';
if (!is_admin()) {
    $having = "FIND_IN_SET('" . get_staff_user_id() . "', member_list) != 0";
}


$from_date_filter_name_value = !empty($this->ci->input->post('from_date')) ? $this->ci->input->post('from_date') : NULL;
update_module_filter($module_name, $from_date_filter_name, $from_date_filter_name_value);

$to_date_filter_name_value = !empty($this->ci->input->post('to_date')) ? $this->ci->input->post('to_date') : NULL;
update_module_filter($module_name, $to_date_filter_name, $to_date_filter_name_value);

$changee_request_filter_name_value = !empty($this->ci->input->post('changee_request')) ? implode(',', $this->ci->input->post('changee_request')) : NULL;
update_module_filter($module_name, $changee_request_filter_name, $changee_request_filter_name_value);

$delivery_status_filter_name_value = !empty($this->ci->input->post('delivery_status')) ? implode(',', $this->ci->input->post('delivery_status')) : NULL;
update_module_filter($module_name, $delivery_status_filter_name, $delivery_status_filter_name_value);

$vendor_filter_name_value = !empty($this->ci->input->post('vendor')) ?  implode(',',$this->ci->input->post('vendor')) : NULL;
update_module_filter($module_name, $vendor_filter_name, $vendor_filter_name_value);

$type_filter_name_value = !empty($this->ci->input->post('type')) ? implode(',', $this->ci->input->post('type')) : NULL;
update_module_filter($module_name, $type_filter_name, $type_filter_name_value);

$project_filter_name_value = !empty($this->ci->input->post('project')) ? implode(',', $this->ci->input->post('project')) : NULL;
update_module_filter($module_name, $project_filter_name, $project_filter_name_value);

$status_filter_name_value = !empty($this->ci->input->post('status')) ? implode(',', $this->ci->input->post('status')) : NULL;
update_module_filter($module_name, $status_filter_name, $status_filter_name_value);

$department_filter_name_value = !empty($this->ci->input->post('department')) ? implode(',', $this->ci->input->post('department')) : NULL;
update_module_filter($module_name, $department_filter_name, $department_filter_name_value);




$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'co_orders.id as id', 'company', 'pur_order_number', 'expense_convert', db_prefix() . 'projects.name as project_name', db_prefix() . 'departments.name as department_name', 'currency', '(SELECT GROUP_CONCAT(' . db_prefix() . 'project_members.staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE ' . db_prefix() . 'project_members.project_id=' . db_prefix() . 'co_orders.project) as member_list'], '', [], $having);

$output  = $result['output'];
$rResult = $result['rResult'];

$footer_data = [
    'total_co_value' => 0,
    'total_tax_value' => 0,
    'total_co_value_included_tax' => 0,
];

$this->ci->load->model('changee/changee_model');

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        $base_currency = changee_get_base_currency_pur();
        if ($aRow['currency'] != 0) {
            $base_currency = changee_pur_get_currency_by_id($aRow['currency']);
        }

        if ($aColumns[$i] == 'total') {
            $_data = app_format_money($aRow['total'], $base_currency->symbol);
        } elseif ($aColumns[$i] == 'pur_order_number') {

            $numberOutput = '';

            $numberOutput = '<a href="' . admin_url('changee/changee_order/' . $aRow['id']) . '"  onclick="init_pur_order(' . $aRow['id'] . '); small_table_full_view(); return false;" >' . $aRow['pur_order_number'] . '</a>';

            $numberOutput .= '<div class="row-options">';

            if (has_permission('changee_orders', '', 'view') || has_permission('changee_orders', '', 'view_own')) {
                $numberOutput .= ' <a href="' . admin_url('changee/changee_order/' . $aRow['id']) . '" onclick="init_pur_order(' . $aRow['id'] . '); small_table_full_view(); return false;" >' . _l('view') . '</a>';
            }
            if ((has_permission('changee_orders', '', 'edit') || is_admin()) && $aRow['approve_status'] != 2) {
                $numberOutput .= ' | <a href="' . admin_url('changee/pur_order/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('changee_orders', '', 'delete') || is_admin()) {
                $numberOutput .= ' | <a href="' . admin_url('changee/delete_pur_order/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $numberOutput .= '</div>';

            $_data = $numberOutput;
        } elseif ($aColumns[$i] == 'vendor') {
            $_data = '<a href="' . admin_url('changee/vendor/' . $aRow['vendor']) . '" >' .  $aRow['company'] . '</a>';
        } elseif ($aColumns[$i] == 'order_date') {
            $_data = _d($aRow['order_date']);
        } elseif ($aColumns[$i] == 'approve_status') {
            $_data = changee_get_status_approve($aRow['approve_status']);
        } elseif ($aColumns[$i] == 'total_tax') {
            $tax = $this->ci->changee_model->get_html_tax_pur_order($aRow['id']);
            $total_tax = 0;
            foreach ($tax['taxes_val'] as $tax_val) {
                $total_tax += $tax_val;
            }

            $_data = app_format_money($total_tax, $base_currency->symbol);
        } elseif ($aColumns[$i] == 'expense_convert') {
            if ($aRow['expense_convert'] == 0) {
                $_data = '<a href="javascript:void(0)" onclick="convert_expense(' . $aRow['id'] . ',' . $aRow['total'] . '); return false;" class="btn btn-warning btn-icon">' . _l('convert') . '</a>';
            } else {
                $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['expense_convert']) . '" class="btn btn-success btn-icon">' . _l('view_expense') . '</a>';
            }
        } elseif ($aColumns[$i] == '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'co_orders.id and rel_type="pur_order" ORDER by tag_order ASC) as tags') {

            $_data = render_tags($aRow['tags']);
        } elseif ($aColumns[$i] == 'type') {
            $_data = _l($aRow['type']);
        } elseif ($aColumns[$i] == 'subtotal') {
            $_data = app_format_money($aRow['subtotal'], $base_currency->symbol);
        } elseif ($aColumns[$i] == db_prefix() . 'projects.name as project_name') {
            $_data = $aRow['project_name'];
        } elseif ($aColumns[$i] == 'department') {
            $_data = $aRow['department_name'];
        } elseif ($aColumns[$i] == 'delivery_status') {
            $delivery_status = '';

            if ($aRow['delivery_status'] == 0) {
                $delivery_status = '<span class="inline-block label label-danger" id="status_span_' . $aRow['id'] . '" task-status-table="undelivered">' . _l('undelivered');
            } else if ($aRow['delivery_status'] == 1) {
                $delivery_status = '<span class="inline-block label label-success" id="status_span_' . $aRow['id'] . '" task-status-table="completely_delivered">' . _l('completely_delivered');
            } else if ($aRow['delivery_status'] == 2) {
                $delivery_status = '<span class="inline-block label label-info" id="status_span_' . $aRow['id'] . '" task-status-table="pending_delivered">' . _l('pending_delivered');
            } else if ($aRow['delivery_status'] == 3) {
                $delivery_status = '<span class="inline-block label label-warning" id="status_span_' . $aRow['id'] . '" task-status-table="partially_delivered">' . _l('partially_delivered');
            }

            if (has_permission('changee_orders', '', 'edit') || is_admin()) {
                $delivery_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $delivery_status .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $delivery_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                $delivery_status .= '</a>';

                $delivery_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['id'] . '">';

                if ($aRow['delivery_status'] == 0) {
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('completely_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('pending_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('partially_delivered') . '
                              </a>
                           </li>';
                } else if ($aRow['delivery_status'] == 1) {
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 0 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('undelivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('pending_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('partially_delivered') . '
                              </a>
                           </li>';
                } else if ($aRow['delivery_status'] == 2) {
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 0 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('undelivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('completely_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('partially_delivered') . '
                              </a>
                           </li>';
                } else if ($aRow['delivery_status'] == 3) {
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 0 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('undelivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('completely_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('pending_delivered') . '
                              </a>
                           </li>';
                }

                $delivery_status .= '</ul>';
                $delivery_status .= '</div>';
            }
            $delivery_status .= '</span>';
            $_data = $delivery_status;
        } elseif ($aColumns[$i] == 'delivery_date') {
            $_data = _d($aRow['delivery_date']);
        } else if ($aColumns[$i] == 'number') {
            $paid = $aRow['total'] - changee_purorder_inv_left_to_pay($aRow['id']);

            $percent = 0;

            if ($aRow['total'] > 0) {

                $percent = ($paid / $aRow['total']) * 100;
            }



            $_data = '<div class="progress">

                          <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="' . round($percent) . '"

                          aria-valuemin="0" aria-valuemax="100" style="width:' . round($percent) . '%" data-percent="' . round($percent) . '">

                           ' . round($percent) . ' % 

                          </div>

                        </div>';
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;
    }

    $footer_data['total_co_value'] += $aRow['subtotal'];
    $footer_data['total_tax_value'] += $total_tax;
    $footer_data['total_co_value_included_tax'] += $aRow['total'];
    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = app_format_money($total, $base_currency->symbol);
}
$output['sums'] = $footer_data;
