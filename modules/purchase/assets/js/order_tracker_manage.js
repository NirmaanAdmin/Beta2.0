var hidden_columns = [2, 3, 4, 5], table_rec_campaign;
Dropzone.autoDiscover = false;
var expenseDropzone;
(function ($) {
    "use strict";
    table_rec_campaign = $('.table-table_order_tracker');

    var Params = {
        "type": "[name='type[]']",
        "rli_filter": "[name='rli_filter']",
        "vendors": "[name='vendors[]']",
        "kind": "[name='kind']",
        "budget_head": "[name='budget_head']",
        "order_type_filter": "[name='order_type_filter']",
        "projects": "[name='projects[]']",
        "aw_unw_order_status": "[name='aw_unw_order_status[]']",
        "yield": "[name='yield[]']",
        "is_change_order": "[name='is_change_order']"
    };

    initDataTable('.table-table_order_tracker', admin_url + 'purchase/table_order_tracker', [], [], Params, [3, 'desc']);

    $.each(Params, function (i, obj) {
        // console.log(obj);
        $('select' + obj).on('change', function () {
            table_rec_campaign.DataTable().ajax.reload();
        });
    });

    $(document).on('change', 'select[name="type[]"]', function () {
        $('select[name="type[]"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="vendors[]"]', function () {
        $('select[name="vendors[]"]').selectpicker('refresh');
    });
    $(document).on('change', 'select[name="projects[]"]', function () {
        $('select[name="projects[]"]').selectpicker('refresh');
    });
    $(document).on('change', 'select[name="aw_unw_order_status[]"]', function () {
        $('select[name="aw_unw_order_status[]"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="rli_filter"]', function () {
        $('select[name="rli_filter"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="kind"]', function () {
        $('select[name="kind"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="budget_head"]', function () {
        $('select[name="budget_head"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="order_type_filter"]', function () {
        $('select[name="order_type_filter"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="yield[]"]', function () {
        $('select[name="yield[]"]').selectpicker('refresh');
    });

    $(document).on('change', 'select[name="is_change_order"]', function () {
        $('select[name="is_change_order"]').selectpicker('refresh');
    });

    $(document).on('click', '.reset_all_ot_filters', function () {
        var filterArea = $('.all_ot_filters');
        filterArea.find('input').val("");
        filterArea.find('select').not('select[name="projects[]"]').selectpicker("val", "");
        table_rec_campaign.DataTable().ajax.reload();
        get_order_tracker_dashboard();
    });

    $(document).on('click', '.upload_order_tracker_attachments', function () {
        var rowId = $(this).data('id');
        var source = $(this).data('source');
        var input = $(this).closest('.input-group').find('.upload_order_tracker_files')[0];
        if (!input.files.length) {
            alert_float('warning', "Please select at least one file to upload.");
            return;
        }
        var formData = new FormData();
        for (var i = 0; i < input.files.length; i++) {
            formData.append('attachments[]', input.files[i]);
        }
        formData.append('id', rowId);
        formData.append('source', source);
        formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
        $.ajax({
            url: admin_url + 'purchase/upload_order_tracker_attachments',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var res = JSON.parse(response);
                var table_order_tracker_new = $('.table-table_order_tracker').DataTable();
                table_order_tracker_new.ajax.reload(null, false);
                if (res.status === true) {
                    alert_float('success', "Order tracker attachments uploaded successfully.");
                } else {
                    alert_float('warning', "Upload failed.");
                }
            },
            error: function () {
                alert_float('warning', "Upload failed.");
            }
        });
    });

    $('.table-table_order_tracker').on('draw.dt', function () {
        var reportsTable = $(this).DataTable();
        var sums = reportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(0).html("Total (Per Page)");
        $(this).find('tfoot td.total_budget_ro_projection').html(sums.total_budget_ro_projection);
        $(this).find('tfoot td.total_order_value').html(sums.total_order_value);
        $(this).find('tfoot td.total_committed_contract_amount').html(sums.total_committed_contract_amount);
        $(this).find('tfoot td.total_change_order_amount').html(sums.total_change_order_amount);
        $(this).find('tfoot td.total_rev_contract_value').html(sums.total_rev_contract_value);
        $(this).find('tfoot td.total_anticipate_variation').html(sums.total_anticipate_variation);
        $(this).find('tfoot td.total_cost_to_complete').html(sums.total_cost_to_complete);
        $(this).find('tfoot td.total_final_certified_amount').html(sums.total_final_certified_amount);
        $(this).find('tfoot td.total_ril_certified_amount').html(sums.total_ril_certified_amount);
    });
    $('.buttons-collection').hide()

    $(document).on('change', 'select[name="type[]"], select[name="rli_filter"], select[name="vendors[]"], select[name="kind"], select[name="budget_head"], select[name="order_type_filter"], select[name="projects[]"], select[name="aw_unw_order_status[]"]', function() {
        get_order_tracker_dashboard();
    });
})(jQuery);

function change_rli_filter(status, id, table_name) {
    "use strict";
    if (id > 0) {
        $.post(admin_url + 'purchase/change_rli_filter/' + status + '/' + id + '/' + table_name)
            .done(function (response) {
                try {
                    response = JSON.parse(response);

                    if (response.success) {
                        var $statusSpan = $('#status_span_' + id);

                        // Debugging
                        // console.log('Before:', $statusSpan.attr('class'));

                        // Remove all status-related classes
                        $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

                        // Add the new class and update content
                        if (response.class) {
                            $statusSpan.addClass(response.class);
                        }
                        if (response.status_str) {
                            $statusSpan.html(response.status_str + ' ' + (response.html || ''));
                        }

                        // Debugging
                        // console.log('After:', $statusSpan.attr('class'));

                        // Display success message
                        // $(".table-table_order_tracker").DataTable().ajax.reload();
                        alert_float('success', response.mess);
                    } else {
                        // Display warning message if the operation fails
                        alert_float('warning', response.mess);
                    }
                } catch (e) {
                    console.error('Error parsing server response:', e);
                    alert_float('danger', 'Invalid server response');
                }
            })
            .fail(function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert_float('danger', 'Failed to update status');
            });
    }
}

function change_aw_unw_order_status(status, id, table_name) {
    "use strict";
    if (id > 0) {
        $.post(admin_url + 'purchase/change_aw_unw_order_status/' + status + '/' + id + '/' + table_name)
            .done(function (response) {
                try {
                    response = JSON.parse(response);

                    if (response.success) {
                        var $statusSpan = $('#status_aw_uw_span_' + id);

                        // Debugging
                        // console.log('Before:', $statusSpan.attr('class'));

                        // Remove all status-related classes
                        $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

                        // Add the new class and update content
                        if (response.class) {
                            $statusSpan.addClass(response.class);
                        }
                        if (response.status_str) {
                            $statusSpan.html(response.status_str + ' ' + (response.html || ''));
                        }

                        // Debugging
                        // console.log('After:', $statusSpan.attr('class'));

                        // Display success message
                        // var table_critical_tracker = $(".table-table_order_tracker");
                        // table_critical_tracker.DataTable().ajax.reload();
                        alert_float('success', response.mess);
                    } else {
                        // Display warning message if the operation fails
                        alert_float('warning', response.mess);
                    }
                } catch (e) {
                    console.error('Error parsing server response:', e);
                    alert_float('danger', 'Invalid server response');
                }
            })
            .fail(function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert_float('danger', 'Failed to update status');
            });
    }
}

function update_budget_head(status, id, table_name) {
    "use strict";
    if (id > 0) {
        $.post(admin_url + 'purchase/update_budget_head/' + status + '/' + id + '/' + table_name)
            .done(function (response) {
                try {
                    response = JSON.parse(response);

                    if (response.success) {
                        var $statusSpan = $('#budget_head_span_' + id);

                        // Debugging
                        // console.log('Before:', $statusSpan.attr('class'));

                        // Remove all status-related classes
                        $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

                        // Add the new class and update content
                        if (response.class) {
                            $statusSpan.addClass(response.class);
                        }
                        if (response.status_str) {
                            $statusSpan.html(response.status_str + ' ' + (response.html || ''));
                        }

                        // Debugging
                        // console.log('After:', $statusSpan.attr('class'));

                        // Display success message
                        // var table_critical_tracker = $(".table-table_order_tracker");
                        // table_critical_tracker.DataTable().ajax.reload();
                        alert_float('success', response.mess);
                    } else {
                        // Display warning message if the operation fails
                        alert_float('warning', response.mess);
                    }
                } catch (e) {
                    console.error('Error parsing server response:', e);
                    alert_float('danger', 'Invalid server response');
                }
            })
            .fail(function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert_float('danger', 'Failed to update status');
            });
    }
}

function view_order_tracker_attachments(rel_id, rel_type) {
    "use strict";
    $.post(admin_url + 'purchase/view_order_tracker_attachments', {
        rel_id: rel_id,
        rel_type: rel_type
    }).done(function (response) { 
        response = JSON.parse(response);
        if (response.result) {
            $('.view_order_attachment_modal').html(response.result);
        } else {
            $('.view_order_attachment_modal').html('');
        }
        $('#viewOrderAttachmentModal').modal('show');
    });
}

function delete_order_tracker_attachment(id) {
    "use strict";
    if (confirm_delete()) {
        requestGet('purchase/delete_order_tracker_attachment/' + id).done(function (success) {
            if (success == 1) {
                $(".view_order_attachment_modal").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function (error) {
            alert_float('danger', error.responseText);
        });
    }
}

function preview_order_tracker_btn(invoker) {
    "use strict";
    var id = $(invoker).attr('id');
    view_order_tracker_file(id);
}

function view_order_tracker_file(id) {
    "use strict";
    $('#order_tracker_file_data').empty();
    $("#order_tracker_file_data").load(admin_url + 'purchase/view_order_tracker_file/' + id, function (response, status, xhr) {
        if (status == "error") {
            alert_float('danger', xhr.statusText);
        }
    });
}

function close_modal_preview() {
    "use strict";
    $('._project_file').modal('hide');
}

get_order_tracker_dashboard();

var budgetedVsActualCategory;
var lineChartOverTime;
var lineChartCertifiedOverTime;

function get_order_tracker_dashboard() {
  "use strict";

  var data = {
    type: $('select[name="type[]"]').val(),
    rli_filter: $('select[name="rli_filter"]').val(),
    vendors: $('select[name="vendors[]"]').val(),
    kind: $('select[name="kind"]').val(),
    budget_head: $('select[name="budget_head"]').val(),
    order_type_filter: $('select[name="order_type_filter"]').val(),
    projects: $('select[name="projects[]"]').val(),
    aw_unw_order_status: $('select[name="aw_unw_order_status[]"]').val(),
  }

  $.post(admin_url + 'purchase/get_order_tracker_charts', data).done(function(response){
    response = JSON.parse(response);

    // Update value summaries
    $('.cost_to_complete').text(response.cost_to_complete);
    $('.rev_contract_value').text(response.rev_contract_value);
    $('.percentage_utilized').text(response.percentage_utilized + '% Of CTC');
    $('.budgeted_procurement_net_value').text(response.budgeted_procurement_net_value);
    $('.anticipate_variation').text(response.anticipate_variation);
    $('.percentage_anticipate_variation').text(response.percentage_anticipated + '% Of CTC');
    $('.unawarded_capex').text(response.unawarded_capex);
    $('.work_done_value').text(response.work_done_value);
    $('.ril_certified_amount').text(response.ril_certified_amount);
    $('.total_payment_due').text(response.total_payment_due);

    // PIE CHART - Order Status Distribution
    var statusPieCtx = document.getElementById('pieChartForStatus').getContext('2d');
    var statusData = response.pie_status_value;
    var statusLabels = response.pie_status_name;

    if (window.statusChart) {
      statusChart.data.labels = statusLabels;
      statusChart.data.datasets[0].data = statusData;
      statusChart.update();
    } else {
      window.statusChart = new Chart(statusPieCtx, {
        type: 'pie',
        data: {
          labels: statusLabels,
          datasets: [{
            data: statusData,
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

    // COLUMN CHART - Budgeted vs Actual Procurement by Category
    var barCtx = document.getElementById('budgetedVsActualCategory').getContext('2d');
    var barData = {
      labels: response.budgeted_actual_category_labels,
      datasets: [{
          label: 'Budgeted',
          data: response.budgeted_category_value,
          backgroundColor: '#00008B',
          borderColor: '#00008B',
          borderWidth: 1
        },
        {
          label: 'Actual',
          data: response.actual_category_value,
          backgroundColor: '#1E90FF',
          borderColor: '#1E90FF',
          borderWidth: 1
        }
      ]
    };

    if (budgetedVsActualCategory) {
      budgetedVsActualCategory.data.labels = barData.labels;
      budgetedVsActualCategory.data.datasets[0].data = barData.datasets[0].data;
      budgetedVsActualCategory.data.datasets[1].data = barData.datasets[1].data;
      budgetedVsActualCategory.update();
    } else {
      budgetedVsActualCategory = new Chart(barCtx, {
        type: 'bar',
        data: barData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          },
          scales: {
            x: {
              title: {
                display: false,
                text: 'Order Date'
              }
            },
            y: {
              beginAtZero: true,
              title: {
                display: false,
                text: 'Amount'
              }
            }
          }
        }
      });
    }

    // LINE CHART - Order Value Over Time
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
            label: 'Order Value',
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
                text: 'Order Value'
              }
            }
          }
        }
      });
    }

    // LINE CHART - Total Certified Amount Over Period of Time
    var lineCertifiedCtx = document.getElementById('lineChartCertifiedOverTime').getContext('2d');
    if (lineChartCertifiedOverTime) {
      lineChartCertifiedOverTime.data.labels = response.line_certified_date;
      lineChartCertifiedOverTime.data.datasets[0].data = response.line_certified_total;
      lineChartCertifiedOverTime.data.datasets[1].data = response.line_certified_paid;
      lineChartCertifiedOverTime.update();
    } else {
      lineChartCertifiedOverTime = new Chart(lineCertifiedCtx, {
        type: 'line',
        data: {
          labels: response.line_certified_date,
          datasets: [
            {
              label: 'Total Certified Amount',
              data: response.line_certified_total,
              fill: false,
              borderColor: '#00008B',
              backgroundColor: '#00008B',
              tension: 0.3
            },
            {
              label: 'Paid Amount',
              data: response.line_certified_paid,
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
            legend: { display: true, position: 'bottom' },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            x: { title: { display: true, text: 'Month' } },
            y: { beginAtZero: true, title: { display: true, text: 'Amount' } }
          }
        }
      });
    }

  });
}

