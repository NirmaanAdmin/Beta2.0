<script>
  var report_import_goods, report_po_voucher,
    report_from_choose, report_po, report_pur_inv, report_wo,
    fnServerParams,
    statistics_number_of_purchase_orders,
    statistics_cost_of_purchase_orders, report_item_tracker, po_wo_report_aging, payment_certificate_report_summary, delivery_report_performance;
  var report_from = $('input[name="report-from"]');
  var report_to = $('input[name="report-to"]');
  var date_range = $('#date-range');
  (function($) {
    "use strict";
    report_pur_inv = $('#list_purchase_inv_report');
    report_po = $('#list_po_report');
    report_wo = $('#list_wo_report');
    report_po_voucher = $('#list_po_voucher');
    report_import_goods = $('#list_import_goods');
    statistics_number_of_purchase_orders = $('#number-purchase-orders-report');
    statistics_cost_of_purchase_orders = $('#cost-purchase-orders-report');
    report_from_choose = $('#report-time');
    report_item_tracker = $('#list_item_tracker_report');
    po_wo_report_aging = $('#po_wo_aging_report');
    payment_certificate_report_summary = $('#payment_certificate_summary_report');
    delivery_report_performance = $('#delivery_performance_report');
    fnServerParams = {
      "products_services": '[name="products_services"]',
      "report_months": '[name="months-report"]',
      "report_from": '[name="report-from"]',
      "report_to": '[name="report-to"]',
      "year_requisition": "[name='year_requisition']",
      "report_currency": '[name="currency"]',
      "pur_order": '[name="pur_order[]"]',
      "vendor": '[name="vendor[]"]',
      "pur_vendor": '[name="pur_vendor[]"]',
      "pur_status": '[name="pur_status[]"]',
      "wo_vendor": '[name="wo_vendor[]"]',
      "wo_status": '[name="wo_status[]"]',
      "department": '[name="department[]"]',
      "wo_department": '[name="wo_department[]"]',
      "production_status": '[name="production_status[]"]',
      "delivery": '[name="delivery"]',
      "vendor": '[name="vendors[]"]',
      "vendor_id": '[name="vendor_id[]"]',
      "delivery_status": '[name="delivery_status[]"]',
      "risk": '[name="risk[]"]',
      "vendor_ids" : '[name="vendor_ids[]"]',
      "delivery_status_filter": '[name="delivery_status_filter"]'
    }

    $('select[name="products_services"]').on('change', function() {
      gen_reports();
    });

    $('select[name="currency"]').on('change', function() {
      gen_reports();
    });


    $('select[name="months-report"]').on('change', function() {
      if ($(this).val() != 'custom') {
        gen_reports();
      }
    });

    $('select[name="year_requisition"]').on('change', function() {
      gen_reports();
    });

    $('select[name="pur_vendor[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="pur_status[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="wo_vendor[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="wo_status[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="department[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="wo_department[]"]').on('change', function() {
      gen_reports();
    });

    $('select[name="vendors[]"]').on('change', function() {
      gen_reports();
    });

    report_from.on('change', function() {
      var val = $(this).val();
      var report_to_val = report_to.val();
      if (val != '') {
        report_to.attr('disabled', false);
        if (report_to_val != '') {
          gen_reports();
        }
      } else {
        report_to.attr('disabled', true);
      }
    });

    report_to.on('change', function() {
      var val = $(this).val();
      if (val != '') {
        gen_reports();
      }
    });

    $('.table-import-goods-report').on('draw.dt', function() {
      var paymentReceivedReportsTable = $(this).DataTable();
      var sums = paymentReceivedReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total').html(sums.total);
    });

    $('.table-po-report').on('draw.dt', function() {
      var poReportsTable = $(this).DataTable();
      var sums = poReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total').html(sums.total);
      $(this).find('tfoot td.total_tax').html(sums.total_tax);
      $(this).find('tfoot td.total_value').html(sums.total_value);
    });
    $('.table-wo-report').on('draw.dt', function() {
      var poReportsTable = $(this).DataTable();
      var sums = poReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total').html(sums.total);
      $(this).find('tfoot td.total_tax').html(sums.total_tax);
      $(this).find('tfoot td.total_value').html(sums.total_value);
    });

    $('.table-purchase-inv-report').on('draw.dt', function() {
      var poReportsTable = $(this).DataTable();
      var sums = poReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total').html(sums.total);
      $(this).find('tfoot td.total_tax').html(sums.total_tax);
      $(this).find('tfoot td.total_value').html(sums.total_value);
    });

    $('.table-payment-certificate-summary-report').on('draw.dt', function() {
      var pcReportsTable = $(this).DataTable();
      var sums = pcReportsTable.ajax.json().sums;
      $(this).find('tfoot').addClass('bold');
      $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
      $(this).find('tfoot td.total_po_value').html(sums.total_po_value);
      $(this).find('tfoot td.total_paid_value').html(sums.total_paid_value);
      $(this).find('tfoot td.total_balance_value').html(sums.total_balance_value);
    });

    $('.table-item-tracker-report').on('draw.dt', function() {
      var itReportsTable = $(this).DataTable();
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
      gen_reports();
    });
  })(jQuery);


  function init_report(e, type) {
    "use strict";

    var report_wrapper = $('#report');

    if (report_wrapper.hasClass('hide')) {
      report_wrapper.removeClass('hide');
    }

    $('head title').html($(e).text());


    report_from_choose.addClass('hide');

    $('#year_requisition').addClass('hide');

    report_po.addClass('hide');
    report_wo.addClass('hide');
    report_po_voucher.addClass('hide');
    report_pur_inv.addClass('hide');
    report_import_goods.addClass('hide');
    statistics_cost_of_purchase_orders.addClass('hide');
    statistics_number_of_purchase_orders.addClass('hide');
    report_item_tracker.addClass('hide');
    po_wo_report_aging.addClass('hide');
    payment_certificate_report_summary.addClass('hide');
    delivery_report_performance.addClass('hide');

    $('select[name="months-report"]').selectpicker('val', 'this_month');
    // Clear custom date picker
    $('#currency').removeClass('hide');

    if (type != 'statistics_number_of_purchase_orders' && type != 'statistics_cost_of_purchase_orders') {
      report_from_choose.removeClass('hide');
    }
    if (type == 'list_import_goods') {
      report_import_goods.removeClass('hide');
    } else if (type == 'statistics_number_of_purchase_orders') {
      $('#currency').addClass('hide');
      statistics_number_of_purchase_orders.removeClass('hide');
      $('#year_requisition').removeClass('hide');
    } else if (type == 'statistics_cost_of_purchase_orders') {
      statistics_cost_of_purchase_orders.removeClass('hide');
      $('#year_requisition').removeClass('hide');
    } else if (type == 'po_voucher_report') {
      $('#currency').addClass('hide');
      report_po_voucher.removeClass('hide');
    } else if (type == 'po_report') {
      report_po.removeClass('hide');
    } else if (type == 'wo_report') {
      report_wo.removeClass('hide');
    } else if (type == 'purchase_invoice_rp') {
      report_pur_inv.removeClass('hide');
    } else if (type == 'item_tracker_report') {
      report_item_tracker.removeClass('hide');
    } else if (type == 'po_wo_aging_report') {
      po_wo_report_aging.removeClass('hide');
    } else if (type == 'payment_certificate_summary_report') {
      payment_certificate_report_summary.removeClass('hide');
    } else if (type == 'delivery_performance_report') {
      delivery_report_performance.removeClass('hide');
    }

    gen_reports();
  }


  function import_goods_report() {
    "use strict";

    if ($.fn.DataTable.isDataTable('.table-import-goods-report')) {
      $('.table-import-goods-report').DataTable().destroy();
    }
    initDataTable('.table-import-goods-report', admin_url + 'purchase/import_goods_report', false, false, fnServerParams);
  }

  function po_voucher_report() {
    "use strict";

    if ($.fn.DataTable.isDataTable('.table-po-voucher-report')) {
      $('.table-po-voucher-report').DataTable().destroy();
    }
    initDataTable('.table-po-voucher-report', admin_url + 'purchase/po_voucher_report', false, false, fnServerParams);
  }

  function po_report() {
    "use strict";

    if ($.fn.DataTable.isDataTable('.table-po-report')) {
      $('.table-po-report').DataTable().destroy();
    }
    initDataTable('.table-po-report', admin_url + 'purchase/po_report', false, false, fnServerParams);
  }

  function wo_report() {
    "use strict";

    if ($.fn.DataTable.isDataTable('.table-wo-report')) {
      $('.table-wo-report').DataTable().destroy();
    }
    initDataTable('.table-wo-report', admin_url + 'purchase/wo_report', false, false, fnServerParams);
  }

  function purchase_inv_report() {
    "use strict";

    if ($.fn.DataTable.isDataTable('.table-purchase-inv-report')) {
      $('.table-purchase-inv-report').DataTable().destroy();
    }
    initDataTable('.table-purchase-inv-report', admin_url + 'purchase/purchase_inv_report', false, false, fnServerParams);
  }

  function item_tracker_report() {
    "use strict";
    var table_rec_campaign = $('.table-item-tracker-report');
    if ($.fn.DataTable.isDataTable('.table-item-tracker-report')) {
      $('.table-item-tracker-report').DataTable().destroy();
    }
    initDataTable('.table-item-tracker-report', admin_url + 'purchase/item_tracker_report', false, false, fnServerParams, undefined, true);
    $.each(fnServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_rec_campaign.DataTable().ajax.reload()
          .columns.adjust()
          .responsive.recalc();
      });
    });
  }

  function po_wo_aging_report() {
    "use strict";
    var table_po_wo_campaign = $('.table-po-wo-aging-report');
    if ($.fn.DataTable.isDataTable('.table-po-wo-aging-report')) {
      $('.table-po-wo-aging-report').DataTable().destroy();
    }
    initDataTable('.table-po-wo-aging-report', admin_url + 'purchase/po_wo_aging_report', false, false, fnServerParams, [4, 'desc']);
    $.each(fnServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_po_wo_campaign.DataTable().ajax.reload()
          .columns.adjust()
          .responsive.recalc();
      });
    });
  }

  function payment_certificate_summary_report() {
    "use strict";
    var table_payment_certificate_summary = $('.table-payment-certificate-summary-report');
    if ($.fn.DataTable.isDataTable('.table-payment-certificate-summary-report')) {
      $('.table-payment-certificate-summary-report').DataTable().destroy();
    }
    initDataTable('.table-payment-certificate-summary-report', admin_url + 'purchase/payment_certificate_summary_report', false, false, fnServerParams, [4, 'asc']);
    $.each(fnServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_payment_certificate_summary.DataTable().ajax.reload()
          .columns.adjust();
      });
    });
  }

  
  function delivery_performance_report() {
    "use strict";
    var table_delivery_performance = $('.table-delivery-performance-report');
    if ($.fn.DataTable.isDataTable('.table-delivery-performance-report')) {
      $('.table-delivery-performance-report').DataTable().destroy();
    }
    initDataTable('.table-delivery-performance-report', admin_url + 'purchase/delivery_performance_report', false, false, fnServerParams, [2, 'desc']);
    $.each(fnServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_delivery_performance.DataTable().ajax.reload()
          .columns.adjust()
          .responsive.recalc();
      });
    });
  }

  function number_of_purchase_orders_analysis() {
    "use strict";

    var data = {};
    data.year = $('select[name="year_requisition"]').val();
    $.post(admin_url + 'purchase/number_of_purchase_orders_analysis/', data).done(function(response) {
      response = JSON.parse(response);
      Highcharts.setOptions({
        chart: {
          style: {
            fontFamily: 'inherit !important',
            fill: 'black'
          }
        },
        colors: ['#119EFA', '#ef370dc7', '#15f34f', '#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4', '#50B432', '#0d91efc7', '#ED561B']
      });
      Highcharts.chart('container_number_purchase_orders', {
        chart: {
          type: 'column'
        },
        title: {
          text: '<?php echo _l('number_of_purchase_orders') ?>'
        },
        subtitle: {
          text: ''
        },
        credits: {
          enabled: false
        },
        xAxis: {
          categories: ['<?php echo _l('month_1') ?>',
            '<?php echo _l('month_2') ?>',
            '<?php echo _l('month_3') ?>',
            '<?php echo _l('month_4') ?>',
            '<?php echo _l('month_5') ?>',
            '<?php echo _l('month_6') ?>',
            '<?php echo _l('month_7') ?>',
            '<?php echo _l('month_8') ?>',
            '<?php echo _l('month_9') ?>',
            '<?php echo _l('month_10') ?>',
            '<?php echo _l('month_11') ?>',
            '<?php echo _l('month_12') ?>'
          ],
          crosshair: true,
        },
        yAxis: {
          min: 0,
          title: {
            text: ''
          }
        },
        tooltip: {
          headerFormat: '<span>{point.key}</span><table>',
          pointFormat: '<tr><td>{series.name}: </td>' +
            '<td><b>{point.y:.0f}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
        },
        plotOptions: {
          column: {
            pointPadding: 0.2,
            borderWidth: 0
          }
        },

        series: [{
          type: 'column',
          colorByPoint: true,
          name: '<?php echo _l('purchase_quantity') ?>',
          data: response,
          showInLegend: false
        }]
      });

    })
  }

  function cost_of_purchase_orders_analysis() {
    "use strict";

    var data = {};
    data.year = $('select[name="year_requisition"]').val();
    data.report_currency = $('select[name="currency"]').val();
    $.post(admin_url + 'purchase/cost_of_purchase_orders_analysis', data).done(function(response) {
      response = JSON.parse(response);
      Highcharts.setOptions({
        chart: {
          style: {
            fontFamily: 'inherit !important',
            fill: 'black'
          }
        },
        colors: ['#119EFA', '#ef370dc7', '#15f34f', '#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4', '#50B432', '#0d91efc7', '#ED561B']
      });
      Highcharts.chart('container_cost_purchase_orders', {
        chart: {
          type: 'column'
        },
        title: {
          text: '<?php echo _l('cost_of_purchase_orders') ?>'
        },
        subtitle: {
          text: ''
        },
        credits: {
          enabled: false
        },
        xAxis: {
          categories: ['<?php echo _l('month_1') ?>',
            '<?php echo _l('month_2') ?>',
            '<?php echo _l('month_3') ?>',
            '<?php echo _l('month_4') ?>',
            '<?php echo _l('month_5') ?>',
            '<?php echo _l('month_6') ?>',
            '<?php echo _l('month_7') ?>',
            '<?php echo _l('month_8') ?>',
            '<?php echo _l('month_9') ?>',
            '<?php echo _l('month_10') ?>',
            '<?php echo _l('month_11') ?>',
            '<?php echo _l('month_12') ?>'
          ],
          crosshair: true,
        },
        yAxis: {
          min: 0,
          title: {
            text: response.name
          }
        },
        tooltip: {
          headerFormat: '<span >{point.key}</span><table>',
          pointFormat: '<tr>' +
            '<td><b>{point.y:.0f} {series.name}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
        },
        plotOptions: {
          column: {
            pointPadding: 0.2,
            borderWidth: 0
          }
        },

        series: [{
          type: 'column',
          colorByPoint: true,
          name: response.unit,
          data: response.data,
          showInLegend: false,
        }]
      });

    })
  }

  // Main generate report function
  function gen_reports() {
    "use strict";

    if (!report_import_goods.hasClass('hide')) {
      import_goods_report();
    } else if (!statistics_number_of_purchase_orders.hasClass('hide')) {
      number_of_purchase_orders_analysis();
    } else if (!statistics_cost_of_purchase_orders.hasClass('hide')) {
      cost_of_purchase_orders_analysis();
    } else if (!report_po_voucher.hasClass('hide')) {
      po_voucher_report();
    } else if (!report_po.hasClass('hide')) {
      po_report();
    } else if (!report_wo.hasClass('hide')) {
      wo_report();
    } else if (!report_pur_inv.hasClass('hide')) {
      purchase_inv_report();
    } else if (!report_item_tracker.hasClass('hide')) {
      item_tracker_report();
    } else if (!po_wo_report_aging.hasClass('hide')) {
      po_wo_aging_report();
    } else if (!payment_certificate_report_summary.hasClass('hide')) {
      payment_certificate_summary_report();
    } else if(!delivery_report_performance.hasClass('hide')) {
      delivery_performance_report();
    }
  }
</script>