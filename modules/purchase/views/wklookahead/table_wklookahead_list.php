<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . '_wklookahead_activities.activity',
    db_prefix() . '_wklookahead_activities.vendor_id',
    db_prefix() . '_wklookahead_activities.due_date',
    db_prefix() . '_wklookahead_activities.percentage',
];

$sIndexColumn = 'id';

$sTable = db_prefix() . '_wklookahead_activities';

$join = [
    'LEFT JOIN ' . db_prefix() . '_wklookahead ON ' .
        db_prefix() . '_wklookahead.id = ' .
        db_prefix() . '_wklookahead_activities.lookahead_id',

    'LEFT JOIN ' . db_prefix() . 'pur_vendor AS pur_vendors ON ' .
        'pur_vendors.userid = ' .
        db_prefix() . '_wklookahead_activities.vendor_id',
];

$where = [];

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    [
        db_prefix() . '_wklookahead_activities.id',
        db_prefix() . '_wklookahead_activities.activity',
        db_prefix() . '_wklookahead_activities.vendor_id',
        db_prefix() . '_wklookahead_activities.due_date',
        db_prefix() . '_wklookahead_activities.percentage',

        db_prefix() . '_wklookahead.week_start_date',

        'pur_vendors.company as vendor_name',

        // Day fields
        db_prefix() . '_wklookahead_activities.day_1',
        db_prefix() . '_wklookahead_activities.day_2',
        db_prefix() . '_wklookahead_activities.day_3',
        db_prefix() . '_wklookahead_activities.day_4',
        db_prefix() . '_wklookahead_activities.day_5',
        db_prefix() . '_wklookahead_activities.day_6',
        db_prefix() . '_wklookahead_activities.day_7',
        db_prefix() . '_wklookahead_activities.day_8',
        db_prefix() . '_wklookahead_activities.day_9',
        db_prefix() . '_wklookahead_activities.day_10',
        db_prefix() . '_wklookahead_activities.day_11',
        db_prefix() . '_wklookahead_activities.day_12',
        db_prefix() . '_wklookahead_activities.day_13',
        db_prefix() . '_wklookahead_activities.day_14',
        db_prefix() . '_wklookahead_activities.day_15',
        db_prefix() . '_wklookahead_activities.day_16',
        db_prefix() . '_wklookahead_activities.day_17',
        db_prefix() . '_wklookahead_activities.day_18',
        db_prefix() . '_wklookahead_activities.day_19',
        db_prefix() . '_wklookahead_activities.day_20',
        db_prefix() . '_wklookahead_activities.day_21',
    ]
);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];

    /*
     * ACTIVITY
     */

    $numberOutput = '';

    $numberOutput .= '<a href="' . admin_url('purchase/add_update_wklookahead/' . $aRow['id']) . '">'
        .$aRow['activity']
        . '</a>';


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
            'purchase/delete_activity/' . $aRow['id']
        ) .
        '" class="text-danger _delete">' .
        _l('delete') .
        '</a>';



    $numberOutput .= '</div>';

    $_data = $numberOutput;

    $row[] = $_data;
    /*
     * VENDOR
     */
    $row[] = !empty($aRow['vendor_name'])
        ? html_escape($aRow['vendor_name'])
        : '-';

    /*
     * FIND START DATE
     *
     * Find the first day_X which has value 1.
     */
    $startDate = '';

    if (!empty($aRow['week_start_date'])) {

        for ($day = 1; $day <= 21; $day++) {

            if ((int) $aRow['day_' . $day] === 1) {

                $startDate = date(
                    'Y-m-d',
                    strtotime(
                        $aRow['week_start_date'] . ' +' . ($day - 1) . ' days'
                    )
                );

                break;
            }
        }
    }

    /*
     * START DATE
     */
    $row[] = !empty($startDate)
        ? _d($startDate)
        : '-';

    /*
     * DUE DATE
     */
    $row[] = !empty($aRow['due_date'])
        ? _d($aRow['due_date'])
        : '-';

    /*
     * COMPLETE
     */
    $percentage = (float) $aRow['percentage'];

    $row[] = '
        <div class="progress" style="height:20px;margin-bottom:0;">
            <div class="progress-bar"
                 role="progressbar"
                 style="width:' . $percentage . '%;"
                 aria-valuenow="' . $percentage . '"
                 aria-valuemin="0"
                 aria-valuemax="100">
                ' . $percentage . '%
            </div>
        </div>
    ';

    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
