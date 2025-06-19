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

  .n_width {
    width: 20% !important;
  }
</style>
<?php $module_name = 'purchase_dashboard'; ?>
<div id="wrapper">
  <div class="content">

    <!-- <div class="panel_s">
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
    </div> -->

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">
        <div class="col-md-12">
          <p class="no-margin main_head_title">Inventory Live Stock/Documentation</p>
          <hr class="mtop10">
        </div>

        <div class="col-md-12">
          <div class="row">

            <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total Items In Inventory</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_items_in_inventory"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>

            <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Fully PO Material Receipt</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value fully_po_material_receipt"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>

            <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Missing Security Sign</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value missing_security_signature"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
            <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Missing Transport Doc</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value missing_transport_document"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
            <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Missing Production Cert</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value missing_production_certificate"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>

          </div>


        </div>

        <div class="col-md-4" style="margin-top: 2%;">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Documentation Status</p>
            <div style="width: 95%; height: 450px; display: flex; ">
              <canvas id="doughnutChartDocumentationStatus"></canvas>
            </div>
          </div>
        </div>


        <div class="col-md-8 mtop20">

          <p class="mbot15 dashboard_stat_title">Receipt Status</p>


          <div class="scroll-wrapper" style="max-height: 461px; overflow-y: auto;overflow-x: clip;">
            <table class="table table-receipt-status">
              <thead>
                <tr>
                  <th><?php echo _l('PO ID'); ?></th>
                  <th><?php echo _l('Scurity Signed'); ?></th>
                  <th><?php echo _l('Transport Document'); ?></th>
                  <th><?php echo _l('Production Certificate'); ?></th>
                  <th><?php echo _l('Voucher Date'); ?></th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>


        </div>
      </div>
    </div>

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">

        <div class="col-md-12">
          <p class="no-margin main_head_title">Client Supply Material Issue</p>
          <hr class="mtop10">
        </div>

        <div class="col-md-5">
          <div class="row">

            <div class="quick-stats-invoices col-md-7 tw-mb-2 sm:tw-mb-0 ">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total Materials Issued</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_materials_issued"></span>
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
                    <span class="tw-truncate dashboard_stat_title">Total Material Return</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_material_return"></span>
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
                    <span class="tw-truncate dashboard_stat_title">Returnable Past Dates</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value returnable_past_dates"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-7" >
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Consumption Over Time</p>
            <div style="width: 100%; height: 450px; display: flex; justify-content: center;">
              <canvas id="lineChartConsumptionOverTime"></canvas>
            </div>
          </div>
        </div>

        <div class="col-md-12" style="margin-top: 2%;">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Material Return Details</p>

            <div class="scroll-wrapper" style="max-height: 750px; overflow-y: auto;overflow-x: clip;">
              <table class="table table-return-details">
                <thead>
                  <tr>
                    <th><?php echo _l('Issue Voucher Code'); ?></th>
                    <th><?php echo _l('Product Code'); ?></th>
                    <th><?php echo _l('Product Description'); ?></th>
                    <th><?php echo _l('Vendor'); ?></th>
                    <th><?php echo _l('Return Date'); ?></th>
                    <th><?php echo _l('Status'); ?></th>
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
  <?php init_tail(); ?>
  </body>

  </html>

  <?php
  require 'modules/warehouse/assets/js/dashboard/dashboard_js.php';
  echo '<script src="' . module_dir_url(WAREHOUSE_MODULE_NAME, 'assets/js/dashboard/chart.js') . '"></script>';
  ?>