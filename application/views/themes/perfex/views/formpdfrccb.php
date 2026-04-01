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
$formhtml .= '
<table width="100%" cellpadding="5" style="margin-bottom:20px;">
    <tr>
        <!-- Left Title Section -->
        <td width="70%" style="position: relative;">
            <div style="
                background:#d9d9d9;
                padding:15px 20px;
                width:80%;
                font-weight:bold;
                font-size:16px;
                display:inline-block;
            ">
                RCCB/ELCB Test Register
            </div>
        </td>

        <!-- Right Logo Section -->
        <td width="30%" style="text-align:right;">
';

if (!empty($logo)) {
    $formhtml .= '
        <div style="
            display:inline-block;
            border-radius:50%;
            overflow:hidden;
            width:60px;
            height:60px;
        ">
            ' . $logo . '
        </div>
    ';
}

$formhtml .= '
        </td>
    </tr><br>

    <!-- Bottom Row -->
    <tr>
        <td style="padding-top:15px;">
            <b>Name of Project/Site: ' . get_project_name_by_id($form_data->project_id) . '</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Contractor Name:' . get_vendor_name_by_id($rccb_data->name_of_contractor) . '</b>
        </td>
        <td style="padding-top:15px;">
            
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="float:right;"><b>Date : ' . (!empty($rccb_data->date) ? date('d M Y', strtotime($rccb_data->date)) : ' ') . '</b></span>
        </td>
    </tr>
</table><br><br>    
';

$formhtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$formhtml .= '<tbody>';

$formhtml .= '
<tr>
    <th width="3%" class="thead-dark">Sr. No.</th>
    <th width="12%" class="thead-dark">Location</th>
    <th width="12%" class="thead-dark">RCCB/ELCB NO</th>
    <th width="12%" class="thead-dark">Brief Details of equipments connected</th>
    <th width="12%" class="thead-dark">Date of Testing</th>
    <th width="12%" class="thead-dark">Sensitivity of RCCB</th>
    <th width="12%" class="thead-dark">Test Remarks</th>
    <th width="12%" class="thead-dark">Name of the Contractor Electrical Engineer/ Authorized Electrician</th>
    <th width="12%" class="thead-dark">Signature</th>
</tr>';

if (!empty($rccb_details)) {
    $sr = 1;
    foreach ($rccb_details as $detail) {

        $formhtml .= '<tr>';
        $formhtml .= '<td>' . $sr++ . '</td>';
        $formhtml .= '<td>' . ($detail['location'] ?? '') . '</td>';
        $formhtml .= '<td>' . ($detail['rccb'] ?? '') . '</td>';
        $formhtml .= '<td>' . ($detail['connected'] ?? '') . '</td>';
        $formhtml .= '<td>' . date('d M, Y', strtotime($detail['date_of_testing'])) . '</td>';
        $formhtml .= '<td>' . ($detail['sensitivity_of_rccb'] ?? '') . '</td>';
        $formhtml .= '<td>' . ($detail['test_remarks'] ?? '') . '</td>';
        $formhtml .= '<td>' . ($detail['electrical'] ?? '') . '</td>';
        $formhtml .= '</tr>';
        $sr++;
    }
}

$formhtml .= '</tbody>';
$formhtml .= '</table><br><br><br><br>';
$formhtml .= '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_pdf.css') . '"  rel="stylesheet" type="text/css" />';

$formhtml .= '
<table width="100%" cellpadding="8" style="border-collapse:collapse; margin-top:30px;">
    <tr>
        <!-- Left Column -->
        <td style="
            width:50%;
            border:1px solid #000;
            text-align:center;
            vertical-align:top;
            height:80px;
        ">
            <b>Contractor HSE Representative</b><br>
            <b>Name & Signature</b>
        </td>

        <!-- Right Column -->
        <td style="
            width:50%;
            border:1px solid #000;
            text-align:center;
            vertical-align:top;
            height:80px;
        ">
            <b>BIL HSE Representative</b><br>
            <b>Name & Signature</b>
        </td>
    </tr>

    <!-- Empty Row for Sign Area -->
    <tr>
        <td style="border:1px solid #000; height:60px;"></td>
        <td style="border:1px solid #000; height:60px;"></td>
    </tr>
</table>
';

$pdf->writeHTML($formhtml, true, false, true, false, '');
