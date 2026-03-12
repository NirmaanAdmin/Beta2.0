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
$formhtml .= '<thead>';
$formhtml .= '
<tr>
    <td colspan="9" width="100%" align="center" style="font-weight:bold; font-size: 16px;">
        Safety Training
    </td>
</tr>';
$formhtml .= '<tr>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label">Project: <span class="view_project_name">' . $form_data->project_id . '</span></span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label">Date :
                            
                                ' . date('d M, Y', strtotime($st_data->date)) . '
                            
                        </span>
                    </th>
                    <th colspan="3" class="daily_report_head">
                        <span class="daily_report_label">Time :
                            
                               ' . date('H:i A', strtotime($st_data->date)) . '
                            
                        </span>
                    </th>
                </tr>';

$formhtml .= '<tr>
                    <th colspan="5" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Safety Training Given by :</span><span class="daily_report_label" style="display: ruby;">' . get_vendor_name_by_id($st_data->training_given_by) . ' </span>
                    </th>
                    <th colspan="4" class="daily_report_head">
                        <span class="daily_report_label" style="display: ruby;">Number of Participants :</span><span class="daily_report_label" style="display: ruby;">' . $st_data->no_of_participants . '</span>
                    </th>

                </tr>';

$formhtml .= '<tr>
                    <td colspan="9" class="thead-dark">
                        Following points have been detailed and understood by us :
                    </td>
                </tr>
                <tr>
                    <td colspan="2">1. PPE <br> 2. Work Permits <br> 3. Electrical Safety</td>
                    <td colspan="3">4. Hot Works & Gas Cutting <br> 5. Scaffolds <br> 6. SWMS </td>
                    <td colspan="2">7. Working @ Heights <br> 8. Fire Prevention & Protection <br> 9. Lifting Practice</td>
                    <td colspan="2">10. Housekeeping & Waste Disposal <br>11. First Aid & Accident Reporting <br> 12. Others </td>
                </tr>';
$formhtml .= '<tr class="main">
                    <th colspan="1" class="daily_report_head daily_center">
                        <span class="daily_report_label">Sr. No.</span>
                    </th>
                    <th colspan="2" class="daily_report_head daily_center">
                        <span class="daily_report_label">Name</span>
                    </th>
                    <th colspan="3" class="daily_report_head daily_center">
                        <span class="daily_report_label">Contractor Name</span>
                    </th>
                    <th colspan="3" class="daily_report_head daily_center">
                        <span class="daily_report_label">Signature</span>
                    </th>
                    
                </tr>';

$formhtml .= '</thead>';
$formhtml .= '<tbody>';
if (!empty($st_details)) {
    $sr = 1;
    foreach ($st_details as $detail) {
        $formhtml .= '<tr>';
        $formhtml .= '<td colspan="1">' . $sr++ . '</td>';
        $formhtml .= '<td colspan="2">' . $detail['name_staff'] . '</td>';
        $formhtml .= '<td colspan="3">' . get_vendor_name_by_id($detail['contractor']) . '</td>';
        $formhtml .= '<td colspan="3">' . $detail['signature'] . '</td>';
        $formhtml .= '</tr>';
    }
}

$formhtml .='<tr>
                <th colspan="4" class="daily_report_head">
                    <span class="daily_report_label" style="display: ruby;">Name :</span><span class="daily_report_label" style="display: ruby;">'.$st_data->name_footer. ' </span>
                </th>
                <th colspan="5" class="daily_report_head">
                    <span class="daily_report_label" style="display: ruby;">Designation :</span><span class="daily_report_label" style="display: ruby;">'.$st_data->designation. '</span>
                </th>

            </tr>';
$formhtml .= '</tbody>';
$formhtml .= '</table>';
$formhtml .= '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_pdf.css') . '"  rel="stylesheet" type="text/css" />';

$pdf->writeHTML($formhtml, true, false, true, false, '');
