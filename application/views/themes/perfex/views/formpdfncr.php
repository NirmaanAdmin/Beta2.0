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

$formhtml = '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$formhtml .= '<tbody>';

// TITLE
$formhtml .= '
<tr>
    <td colspan="6" align="center" style="font-weight:bold; font-size:16px;">
        NON-CONFORMANCE REPORT
    </td>
</tr>';

// PROJECT & NCR NO
$formhtml .= '<tr>';
$formhtml .= '<td colspan="3"><b>Project:</b> ' . get_project_name_by_id($form_data->project_id) . '</td>';
$formhtml .= '<td colspan="3"><b>NCR No:</b> ' . ($ncr_data->ncr_no ?? '') . '</td>';
$formhtml .= '</tr>';

// DEPARTMENT
$formhtml .= '<tr>';
$formhtml .= '<td colspan="6"><b>Department:</b> EHS (Environment, Health & Safety)</td>';
$formhtml .= '</tr>';

// ACTIVITY + SYSTEM REF + DATE
$formhtml .= '<tr>';
$formhtml .= '<td colspan="2"><b>Activity:</b> ' . ($ncr_data->activity ?? '') . '</td>';
$formhtml .= '<td colspan="2"><b>System Ref:</b> ' . ($ncr_data->system_ref ?? '') . '</td>';
$formhtml .= '<td colspan="2"><b>Date:</b> ' . (!empty($ncr_data->date) ? date('d M Y', strtotime($ncr_data->date)) : '') . '</td>';
$formhtml .= '</tr>';

// DESCRIPTION
$formhtml .= '<tr>';
$formhtml .= '<td colspan="6"><b>Description of Findings:</b><br>' . ($ncr_data->des_of_findings ?? '') . '</td>';
$formhtml .= '</tr>';

// LOCATION
$formhtml .= '<tr>';
$formhtml .= '<td colspan="6"><b>Location of Non-Conformance:</b> ' . (get_area_ncr_name_by_id($ncr_data->area) ?? '') . '</td>';
$formhtml .= '</tr>';

// PHOTO + NCR TYPE
$formhtml .= '<tr>';
// $formhtml .= '<td colspan="2"><b>Photograph Attached:</b> ' . ($ncr_data->photo == 1 ? 'Yes' : 'No') . '</td>';

$checked = base_url('assets/images/checked.png');
$unchecked = base_url('assets/images/unchecked.png');

$formhtml .= '<td colspan="4">
    <b>NCR Type:</b><br>
    <img src="' . ($ncr_data->ncr_type == 1 ? $checked : $unchecked) . '" width="12"> Major NCR
    &nbsp;&nbsp;
    <img src="' . ($ncr_data->ncr_type == 2 ? $checked : $unchecked) . '" width="12"> Minor NCR
</td>';
$formhtml .= '</tr>';

// REFERENCE CLAUSE
$formhtml .= '<tr>';
$formhtml .= '<td colspan="6"><b>Reference Document Clause No:</b> ' . ($ncr_data->reference_clause ?? '') . '</td>';
$formhtml .= '</tr>';

// ASSESSOR / ASSESSEE
$formhtml .= '<tr>';
$formhtml .= '<td colspan="2"><b>Assessor(s):</b> ' . ($ncr_data->assessor ?? '') . '</td>';
$formhtml .= '<td colspan="2"><b>Assessee: Contractor -</b> ' . (isset($ncr_data->name_of_contractor) ? get_vendor_name_by_id($ncr_data->name_of_contractor) : '') . '</td>';
$formhtml .= '<td colspan="2"><b>Date:</b> ' . (!empty($ncr_data->date) ? date('d M Y', strtotime($ncr_data->date)) : '') . '</td>';
$formhtml .= '</tr>';

// IMMEDIATE ACTION
$formhtml .= '<tr>';
$formhtml .= '<td colspan="6"><b>Immediate Action (Correction):</b><br>' . ($ncr_data->immediate_action ?? '') . '</td>';
$formhtml .= '</tr>';

// INVESTIGATION
$formhtml .= '<tr>';
$formhtml .= '<td colspan="6"><b>Investigation (Root Cause, Impact):</b><br>' . ($ncr_data->investigation ?? '') . '</td>';
$formhtml .= '</tr>';

// CORRECTIVE ACTION
$formhtml .= '<tr>';
$formhtml .= '<td colspan="3"><b>Corrective / Preventive Action:</b><br>' . ($ncr_data->corrective_action ?? '') . '</td>';
$formhtml .= '<td><b>Resp:</b><br>' . ($ncr_data->resp ?? '') . '</td>';
$formhtml .= '<td colspan="2"><b>Target Date:</b><br>' . (!empty($ncr_data->target_date) ? date('d M Y', strtotime($ncr_data->target_date)) : '') . '</td>';
$formhtml .= '</tr>';

// FOLLOW UP
$formhtml .= '<tr>';
$formhtml .= '<td colspan="6"><b>Follow-up Report:</b><br>' . ($ncr_data->followup_report ?? '') . '</td>';
$formhtml .= '</tr>';


// SIGNATURE ROW
$formhtml .= '<tr>';
$formhtml .= '<td colspan="2"><b>NC : ' . 
    (isset($ncr_data->nc) 
        ? ($ncr_data->nc == 2 ? 'Closed' : ($ncr_data->nc == 1 ? 'Open' : '')) 
        : '') 
. '</b></td>';
$formhtml .= '<td colspan="2" rowspan="2" align="center"><b>Reviewed By Project In charge</b><br>' . ($ncr_data->reviewed_by ?? '') . '</td>';
$formhtml .= '<td colspan="2" align="center"><b>Sign</b></td>';
$formhtml .= '</tr>';

$formhtml .= '<tr>';
$formhtml .= '<td colspan="2"><b>Assessor (s): </b>'.($ncr_data->assessor_footer ?? '').'</td>';
$formhtml .= '<td colspan="2"></td>';
$formhtml .= '</tr>';

$formhtml .= '<tr>';
$formhtml .= '<td colspan="2"><b>Date:</b> ' . (!empty($ncr_data->footer_date_1) ? date('d M Y', strtotime($ncr_data->footer_date_1)) : '') . '</td>';
$formhtml .= '<td colspan="2"><b>Date:</b>' . (!empty($ncr_data->footer_date_2) ? date('d M Y', strtotime($ncr_data->footer_date_2)) : '') . '</td>';
$formhtml .= '<td colspan="2"></td>';
$formhtml .= '</tr>';

$formhtml .= '</tbody>';
$formhtml .= '</table>';

// Comments Section
if (!empty($ncr_comments)) {
    foreach ($ncr_comments as $index => $comment) {
        // Find attachments for this comment (matching form_detail_id)
        $comment_attachments = array_filter($attachments, function ($attachment) use ($comment) {
            return $attachment['form_detail_id'] == $comment['id'];
        });

        // Display attachments in 2x2 grid if they exist
        if (!empty($comment_attachments)) {
            // Add page break before the image grid starts
            $formhtml .= '<div style="page-break-before: always;"></div>';

            $formhtml .= '<table width="100%" cellspacing="10" cellpadding="0" border="1" style="margin-top: 10px;">';

            $chunks = array_chunk($comment_attachments, 4); // Split into groups of 4 (2x2 grid per page)

            foreach ($chunks as $chunk_index => $chunk) {
                if ($chunk_index > 0) {
                    $formhtml .= '<div style="page-break-before: always;"></div>';
                }

                for ($i = 0; $i < 4; $i++) {
                    if ($i % 2 == 0) {
                        $formhtml .= '<tr>';
                    }

                    $formhtml .= '<td width="50%" style="text-align: center; vertical-align: top; height: 300px;">';
                    if (isset($chunk[$i])) {
                        $file_path = 'uploads/form_attachments/ncrattachments/' . $chunk[$i]['form_id'] . '/' . $chunk[$i]['form_detail_id'] . '/' . $chunk[$i]['file_name'];

                        if (file_exists(FCPATH . $file_path)) {
                            $file_ext = pathinfo($chunk[$i]['file_name'], PATHINFO_EXTENSION);
                            $full_path = FCPATH . $file_path;
                            $base64 = base64_encode(file_get_contents($full_path));
                            $mime_type = mime_content_type($full_path);

                            // Check if it's an image (you can expand this list if needed)
                            if (in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                                $formhtml .= '<img src="data:' . $mime_type . ';base64,' . $base64 . '" style="max-width: 100%; max-height: 500px;">';
                            } else {
                                $formhtml .= '<div style="padding: 10px; border: 1px solid #ccc;">File: ' . $chunk[$i]['file_name'] . '</div>';
                            }
                        } else {
                            $formhtml .= '<div style="color: red;">File not found: ' . $chunk[$i]['file_name'] . '</div>';
                        }
                    } else {
                        $formhtml .= '&nbsp;';
                    }
                    $formhtml .= '</td>';

                    if ($i % 2 == 1) {
                        $formhtml .= '</tr>';
                    }
                }
            }

            $formhtml .= '</table>';

            // Display the comment
            $formhtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1" style="margin-top: 20px;">';
            $formhtml .= '<tr>';
            $formhtml .= '<td>' . htmlspecialchars($comment['comments']) . '</td>';
            $formhtml .= '</tr>';
            $formhtml .= '</table>';
        }

        // Add space between comment sections
        if ($index < count($ncr_comments) - 1) {
            $formhtml .= '<div style="margin-bottom: 30px;"></div>';
        }
    }
}

$pdf->writeHTML($formhtml, true, false, true, false, '');
