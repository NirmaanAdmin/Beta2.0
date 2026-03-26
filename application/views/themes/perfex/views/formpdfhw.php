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
        <b>FN: ' . ($hw_data->fn ?? '') . '</b><br>
        <b>Rev. No: ' . ($hw_data->rev_no ?? '') . '</b><br>
    </td>
    <td width="30%" align="right">
        ' . $logo_html . '
    </td>
</tr>
</table>';

// TITLE
$formhtml .= '
<h4 style="text-align:center; font-weight:bold;">
CHECKLIST FOR<br>HOT WORK (WELDING, GRINDING & GAS CUTTING)
</h4>';
$shift = $hw_data->shift;
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
    <td width="50%"><b>Work Location:</b> ' . ($hw_data->location ?? '') . '</td>
</tr>
<tr>
    <td><b>Sub-Contractor:</b> ' . ($hw_data->sub_contractor ?? '') . '</td>
    <td><b>Date:</b> ' . (!empty($hw_data->datetime) ? date('d-m-Y', strtotime($hw_data->datetime)) : '') . '</td>
</tr>
<tr>
    <td><b>Time:</b> ' . (!empty($hw_data->datetime) ? date('H:i A', strtotime($hw_data->datetime)) : '') . '</td>
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
    "Has Job Safety Analysis (JSA) along with LMRA prepared for hot work as per the method statement?",
    "All the workers have been explained regarding safe work-procedures, including the specific identified activity for which the PTW is being issued?",
    "Whether Suitable Emergency arrangement including Evacuation system, Ambulance/Emergency vehicle with driver & first aider, Fire extinguisher/ fire hose, fire watch (trained person looking out for fire incidents, exclusively) is available and kept standby in case of gas leakage, fire & explosion?",
    "Are competent welder/gas cutter/grinder engaged to perform activity?",
    "Are all power cables underground or over head as per safety standards?",
    "Is the work area free from any combustible material?",
    "All floor openings and cut outs covered with fire blanket (in addition to covering the floor opening and cut outs with reinforcement mesh/hard barricading)?",
    "Ensure availability of metal tray or fire retardant cloth beneath & sides of working place to arrest hot molten slag / spatter /spark?",
    "Before hot work, whether closed container (eg. Storage tank) / existing hydrocarbon pipe line purged/pigging for flammable vapours?",
    "Is welding machine routed through RCCB/ELCB?",
    "Is voltage reduction devise (VRD) installed/inbuilt in welding machine?",
    "Is Power cable for welding machine guarded / protected and free from persons tripping / falling?",
    "Is return path cable of welding machine is free from damage?",
    "Is electrode holder insulated and in good condition?",
    "Whether standard lugs & connector (spark free clamp) used for cable joint & connection with rigid crimping?",
    "Receptacles are provided for collecting welding buts?",
    "Is wheel guard available in the Grinding machine?",
    "Is the expiry date for grinding wheel checked at Stores before issue?",
    "Is RPM of grinding wheel is > Grinding Machine?",
    "Is ring test for Grinding wheel conducted at Store / work location?",
    "Is dead man switch inbuilt in grinding machine?",
    "Is cord strain reliever (glands) available in grinding machine?",
    "Is gas cylinder trolley available?",
    "Are two flash back arrestors fitted at torch and cylinder end?",
    "Are proper crimp clamp use at both ends of gas cutting hoses?",
    "Are gas hoses checked visually & with soap solution for any leakage?",
    "All regulator, pressure gauges and cutting torch are in good condition?",
    "Is worker having necessary work specific PPE?",
    "If hot work in specific location (such as height, confined space, underground, on shore, oil & gas field etc.), specific PTW obtained?"
];


$hw_map = [];
foreach ($hw_details as $row) {
    $hw_map[$row['items']] = $row;
}

$checked = '<img src="' . base_url('assets/images/checked.png') . '" width="10">';

// LOOP
foreach ($questions as $key => $q) {
    $i = $key + 1;

    $row = isset($hw_map[$i]) ? $hw_map[$i] : [];

    $desc = isset($row['description']) ? $row['description'] : '';

    $yes = ($desc == 1) ? $checked : '';
    $no  = ($desc == 2) ? $checked : '';
    $na  = ($desc == 3) ? $checked : '';

    $remarks = isset($row['remarks']) ? $row['remarks'] : '';

    // Section headers
    if ($i == 11) {
        $formhtml .= '
        <tr>
            <td colspan="6" style="font-weight:bold; background:#f2f2f2;">
                Welding Related Work:
            </td>
        </tr>';
    }

    if ($i == 18) {
        $formhtml .= '
        <tr>
            <td colspan="6" style="font-weight:bold; background:#f2f2f2;">
                Grinding Related Work:
            </td>
        </tr>';
    }

    if ($i == 24) {
        $formhtml .= '
        <tr>
            <td colspan="6" style="font-weight:bold; background:#f2f2f2;">
                Gas Cutting Related Work:
            </td>
        </tr>';
    }

    // Main row
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
•	The above Checklist is mandatory at every site & manufacturing units of BIL wherever Hot Work is involved.<br>
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
