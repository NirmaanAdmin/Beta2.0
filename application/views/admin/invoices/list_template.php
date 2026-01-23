<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
   .n_width {
      width: 25% !important;
   }
</style>
<div class="col-md-12">
    <div class="tw-mb-2 sm:tw-mb-4">
        <div class="_buttons">
            <?php $this->load->view('admin/invoices/invoices_top_stats'); ?>
            <?php if (staff_can('create',  'invoices')) { ?>
                <?php /* <a href="<?php echo admin_url('invoices/invoice'); ?>"
                    class="btn btn-primary pull-left new new-invoice-list mright5">
                    <i class="fa-regular fa-plus tw-mr-1"></i>
                    <?php echo _l('create_new_invoice'); ?>
                </a> */ ?>
            <?php } ?>
            <?php if (!isset($project) && !isset($customer) && staff_can('create', 'payments')) { ?>
                <button id="add-batch-payment" onclick="add_batch_payment()" class="btn btn-primary pull-left">
                    <i class="fa-solid fa-file-invoice tw-mr-1"></i>
                    <?php echo _l('batch_payments'); ?>
                </button>
            <?php } ?>
            <?php if (!isset($project)) { ?>
                <a href="<?php echo admin_url('invoices/recurring'); ?>" class="btn btn-default pull-left mleft5">
                    <i class="fa-solid fa-repeat tw-mr-1"></i>
                    <?php echo _l('invoices_list_recurring'); ?>
                </a>
            <?php } ?>
            <a href="<?php echo admin_url('purchase/activity_log?module=cli'); ?>" class="btn btn-primary pull-left mleft5" target="_blank"><?php echo _l('activity_log'); ?></a>
            <button class="btn btn-info pull-left mleft5 display-block" type="button" data-toggle="collapse" data-target="#ci-charts-section" aria-expanded="true"aria-controls="ci-charts-section">
            <?php echo _l('Client Invoices Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
            </button>
            <div class="display-block pull-right tw-space-x-0 sm:tw-space-x-1.5">
                <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs"
                    onclick="toggle_small_view('.table-invoices','#invoice'); return false;" data-toggle="tooltip"
                    title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i
                        class="fa fa-angle-double-left"></i>
                    </a>
                <a href="#" class="btn btn-default btn-with-tooltip invoices-total"
                    onclick="slideToggle('#stats-top'); init_invoices_total(true); return false;" data-toggle="tooltip"
                    title="<?php echo _l('view_stats_tooltip'); ?>">
                    <i class="fa fa-bar-chart"></i>
                </a>
                <app-filters
                            id="<?php echo $invoices_table->id(); ?>"
                            view="<?php echo $invoices_table->viewName(); ?>"
                            :rules="extra.invoicesRules || <?php echo app\services\utilities\Js::from($this->input->get('status') ? $invoices_table->findRule('status')->setValue([$this->input->get('status')]) : ($this->input->get('not_sent') ?  $invoices_table->findRule('sent')->setValue("0") : [])); ?>"
                            :saved-filters="<?php echo $invoices_table->filtersJs(); ?>"
                            :available-rules="<?php echo $invoices_table->rulesJs(); ?>">
                </app-filters>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <div id="ci-charts-section" class="collapse in">
      <div class="row">
         <div class="col-md-12 mtop20">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="row">
                        <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                           <div class="top_stats_wrapper">
                             <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                               <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                 <span class="tw-truncate dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Total Invoices Raised</span>
                               </div>
                               <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                             </div>
                             <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                               <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                 <span class="tw-truncate dashboard_stat_value total_invoices_raised" style="font-size: 19px; font-weight: bold;"></span>
                               </div>
                               <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                             </div>
                           </div>
                        </div>
                        <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                           <div class="top_stats_wrapper">
                             <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                               <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                 <span class="tw-truncate dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Total Invoiced Amount</span>
                               </div>
                               <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                             </div>
                             <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                               <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                 <span class="tw-truncate dashboard_stat_value total_invoiced_amount" style="font-size: 19px; font-weight: bold;"></span>
                               </div>
                               <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                             </div>
                           </div>
                        </div>
                        <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                           <div class="top_stats_wrapper">
                             <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                               <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                 <span class="tw-truncate dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Average Invoice Value</span>
                               </div>
                               <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                             </div>
                             <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                               <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                 <span class="tw-truncate dashboard_stat_value average_invoice_value" style="font-size: 19px; font-weight: bold;"></span>
                               </div>
                               <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                             </div>
                           </div>
                        </div>
                    </div>
                    <div class="row mtop20">
                        <div class="col-md-5">
                          <p class="mbot15 dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Invoicing Trends Over Time</p>
                          <div style="width: 100%; height: 400px;">
                            <canvas id="lineChartOverTime"></canvas>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <p class="mbot15 dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Top 10 Vendors by Amount</p>
                          <div style="width: 100%; height: 400px;">
                            <canvas id="barChartTopVendors"></canvas>
                          </div>
                        </div>
                        <div class="col-md-3">
                            <p class="mbot15 dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Pie Chart for Invoice per Status</p>
                            <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                               <canvas id="pieChartForStatus"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
      </div>
    </div>

    <div class="row">
        <div class="col-md-12" id="small-table">
            <div class="panel_s">
                <div class="panel-body panel-table-full">
                    <div class="btn-group show_hide_columns" id="show_hide_columns" style="position: absolute !important; z-index: 99999; left: 204px !important">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;"><i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
                            <div>
                                <input type="checkbox" id="select-all-columns"> <strong><?php echo _l('select_all'); ?></strong>
                            </div>
                            <hr>
                            <?php
                            $columns = [
                                '#',
                                _l('invoice_dt_table_heading_number'),
                                _l('invoice_title'),
                                _l('invoice_dt_table_heading_amount'),
                                _l('invoice_amount_due'),
                                _l('invoice_dt_table_heading_date'),
                                _l('invoice_dt_table_heading_client'),
                                _l('project'),
                                _l('tags'),
                                _l('invoice_dt_table_heading_duedate'),
                                _l('invoice_dt_table_heading_status'),
                                _l('last_action_by')
                            ];
                            ?>
                            <div>
                                <?php foreach ($columns as $key => $label): ?>
                                    <input type="checkbox" class="toggle-column" value="<?php echo $key; ?>" checked>
                                    <?php echo $label; ?><br>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <!-- if invoiceid found in url -->
                    <?php echo form_hidden('invoiceid', $invoiceid); ?>
                    <?php $this->load->view('admin/invoices/table_html'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="invoice" class="hide"></div>
        </div>
    </div>
</div>