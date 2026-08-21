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
        db_prefix() . '_wklookahead_activities.id as activity_id',
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

    /*
     * FIND ALL DAYS WITH VALUE 1
     * Store all dates where day_X = 1
     * 
     * First, get the Monday of the week from week_start_date
     * Then day_1 = that Monday
     * day_2 = Monday + 1 day (Tuesday)
     * day_3 = Monday + 2 days (Wednesday)
     * ...
     * day_7 = Monday + 6 days (Sunday - Holiday)
     * day_8 = Monday + 7 days (Next Monday)
     * ...
     */
    $activityDates = [];
    $dayNumbers = [];

    if (!empty($aRow['week_start_date'])) {
        
        // Get the Monday of the week from the selected date
        $weekStartDate = new DateTime($aRow['week_start_date']);
        $dayOfWeek = $weekStartDate->format('N'); // 1 (Monday) to 7 (Sunday)
        
        // Calculate days to subtract to get Monday
        $daysToSubtract = $dayOfWeek - 1;
        
        // Set to Monday
        $weekStartDate->modify("-$daysToSubtract days");
        $mondayDate = $weekStartDate->format('Y-m-d');
        
        for ($day = 1; $day <= 21; $day++) {
            if ((int) $aRow['day_' . $day] === 1) {
                // day_1 is Monday, day_2 is Tuesday, etc.
                // So day_X is Monday + (X - 1) days
                $activityDates[] = date(
                    'Y-m-d',
                    strtotime(
                        $mondayDate . ' +' . ($day - 1) . ' days'
                    )
                );
                $dayNumbers[] = $day;
            }
        }
    }

    /*
     * If no dates found, show the activity with no start date
     */
    if (empty($activityDates)) {
        $row = [];
        
        /* 
         * ACTIVITY
         */
        $numberOutput = '';
        $numberOutput .= '<a href="' . admin_url('purchase/add_update_wklookahead/' . $aRow['activity_id']) . '">'
            . html_escape($aRow['activity'])
            . '</a>';

        $numberOutput .= '<div class="row-options">';
        $numberOutput .= '<a href="#" class="edit-activity" 
            data-id="' . $aRow['activity_id'] . '" 
            data-toggle="modal" 
            data-target="#newActivityModal">
            <i class="fa fa-pencil-square-o"></i> ' . _l('edit') . '
            </a>';

        $numberOutput .= ' | <a href="' .
            admin_url(
                'purchase/delete_activity/' . $aRow['activity_id']
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
         * START DATE (no start date)
         */
        $row[] = '-';

        /*
         * DUE DATE
         */
        $row[] = !empty($aRow['due_date'])
            ? date('d M, Y', strtotime($aRow['due_date']))
            : '-';

        /*
         * PERCENTAGE
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
        
        continue;
    }

    /*
     * FOR EACH DATE (repeat the row for each occurrence)
     */
    foreach ($activityDates as $index => $date) {
        $row = [];

        /* 
         * ACTIVITY (with occurrence number if repeated)
         */
        $numberOutput = '';
        $displayActivity = html_escape($aRow['activity']);
        
        // If activity repeats multiple times, show occurrence number
        if (count($activityDates) > 1) {
            $displayActivity .= ' <span class="badge badge-info">#' . ($index + 1) . '</span>';
        }

        $numberOutput .= ''
            . $displayActivity
            . '';

        $numberOutput .= '<div class="row-options">';
        $numberOutput .= '<a href="#" class="edit-activity" 
            data-id="' . $aRow['activity_id'] . '" 
            data-toggle="modal" 
            data-target="#newActivityModal">
            <i class="fa fa-pencil-square-o"></i> ' . _l('edit') . '
            </a>';

        $numberOutput .= ' | <a href="' .
            admin_url(
                'purchase/delete_activity/' . $aRow['activity_id']
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
         * START DATE (show the specific date for this occurrence)
         */
        $row[] = !empty($date)
            ? date('d M, Y', strtotime($date))
            : '-';

        /*
         * DUE DATE
         */
        $row[] = !empty($aRow['due_date'])
            ? date('d M, Y', strtotime($aRow['due_date']))
            : '-';

        /*
         * PERCENTAGE
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

        // Add a custom class for repeated rows to style them differently if needed
        if (count($activityDates) > 1) {
            $row['DT_RowClass'] .= ' repeated-activity';
        }

        $output['aaData'][] = $row;
    }
}