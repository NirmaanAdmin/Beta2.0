<?php
defined('BASEPATH') or exit('No direct script access allowed');

$formhtml = '';

/* HEADER */
$formhtml .= '<table width="100%" border="1" cellspacing="0" cellpadding="6" style="border-collapse:collapse;font-family:Arial;">';

$formhtml .= '
<tr>
    <td colspan="4" align="center" style="font-weight:bold;font-size:18px;background:#f0f0f0;">
        PERMIT TO WORK SYSTEM (PTW)
    </td>
</tr>';

/* GENERAL INFO */
$formhtml .= '
<tr>
    <td colspan="4" style="font-weight:bold;background:#e6e6e6;">GENERAL INFORMATION</td>
</tr>';

$formhtml .= '
<tr>
    <td width="25%"><b>Date</b></td>
    <td width="25%">' . date('d M, Y', strtotime($wpf_data->date)) . '</td>
    <td width="25%"><b>Permit No</b></td>
    <td width="25%">' . (isset($wpf_data->permit_no) ? $wpf_data->permit_no : '') . '</td>
</tr>';

$formhtml .= '
<tr>
    <td><b>Person Name undertaking work</b></td>
    <td>' . (isset($wpf_data->person_name) ? $wpf_data->person_name : '') . '</td>
    <td><b>EHS Person</b></td>
    <td>' . (isset($wpf_data->ehs_person) ? $wpf_data->ehs_person : '') . '</td>
</tr>';

$formhtml .= '
<tr>
    <td><b>Contractor</b></td>
    <td colspan="3">' . (isset($wpf_data->name_of_contractor) ? get_vendor_name_by_id($wpf_data->name_of_contractor) : '') . '</td>
</tr>';

$formhtml .= '
<tr>
    <td><b>Project Name & Address</b></td>
    <td colspan="3">' . get_project_name_by_id($form_data->project_id) . '</td>
</tr>';

if ($wpf_data->risk == 1) {
    $risk = 'Low';
} elseif ($wpf_data->risk == 2) {
    $risk = 'Medium';
} elseif ($wpf_data->risk == 3) {
    $risk = 'High';
} else {
    $risk = '';
}

$formhtml .= '
<tr>
    <td><b>RISK ACTIVITY</b></td>
    <td>' . $risk . '</td>
    <td><b>NUMBER OF WORKMEN</b></td>
    <td>' . $wpf_data->no_of_workmen . '</td>
</tr>';

/* TIME */
$formhtml .= '
<tr>
    <td><b>Permit From (Date & Time)</b></td>
    <td>' . date('d M, Y H:i A', strtotime($wpf_data->permit_from)) . '</td>
    <td><b>To (Date & Time)</b></td>
    <td>' . date('d M, Y H:i A', strtotime($wpf_data->permit_to)) . '</td>
</tr>';

$formhtml .= '<tr>

<td align="center">
    <img src="' . site_url('assets/images/wpf/labour.png') . '" width="25" height="25"><br>
    ' . (!empty($wpf_data->ppe_labour) ? 'Yes' : 'No') . '
</td>

<td align="center">
    <img src="' . site_url('assets/images/wpf/safety.png') . '" width="25" height="25"><br>
    ' . (!empty($wpf_data->ppe_safety) ? 'Yes' : 'No') . '
</td>

<td align="center">
    <img src="' . site_url('assets/images/wpf/face-mask.png') . '" width="25" height="25"><br>
    ' . (!empty($wpf_data->ppe_mask) ? 'Yes' : 'No') . '
</td>

<td align="center">
    <img src="' . site_url('assets/images/wpf/high-visibility-vest.png') . '" width="25" height="25"><br>
    ' . (!empty($wpf_data->ppe_vest) ? 'Yes' : 'No') . '
</td>

</tr>

<tr>

<td align="center">
    <img src="' . site_url('assets/images/wpf/boot.png') . '" width="25" height="25"><br>
    ' . (!empty($wpf_data->ppe_boot) ? 'Yes' : 'No') . '
</td>

<td></td>
<td></td>
<td></td>

</tr>';


$formhtml .= '
<tr>
    <td colspan="4"><b>Work to be done:</b> </td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="4" style="height:50px;">' . (isset($wpf_data->work_to_be_done) ? $wpf_data->work_to_be_done : '') . '</td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="4"><b>Tools / Equipment to be used:</b></td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="4" style="height:50px;">' . (isset($wpf_data->tools_equipment) ? $wpf_data->tools_equipment : '') . '</td>
</tr>';

/* HAZARD TABLE */
$formhtml .= '
<tr>
    <td colspan="4">Note: For Medium or High-Risk activity refer to the RA & MS for further details. The Predetermined RA must be reviewed for the work that will occur EACH DAY that the permit is open. The Permit Holder/Acceptor must ensure that any changes have been Risk assessed and the Permit and/or associated RA is updated to reflect changing conditions</td>
</tr>';

$formhtml .= '
<tr>
    <td style="text-align:center"><b>Hazards</b></td>
    <td style="text-align:center"><b>Controls</b></td>
    <td colspan="2" style="text-align:center"><b>Remarks</b></td>
</tr>';

if (!empty($wpf_details)) {
    $sr = 1;
    foreach ($wpf_details as $detail) {
        $formhtml .= '<tr>';
        $formhtml .= '<td >' . $detail['hazards'] . '</td>';
        $formhtml .= '<td >' . $detail['controls'] . '</td>';
        $formhtml .= '<td colspan="2">' . $detail['remark'] . '</td>';
        $formhtml .= '</tr>';
    }
}

/* PPE */
$formhtml .= '
<tr>
    <td colspan="4"><b>Personal Protective Equipment (PPE):</b></td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="4" style="height:40px;">'.(isset($wpf_data->personal_protective_equipment) ? $wpf_data->personal_protective_equipment : '').'</td>
</tr>';

/* FIRE */
$formhtml .= '
<tr>
    <td colspan="4"><b>Fire extinguisher located at:</b></td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="4" style="height:40px;">'.(isset($wpf_data->fire_extinguisher_location) ? $wpf_data->fire_extinguisher_location : '').'</td>
</tr>';

/* ACKNOWLEDGEMENT */
$formhtml .= '
<tr>
    <td colspan="4" style="text-align: center;">ACKNOWLEDGEMENT OF WORK - CONTRACTOR</td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="4">The contractor acknowledges that the job will be performed in line with the precautions listed above, that all proposed work has been discussed with the Site Representative,and that the Site Representative will be informed of any incidents</td>
</tr>';

/* SIGNATURE BLOCK */
$formhtml .= '
<tr>
    <td><b>Name of Issuer</b></td>
    <td><b>Signature</b></td>
    <td><b>Date</b></td>
    <td><b>Time</b></td>
</tr>';

$formhtml .= '
<tr>
    <td style="height:40px;">'.(isset($wpf_data->name_of_issuer) ? $wpf_data->name_of_issuer : '').'</td>
    <td></td>
    <td>'.date('d M. Y', strtotime(isset($wpf_data->date_time_1) ? $wpf_data->date_time_1 : '')).'</td>
    <td>'.date('H:i A', strtotime(isset($wpf_data->date_time_1) ? $wpf_data->date_time_1 : '')).'</td>
</tr>';
$formhtml .= '
<tr>
    <td colspan="4">The Site Representative witnesses the Contractors signature and advises the contractor of any changes in site conditions  </td>
</tr>';

$formhtml .= '
<tr>
    <td><b>Name of Accepter</b></td>
    <td><b>Signature</b></td>
    <td><b>Date</b></td>
    <td><b>Time</b></td>
</tr>';

$formhtml .= '
<tr>
    <td style="height:40px;">'.(isset($wpf_data->name_of_accepter) ? $wpf_data->name_of_accepter : '').'</td>
    <td></td>
    <td>'.date('d M. Y', strtotime(isset($wpf_data->date_time_2) ? $wpf_data->date_time_2 : '')).'</td>
    <td>'.date('H:i A', strtotime(isset($wpf_data->date_time_2) ? $wpf_data->date_time_2 : '')).'</td>
</tr>';

/* CLOSE OUT */
$formhtml .= '
<tr>
    <td colspan="4" style="text-align: center;"><b>WORK CLEARANCE CLOSE OUT</b></td>
</tr>';
$formhtml .= '
<tr>
    <td colspan="4">The contractor acknowledges that the job has been completed / suspended and the site has been left in a safe and satisfactory conditions.</td>
</tr>';

$formhtml .= '
<tr>
    <td><b>Name of Issuer</b></td>
    <td><b>Signature</b></td>
    <td><b>Date</b></td>
    <td><b>Time</b></td>
</tr>';

$formhtml .= '
<tr>
    <td style="height:40px;">'.(isset($wpf_data->name_of_issuer_2) ? $wpf_data->name_of_issuer_2 : '').'</td>
    <td></td>
    <td>'.date('d M. Y', strtotime(isset($wpf_data->date_time_3) ? $wpf_data->date_time_3 : '')).'</td>
    <td>'.date('H:i A', strtotime(isset($wpf_data->date_time_3) ? $wpf_data->date_time_3 : '')).'</td>
</tr>';
$formhtml .= '
<tr>
    <td colspan="4">The Site Representative acknowledges that the job has been completed / suspended</td>
</tr>';
$formhtml .= '
<tr>
    <td><b>Name of Accepter</b></td>
    <td><b>Signature</b></td>
    <td><b>Date</b></td>
    <td><b>Time</b></td>
</tr>';

$formhtml .= '
<tr>
    <td style="height:40px;">'.(isset($wpf_data->name_of_accepter_2) ? $wpf_data->name_of_accepter_2 : '').'</td>
    <td></td>
    <td>'.date('d M. Y', strtotime(isset($wpf_data->date_time_4) ? $wpf_data->date_time_4 : '')).'</td>
    <td>'.date('H:i A', strtotime(isset($wpf_data->date_time_4) ? $wpf_data->date_time_4 : '')).'</td>
</tr>';

/* REMARKS */
$formhtml .= '
<tr>
    <td colspan="4"><b>Nate :- Kindly Attached the checklist If Doing Hot Work, Height Work, Lifting Work, Excavation Work, Height Work, Night Work, Electric Work Etc,</b><br>Remarks:</td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="4" style="height:50px;"></td>
</tr>';


$formhtml .= '</table>';
$pdf->writeHTML($formhtml, true, false, true, false, '');
