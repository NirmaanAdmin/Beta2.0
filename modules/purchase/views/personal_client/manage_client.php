<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'per_client'; ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 5000;
      left: 200px
   }

   .n_width {
      width: 20% !important;
   }

   .dashboard_stat_title {
      font-size: 19px;
      font-weight: bold;
   }

   .dashboard_stat_value {
      font-size: 19px;
   }

   .bulk-title {
      font-weight: bold;
   }

   b {
      font-weight: 700;
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
                           <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('Client Data'); ?></h4>
                           <hr />
                        </div>
                        <div class="col-md-12">
                           <button class="btn btn-info display-block" type="button" data-toggle="collapse" data-target="#ac-charts-section" aria-expanded="true" aria-controls="ac-charts-section">
                              <?php echo _l('Client Data Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                           </button>
                        </div>
                        <div class="col-md-12 mtop10" style="justify-content: right; display: flex;">
                           <a href="javascript:void(0);" id="generate-pdf" class="btn btn-default" style="margin-right: 10px;" data-toggle="tooltip" title="<?php echo _l('Generate PDF'); ?>">
                              <i class="fa fa-file-pdf"></i>
                           </a>
                           <!-- NEW ZIP BUTTON -->
                           <a href="javascript:void(0);" id="export-pdf-zip" class="btn btn-success" data-toggle="tooltip" title="Export All Client PDFs (ZIP)">
                              <i class="fa fa-file-zipper-o"></i> Export ZIP
                           </a>

                        </div>
                     </div>
                     <div id="ac-charts-section" class="collapse in">
                        <div class="row">
                           <div class="col-md-12 mtop20">
                              <div class="row">
                                 <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                    <div class="top_stats_wrapper">
                                       <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                             <span class="tw-truncate dashboard_stat_title">Total Clients</span>
                                          </div>
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                       </div>
                                       <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                             <span class="tw-truncate dashboard_stat_value total_clients"></span>
                                          </div>
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                    <div class="top_stats_wrapper">
                                       <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                             <span class="tw-truncate dashboard_stat_title">Total Investment</span>
                                          </div>
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                       </div>
                                       <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                             <span class="tw-truncate dashboard_stat_value total_investment"></span>
                                          </div>
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                    <div class="top_stats_wrapper">
                                       <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                             <span class="tw-truncate dashboard_stat_title">Total Earnings</span>
                                          </div>
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                       </div>
                                       <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                             <span class="tw-truncate dashboard_stat_value total_earnings"></span>
                                          </div>
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                    <div class="top_stats_wrapper">
                                       <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                             <span class="tw-truncate dashboard_stat_title">Average Profit</span>
                                          </div>
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                       </div>
                                       <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                             <span class="tw-truncate dashboard_stat_value last_month_average_profit"></span>
                                          </div>
                                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row mtop20">
                           <div class="col-md-6">
                              <p class="mbot15 dashboard_stat_title">% Profit</p>
                              <div style="width: 100%; height: 400px;">
                                 <canvas id="barChartTopStaffs"></canvas>
                              </div>
                              <input type="hidden" id="bar_chart_img" name="bar_chart_img">
                           </div>
                           <div class="col-md-6">
                              <p class="mbot15 dashboard_stat_title">Monthly Earnings Trend</p>
                              <div style="width: 100%; height: 400px;">
                                 <canvas id="lineChartOverTime"></canvas>
                              </div>
                              <input type="hidden" id="line_chart_img" name="line_chart_img">
                           </div>
                        </div>
                     </div>
                     <div class="row all_filters mtop20">

                        <div class="col-md-2 form-group">
                           <?php
                           $months_filter = get_module_filter($module_name, 'months');
                           $month_filter_val = !empty($months_filter) ? $months_filter->filter_value : '';
                           ?>
                           <select class="selectpicker" name="months" data-width="100%" data-none-selected-text="<?php echo _l('Year'); ?>">
                              <option value=""></option>
                              <option value="2025" <?php echo ($month_filter_val == '2025') ? 'selected' : ''; ?>>2025</option>
                           </select>
                        </div>

                        <div class="col-md-2 form-group">
                           <?php
                           $frequency_filter = get_module_filter($module_name, 'frequency');
                           $frequency_filter_val = !empty($frequency_filter) ? $frequency_filter->filter_value : '';
                           ?>
                           <select class="selectpicker" name="frequency" data-width="100%" data-none-selected-text="<?php echo _l('Frequency'); ?>">
                              <option value="all" <?php echo ($frequency_filter_val == 'all' || $frequency_filter_val == '') ? 'selected' : ''; ?>>All</option>
                              <option value="Monthly" <?php echo ($frequency_filter_val == 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
                              <option value="Quarterly" <?php echo ($frequency_filter_val == 'Quarterly') ? 'selected' : ''; ?>>Quarterly</option>
                           </select>
                        </div>

                        <div class="col-md-2 form-group">
                           <?php
                           $per_client_filter = get_module_filter($module_name, 'per_client');
                           $per_client_filter_val = !empty($per_client_filter) ? explode(",", $per_client_filter->filter_value) : [];
                           echo render_select('per_client[]', $per_clients, array('id', 'name'), '', $per_client_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Client'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>

                        <div class="col-md-1 form-group ">
                           <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_filters">
                              <?php echo _l('reset_filter'); ?>
                           </a>
                        </div>
                     </div>

                     <table class="dt-table-loading table table-table_manage_client">
                        <thead>
                           <tr>
                              <th><?php echo _l('Client Id'); ?></th>
                              <th><?php echo _l('Name'); ?></th>
                              <th><?php echo _l('Phone'); ?></th>
                              <th><?php echo _l('Start Date'); ?></th>
                              <th><?php echo _l('Investment'); ?></th>
                              <th><?php echo _l('Frequency'); ?></th>
                              <th><?php echo _l('August 2025'); ?></th>
                              <th><?php echo _l('September 2025'); ?></th>
                              <th><?php echo _l('October 2025'); ?></th>
                              <th><?php echo _l('November 2025'); ?></th>
                              <th><?php echo _l('December 2025'); ?></th>
                              <th><?php echo _l('Earned To Date'); ?></th>
                              <th><?php echo _l('Percent Profits'); ?></th>
                           </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="investment"></td>
                           <td></td>
                           <td class="aug"></td>
                           <td class="sep"></td>
                           <td class="oct"></td>
                           <td class="nov"></td>
                           <td class="dec"></td>
                           <td></td>
                           <td></td>
                        </tfoot>
                     </table>

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
      var table_manage_client = $('.table-table_manage_client');
      var Params = {
         "months": "[name='months']",
         "frequency": "[name='frequency']",
         "per_client": "[name='per_client[]']",
      };
      initDataTable(table_manage_client, admin_url + 'purchase/table_manage_client', [], [], Params, []);
      $.each(Params, function(i, obj) {
         $('select' + obj).on('change', function() {
            table_manage_client.DataTable().ajax.reload();
         });
      });
      $(document).on('click', '.reset_all_filters', function() {
         var filterArea = $('.all_filters');
         filterArea.find('input').val("");
         filterArea.find('select').selectpicker("val", "");
         table_manage_client.DataTable().ajax.reload();
         get_pre_client_dashboard();
      });

      get_pre_client_dashboard();

      $(document).on('change', 'select[name="months"], select[name="frequency"], select[name="per_client[]"]', function() {
         get_pre_client_dashboard();
      });

      var lineChartOverTime;

      function get_pre_client_dashboard() {
         "use strict";

         var data = {
            months: $('select[name="months"]').val(),
            frequency: $('select[name="frequency"]').val(),
            per_client: $('select[name="per_client[]"]').val(),
         }

         $.post(admin_url + 'purchase/get_per_clients_charts', data).done(function(response) {
            response = JSON.parse(response);


            $('.total_clients').text(response.total_clients);
            $('.total_investment').text('₹' + response.total_investment);
            $('.total_earnings').text('₹' + response.total_earnings);
            $('.last_month_average_profit').text('₹' + response.last_month_average_profit);

            var staffBarCtx = document.getElementById('barChartTopStaffs').getContext('2d');
            var staffLabels = response.bar_top_client_name;
            var staffData = response.bar_top_client_value;

            if (window.barTopStaffsChart) {
               barTopStaffsChart.data.labels = staffLabels;
               barTopStaffsChart.data.datasets[0].data = staffData;
               setTimeout(function() {
                  var base64 = barTopStaffsChart.toBase64Image();
                  $('#bar_chart_img').val(base64);
               }, 300);
               barTopStaffsChart.update();
            } else {
               window.barTopStaffsChart = new Chart(staffBarCtx, {
                  type: 'bar',
                  data: {
                     labels: staffLabels,
                     datasets: [{
                        label: 'Total Count',
                        data: staffData,
                        backgroundColor: '#1E90FF',
                        borderColor: '#1E90FF',
                        borderWidth: 1
                     }]
                  },
                  options: {
                     indexAxis: 'y',
                     responsive: true,
                     maintainAspectRatio: false,
                     animation: {
                        onComplete: function() {
                           var base64 = barTopStaffsChart.toBase64Image();
                           $('#bar_chart_img').val(base64);
                        }
                     },
                     plugins: {
                        legend: {
                           display: false
                        }
                     },
                     scales: {
                        x: {
                           beginAtZero: true,
                           ticks: {
                              color: '#000'
                           },
                           title: {
                              display: true,
                              text: '% Profit'
                           }
                        },
                        y: {
                           ticks: {
                              autoSkip: false,
                              color: '#000'
                           },
                           title: {
                              display: true,
                              text: 'Client'
                           }
                        }
                     }
                  }
               });
            }

            // Activity Type Breakdown
            var lineCtx = document.getElementById('lineChartOverTime').getContext('2d');

            if (lineChartOverTime) {
               lineChartOverTime.data.labels = response.line_order_date;
               lineChartOverTime.data.datasets[0].data = response.line_order_total;
               setTimeout(function() {
                  var base64 = lineChartOverTime.toBase64Image();
                  $('#line_chart_img').val(base64);
               }, 300);
               lineChartOverTime.update();
            } else {
               lineChartOverTime = new Chart(lineCtx, {
                  type: 'line',
                  data: {
                     labels: response.line_order_date,
                     datasets: [{
                        label: 'Total Count',
                        data: response.line_order_total,
                        fill: false,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.3
                     }]
                  },
                  options: {
                     responsive: true,
                     maintainAspectRatio: false,
                     animation: {
                        onComplete: function() {
                           var base64 = lineChartOverTime.toBase64Image();
                           $('#line_chart_img').val(base64);
                        }
                     },
                     plugins: {
                        legend: {
                           display: true,
                           position: 'bottom'
                        },
                        tooltip: {
                           mode: 'index',
                           intersect: false
                        }
                     },
                     scales: {
                        x: {
                           ticks: {
                              color: '#000'
                           },
                           title: {
                              display: true,
                              text: ''
                           }
                        },
                        y: {
                           beginAtZero: true,
                           ticks: {
                              color: '#000'
                           },
                           title: {
                              display: true,
                              text: 'Total Count'
                           }
                        }
                     }
                  }
               });
            }

         });
      }
   });
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>
<script>
   $(document).on('click', '#generate-pdf', function(e) {
      e.preventDefault();
      var months = $('select[name="months"]').val();
      var frequency = $('select[name="frequency"]').val();
      var per_client = $('select[name="per_client[]"]').val();
      var bar_chart_img = $('input[name="bar_chart_img"]').val();
      var line_chart_img = $('input[name="line_chart_img"]').val();
      var form = $('<form>', {
         action: admin_url + 'purchase/per_client_pdf',
         method: 'POST',
         target: '_blank'
      });
      form.append($('<input>', {
         type: 'hidden',
         name: "csrf_token_name",
         value: $('input[name="csrf_token_name"]').val()
      }));
      form.append($('<input>', {
         type: 'hidden',
         name: 'output_type',
         value: 'I'
      }));
      form.append($('<input>', {
         type: 'hidden',
         name: 'months',
         value: months || ''
      }));
      form.append($('<input>', {
         type: 'hidden',
         name: 'frequency',
         value: frequency || ''
      }));
      form.append($('<input>', {
         type: 'hidden',
         name: 'per_client',
         value: per_client ? per_client.join(',') : ''
      }));
      form.append($('<input>', {
         type: 'hidden',
         name: 'bar_chart_img',
         value: bar_chart_img || ''
      }));
      form.append($('<input>', {
         type: 'hidden',
         name: 'line_chart_img',
         value: line_chart_img || ''
      }));
      $('body').append(form);
      form.submit();
      form.remove();
   });
   $(document).on('click', '#export-pdf-zip', function(e) {
      e.preventDefault();

      var months = $('select[name="months"]').val();
      var frequency = $('select[name="frequency"]').val();
      var per_client = $('select[name="per_client[]"]').val();

      var form = $('<form>', {
         action: admin_url + 'purchase/per_client_pdf_zip',
         method: 'POST',
         target: '_blank'
      });

      form.append($('<input>', {
         type: 'hidden',
         name: "csrf_token_name",
         value: $('input[name="csrf_token_name"]').val()
      }));

      form.append($('<input>', {
         type: 'hidden',
         name: 'months',
         value: months || ''
      }));
      form.append($('<input>', {
         type: 'hidden',
         name: 'frequency',
         value: frequency || ''
      }));
      form.append($('<input>', {
         type: 'hidden',
         name: 'per_client',
         value: per_client ? per_client.join(',') : ''
      }));

      $('body').append(form);
      form.submit();
      form.remove();
   });
</script>
