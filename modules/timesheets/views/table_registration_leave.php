<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('timesheets_model');
$user_id = get_staff_user_id();
$add_query = ' IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 8,"Leave",IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 2,"maternity_leave",IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 4,"private_work_without_pay",IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 1,"sick_leave", IF(' . db_prefix() . 'timesheets_requisition_leave.type_of_leave = 0,' . db_prefix() . 'timesheets_requisition_leave.type_of_leave_text,"")))))';

$type_of_leave = 'IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 1,' . $add_query . ', IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 2,"late", IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 3,"Go_out", IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 4,"Go_on_bussiness", IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 5,"quit_job", IF(' . db_prefix() . 'timesheets_requisition_leave.rel_type = 6,"early",""))))))';
$aColumns = [
	'1',
	db_prefix() . 'timesheets_requisition_leave.id',
	db_prefix() . 'timesheets_requisition_leave.id',
	db_prefix() . 'timesheets_requisition_leave.staff_id',
	db_prefix() . 'timesheets_requisition_leave.followers_id',
	db_prefix() . 'timesheets_requisition_leave.subject',
	db_prefix() . 'timesheets_requisition_leave.reason',
	db_prefix() . 'timesheets_requisition_leave.start_time',
	db_prefix() . 'timesheets_requisition_leave.end_time',
	'(SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM ' . db_prefix() . 'timesheets_approval_details WHERE rel_id = ' . db_prefix() . 'timesheets_requisition_leave.id and ' . db_prefix() . 'timesheets_approval_details.rel_type <> "additional_timesheets") as approver',
	db_prefix() . 'timesheets_requisition_leave.status',
	'2',
	db_prefix() . 'timesheets_requisition_leave.datecreated',
	db_prefix() . 'timesheets_requisition_leave.id',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'timesheets_requisition_leave';
$join = [
	'LEFT JOIN ' . db_prefix() . 'staff b ON b.staffid = ' . db_prefix() . 'timesheets_requisition_leave.staff_id',
	'LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = b.role',
];
$where = [];

if (!is_admin() && !has_permission('leave_management', '', 'view')) {
	array_push($where, ' AND ((' . get_staff_user_id() . ' in (select staffid from ' . db_prefix() . 'timesheets_approval_details where rel_type <> "additional_timesheets" and rel_id = ' . db_prefix() . 'timesheets_requisition_leave.id) or ' . db_prefix() . 'timesheets_requisition_leave.staff_id = ' . get_staff_user_id() . ')' . timesheet_staff_manager_query('leave_management', 'staff_id', 'OR') . ')');
}

if ($this->ci->input->post('status_filter')) {
	$where_status = '';
	$status = $this->ci->input->post('status_filter');
	foreach ($status as $statues) {
		if ($status != '') {
			if ($where_status == '') {
				$where_status .= ' AND (status = "' . $statues . '"';
			} else {
				$where_status .= ' OR status = "' . $statues . '"';
			}
		}
	}
	if ($where_status != '') {
		$where_status .= ')';
		array_push($where, $where_status);
	}
}

if ($this->ci->input->post('department_filter')) {
	$where_dpm = '';
	$department = $this->ci->input->post('department_filter');
	foreach ($department as $statues) {
		if ($department != '') {
			if ($where_dpm == '') {
				$where_dpm = ' AND (staff_id IN (SELECT staffid FROM ' . db_prefix() . 'staff_departments WHERE departmentid = ' . $statues . ')';
			} else {
				$where_dpm .= 'OR staff_id IN (SELECT staffid FROM ' . db_prefix() . 'staff_departments WHERE departmentid = ' . $statues . ')';
			}
		}
	}
	if ($where_dpm != '') {
		$where_dpm .= ')';
		array_push($where, $where_dpm);
	}
}

if ($this->ci->input->post('rel_type_filter')) {
	$where_rel_type = '';
	$rel_type = $this->ci->input->post('rel_type_filter');
	foreach ($rel_type as $statues) {
		if ($rel_type != '') {
			if ($where_rel_type == '') {
				$where_rel_type .= ' AND (rel_type = "' . $statues . '"';
			} else {
				$where_rel_type .= ' or rel_type = "' . $statues . '"';
			}
		}
	}
	if ($where_rel_type != '') {
		$where_rel_type .= ')';
		array_push($where, $where_rel_type);
	}
}

if ($this->ci->input->post('chose')) {
	$chose = $this->ci->input->post('chose');
	$sql_where = '';
	if ($chose != 'all') {
		if ($sql_where != '') {
			$sql_where .= ' AND (' . get_staff_user_id() . ' IN (SELECT staffid FROM ' . db_prefix() . 'timesheets_approval_details where ' . db_prefix() . 'timesheets_approval_details.rel_type IN ("Leave","maternity_leave","private_work_without_pay","sick_leave","late","early","Go_out","Go_on_bussiness") AND ' . db_prefix() . 'timesheets_approval_details.rel_id = ' . db_prefix() . 'timesheets_requisition_leave.id ))';
		} else {
			$sql_where .= '(' . get_staff_user_id() . ' IN (SELECT staffid FROM ' . db_prefix() . 'timesheets_approval_details where ' . db_prefix() . 'timesheets_approval_details.rel_type IN ("Leave","maternity_leave","private_work_without_pay","sick_leave","late","early","Go_out","Go_on_bussiness") AND ' . db_prefix() . 'timesheets_approval_details.rel_id = ' . db_prefix() . 'timesheets_requisition_leave.id ))';
		}
	} else {
		$sql_where = '';
	}
	if ($sql_where != '') {
		array_push($where, 'AND ' . $sql_where);
	}
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'timesheets_requisition_leave.rel_type', db_prefix() . 'timesheets_requisition_leave.type_of_leave', db_prefix() . 'timesheets_requisition_leave.subject', 'b.firstname', 'reason', db_prefix() . 'timesheets_requisition_leave.followers_id', db_prefix() . 'timesheets_requisition_leave.status as status', db_prefix() . 'timesheets_requisition_leave.datecreated', 'type_of_leave_text']);

$output = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
	$row = [];
	$row[] = $aRow[db_prefix() . 'timesheets_requisition_leave.id'];

	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow[db_prefix() . 'timesheets_requisition_leave.id'] . '"><label></label></div>';

	$row[] = '<div class="row">'
		. '<a class="col-md-10" href="' . admin_url('timesheets/requisition_detail' . '/' . ($aRow[db_prefix() . 'timesheets_requisition_leave.id'])) . '">' . $aRow['subject'] . '</a>'
		. '</div>';

	$row[] = '<a data-toggle="tooltip" data-title="' . get_staff_full_name($aRow[db_prefix() . 'timesheets_requisition_leave.staff_id']) . '" href="' . admin_url('profile/' . $aRow[db_prefix() . 'timesheets_requisition_leave.staff_id']) . '">' . staff_profile_image($aRow[db_prefix() . 'timesheets_requisition_leave.staff_id'], [
		'staff-profile-image-small',
	]) . ' ' . get_staff_full_name($aRow[db_prefix() . 'timesheets_requisition_leave.staff_id']) . '</a><span class="hide">' . get_staff_full_name($aRow[db_prefix() . 'timesheets_requisition_leave.staff_id']) . '</span>';

	$start_time = '';
	$end_time = '';
	if ($aRow['rel_type'] == 1) {
		$start_time = _d(date('Y-m-d', strtotime($aRow[db_prefix() . 'timesheets_requisition_leave.start_time'])));
		$end_time = _d(date('Y-m-d', strtotime($aRow[db_prefix() . 'timesheets_requisition_leave.end_time'])));
	} else {
		$start_time = _dt($aRow[db_prefix() . 'timesheets_requisition_leave.start_time']);
		$end_time = _dt($aRow[db_prefix() . 'timesheets_requisition_leave.end_time']);
	}


	$row[] = $start_time;
	$row[] = $end_time;

	$list_member_approve = [];

	$membersOutput = '';

	$members = ($aRow['approver'] ? explode(',', $aRow['approver']) : []);
	$list_member = '';
	$exportMembers =  $flag ='';
	foreach ($members as $key => $member_id) {
		if ($member_id != '') {
			$member_name = get_staff_full_name($member_id);
			$list_member .= '<li class="text-success mbot10 mtop"><a href="' . admin_url('profile/' . $member_id) . '" class="avatar cover-image text-align-left">' .
				staff_profile_image($member_id, [
					'staff-profile-image-small mright5',
				], 'small', [
					'data-toggle' => 'tooltip',
					'data-title' => $member_name,
				]) . ' ' . $member_name . '</a></li>';
			if ($key <= 2) {
				$membersOutput .= '<span class="avatar cover-image brround">' .
					staff_profile_image($member_id, [
						'staff-profile-image-small mright5',
					], 'small', [
						'data-toggle' => 'tooltip',
						'data-title' => $member_name,
					]) . '</span>';
			}
			// For exporting
			$exportMembers .= $member_name . ', ';
			$list_member_approve[] = $member_id;
		}
	}
	if (count($members) > 3) {
		$membersOutput .= '<span class="avatar bg-secondary brround avatar-none">+' . (count($members) - 3) . '</span>';
	}

	$membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';

	$membersOutput1 = '<div class="task-info task-watched task-info-watched">
    <h5>
    <div class="btn-group">
    <span class="task-single-menu task-menu-watched">
    <div class="avatar-list avatar-list-stacked" data-toggle="dropdown">' . $membersOutput . '</div>
    <ul class="dropdown-menu list-staff" role="menu">
    <li class="dropdown-plus-title">
    ' . _l('approver') . '
    </li>
    ' . $list_member . '
    </ul>
    </span>
    </div>
    </h5>
    </div>';

	$liss = '';
	$approce = '';

	$row[] = $membersOutput;
	if ($aRow[db_prefix() . 'timesheets_requisition_leave.followers_id'] != 0) {
		$row[] = '<a data-toggle="tooltip" data-title="' . get_staff_full_name($aRow[db_prefix() . 'timesheets_requisition_leave.followers_id']) . '" href="' . admin_url('profile/' . $aRow[db_prefix() . 'timesheets_requisition_leave.followers_id']) . '">' . staff_profile_image($aRow[db_prefix() . 'timesheets_requisition_leave.followers_id'], [
			'staff-profile-image-small',
		]) . ' ' . get_staff_full_name($aRow[db_prefix() . 'timesheets_requisition_leave.followers_id']) . '</a><span class="hide">' . get_staff_full_name($aRow[db_prefix() . 'timesheets_requisition_leave.followers_id']) . '</span>';
	} else {
		$row[] = '';
	}

	$row[] = $aRow['reason'];
	$rel_type = '';
	if ($aRow['rel_type'] == 1) {
		switch ($aRow['type_of_leave']) {
			case 8:
				$rel_type = 'Leave';
				break;
			case 2:
				$rel_type = 'maternity_leave';
				break;
			case 4:
				$rel_type = 'private_work_without_pay';
				break;
			case 1:
				$rel_type = 'sick_leave';
				break;
		}
	} else if ($aRow['rel_type'] == 2) {
		$rel_type = 'late';
	} else if ($aRow['rel_type'] == 3) {
		$rel_type = 'Go_out';
	} else if ($aRow['rel_type'] == 4) {
		$rel_type = 'Go_on_bussiness';
	} else if ($aRow['rel_type'] == 6) {
		$rel_type = 'early';
	} else {
		$rel_type = 'quit_job';
	}

	if ($rel_type != '') {
		$row[] = '<p>' . _l($rel_type) . '</p>';
	} else {
		$rel_type_name = $this->ci->timesheets_model->get_custom_leave_name_by_slug($aRow['type_of_leave_text']);
		$rel_type = $aRow['type_of_leave_text'];
		$row[] = '<p>' . $rel_type_name . '</p>';
	}

	if ($aRow['status'] == 0) {
		$row[] = '<span class="label label-primary  mr-1 mb-1 mt-1">' . _l('ts_new') . '</span>';
	} else if ($aRow['status'] == 1) {
		$row[] = '<span class="label label-success  mr-1 mb-1 mt-1">' . _l('approved') . '</span>';
	} else {
		$row[] = '<span class="label label-danger  mr-1 mb-1 mt-1">' . _l('Reject') . '</span>';
	}
	$staff_id = $aRow[db_prefix() . 'timesheets_requisition_leave.staff_id'];
	$row[] = '
    <button type="button" class="btn btn-default btn-icon" onclick="openStaffLeavesModal(' . $staff_id . ')" data-toggle="tooltip" data-title="' . _l('view_leaves') . '">
        <i class="fa fa-eye"></i>
    </button>
';
	$row[] = _d(date('Y-m-d', strtotime($aRow[db_prefix() . 'timesheets_requisition_leave.datecreated'])));
	$flag = 0;
	$action_option = '<div class="row">';
	if (in_array($user_id, $list_member_approve)) {
		$data_check_approve_status = $this->ci->timesheets_model->check_approval_details(($aRow[db_prefix() . 'timesheets_requisition_leave.id']), $rel_type);
		if (isset($data_check_approve_status['staffid'])) {
			if ($data_check_approve_status['staffid']) {
				if (in_array($user_id, $data_check_approve_status['staffid'])) {
					$action_option .= '<span data-placement="top" data-toggle="tooltip" data-title="' . _l('approve') . '" onclick="approve_request(' . ($aRow[db_prefix() . 'timesheets_requisition_leave.id']) . ',\'' . $rel_type . '\');" class="btn btn-success btn-icon mright5"><i class="fa fa-check"></i></span>';
					$action_option .= '<span data-placement="top" data-toggle="tooltip" data-title="' . _l('deny') . '" onclick="deny_request(' . ($aRow[db_prefix() . 'timesheets_requisition_leave.id']) . ',\'' . $rel_type . '\');" class="btn btn-primary btn-icon"><i class="fa fa-ban"></i></span>';
					$flag = 1;
				}
			}
		}
	}

	if (is_admin() || $aRow['status'] == 0) {
		$action_option .= '<a id="delete-insurance" data-placement="top" data-toggle="tooltip" data-title="' . _l('delete') . '" href="' . admin_url('timesheets/delete_requisition' . '/' . ($aRow[db_prefix() . 'timesheets_requisition_leave.id'])) . '" class="btn btn-danger btn-icon _delete">' . '<i class="fa fa-remove"></i>' . '</a>';
	}

	$get_deprtment = get_staff_department($user_id);
	$leaveBalance = check_emp_leave_balance($aRow[db_prefix() . 'timesheets_requisition_leave.staff_id']);
	$leaveBalance = $leaveBalance['remain'];
	if($flag == 1){
		$margin = 'margin-top: 10px;';
	}else{
		$margin = '';
	}
	if ((is_admin() || $get_deprtment == '8') && $leaveBalance > 0) {
		$action_option .= '<a style="' . $margin . '" id="edit-leave" data-placement="top" data-toggle="tooltip" data-title="Edit Leave" class="btn btn-info btn-icon" onclick="edit_leave(' . $aRow[db_prefix() . 'timesheets_requisition_leave.staff_id'] . ', \'' . date('Y-m-d', strtotime($aRow[db_prefix() . 'timesheets_requisition_leave.start_time'])) . '\', \'' . date('Y-m-d', strtotime($aRow[db_prefix() . 'timesheets_requisition_leave.end_time'])) . '\', ' . $aRow[db_prefix() . 'timesheets_requisition_leave.id'] . '); return false">' . '<i class="fa fa-pencil"></i>' . '</a>';

	}
	$row[] = $action_option . '</div>';

	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
