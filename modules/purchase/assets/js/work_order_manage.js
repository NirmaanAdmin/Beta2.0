var hidden_columns = [2,3,4,5], table_rec_campaign;
Dropzone.autoDiscover = false;
var expenseDropzone;
(function($) {
"use strict"; 
    table_rec_campaign = $('.table-table_wo_order');

    var Params = {
        "from_date": 'input[name="from_date"]',
        "to_date": 'input[name="to_date"]',
        "vendor": "[name='vendor_ft[]']",
        "status": "[name='status[]']",
        "item_filter": "[name='item_filter[]']",
        "group_pur": "[name='group_pur[]']",
        "project": "[name='project[]']",
        "department": "[name='department[]']",
        "delivery_status": "[name='delivery_status[]']",
        "purchase_request": "[name='pur_request[]']"
    };

    initDataTable('.table-table_wo_order', admin_url+'purchase/table_wo_order', [], [], Params,[3, 'desc']);
	init_wo_order();
    $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_rec_campaign.DataTable().ajax.reload();
        });
    });

    $('input[name="from_date"]').on('change', function() {
        table_rec_campaign.DataTable().ajax.reload();
    });
    $('input[name="to_date"]').on('change', function() {
        table_rec_campaign.DataTable().ajax.reload();
    });

    
    if ($('#wo_order-expense-form').length > 0) {
          expenseDropzone = new Dropzone("#wo_order-expense-form", appCreateDropzoneOptions({
              autoProcessQueue: false,
              clickable: '#dropzoneDragArea',
              previewsContainer: '.dropzone-previews',
              addRemoveLinks: true,
              maxFiles: 1,
              success: function(file, response) {
                  if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                      window.location.reload();
                  }
              }
        }));
    }

    $('.table-table_wo_order').on('draw.dt', function () {
        var reportsTable = $(this).DataTable();
        var sums = reportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(0).html("Total (Per Page)");
        $(this).find('tfoot td.total_wo_value').html(sums.total_wo_value);
        $(this).find('tfoot td.total_tax_value').html(sums.total_tax_value);
        $(this).find('tfoot td.total_wo_value_included_tax').html(sums.total_wo_value_included_tax);
    });

    appValidateForm($('#wo_order-expense-form'), {
          expense_name: 'required',
          category: 'required',
          date: 'required',
          amount: 'required'
    }, projectExpenseSubmitHandler);

    $(document).on('click', '.reset_all_ot_filters', function () {
        var filterArea = $('.all_ot_filters');
        filterArea.find('input').val("");
        filterArea.find('select').not('select[name="project[]"]').selectpicker("val", "");
        table_rec_campaign.DataTable().ajax.reload();
        get_work_order_dashboard();
    });

    $(document).on('change', 'select[name="vendor_ft[]"], select[name="project[]"], select[name="group_pur[]"]', function() {
      get_work_order_dashboard();
    });

    get_work_order_dashboard();

})(jQuery);
function init_wo_order(id) {
    "use strict";
    load_small_wo_order_table_item(id, '#pur_order', 'work_orderid', 'purchase/get_wo_order_data_ajax', '.table-table_wo_order');
}
function load_small_wo_order_table_item(id, selector, input_name, url, table) {
    "use strict";
    var _tmpID = $('input[name="' + input_name + '"]').val();
    // Check if id passed from url, hash is prioritized becuase is last
    if (_tmpID !== '' && !window.location.hash) {
        id = _tmpID;
        // Clear the current id value in case user click on the left sidebar credit_note_ids
        $('input[name="' + input_name + '"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof(id) == 'undefined' || id === '') { return; }
    destroy_dynamic_scripts_in_element($(selector))
    if (!$("body").hasClass('small-table')) { toggle_small_pur_order_view(table, selector); }
    $('input[name="' + input_name + '"]').val(id);
    do_hash_helper(id);
    $(selector).load(admin_url + url + '/' + id);
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $(selector).offset().top + 150
        }, 600);
    }
}

function toggle_small_pur_order_view(table, main_data) {
        "use strict";
        $("body").toggleClass('small-table');
        var tablewrap = $('#small-table');
        if (tablewrap.length === 0) { return; }
        var _visible = false;
        if (tablewrap.hasClass('col-md-5')) {
            tablewrap.removeClass('col-md-5').addClass('col-md-12');
            _visible = true;
            $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
        } else {
            tablewrap.addClass('col-md-5').removeClass('col-md-12');
            $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
        }
        var _table = $(table).DataTable();
        // Show hide hidden columns
        _table.columns(hidden_columns).visible(_visible, false);
        _table.columns.adjust();
        $(main_data).toggleClass('hide');
        $(window).trigger('resize');
}

function toggle_small_wo_order_view(table, main_data) {
    "use strict";
    $("body").toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length === 0) { return; }
    var _visible = false;
    if (tablewrap.hasClass('col-md-5')) {
        tablewrap.removeClass('col-md-5').addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-5').removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }
    var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
    $(window).trigger('resize');
}
function convert_expense_wo(wo_order,total){
    "use strict";

    $.post(admin_url + 'purchase/get_project_info_wo/'+wo_order).done(function(response){
      response = JSON.parse(response);
      $('select[name="project_id"]').val(response.project_id).change();
      $('select[name="clientid"]').val(response.customer).change();
      $('select[name="currency"]').val(response.currency).change();
      $('input[name="vendor"]').val(response.vendor);
      if(response.budget_head) {
        $('#category').val(response.budget_head).change();
      } else {
        $('#category').val('').change();
      }
    });

    $('#wo_order_expense').modal('show');
    $('input[id="amount"]').val(total);
    $('#wo_order_additional').html('');
    $('#wo_order_additional').append(hidden_input('wo_order',wo_order));
}

function projectExpenseSubmitHandler(form) {
    "use strict";
      $.post(form.action, $(form).serialize()).done(function(response) {
          response = JSON.parse(response);
          if (response.expenseid) {
              if (typeof(expenseDropzone) !== 'undefined') {
                  if (expenseDropzone.getQueuedFiles().length > 0) {
                      expenseDropzone.options.url = admin_url + 'expenses/add_expense_attachment/' + response.expenseid;
                      expenseDropzone.processQueue();
                  } else {
                      window.location.assign(response.url);
                  }
              } else {
                  window.location.assign(response.url);
              }
          } else {
              window.location.assign(response.url);
          }
      });
      return false;
}

function change_delivery_status(status, id){
  "use strict";
  if(id > 0){
    $.post(admin_url + 'purchase/change_delivery_status/'+status+'/'+id).done(function(response){
      response = JSON.parse(response);
      if(response.success == true){
        if($('#status_span_'+id).hasClass('label-danger')){
          $('#status_span_'+id).removeClass('label-danger');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-success')){
          $('#status_span_'+id).removeClass('label-success');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-info')){
          $('#status_span_'+id).removeClass('label-info');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-warning')){
          $('#status_span_'+id).removeClass('label-warning');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }
        alert_float('success', response.mess);
      }else{
        alert_float('warning', response.mess);
      }
    });
  }
}

var pieChartWOStatus;

function get_work_order_dashboard() {
  "use strict";

  var data = {
    vendors: $('select[name="vendor_ft[]"]').val(),
    projects: $('select[name="project[]"]').val(),
    group_pur: $('select[name="group_pur[]"]').val(),
  }

  $.post(admin_url + 'purchase/get_wo_charts', data).done(function(response){
    response = JSON.parse(response);

    // Update value summaries
    $('.total_wo_value').text(response.total_wo_value);
    $('.approved_wo_value').text(response.approved_wo_value);
    $('.draft_wo_value').text(response.draft_wo_value);

    // PIE CHART - Approval Status
    var pieCtx = document.getElementById('pieChartForWOApprovalStatus').getContext('2d');
    var pieData = [response.approved_wo_count, response.draft_wo_count, response.rejected_wo_count];

    if (pieChartWOStatus) {
      pieChartWOStatus.data.datasets[0].data = pieData;
      pieChartWOStatus.update();
    } else {
      pieChartWOStatus = new Chart(pieCtx, {
        type: 'pie',
        data: {
          labels: ['Approved', 'Draft', 'Rejected'],
          datasets: [{
            data: pieData,
            backgroundColor: [
              'rgba(75, 192, 192, 0.7)',
              'rgba(255, 206, 86, 0.7)',
              'rgba(255, 99, 132, 0.7)'
            ],
            borderColor: [
              'rgba(75, 192, 192, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    }

    // PIE CHART - Wo Distribution by Budget
    var woPieCtx = document.getElementById('pieChartForWoByBudget').getContext('2d');
    var woData = response.pie_total_value;
    var budgetLabels = response.pie_budget_name;

    if (window.woByBudgetChart) {
      woByBudgetChart.data.labels = budgetLabels;
      woByBudgetChart.data.datasets[0].data = woData;
      woByBudgetChart.update();
    } else {
      window.woByBudgetChart = new Chart(woPieCtx, {
        type: 'pie',
        data: {
          labels: budgetLabels,
          datasets: [{
            data: woData,
            backgroundColor: budgetLabels.map((_, i) => `hsl(${i * 35 % 360}, 70%, 60%)`),
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

    // PIE CHART - Pie Chart for WO per Department
    var departmentPieCtx = document.getElementById('pieChartForDepartment').getContext('2d');
    var departmentData = response.department_value;
    var departmentLabels = response.department_name;

    if (window.departmentChart) {
      departmentChart.data.labels = departmentLabels;
      departmentChart.data.datasets[0].data = departmentData;
      departmentChart.update();
    } else {
      window.departmentChart = new Chart(departmentPieCtx, {
        type: 'pie',
        data: {
          labels: departmentLabels,
          datasets: [{
            data: departmentData,
            backgroundColor: departmentLabels.map((_, i) => `hsl(${i * 35 % 360}, 70%, 60%)`),
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
