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
          <p class="no-margin main_head_title">Drawing</p>
          <hr class="mtop10">
        </div>

        <div class="row">
          <div class="col-md-12">
            <canvas id="stackedChart" height="130"></canvas>
          </div>
        </div>

        <div class="col-md-12 mtop20">

          <p class="mbot15 dashboard_stat_title">Number of Drawings by Dicipline and Status</p>


          <div class="scroll-wrapper" style="max-height: 461px; overflow-y: auto;overflow-x: clip;">
            <table class="table table-dicipline-status">
              <thead>
                <tr>
                  <th><?php echo _l('Discipline'); ?></th>
                  <th><?php echo _l('Documents Under Review'); ?></th>
                  <th><?php echo _l('Briefs'); ?></th>
                  <th><?php echo _l('Concept'); ?></th>
                  <th><?php echo _l('Schematic'); ?></th>
                  <th><?php echo _l('Design Development'); ?></th>
                  <th><?php echo _l('Tender Documents'); ?></th>
                  <th><?php echo _l('Construction Documents'); ?></th>
                  <th><?php echo _l('Shop Drawings'); ?></th>
                  <th><?php echo _l('As-Built'); ?></th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>


          <!-- <div class="col-md-4" style="margin-top: 2%;">
            <div class="row">
              <p class="mbot15 dashboard_stat_title">Documentation Status</p>
              <div style="width: 95%; height: 450px; display: flex; ">
                <canvas id="doughnutChartDocumentationStatus"></canvas>
              </div>
            </div>
          </div> -->




        </div>
      </div>
    </div>



  </div>
  <?php init_tail(); ?>
  </body>

  </html>

  <?php
  require 'modules/drawing_management/assets/js/dashboard/dashboard_js.php';
  echo '<script src="' . module_dir_url(DRAWING_MANAGEMENT_MODULE_NAME, 'assets/js/dashboard/chart.js') . '"></script>';
  ?>