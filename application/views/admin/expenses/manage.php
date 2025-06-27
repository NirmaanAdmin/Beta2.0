<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <div class="_buttons">
                        <?php if (staff_can('create',  'expenses')) { ?>
                        <a href="<?php echo admin_url('expenses/expense'); ?>" class="btn btn-primary">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('new_expense'); ?>
                        </a>
                        <a href="<?php echo admin_url('expenses/import'); ?>" class="btn btn-primary mleft5">
                            <i class="fa-solid fa-upload tw-mr-1"></i>
                            <?php echo _l('import_expenses'); ?>
                        </a>
                        <?php } ?>
                        <button class="btn btn-primary mleft5" type="button" data-toggle="collapse" data-target="#ex-charts-section" aria-expanded="true"aria-controls="ex-charts-section">
                        <?php echo _l('Expenses Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                        </button>
                        <div id="vueApp" class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5">
                            <app-filters 
                                id="<?php echo $table->id(); ?>" 
                                view="<?php echo $table->viewName(); ?>"
                                :saved-filters="<?php echo $table->filtersJs(); ?>"
                                :available-rules="<?php echo $table->rulesJs(); ?>">
                            </app-filters>
                        </div>

                        <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs"
                            onclick="toggle_small_view('.table-expenses','#expense'); return false;"
                            data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i
                                class="fa fa-angle-double-left"></i></a>
                    </div>
                </div>

                <div id="ex-charts-section" class="collapse in">
                  <div class="row">
                     <div class="col-md-12 mtop20">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="row">
                                   <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                            <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                                  </path>                          
                                               </svg>                          
                                               <span class="tw-truncate">Total Expenses</span>                 
                                            </div>                      
                                            <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_expenses"></span>  
                                         </div>                    
                                         <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                            <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                            </div>                  
                                         </div>              
                                      </div>          
                                   </div>

                                   <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                            <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                                  </path>                          
                                               </svg>                          
                                               <span class="tw-truncate">Average Expenses</span>                 
                                            </div>                      
                                            <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_average_expenses"></span>  
                                         </div>                    
                                         <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                            <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                            </div>                  
                                         </div>              
                                      </div>          
                                   </div>

                                   <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                            <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                                  </path>                          
                                               </svg>                          
                                               <span class="tw-truncate">Expenses without Receipts</span>                 
                                            </div>                      
                                            <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_expenses_without_receipts"></span>  
                                         </div>                    
                                         <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                            <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                            </div>                  
                                         </div>              
                                      </div>          
                                   </div>
                                </div>
                                <div class="row mtop20">
                                    <div class="col-md-4">
                                      <p class="mbot15 dashboard_stat_title" style="font-size: 18px; font-weight: bold;">Expenses Over Time</p>
                                      <div style="width: 100%; height: 400px;">
                                        <canvas id="lineChartOverTime"></canvas>
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mbot15 dashboard_stat_title" style="font-size: 18px; font-weight: bold;">Pie Chart for Expense per Project</p>
                                        <div style="width: 100%; height: 420px; display: flex; justify-content: left;">
                                           <canvas id="pieChartForProject"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mbot15 dashboard_stat_title" style="font-size: 18px; font-weight: bold;">Pie Chart for Expense per Category</p>
                                        <div style="width: 100%; height: 470px; display: flex; justify-content: left;">
                                           <canvas id="pieChartForCategory"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
                  </div>
                </div>

                <div class="row">
                    <div class="col-md-12" id="small-table">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="clearfix"></div>
                                <!-- if expenseid found in url -->
                                <?php echo form_hidden('expenseid', $expenseid); ?>
                                <div class="panel-table-full">
                                    <?php $this->load->view('admin/expenses/table_html', ['withBulkActions' => true]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 small-table-right-col">
                        <div id="expense" class="hide">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="expense_convert_helper_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('additional_action_required'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="radio radio-primary">
                    <input type="radio" checked id="expense_convert_invoice_type_1" value="save_as_draft_false"
                        name="expense_convert_invoice_type">
                    <label for="expense_convert_invoice_type_1"><?php echo _l('convert'); ?></label>
                </div>
                <div class="radio radio-primary">
                    <input type="radio" id="expense_convert_invoice_type_2" value="save_as_draft_true"
                        name="expense_convert_invoice_type">
                    <label for="expense_convert_invoice_type_2"><?php echo _l('convert_and_save_as_draft'); ?></label>
                </div>
                <div id="inc_field_wrapper">
                    <hr />
                    <p><?php echo _l('expense_include_additional_data_on_convert'); ?></p>
                    <p><b><?php echo _l('expense_add_edit_description'); ?> +</b></p>
                    <div class="checkbox checkbox-primary inc_note">
                        <input type="checkbox" id="inc_note">
                        <label for="inc_note"><?php echo _l('expense'); ?>
                            <?php echo _l('expense_add_edit_note'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary inc_name">
                        <input type="checkbox" id="inc_name">
                        <label for="inc_name"><?php echo _l('expense'); ?> <?php echo _l('expense_name'); ?></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"
                    id="expense_confirm_convert"><?php echo _l('confirm'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
var hidden_columns = [4, 5, 6, 7, 8, 9];
</script>
<?php init_tail(); ?>
<script>
Dropzone.autoDiscover = false;
$(function() {
    initDataTable('.table-expenses', admin_url + 'expenses/table', [0], [0], {},
            <?php echo hooks()->apply_filters('expenses_table_default_order', json_encode([6, 'desc'])); ?>)
        .column(1).visible(false, false).columns.adjust();

    init_expense();

    $('.table-expenses').on('draw.dt', function () {
        var reportsTable = $(this).DataTable();
        var sums = reportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(1).html("Total (Per Page)");
        $(this).find('tfoot td.total_expense_amount').html(sums.total_expense_amount);
    });

    $('#expense_convert_helper_modal').on('show.bs.modal', function() {
        var emptyNote = $('#tab_expense').attr('data-empty-note');
        var emptyName = $('#tab_expense').attr('data-empty-name');
        if (emptyNote == '1' && emptyName == '1') {
            $('#inc_field_wrapper').addClass('hide');
        } else {
            $('#inc_field_wrapper').removeClass('hide');
            emptyNote === '1' && $('.inc_note').addClass('hide') || $('.inc_note').removeClass('hide')
            emptyName === '1' && $('.inc_name').addClass('hide') || $('.inc_name').removeClass('hide')
        }
    });

    $('body').on('click', '#expense_confirm_convert', function() {
        var parameters = new Array();
        if ($('input[name="expense_convert_invoice_type"]:checked').val() == 'save_as_draft_true') {
            parameters['save_as_draft'] = 'true';
        }
        parameters['include_name'] = $('#inc_name').prop('checked');
        parameters['include_note'] = $('#inc_note').prop('checked');
        window.location.href = buildUrl(admin_url + 'expenses/convert_to_invoice/' + $('body').find(
            '.expense_convert_btn').attr('data-id'), parameters);
    });

    $('#ex-charts-section').on('shown.bs.collapse', function () {
        $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
    $('#ex-charts-section').on('hidden.bs.collapse', function () {
        $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });

    get_expenses_dashboard();

    var lineChartOverTime;

    function get_expenses_dashboard() {
        "use strict";
        var data = {}
        
        $.post(admin_url + 'expenses/get_expenses_dashboard', data).done(function(response){
            response = JSON.parse(response);

            // Update value summaries
            $('.total_expenses').text(response.total_expenses);
            $('.total_average_expenses').text(response.total_average_expenses);
            $('.total_expenses_without_receipts').text(response.total_expenses_without_receipts);

            // LINE CHART - Certified Value Over Time
            var lineCtx = document.getElementById('lineChartOverTime').getContext('2d');

            if (lineChartOverTime) {
              lineChartOverTime.data.labels = response.line_order_date;
              lineChartOverTime.data.datasets[0].data = response.line_order_total;
              lineChartOverTime.update();
            } else {
              lineChartOverTime = new Chart(lineCtx, {
                type: 'line',
                data: {
                  labels: response.line_order_date,
                  datasets: [{
                    label: 'Certified Value',
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
                      title: {
                        display: true,
                        text: 'Month'
                      }
                    },
                    y: {
                      beginAtZero: true,
                      title: {
                        display: true,
                        text: 'Certified Value'
                      }
                    }
                  }
                }
              });
            }

            // PIE CHART - Pie Chart for Invoice per Project
            var projectPieCtx = document.getElementById('pieChartForProject').getContext('2d');
            var projectData = response.pie_project_value;
            var projectLabels = response.pie_project_name;

            if (window.poByProjectChart) {
              poByProjectChart.data.labels = projectLabels;
              poByProjectChart.data.datasets[0].data = projectData;
              poByProjectChart.update();
            } else {
              window.poByProjectChart = new Chart(projectPieCtx, {
                type: 'pie',
                data: {
                  labels: projectLabels,
                  datasets: [{
                    data: projectData,
                    backgroundColor: projectLabels.map((_, i) => `hsl(${i * 35 % 360}, 70%, 60%)`),
                    borderColor: '#fff',
                    borderWidth: 1
                  }]
                },
                options: {
                  responsive: true,
                  plugins: {
                    legend: {
                      position: 'bottom'
                    },
                    tooltip: {
                      callbacks: {
                        label: function(context) {
                          return context.label + ': ' + context.formattedValue;
                        }
                      }
                    }
                  }
                }
              });
            }

            // PIE CHART - Pie Chart for Invoice per Category
            var categoryPieCtx = document.getElementById('pieChartForCategory').getContext('2d');
            var categoryData = response.pie_category_value;
            var categoryLabels = response.pie_category_name;

            if (window.poByCategoryChart) {
              poByCategoryChart.data.labels = categoryLabels;
              poByCategoryChart.data.datasets[0].data = categoryData;
              poByCategoryChart.update();
            } else {
              window.poByCategoryChart = new Chart(categoryPieCtx, {
                type: 'pie',
                data: {
                  labels: categoryLabels,
                  datasets: [{
                    data: categoryData,
                    backgroundColor: categoryLabels.map((_, i) => `hsl(${i * 35 % 360}, 70%, 60%)`),
                    borderColor: '#fff',
                    borderWidth: 1
                  }]
                },
                options: {
                  responsive: true,
                  plugins: {
                    legend: {
                      position: 'bottom'
                    },
                    tooltip: {
                      callbacks: {
                        label: function(context) {
                          return context.label + ': ' + context.formattedValue;
                        }
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