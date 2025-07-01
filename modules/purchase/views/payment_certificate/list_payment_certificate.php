<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'payment_certificate'; ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 5000;
      left: 204px
   }

   .show_hide_columns1 {
      position: absolute;
      z-index: 5000;
      left: 204px
   }
   .dashboard_stat_title {
      font-size: 18px;
      font-weight: bold;
   }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">

         <div class="row">
            <div class="col-md-12" id="small-table">
               <div class="panel_s">
                  <div class="panel-body">
                     <div class="row">
                        <div class="col-md-12">
                           <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('payment_certificate'); ?></h4>
                           <hr />
                        </div>
                        <div class="col-md-3">
                           <button class="btn btn-info display-block" type="button" data-toggle="collapse" data-target="#pc-charts-section" aria-expanded="true" aria-controls="pc-charts-section">
                              <?php echo _l('Payment Certificate Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                           </button>
                        </div>
                     </div>

                     <div id="pc-charts-section" class="collapse in">
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
                                             <span class="tw-truncate">Total Purchase Orders</span>                 
                                          </div>                      
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_purchase_orders"></span>  
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
                                             <span class="tw-truncate">Total Work Orders</span>                 
                                          </div>                      
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_work_orders"></span>  
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
                                             <span class="tw-truncate">Total Certified Value</span>                 
                                          </div>                      
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_certified_value"></span>  
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
                                             <span class="tw-truncate">Approved Payment Certificates</span>                 
                                          </div>                      
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 approved_payment_certificates"></span>  
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
                           <div class="col-md-6">
                              <p class="mbot15 dashboard_stat_title">Bar Chart for Top 10 Vendors by Certified Value</p>
                              <div style="width: 100%; height: 400px;">
                                <canvas id="barChartTopVendors"></canvas>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <p class="mbot15 dashboard_stat_title">Certified Value Over Time</p>
                              <div style="width: 100%; height: 400px;">
                                <canvas id="lineChartOverTime"></canvas>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row all_ot_filters mtop20">
                        <div class="col-md-2 form-group">
                           <?php
                           $vendors_type_filter = get_module_filter($module_name, 'vendors');
                           $vendors_type_filter_val = !empty($vendors_type_filter) ? explode(",", $vendors_type_filter->filter_value) : [];
                           echo render_select('vendors[]', $vendors, array('userid', 'company'), '', $vendors_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('pur_vendor'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-2 form-group">
                           <?php
                           $group_pur_type_filter = get_module_filter($module_name, 'group_pur');
                           $group_pur_type_filter_val = !empty($group_pur_type_filter) ? explode(",", $group_pur_type_filter->filter_value) : [];
                           echo render_select('group_pur[]', $item_group, array('id', 'name'), '', $group_pur_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('group_pur'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-2 form-group">
                           <?php
                           $approval_status_type_filter = get_module_filter($module_name, 'approval_status');
                           $approval_status_type_filter_val = !empty($approval_status_type_filter) ? explode(",", $approval_status_type_filter->filter_value) : [];
                           $payment_status = [
                              ['id' => 1, 'name' => 'Send approval request'],
                              ['id' => 2, 'name' => 'Approved'],
                              ['id' => 3, 'name' => 'Rejected'],
                           ];
                           echo render_select('approval_status[]', $payment_status, array('id', 'name'), '', $approval_status_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('approval_status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <?php
                        $projects_type_filter = get_module_filter($module_name, 'projects');
                        $projects_type_filter_val = !empty($projects_type_filter) ? explode(",", $projects_type_filter->filter_value) : [];
                        ?>
                        <div class="col-md-2 form-group">
                           <select name="projects[]" id="projects" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                              <?php foreach ($projects as $pj) { ?>
                                 <option value="<?php echo pur_html_entity_decode($pj['id']); ?>"
                                    <?php echo in_array($pj['id'], $projects_type_filter_val) ? 'selected' : ''; ?>>
                                    <?php echo pur_html_entity_decode($pj['name']); ?>
                                 </option>
                              <?php } ?>
                           </select>
                        </div>
                        <div class="col-md-1 form-group ">
                           <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
                              <?php echo _l('reset_filter'); ?>
                           </a>
                        </div>
                     </div>
                     <br>

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
                              'Payment cert',
                              'project',
                              'order_name',
                              'vendor',
                              'order_date',
                              'group_pur',
                              'approval_status',
                              'applied_to_vendor_bill'
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


                     <?php $table_data = array(
                        _l('Payment cert'),
                        _l('project'),
                        _l('order_name'),
                        _l('vendor'),
                        _l('order_date'),
                        _l('group_pur'),
                        _l('approval_status'),
                        _l('applied_to_vendor_bill'),
                     );

                     foreach ($custom_fields as $field) {
                        array_push($table_data, $field['name']);
                     }
                     render_datatable($table_data, 'table_payment_certificate');
                     ?>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php init_tail(); ?>
<script>
   $(document).ready(function() {
      var table_payment_certificate = $('.table-table_payment_certificate');
      var Params = {
         "vendors": "[name='vendors[]']",
         "group_pur": "[name='group_pur[]']",
         "approval_status": "[name='approval_status[]']",
         "projects": "[name='projects[]']",
      };
      initDataTable(table_payment_certificate, admin_url + 'purchase/table_payment_certificate', [], [], Params, [4, 'desc']);
      $.each(Params, function(i, obj) {
         $('select' + obj).on('change', function() {
            table_payment_certificate.DataTable().ajax.reload();
         });
      });
      $(document).on('click', '.reset_all_ot_filters', function() {
         var filterArea = $('.all_ot_filters');
         filterArea.find('input').val("");
         filterArea.find('select').not('select[name="projects[]"]').selectpicker("val", "");
         table_payment_certificate.DataTable().ajax.reload();
      });

      $('#pc-charts-section').on('shown.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
      });

      $('#pc-charts-section').on('hidden.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
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
      });

      // Sync checkboxes with column visibility on page load
      table.columns().every(function(index) {
         var column = this;
         $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

   });
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>