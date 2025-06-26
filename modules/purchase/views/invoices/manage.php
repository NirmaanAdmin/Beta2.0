<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 999;
      left: 384px
   }

   /* Ensure the table uses correct layout */
   table {
      table-layout: auto !important;
      width: 100%;
      border-collapse: collapse;
   }

   /* Ensure table cells do not force text stacking */
   th,
   td {
      white-space: normal;
      word-wrap: break-word;
      overflow-wrap: break-word;
      vertical-align: top;
   }

   /* Ensure tag container allows wrapping */
   .tags-labels {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
      max-width: 100%;
      align-items: center;
   }

   /* Ensure each tag is inline and does not stack vertically */
   .label-tag {
      display: inline-block;
      max-width: 100%;
      white-space: nowrap;
      /* Prevent text from stacking */
      overflow: hidden;
      text-overflow: ellipsis;
      padding: 5px 10px;
      background: #f0f0f0;
      border-radius: 5px;
   }

   /* Ensure tags do not stretch vertically */
   .label-tag .tag {
      display: inline;
   }

   .bulk-title {
      text-align: center;
      font-weight: bold;
   }
   .dashboard_stat_title {
      font-size: 18px;
      font-weight: bold;
   }
</style>
<?php $module_name = 'vendor_billing_tracker'; ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                        <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                        <hr />
                     </div>
                  </div>
                  <div class="row">
                     <div class="_buttons col-md-12">
                        <?php if (has_permission('purchase_invoices', '', 'create') || is_admin()) { ?>
                           <a href="<?php echo admin_url('purchase/pur_invoice'); ?>" class="btn btn-info pull-left mright10 display-block">
                              <?php echo _l('new'); ?>
                           </a>
                        <?php } ?>
                        <button class="btn btn-info pull-left mleft10 display-block" type="button" data-toggle="collapse" data-target="#vbt-charts-section" aria-expanded="true"aria-controls="vbt-charts-section">
                         <?php echo _l('Vendor Billing Tracker Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                        </button>
                     </div>
                  </div>

                  <div id="vbt-charts-section" class="collapse in">
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
                                          <span class="tw-truncate">Total certified amount</span>                 
                                       </div>                      
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_certified_amount"></span>  
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
                                          <span class="tw-truncate">Total untagged bills to orders</span>                 
                                       </div>                      
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_bills_not_tag_to_orders"></span>  
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
                                          <span class="tw-truncate">Uninvoice bills</span>                 
                                       </div>                      
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_uninvoice_bills"></span>  
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
                                          <span class="tw-truncate">To be invoiced</span>                 
                                       </div>                      
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_pending_amount_to_be_invoice"></span>  
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
                                 <p class="mbot15 dashboard_stat_title">Top 10 Vendors by Total Certified Amount</p>
                                 <div style="width: 100%; height: 500px;">
                                   <canvas id="barChartTopVendors"></canvas>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <p class="mbot15 dashboard_stat_title">Total Certified Amount per Budget Head</p>
                                 <div style="width: 100%; height: 500px; display: flex; justify-content: left;">
                                    <canvas id="pieChartForBudget"></canvas>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <p class="mbot15 dashboard_stat_title">Total Vendor Bills per Billing Status</p>
                                 <div style="width: 100%; height: 470px; display: flex; justify-content: left;">
                                    <canvas id="pieChartForBilling"></canvas>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row vbt_all_filters mtop20">
                     <div class="col-md-2">
                        <?php
                        $from_date_filter = get_module_filter($module_name, 'from_date');
                        $from_date_filter_val = !empty($from_date_filter) ? $from_date_filter->filter_value : '';
                        echo render_date_input('from_date', '', $from_date_filter_val, array('placeholder' => _l('from_date')));
                        ?>
                     </div>

                     <div class="col-md-2">
                        <?php
                        $to_date_filter = get_module_filter($module_name, 'to_date');
                        $to_date_filter_val = !empty($to_date_filter) ? $to_date_filter->filter_value : '';
                        echo render_date_input('to_date', '', $to_date_filter_val, array('placeholder' => _l('to_date')));
                        ?>
                     </div>

                     <div class="col-md-3">
                        <?php
                        $vendors_filter = get_module_filter($module_name, 'vendors');
                        $vendors_filter_val = !empty($vendors_filter) ? explode(",", $vendors_filter->filter_value) : '';
                        echo render_select('vendor_ft[]', $vendors, array('userid', 'company'), '', $vendors_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('vendors'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                        ?>
                     </div>

                     <div class="col-md-3 form-group">
                        <?php
                        $billing_invoices_filter = get_module_filter($module_name, 'billing_invoices');
                        $billing_invoices_filter_val = !empty($billing_invoices_filter) ? $billing_invoices_filter->filter_value : '';
                        ?>
                        <select name="billing_invoices" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('pur_invoices'); ?>" data-actions-box="true">
                           <option value=""></option>
                           <option value="None" <?php echo ($billing_invoices_filter_val == 'None') ? 'selected' : ''; ?>>To Be Converted</option>
                           <?php foreach ($billing_invoices as $invoice) { ?>
                              <option value="<?php echo $invoice['id']; ?>" <?php echo ($billing_invoices_filter_val == $invoice['id']) ? 'selected' : ''; ?>><?php echo $invoice['value']; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <div class="col-md-3 form-group">
                        <?php
                        $budget_head_filter = get_module_filter($module_name, 'budget_head');
                        $budget_head_filter_val = !empty($budget_head_filter) ? $budget_head_filter->filter_value : '';
                        ?>
                        <select name="budget_head" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('group_pur'); ?>" data-actions-box="true">
                           <option value=""></option>
                           <option value="None">None</option>
                           <?php foreach ($budget_head as $head) { ?>
                              <option value="<?php echo $head['id']; ?>" <?php echo ($budget_head_filter_val == $head['id']) ? 'selected' : ''; ?>><?php echo $head['name']; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <div class="col-md-3 form-group">
                        <?php
                        $billing_status_filter = get_module_filter($module_name, 'billing_status');
                        $billing_status_filter_val = !empty($billing_status_filter) ? $billing_status_filter->filter_value : '';
                        $billing_status = [
                           ['id' => 1, 'name' => _l('rejected')],
                           ['id' => 2, 'name' => _l('recevied_with_comments')],
                           ['id' => 3, 'name' => _l('bill_verification_in_process')],
                           ['id' => 4, 'name' => _l('bill_verification_on_hold')],
                           ['id' => 5, 'name' => _l('bill_verified_by_ril')],
                           ['id' => 6, 'name' => _l('payment_certifiate_issued')],
                           ['id' => 7, 'name' => _l('payment_processed')],
                           ['id' => 8, 'name' => _l('unpaid')]
                        ];

                        ?>
                        <select name="billing_status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('billing_status'); ?>" data-actions-box="true">
                           <option value=""></option>
                           <option value="None">None</option>
                           <?php foreach ($billing_status as $head) { ?>
                              <option value="<?php echo $head['id']; ?>" <?php echo ($billing_status_filter_val == $head['id']) ? 'selected' : ''; ?>><?php echo $head['name']; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <div class="col-md-1 form-group">
                        <a href="javascript:void(0)" class="btn btn-info btn-icon reset_vbt_all_filters">
                           <?php echo _l('reset_filter'); ?>
                        </a>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-offset-9 col-md-3">
                        <div style="display: flex;align-items: end;padding: 0px;">
                           <?php echo form_open_multipart(admin_url('purchase/import_file_xlsx_vendor_billing_tracker'), array('id' => 'import_form')); ?>
                           <?php echo render_input('file_csv', 'choose_excel_file', '', 'file'); ?>
                           <div class="form-group">
                              <button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv(this);"><?php echo _l('import'); ?></button>
                              <a href="<?php echo site_url('modules/purchase/uploads/file_sample/Sample_vendor_billing_tracker_item_en.xlsx') ?>" class="btn btn-primary">Template</a>
                           </div>
                           <?php echo form_close(); ?>
                           <div class="form-group" id="file_upload_response" style="padding-left: 20px;">
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <a onclick="bulk_convert_ril_bill(); return false;" data-toggle="modal" data-table=".table-table_pur_invoices" class=" hide bulk-actions-btn table-btn">Bulk Convert</a>
                     <a onclick="bulk_assign_ril_bill(); return false;" data-toggle="modal" data-table=".table-table_pur_invoices" class=" hide bulk-actions-btn table-btn">Bulk Assign</a>
                  </div>

                  <?php
                  /*
                    $table_data = array(
                        _l('invoice_code'),
                        _l('invoice_number'),
                        _l('vendor'), 
                        _l('group_pur'),                       
                        // _l('project'),
                        _l('order_name'),
                        // _l('wo_order'),
                        _l('invoice_date'),
                        // _l('payment_request_status'),
                        _l('billing_status'),
                        _l('convert_expense'),
                        _l('amount_without_tax'),
                        _l('tax_value'),
                        _l('total_included_tax'),
                        _l('certified_amount'),
                        _l('transaction_id'),
                        _l('tag'),
                        );

                    $custom_fields = get_custom_fields('pur_invoice',array('show_on_table'=>1));
                    foreach($custom_fields as $field){
                     array_push($table_data,$field['name']);
                    } ?> */ ?>
                  <div class="btn-group show_hide_columns" id="show_hide_columns">
                     <!-- Settings Icon -->
                     <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                        <i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
                     </button>
                     <!-- Dropdown Menu with Checkboxes -->
                     <div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
                        <!-- Select All / Deselect All -->
                        <div>
                           <input type="checkbox" id="select-all-columns"> <strong><?php echo _l('select_all'); ?></strong>
                        </div>
                        <hr>
                        <!-- Column Checkboxes -->
                        <?php
                        $columns = [
                           'Checkbox',
                           'id',
                           'invoice_code',
                           'invoice_number',
                           'vendor',                      
                           'invoice_date',
                           'Billing Budget Head',
                           'description_of_services',
                           'ril_invoice',
                           'amount_without_tax',
                           'vendor_submitted_tax_amount',
                           'certified_amount',
                           'billing_status',
                           'vbt_order_name',
                           'Order Budget Head',
                           'tag',
                           'attachment',
                           'adminnote',
                        ];
                        ?>
                        <div>
                           <?php foreach ($columns as $key => $label): ?>
                              <input type="checkbox" class="toggle-column" value="<?php echo $key; ?>" checked>
                              <?php echo _l($label); ?><br>
                           <?php endforeach; ?>
                        </div>

                     </div>
                  </div>
                  <div class="">
                     <table class="dt-table-loading table table-table_pur_invoices">
                        <thead>
                           <tr>
                              <th style="width: 5px"><span class="hide"> - </span>
                                 <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_pur_invoices"><label></label></div>
                              </th>
                              <th>#</th>
                              <th><?php echo _l('invoice_code'); ?></th>
                              <th><?php echo _l('invoice_number'); ?></th>
                              <th><?php echo _l('vendor'); ?></th>
                              <th><?php echo _l('invoice_date'); ?></th>
                              <th>Billing <?php echo _l('group_pur'); ?></th>
                              <th><?php echo _l('description_of_services'); ?></th>
                              <th><?php echo _l('ril_invoice'); ?></th>
                              <th><?php echo _l('amount_without_tax'); ?></th>
                              <th><?php echo _l('vendor_submitted_tax_amount'); ?></th>
                              <th><?php echo _l('final_certified_amount'); ?></th>
                              <th><?php echo _l('billing_status'); ?></th>
                              <th><?php echo _l('vbt_order_name'); ?></th>
                              <th>Order Budget Head</th>
                              <!-- <th><?php echo _l('vbt_order_amount'); ?></th> -->
                              <th><?php echo _l('tag'); ?></th>
                              <th><?php echo _l('attachment'); ?></th>
                              <th><?php echo _l('adminnote'); ?></th>
                           </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                           <tr>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td class="total_vendor_submitted_amount_without_tax"></td>
                              <td class="total_vendor_submitted_tax_amount"></td>
                              <td class="total_final_certified_amount"></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>

               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="pur_invoice_expense" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <?php echo form_open(admin_url('purchase/add_invoice_expense'), array('id' => 'pur_invoice-expense-form', 'class' => 'dropzone dropzone-manual')); ?>
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('add_new', _l('expense_lowercase')); ?></h4>
         </div>
         <div class="modal-body">
            <div id="dropzoneDragArea" class="dz-default dz-message">
               <span><?php echo _l('expense_add_edit_attach_receipt'); ?></span>
            </div>
            <div class="dropzone-previews"></div>
            <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('expense_name_help'); ?>"></i>
            <?php echo form_hidden('vendor'); ?>
            <?php echo render_input('expense_name', 'description_of_services'); ?>
            <?php echo render_textarea('note', 'expense_add_edit_note', '', array('rows' => 4), array()); ?>
            <?php echo render_select('clientid', $customers, array('userid', 'company'), 'customer'); ?>

            <?php echo render_select('project_id', $projects, array('id', 'name'), 'project'); ?>

            <?php echo render_select('category', $expense_categories, array('id', 'name'), 'expense_category'); ?>
            <?php echo render_date_input('date', 'expense_add_edit_date', _d(date('Y-m-d'))); ?>
            <?php echo render_input('amount', 'expense_add_edit_amount', '', 'number', ['readonly' => true]); ?>
            <div class="row mbot15">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="control-label" for="tax"><?php echo _l('tax_1'); ?></label>
                     <select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""><?php echo _l('no_tax'); ?></option>
                        <?php foreach ($taxes as $tax) { ?>
                           <option value="<?php echo pur_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo pur_html_entity_decode($tax['name']); ?>"><?php echo pur_html_entity_decode($tax['taxrate']); ?>%</option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="control-label" for="tax2"><?php echo _l('tax_2'); ?></label>
                     <select class="selectpicker display-block" data-width="100%" name="tax2" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" disabled>
                        <option value=""><?php echo _l('no_tax'); ?></option>
                        <?php foreach ($taxes as $tax) { ?>
                           <option value="<?php echo pur_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo pur_html_entity_decode($tax['name']); ?>"><?php echo pur_html_entity_decode($tax['taxrate']); ?>%</option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="hide">
               <?php echo render_select('currency', $currencies, array('id', 'name', 'symbol'), 'expense_currency', $currency->id); ?>
            </div>

            <div class="checkbox checkbox-primary">
               <input type="checkbox" id="billable" name="billable" checked>
               <label for="billable"><?php echo _l('expense_add_edit_billable'); ?></label>
            </div>
            <?php echo render_input('reference_no', 'expense_add_edit_reference_no'); ?>

            <?php
            // Fix becuase payment modes are used for invoice filtering and there needs to be shown all
            // in case there is payment made with payment mode that was active and now is inactive
            $expenses_modes = array();
            foreach ($payment_modes as $m) {
               if (isset($m['invoices_only']) && $m['invoices_only'] == 1) {
                  continue;
               }
               if ($m['active'] == 1) {
                  $expenses_modes[] = $m;
               }
            }
            ?>
            <?php echo render_select('paymentmode', $expenses_modes, array('id', 'name'), 'payment_mode'); ?>

            <div class="row mbot15">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="control-label" for="select_invoice"><?php echo _l('invoice'); ?></label>
                     <select class="selectpicker display-block" data-width="100%" name="select_invoice" id="select_invoice" data-none-selected-text="<?php echo _l('none'); ?>">
                        <option value=""></option>
                        <option value="create_invoice"><?php echo _l('expense_convert_to_invoice'); ?></option>
                        <option value="applied_invoice"><?php echo _l('applied_to_invoice'); ?></option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6 applied-to-invoice hide">
                  <div class="form-group">
                     <label class="control-label" for="applied_to_invoice"><?php echo _l('applied_to_invoice'); ?></label>
                     <select class="selectpicker display-block" data-width="100%" name="applied_to_invoice" id="applied_to_invoice" data-none-selected-text="<?php echo _l('applied_to_invoice'); ?>">
                        <option value=""></option>
                        <?php
                        foreach ($invoices as $i) { ?>
                           <option value="<?php echo $i['id']; ?>"><?php echo e(format_invoice_number($i['id'])) . " (" . $i['title'] . ")"; ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
            </div>

            <div class="clearfix mbot15"></div>
            <?php echo render_custom_fields('expenses'); ?>
            <div id="pur_invoice_additional"></div>
            <div class="clearfix"></div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="convert_ril_bill_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-xl">
      <div class="modal-content">
         <?php echo form_open(admin_url('purchase/add_bulk_convert_ril_bill'), array('id' => 'convert_ril_bill_form', 'class' => '')); ?>
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><div class="bulk_convert_title"></div></h4>
         </div>
         <div class="modal-body convert-bulk-actions-body">
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>

<?php init_tail(); ?>
<script>
   $(document).ready(function() {
      var table = $('.table-table_pur_invoices').DataTable();

      // On page load, fetch and apply saved preferences for the logged-in user
      $.ajax({
         url: admin_url + 'purchase/getPreferences',
         type: 'GET',
         data: {
            module: 'vendor_billing_tracker'
         },
         dataType: 'json',
         success: function(data) {
            console.log("Retrieved preferences:", data);

            // Ensure DataTable is initialized
            let table = $('.table-table_pur_invoices').DataTable();

            // Loop through each toggle checkbox to update column visibility
            $('.toggle-column').each(function() {
               // Parse the column index (ensuring it's a number)
               let colIndex = parseInt($(this).val(), 10);

               // Use the saved preference if available; otherwise, default to visible ("true")
               let prefValue = data.preferences && data.preferences[colIndex] !== undefined ?
                  data.preferences[colIndex] :
                  "true";

               // Convert string to boolean if needed
               let isVisible = (typeof prefValue === "string") ?
                  (prefValue.toLowerCase() === "true") :
                  prefValue;

               // Set column visibility but prevent immediate redraw (redraw = false)
               table.column(colIndex).visible(isVisible, false);
               // Update the checkbox state accordingly
               $(this).prop('checked', isVisible);
            });

            // Finally, adjust columns and redraw the table once
            table.columns.adjust().draw();

            // Update the "Select All" checkbox based on individual toggle states
            let allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
            $('#select-all-columns').prop('checked', allChecked);
         },
         error: function() {
            console.error('Could not retrieve column preferences.');
         }
      });



      // Handle "Select All" checkbox
      $('#select-all-columns').on('change', function() {
         var isChecked = $(this).is(':checked');
         $('.toggle-column').prop('checked', isChecked).trigger('change');
      });

      // Handle individual column visibility toggling
      $('.toggle-column').on('change', function() {
         var column = table.column($(this).val());
         column.visible($(this).is(':checked'));

         // Sync "Select All" checkbox state
         var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
         $('#select-all-columns').prop('checked', allChecked);

         // Save updated preferences
         saveColumnPreferences();
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

      // Function to collect and save preferences via AJAX
      function saveColumnPreferences() {
         var preferences = {};
         $('.toggle-column').each(function() {
            preferences[$(this).val()] = $(this).is(':checked');
         });

         $.ajax({

            url: admin_url + 'purchase/savePreferences',
            type: 'POST',
            data: {
               preferences: preferences,
               module: 'vendor_billing_tracker'

            },
            success: function(response) {
               console.log('Preferences saved successfully.');
            },
            error: function() {
               console.error('Failed to save preferences.');
            }
         });
      }

      $('#vbt-charts-section').on('shown.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
      });

      $('#vbt-charts-section').on('hidden.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
      });
   });


   function uploadfilecsv() {
      "use strict";

      if (($("#file_csv").val() != '') && ($("#file_csv").val().split('.').pop() == 'xlsx')) {
         var formData = new FormData();
         formData.append("file_csv", $('#file_csv')[0].files[0]);
         if (<?php echo  pur_check_csrf_protection(); ?>) {
            formData.append(csrfData.token_name, csrfData.hash);
         }

         $.ajax({
            url: admin_url + 'purchase/import_file_xlsx_vendor_billing_tracker',
            method: 'post',
            data: formData,
            contentType: false,
            processData: false

         }).done(function(response) {
            response = JSON.parse(response);
            $("#file_csv").val(null);
            $("#file_csv").change();
            $(".panel-body").find("#file_upload_response").html();

            if ($(".panel-body").find("#file_upload_response").html() != '') {
               $(".panel-body").find("#file_upload_response").empty();
            };
            $("#file_upload_response").append("<h4><?php echo _l("_Result") ?></h4><h5><?php echo _l('import_line_number') ?> :" + response.total_rows + " </h5>");
            $("#file_upload_response").append("<h5><?php echo _l('import_line_number_success') ?> :" + response.total_row_success + " </h5>");
            $("#file_upload_response").append("<h5><?php echo _l('import_line_number_failed') ?> :" + response.total_row_false + " </h5>");
            if ((response.total_row_false > 0) || (response.total_rows_data_error > 0)) {
               $("#file_upload_response").append('<a href="' + site_url + response.filename + '" class="btn btn-warning"  ><?php echo _l('download_file_error') ?></a>');
            }
            if (response.total_rows < 1) {
               alert_float('warning', response.message);
            }
         });
         return false;

      } else if ($("#file_csv").val() != '') {
         alert_float('warning', "<?php echo _l('_please_select_a_file') ?>");
      }

   }
   $('body').on('click', '.vsawt-display', function(e) {
      e.preventDefault();

      var rowId = $(this).data('id');
      var tableType = $(this).data('type');
      var currentAmount = $(this).text().replace(/[^\d.-]/g, ''); // Remove currency formatting

      // Replace the span with an input field
      $(this).replaceWith('<input type="number" class="form-control vsawt-input" value="' + currentAmount + '" data-id="' + rowId + '">');
   });
   // Initialize the DataTable
   var table_pur_invoices = $('.table-table_pur_invoices').DataTable();
   // Inline editing for "budget"
   $('body').on('change', '.vsawt-input', function(e) {
      e.preventDefault();

      var rowId = $(this).data('id');
      var amount = $(this).val();

      var amountWithoutTax = parseFloat($(this).val()) || 0;
      var taxAmount = parseFloat($(`.vsta-display[data-id="${rowId}"]`).text().replace(/[^\d.-]/g, '')) || 0;
      var totalAmount = amountWithoutTax + taxAmount;

      // Perform AJAX request to update the budget
      $.post(admin_url + 'purchase/update_certified_amount_without_tax', {
         id: rowId,
         amount: amount
      }).done(function(response) {

         response = JSON.parse(response);
         updateAmount(rowId, totalAmount);
         if (response.success) {
            alert_float('success', response.message);
            table_pur_invoices.ajax.reload(null, false); // Reload table without refreshing the page
         } else {
            alert_float('danger', response.message);
         }
      });
   });
   $('body').on('click', '.vsta-display', function(e) {
      e.preventDefault();

      var rowId = $(this).data('id');
      var tableType = $(this).data('type');
      var currentAmount = $(this).text().replace(/[^\d.-]/g, ''); // Remove currency formatting

      // Replace the span with an input field
      $(this).replaceWith('<input type="number" class="form-control vsta-input" value="' + currentAmount + '" data-id="' + rowId + '">');
   });
   $('body').on('change', '.vsta-input', function(e) {
      e.preventDefault();

      var rowId = $(this).data('id');
      var amount = $(this).val();
      var amountWithoutTax = parseFloat($(this).val()) || 0;
      var taxAmount = parseFloat($(`.vsawt-display[data-id="${rowId}"]`).text().replace(/[^\d.-]/g, '')) || 0;
      var totalAmount = amountWithoutTax + taxAmount;
      // Perform AJAX request to update the budget
      $.post(admin_url + 'purchase/update_vendor_submitted_tax_amount', {
         id: rowId,
         amount: amount
      }).done(function(response) {
         response = JSON.parse(response);
         updateAmount(rowId, totalAmount)
         if (response.success) {
            alert_float('success', response.message);
            table_pur_invoices.ajax.reload(null, false); // Reload table without refreshing the page
         } else {
            alert_float('danger', response.message);
         }
      });
   });

   function updateAmount(rowId, totalAmount) {
      $.post(admin_url + 'purchase/update_total_amount', {
         id: rowId,
         total_amount: totalAmount
      }).done(function(response) {
         response = JSON.parse(response);
         if (response.success) {
            alert_float('success', response.message);
            table_pur_invoices.ajax.reload(null, false); // Reload table without refreshing the page
         } else {
            alert_float('danger', response.message);
         }
      });
   }
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>