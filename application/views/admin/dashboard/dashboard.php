<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
.charts_stat_title {
  font-size: 17px;
  font-weight: bold;
}
.charts_stat_value {
  font-size: 18px;
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
                                <p class="mbot15 charts_stat_title">Cost vs Progress S-Curve</p>
                                <div style="width: 100%; height: 400px;">
                                  <canvas id="costvsProgressLineChartOverTime"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                              <div class="row">
                                <div class="col-md-6 tw-mb-2 sm:tw-mb-0">
                                  <div class="top_stats_wrapper">
                                    <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_title">Total Budgeted Procurement</span>
                                      </div>
                                    </div>
                                    <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_value cost_to_complete"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6 tw-mb-2 sm:tw-mb-0">
                                  <div class="top_stats_wrapper">
                                    <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_title">Total Procured Till Date</span>
                                      </div>
                                    </div>
                                    <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_value rev_contract_value"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row mtop20">
                                <div class="col-md-6 tw-mb-2 sm:tw-mb-0">
                                  <div class="top_stats_wrapper">
                                    <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_title">Percentage of Budget Utilized</span>
                                      </div>
                                    </div>
                                    <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_value percentage_utilized"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6 tw-mb-2 sm:tw-mb-0">
                                  <div class="top_stats_wrapper">
                                    <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_title">Net Remaining</span>
                                      </div>
                                    </div>
                                    <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_value budgeted_procurement_net_value"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row mtop20">
                                <div class="col-md-6 tw-mb-2 sm:tw-mb-0">
                                  <div class="top_stats_wrapper">
                                    <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_title">Vendors onboarded this week</span>
                                      </div>
                                    </div>
                                    <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                      <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <span class="tw-truncate charts_stat_value onboarded_this_week"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="row mtop20">
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

            <div class="col-md-12 mtop20">
                <div class="panel_s">
                    <div class="panel-body padding-15">
                        <div class="row">
                            <div class="col-md-4">
                              <p class="mbot15 charts_stat_title">Pie Chart for PR Approval Status</p>
                              <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                                 <canvas id="pieChartForPRApprovalStatus"></canvas>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <p class="mbot15 charts_stat_title">Pie Chart for PO Approval Status</p>
                              <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                                 <canvas id="pieChartForPOApprovalStatus"></canvas>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <p class="mbot15 charts_stat_title">Pie Chart for WO Approval Status</p>
                              <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                                 <canvas id="pieChartForWOApprovalStatus"></canvas>
                              </div>
                            </div>
                        </div>
                        <div class="row mtop20">
                            <div class="col-md-4">
                              <p class="mbot15 charts_stat_title">Pie Chart for CO Approval Status</p>
                              <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                                 <canvas id="pieChartForCOApprovalStatus"></canvas>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <p class="mbot15 charts_stat_title">Total Vendor Bills per Billing Status</p>
                              <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                                 <canvas id="pieChartForBillingStatus"></canvas>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <p class="mbot15 charts_stat_title">Pie Chart for Payment Certificate Approval Status</p>
                              <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                                 <canvas id="pieChartForPCApprovalStatus"></canvas>
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