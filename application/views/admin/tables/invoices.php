<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('invoices_model');

return App_table::find('invoices')
    ->outputUsing(function ($params) {
        extract($params);
        $project_id = $this->ci->input->post('project_id');

        $aColumns = [
            '1',
            'number',
            'title',
            'total',
            // 'YEAR(date) as year',
            'total_left_to_pay',
            'date',
            get_sql_select_client_company(),
            db_prefix() . 'projects.name as project_name',
            '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'invoices.id and rel_type="invoice" ORDER by tag_order ASC) as tags',
            'duedate',
            db_prefix() . 'invoices.status',
        ];

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'invoices';

        $join = [
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
            'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency',
            'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'invoices.project_id',
        ];

        $custom_fields = get_table_custom_fields('invoice');

        foreach ($custom_fields as $key => $field) {
            $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);

            array_push($customFieldsColumns, $selectAs);
            array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
            array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'invoices.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
        }

        $where  = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        if ($clientid != '') {
            array_push($where, 'AND ' . db_prefix() . 'invoices.clientid=' . $this->ci->db->escape_str($clientid));
        }

        if ($project_id) {
            array_push($where, 'AND project_id=' . $this->ci->db->escape_str($project_id));
        }

        if (staff_cant('view', 'invoices')) {
            $userWhere = 'AND ' . get_invoices_where_sql_for_staff(get_staff_user_id());
            array_push($where, $userWhere);
        }

        if(get_default_project()) {
            array_push($where, 'AND ' . db_prefix() . 'invoices.project_id = '.get_default_project().'');
        }

        $aColumns = hooks()->apply_filters('invoices_table_sql_columns', $aColumns);

        // Fix for big queries. Some hosting have max_join_limit
        if (count($custom_fields) > 4) {
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            db_prefix() . 'invoices.id',
            db_prefix() . 'invoices.clientid',
            db_prefix() . 'currencies.name as currency_name',
            'project_id',
            'hash',
            'recurring',
            'deleted_customer_name',
            'title',
            '(
                SELECT 
                    ROUND(
                        inv.total
                        - IFNULL((SELECT SUM(p.amount) FROM ' . db_prefix() . 'invoicepaymentrecords p WHERE p.invoiceid = inv.id), 0)
                        - IFNULL((SELECT SUM(c.amount) FROM ' . db_prefix() . 'credits c WHERE c.invoice_id = inv.id), 0),
                    2)
                FROM ' . db_prefix() . 'invoices inv
                WHERE inv.id = ' . db_prefix() . 'invoices.id
            ) AS total_left_to_pay',
        ]);
        $output  = $result['output'];
        $rResult = $result['rResult'];
        $sr = 1 + $this->ci->input->post('start');

        $footer_data = [
            'total_invoice_amount' => 0,
            'total_invoice_amount_due' => 0,
        ];
        foreach ($rResult as $aRow) {
            $row = [];

            $numberOutput = '';
            $total_left_to_pay = get_invoice_total_left_to_pay($aRow['id'], $aRow['total']);
            // If is from client area table
            if (is_numeric($clientid) || $project_id) {
                $numberOutput = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . e(format_invoice_number($aRow['id'])) . '</a>';
            } else {
                $numberOutput = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" onclick="init_invoice(' . $aRow['id'] . '); small_table_full_view(); return false;">' . e(format_invoice_number($aRow['id'])) . '</a>';
            }

            if ($aRow['recurring'] > 0) {
                $numberOutput .= '<br /><span class="label label-primary inline-block tw-mt-1"> ' . _l('invoice_recurring_indicator') . '</span>';
            }

            $numberOutput .= '<div class="row-options">';

            $numberOutput .= '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" onclick="init_invoice(' . $aRow['id'] . '); small_table_full_view(); return false;">' . _l('view') . '</a>';
            if (staff_can('edit',  'invoices')) {
                $numberOutput .= ' | <a href="' . admin_url('invoices/invoice/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            $numberOutput .= '</div>';
            $row[] = $sr++;
            $row[] = $numberOutput;

            $row[] = $aRow['title'];

            $row[] = e(app_format_money($aRow['total'], $aRow['currency_name']));

            $row[] = e(app_format_money($aRow['total_left_to_pay'], $aRow['currency_name']));

            // $row[] = e($aRow['year']);

            $row[] = e(_d($aRow['date']));

            if (empty($aRow['deleted_customer_name'])) {
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . e($aRow['company']) . '</a>';
            } else {
                $row[] = e($aRow['deleted_customer_name']);
            }

            $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . e($aRow['project_name']) . '</a>';;

            $row[] = render_tags($aRow['tags']);

            $row[] = e(_d($aRow['duedate']));

            $row[] = format_invoice_status($aRow[db_prefix() . 'invoices.status']);

            // Custom fields add values
            foreach ($customFieldsColumns as $customFieldColumn) {
                $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
            }

            $row['DT_RowClass'] = 'has-row-options';

            $row = hooks()->apply_filters('invoices_table_row_data', $row, $aRow);

            $footer_data['total_invoice_amount'] += $aRow['total'];
            $footer_data['total_invoice_amount_due'] += $total_left_to_pay;
            $output['aaData'][] = $row;
        }

        foreach ($footer_data as $key => $total) {
            $footer_data[$key] = app_format_money($total, $aRow['currency_name']);
        }
        $output['sums'] = $footer_data;
        return $output;
    })->setRules([
        App_table_filter::new('number', 'NumberRule')->label(_l('invoice_add_edit_number')),
        App_table_filter::new('total', 'NumberRule')->label(_l('invoice_total')),
        App_table_filter::new('subtotal', 'NumberRule')->label(_l('invoice_subtotal')),
        App_table_filter::new('date', 'DateRule')->label(_l('invoice_add_edit_date')),
        App_table_filter::new('duedate', 'DateRule')
            ->label(_l('invoice_dt_table_heading_duedate'))
            ->withEmptyOperators(),
        App_table_filter::new('sent', 'BooleanRule')->label(_l('estimate_status_sent'))->raw(function ($value) {
            if ($value == '1') {
                return 'sent = 1';
            } else {
                return 'sent = 0 and ' . db_prefix() . 'invoices.status NOT IN (' . Invoices_model::STATUS_PAID . ',' . Invoices_model::STATUS_CANCELLED . ')';
            }
        }),
        App_table_filter::new('sale_agent', 'SelectRule')->label(_l('sale_agent_string'))
            ->withEmptyOperators()
            ->emptyOperatorValue(0)
            ->isVisible(fn () => staff_can('view', 'invoices'))
            ->options(function ($ci) {
                return collect($ci->invoices_model->get_sale_agents())->map(function ($data) {
                    return [
                        'value' => $data['sale_agent'],
                        'label' => get_staff_full_name($data['sale_agent'])
                    ];
                })->all();
            }),

        App_table_filter::new('status', 'MultiSelectRule')
            ->label(_l('invoice_dt_table_heading_status'))
            ->options(function ($ci) {
                return collect($ci->invoices_model->get_statuses())->map(fn ($status) => [
                    'value' => (string) $status,
                    'label' => format_invoice_status($status, '', false),
                ])->all();
            }),

        App_table_filter::new('year', 'MultiSelectRule')
            ->label(_l('year'))
            ->raw(function ($value, $operator) {
                if ($operator == 'in') {
                    return "YEAR(date) IN (" . implode(',', $value) . ")";
                } else {
                    return "YEAR(date) NOT IN (" . implode(',', $value) . ")";
                }
            })
            ->options(function ($ci) {
                return collect($ci->invoices_model->get_invoices_years())->map(fn ($data) => [
                    'value' => $data['year'],
                    'label' => $data['year'],
                ])->all();
            }),
        App_table_filter::new('recurring', 'BooleanRule')->label(_l('invoices_list_recurring'))->raw(function ($value) {
            return $value == '1' ? 'recurring > 0' : 'recurring = 0';
        }),
        App_table_filter::new('not_have_payment', 'BooleanRule')->label(_l('invoices_list_not_have_payment'))->raw(function ($value) {
            return '(' . db_prefix() . 'invoices.id ' . ($value == '1' ? 'NOT IN' : 'IN') . ' (SELECT invoiceid FROM ' . db_prefix() . 'invoicepaymentrecords) AND ' . db_prefix() . 'invoices.status != ' . Invoices_model::STATUS_CANCELLED . ')';
        }),
        App_table_filter::new('made_payment_by', 'MultiSelectRule')->label(str_replace(' %s', '', _l('invoices_list_made_payment_by')))->options(function ($ci) {
            return collect($ci->payment_modes_model->get('', [], true))->map(fn ($mode) => [
                'value' => $mode['id'],
                'label' => $mode['name'],
            ])->all();
        })->raw(function ($value, $operator, $sqlOperator) {
            $dbPrefix = db_prefix();
            $sqlOperator = $sqlOperator['operator'];

            return "({$dbPrefix}invoices.id IN (SELECT invoiceid FROM {$dbPrefix}invoicepaymentrecords WHERE paymentmode $sqlOperator ('" . implode("','", $value) . "')))";
        })
    ]);
