<?php

defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'purchase_request';

$group_pur_filter_name = 'group_pur';
$sub_groups_pur_filter_name = 'sub_groups_pur';
$project_filter_name = 'project';
$department_filter_name = 'department';
$requester_filter_name = 'requester';
$status_filter_name = 'status';
$from_date_filter_name = 'from_date';
$to_date_filter_name = 'to_date';
$aColumns = [
    'pur_rq_code',
    'pur_rq_name',
    db_prefix() . 'departments.name as department_name',
    db_prefix() . 'items_groups.name',
    'sub_group_name',
    // 'area_name',
    'requester',
    // 'department', 
    'request_date',
    'project',
    'status',
    // 'project',
    db_prefix() . 'pur_request' . '.id',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'pur_request';
$join         = [
    'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'pur_request.department',
    'LEFT JOIN ' . db_prefix() . 'items_groups ON ' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'pur_request.group_pur',
    'LEFT JOIN ' . db_prefix() . 'wh_sub_group ON ' . db_prefix() . 'wh_sub_group.id = ' . db_prefix() . 'pur_request.sub_groups_pur',
    // 'LEFT JOIN '.db_prefix().'area ON '.db_prefix().'area.id = '.db_prefix().'pur_request.area_pur',
];
$where = [];

$having = '';
if (!is_admin()) {
    $having = "FIND_IN_SET('" . get_staff_user_id() . "', member_list) != 0";
}

if ($this->ci->input->post('from_date') && $this->ci->input->post('from_date') != '') {
    $from_date = date('Y-m-d', strtotime($this->ci->input->post('from_date')));
    array_push($where, 'AND request_date >= "' . $from_date . '"');
}

if ($this->ci->input->post('to_date') && $this->ci->input->post('to_date') != '') {
    $to_date = date('Y-m-d', strtotime($this->ci->input->post('to_date')));
    array_push($where, 'AND request_date <= "' . $to_date . '"');
}

if (
    $this->ci->input->post('department')
    && count($this->ci->input->post('department')) > 0
) {
    array_push($where, 'AND department IN (' . implode(',', $this->ci->input->post('department')) . ')');
}

if (
    $this->ci->input->post('project')
    && count($this->ci->input->post('project')) > 0
) {
    array_push($where, 'AND project IN (' . implode(',', $this->ci->input->post('project')) . ')');
}

if (isset($project)) {
    array_push($where, ' AND ' . db_prefix() . 'pur_request.project = ' . $project);
}

if (
    $this->ci->input->post('requester')
    && count($this->ci->input->post('requester')) > 0
) {
    array_push($where, 'AND requester IN (' . implode(',', $this->ci->input->post('requester')) . ')');
}

if (
    $this->ci->input->post('status')
    && count($this->ci->input->post('status')) > 0
) {
    array_push($where, 'AND status IN (' . implode(',', $this->ci->input->post('status')) . ')');
}

if (
    $this->ci->input->post('group_pur')
    && count($this->ci->input->post('group_pur')) > 0
) {
    array_push($where, 'AND group_pur IN (' . implode(',', $this->ci->input->post('group_pur')) . ')');
}

if (
    $this->ci->input->post('sub_groups_pur')
    && count($this->ci->input->post('sub_groups_pur')) > 0
) {
    array_push($where, 'AND sub_groups_pur IN (' . implode(',', $this->ci->input->post('sub_groups_pur')) . ')');
}





if (!has_permission('purchase_request', '', 'view')) {
    $or_where = '';
    $list_vendor = get_vendor_admin_list(get_staff_user_id());
    foreach ($list_vendor as $vendor_id) {
        $or_where .= ' OR find_in_set(' . $vendor_id . ', ' . db_prefix() . 'pur_request.send_to_vendors)';
    }

    array_push($where, 'AND (' . db_prefix() . 'pur_request.requester = ' . get_staff_user_id() .  $or_where . ' OR ' . get_staff_user_id() . ' IN (SELECT staffid FROM ' . db_prefix() . 'pur_approval_details WHERE ' . db_prefix() . 'pur_approval_details.rel_type = "pur_request" AND ' . db_prefix() . 'pur_approval_details.rel_id = ' . db_prefix() . 'pur_request.id))');
}
$from_date_filter_name_value = !empty($this->ci->input->post('from_date')) ? $this->ci->input->post('from_date') : NULL;
update_module_filter($module_name, $from_date_filter_name, $from_date_filter_name_value);

$to_date_filter_name_value = !empty($this->ci->input->post('to_date')) ? $this->ci->input->post('to_date') : NULL;
update_module_filter($module_name, $to_date_filter_name, $to_date_filter_name_value);

$status_filter_name_value = !empty($this->ci->input->post('status')) ? implode(',', $this->ci->input->post('status')) : NULL;
update_module_filter($module_name, $status_filter_name, $status_filter_name_value);

$group_pur_filter_name_value = !empty($this->ci->input->post('group_pur')) ? implode(',', $this->ci->input->post('group_pur')) : NULL;
update_module_filter($module_name, $group_pur_filter_name, $group_pur_filter_name_value);

$sub_groups_pur_filter_name_value = !empty($this->ci->input->post('sub_groups_pur')) ? implode(',', $this->ci->input->post('sub_groups_pur')) : NULL;
update_module_filter($module_name, $sub_groups_pur_filter_name, $sub_groups_pur_filter_name_value);

$project_filter_name_value = !empty($this->ci->input->post('project')) ? implode(',', $this->ci->input->post('project')) : NULL;
update_module_filter($module_name, $project_filter_name, $project_filter_name_value);

$department_filter_name_value = !empty($this->ci->input->post('department')) ? implode(',', $this->ci->input->post('department')) : NULL;
update_module_filter($module_name, $department_filter_name, $department_filter_name_value);

$requester_filter_name_value = !empty($this->ci->input->post('requester')) ? implode(',', $this->ci->input->post('requester')) : NULL;
update_module_filter($module_name, $requester_filter_name, $requester_filter_name_value);

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'pur_request' . '.id',
    'tbldepartments.name',
    'pur_rq_code',
    '(SELECT GROUP_CONCAT(' . db_prefix() . 'project_members.staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE ' . db_prefix() . 'project_members.project_id=' . db_prefix() . 'pur_request.project) as member_list',
], '', [], $having);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'request_date') {
            $_data = _dt($aRow['request_date']);
        } elseif ($aColumns[$i] == 'requester') {
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['requester']) . '">' . staff_profile_image($aRow['requester'], [
                'staff-profile-image-small',
            ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['requester']) . '">' . get_staff_full_name($aRow['requester']) . '</a>';
        } elseif ($aColumns[$i] == db_prefix() . 'departments.name as department_name') {
            $_data = $aRow['department_name'];
        } elseif ($aColumns[$i] == 'status') {

            $approve_status = get_status_approve($aRow['status']);

            if ($aRow['status'] == 1) {
                $approve_status = '<span class="label label-primary" id="status_span_' . $aRow['id'] . '"> ' . _l('purchase_draft');
            } elseif ($aRow['status'] == 2) {
                $approve_status = '<span class="label label-success" id="status_span_' . $aRow['id'] . '"> ' . _l('purchase_approved');
            } elseif ($aRow['status'] == 3) {
                $approve_status = '<span class="label label-warning" id="status_span_' . $aRow['id'] . '"> ' . _l('pur_rejected');
            } elseif ($aRow['status'] == 4) {
                $approve_status = '<span class="label label-default" id="status_span_' . $aRow['id'] . '"> ' . _l('pur_closed');
            }

            if (false) {
                $approve_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $approve_status .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $approve_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                $approve_status .= '</a>';

                $approve_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['id'] . '">';

                if ($aRow['status'] == 1) {

                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('purchase_approved') . '
                              </a>
                           </li>';
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('pur_rejected') . '
                              </a>
                           </li>';
                } else if ($aRow['status'] == 2) {
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('purchase_draft') . '
                              </a>
                           </li>';

                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('pur_rejected') . '
                              </a>
                           </li>';
                } else if ($aRow['status'] == 3) {

                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('purchase_draft') . '
                              </a>
                           </li>';
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' . _l('purchase_approved') . '
                              </a>
                           </li>';
                }

                $approve_status .= '</ul>';
                $approve_status .= '</div>';
            }

            $approve_status .= '</span>';

            $_data = $approve_status;
        } elseif ($aColumns[$i] == 'pur_rq_name') {
            $name = '<a href="' . admin_url('purchase/view_pur_request/' . $aRow['id']) . '">' . $aRow['pur_rq_name'] . '</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="' . admin_url('purchase/view_pur_request/' . $aRow['id']) . '" >' . _l('view') . '</a>';

            if ((has_permission('purchase_request', '', 'edit') || is_admin()) &&  $aRow['status'] != 2) {
                $name .= ' | <a href="' . admin_url('purchase/pur_request/' . $aRow['id']) . '" >' . _l('edit') . '</a>';
            }

            if (has_permission('purchase_request', '', 'delete') || is_admin()) {
                $name .= ' | <a href="' . admin_url('purchase/delete_pur_request/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }

            $name .= '</div>';

            $_data = $name;
        } elseif ($aColumns[$i] == db_prefix() . 'pur_request' . '.id') {
            if ($aRow['status'] == 2) {
                $_data = '<div class="btn-group mright5" data-toggle="tooltip" title="' . _l('request_quotation_tooltip') . '">
                           <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="fa fa-file-pdf"></i><span class="caret"></span></a>
                           <ul class="dropdown-menu dropdown-menu-right">
                              <li class="hidden-xs"><a href="' . admin_url('purchase/request_quotation_pdf/' . $aRow['id'] . '?output_type=I') . '">' . _l('view_pdf') . '</a></li>
                              <li class="hidden-xs"><a href="' . admin_url('purchase/request_quotation_pdf/' . $aRow['id'] . '?output_type=I') . '" target="_blank">' . _l('view_pdf_in_new_window') . '</a></li>
                              <li><a href="' . admin_url('purchase/request_quotation_pdf/' . $aRow['id']) . '">' . _l('download') . '</a></li>
                           </ul>
                           </div>';

                $_data .= '<a href="#" onclick="send_request_quotation(' . $aRow['id'] . '); return false;" class="btn btn-success" ><i class="fa fa-envelope" data-toggle="tooltip" title="' . _l('request_quotation') . '"></i></a>';

                $_data .= '<a href="#" onclick="share_request(' . $aRow['id'] . '); return false;" class="btn btn-primary mleft5" ><i class="fa fa-share-alt" data-toggle="tooltip" title="' . _l('share_request') . '"></i></a>';
            } else {
                $_data = '';
            }
        } elseif ($aColumns[$i] == 'pur_rq_code') {
            $_data = '<a href="' . admin_url('purchase/view_pur_request/' . $aRow['id']) . '">' . $aRow['pur_rq_code'] . '</a>';
        } elseif ($aColumns[$i] == 'project') {

            // $_data = get_po_html_by_pur_request($aRow['id']);
            $_data = get_project_name_by_id($aRow['project']);
        }


        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
