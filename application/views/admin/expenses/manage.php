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
                        <div id="vueApp" class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5">
                            <app-filters 
                                id="<?php echo $table->id(); ?>" 
                                view="<?php echo $table->viewName(); ?>"
                                :saved-filters="<?php echo $table->filtersJs(); ?>"
                                :available-rules="<?php echo $table->rulesJs(); ?>">
                            </app-filters>
                        </div>

                        <a href="#" onclick="slideToggle('#expense-chart'); return false;" class="pull-right btn btn-default mleft5 btn-with-tooltip" data-toggle="tooltip" title="Expense Chart"><i class="fa fa-pie-chart"></i></a>

                        <a href="#" onclick="slideToggle('#stats-top'); return false;"
                            class="pull-right btn btn-default mleft5 btn-with-tooltip" data-toggle="tooltip"
                            title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
                        <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs"
                            onclick="toggle_small_view('.table-expenses','#expense'); return false;"
                            data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i
                                class="fa fa-angle-double-left"></i></a>
                        <div id="stats-top" class="hide">
                            <hr />
                            <div id="expenses_total"></div>
                        </div>
                        <div id="expense-chart" class="hide mtop15">
                            <div class="col-md-3 pull-right" style="padding-right: 0px; padding-bottom: 10px;">
                                <select class="form-control" id="expenseType" name="expenseType" onchange="updateExpenseChart();">
                                   <option value="0">Category Wise</option>
                                   <option value="1">Payment Wise</option>
                                   <option value="2">Project Wise</option>
                                </select>
                            </div>
                            <div id="expense_chart" style="width:100%; height:400px;"></div>
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
<?php 
echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/highcharts.js') . '"></script>';
echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/exporting.js') .'"></script>';
?>
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
});
</script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
      renderChart(<?php echo json_encode($chart_data); ?>, 'Category Wise Expenses');
   });

   function renderChart(chartData, titleText) {
      Highcharts.chart('expense_chart', {
         chart: {
            type: 'pie',
            options3d: {
            enabled: true,
            alpha: 45
        }
         },
         title: {
            text: titleText
         },
         series: [{
            name: 'Expense',
            colorByPoint: true,
            data: chartData
         }]
      });
   }

   function updateExpenseChart() {
      var selectedType = document.getElementById("expenseType").value;
      var titleText = '';

      // Determine the chart title and request data based on the selected type
      if (selectedType == '0') {
         titleText = 'Category Wise Expenses';
      } else if (selectedType == '1') {
         titleText = 'Payment Wise Expenses';
      } else if (selectedType == '2') {
         titleText = 'Project Wise Expenses';
      }

      // Use AJAX to fetch the correct chart data
      var xhr = new XMLHttpRequest();
      xhr.open('GET', '' + admin_url + 'expenses/get_expenses_chart_data_type_wise?type=' + selectedType, true);
      xhr.onload = function() {
         if (xhr.status === 200) {
            var responseData = JSON.parse(xhr.responseText);
            renderChart(responseData, titleText); // Update chart with new data and title
         }
      };
      xhr.send();
   }
</script>
</body>

</html>