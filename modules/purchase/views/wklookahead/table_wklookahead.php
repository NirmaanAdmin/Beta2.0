<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tbl_wklookahead.week_start_date',
    'tbl_wklookahead.id',
];

$sIndexColumn = 'id';

$sTable = db_prefix() . '_wklookahead';

$join = [
    'LEFT JOIN ' . db_prefix() . 'projects ON ' .
        db_prefix() . 'projects.id = ' .
        db_prefix() . '_wklookahead.project_id',
];

$where = [];

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    [
        db_prefix() . '_wklookahead.id',
        db_prefix() . '_wklookahead.week_start_date',
        db_prefix() . '_wklookahead.project_id',
        db_prefix() . 'projects.name as project_name',
    ]
);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

    $row = [];

    /*
     * DATE + PROJECT
     */
    $numberOutput = '';

    $numberOutput .= '<a href="' . admin_url('purchase/add_update_wklookahead/' . $aRow['id']) . '">'
        . _d($aRow['week_start_date'])
        . '</a>';

    if (!empty($aRow['project_name'])) {

        $numberOutput .= '<div class="text-muted small mtop5">';

        $numberOutput .= '<i class="fa fa-building-o"></i> ';

        $numberOutput .= html_escape(
            $aRow['project_name']
        );

        $numberOutput .= '</div>';
    }


    $numberOutput .= '<div class="row-options">';



    $numberOutput .= ' <a href="' .
        admin_url(
            'purchase/add_update_wklookahead/' . $aRow['id']
        ) .
        '">' .
        _l('view') .
        '</a>';


    $numberOutput .= ' | <a href="' .
        admin_url(
            'purchase/add_update_wklookahead/' . $aRow['id']
        ) .
        '">' .
        _l('edit') .
        '</a>';





    $numberOutput .= ' | <a href="' .
        admin_url(
            'purchase/delete_wklookahead/' . $aRow['id']
        ) .
        '" class="text-danger _delete">' .
        _l('delete') .
        '</a>';



    $numberOutput .= '</div>';

    $_data = $numberOutput;

    $row[] = $_data;




    /*
     * PDF
     */
    $option = '';

    $option .= '<div class="btn-group pull-right">
    <a href="javascript:void(0)"
        class="btn btn-default dropdown-toggle"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false">
        <i class="fa fa-file-pdf"></i>';

    if (is_mobile()) {
        $option .= ' PDF';
    }

    $option .= ' <span class="caret"></span>
    </a>

    <ul class="dropdown-menu dropdown-menu-right">

        <li class="hidden-xs">
            <a href="' . admin_url('purchase/wklookahead_pdf/' . $aRow['id'] . '?output_type=I') . '">
                ' . _l('view_pdf') . '
            </a>
        </li>

        <li class="hidden-xs">
            <a href="' . admin_url('purchase/wklookahead_pdf/' . $aRow['id'] . '?output_type=I') . '" target="_blank">
                ' . _l('view_pdf_in_new_window') . '
            </a>
        </li>

        <li>
            <a href="' . admin_url('purchase/wklookahead_pdf/' . $aRow['id']) . '">
                ' . _l('download') . '
            </a>
        </li>

        <li>
            <a href="' . admin_url('purchase/wklookahead_pdf/' . $aRow['id'] . '?print=true') . '" target="_blank">
                ' . _l('print') . '
            </a>
        </li>

    </ul>

    </div>';
    $row[] = $option;

    $row[] = $option;


    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
