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
        <b>FN: ' . ($wah_data->fn ?? '') . '</b><br>
        <b>Rev. No: ' . ($wah_data->rev_no ?? '') . '</b><br>
    </td>
    <td width="30%" align="right">
        ' . $logo_html . '
    </td>
</tr>
</table>';

// TITLE
$formhtml .= '
<h4 style="text-align:center; font-weight:bold;">
CHECKLIST FOR<br>LIFTING & ERECTION
</h4>';
$shift = $wah_data->shift;
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
    <td width="50%"><b>Name of Project:</b> ' . get_project_name_by_id($form_data->project_id) . '</td>
    <td width="50%"><b>Work Location:</b> ' . ($wah_data->location ?? '') . '</td>
</tr>
<tr>
    <td><b>Sub-Contractor:</b> ' . ($wah_data->sub_contractor ?? '') . '</td>
    <td><b>Date:</b> ' . (!empty($wah_data->datetime) ? date('d-m-Y', strtotime($wah_data->datetime)) : '') . '</td>
</tr>
<tr>
    <td><b>Time:</b> ' . (!empty($wah_data->datetime) ? date('H:i A', strtotime($wah_data->datetime)) : '') . '</td>
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
    "Whether approved Method Statement for specific work is available?",
    "Has Job Safety Analysis (JSA) along with LMRA prepared for working at height as per the method statement?",
    "All the workers have been explained regarding safe work-procedures, including the specific identified activity for which the PTW is being issued?",
    "Whether Suitable Emergency arrangement including Evacuation system, Ambulance/Emergency vehicle with driver & first aider, is available and kept standby in case of emergency.",
    "Whether all workers engaged for work at height are having height Pass?",
    "Is required illumination ensured?",
    "Workplace inspected prior to start of work?",
    "Area below the workplace barricaded?",
    "Workmen provided with tool bag / box to carry bolts, nuts and hand tools?",
    "Arrangement for tag line /fastening hand tools available?",
    "All work platforms are of adequate strength & ergonomically suitable?",
    "Ensured that work at one location/ elevation is not carried out above or below levels, where the activity for which PTW is being issued, to avoid falling of material/tools.",
    "Walkways provided with hand-rail / top rail, mid-rail & toe guard?",
    "All chequered plates, gratings properly fastened / welded / bolted?",
    "All ladders shall be standard make and made of MS/Aluminium/FRP?",
    "Are ladders inspected and are maintained in good condition?",
    "Are ladders properly secured to prevent slipping, sliding or falling?",
    "Are ladders extend 1meter above top of landing and placed at 750 angle at the base?",
    "Ladder Rungs not more than 300mm?",
    "Proper handrails provided on ramps?",
    "Walkways, aisles & all overhead workplaces are cleared of loose material?",
    "Area clear from unwanted materials?",
    "Platforms and walkways are free from oil / grease or other slippery material?",
    "Use of basic and job specific PPE ensured for all?",
    "Common lifeline provided wherever linear movement at height is required?",
    "Double braided Safety nets are provided?",
    "Crawler boards / safety system for work on fragile roof are used?",
    "Second line of defence / safety system (Retractable fall arrestor and life line) are provided?",
    "Are all the Floor openings covered and barricaded?",
    "Adequate warning signs have been displayed?",
    "Are the life lines free from any defects and knots?",
    "Are vertical lifeline used by only one person at a time?",
    "Is lifeline protected from abrasive or sharp edges?"
];


$lse_map = [];
foreach ($wah_details as $row) {
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
$formhtml .= '<tcpdf method="AddPage" />';
$formhtml .= '
<tr>
    <td colspan="6">NOTE: Before issuing PTW, Site Safety In-charge and Execution Engineer/Supervisor shall ensure the above points are checked and complied.<br>
•	The above Checklist is mandatory at every site & manufacturing units of BIL wherever Work at Height is involved.<br>
•	Wherever the Checklist prescribed by client/ customer is mandated for compliance, the same shall be followed.

</td>
</tr>';

// SIGNATURE SECTION
$formhtml .= '
<table width="100%" cellpadding="8" border="1" style="border-collapse:collapse; font-size:11px;">
<tr>
    <td width="50%" align="center">
        <br><br><br><br>
        Signature of the Execution Engineer/Supervisor<br>
        <span style="font-size:10px;">Checked by</span>
    </td>
    <td width="50%" align="center">
        <br><br><br><br>
        Signature of Site Safety In-charge/ Supervisor<br>
        <span style="font-size:10px;">Reviewed By</span>
    </td>
</tr>

</table>
';




$formhtml .= '</tbody></table>';
$pdf->writeHTML($formhtml, true, false, true, false, '');
