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

// HEADER TABLE (Exact Image Style)
$formhtml .= '
<table width="100%" cellpadding="6" border="1" style="border-collapse:collapse; font-size:12px;">

    <!-- Top Row -->
    <tr>
        <td width="70%" style="font-weight:bold; font-size:14px;">
            BASILIUS INTERNATIONAL LLP
        </td>
        <td width="30%" rowspan="2" align="center">
            ' . $logo_html . '
        </td>
    </tr>

    <!-- Second Row -->
    <tr>
        <td>
            <table width="100%" cellpadding="4" style="border-collapse:collapse; font-size:12px;">
                <tr>
                    <td width="50%"><strong>SAFETY DEPARTMENT</strong></td>
                    <td width="50%" align="right">
                        <strong>CERTIFICATE OF FITNESS FOR HEIGHT PASS</strong>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Scope Row -->
    <tr>
        <td colspan="2">
            <strong>SCOPE OF WORK:</strong> Fitness Certificate for height pass and height pass issue details
        </td>
    </tr>

</table>';

// MAIN TABLE
$formhtml .= '<table width="100%" cellpadding="6" style="font-family: Arial; border-collapse: collapse;">';

// Date and Certificate Info
$formhtml .= '
<tr>
    <td width="70%"><strong>Date:</strong> ' . (!empty($vtf_data->date) ? date('d M Y', strtotime($vtf_data->date)) : '_________________') . '</td>
    <td width="30%"></td>
</tr>';

// Certification Statement
$formhtml .= '
<tr>
    <td colspan="2">
        This is to certify that Mr. <strong>' . ($vtf_data->name_of_employee ?? '_________________') . '</strong> 
        son of <strong>' . ($vtf_data->son_of ?? '_________________') . '</strong> 
        working under M/s <strong>' . ($vtf_data->working_under ?? '_________________') . '</strong>
        In ' . (get_area_ncr_name_by_id($vtf_data->area) ?? '') . ' area, '. get_department_name_by_id($vtf_data->department_vtf) .' department of ' . get_project_name_by_id($form_data->project_id) . ' project M is declared 
        <strong>' . ($vtf_data->fitness_status ?? 'fit / unfit') . '</strong> 
        to work at height as per the medical examination carried out.
    </td>
</tr>';

// EXAMINATION Section
$formhtml .= '
<tr>
    <td colspan="2"><strong>EXAMINATION:</strong></td>
</tr>';

// Examination Details Table
$formhtml .= '
<tr>
    <td colspan="2">
        <table width="100%" cellpadding="6" style="font-size:12px;">
            
            <tr>
                <td width="50%">
                    Weight : ' . ($vtf_data->weight ?? '__________________') . ' Kg
                </td>
                <td width="50%">
                    Height  : ' . ($vtf_data->height ?? '__________________') . ' CM
                </td>
            </tr>

            <tr>
                <td>
                    Vision : ' . ($vtf_data->vision ?? '__________________') . '
                </td>
                <td>
                    Colour Vision : ' . ($vtf_data->color_vision ?? '__________________') . '
                </td>
            </tr>

            <tr>
                <td>
                    Pulse : ' . ($vtf_data->pulse ?? '__________________') . ' BP : ' . ($vtf_data->bp ?? '__________________') . '
                </td>
                <td>
                    Blood Group : ' . ($vtf_data->blood_group ?? '__________________') . '
                </td>
            </tr>

            <tr>
                <td>
                    Vertigo/Fits : ' . ($vtf_data->vertigo_fits ?? '__________________') . '
                </td>
                <td>
                    Gait : ' . ($vtf_data->gait ?? '__________________') . '
                </td>
            </tr>

            <tr>
                <td>
                    Giddiness : ' . ($vtf_data->giddiness ?? '__________________') . '
                </td>
                <td>
                    Other height related disease (If any) :
                    <br>
                    ' . ($vtf_data->other_height_related_disease ?? '__________________') . '
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    Hearing : ' . ($vtf_data->hearing ?? '__________________') . '
                </td>
            </tr>

        </table>
    </td>
</tr>';

// VERTIGO TEST Section
$formhtml .= '
<tr>
    <td colspan="2"><strong>DURING VERTIGO TEST</strong></td>
</tr>';

// Vertigo Test Table
$formhtml .= '
<tr>
    <td colspan="2">

        <table width="100%" cellpadding="5" border="1" style="border-collapse:collapse; font-size:12px;">

            <!-- Header -->
            <tr style="text-align:center;">
                <th width="25%">Parameters</th>
                <th width="25%">Before Test</th>
                <th width="25%">After Test</th>
                <th width="25%">Deviation Observed</th>
            </tr>

            <!-- Blood Pressure Row -->
            <tr>
                <td>Blood Pressure</td>
                <td>' . ($vtf_data->blood_p_be ?? '________') . '</td>
                <td>' . ($vtf_data->blood_p_af ?? '________') . '</td>
                <td>' . ($vtf_data->blood_p_dev ?? '________') . '</td>
            </tr>

            <!-- Pulse Row -->
            <tr>
                <td>Pulse</td>
                <td>' . ($vtf_data->pulse_before_test ?? '________') . '</td>
                <td>' . ($vtf_data->pulse_after_test ?? '________') . '</td>
                <td>' . ($vtf_data->pulse_deviation_observed ?? '________') . '</td>
            </tr>

        </table>

        <br>

        <!-- Footer Line -->
        <table width="100%" cellpadding="4">
            <tr>
                <td>Any other abnormality observed 
                </td>
            </tr>
        </table>

    </td>
</tr> <br>';
// Signature of Medical Officer
$formhtml .= '
<tr>
    <td colspan="2" style="padding-top: 20px;">
        <table width="100%">
            <tr>
                <td width="60%"></td>
                <td width="40%" style="border-top: 1px solid #000; text-align: center;">
                    <strong>Signature of Medical Officer with seal</strong>
                </td>
            </tr>
        </table>
    </td>
</tr><br><br>';

// HEIGHT PASS ISSUE DETAILS Section
$formhtml .= '
<tr>
    <td colspan="2" style="border-top: 1px solid #000;"><strong>HEIGHT PASS ISSUE DETAILS</strong></td>
</tr>';

// Height Pass Details
$formhtml .= '
<tr>
    <td colspan="2">
        <strong>No:</strong> ' . ($vtf_data->no ?? '_________________') . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <strong>Dated:</strong> ' . (!empty($vtf_data->date) ? date('d M Y', strtotime($vtf_data->date)) : '_________________') . '
    </td>
</tr>';

$formhtml .= '
<tr>
    <td colspan="2">
        This is to certify that the under mentioned person has been permitted to work at height.
    </td>
</tr>';

// Person Details Table
$formhtml .= '
<tr>
    <td colspan="2">
        <table width="100%" cellpadding="4">
            <tr>
                <td width="20%"><strong>Name</strong></td>
                <td width="30%">' . ($vtf_data->name ?? '_________________') . '</td>
                <td width="20%"><strong>Age</strong></td>
                <td width="30%">' . ($vtf_data->age ?? '________') . '</td>
            </tr>
            <tr>
                <td><strong>Father\'s Name</strong></td>
                <td colspan="3">' . ($vtf_data->father_name ?? '_________________') . '</td>
            </tr>
            <tr>
                <td><strong>Designation</strong></td>
                <td>' . ($vtf_data->designation ?? '_________________') . '</td>
                <td><strong>Mobile No</strong></td>
                <td>' . ($vtf_data->mobile_no ?? '_________________') . '</td>
            </tr>
            <tr>
                <td><strong>Valid from</strong></td>
                <td>' . (!empty($vtf_data->valid_from) ? date('d M Y', strtotime($vtf_data->valid_from)) : '_________________') . '</td>
                <td><strong>to</strong></td>
                <td>' . (!empty($vtf_data->valid_to) ? date('d M Y', strtotime($vtf_data->valid_to)) : '_________________') . '</td>
            </tr>
        </table>
    </td>
</tr><br><br>';

// Signature Section
$formhtml .= '
<tr>
    <td colspan="2" style="padding-top: 20px;">
        <table width="100%">
            <tr>
                <td width="50%" style="text-align: center;">
                    <strong>Signature/Left thumb impression of worker</strong>
                </td>
                <td width="50%" style=" text-align: center;">
                    <strong>Signature of Safety Office</strong>
                </td>
            </tr>
        </table>
    </td>
</tr>';

$formhtml .= '</table>';

// Generate PDF
$pdf->writeHTML($formhtml, true, false, true, false, '');
