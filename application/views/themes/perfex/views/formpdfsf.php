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
    <td width="30%" align="right">' . $logo_html . '<br>
        <span><b>SL.NO:</b> ' . ($sf_data->sr_no ?? '') . '</span>
    </td>
</tr>
</table>';

// TITLE
$formhtml .= '
<h2 style="text-align:center;">SCREENING FORM</h2>';

// MAIN TABLE
$formhtml .= '<table width="100%" cellpadding="6" style="font-family: Arial;">';

// Name
$formhtml .= '
<tr>
    <td width="70%"><b>Name of the Employee:</b> ' . ($sf_data->name_of_employee ?? '') . '</td>
    <td width="30%" rowspan="6" align="center" style="border:1px solid #000;">
        Affix Passport size Photo here
    </td>
</tr>';

// Gender + Age
$formhtml .= '
<tr>
    <td><b>Gender:</b> ' . ($sf_data->gender ?? '') . ' <b>Age:</b> ' . ($sf_data->age ?? '') . '</td>
</tr>';

// Designation
$formhtml .= '
<tr>
    <td><b>Designation:</b> ' . ($sf_data->designation ?? '') . '</td>
</tr>';

// Company
$formhtml .= '
<tr>
    <td><b>Name of the Company:</b> ' . ($sf_data->name_of_company ?? '') . '</td>
</tr>';

$formhtml .= '
<tr>
    <td><b>If Sub Contractor:</b> ' . ($sf_data->sub_contractor ?? '') . '</td>
</tr>';

// Aadhar
$formhtml .= '
<tr>
    <td><b>Employee Aadhar Number:</b> ' . ($sf_data->aadhar_number ?? '') . '</td>
</tr>';

// Address
$formhtml .= '
<tr>
    <td colspan="2"><b>Employee Name & Address:</b> ' . ($sf_data->emp_name ?? '') . '</td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="2"><b>Present Address:</b> ' . ($sf_data->present_address ?? '') . '<br><br><b>Mobile no: </b>' . ($sf_data->mobile_number ?? '') . '
    </td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="2"><b>Permanent Address:</b> ' . ($sf_data->parmanent_address ?? '') . '<br><br><b>Mobile no:</b> ' . ($sf_data->mobile_number_2 ?? '') . '
    </td>
</tr>';

// Relation
$formhtml .= '
<tr>
    <td colspan="2"><b>Relation Phone:</b> ' . ($sf_data->relation_phone ?? '') . '<br><br><b>What Relation:</b>' . ($sf_data->what_relation ?? '') . '
    </td>
</tr>';

$formhtml .= '</table>';

// INDUCTION BOX
$formhtml .= '
<br><br>
<table width="100%" cellpadding="8" border="1" style="border-collapse:collapse;">
<tr>
    <td>
        <b>INDUCTION STATEMENT</b><br><br>

        I understand that the information presented to me during induction and agree the information I have given is true and accurate. 
        I also agree to work safely to ensure the safety of myself and work mates and to protect the environment.
        <br><br><br>

        <b>Employer Sign</b>.............. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <b>HSE sign</b> ............... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <b>Date</b>: ' . (!empty($sf_data->date) ? date('d M Y', strtotime($sf_data->date)) : '') . '
    </td>
</tr>
</table>';

// CSS
$formhtml .= '<style>
    body { font-family: Arial; font-size: 12px; }
</style>';

$pdf->writeHTML($formhtml, true, false, true, false, '');
