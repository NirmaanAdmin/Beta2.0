<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .budget_actual_procurement {
    font-size: 19px;
    font-weight: bold;
  }
  .dashboard_stat_title {
    font-size: 19px;
    font-weight: bold;
  }
  .dashboard_stat_value {
    font-size: 19px;
  }
</style>
<div id="wrapper">
  <div class="content">

    <div class="panel_s">
      <div class="panel-body">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-2">
                    <?php echo render_select('vendors', $vendors, array('userid', 'company'), 'vendor'); ?>
                  </div>
                  <div class="col-md-2">
                    <?php echo render_select('projects', $projects, array('id', 'name'), 'projects'); ?>
                  </div>
                  <div class="col-md-2">
                    <?php echo render_select('group_pur', $commodity_groups_pur, array('id', 'name'), 'group_pur'); ?>
                  </div>
                  <div class="col-md-2 form-group">
                    <label for="kind"><?php echo _l('cat'); ?></label>
                  <select name="kind" id="kind" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value=""></option>
                      <option value="Client Supply"><?php echo _l('client_supply'); ?></option>
                      <option value="Bought out items"><?php echo _l('bought_out_items'); ?></option>
                    </select>
                </div>
              <div class="col-md-2">
                    <?php echo render_date_input('from_date','from_date', ''); ?>
                  </div>
                  <div class="col-md-2">
                    <?php echo render_date_input('to_date','to_date', ''); ?>
                  </div>
                  <div class="col-md-1">
                    <a href="#" onclick="get_purchase_order_dashboard(); return false;" class="btn btn-info"><?php echo _l('_filter'); ?></a>
                  </div>
            </div>
          </div>
      </div>
    </div>

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">
          <div class="col-md-12">
            <p class="no-margin budget_actual_procurement">Budget vs Actual Procurement</p>
            <hr class="mtop10">
          </div>

          <div class="col-md-6">
            <div class="row">

              <div class="quick-stats-invoices col-md-6 tw-mb-2 sm:tw-mb-0">    
                <div class="top_stats_wrapper">                                  
                  <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                                                    
                      <span class="tw-truncate dashboard_stat_title">Total Budgeted Procurement</span>                 
                    </div>                      
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>  
                  </div>                    
                  <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                                                    
                      <span class="tw-truncate dashboard_stat_value cost_to_complete"></span>                 
                    </div>                      
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>  
                  </div>              
                </div>          
              </div>

              <div class="quick-stats-invoices col-md-6 tw-mb-2 sm:tw-mb-0">    
                <div class="top_stats_wrapper">                                  
                  <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                                                    
                      <span class="tw-truncate dashboard_stat_title">Total Procured Till Date</span>                 
                    </div>                      
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>  
                  </div>                    
                  <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                                                    
                      <span class="tw-truncate dashboard_stat_value rev_contract_value"></span>                 
                    </div>                      
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>  
                  </div>              
                </div>          
              </div>

            </div>

            <br>

            <div class="row">

              <div class="quick-stats-invoices col-md-6 tw-mb-2 sm:tw-mb-0">    
                <div class="top_stats_wrapper">                                  
                  <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                                                    
                      <span class="tw-truncate dashboard_stat_title">Percentage of Budget Utilized</span>                 
                    </div>                      
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>  
                  </div>                    
                  <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                                                    
                      <span class="tw-truncate dashboard_stat_value percentage_utilized"></span>        
                    </div>                      
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>  
                  </div>              
                </div>          
              </div>

              <div class="quick-stats-invoices col-md-6 tw-mb-2 sm:tw-mb-0">    
                <div class="top_stats_wrapper">                                  
                  <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                                                    
                      <span class="tw-truncate dashboard_stat_title">Net Remaining</span>                 
                    </div>                      
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>  
                  </div>                    
                  <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                                                    
                      <span class="tw-truncate dashboard_stat_value budgeted_procurement_net_value"></span>                 
                    </div>                      
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>  
                  </div>              
                </div>          
              </div>

            </div>
          </div>

          <div class="col-md-6">
            <div class="row">
              <div style="width: 100%; height: 450px; display: flex; justify-content: center;">
                <canvas id="doughnutChartbudgetUtilization"></canvas>
              </div>
            </div>
          </div>

          <div class="col-md-12 mtop20">
            <div class="row">
              <div class="col-md-7">
                <p class="mbot15 dashboard_stat_title">Budgeted vs Actual Procurement by Category</p>
                <div style="width: 100%; height: 450px;">
                  <canvas id="budgetedVsActualCategory"></canvas>
                </div>
              </div>
              <div class="col-md-5">
                <p class="mbot15 dashboard_stat_title">Procurement Data</p>
                <div class="procurement_table_data"></div>
              </div>
            </div>
          </div>

      </div>
    </div>
      
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>

<?php
require 'modules/purchase/assets/js/dashboard/dashboard_js.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>