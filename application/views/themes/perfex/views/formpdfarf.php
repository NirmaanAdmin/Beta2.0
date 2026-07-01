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

// Main Title
$formhtml .= '<tr>';
$formhtml .= '<td colspan="8" align="center" style="font-weight:bold; font-size: 16px; background-color: #d3d3d3;">';
$formhtml .= 'Accident Report Format';
$formhtml .= '</td>';
$formhtml .= '</tr>';

// General Information & Injured Person(s) Details - Headers
$formhtml .= '<tr>';
$formhtml .= '<th colspan="4" style="font-weight:bold; text-align:center; background-color: #d3d3d3;">General Information</th>';
$formhtml .= '<th colspan="4" style="font-weight:bold; text-align:center; background-color: #d3d3d3;">Injured Person(s) Details</th>';
$formhtml .= '</tr>';

// General Information & Injured Person(s) Details - Fields
$formhtml .= '<tr>';
$formhtml .= '<td colspan="4">';
$formhtml .= '<table width="100%" cellspacing="2" cellpadding="2">';
$formhtml .= '<tr><td><strong>Report No.:</strong></td><td>' . (isset($arf_data) ? $arf_data->report_no : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Date of Report:</strong></td><td>' . (isset($arf_data) && $arf_data->date_of_report != '' ? date('d M, Y', strtotime($arf_data->date_of_report)) : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Date & Time of Accident:</strong></td><td>' . (isset($arf_data) && $arf_data->date_time_accident != '' ? date('d M, Y H:i', strtotime($arf_data->date_time_accident)) : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Exact Location:</strong></td><td>' . (isset($arf_data) ? $arf_data->location : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Department / Site:</strong></td><td>' . (isset($arf_data) ? $arf_data->department_site : '') . '</td></tr>';
$formhtml .= '</table>';
$formhtml .= '</td>';
$formhtml .= '<td colspan="4">';
$formhtml .= '<table width="100%" cellspacing="2" cellpadding="2">';
$formhtml .= '<tr><td><strong>Name:</strong></td><td>' . (isset($arf_data) ? $arf_data->name : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Age / Gender:</strong></td><td>' . (isset($arf_data) ? $arf_data->age_gender : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Designation:</strong></td><td>' . (isset($arf_data) ? $arf_data->designation : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Employee ID:</strong></td><td>' . (isset($arf_data) ? $arf_data->emp_id : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Nature of Employment:</strong></td><td>' . (isset($arf_data) ? $arf_data->nature_of_emp : '') . '</td></tr>';
$formhtml .= '</table>';
$formhtml .= '</td>';
$formhtml .= '</tr>';

// Description of Accident & Injury / Damage Details - Headers
$formhtml .= '<tr>';
$formhtml .= '<th colspan="4" style="font-weight:bold; text-align:center; background-color: #d3d3d3;">Description of Accident</th>';
$formhtml .= '<th colspan="4" style="font-weight:bold; text-align:center; background-color: #d3d3d3;">Injury / Damage Details</th>';
$formhtml .= '</tr>';

// Description of Accident & Injury / Damage Details - Fields
$formhtml .= '<tr>';
$formhtml .= '<td colspan="4">';
$formhtml .= '<table width="100%" cellspacing="2" cellpadding="2">';
$formhtml .= '<tr><td><strong>Detailed description of what happened:</strong></td></tr>';
$formhtml .= '<tr><td>' . (isset($arf_data) ? $arf_data->detailed_description : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Equipment / tools involved:</strong></td></tr>';
$formhtml .= '<tr><td>' . (isset($arf_data) ? $arf_data->equipment_tools_involved : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Weather / environmental conditions:</strong></td></tr>';
$formhtml .= '<tr><td>' . (isset($arf_data) ? $arf_data->weather_environmental_conditions : '') . '</td></tr>';
$formhtml .= '</table>';
$formhtml .= '</td>';
$formhtml .= '<td colspan="4">';
$formhtml .= '<table width="100%" cellspacing="2" cellpadding="2">';
$formhtml .= '<tr><td><strong>Type of injury or damage:</strong></td><td>' . (isset($arf_data) ? $arf_data->type_of_injury_damage : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Body part affected:</strong></td><td>' . (isset($arf_data) ? $arf_data->body_part_affected : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Severity:</strong></td><td>' . (isset($arf_data) ? $arf_data->severity : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Property damage (if any):</strong></td><td>' . (isset($arf_data) ? $arf_data->property_damage : '') . '</td></tr>';
$formhtml .= '</table>';
$formhtml .= '</td>';
$formhtml .= '</tr>';

// Immediate Action & First Aid & Witness Details - Headers
$formhtml .= '<tr>';
$formhtml .= '<th colspan="4" style="font-weight:bold; text-align:center; background-color: #d3d3d3;">Immediate Action & First Aid</th>';
$formhtml .= '<th colspan="4" style="font-weight:bold; text-align:center; background-color: #d3d3d3;">Witness Details</th>';
$formhtml .= '</tr>';

// Immediate Action & First Aid & Witness Details - Fields
$formhtml .= '<tr>';
$formhtml .= '<td colspan="4">';
$formhtml .= '<table width="100%" cellspacing="2" cellpadding="2">';
$formhtml .= '<tr><td><strong>First aid provided:</strong></td><td>' . (isset($arf_data) ? $arf_data->first_aid_provided : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Medical treatment details:</strong></td><td>' . (isset($arf_data) ? $arf_data->medical_treatment_details : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Hospital / Clinic name:</strong></td><td>' . (isset($arf_data) ? $arf_data->hospital_clinic_name : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Time taken to respond:</strong></td><td>' . (isset($arf_data) ? $arf_data->time_taken_to_respond : '') . '</td></tr>';
$formhtml .= '</table>';
$formhtml .= '</td>';
$formhtml .= '<td colspan="4">';
$formhtml .= '<table width="100%" cellspacing="2" cellpadding="2">';
$formhtml .= '<tr><td><strong>Name(s):</strong></td><td>' . (isset($arf_data) ? $arf_data->name_s : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Designation:</strong></td><td>' . (isset($arf_data) ? $arf_data->designation : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Contact information:</strong></td><td>' . (isset($arf_data) ? $arf_data->contact_information : '') . '</td></tr>';
$formhtml .= '</table>';
$formhtml .= '</td>';
$formhtml .= '</tr>';

// Root Cause Analysis & Corrective & Preventive Actions - Headers
$formhtml .= '<tr>';
$formhtml .= '<th colspan="4" style="font-weight:bold; text-align:center; background-color: #d3d3d3;">Root Cause Analysis</th>';
$formhtml .= '<th colspan="4" style="font-weight:bold; text-align:center; background-color: #d3d3d3;">Corrective & Preventive Actions</th>';
$formhtml .= '</tr>';

// Root Cause Analysis & Corrective & Preventive Actions - Fields
$formhtml .= '<tr>';
$formhtml .= '<td colspan="4">';
$formhtml .= '<table width="100%" cellspacing="2" cellpadding="2">';
$formhtml .= '<tr><td><strong>Immediate cause:</strong></td></tr>';
$formhtml .= '<tr><td>' . (isset($arf_data) ? $arf_data->immediate_cause : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Underlying cause:</strong></td></tr>';
$formhtml .= '<tr><td>' . (isset($arf_data) ? $arf_data->underlying_cause : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Human / Equipment / Environmental factors:</strong></td></tr>';
$formhtml .= '<tr><td>' . (isset($arf_data) ? $arf_data->human_equipment_environmental_factors : '') . '</td></tr>';
$formhtml .= '</table>';
$formhtml .= '</td>';
$formhtml .= '<td colspan="4">';
$formhtml .= '<table width="100%" cellspacing="2" cellpadding="2">';
$formhtml .= '<tr><td><strong>Actions to prevent recurrence:</strong></td></tr>';
$formhtml .= '<tr><td>' . (isset($arf_data) ? $arf_data->actions_to_prevent_recurrence : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Responsible person:</strong></td><td>' . (isset($arf_data) ? $arf_data->responsible_person : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Target date:</strong></td><td>' . (isset($arf_data) && $arf_data->target_date != '' ? date('d M, Y', strtotime($arf_data->target_date)) : '') . '</td></tr>';
$formhtml .= '</table>';
$formhtml .= '</td>';
$formhtml .= '</tr>';

// Reporting & Approval - Header
$formhtml .= '<tr>';
$formhtml .= '<th colspan="8" style="font-weight:bold; text-align:center; background-color: #d3d3d3;">Reporting & Approval</th>';
$formhtml .= '</tr>';

// Reporting & Approval - Fields
$formhtml .= '<tr>';
$formhtml .= '<td colspan="8">';
$formhtml .= '<table width="100%" cellspacing="2" cellpadding="2">';
$formhtml .= '<tr><td><strong>Reported by (Name & Signature):</strong></td><td>' . (isset($arf_data) ? $arf_data->reported_by : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Reviewed by:</strong></td><td>' . (isset($arf_data) ? $arf_data->reviewed_by : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Approved by:</strong></td><td>' . (isset($arf_data) ? $arf_data->approved_by : '') . '</td></tr>';
$formhtml .= '<tr><td><strong>Date:</strong></td><td>' . (isset($arf_data) && $arf_data->approved_date != '' ? date('d M, Y', strtotime($arf_data->approved_date)) : '') . '</td></tr>';
$formhtml .= '</table>';
$formhtml .= '</td>';
$formhtml .= '</tr>';

$formhtml .= '</tbody>';
$formhtml .= '</table>';

$pdf->writeHTML($formhtml, true, false, true, false, '');