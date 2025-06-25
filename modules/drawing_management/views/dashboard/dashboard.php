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
<?php $module_name = 'drawing_dashboard'; ?>
<div id="wrapper">
  <div class="content">

    <!-- <div class="panel_s">
      <div class="panel-body">
        <div class="col-md-12">
          <div class="row all_filters">

            <div class="col-md-2">
              <?php
              $project_type_filter = get_module_filter($module_name, 'project');
              $project_type_filter_val = !empty($project_type_filter) ? $project_type_filter->filter_value : '';
              echo render_select('projects', $projects, array('id', 'name'), 'projects', $project_type_filter_val);
              ?>
            </div>

            <div class="col-md-1" style="margin-top: 1.5%;">
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
          <div class="col-md-4">
            <div class="row">
              <div style="width: 100%; height: 450px; display: flex;">
                <canvas id="doughnutChartapproval"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-8">
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