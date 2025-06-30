<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $sGroupBy group results
 * @return array
 */
function data_tables_init($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [], $having = '', $module = '')
{
    $CI          = &get_instance();
    $data      = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = '';
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = 'LIMIT ' . intval($CI->input->post('start')) . ', ' . intval($CI->input->post('length'));
    }

    $allColumns = [];

    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (startsWith($_column[0], db_prefix())) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
                    array_push($allColumns, $_prefix);
                } else {
                    array_push($allColumns, $column);
                }
            } else {
                array_push($allColumns, $_column[0]);
            }
        } else {
            array_push($allColumns, $column);
        }
    }

    /*
     * Ordering
     */
    $nullColumnsAsLast = get_null_columns_that_should_be_sorted_as_last();

    $sOrder = '';
    if ($CI->input->post('order')) {
        $sOrder = 'ORDER BY ';
        foreach ($CI->input->post('order') as $key => $val) {
            $columnName = $aColumns[intval($data['order'][$key]['column'])];
            $dir        = strtoupper($data['order'][$key]['dir']);
            $type       = $data['order'][$key]['type'] ?? null;

            // Security
            if (!in_array($dir, ['ASC', 'DESC'])) {
                $dir = 'ASC';
            }

            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            // first checking is for eq tablename.column name
            // second checking there is already prefixed table name in the column name
            // this will work on the first table sorting - checked by the draw parameters
            // in future sorting user must sort like he want and the duedates won't be always last
            if ((in_array($sTable . '.' . $columnName, $nullColumnsAsLast)
                || in_array($columnName, $nullColumnsAsLast))) {
                $sOrder .= $columnName . ' IS NULL ' . $dir . ', ' . $columnName;
            } else {
                // Custom fields sorting support for number type custom fields
                if ($type === 'number') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'CAST(' . $columnName . ' as SIGNED)', $sTable);
                } elseif ($type === 'date_picker') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'CAST(' . $columnName . ' as DATE)', $sTable);
                } elseif ($type === 'date_picker_time') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'CAST(' . $columnName . ' as DATETIME)', $sTable);
                } elseif ($module == 'vendor_billing_tracker' && $columnName == 'tblpur_vendor.company') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'TRIM(LOWER(' . $columnName . '))', $sTable);
                } else {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', $columnName, $sTable);
                }
            }

            $sOrder .= ' ' . $dir . ', ';
        }

        if (trim($sOrder) == 'ORDER BY') {
            $sOrder = '';
        }
        // If the $module parameter is set, add custom ordering
        if (!empty($module)) {
            $additionalOrder = '';

            // Add specific ordering logic based on the module value
            if ($module === 'warehouse') {
                $additionalOrder = db_prefix() . 'inventory_manage.inventory_number DESC, ';
            } elseif ($module === 'tasks') {
                $additionalOrder = db_prefix() . 'tasks.status ASC, ';
            }

            // Prepend additional order conditions
            $sOrder = 'ORDER BY ' . $additionalOrder . ltrim($sOrder, 'ORDER BY ');
        }
        $sOrder = rtrim($sOrder, ', ');

        if (
            get_option('save_last_order_for_tables') == '1'
            && $CI->input->post('last_order_identifier')
            && $CI->input->post('order')
        ) {
            // https://stackoverflow.com/questions/11195692/json-encode-sparse-php-array-as-json-array-not-json-object

            $indexedOnly = [];
            foreach ($CI->input->post('order') as $row) {
                $indexedOnly[] = array_values($row);
            }

            $meta_name = $CI->input->post('last_order_identifier') . '-table-last-order';

            update_staff_meta(get_staff_user_id(), $meta_name, json_encode($indexedOnly, JSON_NUMERIC_CHECK));
        }
    }
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = '';
    if ((isset($data['search'])) && $data['search']['value'] != '') {
        $search_value = $data['search']['value'];
        $search_value = trim($search_value);

        $sWhere             = 'WHERE (';
        $sMatchCustomFields = [];

        // Not working, do not use it
        $useMatchForCustomFieldsTableSearch = hooks()->apply_filters('use_match_for_custom_fields_table_search', 'false');

        for ($i = 0; $i < count($aColumns); $i++) {
            $columnName = $aColumns[$i];
            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            if (stripos($columnName, 'AVG(') === false && stripos($columnName, 'SUM(') === false) {
                if (($data['columns'][$i]) && $data['columns'][$i]['searchable'] == 'true') {
                    if (isset($searchAs[$i])) {
                        $columnName = $searchAs[$i];
                    }

                    // Custom fields values are FULLTEXT and should be searched with MATCH
                    // Not working ATM
                    if ($useMatchForCustomFieldsTableSearch === 'true' && startsWith($columnName, 'ctable_')) {
                        $sMatchCustomFields[] = $columnName;
                    } else {
                        $sWhere .= 'convert(' . $columnName . ' USING utf8)' . " LIKE '%" . $CI->db->escape_like_str($search_value) . "%' ESCAPE '!' OR ";
                    }
                }
            }
        }

        if (count($sMatchCustomFields) > 0) {
            $s = $CI->db->escape_str($search_value);
            foreach ($sMatchCustomFields as $matchCustomField) {
                $sWhere .= "MATCH ({$matchCustomField}) AGAINST (CONVERT(BINARY('{$s}') USING utf8)) OR ";
            }
        }

        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                if (strpos($searchAdditionalField, ' as ') !== false) {
                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');
                }

                if (stripos($columnName, 'AVG(') === false && stripos($columnName, 'SUM(') === false) {
                    // Use index
                    $sWhere .= 'convert(' . $searchAdditionalField . ' USING utf8)' . " LIKE '%" . $CI->db->escape_like_str($search_value) . "%'ESCAPE '!' OR ";
                }
            }
        }

        $sWhere = substr_replace($sWhere, '', -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere      = 'WHERE (';

        foreach ($aColumns as $i => $column) {
            if (isset($data['columns'][$i]) && $data['columns'][$i]['searchable'] == 'true') {
                $search_value = $data['columns'][$i]['search']['value'];
                $columnName = $column;

                if (strpos($columnName, ' as ') !== false) {
                    $columnName = strbefore($columnName, ' as');
                }

                if ($search_value != '') {
                    // Add condition for current column
                    $likeClause = $CI->db->escape_like_str($search_value);
                    $sWhere .= "convert($columnName USING utf8) LIKE '%$likeClause%' ESCAPE '!' OR ";

                    // Process additional select fields if any
                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= "convert($searchAdditionalField USING utf8) LIKE '%$likeClause%' ESCAPE '!' OR ";
                        }
                    }

                    $searchFound++;
                }
            }
        }

        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, '', -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }

    /*
     * SQL queries
     * Get data to display
     */
    $additionalColumns = '';
    if (count($additionalSelect) > 0) {
        $additionalColumns = ',' . implode(',', $additionalSelect);
    }

    $where = implode(' ', $where);

    if ($sWhere == '') {
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }

    $join = implode(' ', $join);

    $havingSet = '';
    if (!empty($having)) {
        $havingSet = 'HAVING ' . $having;
    }

   $resultQuery = '
    SELECT ' . str_replace(' , ', ' ', implode(', ', $allColumns)) . ' ' . $additionalColumns . "
    FROM $sTable
    " . $join . "
    $sWhere
    " . $where . "
    $sGroupBy
    $havingSet
    $sOrder
    $sLimit
    ";
    $rResult = hooks()->apply_filters(
        'datatables_sql_query_results',
        $CI->db->query($resultQuery)->result_array(),
        [
            'table' => $sTable,
            'limit' => $sLimit,
            'order' => $sOrder,
        ]
    );

    /* Data set length after filtering */
    $iFilteredTotal = $CI->db->query("
        SELECT COUNT(*) as iFilteredTotal
        FROM $sTable
        " . $join . "
        $sWhere
        " . $where . "
        $sGroupBy
    ")->row()->iFilteredTotal;

    if (startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }

    /* Total data set length */
    $iTotal = $CI->db->query("SELECT COUNT(*) as iTotal from $sTable $join $where")->row()->iTotal;

    return [
        'rResult' => $rResult,
        'output'  => [
            'draw'                 => $data['draw'] ? intval($data['draw']) : 0,
            'iTotalRecords'        => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData'               => [],
        ],
    ];
}

function data_tables_init_union($aColumns, $sIndexColumn, $combinedTables, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [], $having = '', $module = '')
{
    $CI = &get_instance();
    $data = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = '';
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = 'LIMIT ' . intval($CI->input->post('start')) . ', ' . intval($CI->input->post('length'));
    }

    // Combine `tblpur_orders` and `tblwo_orders` into one derived table with vendor `company` and `kind`
    $sTable = "(
        SELECT DISTINCT
            po.id,
            po.aw_unw_order_status as aw_unw_order_status,
            po.pur_order_number AS order_number,
            po.pur_order_name AS order_name,
            po.rli_filter,
            pv.company AS vendor,
            pv.userid AS vendor_id,
            po.order_date,
            po.completion_date,
            po.budget,
            po.order_value,
            po.total AS total,
            IFNULL(co_sum.co_total, 0) AS co_total,
            (po.subtotal + IFNULL(co_sum.co_total, 0)) AS total_rev_contract_value, 
            po.anticipate_variation,
            (IFNULL(po.anticipate_variation, 0) + (po.subtotal + IFNULL(co_sum.co_total, 0))) AS cost_to_complete,
            COALESCE(inv_po_sum.vendor_submitted_amount_without_tax, 0) AS vendor_submitted_amount_without_tax,
            po.group_pur,
            po.kind,
            po.remarks AS remarks,
            po.subtotal as subtotal,
            pr.name as project,
            pr.id as project_id,
            'pur_orders' AS source_table
        FROM tblpur_orders po
        LEFT JOIN tblpur_vendor pv ON pv.userid = po.vendor
        LEFT JOIN (
            SELECT po_order_id, SUM(co_value) AS co_total
            FROM tblco_orders
            WHERE po_order_id IS NOT NULL
            GROUP BY po_order_id
        ) AS co_sum ON co_sum.po_order_id = po.id
        LEFT JOIN tblprojects pr ON pr.id = po.project
        LEFT JOIN (
        SELECT
            pur_order,
            SUM(vendor_submitted_amount_without_tax) AS vendor_submitted_amount_without_tax
            FROM tblpur_invoices
            WHERE pur_order IS NOT NULL AND payment_status IN (5,6,7)
            GROUP BY pur_order
        ) AS inv_po_sum
            ON inv_po_sum.pur_order = po.id
    
        UNION ALL
    
        SELECT DISTINCT
            wo.id,
            wo.aw_unw_order_status as aw_unw_order_status,
            wo.wo_order_number AS order_number,
            wo.wo_order_name AS order_name,
            wo.rli_filter,
            pv.company AS vendor,
            pv.userid AS vendor_id,
            wo.order_date,
            wo.completion_date,
            wo.budget,
            wo.order_value,
            wo.total AS total,
            IFNULL(co_sum.co_total, 0) AS co_total,
            (wo.subtotal + IFNULL(co_sum.co_total, 0)) AS total_rev_contract_value,
            wo.anticipate_variation,
            (IFNULL(wo.anticipate_variation, 0) + (wo.subtotal + IFNULL(co_sum.co_total, 0))) AS cost_to_complete,
            COALESCE(inv_wo_sum.vendor_submitted_amount_without_tax, 0) AS vendor_submitted_amount_without_tax,
            wo.group_pur,
            wo.kind,
            wo.remarks AS remarks,
            wo.subtotal as subtotal,
            pr.name as project,
            pr.id as project_id,
            'wo_orders' AS source_table
        FROM tblwo_orders wo
        LEFT JOIN tblpur_vendor pv ON pv.userid = wo.vendor
        LEFT JOIN (
            SELECT wo_order_id, SUM(co_value) AS co_total
            FROM tblco_orders
            WHERE wo_order_id IS NOT NULL
            GROUP BY wo_order_id
        ) AS co_sum ON co_sum.wo_order_id = wo.id
        LEFT JOIN tblprojects pr ON pr.id = wo.project
        LEFT JOIN (
        SELECT
            wo_order,
            SUM(vendor_submitted_amount_without_tax) AS vendor_submitted_amount_without_tax
            FROM tblpur_invoices
            WHERE wo_order IS NOT NULL AND payment_status IN (5,6,7)
            GROUP BY wo_order
        ) AS inv_wo_sum
            ON inv_wo_sum.wo_order = wo.id
    
        UNION ALL
    
        SELECT DISTINCT
            t.id,
            t.aw_unw_order_status as aw_unw_order_status,
            t.pur_order_number AS order_number,
            t.pur_order_name AS order_name,
            t.rli_filter,
            pv.company AS vendor,
            pv.userid AS vendor_id,
            t.order_date,
            t.completion_date,
            t.budget,
            t.order_value,
            t.total AS total,
            t.co_total AS co_total,
            (t.total + IFNULL(t.co_total, 0)) AS total_rev_contract_value,
            t.anticipate_variation,
            (IFNULL(t.anticipate_variation, 0) + (t.total + IFNULL(t.co_total, 0))) AS cost_to_complete,
            COALESCE(inv_ot_sum.vendor_submitted_amount_without_tax, 0) AS vendor_submitted_amount_without_tax,
            t.group_pur,
            t.kind,
            t.remarks AS remarks,
            t.subtotal as subtotal,
            pr.name as project,
            pr.id as project_id,
            'order_tracker' AS source_table
        FROM tblpur_order_tracker t
        LEFT JOIN tblpur_vendor pv ON pv.userid = t.vendor
        LEFT JOIN tblprojects pr ON pr.id = t.project
        LEFT JOIN (
        SELECT
            order_tracker_id ,
            SUM(vendor_submitted_amount_without_tax) AS vendor_submitted_amount_without_tax
            FROM tblpur_invoices
            WHERE order_tracker_id  IS NOT NULL AND payment_status IN (5,6,7)
            GROUP BY order_tracker_id 
        ) AS inv_ot_sum
            ON inv_ot_sum.order_tracker_id = t.id
    ) AS combined_orders";


    $allColumns = [];
    foreach ($aColumns as $column) {
        if (strpos($column, ' as ') !== false) {
            // Extract alias and real column name
            $aliasColumn = strbefore($column, ' as');
            $aliasName = strafter($column, ' as ');
            $allColumns[] = "$aliasColumn AS $aliasName";
        } else {
            $allColumns[] = $column;
        }
    }

    /*
     * Ordering
     */
    $sOrder = '';
    if ($CI->input->post('order')) {
        $sOrder = 'ORDER BY ';
        foreach ($CI->input->post('order') as $key => $val) {
            $columnName = $aColumns[intval($data['order'][$key]['column'])];
            $dir = strtoupper($data['order'][$key]['dir']);
            $type = $data['order'][$key]['type'] ?? null;

            if (!in_array($dir, ['ASC', 'DESC'])) {
                $dir = 'ASC';
            }

            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            if ($type === 'text') {
                $sOrder .= "CONVERT($columnName USING utf8) COLLATE utf8_general_ci $dir, ";
            } elseif ($type === 'number') {
                $sOrder .= "CAST($columnName AS SIGNED) $dir, ";
            } elseif ($type === 'date_picker') {
                $sOrder .= "CAST($columnName AS DATE) $dir, ";
            } elseif ($type === 'date_picker_time') {
                $sOrder .= "CAST($columnName AS DATETIME) $dir, ";
            } else {
                $sOrder .= "$columnName $dir, ";
            }
        }
        $sOrder = rtrim($sOrder, ', ');
    }

    /*
     * Filtering
     */
    $sWhere = '';
    if ((isset($data['search'])) && $data['search']['value'] != '') {
        $search_value = trim($data['search']['value']);
        $sWhere = 'WHERE (';

        foreach ($aColumns as $i => $column) {
            $columnName = strpos($column, ' as ') !== false ? strbefore($column, ' as') : $column;

            if (isset($data['columns'][$i]) && $data['columns'][$i]['searchable'] == 'true') {
                $sWhere .= "CONVERT($columnName USING utf8) LIKE '%" . $CI->db->escape_like_str($search_value) . "%' ESCAPE '!' OR ";
            }
        }

        $sWhere = substr($sWhere, 0, -3) . ')';
    }

    /*
     * SQL Queries
     */
    $additionalColumns = '';
    if (count($additionalSelect) > 0) {
        $additionalColumns = ',' . implode(',', $additionalSelect);
    }

    $where = implode(' ', $where);

    if ($sWhere == '') {
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }

    $join = implode(' ', $join);

    $havingSet = '';
    if (!empty($having)) {
        $havingSet = 'HAVING ' . $having;
    }

    $resultQuery = "
    SELECT " . str_replace(' , ', ' ', implode(', ', $allColumns)) . " $additionalColumns
    FROM $sTable
    $join
    $sWhere
    $where
    $sGroupBy
    $havingSet
    $sOrder
    $sLimit
    ";
    $rResult = hooks()->apply_filters(
        'datatables_sql_query_results',
        $CI->db->query($resultQuery)->result_array(),
        [
            'table' => $sTable,
            'limit' => $sLimit,
            'order' => $sOrder,
        ]
    );

    /* Data set length after filtering */
    $iFilteredTotal = $CI->db->query("
        SELECT COUNT(*) as iFilteredTotal
        FROM $sTable
        $join
        $sWhere
        $where
        $sGroupBy
    ")->row()->iFilteredTotal;

    if (startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }

    /* Total data set length */
    $iTotal = $CI->db->query("SELECT COUNT(*) as iTotal FROM $sTable $join $where")->row()->iTotal;

    return [
        'rResult' => $rResult,
        'output' => [
            'draw' => $data['draw'] ? intval($data['draw']) : 0,
            'iTotalRecords' => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData' => [],
        ],
    ];
}
function data_tables_init_union_unawarded($aColumns, $sIndexColumn, $combinedTables, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [], $having = '', $module = '')
{
    $CI = &get_instance();
    $data = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = '';
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = 'LIMIT ' . intval($CI->input->post('start')) . ', ' . intval($CI->input->post('length'));
    }

    $sTable = "(
        SELECT 
            p.id,
            pr.id as project_id,
            pr.name as project,
            p.estimate_id,
            budget_head,
            ig.name as budget_head_name,
            p.project_awarded_date,
            p.package_name,
            p.sdeposit_percent,
            p.sdeposit_value,
            p.total_package,
            p.awarded_value,
            p.kind,
            p.rli_filter,
            (
                (
                    SELECT SUM(unawarded_qty * unawarded_rate) 
                    FROM tblunawarded_budget_info 
                    WHERE tblunawarded_budget_info.estimate_id = p.estimate_id 
                      AND tblunawarded_budget_info.budget_head = p.budget_head
                ) - p.awarded_value + p.sdeposit_value
            ) AS pending_value_in_package,
            (
                CASE 
                    WHEN (
                        SELECT SUM(unawarded_qty * unawarded_rate) 
                        FROM tblunawarded_budget_info 
                        WHERE tblunawarded_budget_info.estimate_id = p.estimate_id 
                          AND tblunawarded_budget_info.budget_head = p.budget_head
                    ) > 0 
                    THEN 
                        (p.total_package / 
                            (
                                SELECT SUM(unawarded_qty * unawarded_rate) 
                                FROM tblunawarded_budget_info 
                                WHERE tblunawarded_budget_info.estimate_id = p.estimate_id 
                                  AND tblunawarded_budget_info.budget_head = p.budget_head
                            ) * 100)
                    ELSE 0
                END
            ) AS percentage_of_capex_used
        FROM tblestimate_package_info p
        LEFT JOIN tblestimates est ON est.id = p.estimate_id
        LEFT JOIN tblprojects pr ON pr.id = est.project_id
        LEFT JOIN tblitems_groups ig ON ig.id = p.budget_head
    ) AS combined_orders";

    $allColumns = [];
    foreach ($aColumns as $column) {
        if (strpos($column, ' as ') !== false) {
            // Extract alias and real column name
            $aliasColumn = strbefore($column, ' as');
            $aliasName = strafter($column, ' as ');
            $allColumns[] = "$aliasColumn AS $aliasName";
        } else {
            $allColumns[] = $column;
        }
    }

    /*
     * Ordering
     */
    $sOrder = '';
    if ($CI->input->post('order')) {
        $sOrder = 'ORDER BY ';
        foreach ($CI->input->post('order') as $key => $val) {
            $columnName = $aColumns[intval($data['order'][$key]['column'])];
            $dir = strtoupper($data['order'][$key]['dir']);
            $type = $data['order'][$key]['type'] ?? null;

            if (!in_array($dir, ['ASC', 'DESC'])) {
                $dir = 'ASC';
            }

            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            if ($type === 'text') {
                $sOrder .= "CONVERT($columnName USING utf8) COLLATE utf8_general_ci $dir, ";
            } elseif ($type === 'number') {
                $sOrder .= "CAST($columnName AS SIGNED) $dir, ";
            } elseif ($type === 'date_picker') {
                $sOrder .= "CAST($columnName AS DATE) $dir, ";
            } elseif ($type === 'date_picker_time') {
                $sOrder .= "CAST($columnName AS DATETIME) $dir, ";
            } else {
                $sOrder .= "$columnName $dir, ";
            }
        }
        $sOrder = rtrim($sOrder, ', ');
    }

    /*
     * Filtering
     */
    $sWhere = '';
    if ((isset($data['search'])) && $data['search']['value'] != '') {
        $search_value = trim($data['search']['value']);
        $sWhere = 'WHERE (';

        foreach ($aColumns as $i => $column) {
            $columnName = strpos($column, ' as ') !== false ? strbefore($column, ' as') : $column;

            if (isset($data['columns'][$i]) && $data['columns'][$i]['searchable'] == 'true') {
                $sWhere .= "CONVERT($columnName USING utf8) LIKE '%" . $CI->db->escape_like_str($search_value) . "%' ESCAPE '!' OR ";
            }
        }

        $sWhere = substr($sWhere, 0, -3) . ')';
    }

    /*
     * SQL Queries
     */
    $additionalColumns = '';
    if (count($additionalSelect) > 0) {
        $additionalColumns = ',' . implode(',', $additionalSelect);
    }

    $where = implode(' ', $where);

    if ($sWhere == '') {
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }

    $join = implode(' ', $join);

    $havingSet = '';
    if (!empty($having)) {
        $havingSet = 'HAVING ' . $having;
    }

    $resultQuery = "
    SELECT " . str_replace(' , ', ' ', implode(', ', $allColumns)) . " $additionalColumns
    FROM $sTable
    $join
    $sWhere
    $where
    $sGroupBy
    $havingSet
    $sOrder
    $sLimit
    ";
    $rResult = hooks()->apply_filters(
        'datatables_sql_query_results',
        $CI->db->query($resultQuery)->result_array(),
        [
            'table' => $sTable,
            'limit' => $sLimit,
            'order' => $sOrder,
        ]
    );

    /* Data set length after filtering */
    $iFilteredTotal = $CI->db->query("
        SELECT COUNT(*) as iFilteredTotal
        FROM $sTable
        $join
        $sWhere
        $where
        $sGroupBy
    ")->row()->iFilteredTotal;

    if (startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }

    /* Total data set length */
    $iTotal = $CI->db->query("SELECT COUNT(*) as iTotal FROM $sTable $join $where")->row()->iTotal;

    return [
        'rResult' => $rResult,
        'output' => [
            'draw' => $data['draw'] ? intval($data['draw']) : 0,
            'iTotalRecords' => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData' => [],
        ],
    ];
}


/**
 * Used in data_tables_init function to fix sorting problems when duedate is null
 * Null should be always last
 * @return array
 */
function get_null_columns_that_should_be_sorted_as_last()
{
    $columns = [
        db_prefix() . 'projects.deadline',
        db_prefix() . 'tasks.duedate',
        db_prefix() . 'contracts.dateend',
        db_prefix() . 'subscriptions.date_subscribed',
    ];

    return hooks()->apply_filters('null_columns_sort_as_last', $columns);
}
/**
 * Render table used for datatables
 * @param  array  $headings           [description]
 * @param  string $class              table class / added prefix table-$class
 * @param  array  $additional_classes
 * @return string                     formatted table
 */
/**
 * Render table used for datatables
 * @param  array   $headings
 * @param  string  $class              table class / add prefix eq.table-$class
 * @param  array   $additional_classes additional table classes
 * @param  array   $table_attributes   table attributes
 * @param  boolean $tfoot              includes blank tfoot
 * @return string
 */
function render_datatable($headings = [], $class = '', $additional_classes = [''], $table_attributes = [])
{
    $_additional_classes = '';
    $_table_attributes   = ' ';
    if (count($additional_classes) > 0) {
        $_additional_classes = ' ' . implode(' ', $additional_classes);
    }
    $CI      = &get_instance();
    $browser = $CI->agent->browser();
    $IEfix   = '';
    if ($browser == 'Internet Explorer') {
        $IEfix = 'ie-dt-fix';
    }

    foreach ($table_attributes as $key => $val) {
        $_table_attributes .= $key . '=' . '"' . $val . '" ';
    }

    $table = '<div class="' . $IEfix . '"><table' . $_table_attributes . 'class="dt-table-loading table table-' . $class . '' . $_additional_classes . '">';
    $table .= '<thead>';
    $table .= '<tr>';
    foreach ($headings as $heading) {
        if (!is_array($heading)) {
            $table .= '<th>' . $heading . '</th>';
        } else {
            $th_attrs = '';
            if (isset($heading['th_attrs'])) {
                foreach ($heading['th_attrs'] as $key => $val) {
                    $th_attrs .= $key . '=' . '"' . $val . '" ';
                }
            }
            $th_attrs = ($th_attrs != '' ? ' ' . $th_attrs : $th_attrs);
            $table .= '<th' . $th_attrs . '>' . $heading['name'] . '</th>';
        }
    }
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    $table .= '</table></div>';
    echo $table;
}

/**
 * Translated datatables language based on app languages
 * This feature is used on both admin and customer area
 * @return array
 */
function get_datatables_language_array()
{
    $lang = [
        'emptyTable'        => preg_replace("/{(\d+)}/", _l('dt_entries'), _l('dt_empty_table')),
        'info'              => preg_replace("/{(\d+)}/", _l('dt_entries'), _l('dt_info')),
        'infoEmpty'         => preg_replace("/{(\d+)}/", _l('dt_entries'), _l('dt_info_empty')),
        'infoFiltered'      => preg_replace("/{(\d+)}/", _l('dt_entries'), _l('dt_info_filtered')),
        'lengthMenu'        => '_MENU_',
        'loadingRecords'    => _l('dt_loading_records'),
        'processing'        => '<div class="dt-loader"></div>',
        'search'            => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-search"></span></span>',
        'searchPlaceholder' => _l('dt_search'),
        'zeroRecords'       => _l('dt_zero_records'),
        'paginate'          => [
            'first'    => _l('dt_paginate_first'),
            'last'     => _l('dt_paginate_last'),
            'next'     => _l('dt_paginate_next'),
            'previous' => _l('dt_paginate_previous'),
        ],
        'aria' => [
            'sortAscending'  => _l('dt_sort_ascending'),
            'sortDescending' => _l('dt_sort_descending'),
        ],
    ];

    return hooks()->apply_filters('datatables_language_array', $lang);
}

/**
 * Function that will parse filters for datatables and will return based on a couple conditions.
 * The returned result will be pushed inside the $where variable in the table SQL
 * @param  array $filter
 * @return string
 */
function prepare_dt_filter($filter)
{
    $filter = implode(' ', $filter);
    if (startsWith($filter, 'AND')) {
        $filter = substr($filter, 3);
    } elseif (startsWith($filter, 'OR')) {
        $filter = substr($filter, 2);
    }

    return $filter;
}
/**
 * Get table last order
 * @param  string $tableID table unique identifier id
 * @return string
 */
function get_table_last_order($tableID)
{
    return htmlentities(get_staff_meta(get_staff_user_id(), $tableID . '-table-last-order'));
}

function data_tables_purchase_tracker_init($aColumns, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $having = '')
{
    $CI = &get_instance();
    $data = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = '';
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = 'LIMIT ' . intval($CI->input->post('start')) . ', ' . intval($CI->input->post('length'));
    }

    $sTable = "( SELECT *, CASE WHEN type = 1 THEN CASE WHEN ( SELECT COALESCE(SUM(po_quantities), 0) FROM tblgoods_receipt_detail WHERE goods_receipt_id = combined_orders.id ) = ( SELECT COALESCE(SUM(quantities), 0) FROM tblgoods_receipt_detail WHERE goods_receipt_id = combined_orders.id ) THEN '2' WHEN ( SELECT COALESCE(SUM(quantities), 0) FROM tblgoods_receipt_detail WHERE goods_receipt_id = combined_orders.id ) = 0 THEN '0' WHEN ( SELECT COALESCE(SUM(quantities), 0) FROM tblgoods_receipt_detail WHERE goods_receipt_id = combined_orders.id ) > 0 THEN '1' ELSE '0' END ELSE '0' END AS delivery_status FROM ( SELECT id, goods_receipt_code, supplier_code AS supplier_name, buyer_id, kind, pr_order_id, date_add, approval, id AS pdf_id, 1 AS type, project FROM tblgoods_receipt UNION ALL SELECT id, '' AS goods_receipt_code, vendor AS supplier_name, id AS buyer_id, kind, id AS pr_order_id, datecreated AS date_add, approve_status AS approval, id AS pdf_id, 2 AS type, project FROM tblpur_orders WHERE goods_id = 0 ) AS combined_orders ) AS final_result";

    $allColumns = [];
    foreach ($aColumns as $column) {
        if (strpos($column, ' as ') !== false) {
            // Extract alias and real column name
            $aliasColumn = strbefore($column, ' as');
            $aliasName = strafter($column, ' as ');
            $allColumns[] = "$aliasColumn AS $aliasName";
        } else {
            $allColumns[] = $column;
        }
    }

    /*
     * Ordering
     */
    $sOrder = '';
    if ($CI->input->post('order')) {
        $sOrder = 'ORDER BY ';
        foreach ($CI->input->post('order') as $key => $val) {
            $columnName = $aColumns[intval($data['order'][$key]['column'])];
            $dir = strtoupper($data['order'][$key]['dir']);
            $type = $data['order'][$key]['type'] ?? null;

            if (!in_array($dir, ['ASC', 'DESC'])) {
                $dir = 'ASC';
            }

            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            if ($type === 'text') {
                $sOrder .= "CONVERT($columnName USING utf8) COLLATE utf8_general_ci $dir, ";
            } elseif ($type === 'number') {
                $sOrder .= "CAST($columnName AS SIGNED) $dir, ";
            } elseif ($type === 'date_picker') {
                $sOrder .= "CAST($columnName AS DATE) $dir, ";
            } elseif ($type === 'date_picker_time') {
                $sOrder .= "CAST($columnName AS DATETIME) $dir, ";
            } else {
                $sOrder .= "$columnName $dir, ";
            }
        }
        $sOrder = rtrim($sOrder, ', ');
    }

    /*
     * Filtering
     */
    $sWhere = '';
    if ((isset($data['search'])) && $data['search']['value'] != '') {
        $search_value = trim($data['search']['value']);
        $sWhere = 'WHERE (';

        foreach ($aColumns as $i => $column) {
            $columnName = strpos($column, ' as ') !== false ? strbefore($column, ' as') : $column;

            if (isset($data['columns'][$i]) && $data['columns'][$i]['searchable'] == 'true') {
                $sWhere .= "CONVERT($columnName USING utf8) LIKE '%" . $CI->db->escape_like_str($search_value) . "%' ESCAPE '!' OR ";
            }
        }

        $sWhere = substr($sWhere, 0, -3) . ')';
    }

    /*
     * SQL Queries
     */
    $additionalColumns = '';
    if (count($additionalSelect) > 0) {
        $additionalColumns = ',' . implode(',', $additionalSelect);
    }

    $where = implode(' ', $where);

    if ($sWhere == '') {
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }

    $join = implode(' ', $join);

    $havingSet = '';
    if (!empty($having)) {
        $havingSet = 'HAVING ' . $having;
    }

    $resultQuery = "
    SELECT " . str_replace(' , ', ' ', implode(', ', $allColumns)) . " $additionalColumns
    FROM $sTable
    $join
    $sWhere
    $where
    $sGroupBy
    $havingSet
    $sOrder
    $sLimit
    ";

    $rResult = hooks()->apply_filters(
        'datatables_sql_query_results',
        $CI->db->query($resultQuery)->result_array(),
        [
            'table' => $sTable,
            'limit' => $sLimit,
            'order' => $sOrder,
        ]
    );

    /* Data set length after filtering */
    $iFilteredTotal = $CI->db->query("
        SELECT COUNT(*) as iFilteredTotal
        FROM $sTable
        $join
        $sWhere
        $where
        $sGroupBy
    ")->row()->iFilteredTotal;

    if (startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }

    /* Total data set length */
    $iTotal = $CI->db->query("SELECT COUNT(*) as iTotal FROM $sTable $join $where")->row()->iTotal;

    return [
        'rResult' => $rResult,
        'output' => [
            'draw' => $data['draw'] ? intval($data['draw']) : 0,
            'iTotalRecords' => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData' => [],
        ],
    ];
}
