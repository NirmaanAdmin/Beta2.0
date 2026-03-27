<?php
defined('BASEPATH') or exit('No direct script access allowed');

$formhtml = '';

// Logo Right Side
$company_logo = get_option('company_logo_dark');
$logo_html = '';
if (!empty($company_logo)) {
    $logo_path = FCPATH . 'uploads/company/' . $company_logo;
    if (file_exists($logo_path)) {
        $image_data = file_get_contents($logo_path);
        $base64 = 'data:image/png;base64,' . base64_encode($image_data);

        $logo_html = '<img src="' . $base64 . '" width="80">';
    }
}

// HEADER TABLE
$formhtml .= '
<table width="100%" cellpadding="5">
<tr>
    <td width="70%"></td>
    <td width="30%" align="right">' . $logo_html . '
    </td>
</tr>
</table>';

// TITLE
$formhtml .= '
<h3 style="text-align:center;">FORM XI</h3><br>
<span style="text-align:center;">[Refer rule 233(c)]</span><br>
<span style="text-align:center;">CERTIFICATE OF MEDICAL EXAMINATION</span><br>';

// MAIN TABLE
$formhtml .= '<table width="100%" cellpadding="6" style="font-family: Arial;">';

// Name
$formhtml .= '
<tr>
    <td width="80%"><b>Certificate Serial No.:</b> ' . ($me_data->sr_no ?? '') . '</td>
    <td width="20%"><b>Date:</b> ' . (!empty($me_data->date) ? date('d M Y', strtotime($me_data->date)) : '') . '</td>
</tr>';

// Gender + Age
$formhtml .= '
<tr>
    <td><b>1. Name:</b> ' . ($me_data->name_of_employee ?? '') . '</td>
</tr>';

// Designation
$formhtml .= '
<tr>
    <td><b>2. Identification marks: (1)</b> ' . ($me_data->identification_marks ?? '') . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>(2)</b> ' . ($me_data->identification_marks_2 ?? '') . '</td>
</tr>';

// Company
$formhtml .= '
<tr>
    <td><b>3. Father\'s Name:</b> ' . ($me_data->father_name ?? '') . '</td>
</tr>';

$formhtml .= '
<tr>
    <td><b>4. Sex:</b> ' . ($me_data->sex ?? '') . '</td>
</tr>';

// Aadhar
$formhtml .= '
<tr>
    <td><b>5. Residence:</b> ' . ($me_data->residence ?? '') . '<br>&nbsp;&nbsp;&nbsp;&nbsp;<b>Son/daughter of: </b>' . ($me_data->son_and_daughter ?? '') . '</td>
</tr>';


$formhtml .= '
<tr>
    <td colspan="2"><b>6. Date of birth, if available:</b> ' . (!empty($me_data->sub_contractor) ? date('d M Y', strtotime($me_data->sub_contractor)) : '') . ' <b>And/or certificate age: </b> ' . ($me_data->age_certificate ?? '') . '
    </td>
</tr>';

// Physical Fitness
$formhtml .= '
<tr>
    <td colspan="2"><b>7. Physical Fitness:</b><br>
    I hereby certify that I have personally examined (name) : <b>' . ($me_data->personal_examined ?? '') . '</b><br>
    son/daughter/wife of. <b>' . ($me_data->personal_examined_1 ?? '') . '</b><br>
    Residing at <b>' . ($me_data->residing_at ?? '') . '</b><br>
    Who is desirous of being employed in building and construction work and that his/her age as nearlyas can be ascertained from my examination is <b>' . ($me_data->ascertain_examination ?? '') . '</b> Years and that he/she is fit for employment in <b> '.($me_data->emloyment_in ?? '').'</b><br>
    <b>As an adult/adolescent.</b>
    </td>
</tr>';

// Reason for refusal or revocation
$formhtml .= '
<tr>
    <td colspan="2">
        <b>8. Reason for-</b><br>
        (1) Refusal of certificate <b>'.($me_data->refusal_of_certificate ?? '').'</b><br>
        (2) Certificate being revoked <b>'.($me_data->certificate_being_revoked ?? '').'</b><br><br>
    </td>
</tr>';

// Signature section
$formhtml .= '
<tr>
    <td width="70%" style=" padding-top: 10px;">
        Signature/Left hand Thumb<br><br><br>
        Impression of building worker
    </td>
    <td width="30%" style="padding-top: 10px;">
        Signature with Seal<br><br><br>
        Medical Inspector/C.M.O.
    </td>
</tr>';

// Note
$formhtml .= '
<tr>
    <td colspan="2">
        <b>Note:</b><br>
        1. Exact details of cause of physical disability should be clearly stated.<br>
        2. Functional/productive abilities should also be stated if disability is stated.
    </td>
</tr>';

$formhtml .= '</table>';



$pdf->writeHTML($formhtml, true, false, true, false, '');
