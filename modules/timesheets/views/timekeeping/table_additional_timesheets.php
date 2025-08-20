<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
    'id',
    'creator',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'staff_departments JOIN '.db_prefix().'departments ON '.db_prefix().'staff_departments.departmentid = '.db_prefix().'departments.departmentid WHERE staffid = creator ORDER by name ASC) as department_name',
    'additional_day',
    'timekeeping_value',
    'comoff_value',
    'timekeeping_type',
    '(SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM '.db_prefix().'timesheets_approval_details WHERE rel_id = '.db_prefix().'timesheets_additional_timesheet.id and rel_type = "additional_timesheets") as approver',
    'status',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'timesheets_additional_timesheet';
$join = ['LEFT JOIN '.db_prefix().'staff b ON b.staffid = '.db_prefix().'timesheets_additional_timesheet.creator',];
$where = [];

if(!is_admin() && !has_permission('additional_timesheets_management','','view')){
    if(!$this->ci->input->post('chose_ats') || $this->ci->input->post('chose_ats') == 'all'){
        array_push($where, timesheet_staff_manager_query('additional_timesheets_management', 'creator', 'AND'));
           array_push($where,'OR ('. get_staff_user_id() .' in (select staffid from '.db_prefix().'timesheets_approval_details where rel_type = "additional_timesheets" and rel_id = '.db_prefix().'timesheets_additional_timesheet.id))');        
   }

   array_push($where,'OR ('. db_prefix() .'timesheets_additional_timesheet.staff_id =  '.get_staff_user_id().')');        

}

if($this->ci->input->post('status_filter_ats')){
    $where_status = '';
    $status = $this->ci->input->post('status_filter_ats');
    foreach ($status as $statues) {

        if($status != '')
        {
            if($where_status == ''){
                $where_status .= ' AND (status = "'.$statues. '"';
            }else{
                $where_status .= ' or status = "' .$statues.'"';
            }
        }
    }
    if($where_status != '')
    {
        $where_status .= ')';
        array_push($where, $where_status);
    }
}

if($this->ci->input->post('department_ats')){
    $where_dpm = '';
    $department = $this->ci->input->post('department_ats');
    foreach ($department as $statues) {

        if($department != '')
        {
            if($where_dpm == ''){
                $where_dpm = ' AND (creator IN (SELECT staffid FROM '.db_prefix().'staff_departments WHERE departmentid = '.$statues.')';
                
            }else{
                $where_dpm .= 'OR creator IN (SELECT staffid FROM '.db_prefix().'staff_departments WHERE departmentid = '.$statues.')';
            }
        }
    }
    if($where_dpm != '')
    {
        $where_dpm .= ')';
        array_push($where,  $where_dpm);
    }
}

if($this->ci->input->post('rel_type_filter_ats')){
    $where_rel_type = '';
    $rel_type = $this->ci->input->post('rel_type_filter_ats');
    foreach ($rel_type as $statues) {

        if($rel_type != '')
        {
            if($where_rel_type == ''){
                $where_rel_type .= ' AND (timekeeping_type = "'.$statues. '"';
            }else{
                $where_rel_type .= ' or timekeeping_type = "' .$statues.'"';
            }
        }
    }
    if($where_rel_type != '')
    {   
        $where_rel_type .= ')';
        array_push($where, $where_rel_type);
    }
}

if($this->ci->input->post('chose_ats')){
    $chose = $this->ci->input->post('chose_ats');
    $sql_where = '';
    if($chose != 'all'){
        if($sql_where != ''){
            $sql_where .= ' AND ("'.get_staff_user_id().'" IN (SELECT staffid FROM '.db_prefix().'timesheets_approval_details where '.db_prefix().'timesheets_approval_details.rel_type IN ("additional_timesheets") AND '.db_prefix().'timesheets_approval_details.rel_id = '.db_prefix().'timesheets_additional_timesheet.id ))';
        }else{
            $sql_where .= '("'.get_staff_user_id().'" IN (SELECT staffid FROM '.db_prefix().'timesheets_approval_details where '.db_prefix().'timesheets_approval_details.rel_type IN ("additional_timesheets") AND '.db_prefix().'timesheets_approval_details.rel_id = '.db_prefix().'timesheets_additional_timesheet.id ))';
        }
    }else{
        $sql_where = '';
    }
    if($sql_where != '')
    {
        array_push($where, 'AND '. $sql_where);
    }
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,['b.firstname']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];

    $_data = '<a href="' . admin_url('staff/profile/' . $aRow['creator']) . '">' . staff_profile_image($aRow['creator'], [
        'staff-profile-image-small',
    ]) . '</a>';
    $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['creator']) . '">' . get_staff_full_name($aRow['creator']) . '</a>';
    $row[] = $_data;
    $row[] = $aRow['department_name'];
    $row[] = _d($aRow['additional_day']);
    $row[] = ($aRow['timekeeping_type'] != null ? _l($aRow['timekeeping_type']) : '');
    $row[] = ceil($aRow['timekeeping_value']);
    $row[] = ceil($aRow['comoff_value']);
    $membersOutput = '';

    $members       = explode(',', $aRow['approver'] ?? '');
    $list_member = '';
    $exportMembers = '';
    foreach ($members as $key => $member_id) {
        if ($member_id != '') {
            $member_name = get_staff_full_name($member_id);
            $list_member .= '<li class="text-success mbot10 mtop"><a href="' . admin_url('profile/' . $member_id) . '" class="avatar cover-image text-align-left">' .
            staff_profile_image($member_id, [
                'staff-profile-image-small mright5',
            ], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $member_name,
            ]) .' '.$member_name. '</a></li>';
            if($key <= 2){
                $membersOutput .= '<span class="avatar cover-image brround">' .
                staff_profile_image($member_id, [
                    'staff-profile-image-small mright5',
                ], 'small', [
                    'data-toggle' => 'tooltip',
                    'data-title'  => $member_name,
                ]) . '</span>';
            }
            // For exporting
            $exportMembers .= $member_name . ', ';
        }
    }
    if(count($members) > 3){
        $membersOutput .= '<span class="avatar bg-secondary brround avatar-none">+'. (count($members) - 3) .'</span>';
    }

    $membersOutput .= '<span class="hide">' . ts_trim($exportMembers, ', ') . '</span>';

    $membersOutput1 = '<div class="task-info task-watched task-info-watched">
    <h5>
    <div class="btn-group">
    <span class="task-single-menu task-menu-watched">
    <div class="avatar-list avatar-list-stacked" data-toggle="dropdown">'.$membersOutput.'</div>
    <ul class="dropdown-menu list-staff" role="menu">
    <li class="dropdown-plus-title">
    '. _l('approver') .'
    </li>
    '.$list_member.'
    </ul>
    </span>
    </div>
    </h5>
    </div>';
    $row[] = $membersOutput1;

    $status_class = 'info';
    $status_text = 'status_0';
    if($aRow['status'] == 1){
      $status_class = 'success';
      $status_text = 'status_1';
  }elseif ($aRow['status'] == 2) {
      $status_class = 'danger';
      $status_text = 'status_-1';
  }

  $row[] = '<span class="label label-'. $status_class.'  mr-1 mb-1 mt-1">'. _l($status_text).'</span>';

  $options = '<a href="Javascript:void(0);" onclick="view_additional_timesheets('.$aRow['id'].'); return false" class="btn btn-default btn-icon" data-toggle="sidebar-right" data-target=".additional-timesheets-sidebar"><i class="fa fa-eye"></i></a>';


  if($aRow['status'] == 0 || is_admin()){
      $options .= '<a id="delete-insurance" href="'. admin_url('timesheets/delete_additional_timesheets/'.$aRow['id']).'" class="btn btn-danger btn-icon _delete mleft5"><i class="fa fa-remove"></i></a>';
  }
  $row[]   = $options;

  $output['aaData'][] = $row;
}
