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

$formhtml .= '<table width="100%" cellspacing="0" cellpadding="8" border="1" style="border-collapse: collapse; font-family: Arial, sans-serif;">';
$formhtml .= '<thead>';
$formhtml .= '
<tr>
    <td colspan="4" width="100%" align="center" style="font-weight:bold; font-size: 18px; padding: 15px; background-color: #f0f0f0;">
        KEY REQUEST (PERMIT)
    </td>
</tr>';
$formhtml .= '<tr>
                <td colspan="4" align="right" style="padding: 5px; background-color: #e6e6e6;">
                    <span style="font-weight:bold;">SR NO: </span>' . (isset($krp_data->sr_no) ? $krp_data->sr_no : '') . '
                </td>
            </tr>';

$formhtml .= '</thead>';
$formhtml .= '<tbody>';

// Contractor Name
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">NAME OF CONTRACTOR</td>
                <td width="75%" colspan="3" style="padding: 8px;">' . (isset($krp_data->name_of_contractor) ? get_vendor_name_by_id($krp_data->name_of_contractor) : '') . '</td>
            </tr>';

// Key Receiver Name
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">NAME OF KEY RECEIVER</td>
                <td width="75%" colspan="3" style="padding: 8px;">' . (isset($krp_data->name_of_key_receiver) ? $krp_data->name_of_key_receiver : '') . '</td>
            </tr>';

// Key Receiver Contact & Key Number
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">KEY RECEIVER CONTACT NUMBER</td>
                <td width="35%" style="padding: 8px;">' . (isset($krp_data->key_receiver_contact_number) ? $krp_data->key_receiver_contact_number : '') . '</td>
                <td width="15%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">KEY NUMBER</td>
                <td width="25%" style="padding: 8px;">' . (isset($krp_data->key_number) ? $krp_data->key_number : '') . '</td>
            </tr>';

// Location
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">LOCATION</td>
                <td width="75%" colspan="3" style="padding: 8px;">' . (isset($krp_data->location) ? $krp_data->location : '') . '</td>
            </tr>';

// Validity Date/Time and Total Hours
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">VALIDITY DATE OR TIME</td>
                <td width="35%" style="padding: 8px;">' . (isset($krp_data->valid_from) ? date('d M, Y H:i A', strtotime($krp_data->valid_from)) : '') . '</td>
                <td width="15%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">TO</td>
                <td width="25%" style="padding: 8px;">' . (isset($krp_data->valid_to) ? date('d M, Y H:i A', strtotime($krp_data->valid_to)) : '') . '</td>
            </tr>';

// Total Hours
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">TOTAL HOURS :</td>
                <td width="75%" colspan="3" style="padding: 8px;">' . (isset($krp_data->total_hours) ? $krp_data->total_hours : '') . '</td>
            </tr>';

// Key Return Date
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">KEY RETURN DATE :</td>
                <td width="35%" style="padding: 8px;">' . (isset($krp_data->key_return_date_time) ? date('d M, Y', strtotime($krp_data->key_return_date_time)) : '') . '</td>
                <td width="15%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">KEY RETURN TIME :</td>
                <td width="25%" style="padding: 8px;">' . (isset($krp_data->key_return_date_time) ? date('H:i A', strtotime($krp_data->key_return_date_time)) : '') . '</td>
            </tr>';

// Department
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">DEPARTMENT</td>
                <td width="75%" colspan="3" style="padding: 8px;">' . (isset($krp_data->department_key_permit) ? get_department_name_by_id($krp_data->department_key_permit) : '') . '</td>
            </tr>';

// Job Description
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">JOB DESCRIPTION :</td>
                <td width="75%" colspan="3" style="padding: 8px;">' . (isset($krp_data->job_description) ? $krp_data->job_description : '') . '</td>
            </tr>';

// Name of Equipments Use
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">NAME OF EQUIPMENTS USE :</td>
                <td width="75%" colspan="3" style="padding: 8px;">' . (isset($krp_data->name_of_equipment_used) ? $krp_data->name_of_equipment_used : '') . '</td>
            </tr>';

// Name of Issuer (KEY) PM
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">NAME OF ISSUER (KEY) PM (BASILIUS)</td>
                <td width="35%" style="padding: 8px;">' . (isset($krp_data->name_of_issuer) ? get_staff_namebyId($krp_data->name_of_issuer) : '') . '</td>
                <td width="15%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">SIGNATURE</td>
                <td width="25%" style="padding: 8px;"></td>
            </tr>';

// Name of Issuer (KEY) Authorizer
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">NAME OF ISSUER (KEY) AUTHORIZER (BASILIUS)</td>
                <td width="35%" style="padding: 8px;">' . (isset($krp_data->name_of_issuer_athorizer) ? get_staff_namebyId($krp_data->name_of_issuer_athorizer) : '') . ' </td>
                <td width="15%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">SIGNATURE</td>
                <td width="25%" style="padding: 8px;"></td>
            </tr>';

// Name of Receiver (KEY) (CONTRACTORS)
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">NAME OF RECEIVER (KEY) (CONTRACTORS)</td>
                <td width="35%" style="padding: 8px;">' . (isset($krp_data->name_of_receiver) ? $krp_data->name_of_receiver : '') . '</td>
                <td width="15%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">SIGNATURE</td>
                <td width="25%" style="padding: 8px;"></td>
            </tr>';

// Permit Closure Authorize Person of BASILIUS
$formhtml .= '<tr>
                <td width="25%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">PERMIT CLOSURE AUTHORIZE PERSON OF BASILIUS</td>
                <td width="35%" style="padding: 8px;">' . (isset($krp_data->permit_closer) ? get_staff_namebyId($krp_data->permit_closer) : '') . '</td>
                <td width="15%" style="font-weight:bold; background-color: #f5f5f5; padding: 8px;">SIGNATURE</td>
                <td width="25%" style="padding: 8px;"></td>
            </tr>';

$formhtml .= '</tbody>';
$formhtml .= '</table>';
$formhtml .= '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_pdf.css') . '"  rel="stylesheet" type="text/css" />';

$pdf->writeHTML($formhtml, true, false, true, false, '');