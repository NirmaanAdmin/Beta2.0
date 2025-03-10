<?php

defined('BASEPATH') or exit('No direct script access allowed');
$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column = '<div style="color:#424242;">';
$info_right_column .= format_organization_info();
$info_right_column .= '</div>';

// Add logo
$info_left_column .= pdf_logo_url();
// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(10);

// Get Y position for the separation
$y = $pdf->getY();

// Bill to
$client_details = '<b>' . _l('statement_bill_to') . '</b>';
$client_details .= '<div style="color:#424242;">';
$client_details .= changee_format_vendor_info($statement['client'], 'statement', 'billing');
$client_details .= '</div>';

$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'] + 15, '', '', $y, $client_details, 0, 0, false, true, 'J', true);

$summary = '';
$summary .= '<h2>' . _l('account_summary') . '</h2>';
$summary .= '<div style="color:#676767;">' . _l('statement_from_to', [
    _d($statement['from']),
    _d($statement['to']),
]) . '</div>';
$summary .= '<hr />';
$summary .= '
<table cellpadding="4" border="0" style="color:#424242;" width="100%">
   <tbody>
      <tr>
          <td align="left"><br /><br />' . _l('statement_beginning_balance') . ':</td>
          <td><br /><br />' . app_format_money($statement['beginning_balance'], $statement['currency']) . '</td>
      </tr>
      <tr>
          <td align="left">' . _l('invoiced_amount') . ':</td>
          <td>' . app_format_money($statement['invoiced_amount'], $statement['currency']) . '</td>
      </tr>
      <tr>
          <td align="left">' . _l('amount_paid') . ':</td>
          <td>' . app_format_money($statement['amount_paid'], $statement['currency']) . '</td>
      </tr>
  </tbody>
  <tfoot>
      <tr>
        <td align="left"><b>' . _l('balance_due') . '</b>:</td>
        <td>' . app_format_money($statement['balance_due'], $statement['currency']) . '</td>
    </tr>
  </tfoot>
</table>';

$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['rm'] - 15, '', '', '', $summary, 0, 1, false, true, 'R', true);


$summary_info = '
<div style="text-align: center;">
    ' . _l('customer_statement_info', [
    _d($statement['from']),
    _d($statement['to']),
]) . '
</div>';

$pdf->ln(9);
$pdf->writeHTMLCell($dimensions['wk'] - ($dimensions['rm'] + $dimensions['lm']), '', '', $pdf->getY(), $summary_info, 0, 1, false, true, 'C', false);
$pdf->ln(9);

$tmpBeginningBalance = $statement['beginning_balance'];

$tblhtml = '<table width="100%" cellspacing="0" cellpadding="8" border="0">
<thead>
 <tr height="10" bgcolor="#e8e8e8" style="color:#424242;">
     <th width="13%"><b>' . _l('statement_heading_date') . '</b></th>
     <th width="27%"><b>' . _l('statement_heading_details') . '</b></th>
     <th align="right"><b>' . _l('statement_heading_amount') . '</b></th>
     <th align="right"><b>' . _l('statement_heading_payments') . '</b></th>
     <th align="right"><b>' . _l('statement_heading_balance') . '</b></th>
 </tr>
</thead>
<tbody>
 <tr>
     <td width="13%">' . _d($statement['from']) . '</td>
     <td width="27%">' . _l('statement_beginning_balance') . '</td>
     <td align="right">' . app_format_money($statement['beginning_balance'], $statement['currency'], true) . '</td>
     <td></td>
     <td align="right">' . app_format_money($statement['beginning_balance'], $statement['currency'], true) . '</td>
 </tr>';
$count = 0;
foreach ($statement['result'] as $data) {
    $tblhtml .= '<tr' . (++$count % 2 ? ' bgcolor="#f6f5f5"' : '') . '>
  <td width="13%">' . _d($data['date']) . '</td>
  <td width="27%">';
    if (isset($data['invoice_id'])) {
        $tblhtml .= _l('statement_invoice_details', [
            changee_get_pur_invoice_number($data['invoice_id']),
            _d($data['duedate']),
        ]);
    } elseif (isset($data['payment_id'])) {
        $tblhtml .= _l('statement_payment_details', [
            '#' . $data['payment_id'],
            changee_get_pur_invoice_number($data['payment_invoice_id']),
        ]);
    } elseif (isset($data['debit_note_id'])) {
        $tblhtml .= _l('statement_debit_note_details', changee_format_debit_note_number($data['debit_note_id']));
    } elseif (isset($data['debit_id'])) {
        $tblhtml .= _l('statement_debits_applied_details', [
            changee_format_debit_note_number($data['debit_applied_debit_note_id']),
            app_format_money($data['debit_amount'], $statement['currency'], true),
            changee_get_pur_invoice_number($data['debit_invoice_id']),
        ]);
    } elseif (isset($data['debit_note_refund_id'])) {
        $tblhtml .= _l('statement_debit_note_refund', changee_format_debit_note_number($data['refund_debit_note_id']));
    }

    $tblhtml .= '</td>
    <td align="right">';
    if (isset($data['invoice_id'])) {
        $tblhtml .= app_format_money($data['invoice_amount'], $statement['currency'], true);
    } elseif (isset($data['debit_note_id'])) {
        $tblhtml .= app_format_money($data['debit_note_amount'], $statement['currency'], true);
    }
    $tblhtml .= '</td>
        <td align="right">';
    if (isset($data['payment_id'])) {
        $tblhtml .= app_format_money($data['payment_total'], $statement['currency'], true);
    } elseif (isset($data['debit_note_refund_id'])) {
        $tblhtml .= app_format_money($data['refund_amount'], $statement['currency'], true);
    }
    $tblhtml .= '</td>
            <td align="right">';
    if (isset($data['invoice_id'])) {
        $tmpBeginningBalance = ($tmpBeginningBalance + $data['invoice_amount']);
    } elseif (isset($data['payment_id'])) {
        $tmpBeginningBalance = ($tmpBeginningBalance - $data['payment_total']);
    } elseif (isset($data['debit_note_id'])) {
        $tmpBeginningBalance = ($tmpBeginningBalance - $data['debit_note_amount']);
    } elseif (isset($data['debit_note_refund_id'])) {
        $tmpBeginningBalance = ($tmpBeginningBalance + $data['refund_amount']);
    }
    if (!isset($data['debit_id'])) {
        $tblhtml .= app_format_money($tmpBeginningBalance, $statement['currency'], true);
    }

    $tblhtml .= '</td>
            </tr>';
}
$tblhtml .= '</tbody>
        <tfoot>
         <tr style="color:#424242;">
             <td></td>
             <td></td>
             <td align="right"><b>' . _l('balance_due') . '</b></td>
             <td></td>
             <td align="right">
                 <b>' . app_format_money($statement['balance_due'], $statement['currency']) . '</b>
             </td>
         </tr>
     </tfoot>
 </table>';

$pdf->writeHTML($tblhtml, true, false, false, false, '');
