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
    
    <td width="30%" align="right">
        ' . $logo_html . '
    </td>
</tr>
</table>';

$formhtml .= '
<table width="100%" cellpadding="6" style="border-collapse:collapse;">
<tr>
    <td width="50%"><b>Name of Project Name:</b> ' . get_project_name_by_id($form_data->project_id) . '</td>
    <td width="50%"><b>Contracto Name:</b> ' . (isset($bcmjcb_data->name_of_contractor) ? get_vendor_name_by_id($bcmjcb_data->name_of_contractor) : '') . '</td>
</tr>
<tr>
    <td><b>Checklist No.:</b> ' . ($bcmjcb_data->checklist_no ?? '') . '</td>
    <td><b>Date:</b> ' . (!empty($bcmjcb_data->date) ? date('d M, Y H:i A', strtotime($bcmjcb_data->date)) : '') . '</td>
</tr>

</table><br><br>';
// PROJECT DETAILS
$formhtml .= '
<table width="100%" cellpadding="6" border="1" style="border-collapse:collapse;">
<tr>
    <td width="50%"><b>Equipment Name & Number:</b> </td>
    <td width="50%">9-<b>BAR CUTTING MACHINE</b> &</td>
</tr>
<tr>
    <td colspan="2"><b>Note:</b> Please write Yes or No in the given box and if some comments write in remarks column.</td>
    
</tr>

</table><br>';

// CHECKLIST TABLE HEADER
$formhtml .= '
<table width="100%" cellpadding="5" border="1" style="border-collapse:collapse; font-size:13px;">
<thead>
<tr style="font-weight:bold; text-align:center;">
    <th width="5%">S.N</th>
    <th width="45%">Description</th>
    <th width="10%">Yes</th>
    <th width="10%">No</th>
    <th width="10%">May Be</th>
    <th width="20%">Remarks</th>
</tr>
</thead>
<tbody>
';

// QUESTIONS ARRAY
$questions = $form_items;


$lse_map = [];
foreach ($bcmjcb_details as $row) {
    $lse_map[$row['items']] = $row;
}

$checked = '<img src="' . base_url('assets/images/checked.png') . '" width="15">';

// LOOP
foreach ($questions as $key => $q) {
    $i = $key + 1;

    $row = isset($lse_map[$i]) ? $lse_map[$i] : [];

    $desc = isset($row['status']) ? $row['status'] : 0;

    $yes = ($desc == 1) ? $checked : '';
    $no  = ($desc == 2) ? $checked : '';
    $na  = ($desc == 3) ? $checked : '';

    $remarks = isset($row['remarks']) ? $row['remarks'] : '';

    $formhtml .= '
    <tr>
        <td align="center" width="5%">' . $i . '</td>
        <td width="45%">' . $q['name'] . '</td>
        <td align="center" width="10%">' . $yes . '</td>
        <td align="center" width="10%">' . $no . '</td>
        <td align="center" width="10%">' . $na . '</td>
        <td width="20%">' . $remarks . '</td>
    </tr>';
}

$formhtml .= '</tbody></table><br>';
$formhtml .= '<div class="col-md-6" style="text-align:center;">
    <img src="' . base_url("assets/images/bcmjcb.jpg") . '" alt="">
</div>';
$checked_new = '<img src="' . base_url('assets/images/checked.png') . '" width="15">';
$fit_status = isset($bcmjcb_data->fit_status) ? $bcmjcb_data->fit_status : 0;

$fit = ($fit_status == 'fit') ? $checked_new : '';
$partial = ($fit_status == 'partial') ? $checked_new : '';
$unfit  = ($fit_status == 'unfit') ? $checked_new : '';
$formhtml .= '
<table width="100%" cellpadding="6" border="1" style="border-collapse:collapse;">

<tr>
    <td width="40%"><b>Fit:</b> ' . $fit . ' </td>
    <td width="40%"><b>Partially Fit:</b> ' . $partial . ' </td>
    <td width="20%"><b>Unfit:</b> ' . $unfit . ' </td>
</tr>

<tr>
    <td width="50%"><b>Inspected By</b></td>
    <td width="50%"><b>Reviewed By</b></td>
</tr>

<tr>
    <td><b>Name:</b> ' . (isset($bcmjcb_data->inspected_by) ? get_staff_namebyId($bcmjcb_data->inspected_by) : '') . '</td>
    <td><b>Name:</b> ' . (isset($bcmjcb_data->reviewed_by) ? get_staff_namebyId($bcmjcb_data->reviewed_by) : '') . '</td>
</tr>

<tr>
    <td><b>Signature with date:</b></td>
    <td><b>Signature with date:</b></td>
</tr>

</table><br><br>';

$pdf->writeHTML($formhtml, true, false, true, false, '');
