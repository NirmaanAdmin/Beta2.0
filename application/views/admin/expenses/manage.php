<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .n_width {
    width: 25% !important;
  }
</style>
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
            <a href="<?php echo admin_url('purchase/activity_log?module=ex'); ?>" class="btn btn-primary mleft5" target="_blank"><?php echo _l('activity_log'); ?></a>
            <button class="btn btn-primary mleft5" type="button" data-toggle="collapse" data-target="#ex-charts-section" aria-expanded="true" aria-controls="ex-charts-section">
              <?php echo _l('Expenses Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
            </button>


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
                    <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                      <div class="top_stats_wrapper">
                        <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Total Expenses Raised</span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                        <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_value total_expenses" style="font-size: 19px; font-weight: bold;"></span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                      </div>
                    </div>
                    <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                      <div class="top_stats_wrapper">
                        <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Average Expenses</span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                        <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_value total_average_expenses" style="font-size: 19px; font-weight: bold;"></span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                      </div>
                    </div>
                    <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                      <div class="top_stats_wrapper">
                        <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Expenses without Receipts</span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                        <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_value total_expenses_without_receipts" style="font-size: 19px; font-weight: bold;"></span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                      </div>
                    </div>
                    <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                      <div class="top_stats_wrapper">
                        <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_title" style="font-size: 19px; font-weight: bold;">Total Untagged Expenses</span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                        </div>
                        <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                          <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                            <span class="tw-truncate dashboard_stat_value total_untagged_expenses" style="font-size: 19px; font-weight: bold;"></span>
                          </div>
                          <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
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
                      <p class="mbot15 dashboard_stat_title" style="font-size: 18px; font-weight: bold;">Top 10 Vendors by Amount</p>
                      <div style="width: 100%; height: 400px;">
                        <canvas id="barChartTopVendors"></canvas>
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
                <div class="btn-group show_hide_columns hide" id="show_hide_columns" style="position: absolute !important; z-index: 999; left: 387px !important; top: 117px;">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                    <i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
                  </button>
                  <div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
                    <div>
                      <input type="checkbox" id="select-all-columns"> <strong><?php echo _l('select_all'); ?></strong>
                    </div>
                    <hr>
                    <?php
                    $columns = [
                      'Checkbox',
                      _l('the_number_sign'),
                      _l('expense_dt_table_heading_category'),
                      _l('expense_dt_table_heading_amount'),
                      _l('expense_name'),
                      _l('receipt'),
                      _l('expense_dt_table_heading_date'),
                      _l('project'),
                      'Converted?',
                      _l('invoice'),
                      _l('expense_dt_table_heading_reference_no'),
                      _l('expense_dt_table_heading_payment_mode'),
                      'Vendor',
                      'Options',
                    ];
                    ?>
                    <div>
                      <?php foreach ($columns as $key => $label): ?>
                        <input type="checkbox" class="toggle-column" value="<?php echo $key; ?>" checked>
                        <?php echo $label; ?><br>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
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

    $('#ex-charts-section').on('shown.bs.collapse', function() {
      $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
    $('#ex-charts-section').on('hidden.bs.collapse', function() {
      $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });

    get_expenses_dashboard();

    var lineChartOverTime;

    function get_expenses_dashboard() {
      "use strict";
      var data = {}

      $.post(admin_url + 'expenses/get_expenses_dashboard', data).done(function(response) {
        response = JSON.parse(response);

        // Update value summaries
        $('.total_expenses').text(response.total_expenses);
        $('.total_average_expenses').text(response.total_average_expenses);
        $('.total_expenses_without_receipts').text(response.total_expenses_without_receipts);
        $('.total_untagged_expenses').text(response.total_untagged_expenses);

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

        // BAR CHART - Top 10 Vendors by Amount
        var vendorBarCtx = document.getElementById('barChartTopVendors').getContext('2d');
        var vendorLabels = response.bar_top_vendor_name;
        var vendorData = response.bar_top_vendor_value;

        if (window.barTopVendorsChart) {
          barTopVendorsChart.data.labels = vendorLabels;
          barTopVendorsChart.data.datasets[0].data = vendorData;
          barTopVendorsChart.update();
        } else {
          window.barTopVendorsChart = new Chart(vendorBarCtx, {
            type: 'bar',
            data: {
              labels: vendorLabels,
              datasets: [{
                label: 'Amount',
                data: vendorData,
                backgroundColor: '#1E90FF',
                borderColor: '#1E90FF',
                borderWidth: 1
              }]
            },
            options: {
              indexAxis: 'y',
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: false
                }
              },
              scales: {
                x: {
                  beginAtZero: true,
                  title: {
                    display: true,
                    text: 'Amount'
                  }
                },
                y: {
                  ticks: {
                    autoSkip: false
                  },
                  title: {
                    display: true,
                    text: 'Vendors'
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

<script>
  var table_rec_task;
  var report_from_choose;
  var report_from = $('input[name="report-from"]');
  var report_to = $('input[name="report-to"]');
  var date_range = $('#date-range');
  (function($) {
    table_rec_task = $('.table-expenses');
    report_from_choose = $('#report-time');

    var Params = {
      "expense_category": "[name='expense_category[]']",
      "payment_mode": "[name='payment_mode[]']",
      "vendor": "[name='vendor[]']",
      "report_months": '[name="months-report"]',
      "report_from": '[name="report-from"]',
      "report_to": '[name="report-to"]',
      "year_requisition": "[name='year_requisition']",
      "order_tagged": "[name='order_tagged']",
      "order_tagged_detail": "[name='order_tagged_detail[]']",
      "converted": "[name='converted']",
    };

    initDataTable('.table-expenses', admin_url + 'expenses/table_expenses', [], [0], Params, [6, 'desc']);

    // initDataTable('.table-expenses', admin_url + 'expenses/table', [0], [0], {},
    //     <?php echo hooks()->apply_filters('expenses_table_default_order', json_encode([6, 'desc'])); ?>)
    //   .column(1).visible(false, false).columns.adjust();

    init_expense();


    $.each(Params, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_rec_task.DataTable().ajax.reload();
      });
    });

    $('select[name="months-report"]').on('change', function() {
      if ($(this).val() != 'custom') {
        table_rec_task.DataTable().ajax.reload();
      }
    });

    $('select[name="year_requisition"]').on('change', function() {
      table_rec_task.DataTable().ajax.reload();
    });

    report_from.on('change', function() {
      var val = $(this).val();
      var report_to_val = report_to.val();
      if (val != '') {
        report_to.attr('disabled', false);
        if (report_to_val != '') {
          table_rec_task.DataTable().ajax.reload();
        }
      } else {
        report_to.attr('disabled', true);
      }
    });

    report_to.on('change', function() {
      var val = $(this).val();
      if (val != '') {
        table_rec_task.DataTable().ajax.reload();
      }
    });

    $('select[name="months-report"]').on('change', function() {
      var val = $(this).val();
      report_to.attr('disabled', true);
      report_to.val('');
      report_from.val('');
      if (val == 'custom') {
        date_range.addClass('fadeIn').removeClass('hide');
        return;
      } else {
        if (!date_range.hasClass('hide')) {
          date_range.removeClass('fadeIn').addClass('hide');
        }
      }
      table_rec_task.DataTable().ajax.reload();
    });

    $(document).on('click', '.reset_all_ot_filters', function() {
      var filterArea = $('.all_ot_filters');
      filterArea.find('input').val("");
      filterArea.find('select').selectpicker("val", "");
      table_rec_task.DataTable().ajax.reload();
    });
    $('.table-expenses').on('draw.dt', function() {
      var reportsTable = $(this).DataTable();
      var sums = reportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(1).html("Total (Per Page)");
      $(this).find('tfoot td.total_expense_amount').html(sums.total_expense_amount);
    });

    $(document).on('change', 'select[name="expense_category[]"]', function() {
      $('select[name="expense_category[]"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="payment_mode[]"]', function() {
      $('select[name="payment_mode[]"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="vendor[]"]', function() {
      $('select[name="vendor[]"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="order_tagged"]', function () {
      $('select[name="order_tagged"]').selectpicker('refresh');
    });
    
    $(document).on('change', 'select[name="order_tagged_detail[]"]', function () {
      $('select[name="order_tagged_detail[]"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="converted"]', function () {
      $('select[name="converted"]').selectpicker('refresh');
    });

    $("body").on('change', '#mass_select_all', function() {
      var rows, checked;
      rows = $('.table-expenses').find('tbody tr');
      checked = $(this).prop('checked');
      $.each(rows, function() {
        $($(this).find('td').eq(0)).find('input').prop('checked', checked);
      });
    });

    var table_expenses = $('.table-expenses').DataTable();
    // Handle "Select All" checkbox
    $('#select-all-columns').on('change', function() {
      var isChecked = $(this).is(':checked');
      $('.toggle-column').prop('checked', isChecked).trigger('change');
    });

    // Handle individual column visibility toggling
    $('.toggle-column').on('change', function() {
      var column = table_expenses.column($(this).val());
      column.visible($(this).is(':checked'));

      // Sync "Select All" checkbox state
      var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
      $('#select-all-columns').prop('checked', allChecked);
    });

    // Sync checkboxes with column visibility on page load
    table_expenses.columns().every(function(index) {
      var column = this;
      $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
    });

    // Prevent dropdown from closing when clicking inside
    $('.dropdown-menu').on('click', function(e) {
      e.stopPropagation();
    });
  })(jQuery);
</script>
<script>
  $(document).ready(function() {
    // Event listener for the choose_from_order dropdown change
    $(document).on('change', '[name*="[choose_from_order]"]', function() {
      var selectedValue = $(this).val();
      var pkey = $(this).attr('name').match(/\[(\d+)\]\[choose_from_order\]/)[1];
      var orderListSelect = $('[name="newitems[' + pkey + '][order_list]"]');

      // Clear previous options
      orderListSelect.empty();

      if (selectedValue === 'none') {
        orderListSelect.append($('<option>', {
          value: '',
          text: '<?php echo _l("none"); ?>'
        }));
        orderListSelect.selectpicker('refresh');
        return;
      }

      // Show loading indicator
      orderListSelect.append($('<option>', {
        value: '',
        text: 'Loading...',
        disabled: true
      }));
      orderListSelect.selectpicker('refresh');

      // AJAX call to fetch options based on selection
      $.ajax({
        url: '<?php echo admin_url("expenses/get_order_options"); ?>',
        type: 'POST',
        data: {
          type: selectedValue,
          pkey: pkey
        },
        dataType: 'json',
        success: function(response) {
          orderListSelect.empty();

          if (response.success && response.options.length > 0) {
            $.each(response.options, function(index, option) {
              orderListSelect.append($('<option>', {
                value: option.id,
                text: option.name
              }));
            });
          } else {
            orderListSelect.append($('<option>', {
              value: '',
              text: 'No options available'
            }));
          }
          orderListSelect.selectpicker('refresh');
        },
        error: function() {
          orderListSelect.empty();
          orderListSelect.append($('<option>', {
            value: '',
            text: 'Error loading options'
          }));
          orderListSelect.selectpicker('refresh');
        }
      });
    });
  });

  $('body').on('click', '.update_vbt_convert', function(e) {
    e.preventDefault();
    var convert_expense_name = $('#convert_expense_name').val();
    var convert_category = $('#convert_category').val();
    var convert_date = $('#convert_date').val();

    if (convert_expense_name) {
      $('.all_expense_name textarea').val(convert_expense_name);
    }
    if (convert_category) {
      $('.all_budget_head select').val(convert_category).trigger('change');
    }
    if (convert_date) {
      $('.all_invoice_date input').val(convert_date).trigger('change');
    }
    
  });
</script>