<?php
defined('BASEPATH') or exit('No direct script access allowed');

$formhtml = '';

// Logo
$company_logo = get_option('company_logo_dark');
$logo_html = '';
if (!empty($company_logo)) {
    $logo_path = FCPATH . 'uploads/company/' . $company_logo;
    if (file_exists($logo_path)) {
        $image_data = file_get_contents($logo_path);
        $base64 = 'data:image/png;base64,' . base64_encode($image_data);
        $logo_html = '<img src="' . $base64 . '" width="70">';
    }
}

// HEADER
$formhtml .= '
<table width="100%" cellpadding="5">
<tr>
    <td width="70%">
        <b>FN: ' . ($lse_data->fn ?? '') . '</b><br>
        <b>Rev. No: ' . ($lse_data->rev_no ?? '') . '</b><br>
        <b>Date: ' . (!empty($lse_data->date) ? date('d M Y', strtotime($lse_data->date)) : '') . '</b>
    </td>
    <td width="30%" align="right">
        ' . $logo_html . '
    </td>
</tr>
</table>';

// TITLE
$formhtml .= '
<h3 style="text-align:center; font-weight:bold;">
CHECKLIST FOR<br>LIFTING & ERECTION
</h3>';
$shift = $lse_data->shift;
$shift_text = '';
if ($shift == 1) {
    $shift_text = 'Day';
} elseif ($shift == 2) {
    $shift_text = 'Night';
}
// PROJECT DETAILS
$formhtml .= '
<table width="100%" cellpadding="6" border="1" style="border-collapse:collapse;">
<tr>
    <td width="50%"><b>Name of Project:</b>' . get_project_name_by_id($form_data->project_id) . '</td>
    <td width="50%"><b>Work Location:</b> ' . ($lse_data->location ?? '') . '</td>
</tr>
<tr>
    <td><b>Sub-Contractor:</b> ' . ($lse_data->sub_contractor ?? '') . '</td>
    <td><b>Date:</b> ' . (!empty($lse_data->datetime) ? date('d-m-Y', strtotime($lse_data->datetime)) : '') . '</td>
</tr>
<tr>
    <td><b>Time:</b> ' . (!empty($lse_data->datetime) ? date('H:i A', strtotime($lse_data->datetime)) : '') . '</td>
    <td><b>Shift:</b> ' . ($shift_text ?? '') . '
    </td>
</tr>
</table><br>';

// CHECKLIST TABLE HEADER
$formhtml .= '
<table width="100%" cellpadding="5" border="1" style="border-collapse:collapse; font-size:11px;">
<thead>
<tr style="font-weight:bold; text-align:center;">
    <th width="5%">S.N</th>
    <th width="45%">Description</th>
    <th width="10%">Yes</th>
    <th width="10%">No</th>
    <th width="10%">NA</th>
    <th width="20%">Remarks</th>
</tr>
</thead>
<tbody>
';

// QUESTIONS ARRAY
$questions = [
    "Whether approved Method Statement, Lifting plan & erection scheme available?",
    "Has Job Safety Analysis (JSA) along with LMRA prepared?",
    "Workers explained safe work procedures?",
    "Emergency arrangement available (Ambulance, first aider)?",
    "Weather & illumination suitable?",
    "Workplace inspected for hazards?",
    "Is this heavy/critical lifting?",
    "Proper lifting equipment selected?",
    "Tools inspected with valid certificate?",
    "Crane inspected & safety devices working?",
    "Outriggers fully extended?",
    "Erected members secured?",
    "Ground load bearing capacity checked?",
    "Beacon light available?",
    "Traffic diversion arranged?",
    "Operators authorized & competent?",
    "Other agencies informed?",
    "Tag lines provided?",
    "Access/ladder/lifeline provided?",
    "Area cordoned with warning signs?",
    "Height work permit followed?"
];

// ✅ Re-index array by items (IMPORTANT FIX)
$lse_map = [];
foreach ($lse_details as $row) {
    $lse_map[$row['items']] = $row;
}

$checked = '<img src="' . base_url('assets/images/checked.png') . '" width="10">';

// LOOP
foreach ($questions as $key => $q) {
    $i = $key + 1;

    $row = isset($lse_map[$i]) ? $lse_map[$i] : [];

    $desc = isset($row['description']) ? $row['description'] : 0;

    $yes = ($desc == 1) ? $checked : '';
    $no  = ($desc == 2) ? $checked : '';
    $na  = ($desc == 3) ? $checked : '';

    $remarks = isset($row['remarks']) ? $row['remarks'] : '';

    $formhtml .= '
    <tr>
        <td align="center" width="5%">' . $i . '</td>
        <td width="45%">' . $q . '</td>
        <td align="center" width="10%">' . $yes . '</td>
        <td align="center" width="10%">' . $no . '</td>
        <td align="center" width="10%">' . $na . '</td>
        <td width="20%">' . $remarks . '</td>
    </tr>';
}
$formhtml .= '
<tr>
    <td colspan="6">NOTE: Before issuing PTW, Site Safety In-charge and Execution Engineer/Supervisor shall ensure the above points are checked and complied.<br>
•	The above Checklist is mandatory at every site & manufacturing units of TPL wherever Lifting / Erection is involved.<br>
•	Wherever the Checklist prescribed by client/ customer is mandated for compliance, the same shall be followed.
</td>
</tr>';

// SIGNATURE SECTION
$formhtml .= '
<table width="100%" cellpadding="8" border="1" style="border-collapse:collapse; font-size:11px;">
<tr>
    <td width="33%" align="center">
        <br><br><br><br>
        Signature of the Execution Engineer/Supervisor<br>
        <span style="font-size:10px;">Checked by</span>
    </td>
    <td width="33%" align="center">
        <br><br><br><br>
        Signature of Site Safety In-charge/ Supervisor<br>
        <span style="font-size:10px;">Reviewed By</span>
    </td>
    <td width="34%" align="center">
        <br><br><br><br>
        Signature of P&M In-charge<br>
        <span style="font-size:10px;">Verified By (In case of Heavy & Critical lift)</span>
    </td>
</tr>

</table>
';

// FOOTER NOTE
$formhtml .= '
<table width="100%" cellpadding="6" border="1" style="border-collapse:collapse;">
<tr>
<td style="font-size:10px;">
<b>*A lift is considered as a heavy and critical, if any of the following conditions individually or in combination are met.</b><br>
1)	Weight of the item is more than 20 T.<br>
2)	Lifting operation being done with the help of 2 or more cranes/ Tandem lifting.<br>
3)	Any HT/LT power cable within the vicinity of lifting zone shutdown obtained and discharge rods placed at both ends.<br>
4)	If Lifting is beyond 10 m height.<br>
5)	Length of load beyond 12m / sectional area beyond 3.14m2
</td>
</tr>
</table>
';



$formhtml .= '</tbody></table>';

$pdf->writeHTML($formhtml, true, false, true, false, '');
