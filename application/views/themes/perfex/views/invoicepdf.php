<?php
defined('BASEPATH') or exit('No direct script access allowed');

$tblinvoicehtml = '';
$tblinvoicehtml .= '<table width="100%" cellspacing="0" cellpadding="5">';
$tblinvoicehtml .= '<tbody>';
$tblinvoicehtml .= '
<tr>
    <td width="50%;" align="right">'.pdf_logo_url().'</td>
    <td width="50%" align="left">
        <div style="font-size: 20px;">
            <br><br>
            <b style="color:black" class="company-name-formatted">
                '.get_option('invoice_company_name').'
            </b>
        </div>
    </td>
</tr>';
$tblinvoicehtml .= '</tbody>';
$tblinvoicehtml .= '</table>';

$invoice_type = 'TAX INVOICE';
if($invoice->sent == 0) {
    $invoice_type = 'PROFORMA INVOICE';
}

$tblinvoicehtml .= '<table width="100%" cellspacing="0" cellpadding="2" border="1">';
$tblinvoicehtml .= '<tbody>';
$tblinvoicehtml .= '
<tr>
    <td colspan="3" width="100%;" align="center" style="font-weight:bold; font-size: 14px;">'.$invoice_type.'</td>
</tr>';
$tblinvoicehtml .= '
<tr>
    <td colspan="3" width="100%;" align="center" style="font-weight:bold; font-size: 14px;">ORIGNAL INVOICE FOR RECIPENT</td>
</tr>';

$bill_to = '<b>To:</b>';
$bill_to .= '<div>';
$bill_to .= format_customer_info($invoice, 'invoice', 'billing');
$bill_to .= '</div>';

$hsn_sac_value = '';
$hsn_sac_code = '';
if ($invoice->hsn_sac) {
    $hsn_sac_value = get_hsn_sac_full_name_by_id($invoice->hsn_sac);
    $hsn_sac_value = str_replace("-", "", $hsn_sac_value->name);
    $hsn_sac_code_only = get_hsn_sac_name_by_id($invoice->hsn_sac);
    if(!empty($hsn_sac_code_only)) {
        $parts = explode(' - ', $hsn_sac_code_only);
        $hsn_sac_code = $parts[0];
    }
}

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td rowspan="20">'.$bill_to.'</td>
    <td>'._l('invoice_number').' :</td>
    <td>'.$invoice->title.'</td>
</tr>';

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('invoice_add_edit_date').' :</td>
    <td>'._d($invoice->date).'</td>
</tr>';

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('order_deal_loc_no').' :</td>
    <td>'.$invoice->deal_slip_no.'</td>
</tr>';

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('order_deal_loc_date').' :</td>
    <td></td>
</tr>';

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('suppliers_vendors_gst').' :</td>
    <td>'.get_option('company_vat').'</td>
</tr>';
 
$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('hsn_sac_sescription').' :</td>
    <td>'.$hsn_sac_value.'</td>
</tr>';

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('place_of_supply_of_services').' :</td>
    <td>'.$invoice->place_of_supply_of_services.'</td>
</tr>';

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('delivery_date').' :</td>
    <td>'._d(date('Y-m-d')).'</td>
</tr>';

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('services_provided_location').' :</td>
    <td>'.$invoice->services_provided_location.'</td>
</tr>';

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('state_name_code').' :</td>
    <td>'.$invoice->state_name_code.'</td>
</tr>';

$tblinvoicehtml .= '
<tr style="font-size:13px">
    <td>'._l('suppliers_vendors_pan').' :</td>
    <td>'.get_option('company_pan').'</td>
</tr>';

$tblinvoicehtml .= '</tbody>';
$tblinvoicehtml .= '</table>';

$amount = $basic_invoice['final_invoice']['amount'];
$rounded_amount = round($amount);
$amount_to_word = amount_to_word($rounded_amount);
$decimal_part = $amount - $rounded_amount;
$decimal_part = number_format(abs($decimal_part), 2);

$tblinvoicehtml .= '<table width="100%" cellspacing="0" cellpadding="6" border="1">';
$tblinvoicehtml .= '
<thead>
  <tr height="30" style="font-size:12px; font-weight: bold">
     <th width="6%;" align="center">Sr No</th>
     <th width="23%" align="center">Description of Material / Services</th>
     <th width="12%" align="center">HSN / SAC Code</th>
     <th width="16%" align="center">Taxable</th>
     <th width="13%" align="center">CGST</th>
     <th width="13%" align="center">SGST</th>
     <th width="17%" align="right">Grand Total</th>
  </tr>
</thead>';
$tblinvoicehtml .= '<tbody>';
$tblinvoicehtml .= '
<tr style="font-size:12px;">
    <td width="6%;"align="center" rowspan="5">1</td>
    <td width="23%" align="left">' . $basic_invoice['final_invoice']['description'] . '</td>
    <td width="12%" align="center">'.$hsn_sac_code.'</td>
    <td width="16%" align="center">' . app_format_money($basic_invoice['final_invoice']['subtotal'], $invoice->currency_name) . '</td>
    <td width="13%" align="center">' . app_format_money($basic_invoice['final_invoice']['cgst_tax'], $invoice->currency_name) . '</td>
    <td width="13%" align="center">' . app_format_money($basic_invoice['final_invoice']['sgst_tax'], $invoice->currency_name) . '</td>
    <td width="17%" align="right">' . app_format_money($amount, $invoice->currency_name) . '</td>
</tr>';
$tblinvoicehtml .= '
<tr style="font-size:12px;">
    <td width="23%" align="left">Total Invoice Value</td>
    <td width="12%" align="center"></td>
    <td width="16%" align="center">' . app_format_money($basic_invoice['final_invoice']['subtotal'], $invoice->currency_name) . '</td>
    <td width="13%" align="center">' . app_format_money($basic_invoice['final_invoice']['cgst_tax'], $invoice->currency_name) . '</td>
    <td width="13%" align="center">' . app_format_money($basic_invoice['final_invoice']['sgst_tax'], $invoice->currency_name) . '</td>
    <td width="17%" align="right">' . app_format_money($amount, $invoice->currency_name) . '</td>
</tr>';
$tblinvoicehtml .= '
<tr style="font-size:12px;">
    <td colspan="5">Round Off</td>
    <td align="right">' . $decimal_part . '</td>
</tr>';
$tblinvoicehtml .= '
<tr style="font-size:12px;">
    <td colspan="5">Total Amount with GST</td>
    <td align="right">' . app_format_money($rounded_amount, $invoice->currency_name) . '</td>
</tr>';
$tblinvoicehtml .= '
<tr style="font-size:12px;">
    <td colspan="6">Total Invoice Value in words Rupees: '.$amount_to_word.'</td>
</tr>';
$tblinvoicehtml .= '</tbody>';
$tblinvoicehtml .= '</table>';

$bank_details = get_bank_details($invoice->clientid);
$tblinvoicehtml .= '<table width="100%" cellspacing="0" cellpadding="0" border="1">';
$tblinvoicehtml .= '
<tbody>
  <tr style="font-size:14px;">
     <td width="50%;" align="left">
        <div style="padding-top:20px;">
            <strong>' . _l('bank_detail') . '</strong>
            <br> 
            '.$bank_details.'
            <br><br><br>
            <strong>Principles Place of Business :</strong> '.$invoice->principles_place_of_business.'
            <br><br><br>
            <strong>Regd Address :</strong>
            <br>
            2601, 26th Floor, Beaumonde, C Wing,
            <br>
            Appasaheb Marathe Marg, Prabhadevi,
            <br>
            Mumbai - 400025, Maharashtra
            <br>
            Mobile No: 9820121234
            <br>
        </div>
     </td>
     <td width="50%;" align="right">
        <div style="padding-top:20px;">
            <strong>For BASILIUS INTERNATIONAL LLP</strong>
            <br /><br /><br /><br /><br />
            _________________________
            <br />
            Authorised Signatory
        </div>
     </td>
  </tr>
</tbody>';
$tblinvoicehtml .= '</table>';

$pdf->writeHTML($tblinvoicehtml, true, false, false, false, '');
$pdf->AddPage();

$tblbudgetsummaryhtml = '';
$tblbudgetsummaryhtml .= '<h3 style="text-align:center; ">'._l('budget_summary').'</h3>';
$tblbudgetsummaryhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="3">';
$tblbudgetsummaryhtml .= '
<thead>
  <tr height="30" bgcolor="#323a45" style="color:#ffffff; font-size:12px;">
     <th width="5%;" align="center">' . _l('the_number_sign') . '</th>
     <th width="12%" align="left">' . _l('budget_head') . '</th>
     <th width="16%" align="right">' . _l('budgeted_amount') . '</th>
     <th width="17%" align="right">' . _l('total_previous_billing') . '</th>
     <th width="17%" align="right">' . _l('total_current_billing_amount') . '</th>
     <th width="16%" align="right">' . _l('total_cumulative_billing') . '</th>
     <th width="17%" align="right">' . _l('balance_available') . '</th>
  </tr>
</thead>';
$tblbudgetsummaryhtml .= '<tbody>';
$budgetsummary = $basic_invoice['budgetsummary'];
foreach ($budgetsummary as $ikey => $ivalue) {
    $tblbudgetsummaryhtml .= '
    <tr style="font-size:12px;">
        <td width="5%;" align="center">' . ($ikey + 1) . '</td>
        <td width="12%" align="left;"><span style="font-size:12px;"><strong>' . $ivalue['name'] . '</strong></span></td>
        <td width="16%" align="right">' . app_format_money($ivalue['budgeted_amount'], $invoice->currency_name) . '</td>
        <td width="17%" align="right">' . app_format_money($ivalue['total_previous_billing'], $invoice->currency_name) . '</td>
        <td width="17%" align="right">' . app_format_money($ivalue['total_current_billing_amount'], $invoice->currency_name) . '</td>
        <td width="16%" align="right">' . app_format_money($ivalue['total_cumulative_billing'], $invoice->currency_name) . '</td>
        <td width="17%" align="right">' . app_format_money($ivalue['balance_available'], $invoice->currency_name) . '</td>
        
    </tr>';
}
$tblbudgetsummaryhtml .= '</tbody>';
$tblbudgetsummaryhtml .= '</table>';

$pdf->writeHTML($tblbudgetsummaryhtml, true, false, false, false, '');
$pdf->Ln(3);
$tblbudgetsummaryhtml = '';
$tblbudgetsummaryhtml .= '<table cellpadding="6" style="font-size:14px">';
$tblbudgetsummaryhtml .= '
<tr>
    <td align="right" width="75%"><strong>' . _l('subtotal_before_management_fees') . '</strong></td>
    <td align="right" width="25%">' . app_format_money($basic_invoice['total_budget_summary']['total_without_man_fees'], $invoice->currency_name) . '</td>
</tr>';
$tblbudgetsummaryhtml .= '
<tr>
    <td align="right" width="75%"><strong>' . _l('total').' '._l('budgeted_amount') . '</strong></td>
    <td align="right" width="25%">' . app_format_money($basic_invoice['total_budget_summary']['budgeted_amount'], $invoice->currency_name) . '</td>
</tr>';
$tblbudgetsummaryhtml .= '
<tr>
    <td align="right" width="75%"><strong>' ._l('total_previous_billing') . '</strong></td>
    <td align="right" width="25%">' . app_format_money($basic_invoice['total_budget_summary']['total_previous_billing'], $invoice->currency_name) . '</td>
</tr>';
$tblbudgetsummaryhtml .= '
<tr>
    <td align="right" width="75%"><strong>' ._l('total_current_billing_amount') . '</strong></td>
    <td align="right" width="25%">' . app_format_money($basic_invoice['total_budget_summary']['total_current_billing_amount'], $invoice->currency_name) . '</td>
</tr>';
$tblbudgetsummaryhtml .= '
<tr>
    <td align="right" width="75%"><strong>' ._l('total_cumulative_billing') . '</strong></td>
    <td align="right" width="25%">' . app_format_money($basic_invoice['total_budget_summary']['total_cumulative_billing'], $invoice->currency_name) . '</td>
</tr>';
$tblbudgetsummaryhtml .= '
<tr>
    <td align="right" width="75%"><strong>' . _l('total').' '._l('balance_available') . '</strong></td>
    <td align="right" width="25%">' . app_format_money($basic_invoice['total_budget_summary']['balance_available'], $invoice->currency_name) . '</td>
</tr>';
$tblbudgetsummaryhtml .= '</table>';
$pdf->writeHTML($tblbudgetsummaryhtml, true, false, false, false, '');
$pdf->AddPage();

$tblindexahtml = '';
$tblindexahtml .= '<h3 style="text-align:center; ">Index - A</h3>';
$tblindexahtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="8">';
$tblindexahtml .= '
<thead>
  <tr height="30" bgcolor="#323a45" style="color:#ffffff; font-size:14px;">
     <th width="7%;" align="center">' . _l('the_number_sign') . '</th>
     <th width="38%" align="left">' . _l('budget_head') . '</th>
     <th width="20%" align="right">' . _l('rate_without_tax') . '</th>
     <th width="15%" align="right">' . _l('invoice_table_tax_heading') . '</th>
     <th width="20%" align="right">' . _l('invoice_table_amount_heading') . '</th>
  </tr>
</thead>';
$tblindexahtml .= '<tbody>';
$indexa = $basic_invoice['indexa'];
foreach ($indexa as $ikey => $ivalue) {
    $tblindexahtml .= '
    <tr style="font-size:13px;">
        <td width="7%;" align="center">' . ($ikey + 1) . '</td>
        <td width="38%" align="left;"><span style="font-size:13px;"><strong>' . $ivalue['name'] . '</strong></span></td>
        <td width="20%" align="right">' . app_format_money($ivalue['subtotal'], $invoice->currency_name) . '</td>
        <td width="15%" align="right">' . app_format_money($ivalue['tax'], $invoice->currency_name) . '</td>
        <td width="20%" align="right">' . app_format_money($ivalue['amount'], $invoice->currency_name) . '</td>
    </tr>';
}
$tblindexahtml .= '</tbody>';
$tblindexahtml .= '</table>';

$pdf->writeHTML($tblindexahtml, true, false, false, false, '');
$pdf->Ln(3);
$tblindexafinalhtml = '';
$tblindexafinalhtml .= '<table cellpadding="6" style="font-size:14px">';
$tblindexafinalhtml .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('subtotal_without_management_fees_and_tax') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($basic_invoice['final_invoice']['total_without_man_fees'], $invoice->currency_name) . '</td>
</tr>';
$tblindexafinalhtml .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('grand_subtotal_without_tax') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($basic_invoice['final_invoice']['subtotal'], $invoice->currency_name) . '</td>
</tr>';
// $tblindexafinalhtml .= '
// <tr>
//     <td align="right" width="85%"><strong>' . _l('tax') . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($basic_invoice['final_invoice']['tax'], $invoice->currency_name) . '</td>
// </tr>';
// $tblindexafinalhtml .= '
// <tr>
//     <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($basic_invoice['final_invoice']['amount'], $invoice->currency_name) . '</td>
// </tr>';
$tblindexafinalhtml .= '</table>';
$pdf->writeHTML($tblindexafinalhtml, true, false, false, false, '');

if (!empty($indexa)) {
    foreach ($indexa as $akey => $avalue) {
        $tblannexurehtml = '';
        $tblannexurehtml .= '<h3 style="text-align:center; ">' . $avalue['name'] . '</h3>';
        $tblannexurehtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5">';
        $tblannexurehtml .= '
            <thead>
              <tr height="30" bgcolor="#323a45" style="color:#ffffff; font-size:12px;">
                 <th width="5%;" align="center">' . _l('the_number_sign') . '</th>
                 <th width="12%" align="left">' . _l('budget_head') . '</th>
                 <th width="12%" align="left">' . _l('description_of_services') . '</th>
                 <th width="16%" align="left">' . _l('vendor') . '</th>
                 <th width="10%" align="left">' . _l('invoice_add_edit_date') . '</th>
                 <th width="11%" align="left">' . _l('invoice_no') . '</th>
                 <th width="11%" align="right">' . _l('rate_without_tax') . '</th>
                 <th width="11%" align="right">' . _l('invoice_table_tax_heading') . '</th>
                 <th width="12%" align="right">' . _l('invoice_table_amount_heading') . '</th>
              </tr>
            </thead>';
        $tblannexurehtml .= '<tbody>';
        $invoice_items = $invoice->items;
        $inv = 1;
        // $invoice_tax = get_annexurewise_tax($invoice->id);
        foreach ($invoice_items as $item) {
            if ($item['annexure'] == $avalue['annexure']) {
                if (!is_numeric($item['qty'])) {
                    $item['qty'] = 1;
                }
                $amount = ($item['rate'] * $item['qty']) + $item['tax'];
                $total_tax = $item['tax'];
                $annexure = $item['annexure'];
                $itemid = $item['id'];
                // if(!empty($invoice_tax)) {
                //     $item_tax_array = array_filter($invoice_tax, function ($item) use ($annexure, $itemid) {
                //         return ($item['annexure'] == $annexure && $item['item_id'] == $itemid);
                //     });
                //     $item_tax_array = !empty($item_tax_array) ? array_values($item_tax_array) : array();
                //     $total_tax = !empty($item_tax_array) ? $item_tax_array[0]['total_tax'] : 0;
                // }
                $vendor_name = '';
                $invoice_no = '';
                $invoice_date = '';
                if (!empty($item['po_id'])) {
                    $pur_orders = get_pur_orders($item['po_id']);
                    $vendor = get_vendor_details($pur_orders->vendor);
                    $vendor_name = $vendor->company;
                    $invoice_no = $pur_orders->pur_order_number;
                }
                if (!empty($item['wo_id'])) {
                    $wo_orders = get_wo_orders($item['wo_id']);
                    $vendor = get_vendor_details($wo_orders->vendor);
                    $vendor_name = $vendor->company;
                    $invoice_no = $wo_orders->wo_order_number;
                }
                if (!empty($item['vbt_id'])) {
                    $pur_invoices = get_pur_invoices($item['vbt_id']);
                    $vendor = get_vendor_details($pur_invoices->vendor);
                    $vendor_name = $vendor->company;
                    $invoice_no = $pur_invoices->vendor_invoice_number;
                    $invoice_date = _d($pur_invoices->invoice_date);
                }
                $tblannexurehtml .= '
                <tr style="font-size:11px;">
                    <td width="5%;" align="center">' . $inv . '</td>
                    <td width="12%" align="left;"><span style="font-size:11px;"><strong>' . clear_textarea_breaks($item['description']) . '</strong></span></td>
                    <td width="12%" align="left"><span style="color:#424242;">' . clear_textarea_breaks($item['long_description']) . '</span></td>
                    <td width="16%" align="left">' . $vendor_name . '</td>
                    <td width="10%" align="left">'.$invoice_date.'</td>
                    <td width="11%" align="left">' . $invoice_no . '</td>
                    <td width="11%" align="right">' . app_format_money($item['rate'], $invoice->currency_name) . '</td>
                    <td width="11%" align="right">' . app_format_money($total_tax, $invoice->currency_name) . '</td>
                    <td width="12%" align="right">' . app_format_money($amount, $invoice->currency_name) . '</td>
                </tr>';
                $inv++;
            }
        }
        $tblannexurehtml .= '</tbody>';
        $tblannexurehtml .= '</table>';

        $pdf->AddPage();
        $pdf->writeHTML($tblannexurehtml, true, false, false, false, '');
        $pdf->Ln(3);
        $tblannexurefinalhtml = '';
        $tblannexurefinalhtml .= '<table cellpadding="6" style="font-size:14px">';
        $tblannexurefinalhtml .= '
        <tr>
            <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
            <td align="right" width="15%">' . app_format_money($avalue['subtotal'], $invoice->currency_name) . '</td>
        </tr>';
        $tblannexurefinalhtml .= '
        <tr>
            <td align="right" width="85%"><strong>' . _l('tax') . '</strong></td>
            <td align="right" width="15%">' . app_format_money($avalue['tax'], $invoice->currency_name) . '</td>
        </tr>';
        $tblannexurefinalhtml .= '
        <tr>
            <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
            <td align="right" width="15%">' . app_format_money($avalue['amount'], $invoice->currency_name) . '</td>
        </tr>';
        $tblannexurefinalhtml .= '</table>';
        $pdf->writeHTML($tblannexurefinalhtml, true, false, false, false, '');
    }
}

$pdf->Ln(8);

$tbltotal = '';
$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$tbltotal .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($basic_invoice['final_invoice']['subtotal'], $invoice->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($invoice)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_discount');
    if (is_sale_discount($invoice, 'percent')) {
        $tbltotal .= ' (' . app_format_number($invoice->discount_percent, true) . '%)';
    }
    $tbltotal .= '</strong>';
    $tbltotal .= '</td>';
    $tbltotal .= '<td align="right" width="15%">-' . app_format_money($invoice->discount_total, $invoice->currency_name) . '</td>
    </tr>';
}

// foreach ($items->taxes() as $tax) {
//     $tbltotal .= '<tr>
//     <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td>
// </tr>';
// }

$tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . _l('tax') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($basic_invoice['final_invoice']['tax'], $invoice->currency_name) . '</td>
</tr>';

if ((int) $invoice->adjustment != 0) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->adjustment, $invoice->currency_name) . '</td>
</tr>';
}

$tbltotal .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($basic_invoice['final_invoice']['amount'], $invoice->currency_name) . '</td>
</tr>';

if (count($invoice->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_total_paid') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', [
        'field' => 'amount',
        'where' => [
            'invoiceid' => $invoice->id,
        ],
    ]), $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('applied_credits') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money($credits_applied, $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_amount_due_on_invoice') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) {
    $tbltotal .= '<tr style="background-color:#f0f0f0;">
       <td align="right" width="85%"><strong>' . _l('invoice_amount_due') . '</strong></td>
       <td align="right" width="15%">' . app_format_money($invoice->total_left_to_pay, $invoice->currency_name) . '</td>
   </tr>';
}

$tbltotal .= '</table>';
// $pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1) {
    // Set the font bold
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->writeHTMLCell('', '', '', '', _l('num_word') . ': ' . $CI->numberword->convert($invoice->total, $invoice->currency_name), 0, 1, false, true, 'C', true);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
}

if (count($invoice->payments) > 0 && get_option('show_transactions_on_invoice_pdf') == 1) {
    $pdf->Ln(4);
    $border = 'border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid; 1px solid black;';
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_received_payments') . ':', 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
    $tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
        <tr height="20"  style="color:#000;border:1px solid #000;">
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_number_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_mode_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_date_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_amount_heading') . '</th>
    </tr>';
    $tblhtml .= '<tbody>';
    foreach ($invoice->payments as $payment) {
        $payment_name = $payment['name'];
        if (!empty($payment['paymentmethod'])) {
            $payment_name .= ' - ' . $payment['paymentmethod'];
        }
        $tblhtml .= '
            <tr>
            <td>' . $payment['paymentid'] . '</td>
            <td>' . $payment_name . '</td>
            <td>' . _d($payment['date']) . '</td>
            <td>' . app_format_money($payment['amount'], $invoice->currency_name) . '</td>
            </tr>
        ';
    }
    $tblhtml .= '</tbody>';
    $tblhtml .= '</table>';
    $pdf->writeHTML($tblhtml, true, false, false, false, '');
}

if (found_invoice_mode($payment_modes, $invoice->id, true, true)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_html_offline_payment') . ':', 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);

    foreach ($payment_modes as $mode) {
        if (is_numeric($mode['id'])) {
            if (!is_payment_mode_allowed_for_invoice($mode['id'], $invoice->id)) {
                continue;
            }
        }
        if (isset($mode['show_on_pdf']) && $mode['show_on_pdf'] == 1) {
            $pdf->Ln(1);
            $pdf->Cell(0, 0, $mode['name'], 0, 1, 'L', 0, '', 0);
            $pdf->Ln(2);
            $pdf->writeHTMLCell('', '', '', '', $mode['description'], 0, 1, false, true, 'L', true);
        }
    }
}

if (!empty($invoice->clientnote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_note'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $invoice->clientnote, 0, 1, false, true, 'L', true);
}

if (!empty($invoice->terms)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('terms_and_conditions') . ':', 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $invoice->terms, 0, 1, false, true, 'L', true);
}
