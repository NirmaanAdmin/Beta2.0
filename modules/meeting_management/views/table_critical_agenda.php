<?php
defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'critical_mom';

// Define filter names
$department_filter_name = 'department';
$status_filter_name     = 'status';
$priority_filter_name   = 'priority';
$from_date_filter_name  = 'from_date';
$to_date_filter_name    = 'to_date';

// Columns for dataTables
$aColumns      = [
    'id',
    'department',
    'area',
    'description',
    'decision',
    'action',
    'staff',
    'project_id',
    'target_date',
    'date_closed',
    'status',
    'priority',
    'minute_id',
];
$sIndexColumn  = 'id';
$sTable        = db_prefix() . 'critical_mom';
$join          = [
    'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'critical_mom.department',
    'LEFT JOIN ' . db_prefix() . 'staff       ON ' . db_prefix() . 'staff.staffid       = ' . db_prefix() . 'critical_mom.staff',
];
$where         = [];

// --- build filters ---
// if ($from = $this->ci->input->post('from_date')) {
//     $where[] = 'AND target_date >= "' . date('Y-m-d', strtotime($from)) . '"';
// }
// if ($to = $this->ci->input->post('to_date')) {
//     $where[] = 'AND target_date <= "' . date('Y-m-d', strtotime($to)) . '"';
// }
if ($depts = $this->ci->input->post('department')) {
    $where[] = 'AND department IN (' . implode(',', $depts) . ')';
}
if ($stats = $this->ci->input->post('status')) {
    $where[] = 'AND status IN (' . implode(',', $stats) . ')';
}
if ($prios = $this->ci->input->post('priority')) {
    $where[] = 'AND priority IN (' . implode(',', $prios) . ')';
}

// persist filters
update_module_filter($module_name, $department_filter_name, !empty($depts) ? implode(',', $depts) : null);
update_module_filter($module_name, $status_filter_name,     !empty($stats) ? implode(',', $stats) : null);
update_module_filter($module_name, $priority_filter_name,   !empty($prios) ? implode(',', $prios) : null);
// update_module_filter($module_name, $from_date_filter_name,  !empty($from)  ? $from  : null);
// update_module_filter($module_name, $to_date_filter_name,    !empty($to)    ? $to    : null);

// fetch data
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'critical_mom.id',
    db_prefix() . 'departments.name as department_name',
    db_prefix() . 'staff.firstname',
    db_prefix() . 'staff.lastname',
    'vendor',
    'minute_id',
]);
$output  = $result['output'];
$rResult = $result['rResult'];

// load models and lists
$this->ci->load->model('departments_model');
$this->ci->load->model('Staff_model');
$departments  = $this->ci->departments_model->get();
$staff_list   = $this->ci->Staff_model->get();
$departments_by_id = array_column($departments, null, 'departmentid');
$staff_by_id       = array_column($staff_list,  null, 'staffid');

// label maps
$status_labels = [
    1 => ['label' => 'danger',  'table' => 'open',  'text' => _l('Open')],
    2 => ['label' => 'success', 'table' => 'close', 'text' => _l('Close')],
];
$priority_labels = [
    1 => ['label' => 'warning', 'table' => 'high',   'text' => _l('High')],
    2 => ['label' => 'default', 'table' => 'low',    'text' => _l('Low')],
    3 => ['label' => 'info',    'table' => 'medium', 'text' => _l('Medium')],
    4 => ['label' => 'danger',  'table' => 'urgent', 'text' => _l('Urgent')],
];
$permission = '';
if (has_permission('critical_agenda', '', 'edit') || is_admin()) {
    $permission = true;
} else {
    $permission = false;
}
// build rows
$i = 1;
foreach ($rResult as $aRow) {
    $row = [];
    // 1) Serial
    $row[] = $i++;

    // 2) Department dropdown (unchanged)
    $department_html = '';
    if ($permission) {
        if (isset($departments_by_id[$aRow['department']])) {
            $dept = $departments_by_id[$aRow['department']];
            $department_html  = '<span class="inline-block label label-default"'
                . ' id="department_span_' . $aRow['id'] . '"'
                . ' data-task-status="department">'
                . html_escape($dept['name']);
        }
        $department_html .= '<div class="dropdown inline-block mleft5 table-export-exclude">'
            . '<a href="#" class="dropdown-toggle text-dark" data-toggle="dropdown"'
            . ' aria-haspopup="true" aria-expanded="false">'
            . '<i class="fa fa-caret-down" data-toggle="tooltip" title="'
            . _l('change_department') . '"></i></a>'
            . '<ul class="dropdown-menu dropdown-menu-right">';
        foreach ($departments_by_id as $id => $d) {
            if ($id != $aRow['department']) {
                $department_html .= '<li><a href="#"'
                    . ' onclick="change_department('
                    . $id . ', ' . $aRow['id']
                    . '); return false;">'
                    . html_escape($d['name']) . '</a></li>';
            }
        }
        $department_html .= '</ul></div></span>';
    } else {
        $department_html =  (isset($aRow['department']) && $aRow['department'] > 0)  ? $departments_by_id[$aRow['department']]['name'] : '';
    }

    $row[] = $department_html;

    if ($permission) {
        // 3) Area
        if (!empty($aRow['area'])) {
            $area = '<span class="area-display" data-id="' . $aRow['id'] . '">'
                . html_escape($aRow['area'])
                . '</span>';
        } else {
            $area = '<textarea class="form-control area-input" placeholder="Enter area" '
                . 'data-id="' . $aRow['id'] . '" rows="3"></textarea>';
        }
    } else {
        $area = html_escape($aRow['area']);
    }

    $row[] = $area;

    if ($permission) {
        // 4) Description
        if (!empty($aRow['description'])) {
            $description = '<span class="description-display" data-id="' . $aRow['id'] . '">'
                . $aRow['description']
                . '</span>';
        } else {
            $description = '<textarea class="form-control description-input" placeholder="Enter description" '
                . 'data-id="' . $aRow['id'] . '" rows="3" cols="80"></textarea>';
        }
    } else {
        $description = $aRow['description'];
    }

    $row[] = $description;


    if ($permission) {
        // 5) Decision
        if (!empty($aRow['decision'])) {
            $decision = '<span class="decision-display" data-id="' . $aRow['id'] . '">'
                . $aRow['decision']
                . '</span>';
        } else {
            $decision = '<textarea class="form-control decision-input" placeholder="Enter decision" '
                . 'data-id="' . $aRow['id'] . '" rows="4" cols="80"></textarea>';
        }
    } else {
        $decision = $aRow['decision'];
    }

    $row[] = $decision;

    if ($permission) {
        // 6) Action
        if (!empty($aRow['action'])) {
            $action = '<span class="action-display" data-id="' . $aRow['id'] . '">'
                . $aRow['action']
                . '</span>';
        } else {
            $action = '<textarea class="form-control action-input" placeholder="Enter action" '
                . 'data-id="' . $aRow['id'] . '" rows="4" cols="80"></textarea>';
        }
    } else {
        $action = $aRow['action'];
    }

    $row[] = $action;

    // 7) STAFF + VENDOR (now supports comma-separated staff IDs)
    if ($permission) {
        $staff_raw = trim($aRow['staff']); // e.g. "35,11,30"
        if ($staff_raw !== '') {
            // build display names
            $ids   = array_filter(explode(',', $staff_raw));
            $names = [];
            foreach ($ids as $sid) {
                if (isset($staff_by_id[$sid])) {
                    $u    = $staff_by_id[$sid];
                    $names[] = $u['firstname'] . ' ' . $u['lastname'];
                }
            }
            $staff_html = '<span '
                . 'class="staff-display" '
                . 'data-id="' . $aRow['id'] . '" '
                . 'data-staff="' . html_escape($staff_raw) . '">'
                . html_escape(implode(', ', $names))
                . '</span>';
        } else {
            // initial empty select
            $staff_html  = '<select multiple '
                . 'class="form-control staff-input selectpicker" '
                . 'data-live-search="true" '
                . 'data-width="100%" '
                . 'data-id="' . $aRow['id'] . '">';
            foreach ($staff_list as $st) {
                $staff_html .= '<option value="' . $st['staffid'] . '">'
                    . html_escape($st['firstname'] . ' ' . $st['lastname'])
                    . '</option>';
            }
            $staff_html .= '</select>';
        }

        // vendor: if empty show input, else display text
        if (!empty($aRow['vendor'])) {
            $vendor_html = '<br><div class="vendor-text vendor-display" data-id="' . $aRow['id'] . '">' . html_escape($aRow['vendor']) . '</div>';
        } else {
            $vendor_html = '<br><input type="text" class="form-control vendor-input" '
                . 'style="margin-top:10px;" placeholder="Enter vendor" '
                . 'data-id="' . $aRow['id'] . '">';
        }
    } else {

        $staff_raw = trim($aRow['staff']);
        $names = []; // initialize early to avoid undefined variable

        if ($staff_raw !== '') {
            $ids = array_filter(explode(',', $staff_raw));
            foreach ($ids as $sid) {
                if (isset($staff_by_id[$sid])) {
                    $u = $staff_by_id[$sid];
                    $names[] = $u['firstname'] . ' ' . $u['lastname'];
                }
            }
        }

        $staff_html = html_escape(implode(', ', $names));

        $vendor_html = html_escape($aRow['vendor']);
    }


    $row[] = $staff_html . $vendor_html;
    $row[] = get_project_name_by_id_mom($aRow['project_id']);
    if ($permission) {
        // 8) Target Date
        if ($aRow['minute_id'] > 0) {
            if ($aRow['target_date'] == '0000-00-00' || $aRow['target_date'] == '') {
                $target_date = '';
            } else {
                $target_date = date('d M, Y', strtotime($aRow['target_date'])); //$aRow['target_date'];  
            }
            $row[] = $target_date;
        } else {
            $row[] = '<input type="date" class="form-control target-date-input"'
                . ' value="' . $aRow['target_date'] . '" data-id="' . $aRow['id'] . '">';
        }
    } else {
        if ($aRow['target_date'] == '0000-00-00' || $aRow['target_date'] == '') {
            $target_date = '';
        } else {
            $target_date = date('d M, Y', strtotime($aRow['target_date'])); //$aRow['target_date'];  
        }
        $row[] = $target_date;
    }

    if ($permission) {
        // 9) Date Closed
        $row[] = '<input type="date" class="form-control closed-date-input"'
            . ' value="' . $aRow['date_closed'] . '" data-id="' . $aRow['id'] . '">';
    } else {
        if ($aRow['date_closed'] == '0000-00-00' || $aRow['date_closed'] == '') {
            $date_closed = '';
        } else {
            $date_closed = date('d M, Y', strtotime($aRow['date_closed'])); //$aRow['date_closed'];  
        }
        $row[] = $date_closed;
    }
    $status_html = '';
    if ($permission) {
        // 10) Status dropdown (unchanged)

        if (isset($status_labels[$aRow['status']])) {
            $s = $status_labels[$aRow['status']];
            $status_html  = '<span class="inline-block label label-' . $s['label'] . '"'
                . ' id="status_span_' . $aRow['id'] . '"'
                . ' data-task-status="' . $s['table'] . '">'
                . $s['text'];
        }
        $status_html  .=  '<div class="dropdown inline-block mleft5 table-export-exclude">'
            . '<a href="#" class="dropdown-toggle text-dark" id="tableStatus-' . $aRow['id'] . '"'
            . ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
            . '<i class="fa fa-caret-down" data-toggle="tooltip" title="'
            . _l('change_status') . '"></i></a>'
            . '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableStatus-' . $aRow['id'] . '">';
        foreach ($status_labels as $key => $lbl) {
            if ($key != $aRow['status']) {
                $status_html .= '<li><a href="#" onclick="change_status_mom('
                    . $key . ', ' . $aRow['id'] . ');return false;">'
                    . $lbl['text'] . '</a></li>';
            }
        }
        $status_html .= '</ul></div></span>';

        $row[] = $status_html;
    } else {
        if (isset($status_labels[$aRow['status']])) {
            $s = $status_labels[$aRow['status']];
            $status_html  = '<span class="inline-block label label-' . $s['label'] . '"'
                . ' id="status_span_' . $aRow['id'] . '"'
                . ' data-task-status="' . $s['table'] . '">'
                . $s['text'] . '</span>';
        }
        $row[] = $status_html;
    }
    $priority_html = '';

    if ($permission) {
        // 11) Priority dropdown (unchanged)

        if (isset($priority_labels[$aRow['priority']])) {
            $p = $priority_labels[$aRow['priority']];
            $priority_html  = '<span class="inline-block label label-' . $p['label'] . '"'
                . ' id="priority_span_' . $aRow['id'] . '"'
                . ' data-task-status="' . $p['table'] . '">'
                . $p['text'];
        }
        $priority_html  .= '<div class="dropdown inline-block mleft5 table-export-exclude">'
            . '<a href="#" class="dropdown-toggle text-dark" id="tablePriority-' . $aRow['id'] . '"'
            . ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
            . '<i class="fa fa-caret-down" data-toggle="tooltip" title="'
            . _l('change_priority') . '"></i></a>'
            . '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePriority-' . $aRow['id'] . '">';
        foreach ($priority_labels as $key => $lbl) {
            if ($key != $aRow['priority']) {
                $priority_html .= '<li><a href="#" onclick="change_priority_mom('
                    . $key . ', ' . $aRow['id'] . ');return false;">'
                    . $lbl['text'] . '</a></li>';
            }
        }
        $priority_html .= '</ul></div></span>';

        $row[] = $priority_html;
    } else {
        if (isset($priority_labels[$aRow['priority']])) {
            $p = $priority_labels[$aRow['priority']];
            $priority_html  = '<span class="inline-block label label-' . $p['label'] . '"'
                . ' id="priority_span_' . $aRow['id'] . '"'
                . ' data-task-status="' . $p['table'] . '">'
                . $p['text'] . '</span>';
        }
        $row[] = $priority_html;
    }


    $row[] = get_meeting_name_by_id($aRow['minute_id']);

    $output['aaData'][] = $row;
}
