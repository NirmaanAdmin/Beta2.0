<?php

use function Clue\StreamFilter\fun;

defined('BASEPATH') or exit('No direct script access allowed');

// Example helper function (You can define any reusable function here)
function example_meeting_helper_function()
{
    return "This is a helper function for the Meeting Management module.";
}

function getstafflist()
{
    $CI = &get_instance();
    $CI->load->model('staff_model');

    // Retrieve active staff members.
    $get_staff = $CI->staff_model->get('', ['active' => 1]);

    // Build an array with 'staffid' and concatenated 'fullname' for each staff member.
    $staff_list = [];
    foreach ($get_staff as $staff) {
        $fullname = $staff['firstname'] . ' ' . $staff['lastname'];
        $staff_list[] = [
            'staffid'  => $staff['staffid'],
            'fullname' => $fullname,
            'email'    => $staff['email'],
            'phonenumber' => $staff['phonenumber'],
            'attributes' => [
                'data-email' => $staff['email'],
                'data-phonenumber' => $staff['phonenumber']
            ]
        ];
    }

    return $staff_list;
}


/**
 * Takes a comma separated string of staff IDs and returns a comma separated string of the
 * corresponding staff full names.
 * 
 * @param string $staff_ids_csv A comma separated string of staff IDs.
 * @return string A comma separated string of the corresponding staff full names.
 */
function getStaffNamesFromCSV($staff_ids_csv)
{
    // Retrieve the full staff list as an array with staffid and fullname.
    $staff_list = getstafflist();

    // Convert the comma separated input string to an array and trim any whitespace.
    $staff_ids_array = array_map('trim', explode(',', $staff_ids_csv));

    // Build a lookup array mapping each staff id to its fullname.
    $staff_map = [];
    foreach ($staff_list as $staff) {
        $staff_map[$staff['staffid']] = $staff['fullname'];
    }

    // Prepare an array to hold the full names corresponding to the provided staff IDs.
    $names = [];
    foreach ($staff_ids_array as $id) {
        if (isset($staff_map[$id])) {
            $names[] = $staff_map[$id];
        }
    }

    // Return the names as a comma separated string.
    return implode(', ', $names);
}
function getvendorlist()
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    return $get_vendor = $CI->purchase_model->get_vendor_for_project_dir();
}

function getdeptmom()
{
    $CI = &get_instance();

    try {
        if (!isset($CI->Departments_model)) {
            $CI->load->model('Departments_model');
        }

        $departments = $CI->Departments_model->get();

        if (empty($departments)) {
            log_message('info', 'No departments found in getdeptmom()');
            return [];
        }

        // Return the original array of department objects
        return $departments;
    } catch (Exception $e) {
        log_message('error', 'Error in getdeptmom(): ' . $e->getMessage());
        return [];
    }
}

function get_meeting_name_by_id($meeting_id)
{
    $CI = &get_instance();

    $meeting = $CI->db->select('meeting_title')
        ->where('id', $meeting_id)
        ->from(db_prefix() . 'meeting_management')
        ->get()
        ->row();
    if ($meeting) {
        return $meeting->meeting_title;
    }

    return '';
}

/**
 * Returns the name of a project by its ID from the 'projects' table.
 *
 * @param int $project_id The ID of the project to retrieve.
 *
 * @return string The name of the project, or an empty string if not found.
 */
function get_project_name_by_id_mom($project_id)
{
    $CI = &get_instance();

    $project = $CI->db->select('name')
        ->where('id', $project_id)
        ->from(db_prefix() . 'projects')
        ->get()
        ->row();
    if ($project) {
        return $project->name;
    }

    return '';
}

/**
 * Returns an array of critical tracker items suitable for use in a PDF.
 *
 * The function will apply any user-saved filters for the critical tracker
 * module, using the current staff member's ID.
 *
 * @return array Critical tracker items.
 */
function get_critical_tracker_pdf()
{
    $CI = &get_instance();

    // 1) Build the base query
    $baseSql = "
    SELECT 
        cm.id,
        cm.department,
        cm.area,
        cm.description,
        cm.decision,
        cm.action,
        cm.staff,
        cm.project_id,
        cm.target_date,
        cm.date_closed,
        cm.status,
        cm.priority,
        cm.minute_id,
        cm.vendor,
        d.name as department_name,
        s.firstname,
        s.lastname,
        'critical_mom' as source_table
    FROM tblcritical_mom cm
    LEFT JOIN tbldepartments d ON d.departmentid = cm.department
    LEFT JOIN tblstaff s ON s.staffid = cm.staff
    ";

    // 2) Load any user-saved filters
    $filters = $CI->db
        ->select('*')
        ->from(db_prefix() . 'module_filter')
        ->where('module_name', 'critical_mom')
        ->where('staff_id', get_staff_user_id())
        ->get()
        ->result_array();

    // 3) Build WHERE clauses
    $whereClauses = [];
    foreach ($filters as $f) {
        $name = $f['filter_name'];
        $value = trim($f['filter_value']);

        if ($value === '') {
            continue;
        }

        $val = $CI->db->escape_str($value);

        switch ($name) {
            case 'priority':
                $whereClauses[] = "cm.priority = '{$val}'";
                break;

            case 'status':
                $whereClauses[] = "cm.status = '{$val}'";
                break;

            case 'department':
                $whereClauses[] = "cm.department = '{$val}'";
                break;
        }
    }

    // 4) If there are filters, apply them
    if (!empty($whereClauses)) {
        $sql = $baseSql . " WHERE " . implode(' AND ', $whereClauses);
    } else {
        $sql = $baseSql;
    }

    // Add sorting
    $sql .= " ORDER BY cm.target_date DESC";

    // 5) Execute and return
    return $CI->db->query($sql)->result_array();
}
