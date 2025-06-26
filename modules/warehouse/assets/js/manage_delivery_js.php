
<script>

"use strict";
    <?php if(isset($invoice_id)){ ?>
    var InvoiceServerParams = {
        "invoice_id": "input[name='invoice_id']",
         "day_vouchers": "input[name='date_add']",
        "approval": "select[name='approval']",
        "delivery_status": "select[name='delivery_status']",
     };
 <?php }else{ ?>
    var InvoiceServerParams = {
        "invoice_id": '',
         "day_vouchers": "input[name='date_add']",
        "approval": "select[name='approval']",
        "delivery_status": "select[name='delivery_status']",
     };

<?php } ?>


var table_manage_delivery = $('.table-table_manage_delivery');

 initDataTable(table_manage_delivery, admin_url+'warehouse/table_manage_delivery',[],[], InvoiceServerParams, [0 ,'desc']);

$('.delivery_sm').DataTable().columns([0]).visible(false, false);


 $('#date_add').on('change', function() {
    table_manage_delivery.DataTable().ajax.reload();
});

$('#approval').on('change', function () {
    table_manage_delivery.DataTable().ajax.reload();
});

$('#delivery_status').on('change', function () {
    table_manage_delivery.DataTable().ajax.reload();
});

$('#si-charts-section').on('shown.bs.collapse', function () {
    $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
});

$('#si-charts-section').on('hidden.bs.collapse', function () {
    $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
});


 init_goods_delivery();
  function init_goods_delivery(id) {
    "use strict";
    load_small_table_item_proposal(id, '#delivery_sm_view', 'delivery_id', 'warehouse/view_delivery', '.delivery_sm');
  }
  var hidden_columns = [3,4,5];


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
    if (typeof(pr_id) == 'undefined' || pr_id === '') { return; }
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
var lineChartOverTime;

get_stock_issued_dashboard();

function get_stock_issued_dashboard() {
  "use strict";

  var data = {}

  $.post(admin_url + 'warehouse/get_stock_issued_dashboard', data).done(function(response){
    response = JSON.parse(response);

    // Update value summaries
    $('.total_issued_quantity').text(response.total_issued_quantity);
    $('.total_issued_entries').text(response.total_issued_entries);
    $('.total_returnable_items').text(response.total_returnable_items);

    // BAR CHART - Issued Quantity by Material (Horizontal)
    var materialBarCtx = document.getElementById('barChartTopMaterials').getContext('2d');
    var materialLabels = response.bar_top_material_name;
    var materialData = response.bar_top_material_value;

    if (window.barTopMaterialsChart) {
      barTopMaterialsChart.data.labels = materialLabels;
      barTopMaterialsChart.data.datasets[0].data = materialData;
      barTopMaterialsChart.update();
    } else {
      window.barTopMaterialsChart = new Chart(materialBarCtx, {
        type: 'bar',
        data: {
          labels: materialLabels,
          datasets: [{
            label: 'Issued Quantity',
            data: materialData,
            backgroundColor: '#1E90FF',
            borderColor: '#1E90FF',
            borderWidth: 1
          }]
        },
        options: {
          indexAxis: 'y', // <--- This makes it horizontal
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
                text: 'Issued Quantity'
              }
            },
            y: {
              ticks: {
                autoSkip: false
              },
              title: {
                display: true,
                text: 'Materials'
              }
            }
          }
        }
      });
    }

    // LINE CHART - Consumption Over Time
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
            label: 'Quantities',
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
                text: 'Quantities'
              }
            }
          }
        }
      });
    }

    // DOUGHNUT CHART - Returnable vs Non-Returnable
    var returnableUtilizationCtx = document.getElementById('returnablevsnonreturnable').getContext('2d');
    var returnableUtilizationLabels = ['Returnable', 'Non-Returnable'];
    var returnableUtilizationData = [
      response.returnable_ratio,
      response.non_returnable_ratio
    ];
    if (window.returnableUtilizationChart) {
      returnableUtilizationChart.data.datasets[0].data = returnableUtilizationData;
      returnableUtilizationChart.update();
    } else {
      window.returnableUtilizationChart = new Chart(returnableUtilizationCtx, {
        type: 'doughnut',
        data: {
          labels: returnableUtilizationLabels,
          datasets: [{
            data: returnableUtilizationData,
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

</script>