<?php
defined('BASEPATH') or exit('No direct script access allowed');

$month = $this->ci->input->post('month');

// --------------------------------------------------
// Build Date Range (8th -> 6th next month)
// --------------------------------------------------
$start = date('Y-m-08', strtotime($month . '-01'));
$end   = date('Y-m-06', strtotime($start . ' +1 month'));

// --------------------------------------------------
// STEP 0: CALCULATE AVG CLIENT EARNINGS
// --------------------------------------------------
$avg_daily_pl = 0;

$pl_data = $this->ci->db
    ->select('SUM(client_earnings) as total, COUNT(*) as cnt')
    ->where('month_year', $month)
    ->get('tbl_assar_main_sheet')
    ->row();



$investment_data = $this->ci->db
    ->select('SUM(investment) as total_investment')
    ->get('tblassar_clients')
    ->row();

$get_client_count = $this->ci->db
    ->select('COUNT(*) as cnt')
    ->get('tblassar_clients')
    ->row();


if ($pl_data && $get_client_count->cnt > 0) {
    $avg_daily_pl = $pl_data->total / $get_client_count->cnt;
}
if ($investment_data && $investment_data->total_investment > 0) {
    $avg_return_per = ($avg_daily_pl / $investment_data->total_investment) * 100;
}

// --------------------------------------------------
// STEP 2: AUTO-INSERT MISSING DATES
// --------------------------------------------------
$period = new DatePeriod(
    new DateTime($start),
    new DateInterval('P1D'),
    (new DateTime($end))->modify('+1 day')
);

foreach ($period as $dt) {

    $date = $dt->format('Y-m-d');

    $exists = $this->ci->db
        ->where('entry_date', $date)
        ->get(db_prefix() . 'daily_return_net')
        ->row();

    if (!$exists) {
        $this->ci->db->insert(db_prefix() . 'daily_return_net', [
            'entry_date' => $date,
            'return_per' => round($avg_return_per, 2),
            'actual_pl'  => round($avg_daily_pl, 2),
            'notes'      => ''
        ]);
    }
}

// --------------------------------------------------
// STEP 3: DATATABLE FETCH
// --------------------------------------------------
$aColumns = [
    'entry_date',
    'return_per',
    'actual_pl',
    'notes'
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'daily_return_net';

$where = [];
$where[] = 'AND entry_date BETWEEN "' . $start . '" AND "' . $end . '"';

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    [],
    $where,
    ['id']
);

$output  = $result['output'];
$rResult = $result['rResult'];

// --------------------------------------------------
// STEP 4: BUILD ROWS
// --------------------------------------------------
foreach ($rResult as $row) {

    $data = [];

    // Date
    $data[] = date('d M, Y', strtotime($row['entry_date']));

    // Return %
    $data[] = $row['return_per'];

    // Actual P&L
    $data[] = '<input type="number"
                step="0.01"
                class="form-control actual-pl"
                data-id="' . $row['id'] . '"
                data-date="' . $row['entry_date'] . '"
                value="' . $row['actual_pl'] . '">';

    // Notes
    $data[] = '<input type="text"
                class="form-control notes"
                data-id="' . $row['id'] . '"
                data-date="' . $row['entry_date'] . '"
                value="' . $row['notes'] . '">';

    $output['aaData'][] = $data;
}

echo json_encode($output);
die;
