<?php
defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'tickets';

$status_filter_name = 'ticket_status';
$contact_filter_name = 'contact';
$department_filter_name = 'department';


// Get CI instance
$CI = &get_instance();
$CI->load->model('tickets_model');
$CI->load->model('departments_model');
$CI->load->model('staff_model');

$statuses = $CI->tickets_model->get_ticket_status();
$priorities = $CI->tickets_model->get_priority();
$services = $CI->tickets_model->get_service();
$departments = $CI->departments_model->get();
$staff_members = $CI->staff_model->get('', ['active' => 1]);

$hasPermissionEdit = staff_can('edit', 'tickets');
$hasPermissionDelete = staff_can('delete', 'tickets');

// Base columns
$aColumns = [
    '1', // bulk actions
    db_prefix() . 'tickets.ticketid as ticketid',
    db_prefix() . 'tickets.subject as subject',
    db_prefix() . 'departments.name as department_name',
    'CONCAT(' . db_prefix() . 'contacts.firstname, \' \', ' . db_prefix() . 'contacts.lastname) as contact_full_name',
    db_prefix() . 'tickets.status as status',
    db_prefix() . 'tickets.priority as priority',
    db_prefix() . 'tickets.lastreply as lastreply',
    db_prefix() . 'tickets.date as date',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'tickets.ticketid and rel_type="ticket" ORDER by tag_order ASC) as tags',
];

// Custom fields
$custom_fields = get_table_custom_fields('tickets');
$customFieldsColumns = [];
foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    $customFieldsColumns[] = $selectAs;
    $aColumns[] = '(SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE ' . db_prefix() . 'customfieldsvalues.relid=' . db_prefix() . 'tickets.ticketid AND ' . db_prefix() . 'customfieldsvalues.fieldid=' . $field['id'] . ' AND ' . db_prefix() . 'customfieldsvalues.fieldto="' . $field['fieldto'] . '" LIMIT 1) as ' . $selectAs;
}

$aColumns = hooks()->apply_filters('tickets_table_sql_columns', $aColumns);

// Fix for big queries
if (count($custom_fields) > 4) {
    @$CI->db->query('SET SQL_BIG_SELECTS=1');
}

// Where conditions
$where = [];
$join = [
    'LEFT JOIN ' . db_prefix() . 'contacts ON ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'tickets.contactid',
    'LEFT JOIN ' . db_prefix() . 'services ON ' . db_prefix() . 'services.serviceid = ' . db_prefix() . 'tickets.service',
    'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'tickets.department',
    'LEFT JOIN ' . db_prefix() . 'tickets_status ON ' . db_prefix() . 'tickets_status.ticketstatusid = ' . db_prefix() . 'tickets.status',
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'tickets.userid',
    'LEFT JOIN ' . db_prefix() . 'tickets_priorities ON ' . db_prefix() . 'tickets_priorities.priorityid = ' . db_prefix() . 'tickets.priority',
];

// Add custom fields joins
foreach ($custom_fields as $key => $field) {
    $join[] = 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'tickets.ticketid = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id'];
}

// Permission check - only show tickets staff has access to
if (staff_cant('view', 'tickets')) {
    $where[] = 'AND (' . db_prefix() . 'tickets.assigned = ' . get_staff_user_id() . ' OR ' . db_prefix() . 'tickets.department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE staffid = ' . get_staff_user_id() . '))';
}

// Filter by user/client
if ($CI->input->post('userid') && $CI->input->post('userid') != '') {
    $where[] = 'AND ' . db_prefix() . 'tickets.userid = ' . $CI->db->escape_str($CI->input->post('userid'));
}

// Filter by email
if ($CI->input->post('by_email')) {
    $where[] = 'AND ' . db_prefix() . 'tickets.email = "' . $CI->db->escape_str($CI->input->post('by_email')) . '"';
}

// Exclude specific ticket (for merged tickets)
if ($CI->input->post('via_ticket')) {
    $where[] = 'AND ' . db_prefix() . 'tickets.ticketid != ' . $CI->db->escape_str($CI->input->post('via_ticket'));
}

// Filter by project
if ($CI->input->post('project_id')) {
    $where[] = 'AND project_id = ' . $CI->db->escape_str($CI->input->post('project_id'));
}

// Default project filter
if (get_default_project()) {
    $where[] = 'AND ' . db_prefix() . 'tickets.project_id = ' . get_default_project();
}

// Status filter
if ($CI->input->post('ticket_status') && count($CI->input->post('ticket_status')) > 0) {
    $where[] = 'AND ' . db_prefix() . 'tickets.status IN (' . implode(',', array_map('intval', $CI->input->post('ticket_status'))) . ')';
}

// Department filter
if ($CI->input->post('department') && count($CI->input->post('department')) > 0) {
    $where[] = 'AND ' . db_prefix() . 'tickets.department IN (' . implode(',', array_map('intval', $CI->input->post('department'))) . ')';
}

if ($CI->input->post('contact') && count($CI->input->post('contact')) > 0) {

    $contacts = $CI->input->post('contact');

    // Escape each value safely
    $escaped_contacts = array_map(function ($value) use ($CI) {
        return $CI->db->escape($value);
    }, $contacts);

    $where[] = 'AND ' . db_prefix() . 'tickets.name IN (' . implode(',', $escaped_contacts) . ')';
}




// Update module filters
$status_filter_name_value = !empty($CI->input->post('ticket_status')) ? implode(',', $CI->input->post('ticket_status')) : NULL;
update_module_filter($module_name, $status_filter_name, $status_filter_name_value);

$contact_filter_name_value = !empty($CI->input->post('contact')) ? implode(',', $CI->input->post('contact')) : NULL;
update_module_filter($module_name, $contact_filter_name, $contact_filter_name_value);

$department_filter_name_value = !empty($CI->input->post('department')) ? implode(',', $CI->input->post('department')) : NULL;
update_module_filter($module_name, $department_filter_name, $department_filter_name_value);


$result = data_tables_init(
    $aColumns,
    'ticketid',
    db_prefix() . 'tickets',
    $join,
    $where,
    [
        'adminread',
        'ticketkey',
        db_prefix() . 'tickets.userid',
        'statuscolor',
        db_prefix() . 'tickets.name as ticket_opened_by_name',
        db_prefix() . 'tickets.email',
        db_prefix() . 'clients.company',
        'assigned',
        'merged_ticket_id',
    ],
    '',
    [],
    '',
    'tickets'
);

$output = $result['output'];
$rResult = $result['rResult'];
$sr = 1;

foreach ($rResult as $aRow) {
    $row = [];

    // Checkbox for bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['ticketid'] . '" data-name="' . e($aRow['subject']) . '" data-status="' . $aRow['status'] . '"><label></label></div>';

    // Serial number
    $row[] = $sr++;

    // Subject with assigned staff avatar and row options
    $outputSubject = '';

    if ($aRow['assigned'] != 0) {
        $outputSubject .= '<a href="' . admin_url('profile/' . $aRow['assigned']) . '" 
        data-toggle="tooltip" 
        title="' . e(get_staff_full_name($aRow['assigned'])) . '" 
        class="pull-left mright5">'
            . staff_profile_image($aRow['assigned'], ['staff-profile-image-xs']) .
            '</a>';
    }

    // Wrap ONLY subject in link (not whole block)
    $url = admin_url('tickets/ticket/' . $aRow['ticketid']);

    $outputSubject .= '<a href="' . $url . '?tab=settings" class="valign">'
        . e($aRow['subject']) .
        '</a>';

    $outputSubject .= '<div class="row-options">';

    if ($hasPermissionEdit) {
        $outputSubject .= '<a href="' . $url . '?tab=settings">' . _l('view') . '</a>';
        $outputSubject .= ' | <a href="' . $url . '">' . _l('edit') . '</a>';
    }

    $outputSubject .= ' | <a href="' . get_ticket_public_url($aRow) . '" target="_blank">' . _l('view_public_form') . '</a>';

    if ($hasPermissionDelete) {
        $outputSubject .= ' | <a href="' . admin_url('tickets/delete/' . $aRow['ticketid']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $outputSubject .= '</div>';

    $row[] = $outputSubject;

    // Department name
    $row[] = e($aRow['department_name']);

    // Contact full name with company
    $outputContact = '';
    if ($aRow['userid'] != 0) {
        $outputContact = '<a href="' . admin_url('clients/client/' . $aRow['userid'] . '?group=contacts') . '">' . e($aRow['contact_full_name']);
        if (!empty($aRow['company'])) {
            $outputContact .= ' (' . e($aRow['company']) . ')';
        }
        $outputContact .= '</a>';
    } else {
        $outputContact = e($aRow['ticket_opened_by_name']);
    }
    $row[] = $outputContact;

    // Status with color
    $outputStatus = '<span class="label ticket-status-' . $aRow['status'] . '" style="border:1px solid ' . adjust_hex_brightness($aRow['statuscolor'], 0.4) . '; color:' . $aRow['statuscolor'] . ';background: ' . adjust_hex_brightness($aRow['statuscolor'], 0.04) . ';">';
    $outputStatus .= e(ticket_status_translate($aRow['status']));
    $outputStatus .= '</span>';


    $row[] = $outputStatus;

    // Priority
    $row[] = e(ticket_priority_translate($aRow['priority']));

    // Last reply
    if ($aRow['lastreply'] == null) {
        $row[] = _l('ticket_no_reply_yet');
    } else {
        $row[] = e(_dt($aRow['lastreply']));
    }

    // Date
    $row[] = e(_dt($aRow['date']));

    // Tags
    $row[] = render_tags($aRow['tags']);

    // PDF actions
    $row[] = '<div class="btn-group mright5">
        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="hidden-xs"><a href="' . admin_url('tickets/pdf/' . $aRow['ticketid'] . '?output_type=I') . '">' . _l('view_pdf') . '</a></li>
            <li class="hidden-xs"><a href="' . admin_url('tickets/pdf/' . $aRow['ticketid'] . '?output_type=I') . '" target="_blank">' . _l('view_pdf_in_new_window') . '</a></li>
            <li><a href="' . admin_url('tickets/pdf/' . $aRow['ticketid']) . '">' . _l('download') . '</a></li>
        </ul>
    </div>';

    // Custom fields
    foreach ($customFieldsColumns as $customFieldColumn) {
        if (isset($aRow[$customFieldColumn])) {
            if (strpos($customFieldColumn, 'date_picker_') !== false) {
                $row[] = (strpos($aRow[$customFieldColumn], ' ') !== false ? _dt($aRow[$customFieldColumn]) : _d($aRow[$customFieldColumn]));
            } else {
                $row[] = e($aRow[$customFieldColumn]);
            }
        } else {
            $row[] = '';
        }
    }

    // Row classes
    $row['DT_RowClass'] = 'has-row-options';

    if ($aRow['adminread'] == 0) {
        $row['DT_RowClass'] .= ' text-danger';
    }

    // Check for merged ticket indicator
    if (!empty($aRow['merged_ticket_id'])) {
        $row['DT_RowClass'] .= ' merged-ticket';
    }

    $row = hooks()->apply_filters('tickets_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
