<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('tickets_model');
$statuses = $this->ci->tickets_model->get_ticket_status();
$this->ci->load->model('departments_model');

$rules = [
    App_table_filter::new('subject', 'TextRule')->label(_l('ticket_dt_subject')),
    App_table_filter::new('department', 'SelectRule')->label(_l('ticket_dt_department'))->options(function ($ci) {
        return collect($ci->departments_model->get())->map(fn ($dep) => [
            'value' => $dep['departmentid'],
            'label' => $dep['name']
        ])->all();
    })->isVisible(fn () => is_admin()),
    App_table_filter::new('status', 'MultiSelectRule')->label(_l('ticket_dt_status'))->options(function ($ci) use ($statuses) {
        return collect($statuses)->map(fn ($status) => [
            'value' => $status['ticketstatusid'],
            'label' => ticket_status_translate($status['ticketstatusid'])
        ])->all();
    }),
    App_table_filter::new('priority', 'SelectRule')->label(_l('ticket_dt_priority'))->options(function ($ci) {
        return collect($ci->tickets_model->get_priority())->map(fn ($priority) => [
            'value' => $priority['priorityid'],
            'label' => ticket_priority_translate($priority['priorityid'])
        ])->all();
    }),
    App_table_filter::new('service', 'SelectRule')->label(_l('ticket_dt_service'))->options(function ($ci) use ($statuses) {
        return collect($ci->tickets_model->get_service())->map(fn ($service) => [
            'value' => $service['serviceid'],
            'label' => $service['name']
        ])->all();
    }),
    App_table_filter::new('merged', 'BooleanRule')->label(_l('merged'))->raw(function ($value) {
        return $value == "1" ? 'merged_ticket_id IS NOT NULL' : 'merged_ticket_id IS NULL';
    }),
    App_table_filter::new('my_tickets', 'BooleanRule')->label(_l('my_tickets_assigned'))->raw(function ($value) {
        return $value == "1" ? 'assigned = ' . get_staff_user_id() : 'assigned != ' . get_staff_user_id();
    }),
];

$rules[] = App_table_filter::new('assigned', 'SelectRule')->label(_l('ticket_assigned'))
    ->withEmptyOperators()
    ->emptyOperatorValue(0)
    ->isVisible(fn () => is_admin())
    ->options(function ($ci) {
        $staff = $ci->staff_model->get('', ['active' => 1]);

        return collect($staff)->map(function ($staff) {
            return [
                'value' => $staff['staffid'],
                'label' => $staff['firstname'] . ' ' . $staff['lastname']
            ];
        })->all();
    });

return App_table::find('tickets')
    ->outputUsing(function ($params) use ($statuses) {
        extract($params);

        // First define the actual database columns for the SQL query
        $dbColumns = [
            '1', // bulk actions
            'ticketid', // We still need this internally but won't display it
            'subject',
            db_prefix() . 'departments.name as department_name',
            'CONCAT(' . db_prefix() . 'contacts.firstname, \' \', ' . db_prefix() . 'contacts.lastname) as contact_full_name',
            'status',
            'priority',
            'lastreply',
            db_prefix() . 'tickets.date',
            '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'tickets.ticketid and rel_type="ticket" ORDER by tag_order ASC) as tags',
            '2',
        ];

        // Then define the display columns (includes sr_no instead of ticketid)
        $displayColumns = [
            '1', // bulk actions
            'sr_no', // SR. No column (will be generated in PHP)
            'subject',
            'department_name',
            'contact_full_name',
            'status',
            'priority',
            'lastreply',
            'date',
            'tags',
            '2',
        ];

        $contactColumn = 4; // Position in displayColumns array
        $tagsColumns   = 9; // Position in displayColumns array

        $additionalSelect = [
            'adminread',
            'ticketkey',
            db_prefix() . 'tickets.userid',
            'statuscolor',
            db_prefix() . 'tickets.name as ticket_opened_by_name',
            db_prefix() . 'tickets.email',
            db_prefix() . 'tickets.userid',
            'assigned',
            db_prefix() . 'clients.company',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'contacts ON ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'tickets.contactid',
            'LEFT JOIN ' . db_prefix() . 'services ON ' . db_prefix() . 'services.serviceid = ' . db_prefix() . 'tickets.service',
            'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'tickets.department',
            'LEFT JOIN ' . db_prefix() . 'tickets_status ON ' . db_prefix() . 'tickets_status.ticketstatusid = ' . db_prefix() . 'tickets.status',
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'tickets.userid',
            'LEFT JOIN ' . db_prefix() . 'tickets_priorities ON ' . db_prefix() . 'tickets_priorities.priorityid = ' . db_prefix() . 'tickets.priority',
        ];

        $custom_fields = get_table_custom_fields('tickets');
        foreach ($custom_fields as $key => $field) {
            $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
            array_push($customFieldsColumns, $selectAs);
            array_push($dbColumns, 'ctable_' . $key . '.value as ' . $selectAs);
            array_push($displayColumns, $selectAs);
            array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'tickets.ticketid = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
        }

        $where  = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        if (isset($userid) && $userid != '') {
            array_push($where, 'AND ' . db_prefix() . 'tickets.userid = ' . $this->ci->db->escape_str($userid));
        } elseif (isset($by_email)) {
            array_push($where, 'AND ' . db_prefix() . 'tickets.email = "' . $this->ci->db->escape_str($by_email) . '"');
        }

        if (isset($via_ticket)) {
            array_push($where, 'AND ' . db_prefix() . 'tickets.ticketid != ' . $this->ci->db->escape_str($via_ticket));
        }

        if ($project_id = $this->ci->input->post('project_id')) {
            array_push($where, 'AND project_id = ' . $this->ci->db->escape_str($project_id));
        }

        if(get_default_project()) {
            array_push($where, 'AND ' . db_prefix() . 'tickets.project_id = '.get_default_project().'');
        }

        $sIndexColumn = 'ticketid';
        $sTable       = db_prefix() . 'tickets';

        // Fix for big queries. Some hosting have max_join_limit
        if (count($custom_fields) > 4) {
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }

        // Use the dbColumns for the SQL query
        $result = data_tables_init($dbColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

        $output  = $result['output'];
        $rResult = $result['rResult'];
        
        // Counter for serial number
        $i = 1;
        foreach ($rResult as $aRow) {
            $row = [];
            
            // Process each display column (not db column)
            for ($j = 0; $j < count($displayColumns); $j++) {
                $colName = $displayColumns[$j];
                
                // Handle special columns
                if ($colName == '1') {
                    $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['ticketid'] . '" data-name="' . $aRow['subject'] . '" data-status="' . $aRow['status'] . '"><label></label></div>';
                } elseif ($colName == 'sr_no') {
                    $_data = $i; // Generate serial number
                } elseif ($colName == 'lastreply') {
                    if ($aRow[$colName] == null) {
                        $_data = _l('ticket_no_reply_yet');
                    } else {
                        $_data = e(_dt($aRow[$colName]));
                    }
                } elseif ($colName == 'subject') {
                    // Ticket is assigned
                    if ($aRow['assigned'] != 0) {
                        $_data = e($aRow['subject']) . '<a href="' . admin_url('profile/' . $aRow['assigned']) . '" data-toggle="tooltip" title="' . e(get_staff_full_name($aRow['assigned'])) . '" class="pull-left mright5">' . staff_profile_image($aRow['assigned'], [
                            'staff-profile-image-xs',
                        ]) . '</a>';
                    } else {
                        $_data = e($aRow['subject']);
                    }

                    $url   = admin_url('tickets/ticket/' . $aRow['ticketid']);
                    $_data = '<a href="' . $url . '?tab=settings" class="valign">' . $_data . '</a>';
                    $_data .= '<div class="row-options">';
                    if (staff_can('edit',  'rfi_tracker')) {
                        $_data .= '<a href="' . $url . '?tab=settings">' . _l('view') . '</a>';
                        $_data .= ' | <a href="' . $url . '">' . _l('edit') . '</a>';
                    }
                    $_data .= ' | <a href="' . get_ticket_public_url($aRow) . '" target="_blank">' . _l('view_public_form') . '</a>';
                    if (staff_can('delete',  'rfi_tracker')) {
                        $_data .= ' | <a href="' . admin_url('tickets/delete/' . $aRow['ticketid']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }
                    $_data .= '</div>';
                } elseif ($j == $tagsColumns) {
                    $_data = render_tags($aRow['tags']);
                } elseif ($j == $contactColumn) {
                    if ($aRow['userid'] != 0) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid'] . '?group=contacts') . '">' . e($aRow['contact_full_name']);
                        if (!empty($aRow['company'])) {
                            $_data .= ' (' . e($aRow['company']) . ')';
                        }
                        $_data .= '</a>';
                    } else {
                        $_data = e($aRow['ticket_opened_by_name']);
                    }
                } elseif ($colName == 'status') {
                    $_data = '<span class="label ticket-status-' . $aRow['status'] . '" style="border:1px solid ' . adjust_hex_brightness($aRow['statuscolor'], 0.4) . '; color:' . $aRow['statuscolor'] . ';background: ' . adjust_hex_brightness($aRow['statuscolor'], 0.04) . ';">' . e(ticket_status_translate($aRow['status'])) . '</span>';
                } elseif ($colName == 'date') {
                    $_data = e(_dt($aRow['tbltickets.date']));
                } elseif ($colName == 'priority') {
                    $_data = e(ticket_priority_translate($aRow['priority']));
                } elseif ($colName == '2') {
                    $_data = '<div class="btn-group mright5">
                       <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="fa fa-file-pdf"></i><span class="caret"></span></a>
                       <ul class="dropdown-menu dropdown-menu-right">
                          <li class="hidden-xs"><a href="'. admin_url('tickets/pdf/'.$aRow['ticketid'].'?output_type=I').'">'. _l('view_pdf').'</a></li>
                          <li class="hidden-xs"><a href="'. admin_url('tickets/pdf/'.$aRow['ticketid'].'?output_type=I').'" target="_blank">'. _l('view_pdf_in_new_window').'</a></li>
                          <li><a href="'.admin_url('tickets/pdf/'.$aRow['ticketid']).'">'. _l('download').'</a></li>
                       </ul>
                       </div>';
                } else {
                    // Handle custom fields and other columns
                    if (isset($aRow[$colName])) {
                        if (strpos($colName, 'date_picker_') !== false) {
                            $_data = (strpos($aRow[$colName], ' ') !== false ? _dt($aRow[$colName]) : _d($aRow[$colName]));
                        } else {
                            $_data = e($aRow[$colName]);
                        }
                    } else {
                        $_data = '';
                    }
                }

                $row[] = $_data;

                if ($aRow['adminread'] == 0) {
                    $row['DT_RowClass'] = 'text-danger';
                }
            }

            if (isset($row['DT_RowClass'])) {
                $row['DT_RowClass'] .= ' has-row-options';
            } else {
                $row['DT_RowClass'] = 'has-row-options';
            }

            $row = hooks()->apply_filters('admin_tickets_table_row_data', $row, $aRow);
            $output['aaData'][] = $row;
            $i++; // Increment serial number counter
        }

        return $output;
    })->setRules($rules);