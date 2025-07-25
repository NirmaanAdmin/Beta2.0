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
      <div class="panel-body dashboard-budget-summary">
        <div class="col-md-12">
          <p class="no-margin main_head_title">Critical Tracker</p>
          <hr class="mtop10">
        </div>



        <div class="col-md-3">
          <p class="mbot15 dashboard_stat_title">Status Overview</p>
          <div class="row">
            <div style="width: 100%; height: 450px; display: flex;">
              <canvas id="doughnutChartbudgetUtilization"></canvas>
            </div>
          </div>
        </div>

        <div class="col-md-5">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Department-Wise Issue Count</p>
            <div style="width: 100%; height: 400px; display: flex; justify-content: center;">
              <canvas id="barChartDeptwiseissuecount"></canvas>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Overdue Tracker</p>
            <div style="width: 100%; height: 400px; display: flex; justify-content: center;">
              <canvas id="barChartOverdueTracker"></canvas>
            </div>
          </div>
        </div>

        <div class="col-md-6 mtop20" style="clear: both;">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Action By - Responsibility Tracker</p>
            <div class="scroll-wrapper" style="max-height: 750px; overflow-y: auto;">
              <table class="table table-action-by-responsibility-tracker">
                <thead>
                  <tr>
                    <th><?php echo _l('Assigned To'); ?></th>
                    <th><?php echo _l('Open'); ?></th>
                    <th><?php echo _l('Closed'); ?></th>
                    <th><?php echo _l('Closed %'); ?></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

          </div>
        </div>

        <div class="col-md-5 mtop20" style="    margin-left: 10px;
    width: 49%;">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Upcoming Deadlines</p>
            <div class="scroll-wrapper" style="max-height: 750px; overflow-y: auto;">
              <table class="table table-upcoming-deadlines">
                <thead>
                  <tr>
                    <th><?php echo _l('Department'); ?></th>
                    <th><?php echo _l('Area/Head'); ?></th>
                    <th><?php echo _l('Description'); ?></th>
                    <th><?php echo _l('Action By'); ?></th>
                    <th><?php echo _l('Target Date'); ?></th>
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
require 'modules/meeting_management/assets/js/dashboard/dashboard_js.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>