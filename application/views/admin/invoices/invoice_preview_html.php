<?php defined('BASEPATH') or exit('No direct script access allowed');
if ($invoice->status == Invoices_model::STATUS_DRAFT) { ?>
<div class="alert alert-info">
    <?php echo _l('invoice_draft_status_info'); ?>
</div>
<?php }
if (isset($invoice->scheduled_email) && $invoice->scheduled_email) { ?>
<div class="alert alert-warning">
    <?php echo e(_l('invoice_will_be_sent_at', _dt($invoice->scheduled_email->scheduled_at))); ?>
    <?php if (staff_can('edit', 'invoices') || $invoice->addedfrom == get_staff_user_id()) { ?>
    <a href="#" onclick="edit_invoice_scheduled_email(<?php echo $invoice->scheduled_email->id; ?>); return false;">
        <?php echo _l('edit'); ?>
    </a>
    <?php } ?>
</div>
<?php } ?>
<div id="invoice-preview">
    <div class="row">
        <?php

      if ($invoice->recurring > 0 || $invoice->is_recurring_from != null) {
          $recurring_invoice           = $invoice;
          $show_recurring_invoice_info = true;

          if ($invoice->is_recurring_from != null) {
              $recurring_invoice = $this->invoices_model->get($invoice->is_recurring_from);
              // Maybe recurring invoice not longer recurring?
              if ($recurring_invoice->recurring == 0) {
                  $show_recurring_invoice_info = false;
              } else {
                  $next_recurring_date_compare = $recurring_invoice->last_recurring_date;
              }
          } else {
              $next_recurring_date_compare = $recurring_invoice->date;
              if ($recurring_invoice->last_recurring_date) {
                  $next_recurring_date_compare = $recurring_invoice->last_recurring_date;
              }
          }
          if ($show_recurring_invoice_info) {
              if ($recurring_invoice->custom_recurring == 0) {
                  $recurring_invoice->recurring_type = 'MONTH';
              }
              $next_date = date('Y-m-d', strtotime('+' . $recurring_invoice->recurring . ' ' . strtoupper($recurring_invoice->recurring_type), strtotime($next_recurring_date_compare)));
          } ?>
        <div class="col-md-12">
            <div class="mbot10">
                <?php if ($invoice->is_recurring_from == null
         && $recurring_invoice->cycles > 0
         && $recurring_invoice->cycles == $recurring_invoice->total_cycles) { ?>
                <div class="alert alert-info no-mbot">
                    <?php echo e(_l('recurring_has_ended', _l('invoice_lowercase'))); ?>
                </div>
                <?php } elseif ($show_recurring_invoice_info) { ?>
                <span class="label label-info">
                    <?php
               if ($recurring_invoice->status == Invoices_model::STATUS_DRAFT) {
                   echo '<i class="fa-solid fa-circle-exclamation fa-fw text-warning tw-mr-1" data-toggle="tooltip" title="' . _l('recurring_invoice_draft_notice') . '"></i>';
               }
                    echo _l('cycles_remaining'); ?>:&nbsp;
                    <b>
                        <?php
                            echo e($recurring_invoice->cycles == 0 ? _l('cycles_infinity') : $recurring_invoice->cycles - $recurring_invoice->total_cycles);
                        ?>
                    </b>
                </span>
                <?php
            if ($recurring_invoice->cycles == 0 || $recurring_invoice->cycles != $recurring_invoice->total_cycles) {
                echo '<span class="label label-info tw-ml-1"><i class="fa-regular fa-circle-question fa-fw tw-mr-1" data-toggle="tooltip" data-title="' . _l('recurring_recreate_hour_notice', _l('invoice')) . '"></i> ' . _l('next_invoice_date', '&nbsp;<b>' . e(_d($next_date)) . '</b>') . '</span>';
            }
         } ?>
            </div>
            <?php if ($invoice->is_recurring_from != null) { ?>
            <?php echo '<p class="text-muted' . ($show_recurring_invoice_info ? ' mtop15': '') . '">' . _l('invoice_recurring_from', '<a href="' . admin_url('invoices/list_invoices/' . $invoice->is_recurring_from) . '" onclick="init_invoice(' . $invoice->is_recurring_from . ');return false;">' . e(format_invoice_number($invoice->is_recurring_from)) . '</a></p>'); ?>
            <?php } ?>
        </div>
        <div class="clearfix"></div>
        <hr class="hr-10" />
        <?php
      } ?>
        <?php if ($invoice->project_id) { ?>
        <div class="col-md-12">
            <h4 class="font-medium mtop15 mbot20"><?php echo _l('related_to_project', [
         _l('invoice_lowercase'),
         _l('project_lowercase'),
         '<a href="' . admin_url('projects/view/' . $invoice->project_id) . '" target="_blank">' . e($invoice->project_data->name) . '</a>',
         ]); ?></h4>
        </div>
        <?php } ?>
        <div class="col-md-6 col-sm-6">
            <h4 class="bold">
                <?php
                    $tags = get_tags_in($invoice->id, 'invoice');
                    if (count($tags) > 0) {
                        echo '<i class="fa fa-tag" aria-hidden="true" data-toggle="tooltip" data-title="' . e(implode(', ', $tags)) . '"></i>';
                    }
                ?>
                <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id); ?>">
                    <span id="invoice-number">
                        <?php echo e(format_invoice_number($invoice->id)); ?>
                    </span>
                </a>
            </h4>
            <address>
                <?php echo format_organization_info(); ?>
            </address>
            <h5> <?php echo $invoice->title; ?> </h5>
            <?php hooks()->do_action('after_left_panel_invoice_preview_template', $invoice); ?>
        </div>
        <div class="col-sm-6 text-right">
            <span class="bold"><?php echo _l('invoice_bill_to'); ?></span>
            <address class="tw-text-neutral-500">
                <?php echo format_customer_info($invoice, 'invoice', 'billing', true); ?>
            </address>
            <?php if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) { ?>
            <span class="bold"><?php echo _l('ship_to'); ?></span>
            <address class="tw-text-neutral-500">
                <?php echo format_customer_info($invoice, 'invoice', 'shipping'); ?>
            </address>
            <?php } ?>
            <p class="no-mbot">
                <span class="bold">
                    <?php echo _l('invoice_data_date'); ?>
                </span>
                <?php echo e(_d($invoice->date)); ?>
            </p>
            <?php if (!empty($invoice->duedate)) { ?>
            <p class="no-mbot">
                <span class="bold">
                    <?php echo _l('invoice_data_duedate'); ?>
                </span>
                <?php echo e(_d($invoice->duedate)); ?>
            </p>
            <?php } ?>
            <?php if ($invoice->sale_agent && get_option('show_sale_agent_on_invoices') == 1) { ?>
            <p class="no-mbot">
                <span class="bold"><?php echo _l('sale_agent_string'); ?>: </span>
                <?php echo e(get_staff_full_name($invoice->sale_agent)); ?>
            </p>
            <?php } ?>
            <?php if ($invoice->project_id && get_option('show_project_on_invoice') == 1) { ?>
            <p class="no-mbot">
                <span class="bold"><?php echo _l('project'); ?>:</span>
                <?php echo e(get_project_name_by_id($invoice->project_id)); ?>
            </p>
            <?php } ?>
            <?php if ($invoice->hsn_sac) { ?>
            <p class="no-mbot">
                <span class="bold"><?php echo _l('hsn_sac'); ?>:</span>
                <?php echo get_hsn_sac_name_by_id($invoice->hsn_sac); ?>
            </p>
            <?php } ?>
            <?php $pdf_custom_fields = get_custom_fields('invoice', ['show_on_pdf' => 1]);
            foreach ($pdf_custom_fields as $field) {
                $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                if ($value == '') {
                    continue;
                } ?>
            <p class="no-mbot">
                <span class="bold"><?php echo e($field['name']); ?>: </span>
                <?php echo $value; ?>
            </p>
            <?php } ?>
            <?php if ($invoice->deal_slip_no) { ?>
            <p class="no-mbot">
                <span class="bold"><?php echo _l('deal_slip_no'); ?>:</span>
                <?php echo $invoice->deal_slip_no; ?>
            </p>
            <?php } ?>
            <?php hooks()->do_action('after_right_panel_invoice_preview_template', $invoice); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php $base_currency = $invoice->currency_name; ?>
            <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#final_invoice" aria-controls="final_invoice" role="tab" id="tab_final_invoice" data-toggle="tab">
                            Final invoice
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#budget_summary" aria-controls="budget_summary" role="tab" id="tab_budget_summary" data-toggle="tab">
                            <?php echo _l('budget_summary'); ?>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#indexa" aria-controls="indexa" role="tab" id="tab_indexa" data-toggle="tab">
                            Index - A
                        </a>
                    </li>

                    <?php
                    $annexures = get_all_annexures(); ?>
                    <li role="presentation" class="dropdown">
                        <a href="#" class="dropdown-toggle" id="tab_child_items" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Annexures
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu annexture-list" aria-labelledby="tab_child_items" style="width: max-content;">
                            <?php
                            foreach ($annexures as $key => $annexure) { ?>
                                <li>
                                    <a href="#<?php echo $annexure['annexure_key']; ?>" aria-controls="<?php echo $annexure['annexure_key']; ?>" role="tab" id="tab_<?php echo $annexure['annexure_key']; ?>" data-toggle="tab">
                                        <?php echo $annexure['name']/*." (".$annexure['annexure_name'].")"*/ ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="final_invoice">
                    <div class="table-responsive s_table">
                        <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                            <thead>
                                <tr>
                                    <th width="25%" align="left"><?php echo _l('description_of_services'); ?></th>
                                    <th width="15%" align="right"><?php echo _l('rate_without_tax'); ?></th>
                                    <th width="15%" align="right">
                                      <?php 
                                        echo _l('cgst_tax') . ' (' . 
                                        (($invoice->cgst == intval($invoice->cgst)) 
                                            ? intval($invoice->cgst) 
                                            : number_format($invoice->cgst, 2)) 
                                        . '%)'; 
                                      ?>
                                    </th>
                                    <th width="15%" align="right">
                                      <?php 
                                        echo _l('sgst_tax') . ' (' . 
                                        (($invoice->sgst == intval($invoice->sgst)) 
                                            ? intval($invoice->sgst) 
                                            : number_format($invoice->sgst, 2)) 
                                        . '%)'; 
                                      ?>
                                    </th>
                                    <th width="15%" align="right"><?php echo _l('invoice_table_amount_heading'); ?></th>
                                    <th width="15%" align="right"><?php echo _l('remarks'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="main">
                                    <td align="left">
                                        <?php
                                        echo clear_textarea_breaks($annexure_invoice['final_invoice']['description']);
                                        ?>
                                    </td>
                                    <td align="right">
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['subtotal'], $base_currency); ?>
                                    </td>
                                    <td align="right">
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['cgst_tax'], $base_currency); ?>
                                    </td>
                                    <td align="right">
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['sgst_tax'], $base_currency); ?>
                                    </td>
                                    <td align="right">
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['amount'], $base_currency); ?>
                                    </td>
                                    <td align="right">
                                        <?php
                                        echo clear_textarea_breaks($annexure_invoice['final_invoice']['remarks']);
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-8 col-md-offset-4">
                        <table class="table text-right">
                            <tbody>
                                <tr id="subtotal">
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('subtotal_without_tax'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['subtotal'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr id="total_tax">
                                    <td>
                                        <span class="bold tw-text-neutral-700">
                                            <?php 
                                                echo _l('cgst_tax') . ' (' . 
                                                (($invoice->cgst == intval($invoice->cgst)) 
                                                    ? intval($invoice->cgst) 
                                                    : number_format($invoice->cgst, 2)) 
                                                . '%)'; 
                                            ?> :
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['cgst_tax'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr id="total_tax">
                                    <td>
                                        <span class="bold tw-text-neutral-700">
                                            <?php 
                                                echo _l('sgst_tax') . ' (' . 
                                                (($invoice->sgst == intval($invoice->sgst)) 
                                                    ? intval($invoice->sgst) 
                                                    : number_format($invoice->sgst, 2)) 
                                                . '%)'; 
                                            ?> :
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['sgst_tax'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="bold tw-text-neutral-700"><?php echo _l('invoice_total'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['amount'], $base_currency); ?>
                                    </td>
                                </tr>
                                <?php hooks()->do_action('after_admin_invoice_form_total_field', $invoice ?? null); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="indexa">
                    <div class="table-responsive s_table">
                        <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                            <thead>
                                <tr>
                                    <th width="20%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1"
                                    aria-hidden="true" data-toggle="tooltip"
                                    data-title="<?php echo _l('budget_head'); ?>"></i>
                                    <?php echo _l('budget_head'); ?></th>
                                    <th width="25%" align="left"><?php echo _l('invoice_table_item_description'); ?></th>
                                    <th width="15%" align="right"><?php echo _l('rate_without_tax'); ?></th>
                                    <th width="20%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
                                    <th width="20%" align="right"><?php echo _l('invoice_table_amount_heading'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(!empty($annexure_invoice['indexa'])) {
                                    $indexa = $annexure_invoice['indexa'];
                                    foreach($indexa as $ikey => $ivalue) { ?>
                                        <tr class="main">
                                            <td align="left">
                                                <?php echo $ivalue['name']; ?>
                                            </td>
                                            <td align="left">
                                                <?php echo $ivalue['description']; ?>
                                            </td>
                                            <td align="right">
                                                <?php echo app_format_money($ivalue['subtotal'], $base_currency); ?>
                                            </td>
                                            <td align="right">
                                                <?php echo app_format_money($ivalue['tax'], $base_currency); ?>
                                            </td>
                                            <td align="right">
                                                <?php echo app_format_money($ivalue['amount'], $base_currency); ?>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-8 col-md-offset-4">
                        <table class="table text-right">
                            <tbody>
                                <tr id="subtotal">
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('subtotal_without_management_fees_and_tax'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['total_without_man_fees'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr id="subtotal">
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('grand_subtotal_without_tax'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['subtotal'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr id="total_tax" class="hide">
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('tax'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['tax'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr class="hide">
                                    <td><span class="bold tw-text-neutral-700"><?php echo _l('invoice_total'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['final_invoice']['amount'], $base_currency); ?>
                                    </td>
                                </tr>
                                <?php hooks()->do_action('after_admin_invoice_form_total_field', $invoice ?? null); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="budget_summary">
                    <div class="table-responsive s_table">
                        <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                            <thead>
                                <tr>
                                    <th width="20%" align="left"><?php echo _l('budget_head'); ?></th>
                                    <th width="16%" align="right"><?php echo _l('budgeted_amount'); ?></th>
                                    <th width="16%" align="right"><?php echo _l('total_previous_billing'); ?></th>
                                    <th width="16%" align="right"><?php echo _l('total_current_billing_amount'); ?></th>
                                    <th width="16%" align="right"><?php echo _l('total_cumulative_billing'); ?></th>
                                    <th width="16%" align="right"><?php echo _l('balance_available'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(!empty($annexure_invoice['budgetsummary'])) {
                                    $budgetsummary = $annexure_invoice['budgetsummary'];
                                    foreach($budgetsummary as $bkey => $bvalue) { ?>
                                        <tr class="main">
                                            <td align="left">
                                                <?php echo $bvalue['name']; ?>
                                            </td>
                                            <td align="right">
                                                <span class="budgeted-amount-display" data-invoice="<?php echo $bvalue['invoiceid']; ?>" data-annexure="<?php echo $bvalue['annexure']; ?>" data-id="<?php echo $bkey+1; ?>">
                                                    <?php echo app_format_money($bvalue['budgeted_amount'], $base_currency); ?>
                                                </span>
                                            </td>
                                            <td align="right">
                                                <span class="total-previous-billing-display" data-invoice="<?php echo $bvalue['invoiceid']; ?>" data-annexure="<?php echo $bvalue['annexure']; ?>" data-id="<?php echo $bkey+1; ?>">
                                                    <?php echo app_format_money($bvalue['total_previous_billing'], $base_currency); ?>
                                                </span>
                                            </td>
                                            <td align="right">
                                                <span class="total-current-billing-amount-display" data-id="<?php echo $bkey+1; ?>">
                                                    <?php echo app_format_money($bvalue['total_current_billing_amount'], $base_currency); ?>
                                                </span>
                                            </td>
                                            <td align="right">
                                                <span class="total-cumulative-billing-display" data-id="<?php echo $bkey+1; ?>">
                                                    <?php echo app_format_money($bvalue['total_cumulative_billing'], $base_currency); ?>
                                                </span>
                                            </td>
                                            <td align="right">
                                                <span class="balance-available-display" data-id="<?php echo $bkey+1; ?>">
                                                    <?php echo app_format_money($bvalue['balance_available'], $base_currency); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-8 col-md-offset-4">
                        <table class="table text-right">
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('total').' '._l('budgeted_amount'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['total_budget_summary']['budgeted_amount'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('total_previous_billing'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['total_budget_summary']['total_previous_billing'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('total_current_billing_amount'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['total_budget_summary']['total_current_billing_amount'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('total_cumulative_billing'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['total_budget_summary']['total_cumulative_billing'], $base_currency); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="bold tw-text-neutral-700"><?php echo _l('total').' '._l('balance_available'); ?> :</span>
                                    </td>
                                    <td>
                                        <?php echo app_format_money($annexure_invoice['total_budget_summary']['balance_available'], $base_currency); ?>
                                    </td>
                                </tr>
                                <?php hooks()->do_action('after_admin_invoice_form_total_field', $invoice ?? null); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php
                $annexures = get_all_annexures();
                $i = 1;
                foreach ($annexures as $key => $annexure) { ?>
                    <div role="tabpanel" class="tab-pane" id="<?php echo $annexure['annexure_key']; ?>">
                        <div class="table-responsive s_table">
                            <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                                <thead>
                                    <tr>
                                        <th width="13%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1"
                                        aria-hidden="true" data-toggle="tooltip"
                                        data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i>
                                        <?php echo _l('budget_head'); ?></th>
                                        <th width="15%" align="left"><?php echo _l('description_of_services'); ?></th>
                                        <th width="13%" align="left"><?php echo _l('vendor'); ?></th>
                                        <th width="13%" align="left"><?php echo _l('invoice_no'); ?></th>
                                        <th width="13%" align="left"><?php echo _l('invoice_date'); ?></th>
                                        <th width="13%" align="left"><?php echo _l('rate_without_tax'); ?></th>
                                        <th width="10%" align="left"><?php echo _l('invoice_table_tax_heading'); ?></th>
                                        <th width="13%" align="left"><?php echo _l('invoice_table_amount_heading'); ?></th>
                                        <th width="13%" align="right"><?php echo _l('remarks'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($invoice) || isset($add_items))
                                    {
                                        if (isset($invoice)) {
                                            $add_items = $invoice->items;
                                        }
                                        foreach ($add_items as $item) {
                                            if($item['annexure'] == $annexure['id']) {
                                                $vendor_name = '';
                                                $invoice_no = $invoice_date = '';
                                                if(!empty($item['vbt_id'])) {
                                                    $pur_invoices = get_pur_invoices($item['vbt_id']);
                                                    $vendor = get_vendor_details($pur_invoices->vendor);
                                                    $vendor_name = $vendor->company;
                                                    $invoice_no = $pur_invoices->vendor_invoice_number;
                                                    $invoice_date = date('d M, Y', strtotime($pur_invoices->invoice_date));
                                                }
                                                $table_row = '<tr class="sortable item">';
                                                $amount = ($item['rate'] * $item['qty']) + $item['tax'];
                                                $amount = app_format_money($amount, $base_currency);
                                                $table_row .= '<td>' . clear_textarea_breaks($item['description']) . '</td>';
                                                $table_row .= '<td>' . clear_textarea_breaks($item['long_description']) . '</td>';
                                                $table_row .= '<td>'.$vendor_name.'</td>';
                                                $table_row .= '<td>'.$invoice_no.'</td>';
                                                $table_row .= '<td>' . $invoice_date . '</td>';
                                                $table_row .= '<td>' . app_format_money($item['rate'],$base_currency) . '</td>';
                                                $table_row .= '<td>' . app_format_money($item['tax'],$base_currency) . '</td>';
                                                $table_row .= '<td>' . $amount . '</td>';
                                                $table_row .= '<td align="right">' . clear_textarea_breaks($item['remarks']) . '</td>';
                                                $table_row .= '</tr>';
                                                echo $table_row;
                                                $i++;
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-8 col-md-offset-4">
                            <table class="table text-right">
                                <tbody>
                                    <?php
                                    if (!empty($indexa)) {
                                        foreach ($indexa as $akey => $avalue) {
                                            if($annexure['id'] == $avalue['annexure']) { ?>
                                                <tr id="subtotal">
                                                    <td>
                                                        <span class="bold tw-text-neutral-700"><?php echo _l('subtotal_without_tax'); ?> :</span>
                                                    </td>
                                                    <td>
                                                        <?php echo app_format_money($avalue['subtotal'], $base_currency); ?>
                                                    </td>
                                                </tr>
                                                <tr id="total_tax">
                                                    <td>
                                                        <span class="bold tw-text-neutral-700"><?php echo _l('tax'); ?> :</span>
                                                    </td>
                                                    <td>
                                                        <?php echo app_format_money($avalue['tax'], $base_currency); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="bold tw-text-neutral-700"><?php echo _l('invoice_total'); ?> :</span>
                                                    </td>
                                                    <td>
                                                        <?php echo app_format_money($avalue['amount'], $base_currency); ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
        <?php /*
        <div class="col-md-5 col-md-offset-7">
            <table class="table text-right">
                <tbody>
                    <tr id="subtotal">
                        <td>
                            <span class="bold tw-text-neutral-700"><?php echo _l('invoice_subtotal'); ?></span>
                        </td>
                        <td class="subtotal">
                            <?php echo e(app_format_money($invoice->subtotal, $invoice->currency_name)); ?>
                        </td>
                    </tr>
                    <?php if (is_sale_discount_applied($invoice)) { ?>
                    <tr>
                        <td>
                            <span class="bold tw-text-neutral-700"><?php echo _l('invoice_discount'); ?>
                                <?php if (is_sale_discount($invoice, 'percent')) { ?>
                                (<?php echo e(app_format_number($invoice->discount_percent, true)); ?>%)
                                <?php } ?>
                            </span>
                        </td>
                        <td class="discount">
                            <?php echo e('-' . app_format_money($invoice->discount_total, $invoice->currency_name)); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if ((int)$invoice->adjustment != 0) { ?>
                    <tr>
                        <td>
                            <span class="bold tw-text-neutral-700"><?php echo _l('invoice_adjustment'); ?></span>
                        </td>
                        <td class="adjustment">
                            <?php echo e(app_format_money($invoice->adjustment, $invoice->currency_name)); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>
                            <span class="bold tw-text-neutral-700"><?php echo _l('invoice_total'); ?></span>
                        </td>
                        <td class="total">
                            <?php echo e(app_format_money($invoice->total, $invoice->currency_name)); ?>
                        </td>
                    </tr>
                    <?php if (count($invoice->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) { ?>
                    <tr>
                        <td>
                            <span class="bold tw-text-neutral-700"><?php echo _l('invoice_total_paid'); ?></span>
                        </td>
                        <td>
                            <?php echo e('-' . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', ['field' => 'amount', 'where' => ['invoiceid' => $invoice->id]]), $invoice->currency_name)); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) { ?>
                    <tr>
                        <td>
                            <span class="bold tw-text-neutral-700"><?php echo _l('applied_credits'); ?></span>
                        </td>
                        <td>
                            <?php echo e('-' . app_format_money($credits_applied, $invoice->currency_name)); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if (get_option('show_amount_due_on_invoice') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) { ?>
                    <tr>
                        <td>
                            <span class="<?php echo $invoice->total_left_to_pay > 0 ? 'text-danger ': ''; ?> bold">
                                <?php echo _l('invoice_amount_due'); ?>
                            </span>
                        </td>
                        <td>
                            <span class="<?php echo $invoice->total_left_to_pay > 0 ? 'text-danger ': ''; ?>">
                                <?php echo e(app_format_money($invoice->total_left_to_pay, $invoice->currency_name)); ?>
                            </span>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div> */ ?>
    </div>
    <?php if (count($invoice->attachments) > 0) { ?>
    <div class="clearfix"></div>
    <hr />
    <p class="bold text-muted"><?php echo _l('invoice_files'); ?></p>
    <?php foreach ($invoice->attachments as $attachment) {
                  $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                  if (!empty($attachment['external'])) {
                      $attachment_url = $attachment['external_link'];
                  } ?>
    <div class="mbot15 row inline-block full-width" data-attachment-id="<?php echo e($attachment['id']); ?>">
        <div class="col-md-8">
            <div class="pull-left"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i></div>
            <a href="<?php echo e($attachment_url); ?>" target="_blank"><?php echo e($attachment['file_name']); ?></a>
            <br />
            <small class="text-muted"> <?php echo e($attachment['filetype']); ?></small>
        </div>
        <div class="col-md-4 text-right tw-space-x-2">
            <?php if ($attachment['visible_to_customer'] == 0) {
                      $icon    = 'fa-toggle-off';
                      $tooltip = _l('show_to_customer');
                  } else {
                      $icon    = 'fa-toggle-on';
                      $tooltip = _l('hide_from_customer');
                  } ?>
            <a href="#" data-toggle="tooltip"
                onclick="toggle_file_visibility(<?php echo e($attachment['id']); ?>,<?php echo e($invoice->id); ?>,this); return false;"
                data-title="<?php echo e($tooltip); ?>"><i class="fa <?php echo e($icon); ?> fa-lg"
                    aria-hidden="true"></i></a>
            <?php if ($attachment['staffid'] == get_staff_user_id() || is_admin()) { ?>
            <a href="#" class="text-danger"
                onclick="delete_invoice_attachment(<?php echo e($attachment['id']); ?>); return false;"><i
                    class="fa fa-times fa-lg"></i></a>
            <?php } ?>
        </div>
    </div>
    <?php
              } ?>
    <?php } ?>
    <hr />
    <?php if ($invoice->clientnote != '') { ?>
    <div class="col-md-12 row mtop15">
        <p class="bold text-muted"><?php echo _l('invoice_note'); ?></p>
        <?php echo process_text_content_for_display($invoice->clientnote); ?>
    </div>
    <?php } ?>
    <?php if ($invoice->terms != '') { ?>
    <div class="col-md-12 row mtop15">
        <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
        <?php echo process_text_content_for_display($invoice->terms); ?>
    </div>
    <?php } ?>
</div>
