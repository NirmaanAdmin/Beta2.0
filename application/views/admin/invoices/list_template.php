<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
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
            <button class="btn btn-info pull-left mleft10 display-block" type="button" data-toggle="collapse" data-target="#ci-charts-section" aria-expanded="true"aria-controls="ci-charts-section">
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
                       <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                             <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                      </path>                          
                                   </svg>                          
                                   <span class="tw-truncate">Total Invoices Raised</span>                 
                                </div>                      
                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_invoices_raised"></span>  
                             </div>                    
                             <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                </div>                  
                             </div>              
                          </div>          
                       </div>

                       <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                             <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                      </path>                          
                                   </svg>                          
                                   <span class="tw-truncate">Total Invoiced Amount</span>                 
                                </div>                      
                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_invoiced_amount"></span>  
                             </div>                    
                             <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                </div>                  
                             </div>              
                          </div>          
                       </div>

                       <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                             <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                      </path>                          
                                   </svg>                          
                                   <span class="tw-truncate">Average Invoice Value</span>                 
                                </div>                      
                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 average_invoice_value"></span>  
                             </div>                    
                             <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                </div>                  
                             </div>              
                          </div>          
                       </div>
                    </div>
                    <div class="row mtop20">
                        <div class="col-md-4">
                          <p class="mbot15 dashboard_stat_title" style="font-size: 18px; font-weight: bold;">Invoicing Trends Over Time</p>
                          <div style="width: 100%; height: 400px;">
                            <canvas id="lineChartOverTime"></canvas>
                          </div>
                        </div>
                        <div class="col-md-4">
                            <p class="mbot15 dashboard_stat_title" style="font-size: 18px; font-weight: bold;">Pie Chart for Invoice per Project</p>
                            <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                               <canvas id="pieChartForProject"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <p class="mbot15 dashboard_stat_title" style="font-size: 18px; font-weight: bold;">Pie Chart for Invoice per Status</p>
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