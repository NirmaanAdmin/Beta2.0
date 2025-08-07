
"use strict";

var GoodsreceiptParams = {
    "day_vouchers": "input[name='date_add']",
    "kind": "select[name='kind']",
    "delivery": "select[name='delivery']",
    "vendors": "[name='vendors[]']",
    "group_pur": "[name='group_pur[]']",
    "tracker_status": "[name='tracker_status[]']",
    "production_status": "[name='production_status[]']",
    "wo_po_order" : "[name='wo_po_order[]']",
    "toggle-filter": "input[name='toggle-filter']"
};

var table_manage_goods_receipt = $('.table-table_manage_goods_receipt');

initDataTable(table_manage_goods_receipt, admin_url + 'purchase/table_manage_goods_receipt', [], [], GoodsreceiptParams, [5, 'desc']);

var table_manage_actual_goods_receipt = $('.table-table_manage_actual_goods_receipt');

initDataTable(table_manage_actual_goods_receipt, admin_url + 'purchase/table_manage_actual_goods_receipt', [], [], GoodsreceiptParams, [10, 'desc']);

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        $('.purchase_sm').DataTable().columns([0]).visible(false, false);
        table_manage_goods_receipt.DataTable().ajax.reload();
    } else {
        $('.purchase_sm').DataTable().columns([0]).visible(true, true);
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});

$('#date_add').on('change', function () {
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        table_manage_goods_receipt.DataTable().ajax.reload();
    } else {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});

$('#kind').on('change', function () {
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        table_manage_goods_receipt.DataTable().ajax.reload();
    } else {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('#delivery').on('change', function () {
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        table_manage_goods_receipt.DataTable().ajax.reload();
    } else {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('select[name="vendors[]"]').on('change', function () {
    $('select[name="vendors[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_2') {
        table_manage_goods_receipt.DataTable().ajax.reload();
    } else {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('.toggle-filter').on('change', function () {
    var isChecked = $(this).is(':checked') ? 1 : 0;
    $(this).val(isChecked); // Update the value of the checkbox (0 or 1)

    // Trigger DataTable reload to apply the new filter
    table_manage_goods_receipt.DataTable().ajax.reload();
});
$('select[name="group_pur[]"]').on('change', function () {
    $('select[name="group_pur[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_1') {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('select[name="tracker_status[]"]').on('change', function () {
    $('select[name="tracker_status[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_1') {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});
$('select[name="production_status[]"]').on('change', function () {
    $('select[name="production_status[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_1') {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }
});

$('select[name="wo_po_order[]"]').on('change', function () {
    $('select[name="wo_po_order[]"]').selectpicker('refresh');
    var activeTabId = $('.tracker-pane.active').attr('id');
    if (activeTabId === 'tracker_1') {
        table_manage_actual_goods_receipt.DataTable().ajax.reload();
    }else{
        table_manage_goods_receipt.DataTable().ajax.reload();
    }
});


$(document).on('change', 'select[name="kind"], select[name="delivery"], select[name="vendors[]"], select[name="group_pur[]"], select[name="tracker_status[]"], select[name="production_status[]"], input[name="date_add"], select[name="wo_po_order[]"]' , function() {
    get_purchase_tracker_dashboard();
});

get_purchase_tracker_dashboard();

function get_purchase_tracker_dashboard() {
  "use strict";

  var data = {
    kind: $('select[name="kind"]').val(),
    delivery: $('select[name="delivery"]').val(),
    vendors: $('select[name="vendors[]"]').val(),
    group_pur: $('select[name="group_pur[]"]').val(),
    tracker_status: $('select[name="tracker_status[]"]').val(),
    production_status: $('select[name="production_status[]"]').val(),
    date_add: $('input[name="date_add"]').val(),
    wo_po_order: $('select[name="wo_po_order[]"]').val()
  }

  $.post(admin_url + 'purchase/get_purchase_tracker_charts', data).done(function(response){
    response = JSON.parse(response);

    // Update value summaries
    $('.total_po').text(response.total_po);
    $('.average_lead_time').text(response.average_lead_time);
    $('.percentage_delivered').text(response.percentage_delivered+'%');
    $('.average_advance_payments').text(response.average_advance_payments+'%');
    $('.shop_drawings_approval').text(response.shop_drawings_approval+'%');

    // PO Status Breakdown
    var statusBarCtx = document.getElementById('barChartPOStatus').getContext('2d');
    var statusLabels = response.bar_status_name;
    var statusData = response.bar_status_value;

    if (window.barTopPOStatus) {
      barTopPOStatus.data.labels = statusLabels;
      barTopPOStatus.data.datasets[0].data = statusData;
      barTopPOStatus.update();
    } else {
      window.barTopPOStatus = new Chart(statusBarCtx, {
        type: 'bar',
        data: {
          labels: statusLabels,
          datasets: [{
            label: 'Value',
            data: statusData,
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
                text: 'Value'
              }
            },
            y: {
              ticks: {
                autoSkip: false
              },
              title: {
                display: true,
                text: 'Status'
              }
            }
          }
        }
      });
    }

    // PIE CHART - Procurement by Category
    var categoryPieCtx = document.getElementById('pieChartForCategory').getContext('2d');
    var categoryData = response.pie_category_value;
    var categoryLabels = response.pie_category_name;

    if (window.categoryChart) {
      categoryChart.data.labels = categoryLabels;
      categoryChart.data.datasets[0].data = categoryData;
      categoryChart.update();
    } else {
      window.categoryChart = new Chart(categoryPieCtx, {
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

    // PIE CHART
    var deliveryPerformancePieCtx = document.getElementById('pieChartDeliveryPerformance').getContext('2d');
    var deliveryPerformancePieLabels = response.delivery_performance_labels;
    var deliveryPerformancePieData = response.delivery_performance_values;

    if (window.deliveryPerformancePieChart) {
      deliveryPerformancePieChart.data.labels = deliveryPerformancePieLabels;
      deliveryPerformancePieChart.data.datasets[0].data = deliveryPerformancePieData;
      deliveryPerformancePieChart.update();
    } else {
      window.deliveryPerformancePieChart = new Chart(deliveryPerformancePieCtx, {
        type: 'pie',
        data: {
          labels: deliveryPerformancePieLabels,
          datasets: [{
            data: deliveryPerformancePieData,
            backgroundColor: ['#00008B', '#1E90FF'],
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

init_goods_receipt();
function init_goods_receipt(id) {
    "use strict";
    load_small_table_item_proposal(id, '#purchase_sm_view', 'purchase_id', 'purchase/view_purchase', '.purchase_sm');
}
var hidden_columns = [3, 4, 5];

init_po_tracker();
function init_po_tracker(id) {
    "use strict";
    load_small_table_item_proposal(id, '#purchase_sm_view', 'purchase_id', 'purchase/view_po_tracker', '.purchase_sm');
}

init_wo_tracker();
function init_wo_tracker(id) {
    "use strict";
    load_small_table_item_proposal(id, '#purchase_sm_view', 'purchase_id', 'purchase/view_wo_tracker', '.purchase_sm');
}
 
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

function view_purchase_tracker_attachments(rel_id,view_type) {
    "use strict";
    $.post(admin_url + 'purchase/view_purchase_tracker_attachments', {
        rel_id: rel_id,
        view_type: view_type
    }).done(function (response) { 
        response = JSON.parse(response);
        if (response.result) {
            $('.view_purchase_attachment_modal').html(response.result);
        } else {
            $('.view_purchase_attachment_modal').html('');
        }
        $('#viewpurchaseorderAttachmentModal').modal('show');
    });
}

function preview_purchase_tracker_btn(invoker) {
    "use strict";
    var id = $(invoker).attr('id');
    var view_type = $(invoker).attr('view_type');;
    view_purchase_tracker_file(id,view_type);
}

function view_purchase_tracker_file(id,view_type) {
    "use strict";
    $('#purchase_tracker_file_data').empty();
    $("#purchase_tracker_file_data").load(admin_url + 'purchase/view_purchase_tracker_file/' + id + '/' + view_type, function (response, status, xhr) {
        if (status == "error") {
            alert_float('danger', xhr.statusText);
        }
    });
}

function close_modal_preview() {
    "use strict";
    $('._project_file').modal('hide');
}


function delete_purchase_tracker_attachment(id) {
    "use strict";
    if (confirm_delete()) {
        requestGet('purchase/delete_purchase_tracker_attachment/' + id).done(function (success) {
            if (success == 1) {
                $(".view_purchase_attachment_modal").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function (error) {
            alert_float('danger', error.responseText);
        });
    }
}