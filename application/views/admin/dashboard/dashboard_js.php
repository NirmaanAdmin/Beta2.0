<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    var weekly_payments_statistics;
    var monthly_payments_statistics;
    var user_dashboard_visibility = <?php echo $user_dashboard_visibility; ?>;
    $(function() {
        $("[data-container]").sortable({
            connectWith: "[data-container]",
            helper: 'clone',
            handle: '.widget-dragger',
            tolerance: 'pointer',
            forcePlaceholderSize: true,
            placeholder: 'placeholder-dashboard-widgets',
            start: function(event, ui) {
                $("body,#wrapper").addClass('noscroll');
                $('body').find('[data-container]').css('min-height', '20px');
            },
            stop: function(event, ui) {
                $("body,#wrapper").removeClass('noscroll');
                $('body').find('[data-container]').removeAttr('style');
            },
            update: function(event, ui) {
                if (this === ui.item.parent()[0]) {
                    var data = {};
                    $.each($("[data-container]"), function() {
                        var cId = $(this).attr('data-container');
                        data[cId] = $(this).sortable('toArray');
                        if (data[cId].length == 0) {
                            data[cId] = 'empty';
                        }
                    });
                    $.post(admin_url + 'staff/save_dashboard_widgets_order', data, "json");
                }
            }
        });

        // Read more for dashboard todo items
        $('.read-more').readmore({
            collapsedHeight: 150,
            moreLink: "<a href=\"#\"><?php echo _l('read_more'); ?></a>",
            lessLink: "<a href=\"#\"><?php echo _l('show_less'); ?></a>",
        });

        $('body').on('click', '#viewWidgetableArea', function(e) {
            e.preventDefault();

            if (!$(this).hasClass('preview')) {
                $(this).html("<?php echo _l('hide_widgetable_area'); ?>");
                $('[data-container]').append(
                    '<div class="placeholder-dashboard-widgets pl-preview"></div>');
            } else {
                $(this).html("<?php echo _l('view_widgetable_area'); ?>");
                $('[data-container]').find('.pl-preview').remove();
            }

            $('[data-container]').toggleClass('preview-widgets');
            $(this).toggleClass('preview');
        });

        var $widgets = $('.widget');
        var widgetsOptionsHTML = '';
        widgetsOptionsHTML += '<div id="dashboard-options">';
        widgetsOptionsHTML +=
            "<div class=\"tw-flex tw-space-x-4 tw-items-center\"><h4 class='tw-font-medium tw-text-neutral-600 tw-text-lg'><i class='fa-regular fa-circle-question' data-toggle='tooltip' data-placement=\"bottom\" data-title=\"<?php echo _l('widgets_visibility_help_text'); ?>\"></i> <?php echo _l('widgets'); ?></h4><a href=\"<?php echo admin_url('staff/reset_dashboard'); ?>\" class=\"tw-text-sm\"><?php echo _l('reset_dashboard'); ?></a>";

        widgetsOptionsHTML +=
            ' <a href=\"#\" id="viewWidgetableArea" class=\"tw-text-sm\"><?php echo _l('view_widgetable_area'); ?></a></div>';

        $.each($widgets, function() {
            var widget = $(this);
            var widgetOptionsHTML = '';
            if (widget.data('name') && widget.html().trim().length > 0) {
                widgetOptionsHTML += '<div class="checkbox">';
                var wID = widget.attr('id');
                wID = wID.split('widget-');
                wID = wID[wID.length - 1];
                var checked = ' ';
                console.log('WID', wID);
                var db_result = $.grep(user_dashboard_visibility, function(e) {
                    return e.id == wID;
                });
                if (db_result.length >= 0) {
                    // no options saved or really visible
                    if (typeof(db_result[0]) == 'undefined' || db_result[0]['visible'] == 1) {
                        checked = ' checked ';
                    }
                }

                widgetOptionsHTML += '<input type="checkbox" class="widget-visibility" value="' + wID +
                    '"' + checked + 'id="widget_option_' + wID + '" name="dashboard_widgets[' + wID + ']">';
                widgetOptionsHTML += '<label for="widget_option_' + wID + '">' + widget.data('name') +
                    '</label>';
                widgetOptionsHTML += '</div>';
            }
            widgetsOptionsHTML += widgetOptionsHTML;
        });
        $(document).ready(function() {
            // Force show on page load
            const forcedVisibleWidgets = ['purchase_widget', 'leads_chart'];

            forcedVisibleWidgets.forEach(function(widgetId) {
                const $widget = $('#widget-' + widgetId);
                if ($widget.length ) {
                    $widget.addClass('hide');
                    $('#widget_option_' + widgetId).prop('checked', false);
                }
            });
        });
        $('.screen-options-area').append(widgetsOptionsHTML);
        $('body').find('#dashboard-options input.widget-visibility').on('change', function() {
            if ($(this).prop('checked') == false) {
                $('#widget-' + $(this).val()).addClass('hide');
            } else {
                $('#widget-' + $(this).val()).removeClass('hide');
            }

            var data = {};
            var options = $('#dashboard-options input[type="checkbox"]').map(function() {
                return {
                    id: this.value,
                    visible: this.checked ? 1 : 0
                };
            }).get();

            data.widgets = options;
            /*
                    if (typeof(csrfData) !== 'undefined') {
                        data[csrfData['token_name']] = csrfData['hash'];
                    }
            */
            $.post(admin_url + 'staff/save_dashboard_widgets_visibility', data).fail(function(data) {
                // Demo usage, prevent multiple alerts
                if ($('body').find('.float-alert').length == 0) {
                    alert_float('danger', data.responseText);
                }
            });
        });

        var tickets_chart_departments = $('#tickets-awaiting-reply-by-department');
        var tickets_chart_status = $('#tickets-awaiting-reply-by-status');
        var leads_chart = $('#leads_status_stats');
        var projects_chart = $('#projects_status_stats');

        if (tickets_chart_departments.length > 0) {
            // Tickets awaiting reply by department chart
            var tickets_dep_chart = new Chart(tickets_chart_departments, {
                type: 'doughnut',
                data: <?php echo $tickets_awaiting_reply_by_department; ?>,
            });
        }
        if (tickets_chart_status.length > 0) {
            // Tickets awaiting reply by department chart
            new Chart(tickets_chart_status, {
                type: 'doughnut',
                data: <?php echo $tickets_reply_by_status; ?>,
                options: {
                    onClick: function(evt) {
                        onChartClickRedirect(evt, this);
                    }
                },
            });
        }
        if (leads_chart.length > 0) {
            // Leads overview status
            new Chart(leads_chart, {
                type: 'doughnut',
                data: <?php echo $leads_status_stats; ?>,
                options: {
                    maintainAspectRatio: false,
                    onClick: function(evt) {
                        onChartClickRedirect(evt, this);
                    }
                }
            });
        }
        if (projects_chart.length > 0) {
            // Projects statuses
            new Chart(projects_chart, {
                type: 'doughnut',
                data: <?php echo $projects_status_stats; ?>,
                options: {
                    maintainAspectRatio: false,
                    onClick: function(evt) {
                        onChartClickRedirect(evt, this);
                    }
                }
            });
        }

        if ($(window).width() < 500) {
            // Fix for small devices weekly payment statistics
            $('#payment-statistics').attr('height', '250');
        }

        fix_user_data_widget_tabs();
        $(window).on('resize', function() {
            $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').removeAttr('style');
            fix_user_data_widget_tabs();
        });
        // Payments statistics
        init_weekly_payment_statistics(<?php echo $weekly_payment_stats; ?>);

        $('select[name="currency"]').on('change', function() {
            let $activeChart = $('#Payment-chart-name').data('active-chart');

            if (typeof(weekly_payments_statistics) !== 'undefined') {
                weekly_payments_statistics.destroy();
            }

            if (typeof(monthly_payments_statistics) !== 'undefined') {
                monthly_payments_statistics.destroy();
            }

            if ($activeChart == 'weekly') {
                init_weekly_payment_statistics();
            } else if ($activeChart == 'monthly') {
                init_monthly_payment_statistics();
            }

        });
    });

    function fix_user_data_widget_tabs() {
        if ((app.browser != 'firefox' &&
                isRTL == 'false' && is_mobile()) || (app.browser == 'firefox' &&
                isRTL == 'false' && is_mobile())) {
            $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').css('margin-bottom', '26px');
        }
    }

    function init_weekly_payment_statistics(data) {
        if ($('#payment-statistics').length > 0) {

            if (typeof(weekly_payments_statistics) !== 'undefined') {
                weekly_payments_statistics.destroy();
            }
            if (typeof(data) == 'undefined') {
                var currency = $('select[name="currency"]').val();
                $.get(admin_url + 'dashboard/weekly_payments_statistics/' + currency, function(response) {
                    weekly_payments_statistics = new Chart($('#payment-statistics'), {
                        type: 'bar',
                        data: response,
                        options: {
                            responsive: true,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                    }
                                }]
                            },
                        },
                    });
                }, 'json');
            } else {
                weekly_payments_statistics = new Chart($('#payment-statistics'), {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                }
                            }]
                        },
                    },
                });
            }

        }
    }

    function init_monthly_payment_statistics() {
        if ($('#payment-statistics').length > 0) {

            if (typeof(monthly_payments_statistics) !== 'undefined') {
                monthly_payments_statistics.destroy();
            }

            var currency = $('select[name="currency"]').val();
            $.get(admin_url + 'dashboard/monthly_payments_statistics/' + currency, function(response) {
                monthly_payments_statistics = new Chart($('#payment-statistics'), {
                    type: 'bar',
                    data: response,
                    options: {
                        responsive: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                }
                            }]
                        },
                    },
                });
            }, 'json');
        }
    }

    function update_payment_statistics(el) {
        let type = $(el).data('type');
        let $chartNameWrapper = $('#Payment-chart-name');
        $chartNameWrapper.data('active-chart', type);
        $chartNameWrapper.text($(el).text());

        if (typeof(weekly_payments_statistics) !== 'undefined') {
            weekly_payments_statistics.destroy();
        }

        if (typeof(monthly_payments_statistics) !== 'undefined') {
            monthly_payments_statistics.destroy();
        }

        console.log(type);

        if (type == 'weekly') {
            init_weekly_payment_statistics();
        } else if (type == 'monthly') {
            init_monthly_payment_statistics();
        }

    }

    function update_tickets_report_table(el) {
        var $el = $(el);
        var type = $el.data('type')
        $('#tickets-report-mode-name').text($el.text())

        $('#tickets-report-table-wrapper').load(admin_url + 'dashboard/ticket_widget/' + type, function(data) {
            $('.table-ticket-reports').dataTable().fnDestroy()
            initDataTableInline('.table-ticket-reports')
        });
        return false
    }

    var budgetedVsActualCategory;
    var orderTrackerLineChartOverTime;
    var costvsProgressLineChartOverTime;
    var pieChartForPRApprovalStatus;
    var pieChartForPOApprovalStatus;
    var pieChartForWOApprovalStatus;
    var pieChartForCOApprovalStatus;
    var pieChartForBillingStatus;
    var pieChartForPCApprovalStatus;

    get_order_tracker_dashboard();
    get_vendors_dashboard();
    get_purchase_request_dashboard();
    get_purchase_order_dashboard();
    get_work_order_dashboard();
    get_change_order_dashboard();
    get_vbt_dashboard();
    get_payment_certificate_dashboard();

    function get_order_tracker_dashboard() {
      "use strict";
      var data = {}
      $.post(admin_url + 'purchase/get_order_tracker_charts', data).done(function(response){
        response = JSON.parse(response);

        // Update value summaries
        $('.cost_to_complete').text(response.cost_to_complete);
        $('.rev_contract_value').text(response.rev_contract_value);
        $('.percentage_utilized').text(response.percentage_utilized + '%');
        $('.budgeted_procurement_net_value').text(response.budgeted_procurement_net_value);

        // Cost vs Progress S-Curve
        var costvsProgresslineCtx = document.getElementById('costvsProgressLineChartOverTime').getContext('2d');
        if (costvsProgressLineChartOverTime) {
          costvsProgressLineChartOverTime.data.labels = response.scurve_order_date;
          costvsProgressLineChartOverTime.data.datasets[0].data = response.line_actual_cost_total;
          costvsProgressLineChartOverTime.data.datasets[1].data = response.line_planned_cost_total;
          costvsProgressLineChartOverTime.update();
        } else {
          costvsProgressLineChartOverTime = new Chart(costvsProgresslineCtx, {
            type: 'line',
            data: {
              labels: response.scurve_order_date,
              datasets: [
                {
                  label: 'Actual Cost',
                  data: response.line_actual_cost_total,
                  fill: false,
                  borderColor: 'rgba(54, 162, 235, 1)',
                  backgroundColor: 'rgba(54, 162, 235, 0.2)',
                  tension: 0.3
                },
                {
                  label: 'Planned Cost',
                  data: response.line_planned_cost_total,
                  fill: false,
                  borderColor: 'rgba(255, 99, 132, 1)',
                  backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
                  }
                },
                y: {
                  beginAtZero: true,
                  title: {
                    display: true,
                    text: 'Percentage'
                  }
                }
              }
            }
          });
        }

        // Total Order Value Over Time
        var orderTrackerlineCtx = document.getElementById('orderTrackerLineChartOverTime').getContext('2d');
        if (orderTrackerLineChartOverTime) {
          orderTrackerLineChartOverTime.data.labels = response.line_order_date;
          orderTrackerLineChartOverTime.data.datasets[0].data = response.line_order_total;
          orderTrackerLineChartOverTime.update();
        } else {
          orderTrackerLineChartOverTime = new Chart(orderTrackerlineCtx, {
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

        // Budgeted vs Actual Procurement by Budget Head
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

      });
    }

    function get_vendors_dashboard() {
      "use strict";
      var data = {}
      $.post(admin_url + 'purchase/get_vendors_charts', data).done(function(response){
        response = JSON.parse(response);
        // Update value summaries
        $('.total_vendors').text(response.total_vendors);
        $('.onboarded_this_week').text(response.onboarded_this_week);
      });
    }

    function get_purchase_request_dashboard() {
      "use strict";
      var data = {}
      $.post(admin_url + 'purchase/get_pr_charts', data).done(function(response){
        response = JSON.parse(response);
        // Pie Chart for PO Approval Status
        var prStatusPieCtx = document.getElementById('pieChartForPRApprovalStatus').getContext('2d');
        var prStatusData = response.pie_status_value;
        var prStatusLabels = response.pie_status_name;

        if (pieChartForPRApprovalStatus) {
          pieChartForPRApprovalStatus.data.labels = prStatusLabels;
          pieChartForPRApprovalStatus.data.datasets[0].data = prStatusData;
          pieChartForPRApprovalStatus.update();
        } else {
          pieChartForPRApprovalStatus = new Chart(prStatusPieCtx, {
            type: 'pie',
            data: {
              labels: prStatusLabels,
              datasets: [{
                data: prStatusData,
                backgroundColor: prStatusLabels.map((_, i) => `hsl(${i * 35 % 360}, 70%, 60%)`),
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

    function get_purchase_order_dashboard() {
      "use strict";
      var data = {};
      $.post(admin_url + 'purchase/get_po_charts', data).done(function(response){
        response = JSON.parse(response);

        // Pie Chart for PO Approval Status
        var poStatusCtx = document.getElementById('pieChartForPOApprovalStatus').getContext('2d');
        var poStatusData = [response.approved_po_count, response.draft_po_count, response.rejected_po_count];
        if (pieChartForPOApprovalStatus) {
          pieChartForPOApprovalStatus.data.datasets[0].data = poStatusData;
          pieChartForPOApprovalStatus.update();
        } else {
          pieChartForPOApprovalStatus = new Chart(poStatusCtx, {
            type: 'pie',
            data: {
              labels: ['Approved', 'Draft', 'Rejected'],
              datasets: [{
                data: poStatusData,
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

      });
    }

    function get_work_order_dashboard() {
      "use strict";
      var data = {}
      $.post(admin_url + 'purchase/get_wo_charts', data).done(function(response){
        response = JSON.parse(response);

        // Pie Chart for WO Approval Status
        var woStatusCtx = document.getElementById('pieChartForWOApprovalStatus').getContext('2d');
        var woStatusData = [response.approved_wo_count, response.draft_wo_count, response.rejected_wo_count];

        if (pieChartForWOApprovalStatus) {
          pieChartForWOApprovalStatus.data.datasets[0].data = woStatusData;
          pieChartForWOApprovalStatus.update();
        } else {
          pieChartForWOApprovalStatus = new Chart(woStatusCtx, {
            type: 'pie',
            data: {
              labels: ['Approved', 'Draft', 'Rejected'],
              datasets: [{
                data: woStatusData,
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

      });
    }

    function get_change_order_dashboard() {
      "use strict";
      var data = {}
      $.post(admin_url + 'changee/get_co_charts', data).done(function(response){
        response = JSON.parse(response);

        // Pie Chart for CO Approval Status
        var coStatusCtx = document.getElementById('pieChartForCOApprovalStatus').getContext('2d');
        var coStatusData = [response.approved_co_count, response.draft_co_count, response.rejected_co_count];
        if (pieChartForCOApprovalStatus) {
          pieChartForCOApprovalStatus.data.datasets[0].data = coStatusData;
          pieChartForCOApprovalStatus.update();
        } else {
          pieChartForCOApprovalStatus = new Chart(coStatusCtx, {
            type: 'pie',
            data: {
              labels: ['Approved', 'Draft', 'Rejected'],
              datasets: [{
                data: coStatusData,
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

      });
    }

    function get_vbt_dashboard() {
      "use strict";
      var data = {}
      $.post(admin_url + 'purchase/get_vbt_dashboard', data).done(function(response){
        response = JSON.parse(response);

        // Total Vendor Bills per Billing Status
        var billingStatusPieCtx = document.getElementById('pieChartForBillingStatus').getContext('2d');
        var billingStatusData = response.pie_billing_value;
        var billingStatusLabels = response.pie_billing_name;

        if (pieChartForBillingStatus) {
          pieChartForBillingStatus.data.labels = billingStatusLabels;
          pieChartForBillingStatus.data.datasets[0].data = billingStatusData;
          pieChartForBillingStatus.update();
        } else {
          pieChartForBillingStatus = new Chart(billingStatusPieCtx, {
            type: 'pie',
            data: {
              labels: billingStatusLabels,
              datasets: [{
                data: billingStatusData,
                backgroundColor: billingStatusLabels.map((_, i) => `hsl(${i * 35 % 360}, 70%, 60%)`),
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

    function get_payment_certificate_dashboard() {
      "use strict";
      var data = {}
      $.post(admin_url + 'purchase/get_pc_charts', data).done(function(response){
        response = JSON.parse(response);

        // Pie Chart for Payment Certificate Approval Status
        var pcStatusPieCtx = document.getElementById('pieChartForPCApprovalStatus').getContext('2d');
        var pcStatusData = response.pie_status_value;
        var pcStatusLabels = response.pie_status_name;

        if (pieChartForPCApprovalStatus) {
          pieChartForPCApprovalStatus.data.labels = pcStatusLabels;
          pieChartForPCApprovalStatus.data.datasets[0].data = pcStatusData;
          pieChartForPCApprovalStatus.update();
        } else {
          pieChartForPCApprovalStatus = new Chart(pcStatusPieCtx, {
            type: 'pie',
            data: {
              labels: pcStatusLabels,
              datasets: [{
                data: pcStatusData,
                backgroundColor: pcStatusLabels.map((_, i) => `hsl(${i * 35 % 360}, 70%, 60%)`),
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
</script>