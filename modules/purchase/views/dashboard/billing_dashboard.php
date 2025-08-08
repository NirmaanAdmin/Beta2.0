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
<?php $module_name = 'billing_dashboard'; ?>
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
              $order_tagged_detail_filter = get_module_filter($module_name, 'order_tagged_detail');
              $order_tagged_detail_filter_val = !empty($order_tagged_detail_filter) ? explode(",", $order_tagged_detail_filter->filter_value) : '';
              echo render_select('order_tagged_detail[]', $order_tagged_detail, array('id', 'name'), 'Order Detail', $order_tagged_detail_filter_val, array('data-width' => '100%', 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
              ?>
            </div>
            <div class="col-md-2 form-group" id="report-time">
              <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
              <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                <option value="this_month"><?php echo _l('this_month'); ?></option>
                <option value="1"><?php echo _l('last_month'); ?></option>
                <option value="this_year"><?php echo _l('this_year'); ?></option>
                <option value="last_year"><?php echo _l('last_year'); ?></option>
                <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                <option value="custom"><?php echo _l('period_datepicker'); ?></option>
              </select>
            </div>
            <div id="date-range" class="hide mbot15">
              <div class="row">
                <div class="col-md-2">
                  <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                  <div class="input-group date">
                    <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar calendar-icon"></i>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                  <div class="input-group date">
                    <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar calendar-icon"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php $current_year = date('Y');
            $y0 = (int)$current_year;
            $y1 = (int)$current_year - 1;
            $y2 = (int)$current_year - 2;
            $y3 = (int)$current_year - 3;
            ?>
            <div class="form-group hide" id="year_requisition">
              <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
              <select name="year_requisition" id="year_requisition" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('filter_by') . ' ' . _l('year'); ?>">
                <option value="<?php echo pur_html_entity_decode($y0); ?>" <?php echo 'selected' ?>><?php echo _l('year') . ' ' . pur_html_entity_decode($y0); ?></option>
                <option value="<?php echo pur_html_entity_decode($y1); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y1); ?></option>
                <option value="<?php echo pur_html_entity_decode($y2); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y2); ?></option>
                <option value="<?php echo pur_html_entity_decode($y3); ?>"><?php echo _l('year') . ' ' . pur_html_entity_decode($y3); ?></option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="row">
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
          <p class="no-margin main_head_title">Bill Pending for Certification</p>
          <hr class="mtop10">
        </div>
        <div class="col-md-12">
          <div class="row">
            <div class="quick-stats-invoices col-md-4 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Bill Pending for Certification By BIL</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value bill_pending_by_bil"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
            <div class="quick-stats-invoices col-md-4 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Bill Pending for Certification By RIL</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value bill_pending_by_ril"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">
        <div class="col-md-12">
          <p class="no-margin main_head_title">Bill Certified By BIL</p>
          <hr class="mtop10">
        </div>
        <div class="col-md-5">
          <div class="row">
            <div class="quick-stats-invoices col-md-8 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total Count of Bill Certified By BIL</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_bil_count"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="quick-stats-invoices col-md-8 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total Amount of Bill Certified By BIL</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_bil_amount"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Total Certified Amount Over Period of Time</p>
            <div style="width: 100%; height: 400px;">
               <canvas id="lineChartBilOverTime"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">
        <div class="col-md-12">
          <p class="no-margin main_head_title">Bill Certified By RIL</p>
          <hr class="mtop10">
        </div>
        <div class="col-md-5">
          <div class="row">
            <div class="quick-stats-invoices col-md-8 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total Count of Bill Certified By RIL</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_ril_count"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="quick-stats-invoices col-md-8 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total Amount of Bill Certified By RIL</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_ril_amount"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Total Certified Amount Over Period of Time</p>
            <div style="width: 100%; height: 400px;">
               <canvas id="lineChartRilOverTime"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">
        <div class="col-md-12">
          <p class="no-margin main_head_title">RIL Paid Data</p>
          <hr class="mtop10">
        </div>
        <div class="col-md-5">
          <div class="row">
            <div class="quick-stats-invoices col-md-8 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total RIL Paid Count</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_paid_count"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="quick-stats-invoices col-md-8 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total RIL Paid Amount</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_paid_amount"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Total RIL Amount Paid Over Period of Time</p>
            <div style="width: 100%; height: 400px;">
               <canvas id="lineChartPaidOverTime"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel_s">
      <div class="panel-body dashboard-budget-summary">
        <div class="col-md-12">
          <p class="no-margin main_head_title">RIL Unpaid Data</p>
          <hr class="mtop10">
        </div>
        <div class="col-md-5">
          <div class="row">
            <div class="quick-stats-invoices col-md-8 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total RIL Unpaid Count</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_unpaid_count"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="quick-stats-invoices col-md-8 tw-mb-2 sm:tw-mb-0">
              <div class="top_stats_wrapper">
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_title">Total RIL Unpaid Amount</span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
                <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                    <span class="tw-truncate dashboard_stat_value total_unpaid_amount"></span>
                  </div>
                  <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="row">
            <p class="mbot15 dashboard_stat_title">Total RIL Amount Unpaid Over Period of Time</p>
            <div style="width: 100%; height: 400px;">
               <canvas id="lineChartUnpaidOverTime"></canvas>
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
require 'modules/purchase/assets/js/dashboard/billing_dashboard_js.php';
?>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>