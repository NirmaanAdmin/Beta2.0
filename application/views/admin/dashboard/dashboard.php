<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
.charts_stat_title {
  font-size: 17px;
  font-weight: bold;
}
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>

            <div class="clearfix"></div>
            
            <div class="col-md-12 mtop20" data-container="top-12">
                <?php $this->load->view('admin/dashboard/widgets/top_stats'); ?>
            </div>

            <div class="col-md-12 mtop20">
                <div class="panel_s">
                    <div class="panel-body padding-15">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mbot15 charts_stat_title">Total Order Value Over Time</p>
                                <div style="width: 100%; height: 400px;">
                                  <canvas id="orderTrackerLineChartOverTime"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <p class="mbot15 charts_stat_title">Budgeted vs Actual Procurement by Budget Head</p>
                                <div style="width: 100%; height: 400px;">
                                   <canvas id="budgetedVsActualCategory"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mtop20" data-container="left-8">
                <?php $this->load->view('admin/dashboard/widgets/user_data'); ?>
                <?php $this->load->view('admin/dashboard/widgets/finance_overview'); ?>
                <?php $this->load->view('admin/dashboard/widgets/upcoming_events'); ?>
                <?php $this->load->view('admin/dashboard/widgets/calendar'); ?>
            </div>
            <div class="col-md-4 mtop20" data-container="right-4">
                <?php $this->load->view('admin/dashboard/widgets/todos'); ?>
                <?php $this->load->view('admin/dashboard/widgets/projects_chart'); ?>
                <?php $this->load->view('admin/dashboard/widgets/projects_activity'); ?>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script>
app.calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
</script>
<?php init_tail(); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>