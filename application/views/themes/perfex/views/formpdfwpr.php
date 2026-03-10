<?php


defined('BASEPATH') or exit('No direct script access allowed');

$upload_path = FCPATH . 'uploads/';
$formhtml = '';

$company_logo = get_option('company_logo_dark');
$logo = '';
if (!empty($company_logo)) {
    $logo_path = FCPATH . 'uploads/company/' . $company_logo;
    if (file_exists($logo_path)) {
        $image_data = file_get_contents($logo_path);
        $base64 = 'data:image/png;base64,' . base64_encode($image_data);
        $logo = '<div class="logo">
                <img src="' . $base64 . '" width="130" height="100">
            </div>';
    }
}
if (!empty($logo)) {
    $formhtml .= '<div style="text-align: center; margin-bottom: 20px;">';
    $formhtml .= $logo;
    $formhtml .= '</div>';
}

$formhtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$formhtml .= '<tbody>';
$formhtml .= '
<tr>
    <td colspan="12" width="100%" align="center" style="font-weight:bold; font-size: 16px;">
        Work Permit Register
    </td>
</tr>';
$formhtml .= '
<tr>
    <th width="3%" class="thead-dark">Sr. No.</th>
    <th class="thead-dark">Permit No.</th>
    <th class="thead-dark">Date Issued</th>
    <th class="thead-dark">Type of Work </th>
    <th class="thead-dark">Location / Area</th>
    <th class="thead-dark">Contractor / Agency</th>
    <th class="thead-dark">Person-in-Charge (PIC)</th>
    <th  width="4%" class="thead-dark">Start Time</th>
    <th  width="4%" class="thead-dark">End Time</th>
    <th class="thead-dark">Risk Level</th>
    <th class="thead-dark">Safety Measures Implemented</th>
    <th class="thead-dark">Permit Status</th>
    <th class="thead-dark">Remarks</th>
</tr>';

if (!empty($wpr_details)) {
    $sr = 1;
    foreach ($wpr_details as $detail) {
        $risk = $permit ='';

        if ($detail['risk_level'] == 1) {
            $risk = 'Low';
        } elseif ($detail['risk_level'] == 2) {
            $risk = 'Medium';
        } elseif ($detail['risk_level'] == 3) {
            $risk = 'High';
        }

        if($detail['permit_status'] == 1){
            $permit = 'Close';
        } elseif($detail['permit_status'] == 2){
            $permit = 'Open';
        } 

        $formhtml .= '<tr>';
        $formhtml .= '<td>' . $sr++ . '</td>';
        $formhtml .= '<td>' . $detail['permit_no'] . '</td>';
        $formhtml .= '<td>' . date('d M, Y', strtotime($detail['date_issued'])) . '</td>';
        $formhtml .= '<td>' . $detail['type_of_work'] . '</td>';
        $formhtml .= '<td>' . $detail['area'] . '</td>';
        $formhtml .= '<td>' . get_vendor_name_by_id($detail['agency']) . '</td>';
        $formhtml .= '<td>' . $detail['pic'] . '</td>';
        $formhtml .= '<td>' . date('h:i A', strtotime($detail['start_time'])) . '</td>';
        $formhtml .= '<td>' . date('h:i A', strtotime($detail['end_time'])) . '</td>';
        $formhtml .= '<td>' . $risk . '</td>';
        $formhtml .= '<td>' . $detail['safety_measures'] . '</td>';
        $formhtml .= '<td>' . $permit . '</td>';
        $formhtml .= '<td>' . $detail['remarks'] . '</td>';
        $formhtml .= '</tr>';
        $sr++;
    }
}

$formhtml .= '</tbody>';
$formhtml .= '</table>';
$formhtml .= '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_pdf.css') . '"  rel="stylesheet" type="text/css" />';

$pdf->writeHTML($formhtml, true, false, true, false, '');
