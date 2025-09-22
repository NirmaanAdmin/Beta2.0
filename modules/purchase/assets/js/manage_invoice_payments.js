(function ($) {
  "use strict";
  var table_invoice = $('.table-table_pur_invoice_payments');
  var Params = {
    "from_date": 'input[name="from_date"]',
    "to_date": 'input[name="to_date"]',
    "vendors": "[name='vendor_ft[]']",
    "budget_head": "[name='budget_head[]']",
    "billing_invoices": "[name='billing_invoices[]']",
    "bil_payment_status": "[name='bil_payment_status']",
    "order_tagged_detail": "[name='order_tagged_detail[]']",
  };

  initDataTable(table_invoice, admin_url + 'purchase/table_pur_invoice_payments', [], [], Params, [5, 'desc']);
  $.each(Params, function (i, obj) {
    $('select' + obj).on('change', function () {
      table_invoice.DataTable().ajax.reload();
    });
  });

  $('input[name="from_date"]').on('change', function () {
    table_invoice.DataTable().ajax.reload();
  });
  $('input[name="to_date"]').on('change', function () {
    table_invoice.DataTable().ajax.reload();
  });

  $(document).on('change', 'select[name="vendor_ft[]"]', function () {
    $('select[name="vendor_ft[]"]').selectpicker('refresh');
  });
  $(document).on('change', 'select[name="budget_head[]"]', function () {
    $('select[name="budget_head[]"]').selectpicker('refresh');
  });
  $(document).on('change', 'select[name="billing_invoices[]"]', function () {
    $('select[name="billing_invoices[]"]').selectpicker('refresh');
  });
  $(document).on('change', 'select[name="bil_payment_status"]', function () {
    $('select[name="bil_payment_status"]').selectpicker('refresh');
  });
  $(document).on('change', 'select[name="order_tagged_detail[]"]', function () {
    $('select[name="order_tagged_detail[]"]').selectpicker('refresh');
  });

  $(document).on('click', '.reset_vbt_all_filters', function () {
    var filterArea = $('.vbt_all_filters');
    filterArea.find('input').val("");
    filterArea.find('select').selectpicker("val", "");
    table_invoice.DataTable().ajax.reload();
    get_vpt_dashboard();
  });

  $(document).on('change', 'input[name="from_date"], input[name="to_date"], select[name="vendor_ft[]"], select[name="budget_head"], select[name="billing_invoices"], select[name="bil_payment_status"]', function() {
    get_vpt_dashboard();
  });

  get_vpt_dashboard();

  $('.table-table_pur_invoice_payments').on('draw.dt', function () {
    var reportsTable = $(this).DataTable();
    var sums = reportsTable.ajax.json().sums;
    $(this).find('tfoot').addClass('bold');
    $(this).find('tfoot td').eq(0).html("Total (Per Page)");
    $(this).find('tfoot td.total_vendor_submitted_amount_without_tax').html(sums.total_vendor_submitted_amount_without_tax);
    $(this).find('tfoot td.total_vendor_submitted_tax_amount').html(sums.total_vendor_submitted_tax_amount);
    $(this).find('tfoot td.total_final_certified_amount').html(sums.total_final_certified_amount);
    $(this).find('tfoot td.total_payment_made').html(sums.total_payment_made);
    $(this).find('tfoot td.total_bil_tds').html(sums.total_bil_tds);
    $(this).find('tfoot td.total_bil_total').html(sums.total_bil_total);
    $(this).find('tfoot td.total_ril_previous').html(sums.total_ril_previous);
    $(this).find('tfoot td.total_ril_this_bill').html(sums.total_ril_this_bill);
    $(this).find('tfoot td.total_ril_amount').html(sums.total_ril_amount);
  });

  var table_pur_invoice_payments = $('.table-table_pur_invoice_payments').DataTable();

  $('body').on('click', '.bil-tds-display', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var currentAmount = $(this).text().replace(/[^\d.-]/g, '');
      $(this).replaceWith('<input type="number" class="form-control bil-tds-input" value="' + currentAmount + '" data-id="' + rowId + '" style="width: 138px">');
   });

   $('body').on('change', '.bil-tds-input', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var amount = $(this).val();
      $.post(admin_url + 'purchase/update_bil_tds_amount', {
         id: rowId,
         amount: amount
      }).done(function(response) {
         response = JSON.parse(response);
         if (response.success) {
            alert_float('success', response.message);
            table_pur_invoice_payments.ajax.reload(null, false);
         } else {
            alert_float('danger', response.message);
         }
      });
   });

   $('body').on('click', '.ril-previous-display', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var currentAmount = $(this).text().replace(/[^\d.-]/g, '');
      $(this).replaceWith('<input type="number" class="form-control ril-previous-input" value="' + currentAmount + '" data-id="' + rowId + '" style="width: 138px">');
   });

   $('body').on('change', '.ril-previous-input', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var amount = $(this).val();
      $.post(admin_url + 'purchase/update_ril_previous_amount', {
         id: rowId,
         amount: amount
      }).done(function(response) {
         response = JSON.parse(response);
         if (response.success) {
            alert_float('success', response.message);
            table_pur_invoice_payments.ajax.reload(null, false);
         } else {
            alert_float('danger', response.message);
         }
      });
   });

   $('body').on('click', '.ril-this-bill-display', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var currentAmount = $(this).text().replace(/[^\d.-]/g, '');
      $(this).replaceWith('<input type="number" class="form-control ril-this-bill-input" value="' + currentAmount + '" data-id="' + rowId + '" style="width: 138px">');
   });

   $('body').on('change', '.ril-this-bill-input', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var amount = $(this).val();
      $.post(admin_url + 'purchase/update_ril_this_bill_amount', {
         id: rowId,
         amount: amount
      }).done(function(response) {
         response = JSON.parse(response);
         if (response.success) {
            alert_float('success', response.message);
            table_pur_invoice_payments.ajax.reload(null, false);
         } else {
            alert_float('danger', response.message);
         }
      });
   });

   $('body').on('change', '.ril-date-input', function (e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var rilDate = $(this).val();
    $.post(admin_url + 'purchase/update_ril_date', {
      id: rowId,
      ril_date: rilDate
    }).done(function (response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
        table_pur_invoice_payments.ajax.reload(null, false);
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  $('body').on('click', '.add_new_payment_date', function(e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var newPaymentDateHTML = `
      <div class="input-group date all_payment_date" data-id="${rowId}">
        <input type="date" class="form-control payment-date-input" data-payment-id="0" data-id="${rowId}" style="width: 138px">
        <div class="input-group-addon">
            <i class="fa fa-plus add_new_payment_date" data-id="${rowId}" style="cursor: pointer;"></i>
        </div>
      </div>
    `;
    $(this).closest('.all_payment_date').after(newPaymentDateHTML);
  });

  $('body').on('change', '.payment-date-input', function (e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var paymentId = $(this).data('payment-id');
    var paymentDate = $(this).val();
    $.post(admin_url + 'purchase/update_bil_payment_date', {
      id: paymentId,
      vbt_id: rowId,
      payment_date: paymentDate,
    }).done(function (response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
        table_pur_invoice_payments.ajax.reload(null, false);
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  $('body').on('click', '.add_new_payment_made', function(e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var newPaymentMadeHTML = `
      <div class="input-group all_payment_made" data-id="${rowId}">
        <input type="number" class="form-control payment-made-input" data-payment-id="0" data-id="${rowId}" style="width: 138px">
        <div class="input-group-addon">
            <i class="fa fa-plus add_new_payment_made" data-id="${rowId}" style="cursor: pointer;"></i>
        </div>
      </div>
    `;
    $(this).closest('.all_payment_made').after(newPaymentMadeHTML);
  });

  $('body').on('change', '.payment-made-input', function (e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var paymentId = $(this).data('payment-id');
    var paymentMade = $(this).val();
    $.post(admin_url + 'purchase/update_bil_payment_made', {
      id: paymentId,
      vbt_id: rowId,
      payment_made: paymentMade,
    }).done(function (response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
        table_pur_invoice_payments.ajax.reload(null, false);
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  $('body').on('click', '.add_new_payment_tds', function(e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var newPaymentMadeHTML = `
      <div class="input-group all_payment_tds" data-id="${rowId}">
        <input type="number" class="form-control payment-tds-input" data-payment-id="0" data-id="${rowId}" style="width: 138px">
        <div class="input-group-addon">
            <i class="fa fa-plus add_new_payment_tds" data-id="${rowId}" style="cursor: pointer;"></i>
        </div>
      </div>
    `;
    $(this).closest('.all_payment_tds').after(newPaymentMadeHTML);
  });

  $('body').on('change', '.payment-tds-input', function (e) {
    e.preventDefault();
    var rowId = $(this).data('id');
    var paymentId = $(this).data('payment-id');
    var paymentMade = $(this).val();
    $.post(admin_url + 'purchase/update_bil_payment_tds', {
      id: paymentId,
      vbt_id: rowId,
      payment_tds: paymentMade,
    }).done(function (response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
        table_pur_invoice_payments.ajax.reload(null, false);
      } else {
        alert_float('danger', response.message);
      }
    });
  });

  $('body').on('change', '.payment-remarks-input', function (e) {
    e.preventDefault();

    var rowId = $(this).data('id');
    var payment_remarks = $(this).val();

    // Perform AJAX request to update the invoice date
    $.post(admin_url + 'purchase/update_payment_remarks', {
      id: rowId,
      payment_remarks: payment_remarks
    }).done(function (response) {
      response = JSON.parse(response);
      if (response.success) {
        alert_float('success', response.message);
        table_pur_invoice_payments.ajax.reload(null, false); // Reload table without refreshing the page
      } else {
        alert_float('danger', response.message);
      }
    });
  });

})(jQuery);

var barTopVendorsChart;
var barChartTopBudgetHead;
var vendorLineChartOverTime;

function get_vpt_dashboard() {
  "use strict";

  var data = {
    from_date: $('input[name="from_date"]').val(),
    to_date: $('input[name="to_date"]').val(),
    vendors: $('select[name="vendor_ft[]"]').val(),
    group_pur: $('select[name="budget_head"]').val(),
    billing_invoices: $('select[name="billing_invoices"]').val(),
    bil_payment_status: $('select[name="bil_payment_status"]').val(),
  }

  $.post(admin_url + 'purchase/get_vpt_dashboard', data).done(function(response){
    response = JSON.parse(response);

    // Update value summaries
    $('.total_billed').text(response.total_billed);
    $('.total_paid').text(response.total_paid);
    $('.total_unpaid').text(response.total_unpaid);

    // BAR CHART - Vendor wise Payments Summary
    var vendorBarCtx = document.getElementById('barChartTopVendors').getContext('2d');
    var vendorLabels = response.bar_top_vendor_name;
    var vendorData = response.bar_top_vendor_value;

    if (barTopVendorsChart) {
      barTopVendorsChart.data.labels = vendorLabels;
      barTopVendorsChart.data.datasets[0].data = vendorData;
      barTopVendorsChart.update();
    } else {
      barTopVendorsChart = new Chart(vendorBarCtx, {
        type: 'bar',
        data: {
          labels: vendorLabels,
          datasets: [{
            label: 'Payments',
            data: vendorData,
            backgroundColor: '#1E90FF',
            borderColor: '#1E90FF',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              ticks: {
                autoSkip: false,
                maxRotation: 45,
                minRotation: 0
              },
              title: {
                display: true,
                text: 'Vendors'
              }
            },
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Payments'
              }
            }
          }
        }
      });
    }

    // BAR CHART - Budget Head Utilization
    var budgetHeadBarCtx = document.getElementById('barChartTopBudgetHead').getContext('2d');
    var budgetHeadLabels = response.bar_top_budget_head_name;
    var budgetHeadData = response.bar_top_budget_head_value;

    if (barChartTopBudgetHead) {
      barChartTopBudgetHead.data.labels = budgetHeadLabels;
      barChartTopBudgetHead.data.datasets[0].data = budgetHeadData;
      barChartTopBudgetHead.update();
    } else {
      barChartTopBudgetHead = new Chart(budgetHeadBarCtx, {
        type: 'bar',
        data: {
          labels: budgetHeadLabels,
          datasets: [{
            label: 'Total Certified Value',
            data: budgetHeadData,
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
                text: 'Total Certified Value'
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

    // Vendor Billing vs Vendor Payment Timeline
    var vendorlineCtx = document.getElementById('vendorLineChartOverTime').getContext('2d');
    if (vendorLineChartOverTime) {
      vendorLineChartOverTime.data.labels = response.vendor_order_date;
      vendorLineChartOverTime.data.datasets[0].data = response.line_vendor_billing_total;
      vendorLineChartOverTime.data.datasets[1].data = response.line_vendor_payment_total;
      vendorLineChartOverTime.update();
    } else {
      vendorLineChartOverTime = new Chart(vendorlineCtx, {
        type: 'line',
        data: {
          labels: response.vendor_order_date,
          datasets: [
            {
              label: 'Vendor Billing',
              data: response.line_vendor_billing_total,
              fill: false,
              borderColor: '#00008B',
              backgroundColor: '#00008B',
              tension: 0.3
            },
            {
              label: 'Vendor Payment',
              data: response.line_vendor_payment_total,
              fill: false,
              borderColor: '#1E90FF',
              backgroundColor: '#1E90FF',
              tension: 0.3
            }
          ]
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
              },
              grid: {
                display: false
              }
            },
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Value'
              },
              grid: {
                display: false
              }
            }
          }
        }
      });
    }

  });
}