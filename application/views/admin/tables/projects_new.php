<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$module_name = 'projects';
$project_name = 'project_name';
$client_name = 'clients';
$member_name = 'members';
$status_name = 'statuses';

$hasPermissionEdit   = staff_can('edit',  'projects');
$hasPermissionDelete = staff_can('delete',  'projects');
$hasPermissionCreate = staff_can('create',  'projects');

$aColumns = [
    db_prefix() . 'projects.id as id',
    'name',
    get_sql_select_client_company(),
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'projects.id and rel_type="project" ORDER by tag_order ASC) as tags',
    'start_date',
    'deadline',
    '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members',
    'status',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'projects';

$join = [
    'JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid',
];

$where  = [];

if (staff_cant('view', 'projects')) {
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
}

$custom_fields = get_table_custom_fields('projects');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'projects.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$aColumns = hooks()->apply_filters('projects_table_sql_columns', $aColumns);

if ($CI->input->post('project_name') && count($CI->input->post('project_name')) > 0) {
    array_push($where, 'AND ' . db_prefix() . 'projects.id IN (' . implode(',', $CI->input->post('project_name')) . ')');
}

if ($CI->input->post('clients') && count($CI->input->post('clients')) > 0) {
    array_push($where, 'AND ' . db_prefix() . 'projects.clientid IN (' . implode(',', $CI->input->post('clients')) . ')');
}

if ($CI->input->post('members') && count($CI->input->post('members')) > 0) {
    array_push(
        $where,
        'AND ' . db_prefix() . 'projects.id IN (
            SELECT project_id
            FROM ' . db_prefix() . 'project_members
            WHERE staff_id IN (' . implode(',', $CI->input->post('members')) . ')
        )'
    );
}

if ($CI->input->post('statuses') && count($CI->input->post('statuses')) > 0) {
    array_push($where, 'AND ' . db_prefix() . 'projects.status IN (' . implode(',', $CI->input->post('statuses')) . ')');
}

$project_name_value = !empty($CI->input->post('project_name')) ? implode(',', $CI->input->post('project_name')) : NULL;
update_module_filter($module_name, $project_name, $project_name_value);

$client_name_value = !empty($CI->input->post('clients')) ? implode(',', $CI->input->post('clients')) : NULL;
update_module_filter($module_name, $client_name, $client_name_value);

$member_name_value = !empty($CI->input->post('members')) ? implode(',', $CI->input->post('members')) : NULL;
update_module_filter($module_name, $member_name, $member_name_value);

$status_name_value = !empty($CI->input->post('statuses')) ? implode(',', $CI->input->post('statuses')) : NULL;
update_module_filter($module_name, $status_name, $status_name_value);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'clientid',
    '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members_ids',
]);

$output  = $result['output'];
$rResult = $result['rResult'];
$sr = 1;
foreach ($rResult as $aRow) {
    $row = [];

    $link = admin_url('projects/view/' . $aRow['id']);

    $row[] = '<a href="' . $link . '">' . $sr . '</a>';

    $name = '<a href="' . $link . '">' . e($aRow['name']) . '</a>';

    $name .= '<div class="row-options">';

    $name .= '<a href="' . $link . '">' . _l('view') . '</a>';

    if ($hasPermissionCreate && !$clientid) {
        $name .= ' | <a href="#" data-name="' . e($aRow['name']) . '" onclick="copy_project(' . $aRow['id'] . ', this);return false;">' . _l('copy_project') . '</a>';
    }

    if ($hasPermissionEdit) {
        $name .= ' | <a href="' . admin_url('projects/project/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }

    if ($hasPermissionDelete) {
        $name .= ' | <a href="' . admin_url('projects/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $name .= '</div>';

    $row[] = $name;

    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . e($aRow['company']) . '</a>';

    $row[] = render_tags($aRow['tags']);

    ob_start();
    $percent = $this->ci->projects_model->calc_progress($aRow['id']);
    $progress_bar_percent = $percent / 100; ?>
    <input type="hidden" value="<?php
    echo '' . $progress_bar_percent; ?>" name="percent">
    <div class="goal-progress" data-reverse="true">
       <strong class="goal-percent pr-goal-percent"><?php
        echo '' . $percent; ?>%</strong>
    </div>
    <?php
    $progress = ob_get_contents();
    ob_end_clean();
    $row[] = $progress;

    $row[] = e(_d($aRow['start_date']));

    $row[] = e(_d($aRow['deadline']));

    $membersOutput = '<div class=" -tw-space-x-1">';
    $members       = explode(',', $aRow['members']);
    $exportMembers = '';
    foreach ($members as $key => $member) {
        if ($member != '') {
            $members_ids = explode(',', $aRow['members_ids']);
            $member_id   = $members_ids[$key];
            $membersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
                staff_profile_image($member_id, [
                    'tw-inline-block tw-h-7 tw-w-7 tw-rounded-full tw-ring-2 tw-ring-white',
                ], 'small', [
                    'data-toggle' => 'tooltip',
                    'data-title'  => $member,
                ]) . '</a>';
            // For exporting
            $exportMembers .= $member . ', ';
        }
    }

    $membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
    $membersOutput .= '</div>';
    $row[] = $membersOutput;

    $options = icon_btn('project_roadmap/view_project_roadmap/' . $aRow['id'], 'fa fa-eye', 'btn-default', $data_title);
    $row[] =  $options;

    $status = get_project_status_by_id($aRow['status']);
    $row[]  = '<span class="label project-status-' . $aRow['status'] . '" style="color:' . $status['color'] . ';border:1px solid ' . adjust_hex_brightness($status['color'], 0.4) . ';background: ' . adjust_hex_brightness($status['color'], 0.04) . ';">' . e($status['name']) . '</span>';

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('projects_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
    $sr++;
}

?>