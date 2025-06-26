
"use strict";

var GoodsreceiptParams = {
    "day_vouchers": "input[name='date_add']",
    "kind": "select[name='kind']",
    "toggle-filter": "input[name='toggle-filter']",
    "vendor": '[name="vendor[]"]',
    "status": "select[name='status']",
    "report_months": '[name="months-report"]',
    "report_from": '[name="report-from"]',
    "report_to": '[name="report-to"]'
};
var report_from = $('input[name="report-from"]');
var report_to = $('input[name="report-to"]');
var date_range = $('#date-range');
var table_manage_goods_receipt = $('.table-table_manage_goods_receipt');

initDataTable(table_manage_goods_receipt, admin_url + 'warehouse/table_manage_goods_receipt', [], [], GoodsreceiptParams, [0, 'desc']);


$('.purchase_sm').DataTable().columns([0]).visible(false, false);

$('#date_add').on('change', function () {
    table_manage_goods_receipt.DataTable().ajax.reload();
});

$('#kind').on('change', function () {
    table_manage_goods_receipt.DataTable().ajax.reload();
});
$('#vendor').on('change', function () {
    table_manage_goods_receipt.DataTable().ajax.reload();
});
$('#status').on('change', function () {
    table_manage_goods_receipt.DataTable().ajax.reload();
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
  table_manage_goods_receipt.DataTable().ajax.reload();
});
report_from.on('change', function() {
  var val = $(this).val();
  var report_to_val = report_to.val();
  if (val != '') {
    report_to.attr('disabled', false);
    if (report_to_val != '') {
      table_manage_goods_receipt.DataTable().ajax.reload();
    }
  } else {
    report_to.attr('disabled', true);
  }
});

report_to.on('change', function() {
  var val = $(this).val();
  if (val != '') {
    table_manage_goods_receipt.DataTable().ajax.reload();
  }
});
$('#sr-charts-section').on('shown.bs.collapse', function () {
    $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
});

$('#sr-charts-section').on('hidden.bs.collapse', function () {
    $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
});

$(document).on('change', 'select[name="vendor[]"], select[name="months-report"], input[name="report-from"], input[name="report-to"]', function() {
    get_stock_received_dashboard();
});

get_stock_received_dashboard();

$('.toggle-filter').on('change', function () {
    var isChecked = $(this).is(':checked') ? 1 : 0;
    $(this).val(isChecked); // Update the value of the checkbox (0 or 1)

    // Trigger DataTable reload to apply the new filter
    table_manage_goods_receipt.DataTable().ajax.reload();
});
init_goods_receipt();
function init_goods_receipt(id) {
    "use strict";
    load_small_table_item_proposal(id, '#purchase_sm_view', 'purchase_id', 'warehouse/view_purchase', '.purchase_sm');
}
var hidden_columns = [3, 4, 5];


function load_small_table_item_proposal(pr_id, selector, input_name, url, table) {
    "use strict";

    var _tmpID = $('input[name="' + input_name + '"]').val();
    // Check if id passed from url, hash is prioritized becuase is last
    if (_tmpID !== '' && !window.location.hash) {
        pr_id = _tmpID;
        // Clear the current id value in case user click on the left sidebar credit_note_ids
        $('input[name="' + input_name + '"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !pr_id) {
            pr_id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof (pr_id) == 'undefined' || pr_id === '') { return; }
    if (!$("body").hasClass('small-table')) { toggle_small_view_proposal(table, selector); }
    $('input[name="' + input_name + '"]').val(pr_id);
    do_hash_helper(pr_id);
    $(selector).load(admin_url + url + '/' + pr_id);
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $(selector).offset().top + 150
        }, 600);
    }

}


function toggle_small_view_proposal(table, main_data) {
    "use strict";

    $("body").toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length === 0) { return; }
    var _visible = false;
    if (tablewrap.hasClass('col-md-5')) {
        tablewrap.removeClass('col-md-5').addClass('col-md-12');
        $('#heading').addClass('col-md-10').removeClass('col-md-8');
        $('#filter_div').addClass('col-md-2').removeClass('col-md-4');

        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-5').removeClass('col-md-12');
        $('#heading').removeClass('col-md-10').addClass('col-md-8');
        $('#filter_div').removeClass('col-md-2').addClass('col-md-4');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }
    var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
    $(window).trigger('resize');

}

function view_goods_receipt_attachments(file_id, rel_id, rel_type) {
    "use strict";
    $.post(admin_url + 'warehouse/view_goods_receipt_attachments', {
        rel_id: rel_id,
        rel_type: rel_type,
        file_id : file_id
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.result) {
            $('.view_goods_receipt_attachments').html(response.result);
        } else {
            $('.view_goods_receipt_attachments').html('');
        }
        $('#viewgoodsReceiptAttachmentModal').modal('show');
    });
}


function preview_goods_receipt_btn(invoker) {
    "use strict";
    var id = $(invoker).attr('id');
    view_goods_receipt_file(id);
}

function view_goods_receipt_file(id) {
    "use strict";
    $('#goods_receipt_file_data').empty();
    $("#goods_receipt_file_data").load(admin_url + 'warehouse/view_goods_receipt_file/' + id, function (response, status, xhr) {
        if (status == "error") {
            alert_float('danger', xhr.statusText);
        }
    });
}
function close_modal_preview() {
    "use strict";
    $('._project_file').modal('hide');
}

function delete_goods_receipt_attachment(id) {
    "use strict";
    if (confirm_delete()) {
        requestGet('warehouse/delete_goods_receipt_attachment/' + id).done(function (success) {
            if (success == 1) {
                $(".view_goods_receipt_attachments").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function (error) {
            alert_float('danger', error.responseText);
        });
    }
}

var lineChartOverTime;

function get_stock_received_dashboard() {
  "use strict";

  var data = {
    vendors: $('select[name="vendor[]"]').val(),
    report_months: $('select[name="months-report"]').val(),
    report_from: $('input[name="report-from"]').val(),
    report_to: $('input[name="report-to"]').val(),
  }

  $.post(admin_url + 'warehouse/get_stock_received_dashboard', data).done(function(response){
    response = JSON.parse(response);

    // Update value summaries
    $('.total_receipts').text(response.total_receipts);
    $('.total_received_po').text(response.total_received_po);
    $('.total_po').text(response.total_po);
    $('.total_quantity_received').text(response.total_quantity_received);
    $('.total_client_supply').text(response.total_client_supply);
    $('.total_bought_out_items').text(response.total_bought_out_items);

    // LINE CHART - Receipts Over Time
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
            label: 'Receipts',
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
                text: 'Receipts'
              }
            }
          }
        }
      });
    }

    // BAR CHART - Top 10 Suppliers by Receipts
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
            label: 'Receipts',
            data: vendorData,
            backgroundColor: 'rgba(153, 102, 255, 0.7)',
            borderColor: 'rgba(153, 102, 255, 1)',
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
                text: 'Receipts'
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

    // DOUGHNUT CHART - Documentation Status
    var budgetUtilizationCtx = document.getElementById('doughnutChartDocumentationStatus').getContext('2d');
    var budgetUtilizationLabels = ['Fully Documented', 'Incomplete'];
    var budgetUtilizationData = [
      response.fully_documented,
      response.incompleted
    ];
    if (window.budgetUtilizationChart) {
      budgetUtilizationChart.data.datasets[0].data = budgetUtilizationData;
      budgetUtilizationChart.update();
    } else {
      window.budgetUtilizationChart = new Chart(budgetUtilizationCtx, {
        type: 'doughnut',
        data: {
          labels: budgetUtilizationLabels,
          datasets: [{
            data: budgetUtilizationData,
            backgroundColor: ['#00008B', '#1E90FF'],
            borderColor: ['#00008B', '#1E90FF'],
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
                  var label = context.label || '';
                  var value = context.formattedValue;
                  return `${label}: ${value}%`;
                }
              }
            }
          }
        }
      });
    }

  });
}