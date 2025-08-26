<?php
defined('BASEPATH') or exit('No direct script access allowed');
$project_name = get_project($ticket->project_id);
$dept_name = department_pur_request_name($ticket->department);
$project_prefix = strtoupper(substr($project_name->name, 0, 3));
$dept_prefix = strtoupper(substr($dept_name, 0, 3));
$formatted_ticket_id = str_pad($ticket->ticketid, 2, '0', STR_PAD_LEFT);
$tickethtml = '';
$tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$tickethtml .= '<tbody>';
$tickethtml .= '
<tr>
    <td width="50%;" align="center">'.pdf_logo_url().'</td>
    <td width="25%;" align="center"><b><br><br><br>Project</b></td>
    <td width="25%;" align="center"><b><br><br><br>'.$project_name->name.'</b></td>
</tr>';
$tickethtml .= '</tbody>';
$tickethtml .= '</table>';

$tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$tickethtml .= '<tbody>';
$tickethtml .= '
<tr>
    <td colspan="4" width="100%;" align="center" style="font-weight:bold; font-size: 16px;">
        REQUEST FOR INFORMATION
    </td>
</tr>';
$tickethtml .= '
<tr style="font-size:13px; text-align: center">
    <td><b>RFI No.</b></td>
    <td>'.$project_prefix .'-'. $dept_prefix .'-'.'RIF' .'-'.  $formatted_ticket_id .'</td>
    <td><b>Subject</b></td>
    <td>'.$ticket->subject.'</td>
</tr>';
$reply_by_date = '';
if(!empty($ticket->lastreply)) {
    $reply_by_date = date('d/m/y', strtotime($ticket->lastreply));
}
$tickethtml .= '
<tr style="font-size:13px; text-align: center">
    <td><b>RFI Date.</b></td>
    <td>'.date('d/m/y', strtotime($ticket->date)).'</td>
    <td><b>Reply By Date</b></td>
    <td>'.$reply_by_date.'</td>
</tr>';
$tickethtml .= '
<tr style="font-size:13px; text-align: center">
    <td><b>To</b></td>
    <td>'.$ticket->rfi_to.'</td>
    <td><b>Contact person(s)</b></td>
    <td>'.$ticket->from_name.'</td>
</tr>';
$tickethtml .= '</tbody>';
$tickethtml .= '</table>';

$tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$tickethtml .= '<tbody>';
$tickethtml .= '
<tr>
    <td colspan="2" width="100%;" align="left" style="font-weight:bold; font-size: 14px;">
        QUERY: -
    </td>
</tr>';
$discipline_name = '';
$all_discipline = get_all_discipline();
if(!empty($ticket->discipline)) {
    $index = array_search($ticket->discipline, array_column($all_discipline, 'id'));
    $discipline_name = $index !== false ? $all_discipline[$index]['name'] : null;
}
$area_name = '';
if(!empty($ticket->area)) {
    $area_name = get_area_name_by_id($ticket->area);
}
$tickethtml .= '
<tr style="font-size:13px;">
    <td align="center" width="30%;"><b>Discipline</b></td>
    <td width="70%;">'.$discipline_name.'</td>
</tr>';
$tickethtml .= '
<tr style="font-size:13px;">
    <td align="center" width="30%;"><b>Floor</b></td>
    <td width="70%;">'.$area_name.'</td>
</tr>';
$tickethtml .= '
<tr style="font-size:13px;">
    <td align="center" width="30%;"><b>Description</b></td>
    <td width="70%;">'.$ticket->message.'</td>
</tr>';
$tickethtml .= '
<tr style="font-size:13px;">
    <td align="center" width="30%;"><b>'._l('reference_drawings').'</b></td>
    <td width="70%;">'.$ticket->ref_drawing.'</td>
</tr>';
$tickethtml .= '</tbody>';
$tickethtml .= '</table>';

$tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$tickethtml .= '<tbody>';
$tickethtml .= '
<tr>
    <td width="100%;" align="left" style="font-size: 14px;">
        <b>RFI INTITIATED BY (PROJECT MANAGER/QC MANAGER):</b> '.get_staff_full_name($ticket->created_by).'
    </td>
</tr>';
$tickethtml .= '</tbody>';
$tickethtml .= '</table>';

if(!empty($ticket_replies)) {
    $tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
    $tickethtml .= '<tbody>';
    foreach ($ticket_replies as $reply) {
        if($reply['is_consultant'] == 0) {
            $tickethtml .= '
            <tr>
                <td width="100%;" align="left" style="font-size: 14px;">
                    <b>REPLY BY: </b> '.$reply['submitter'].'
                </td>
            </tr>
            <tr>
                <td width="100%;" align="left" style="font-size: 14px;">
                    <b>REPLY DATE:</b> '.date('d/m/y', strtotime($reply['date'])).'
                </td>
            </tr>
            <tr>
                <td width="100%;" align="left" style="font-size: 14px;">
                    <b>Comments/Actions:</b> '.$reply['message'].'
                </td>
            </tr>
            <tr>
                <td width="100%;" align="left" style="font-size: 14px;">
                    <b>Attachments (if any):</b>
                </td>
            </tr>';
        }
    }
    $tickethtml .= '</tbody>';
    $tickethtml .= '</table>';
}

$tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$tickethtml .= '<tbody>';
if(!empty($ticket_replies)) {
    foreach ($ticket_replies as $reply) {
        if($reply['is_consultant'] == 1) {
            $tickethtml .= '
            <tr style="font-size:13px;">
                <td align="center" width="35%;"><b>Consultant’s advice/ notes</b></td>
                <td width="65%;">'.$reply['message'].'</td>
            </tr>';
        }
    }
}
$tickethtml .= '
<tr style="font-size:13px;">
    <td align="center" width="35%;"><b>Attachments by Consultant</b></td>
    <td width="65%;"></td>
</tr>';
$tickethtml .= '
<tr style="font-size:13px;">
    <td align="center" width="35%;"><b>Signature of consultant</b></td>
    <td width="65%;">
        <br><br><br><br>
    </td>
</tr>';
$tickethtml .= '</tbody>';
$tickethtml .= '</table>';

$tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$tickethtml .= '<tbody>';
$tickethtml .= '
<tr style="font-size:13px;">
    <td width="35%;" align="center">
        <b>RFI CLEARANCE</b>
    </td>
    <td width="65%;">
        ☐ Closed<br>
        ☐ Closed and Continued by RFI No.:
    </td>
</tr>';
$tickethtml .= '</tbody>';
$tickethtml .= '</table>';

$pdf->writeHTML($tickethtml, true, false, false, false, '');

?>
