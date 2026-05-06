<?php
defined('BASEPATH') or exit('No direct script access allowed');
$project_name = get_project($ticket->project_id);
$dept_name = department_pur_request_name($ticket->department);
$project_prefix = strtoupper(substr($project_name->name, 0, 3));
$dept_prefix = strtoupper(substr($dept_name, 0, 3));
if ($dept_prefix == 'INT') {
    $dept_prefix = 'ID';
}
$formatted_ticket_id = str_pad($ticket->ticketid, 2, '0', STR_PAD_LEFT);
$tickethtml = '';
$tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$tickethtml .= '<tbody>';
$tickethtml .= '
<tr>
    <td width="50%;" align="center">' . pdf_logo_url() . '</td>
    <td width="25%;" align="center"><b><br><br><br>Project</b></td>
    <td width="25%;" align="center"><b><br><br><br>' . $project_name->name . '</b></td>
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
    <td>' . $project_prefix . '-' . $dept_prefix . '-' . 'RFI' . '-' .  $formatted_ticket_id . '</td>
    <td><b>Subject</b></td>
    <td>' . $ticket->subject . '</td>
</tr>';
$reply_by_date = '';
if (!empty($ticket->lastreply)) {
    $reply_by_date = date('d/m/y', strtotime($ticket->lastreply));
}
$tickethtml .= '
<tr style="font-size:13px; text-align: center">
    <td><b>RFI Date.</b></td>
    <td>' . date('d/m/y', strtotime($ticket->date)) . '</td>
    <td><b>Reply By Date</b></td>
    <td>' . $reply_by_date . '</td>
</tr>';
$tickethtml .= '
<tr style="font-size:13px; text-align: center">
    <td><b>To</b></td>
    <td>' . $ticket->rfi_to . '</td>
    <td><b>Contact person(s)</b></td>
    <td>' . $ticket->from_name . '</td>
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
if (!empty($ticket->discipline)) {
    $index = array_search($ticket->discipline, array_column($all_discipline, 'id'));
    $discipline_name = $index !== false ? $all_discipline[$index]['name'] : null;
}
$area_name = '';
if (!empty($ticket->area)) {
    $area_name = get_area_name_by_id($ticket->area);
}
$tickethtml .= '
<tr style="font-size:13px;">
    <td align="center" width="30%;"><b>Discipline</b></td>
    <td width="70%;">' . $discipline_name . '</td>
</tr>';
$tickethtml .= '
<tr style="font-size:13px;">
    <td align="center" width="30%;"><b>Floor</b></td>
    <td width="70%;">' . $area_name . '</td>
</tr>';
$tickethtml .= '
<tr style="font-size:13px;">
    <td align="center" width="30%;"><b>Description</b></td>
    <td width="70%;">' . $ticket->message . '</td>
</tr>';
if(!empty($ticket->ref_drawing)) {
    $tickethtml .= '
    <tr style="font-size:13px;">
        <td align="center" width="30%;"><b>' . _l('reference_drawings') . '</b></td>
        <td width="70%;">' . $ticket->ref_drawing . '</td>
    </tr>';
}
if(!empty($ticket_dms_items)) {
    $dms_items_link = '';
    foreach ($ticket_dms_items as $dkey => $dvalue) {
        $dms_items_link .= $dkey > 0 ? '<br>' : '';
        $document_number = !empty($dvalue['document_number']) ? $dvalue['document_number'].'-' : '';
        $dms_items_link .= '<br><a href="'.site_url('download/file/drawing_documents/'.$dvalue['id']).'">'.$document_number.$dvalue['name'].'</a>';
    }
    $tickethtml .= '
    <tr style="font-size:13px;">
        <td align="center" width="30%;"><b>' . _l('Drawings') . '</b></td>
        <td width="70%;">' . $dms_items_link . '</td>
    </tr>';
}
$tickethtml .= '</tbody>';
$tickethtml .= '</table>';

$tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$tickethtml .= '<tbody>';
$tickethtml .= '
<tr>
    <td width="100%;" align="left" style="font-size: 14px;">
        <b>RFI INTITIATED BY (PROJECT MANAGER/QC MANAGER):</b> ' . get_staff_full_name($ticket->created_by) . '
    </td>
</tr>';
$tickethtml .= '</tbody>';
$tickethtml .= '</table>';

$tickethtml .= '<table width="100%" cellspacing="0" cellpadding="5" border="1">';
$tickethtml .= '<tbody>';
if (!empty($ticket_replies)) {
    foreach ($ticket_replies as $reply) {
        if ($reply['is_consultant'] == 1) {
            $tickethtml .= '
            <tr style="font-size:13px;">
                <td align="center" width="35%;"><b>Consultant’s advice/ notes</b></td>
                <td width="65%;">' . $reply['message'] . '</td>
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

// --- COMMENTS (TICKET + REPLIES) SECTION IN PDF ---
$commenthtml = '<h3 style="text-align:center;">Comments</h3>';
if (!empty($ticket)) {
    $commenthtml .= '<div style="border:1px solid #000; margin-bottom:15px; padding:8px;">';
    $commenthtml .= '<table width="100%" cellpadding="5" cellspacing="0" border="0">';
    $submitterDisplay = '';
    if ($ticket->admin == null || $ticket->admin == 0) {
        if ($ticket->userid != 0) {
            $submitterDisplay = htmlspecialchars($ticket->submitter);
        } else {
            $submitterDisplay = htmlspecialchars($ticket->submitter) . '<br>' . htmlspecialchars($ticket->ticket_email);
        }
    } else {
        $submitterDisplay = htmlspecialchars($ticket->opened_by);
    }
    $attachments_items_link = '';
    if(!empty($ticket->attachments)) {
        $attachments_items_link = '<br>';
        foreach ($ticket->attachments as $akey => $avalue) {
            $attachments_items_link .= $akey > 0 ? '<br>' : '';
            $attachments_items_link .= '<br><a href="'.site_url('download/file/ticket/'.$avalue['id']).'">'.$avalue['file_name'].'</a>';
        }
    }
    $commenthtml .= '<tr>
        <td width="25%" valign="top" style="border-right:1px solid #ccc;">
            <b>' . $submitterDisplay . '</b><br>
            <span style="font-size:12px; color:#555;">' . (!empty($ticket->admin) ? 'Staff' : 'Client') . '</span><br>
            <span style="font-size:11px; color:#777;">' . date('d M, Y H:i', strtotime($ticket->date)) . '</span>
        </td>
        <td width="75%" valign="top" style="font-size:13px;">' . htmlspecialchars(strip_tags($ticket->message)) . $attachments_items_link . '</td>
    </tr>';
    $commenthtml .= '</table></div>';
}

// --- REPLIES LOOP ---
if (!empty($ticket_replies)) {
    $commenthtml .= '<h3 style="text-align:center;">Replies</h3>';
    foreach ($ticket_replies as $reply) {
        $role = (!empty($reply['is_consultant']) && $reply['is_consultant'] == 1)
            ? 'Consultant'
            : ((!empty($reply['admin'])) ? 'Staff' : 'Client');
        $commenthtml .= '<div style="border:1px solid #000; margin-bottom:15px; padding:8px;">';
        $commenthtml .= '<table width="100%" cellpadding="5" cellspacing="0" border="0">';
        $attachments_items_link = '';
        if(!empty($reply_attachments)) {
            $attachments_items_link = '<br>';
            foreach ($reply_attachments as $akey => $avalue) {
                if($avalue['replyid'] == $reply['id']) {
                    $attachments_items_link .= $akey > 0 ? '<br>' : '';
                    $attachments_items_link .= '<br><a href="'.site_url('download/file/ticket/'.$avalue['id']).'">'.$avalue['file_name'].'</a>';
                }
            }
        }
        $commenthtml .= '
        <tr>
            <td width="25%" valign="top" style="border-right:1px solid #ccc;">
                <b>' . htmlspecialchars($reply['submitter']) . '</b><br>
                <span style="font-size:12px; color:#555;">' . $role . '</span><br>
                <span style="font-size:11px; color:#777;">' . date('d M, Y H:i', strtotime($reply['date'])) . '</span>
            </td>
            <td width="75%" valign="top" style="font-size:13px;">' . nl2br(htmlspecialchars(strip_tags($reply['message']))) . $attachments_items_link . '</td>
        </tr>';
        $commenthtml .= '</table></div>';
    }
} else {
    $commenthtml .= '<p style="text-align:center;">No comments or replies available.</p>';
}

$pdf->writeHTML($commenthtml, true, false, false, false, '');