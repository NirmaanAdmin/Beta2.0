<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .main_head_title {
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
<?php $module_name = 'purchase_dashboard'; ?>
<div id="wrapper">
  <div class="content">

    <div class="panel_s">
      <div class="panel-body">
        <div class="col-md-12">
          <div class="row all_filters">
            <div class="col-md-2">
              <?php
              $vendor_type_filter = get_module_filter($module_name, 'vendor');
              $vendor_type_filter_val = !empty($vendor_type_filter) ? $vendor_type_filter->filter_value : '';
              echo render_select('vendors', $vendors, array('userid', 'company'), 'vendor', $vendor_type_filter_val);
              ?>
            </div>
            <div class="col-md-2">
              <?php
              $project_type_filter = get_module_filter($module_name, 'project');
              $project_type_filter_val = !empty($project_type_filter) ? $project_type_filter->filter_value : '';
              echo render_select('projects', $projects, array('id', 'name'), 'projects', $project_type_filter_val);
              ?>
            </div>
            <div class="col-md-2">
              <?php
              $group_pur_type_filter = get_module_filter($module_name, 'group_pur');
              $group_pur_type_filter_val = !empty($group_pur_type_filter) ? $group_pur_type_filter->filter_value : '';
              echo render_select('group_pur', $commodity_groups_pur, array('id', 'name'), 'group_pur', $group_pur_type_filter_val);
              ?>
            </div>
            <div class="col-md-2 form-group">
              <?php
              $kind_filter = get_module_filter($module_name, 'kind');
              $kind_filter_val = !empty($kind_filter) ? $kind_filter->filter_value : '';
              ?>
              <label for="kind"><?php echo _l('cat'); ?></label>
              <select name="kind" id="kind" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                <option value=""></option>
                <option value="Client Supply" <?php echo ($kind_filter_val == "Client Supply") ? 'selected' : ''; ?>><?php echo _l('client_supply'); ?></option>
                <option value="Bought out items" <?php echo ($kind_filter_val == "Bought out items") ? 'selected' : ''; ?>><?php echo _l('bought_out_items'); ?></option>
              </select>
            </div>
            <div class="col-md-2">
              <?php
              $from_date_type_filter = get_module_filter($module_name, 'from_date');
              $from_date_type_filter_val = !empty($from_date_type_filter) ?  $from_date_type_filter->filter_value : '';
              echo render_date_input('from_date', 'from_date', $from_date_type_filter_val);
              ?>
            </div>
            <div class="col-md-2">
              <?php
              $to_date_type_filter = get_module_filter($module_name, 'to_date');
              $to_date_type_filter_val = !empty($to_date_type_filter) ?  $to_date_type_filter->filter_value : '';
              echo render_date_input('to_date', 'to_date', $to_date_type_filter_val);
              ?>
            </div>
            <div class="col-md-1">
              <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_filters">
                <?php echo _l('reset_filter'); ?>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">
        <div class="col-md-12">
          <p class="no-margin main_head_title">Budget vs Actual Procurement</p>
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

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">
        <div class="col-md-12">
          <p class="no-margin main_head_title">Delivery Schedules</p>
          <hr class="mtop10">
        </div>

        <div class="col-md-5">
          <div class="row">

            <div class="quick-stats-invoices col-md-6 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">On-Time Deliveries</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value on_time_deliveries_percentage"></span>
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
                    <span class="tw-truncate dashboard_stat_title">Average Delay</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value average_delay"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="col-md-7">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Delivery Delays in Days</p>
            <div style="width: 100%; height: 400px;">
              <canvas id="barChartDeliveryDelay"></canvas>
            </div>
          </div>
        </div>

        <div class="col-md-12 mtop20">
          <div class="row">
            <div class="col-md-5">
              <p class="mbot15 dashboard_stat_title">Delivery Performance</p>
              <div style="width: 100%; height: 450px;">
                <canvas id="pieChartDeliveryPerformance"></canvas>
              </div>
            </div>
            <div class="col-md-7">
              <p class="mbot15 dashboard_stat_title">Delivery Data</p>
              <div class="delivery_table_data"></div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">
        <div class="col-md-12">
          <p class="no-margin main_head_title">Post Order Milestone</p>
          <hr class="mtop10">
        </div>

        <div class="col-md-5">
          <div class="row">

            <div class="quick-stats-invoices col-md-7 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total Procurements Items</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_procurement_items"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="quick-stats-invoices col-md-7 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Late Deliveries</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value late_deliveries"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>

          </div>

          <br>

          <div class="row">

            <div class="quick-stats-invoices col-md-7 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Shop Drawing Approved</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value shop_drawing_approved"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="quick-stats-invoices col-md-7 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Shop Drawing Pending Approval</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value shop_drawing_pending_approval"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="col-md-7">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Procurement Status</p>
            <div style="width: 100%; height: 450px; display: flex; justify-content: center;">
              <canvas id="barChartProcurementStatus"></canvas>
            </div>
          </div>
        </div>


        <div class="col-md-12 mtop20">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Procurement Data</p>

            <!-- scroll-wrapper gives vertical scroll -->
            <div class="scroll-wrapper" style="max-height: 750px; overflow-y: auto;">
              <table class="table table-item-tracker-report">
                <thead>
                  <tr>
                    <th><?php echo _l('Uniclass Code'); ?></th>
                    <th><?php echo _l('description'); ?></th>
                    <th><?php echo _l('po_quantity'); ?></th>
                    <th><?php echo _l('received_quantity'); ?></th>
                    <th><?php echo _l('remaining'); ?></th>
                    <th><?php echo _l('est_delivery_date'); ?></th>
                    <th><?php echo _l('delivery_date'); ?></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
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