<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .onoffswitch-label:before {

        height: 20px !important;
    }
    .dashboard_stat_title {
      font-size: 18px;
      font-weight: bold;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_hidden('purchase_id', $purchase_id); ?>
                        <div class="row">
                            <div class="col-md-12" style="padding: 0px;">
                                <div class="col-md-12" id="heading">
                                    <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> Stock Received</h4>
                                    <hr />
                                </div>
                                <?php /* <div class="col-md-2 display-flex" id="filter_div">
                                    <label>PO Not received</label>
                                    <div class="onoffswitch" style="margin-left: 10px;">
                                        <input type="checkbox" name="toggle-filter" class="onoffswitch-checkbox toggle-filter" id="c_' . $aRow['staffid'] . '" value="0">
                                        <label class="onoffswitch-label" for="c_' . $aRow['staffid'] . '"></label>
                                    </div>

                                    <hr />
                                </div> */ ?>

                            </div>

                        </div>
                        <div class="row">
                            <div class="_buttons col-md-3">
                                <?php if (has_permission('warehouse', '', 'create') || is_admin()) { ?>
                                    <a href="<?php echo admin_url('warehouse/manage_goods_receipt'); ?>" class="btn btn-info pull-left mright10 display-block">
                                        Add New
                                    </a>
                                <?php } ?>
                                <button class="btn btn-info pull-left mleft10 display-block" type="button" data-toggle="collapse" data-target="#sr-charts-section" aria-expanded="true"aria-controls="sr-charts-section">
                                <?php echo _l('Stock Received Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                                </button>
                            </div>
                            <div class="col-md-1 pull-right">
                                <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal(' .purchase_sm','#purchase_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
                            </div>
                        </div>

                        <div id="sr-charts-section" class="collapse in">
                            <div class="row">
                                 <div class="col-md-12 mtop20">
                                    <div class="row">
                                       <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                             <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                                      </path>                          
                                                   </svg>                          
                                                   <span class="tw-truncate">Total Receipts (By period)</span>                 
                                                </div>                      
                                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_receipts"></span>  
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
                                                   <span class="tw-truncate">Received PO / Total PO</span>                 
                                                </div>                      
                                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"><span class="total_received_po"></span> / <span class="total_po"></span></span>
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
                                                   <span class="tw-truncate">Total Quantity Received</span>                 
                                                </div>                      
                                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_quantity_received"></span>  
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
                                                   <span class="tw-truncate">Client Supply / Bought out items</span>                 
                                                </div>                      
                                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"><span class="total_client_supply"></span> / <span class="total_bought_out_items"></span></span>
                                             </div>                    
                                             <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                                <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                                </div>                  
                                             </div>              
                                          </div>          
                                       </div>

                                    </div>
                                 </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-4">
                                  <p class="mbot15 dashboard_stat_title">Receipts Over Time</p>
                                  <div style="width: 100%; height: 400px;">
                                    <canvas id="lineChartOverTime"></canvas>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <p class="mbot15 dashboard_stat_title">Top 10 Suppliers by Receipts</p>
                                  <div style="width: 100%; height: 400px;">
                                    <canvas id="barChartTopVendors"></canvas>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <p class="mbot15 dashboard_stat_title">Documentation Status</p>
                                  <div style="width: 100%; height: 400px; display: flex;">
                                    <canvas id="doughnutChartDocumentationStatus"></canvas>
                                  </div>
                                </div>
                            </div>
                       </div>
                        
                        <div class="row mtop20">
                            <div class="col-md-3 form-group pull-right">
                                <select name="kind" id="kind" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('cat'); ?>">
                                    <option value=""></option>
                                    <option value="Client Supply"><?php echo _l('client_supply'); ?></option>
                                    <option value="Bought out items"><?php echo _l('bought_out_items'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3 pull-right">
                                <?php
                                $input_attr_e = [];
                                $input_attr_e['placeholder'] = _l('day_vouchers');

                                echo render_date_input('date_add', '', '', $input_attr_e); ?>
                            </div>
                            <div class="col-md-3 form-group pull-right">
                                <select name="vendor[]" id="vendor" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
                                    <option value=""></option>
                                    <?php
                                    $vendor = get_pur_vendor_list();
                                    foreach ($vendor as $vendors) { ?>
                                        <option value="<?php echo $vendors['userid']; ?>"><?php echo  $vendors['company']; ?></option>
                                    <?php  } ?>
                                </select>
                            </div>
                            <div class="col-md-3 form-group pull-right">
                                <select name="status" id="status" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
                                    <option value=""></option>
                                    <option value="approved"><?php echo _l('approved'); ?></option>
                                    <option value="not_yet_approve"><?php echo _l('not_yet_approve'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group" id="report-time">
                                <select class="selectpicker" name="months-report" id="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                                    <option value="this_month"><?php echo _l('this_month'); ?></option>
                                    <option value="1"><?php echo _l('last_month'); ?></option>
                                    <option value="this_year"><?php echo _l('this_year'); ?></option>
                                    <option value="last_year"><?php echo _l('last_year'); ?></option>
                                    <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                                    <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                                    <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                                    <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                                </select>
                            </div>
                            <div id="date-range" class="hide">
                                <div class="col-md-3 form-group">
                                    <div class="input-group date">
                                        <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 form-group">
                                    <div class="input-group date">
                                        <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                        <?php render_datatable(array(
                            _l('id'),
                            _l('stock_received_docket_code'),
                            _l('supplier_name'),
                            _l('Prepared By'),
                            _l('category'),
                            _l('reference_order'),
                            _l('Receive Date'),

                            // _l('total_tax_money'),
                            // _l('total_goods_money'),
                            // _l('value_of_inventory'),
                            // _l('total_money'),
                            _l('status_label'),
                            _l('options'),
                        ), 'table_manage_goods_receipt', ['purchase_sm' => 'purchase_sm']); ?>

                    </div>
                </div>
            </div>

            <div class="col-md-7 small-table-right-col">
                <div id="purchase_sm_view" class="hide">
                </div>
            </div>

        </div>
    </div>
</div>



<script>
    var hidden_columns = [3, 4, 5];
</script>
<?php init_tail(); ?>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>